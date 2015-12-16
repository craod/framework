<?php

namespace Craod\Api\Rest\Exception;

use Craod\Api\Rest\HttpStatusCodes;

/**
 * Exception that is thrown when the request data does not match expectations
 *
 * @package Craod\Api\Rest\Exception
 */
class RequestDataException extends Exception {

	/**
	 * @var integer
	 */
	protected $statusCode = HttpStatusCodes::BAD_REQUEST;
}