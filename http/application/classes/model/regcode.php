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
 * Model_Regcode
 *
 * @Table(name="regcodes")
 * @Entity
 */
class Model_Regcode extends Model_Entity
{
	/**
	 * @Id @Column(type="text")
	 * @Column(name="regcode", type="text", nullable=false)
	 */
	protected $regcode;

	/**
	 * @var text $eventName
	 *
	 * @Column(name="event_name", type="text", nullable=false)
	 */
	protected $eventName;

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
         * @var text $emailAuth
         *
         * @Column(name="email_auth", type="boolean", nullable=false)
         */
        protected $emailAuth;

	/**
         * @var text $multiUser
         *
         * @Column(name="multiuser", type="boolean", nullable=false)
         */
        protected $multiUser;

	public function __get($name)
	{
		switch($name)
		{
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

	public function __toString()
	{
		$str  = "Regcode: {$this->regcode}, eventName={$this->eventName}, validFrom={$this->validFrom->format('Y-m-d H:i:s')}, validTo={$this->validTo->format('Y-m-d H:i:s')}, emailAuth={$this->emailAuth}, multiUser={$this->multiUser}";
		return $str;
	}

	public function toHTML()
	{
		$str  = "<div class='regcode' id='regcode_{$this->regcode}'>";
		$str .= "<table>";
		$str .= "<tr class='ID'><th>Regcode</th><td>{$this->regcode}</td></tr>";
		foreach(array('eventName', 'validFrom', 'validTo', 'emailAuth', 'multiUser') as $field)
		{
			$str .= $this->fieldHTML($field);
		}
		$str .= "</table>";
		$str .= "</div>";
		return $str;
	}

	public static function manageEvent($details)
        {
                if ($details["eventName"] != "" && $details["validFrom"] != "" && $details["validTo"] != "")
                {
                        if (!empty($details['emailAuth']))
                                $details['emailAuth'] = 1;
                        else
                                $details['emailAuth'] = 0;
                        if (!empty($details['multiUser']))
                                $details['multiUser'] = 1;
                        else
                                $details['multiUser'] = 0;
                        if (empty($details['regcode']))
                        {
                                $details['regcode'] = Model_Regcode::generateRegistrationCode($details);
				$regcode = Model_Regcode::addEvent($details);
                                if (is_object($regcode))
					return array($regcode, "Event '" . $details["eventName"] . "' successfully created");
				return array(NULL, "Could not create event '" . $details["eventName"] . "'");
                        }
                        elseif (!empty($details["update"]))
                        {
                                $regcode = Model_Regcode::updateEvent($details);
				if (is_object($regcode))
					return array($regcode, "Event '" . $details["eventName"] . "' successfully updated");
					return array(NULL, "Could not update event '" . $details["eventName"] . "'");
                        }
			else
				return array(NULL, "Could not create/update event", );
                }
                return array(NULL, "One or more fields have not been entered");
        }

	public static function redeemRegistrationCode($regcode)
	{
	}

	private static function generateRegistrationCode($details)
        {
                $hash = md5($details['eventName'] . $details['validFrom'] . $details['validTo'] . $details['emailAuth'] . $details['multiUser']);
                $estr = $details['emailAuth'] ? '3' : 'A';
                $mstr = $details['multiUser'] ? 'D' : '1';
                $regcode = substr($hash,0,5).$estr.substr($hash, -5).$mstr;
                while(!Model_Regcode::isUniqueRegcode($regcode))
                {
                        $regcode[-2] = chr((rand % 26) + 65);
                }
                return $regcode;
        }

        private static function isUniqueRegcode($regcode)
        {
		return !is_object(Doctrine::em()->getRepository('Model_Regcode')->findOneByRegcode($regcode));	
        }


        private static function addEvent($details)
        {
		$regcode = new Model_Regcode();
		$regcode->regcode = $details['regcode'];
		$regcode->eventName = $details['eventName'];
		$regcode->validFrom = new \DateTime($details['validFrom']);
		$regcode->validTo = new \DateTime($details['validTo']);
		$regcode->emailAuth = $details['emailAuth'];
		$regcode->multiUser = $details['multiUser'];
		$regcode->save();
		return Doctrine::em()->getRepository('Model_Regcode')->findOneByRegcode($details['regcode']);
	}

        private static function updateEvent($details)
        {
		$regcode = Doctrine::em()->getRepository('Model_Regcode')->findOneByregcode($details['regcode']);
		$regcode->eventName = $details['eventName'];
                $regcode->validFrom = new \DateTime($details['validFrom']);
                $regcode->validTo = new \DateTime($details['validTo']);
                $regcode->emailAuth = $details['emailAuth'];
                $regcode->multiUser = $details['multiUser'];
		$regcode->save();
		return $regcode;
        }


}
