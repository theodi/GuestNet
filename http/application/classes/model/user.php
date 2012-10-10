<?php

use Doctrine\ORM\Mapping\Table;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\GeneratedValue;
use Doctrine\ORM\Mapping\ManyToOne;
use Doctrine\ORM\Mapping\OneToMany;
use Doctrine\ORM\Mapping\JoinColumns;
use Doctrine\ORM\Mapping\JoinColumn;
/**
 * Model_User
 *
 * @Table(name="users")
 * @Entity
 */
class Model_User extends Model_Entity
{
	/**
	 * @Id @Column(type="integer")
	 * @GeneratedValue
	 */
	protected $id;

	/**
	 * @var text $email
	 *
	 * @Column(name="email", type="text", nullable=true)
	 */
	protected $email;

	/**
	 * @var text $name
	 *
	 * @Column(name="name", type="text", nullable=false)
	 */
	protected $name;

	/**
	 * @var text $username
	 *
	 * @Column(name="username", type="text", nullable=true)
	 */
	protected $username;

	/**
	 * @var text $picPath
	 *
	 * @Column(name="pic_path", type="text", nullable=true)
	 */
	protected $picPath;

	/**
	 * @var Model_User $sponsor
	 *
	 * @ManyToOne(targetEntity="Model_User", cascade={"persist"})
	 * @JoinColumns({
	 *   @JoinColumn(name="sponsor_id", referencedColumnName="id")
	 * })
	 */
	protected $sponsor;

	/**
         * @var datetime $validFrom
         *
         * @Column(name="valid_from", type="datetime", nullable=false)
         */
        protected $validFrom;

        /**
         * @var datetime $validTo
         *
         * @Column(name="valid_to", type="datetime", nullable=false)
         */
        protected $validTo;

	/**
	 * @var text $regcode
	 *
	 * @Column(name="regcode", type="text", nullable=true)
	 */
 	protected $regcode;

        /**
         * @var text $active
         *
         * @Column(name="active", type="boolean", nullable=false)
         */
        protected $active;

	/**
	 * @var Model_Radcheck $radchecks
	 *
	 * @ManyToOne(targetEntity="Model_Radcheck", cascade={"persist"})
	 * @JoinColumns({
	 *   @JoinColumn(name="username", referencedColumnName="username")
	 * })
	 */
	protected $radchecks;

	public function __get($name)
	{
		switch($name)
		{
			case "isAdminUser":
				return $this->isAdminUser();
			default:
				if (property_exists($this, $name))
				{
					return $this->$name;
				}
				else
				{
					return parent::__get($name);
				}
		}
	}
	
	public function __set($name, $value)
	{
		switch($name)
		{
			case "isPrivilegedUser":
				parent::__throwReadOnlyException($name);
			case "password":
				$this->setPassword($value);
			default:
				if (property_exists($this, $name))
				{
					$this->$name = $value;
				}
				else
				{
					parent::__set($name, $value);
				}
		}
	}

	public function isAdminUser()
	{
		$admin_users = Kohana::$config->load('system.default.admin_users');
                foreach ($admin_users as $admin_user) {
                        if (preg_match('/^'.$admin_user.'$/', $this->email))
                                return TRUE;
                }
                return FALSE;
	}

	public function __toString()
	{
		$str  = "User: {$this->id}, name={$this->name}, username={$this->username}, email={$this->email}";
		return $str;
	}

	public function toHTML()
	{
		$str  = "<div class='user' id='user_{$this->id}'>";
		$str .= "<table>";
		$str .= "<tr class='ID'><th>User</th><td>{$this->id}</td></tr>";
		foreach(array('name', 'username', 'email') as $field)
		{
			$str .= $this->fieldHTML($field);
		}
		$str .= "</table>";
		$str .= "</div>";
		return $str;
	}

	public function setPassword($password)
        {
		return Model_Radcheck::updateNTPassword($this->username, $password);
        }

	public function checkPassword($password)
        {
		return Model_Radcheck::checkNTPassword($this->username, $password);
        }

	public static function createGuest($prefix, $validFrom = null, $validUntil = null)
	{
		$user = new Model_User();
		$user->sponsor = $this;
		$user->validFrom = $validFrom;
		$user->validUntil = $validUntil;
		$user->save();
		$user->username = $prefix.str_pad($user->id, '0', 5, STR_PAD_LEFT);
		$user->save();
	}

	public static function getGuestUsername($id) {
                $guest_accounts = Kohana::$config->load('system.default.guest_accounts');
                $formattedNumber = str_pad($id, '0', $guest_accounts['number_length'], STR_PAD_LEFT);
                return $guest_accounts['prefix'] . $formattedNumber;
        }

	public static function addGuestUser($details, $sponsor)
        {
                if (empty($details["name"]) || empty($details["email"]) || empty($details["validTo"]))
                        return array(NULL, "Not all fields were filled in");
                if (is_object(Doctrine::em()->getRepository('Model_User')->findOneByEmail($details['email'])))
                        return array(NULL, "There already exists a user tied to this email address");

                $radcheck = Model_Radcheck::addNTPassword('GUEST', GuestNetUtils::generateRandomString());
                if (is_object($radcheck))
                {
                        $guest = new Model_User();
                        $guest->name = $details["name"];
                        $guest->email = $details["email"];
                        $guest->validTo = new \DateTime($details["validTo"]);
                        $guest->validFrom = new \DateTime();
                        $guest->sponsor = $sponsor;
                        $guest->active = TRUE;
                        $guest->save();
                        $guest->username = $radcheck->username;
			$guest->save();
                        if (is_object(Doctrine::em()->getRepository('Model_User')->findOneByUsername($guest->username)))
                                return array($guest, "Guest account successfully added for " . $guest->name);

                        $radcheck->delete();
                        return array(NULL, "Could not create account for " .  $guest->name);
                }
                return array(NULL, "Could not add guest account password for " . $guest->name);
        }

	public static function reactivateUser($username, $validTo)
        {
                $user = Doctrine::em()->getRepository('Model_User')->findOneByUsername($username);
                if (is_object($user))
                {
                        $user->validTo = new \DateTime($validTo);
                        $user->save();
                        $user2 = Doctrine::em()->getRepository('Model_User')->findOneByUsername($user->username);
                        if ($user2->validTo->getTimestamp() == $user->validTo->getTimestamp());
                                return "User account for " . $user->name . " successfully reactivated";
                }
                return "User account could not be reactivated for " . $user->name;
        }

        public static function expireUser($username)
        {
                $user = Doctrine::em()->getRepository('Model_User')->findOneByUsername($username);
                if (is_object($user))
                {
                        $user->validTo = new \DateTime();
                        $user->save();
                        $user2 = Doctrine::em()->getRepository('Model_User')->findOneByUsername($user->username);
                        if ($user2->validTo->getTimestamp() == $user->validTo->getTimestamp());
                                return "User account for " . $user->name . " successfully expired";
                }
                return "User account could not be expired for " . $user->name;
        }

	public static function resetPassword($username)
        {
                $user = Doctrine::em()->getRepository('Model_User')->findOneByUsername($username);
		$password = GuestNetUtils::generateRandomString();
                if (Model_Radcheck::updateNTPassword($username, $password))
                        return "Password successfully reset for " . $user->name . " ($username)<br/>New Password: $password";
                return "Password could not be reset for " . $user->name;
        }

}
