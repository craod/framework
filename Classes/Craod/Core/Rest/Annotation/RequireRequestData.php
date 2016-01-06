<?php

namespace Craod\Core\Rest\Annotation;

use Craod\Core\Rest\Validator\RequiredRequestDataValidator;

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
