<?php

namespace Craod\Api\Rest\Annotation;

use Craod\Api\Rest\Validator\RequiredUserValidator;

/**
 * Annotation that denotes that the action requires a user to proceed
 *
 * @Annotation
 * @Target("METHOD")
 */
final class RequireUser extends Validate {

	/**
	 * The validator that goes with this class
	 *
	 * @var string
	 */
	public $validator = RequiredUserValidator::class;
}
