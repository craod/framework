<?php

namespace Craod\Api\Model;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Cpliakas\Password\Password;

/**
 * Class User
 *
 * @package Craod\Api\Model
 * @ORM\Table(name="users")
 * @ORM\Entity(repositoryClass="Craod\Api\Repository\UserRepository")
 */
class User extends AbstractEntity {

	/**
	 * @var boolean
	 * @ORM\Column(type="boolean")
	 */
	protected $active;

	/**
	 * @var string
	 * @ORM\Column(type="string", unique=TRUE)
	 */
	protected $username;

	/**
	 * @var string|Password
	 * @ORM\Column(type="password")
	 */
	protected $password;

	/**
	 * @var string
	 * @ORM\Column(type="string")
	 */
	protected $firstName;

	/**
	 * @var string
	 * @ORM\Column(type="string")
	 */
	protected $lastName;

	/**
	 * @var array
	 * @ORM\Column(type="jsonb")
	 */
	protected $settings;

	/**
	 * @var ArrayCollection
	 * @ORM\ManyToMany(targetEntity="UserRole", inversedBy="users", fetch="LAZY", cascade={"all"})
	 * @ORM\JoinTable(name="users_user_roles_mm",
	 *   joinColumns={@ORM\JoinColumn(name="users", referencedColumnName="guid")},
	 *   inverseJoinColumns={@ORM\JoinColumn(name="user_roles", referencedColumnName="guid")}
	 * )
	 */
	protected $userRoles;

	/**
	 * Initialize settings
	 */
	public function __construct() {
		$this->settings = [];
		$this->userRoles = new ArrayCollection();
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
	public function getUsername() {
		return $this->username;
	}

	/**
	 * @param string $username
	 * @return User
	 */
	public function setUsername($username) {
		$this->username = $username;
		return $this;
	}

	/**
	 * @return string|Password
	 */
	public function getPassword() {
		return $this->password;
	}

	/**
	 * @param string|Password $password
	 * @return User
	 */
	public function setPassword($password) {
		$this->password = $password;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getFirstName() {
		return $this->firstName;
	}

	/**
	 * @param string $firstName
	 * @return User
	 */
	public function setFirstName($firstName) {
		$this->firstName = $firstName;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getLastName() {
		return $this->lastName;
	}

	/**
	 * @param string $lastName
	 * @return User
	 */
	public function setLastName($lastName) {
		$this->lastName = $lastName;
		return $this;
	}

	/**
	 * @return array
	 */
	public function getSettings() {
		return $this->settings;
	}

	/**
	 * @param array $settings
	 * @return User
	 */
	public function setSettings($settings) {
		$this->settings = $settings;
		return $this;
	}

	/**
	 * @return ArrayCollection
	 */
	public function getUserRoles() {
		return $this->userRoles;
	}

	/**
	 * @param ArrayCollection $userRoles
	 * @return User
	 */
	public function setUserRoles($userRoles) {
		$this->userRoles = $userRoles;
		return $this;
	}

	/**
	 * @param UserRole $userRole
	 * @return boolean
	 */
	public function hasUserRole(UserRole $userRole) {
		foreach ($this->getUserRoles() as $userRoleTest) {
			if ($userRole->getGuid() === $userRoleTest->getGuid()) {
				return TRUE;
			}
		}
		return FALSE;
	}

	/**
	 * @param UserRole $userRole
	 * @return $this
	 */
	public function addUserRole(UserRole $userRole) {
		if (!$this->hasUserRole($userRole)) {
			$this->userRoles->add($userRole);
		}
		return $this;
	}

	/**
	 * @param UserRole $userRole
	 * @return $this
	 */
	public function removeUserRole(UserRole $userRole) {
		foreach ($this->getUserRoles() as $key => $userRoleTest) {
			if ($userRole->getGuid() === $userRoleTest->getGuid()) {
				$this->userRoles->remove($key);
			}
		}
		return $this;
	}
}