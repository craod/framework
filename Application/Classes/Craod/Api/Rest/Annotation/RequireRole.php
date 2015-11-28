<?php

namespace Craod\Api\Rest\Annotation;

use Craod\Api\Rest\Validator\RequiredRoleValidator;

/**
 * @Annotation
 * @Target("METHOD")
 */
final class RequireRole extends Validate {

	/**
	 * The role to require
	 *
	 * @var string
	 */
	public $role;

	/**
	 * The validator that goes with this class
	 *
	 * @var string
	 */
	public $validator = RequiredRoleValidator::class;
}
