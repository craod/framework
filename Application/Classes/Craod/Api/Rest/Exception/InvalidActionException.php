<?php

namespace Craod\Api\Rest\Exception;

use Craod\Api\Rest\HttpStatusCodes;

/**
 * Exception to be thrown when a controller action method is given that is not valid
 *
 * @package Craod\Api\Rest\Exception
 */
class InvalidActionException extends Exception {

	/**
	 * @var integer
	 */
	protected $statusCode = HttpStatusCodes::INTERNAL_SERVER_ERROR;
}