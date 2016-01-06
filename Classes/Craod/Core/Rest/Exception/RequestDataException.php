<?php

namespace Craod\Core\Rest\Exception;

use Craod\Core\Http\HttpStatusCodes;

/**
 * Exception that is thrown when the request data does not match expectations
 *
 * @package Craod\Core\Rest\Exception
 */
class RequestDataException extends Exception {

	/**
	 * @var integer
	 */
	protected $statusCode = HttpStatusCodes::BAD_REQUEST;
}