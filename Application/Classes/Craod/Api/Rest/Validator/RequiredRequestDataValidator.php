<?php

namespace Craod\Api\Rest\Validator;

use Craod\Api\Rest\Exception\RequestDataException;
use Craod\Api\Rest\Annotation\RequireRequestData;

/**
 * Validator that checks whether the variables named in the RequireRequestData annotation are passed in the request body
 *
 * @package Craod\Api\Rest\Validator
 */
class RequiredRequestDataValidator extends AbstractControllerValidator {

	/**
	 * Checks to see whether the required variables are in the request body
	 *
	 * @return void
	 * @throws RequestDataException
	 */
	public function validate() {
		/** @var RequireRequestData $annotation */
		$annotation = $this->annotation;
		$requestData = $this->controller->getRequestData();
		$missingVariables = [];
		foreach ($annotation->value as $requestDataVariable) {
			if (!isset($requestData[$requestDataVariable])) {
				$missingVariables[] = $requestDataVariable;
			}
		}
		if (count($missingVariables) > 0) {
			throw new RequestDataException('Required variable or variables missing from request body: ' . implode(', ', $missingVariables), 1448725921);
		}
	}
}