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
 * Model_Radcheck
 *
 * @Table(name="radcheck")
 * @Entity
 */
class Model_Radcheck extends Model_Entity
{
	/**
	 * @Id @Column(type="integer")
	 * @GeneratedValue
	 */
	protected $id;

	/**
	 * @var text $username
	 *
	 * @Column(name="username", type="text", nullable=false)
	 */
	protected $username;

	/**
	 * @var text $attribute
	 *
	 * @Column(name="attribute", type="text", nullable=false)
	 */
	protected $attribute;

	/**
	 * @var text $op
	 *
	 * @Column(name="op", type="text", nullable=false)
	 */
	protected $op;

	/**
	 * @var text $value
	 *
	 * @Column(name="value", type="text", nullable=false)
	 */
	protected $value;

	/**
	 * @OneToMany(targetEntity="Model_User", mappedBy="user")
	 */
	protected $users;

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
		$str  = "Radcheck: {$this->id}, username={$this->username}, attribute={$this->attribute}, op={$this->op}, value={$this->value}";
		return $str;
	}

	public function toHTML()
	{
		$str  = "<div class='radcheck' id='radcheck_{$this->id}'>";
		$str .= "<table>";
		$str .= "<tr class='ID'><th>Radcheck</th><td>{$this->id}</td></tr>";
		foreach(array('username', 'attribute', 'op', 'value') as $field)
		{
			$str .= $this->fieldHTML($field);
		}
		$str .= "</table>";
		$str .= "</div>";
		return $str;
	}

	public static function addNTPassword($username, $password)
        {
                $radcheck = new Model_Radcheck();
                if ($username == "GUEST")
                        $radcheck->username = GuestNetUtils::generateRandomString();
                else
                        $radcheck->username = $username;
                $radcheck->attribute = "NT-Password";
                $radcheck->op = ":=";
                $hash = new smbHash();
                $radcheck->value = $hash->nthash($password);
                $radcheck->save();
                if ($username == "GUEST")
                {
                        $radcheck->username = Model_User::getGuestUsername($radcheck->id);
                        $radcheck->save();
                }
                return Doctrine::em()->getRepository('Model_Radcheck')->findOneBy(array('username' => $radcheck->username, 'attribute' => "NT-Password", 'op' => ':='));
        }

	public static function updateNTPassword($username, $password)
        {
                $radcheck = Doctrine::em()->getRepository('Model_Radcheck')->findOneBy(array('username' => $username, 'attribute' => "NT-Password", 'op' => ':='));
                $hash = new smbHash();
                $radcheck->value = $hash->nthash($password);
                $radcheck->save();
                return is_object(Doctrine::em()->getRepository('Model_Radcheck')->findOneByValue($radcheck->value));
        }

	public static function checkNTPassword($username, $password)
	{
		$hash = new smbHash();
                $radcheck =  Doctrine::em()->getRepository('Model_Radcheck')->findOneBy(array('username' => $username, 'attribute' => "NT-Password", 'op' => ':='));
                if (is_object($radcheck) && $radcheck->value == $hash->nthash($password))
                        return TRUE;
                return FALSE;
	}

}
