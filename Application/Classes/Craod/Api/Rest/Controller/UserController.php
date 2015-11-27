<?php

namespace Craod\Api\Rest\Controller;

use Craod\Api\Model\User;
use Craod\Api\Rest\Exception\NotFoundException;
use Craod\Api\Rest\Exception\AuthenticationException;

/**
 * Controller class for user actions
 *
 * @package Craod\Api\Rest\Controller
 */
class UserController extends AbstractController {

	/**
	 * Attempts to log the user in
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