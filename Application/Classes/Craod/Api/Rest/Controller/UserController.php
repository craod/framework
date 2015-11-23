<?php

namespace Craod\Api\Rest\Controller;

use Craod\Api\Model\User;

/**
 * Controller class for user actions
 *
 * @package Craod\Api\Rest\Controller
 */
class UserController extends AbstractController {

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