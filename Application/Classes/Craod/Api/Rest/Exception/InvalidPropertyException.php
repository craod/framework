<?php

namespace Craod\Api\Rest\Exception;

use Craod\Api\Rest\HttpStatusCodes;

/**
 * Exception to be thrown when a write is attempted on a property that does not exist or is not marked as writable
 *
 * @package Craod\Api\Rest\Exception
 */
class InvalidPropertyException extends Exception {

	/**
	 * @var integer
	 */
	protected $statusCode = HttpStatusCodes::BAD_REQUEST;
}