<?php

namespace Craod\Api\Rest\Exception;

use Craod\Api\Exception\Exception as BaseException;
use Craod\Api\Rest\HttpStatusCodes;

/**
 * Main Craod RESTful exception class, extends exceptions by adding standard status codes
 *
 * @package Craod\Api\Exception
 */
class Exception extends BaseException {

	/**
	 * @var array
	 */
	protected $statusCode = HttpStatusCodes::INTERNAL_SERVER_ERROR;

	/**
	 * Override the exception constructor by allowing the exception to be built from another, non-restful exception
	 *
	 * @param string|Exception $messageOrException
	 * @param integer $code
	 * @param Exception $previous
	 */
	public function __construct($messageOrException = '', $code = 0, Exception $previous = NULL) {
		if ($messageOrException instanceof \Exception) {
			parent::__construct($messageOrException->getMessage(), $messageOrException->getCode(), $messageOrException->getPrevious());
			$this->file = $messageOrException->file;
			$this->line = $messageOrException->line;
			$this->trace = $messageOrException->trace;
		} else {
			parent::__construct($messageOrException, $code, $previous);
		}
	}

	/**
	 * @return array
	 */
	public function jsonSerialize() {
		$exception = parent::jsonSerialize();
		$exception['statusCode'] = $this->statusCode;
		return $exception;
	}

	/**
	 * @return array
	 */
	public function getStatusCode() {
		return $this->statusCode;
	}

	/**
	 * @param array $statusCode
	 * @return Exception
	 */
	public function setStatusCode($statusCode) {
		$this->statusCode = $statusCode;
		return $this;
	}
}