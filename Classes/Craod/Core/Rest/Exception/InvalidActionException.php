<?php

namespace Craod\Core\Rest\Exception;

use Craod\Core\Http\HttpStatusCodes;

/**
 * Exception to be thrown when a controller action method is given that is not valid
 *
 * @package Craod\Core\Rest\Exception
 */
class InvalidActionException extends Exception {

	/**
	 * @var integer
	 */
	protected $statusCode = HttpStatusCodes::INTERNAL_SERVER_ERROR;
}