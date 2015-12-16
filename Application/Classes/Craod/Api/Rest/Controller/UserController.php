<?php

namespace Craod\Api\Rest\Controller;

use Craod\Api\Rest\Annotation as Craod;
use Craod\Api\Model\User;
use Craod\Api\Rest\Authentication\TokenGenerator;
use Craod\Api\Rest\Exception\InvalidTokenException;
use Craod\Api\Rest\Exception\NotFoundException;
use Craod\Api\Rest\Exception\AuthenticationException;

/**
 * Controller class for user actions
 *
 * @package Craod\Api\Rest\Controller
 */
class UserController extends SearchableCrudController {

	/**
	 * @var string
	 */
	protected $entityClass = User::class;

	/**
	 * Attempts to log the user in, if possible, then returns the user guid and token. This is the only time the token is provided
	 *
	 * @return array
	 * @throws NotFoundException
	 * @throws AuthenticationException
	 */
	public function loginAction() {
		/** @var User $user */
		$user = User::getRepository()->findOneBy(['email' => $this->requestData['email'], 'active' => TRUE]);
		if ($user === NULL) {
			throw new NotFoundException('User does not exist: ' . $this->requestData['email'], 1448587523);
		}
		if (!$user->getPassword()->match($this->requestData['password'])) {
			throw new AuthenticationException('Wrong password provided for: ' . $this->requestData['email'], 1448587524);
		}
		$token = TokenGenerator::generate($user);
		$user->setToken($token);
		$user->save();
		return [
			'guid' => $user->getGuid(),
			'token' => $token
		];
	}

	/**
	 * Validates the user represented by the given guid, by the token given. Also return the settings
	 *
	 * @return boolean
	 * @throws InvalidTokenException
	 */
	public function validateAction() {
		$guid = $this->requestData['guid'];
		$token = $this->requestData['token'];
		if (!$token || strlen($token) < 10) {
			throw new InvalidTokenException('Invalid token presented: ' . $token, 1448652735);
		}
		/** @var User $user */
		$user = User::getRepository()->findOneBy(['guid' => $guid, 'token' => $token, 'active' => TRUE]);
		if ($user === NULL) {
			throw new InvalidTokenException('Invalid token presented: ' . $token, 1448652735);
		}
		$userAsArray = $user->jsonSerialize();
		$userAsArray['settings'] = $user->getSettings();
		return $userAsArray;
	}

	/**
	 * Logs the user out - removes the token
	 *
	 * @return boolean
	 * @throws InvalidTokenException
	 */
	public function logoutAction() {
		$guid = $this->requestData['guid'];
		$token = $this->requestData['token'];
		if (!$token || strlen($token) < 10) {
			throw new InvalidTokenException('Invalid token presented: ' . $token, 1448652735);
		}
		/** @var User $user */
		$user = User::getRepository()->findOneBy(['guid' => $guid, 'token' => $token, 'active' => TRUE]);
		if ($user === NULL) {
			throw new InvalidTokenException('Invalid token presented: ' . $token, 1448652735);
		}
		$user->setToken('');
		$user->save();
		return TRUE;
	}

	/**
	 * Marks a user as active
	 *
	 * @param string $guid
	 * @return boolean
	 * @throws NotFoundException
	 * @throws AuthenticationException
	 * @Craod\RequireRole(role="ADMINISTRATOR")
	 */
	public function activateAction($guid) {
		/** @var User $user */
		$user = User::getRepository()->findOneBy(['guid' => $guid]);
		if ($user === NULL) {
			throw new NotFoundException('Invalid user requested: ' . $guid, 1448652739);
		} else if ($user->hasRole(User::ADMINISTRATOR)) {
			throw new AuthenticationException('You may not edit a user if they have an administrator role', 1448985634);
		}
		$user->setActive(TRUE);
		$user->save();
		return $user->isActive();
	}

	/**
	 * Marks a user as inactive
	 *
	 * @param string $guid
	 * @return boolean
	 * @throws NotFoundException
	 * @throws AuthenticationException
	 * @Craod\RequireRole(role="ADMINISTRATOR")
	 */
	public function deactivateAction($guid) {
		/** @var User $user */
		$user = User::getRepository()->findOneBy(['guid' => $guid]);
		if ($user === NULL) {
			throw new NotFoundException('Invalid user requested: ' . $guid, 1448652739);
		} else if ($user->hasRole(User::ADMINISTRATOR)) {
			throw new AuthenticationException('You may not edit a user if they have an administrator role', 1448985634);
		}
		$user->setActive(FALSE);
		$user->save();
		return $user->isActive();
	}

	/**
	 * Count all users based on the filters
	 *
	 * @return integer
	 */
	public function countAction() {
		return parent::count(self::FILTER);
	}

	/**
	 * Get all users based on the filters
	 *
	 * @return array
	 * @Craod\RequireRole("ADMINISTRATOR")
	 */
	public function getAllAction() {
		return parent::getAll(self::FILTER | self::PAGINATE | self::SORT);
	}

	/**
	 * Retrieve the information for the given user
	 *
	 * @param string $guid
	 * @return User
	 * @throws AuthenticationException
	 * @Craod\RequireRole("ADMINISTRATOR")
	 */
	public function getAction($guid) {
		/** @var User $user */
		$user = parent::get($guid);
		if ($user->hasRole(User::ADMINISTRATOR) && $user->getGuid() != $this->getApplication()->getCurrentUser()->getGuid()) {
			throw new AuthenticationException('Administrators cannot be edited by other users', 1450218534);
		}
		return $user;
	}

	/**
	 * Search for users based on the criteria given
	 *
	 * @return User[]
	 * @Craod\RequireRequestData({"searchTerms"})
	 */
	public function searchAction() {
		return parent::search($this->requestData['searchTerms'], self::PAGINATE | self::SORT);
	}
}