<?php

namespace Craod\Core\Rest\Controller;

use Craod\Core\Rest\Application;

use Slim\Http\Request;
use Slim\Http\Response;

/**
 * The controller class used in the REST API
 *
 * @package Craod\Core\Rest\Controller
 */
abstract class AbstractController {

	/**
	 * The application instance
	 *
	 * @var Application
	 */
	protected $application;

	/**
	 * @var Request
	 */
	protected $request;

	/**
	 * @var Response
	 */
	protected $response;

	/**
	 * @var mixed
	 */
	protected $requestData;

	/**
	 * Checks whether the controller class given points to a proper controller that is a subclass of this class
	 *
	 * @param string $controllerClassPath
	 * @return boolean
	 */
	public static function isValid($controllerClassPath) {
		return (class_exists($controllerClassPath) && is_subclass_of($controllerClassPath, self::class));
	}

	/**
	 * Initialize this controller with the given application
	 *
	 * @param Application $application
	 */
	public function __construct(Application $application) {
		$this->application = $application;
		$this->response = $application->response;
		$this->request = $application->request;
		$this->requestData = json_decode($this->request->getBody(), TRUE);
		if (!is_array($this->requestData)) {
			$this->requestData = [];
		}
	}

	/**
	 * Get the value from the request, optionally filtered using the requested filter
	 *
	 * @param string $name
	 * @param integer $filter
	 * @return mixed
	 */
	public function getRequestVariable($name, $filter = FILTER_DEFAULT) {
		$variable = filter_var($this->getApplication()->request->get($name), $filter);
		if ($variable === FALSE) {
			$variable = NULL;
		}
		return $variable;
	}

	/**
	 * @return Application
	 */
	public function getApplication() {
		return $this->application;
	}

	/**
	 * @param Application $application
	 * @return AbstractController
	 */
	public function setApplication($application) {
		$this->application = $application;
		return $this;
	}

	/**
	 * @return Request
	 */
	public function getRequest() {
		return $this->request;
	}

	/**
	 * @param Request $request
	 * @return AbstractController
	 */
	public function setRequest($request) {
		$this->request = $request;
		return $this;
	}

	/**
	 * @return Response
	 */
	public function getResponse() {
		return $this->response;
	}

	/**
	 * @param Response $response
	 * @return AbstractController
	 */
	public function setResponse($response) {
		$this->response = $response;
		return $this;
	}

	/**
	 * @return mixed
	 */
	public function getRequestData() {
		return $this->requestData;
	}

	/**
	 * @param mixed $requestData
	 * @return AbstractController
	 */
	public function setRequestData($requestData) {
		$this->requestData = $requestData;
		return $this;
	}
}