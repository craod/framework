<?php

namespace Craod\Api\Rest\Exception;

use Craod\Api\Rest\HttpStatusCodes;

/**
 * Exception to be thrown when a resource is requested that does not exist
 *
 * @package Craod\Api\Rest\Exception
 */
class NotFoundException extends Exception {

	/**
	 * @var integer
	 */
	protected $statusCode = HttpStatusCodes::NOT_FOUND;
}