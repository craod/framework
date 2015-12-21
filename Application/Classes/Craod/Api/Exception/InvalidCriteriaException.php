<?php

namespace Craod\Api\Exception;

use Craod\Api\Rest\HttpStatusCodes;

/**
 * Exception thrown when invalid criteria are passed to repository methods
 *
 * @package Craod\Api\Exception
 */
class InvalidCriteriaException extends Exception {

	/**
	 * @var integer
	 */
	protected $statusCode = HttpStatusCodes::BAD_REQUEST;
}