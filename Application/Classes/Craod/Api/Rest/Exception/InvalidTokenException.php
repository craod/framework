<?php

namespace Craod\Api\Rest\Exception;

use Craod\Api\Rest\HttpStatusCodes;

/**
 * Exception to be thrown when an invalid token is provided
 *
 * @package Craod\Api\Rest\Exception
 */
class InvalidTokenException extends Exception {

	/**
	 * @var integer
	 */
	protected $statusCode = HttpStatusCodes::UNAUTHORIZED;
}