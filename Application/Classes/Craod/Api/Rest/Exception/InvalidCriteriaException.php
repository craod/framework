<?php

namespace Craod\Api\Rest\Exception;

use Craod\Api\Rest\HttpStatusCodes;

/**
 * Exception thrown when invalid criteria are passed to repository methods
 *
 * @package Craod\Api\Rest\Exception
 */
class InvalidCriteriaException extends Exception {

	/**
	 * @var integer
	 */
	protected $statusCode = HttpStatusCodes::BAD_REQUEST;
}