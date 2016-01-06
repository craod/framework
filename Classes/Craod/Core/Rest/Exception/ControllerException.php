<?php

namespace Craod\Core\Rest\Exception;

use Craod\Core\Http\HttpStatusCodes;

/**
 * General controller exception
 *
 * @package Craod\Core\Rest\Exception
 */
class ControllerException extends Exception {

	/**
	 * @var integer
	 */
	protected $statusCode = HttpStatusCodes::INTERNAL_SERVER_ERROR;
}