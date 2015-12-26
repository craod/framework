<?php

namespace Craod\Api\Model;

use Craod\Api\Rest\Annotation as Craod;
use Craod\Api\Utility\Settings;

use Doctrine\ORM\Mapping as ORM;

use Cpliakas\Password\Password;

/**
 * Class User
 *
 * @package Craod\Api\Model
 * @ORM\Table(name="users")
 * @ORM\Entity(repositoryClass="Craod\Api\Repository\UserRepository")
 * @ORM\HasLifecycleCallbacks
 */
class User extends SearchableEntity {

	const ADMINISTRATOR = 2;

	/**
	 * @var string
	 * @ORM\Column(type="string", unique=TRUE)
	 * @Craod\Api\Writable
	 * @Craod\Api\Searchable({"analyzer": "simple"})
	 */
	protected $email;

	/**
	 * @var string|Password
	 * @ORM\Column(type="password")
	 */
	protected $password = '';

	/**
	 * @var string
	 * @ORM\Column(type="string")
	 * @Craod\Api\Writable
	 * @Craod\Api\Searchable({"analyzer": "simple"})
	 */
	protected $firstName;

	/**
	 * @var string
	 * @ORM\Column(type="string")
	 * @Craod\Api\Writable
	 * @Craod\Api\Searchable({"analyzer": "simple"})
	 */
	protected $lastName;

	/**
	 * @var string
	 * @ORM\Column(type="string")
	 */
	protected $token = '';

	/**
	 * @var integer
	 * @ORM\Column(type="integer")
	 * @Craod\Api\Searchable({"index": "not_analyzed"})
	 */
	protected $roles = 0;

	/**
	 * @var array
	 * @ORM\Column(type="jsonb")
	 * @Craod\Api\Writable
	 */
	protected $settings = [];

	/**
	 * The time of the user's last action, to determine whether they are online
	 *
	 * @var \DateTime
	 * @ORM\Column(type="datetimetz")
	 * @Craod\Api\Searchable
	 */
	protected $lastAccess;

	/**
	 * Ensure there is a last access date set by default
	 */
	public function __construct() {
		parent::__construct();
		$this->lastAccess = new \DateTime();
	}

	/**
	 * Checks whether the user is online based on the difference between the last action and the online threshold
	 *
	 * @return boolean
	 */
	public function isOnline() {
		$threshold = new \DateTime(Settings::get('Craod.Api.user.onlineThreshold'));
		return ($this->lastAccess !== NULL) && ($this->lastAccess > $threshold);
	}

	/**
	 * Serialize this object into a json array and remove the password, token and settings - other users have no business knowing
	 * these
	 *
	 * @return array
	 */
	public function jsonSerialize() {
		$value = parent::jsonSerialize();
		$value['online'] = $this->isOnline();
		unset($value['password']);
		unset($value['token']);
		unset($value['settings']);
		return $value;
	}

	/**
	 * @return string
	 */
	public function getEmail() {
		return $this->email;
	}

	/**
	 * @param string $email
	 * @return User
	 */
	public function setEmail($email) {
		$this->email = $email;
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
	public function getToken() {
		return $this->token;
	}

	/**
	 * @param array $token
	 * @return User
	 */
	public function setToken($token) {
		$this->token = $token;
		return $this;
	}

	/**
	 * @return integer
	 */
	public function getRoles() {
		return $this->roles;
	}

	/**
	 * @param integer $roles
	 * @return User
	 */
	public function setRole($roles) {
		$this->roles = $roles;
		return $this;
	}

	/**
	 * @param integer $role
	 * @return boolean
	 */
	public function hasRole($role) {
		return (($this->roles & $role) === $role);
	}

	/**
	 * @param integer $role
	 * @return $this
	 */
	public function addRole($role) {
		$this->roles |= $role;
		return $this;
	}

	/**
	 * @param integer $role
	 * @return $this
	 */
	public function removeRole($role) {
		$this->roles ^= $role;
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
	 * @return $this
	 */
	public function updateLastAccess() {
		$this->lastAccess = new \DateTime();
		return $this;
	}
	/**
	 * @return \DateTime
	 */
	public function getLastAccess() {
		return $this->lastAccess;
	}
}