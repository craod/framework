<?php

namespace Craod\Api\Rest\Validator;

use Craod\Api\Rest\Exception\AuthenticationException;
use Craod\Api\Rest\Annotation\RequireRole;

/**
 * Validator that checks whether there is a valid currently logged in user
 *
 * @package Craod\Api\Rest\Validator
 */
class RequiredUserValidator extends AbstractControllerValidator {

	/**
	 * Checks to see whether there is a logged in user
	 *
	 * @return void
	 * @throws AuthenticationException
	 */
	public function validate() {
		$user = $this->controller->getApplication()->getCurrentUser();
		if ($user === NULL) {
			throw new AuthenticationException('User must be logged in to proceed', 1448725920);
		}
	}
}