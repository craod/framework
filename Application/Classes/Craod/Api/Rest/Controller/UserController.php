<?php

namespace Craod\Api\Rest\Controller;

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
class UserController extends AbstractController {

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
	 * Validates the user represented by the given guid, by the token given
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
		$user = User::getRepository()->findOneBy(['guid' => $guid, 'token' => $token]);
		if ($user === NULL) {
			throw new InvalidTokenException('Invalid token presented: ' . $token, 1448652735);
		}
		return $user;
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
		$user = User::getRepository()->findOneBy(['guid' => $guid, 'token' => $token]);
		if ($user === NULL) {
			throw new InvalidTokenException('Invalid token presented: ' . $token, 1448652735);
		}
		$user->setToken('');
		$user->save();
		return TRUE;
	}

	/**
	 * Get a list of all the users, optionally filtered by the given offset, limit and ordered by the requested variable
	 *
	 * @return array
	 */
	public function getUsersAction() {
		$repository = User::getRepository();
		$offset = $this->getRequestVariable('offset', INPUT_GET);
		$limit = $this->getRequestVariable('limit', INPUT_GET);
		return $repository->findBy(['active' => TRUE], NULL, $limit, $offset);
	}
}