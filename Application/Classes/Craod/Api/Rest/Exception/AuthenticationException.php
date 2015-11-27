<?php

namespace Craod\Api\Rest\Exception;

use Craod\Api\Rest\HttpStatusCodes;

/**
 * Exception to be thrown when a bad password is provided
 *
 * @package Craod\Api\Rest\Exception
 */
class AuthenticationException extends Exception {

	/**
	 * @var integer
	 */
	protected $statusCode = HttpStatusCodes::UNAUTHORIZED;
}