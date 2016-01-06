<?php

namespace Craod\Core\Rest\Exception;

use Craod\Core\Http\HttpStatusCodes;

/**
 * Exception to be thrown when a resource is requested that does not exist
 *
 * @package Craod\Core\Rest\Exception
 */
class NotFoundException extends Exception {

	/**
	 * @var integer
	 */
	protected $statusCode = HttpStatusCodes::NOT_FOUND;
}