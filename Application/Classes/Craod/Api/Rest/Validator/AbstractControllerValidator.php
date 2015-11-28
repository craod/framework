<?php

namespace Craod\Api\Rest\Validator;

use Craod\Api\Rest\Annotation\Validate;
use Craod\Api\Rest\Controller\AbstractController;

/**
 * General form for validators for controller actions
 *
 * @package Craod\Api\Rest\Validator
 */
abstract class AbstractControllerValidator {

	/**
	 * The controller this validator is validating
	 *
	 * @var AbstractController
	 */
	protected $controller;

	/**
	 * The action method being validated
	 *
	 * @var string
	 */
	protected $actionMethod;

	/**
	 * Any arguments that were passed to the method call
	 *
	 * @var array
	 */
	protected $arguments;

	/**
	 * The annotation that required this validator, if it was initiated by an annotation
	 *
	 * @var Validate
	 */
	protected $annotation;

	/**
	 * AbstractControllerValidator constructor
	 *
	 * @param AbstractController $controller
	 * @param string $actionMethod
	 * @param array $arguments
	 * @param Validate $annotation
	 */
	public function __construct(AbstractController $controller, $actionMethod, $arguments, Validate $annotation = NULL) {
		$this->controller = $controller;
		$this->actionMethod = $actionMethod;
		$this->arguments = $arguments;
		$this->annotation = $annotation;
	}

	/**
	 * Checks to see whether the call was valid or not, should throw any errors immediately
	 *
	 * @return void
	 * @throws \Exception
	 */
	public abstract function validate();

}