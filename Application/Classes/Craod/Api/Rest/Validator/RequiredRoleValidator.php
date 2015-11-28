<?php

namespace Craod\Api\Rest\Validator;

use Craod\Api\Rest\Exception\AuthenticationException;
use Craod\Api\Rest\Annotation\RequireRole;

/**
 * Validator that checks whether the currently logged in user has a given role
 *
 * @package Craod\Api\Rest\Validator
 */
class RequiredRoleValidator extends RequiredUserValidator {

	/**
	 * Checks to see whether the user has the necessary role
	 *
	 * @return void
	 * @throws AuthenticationException
	 */
	public function validate() {
		parent::validate();
		$user = $this->controller->getApplication()->getCurrentUser();
		/** @var RequireRole $annotation */
		$annotation = $this->annotation;
		$roleName = $annotation->role;
		$reflectionClass = new \ReflectionClass($user);
		$role = $reflectionClass->getConstant($roleName);
		if (!$user->hasRole($role)) {
			$exception = new AuthenticationException('User lacks the required role: ' . $roleName, 1448725921);
			$exception->setData([
				'requiredRole' => $roleName
			]);
			throw $exception;
		}
	}
}