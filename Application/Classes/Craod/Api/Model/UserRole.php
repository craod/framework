<?php

namespace Craod\Api\Model;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Cpliakas\Password\Password;

/**
 * Class User
 *
 * @package Craod\Api\Model
 * @ORM\Table(name="user_roles")
 * @ORM\Entity(repositoryClass="Craod\Api\Repository\UserRoleRepository")
 */
class UserRole extends AbstractEntity {

	/**
	 * @var boolean
	 * @ORM\Column(type="boolean")
	 */
	protected $active;

	/**
	 * @var string
	 * @ORM\Column(type="string", unique=TRUE)
	 */
	protected $abbreviation;

	/**
	 * @var ArrayCollection
	 * @ORM\ManyToMany(targetEntity="User", mappedBy="userRoles", fetch="EXTRA_LAZY")
	 */
	protected $users;

	/**
	 * Initialize settings
	 */
	public function __construct() {
		$this->settings = [];
		$this->users = new ArrayCollection();
	}

	/**
	 * Serialize this object into a json array and remove the password
	 *
	 * @return array
	 */
	public function jsonSerialize() {
		$value = parent::jsonSerialize();
		unset($value['password']);
		return $value;
	}

	/**
	 * @return boolean
	 */
	public function isActive() {
		return $this->active;
	}

	/**
	 * @param boolean $active
	 * @return User
	 */
	public function setActive($active) {
		$this->active = $active;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getAbbreviation() {
		return $this->abbreviation;
	}

	/**
	 * @param string $abbreviation
	 * @return UserRole
	 */
	public function setAbbreviation($abbreviation) {
		$this->abbreviation = $abbreviation;
		return $this;
	}

	/**
	 * @return ArrayCollection
	 */
	public function getUsers() {
		return $this->users;
	}

	/**
	 * @param ArrayCollection $users
	 * @return UserRole
	 */
	public function setUsers($users) {
		$this->users = $users;
		return $this;
	}

	/**
	 * @param User $user
	 * @return boolean
	 */
	public function hasUser(User $user) {
		foreach ($this->getUsers() as $userTest) {
			if ($user->getGuid() === $userTest->getGuid()) {
				return TRUE;
			}
		}
		return FALSE;
	}

	/**
	 * @param User $user
	 * @return $this
	 */
	public function addUser(User $user) {
		if (!$this->hasUser($user)) {
			$this->users->add($user);
		}
		return $this;
	}

	/**
	 * @param User $user
	 * @return $this
	 */
	public function removeUser(User $user) {
		foreach ($this->getUsers() as $key => $userTest) {
			if ($user->getGuid() === $userTest->getGuid()) {
				$this->users->remove($key);
			}
		}
		return $this;
	}

}