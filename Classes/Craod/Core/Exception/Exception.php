<?php

namespace Craod\Core\Exception;

/**
 * Main Craod exception class, extends exceptions by adding data array, standard json serializing
 *
 * @package Craod\Core\Exception
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

	/**
	 * @return array
	 */
	public function getData() {
		return $this->data;
	}

	/**
	 * @param array $data
	 * @return Exception
	 */
	public function setData($data) {
		$this->data = $data;
		return $this;
	}
}