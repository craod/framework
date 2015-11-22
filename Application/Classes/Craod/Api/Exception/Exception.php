<?php

namespace Craod\Api\Exception;

/**
 * Main Craod exception class, extends exceptions by adding data array, standard json serializing
 *
 * @package Craod\Api\Exception
 */
class Exception extends \Exception implements \JsonSerializable {

	/**
	 * @var array
	 */
	protected $data;

	/**
	 * @return array
	 */
	public function jsonSerialize() {
		return [
			'exception' => get_called_class(),
			'message' => $this->getMessage(),
			'code' => $this->getCode(),
			'file' => $this->getFile(),
			'line' => $this->getLine(),
			'previous' => $this->getPrevious(),
			'trace' => $this->getTrace()
		];
	}
}