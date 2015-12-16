<?php

namespace Craod\Api\Rest\Annotation;

use Craod\Api\Rest\Validator\RequiredRequestDataValidator;

/**
 * @Annotation
 * @Target("METHOD")
 */
final class RequireRequestData extends Validate {

	/**
	 * The names of the request data variables that are required
	 *
	 * @var array
	 */
	public $value;

	/**
	 * The validator that goes with this class
	 *
	 * @var string
	 */
	public $validator = RequiredRequestDataValidator::class;
}
