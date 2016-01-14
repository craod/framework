<?php

namespace Craod\Core\Exception;

use Craod\Core\Rest\Exception\Exception as RestException;
use Craod\Core\Http\HttpStatusCodes;

/**
 * Exception to be thrown when authentication is denied to access a resource
 *
 * @package Craod\Core\Exception
 */
class AuthenticationException extends RestException {

	/**
	 * @var integer
	 */
	protected $statusCode = HttpStatusCodes::UNAUTHORIZED;
}