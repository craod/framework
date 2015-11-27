<?php

namespace Craod\Api\Rest;

use Craod\Api\Core\Application as CraodApplication;
use Craod\Api\Rest\Controller\AbstractController;
use Craod\Api\Rest\Exception\Exception as RestException;
use Craod\Api\Rest\Exception\Exception;
use Craod\Api\Rest\Exception\InvalidActionException;
use Craod\Api\Rest\Exception\InvalidControllerException;
use Craod\Api\Rest\Exception\NotFoundException;
use Craod\Api\Utility\Settings;
use Slim\Slim;

/**
 * The REST API
 *
 * @package Craod\Api\Rest
 */
class Application extends Slim implements CraodApplication {

	/**
	 * The array of routes to their methods and controller actions
	 *
	 * @var array
	 */
	protected static $routeMap;

	/**
	 * Cli constructor
	 */
	public function __construct() {
		parent::__construct(Settings::get('Craod.Api.rest.settings'));
		$this->initialize();
	}

	/**
	 * Initialize the class by loading the Routes yaml files
	 *
	 * @return void
	 */
	public function initialize() {
		$this->contentType('application/json');
		$this->addCorsSupport();
		$this->loadRoutes();
		$this->error([$this, 'handleError']);
		$this->notFound([$this, 'handleNotFound']);
	}

	/**
	 * Adds CORS support by returning 200 on every OPTIONS request and returning the proper headers to allow CORS
	 *
	 * @return void
	 */
	public function addCorsSupport() {
		$self = $this;
		$this->options('/:route+', function($route) {
			$this->response->status(HttpStatusCodes::OK);
		});
		$this->hook('slim.before.dispatch', function () use ($self) {
			$self->response->header('Access-Control-Allow-Origin', '*');
			$self->response->header('Access-Control-Allow-Headers', 'Cache-Control, Pragma, Origin, Authorization, Content-Type, X-Requested-With');
			$self->response->header('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE');
		});
	}

	/**
	 * Given a class path, return an initialized controller
	 *
	 * @param string $controllerClassPath
	 * @return AbstractController
	 * @throws InvalidControllerException
	 */
	public function getController($controllerClassPath) {
		if (!class_exists($controllerClassPath)) {
			throw new InvalidControllerException('Controller class does not exist: ' . $controllerClassPath, 1448241671);
		} else if (!is_subclass_of($controllerClassPath, AbstractController::class)) {
			throw new InvalidControllerException('Class given is not a controller class: ' . $controllerClassPath, 1448241672);
		}
		return new $controllerClassPath($this);
	}

	/**
	 * Load the routes using the Settings utility
	 *
	 * @return void
	 * @throws \Craod\Api\Exception\InvalidSettingsBundleException
	 */
	public function loadRoutes() {
		$self = $this;
		$routesBundle = Settings::get('Craod.Api.rest.routes.bundle', 'Routes');
		Settings::loadBundle($routesBundle);
		self::$routeMap = Settings::getLoadedData($routesBundle);
		foreach (self::$routeMap as $route => $routeData) {
			$controllerClassPath = $routeData['controller'];
			$actions = $routeData['actions'];
			foreach ($actions as $method => $parameters) {
				$this->map($route, function () use ($self, $route, $controllerClassPath, $parameters) {
					$self->handleRoute($controllerClassPath, $parameters, func_get_args());
				})->via(strtoupper($method));
			}
		}
	}

	/**
	 * Handle a route by instantiating a controller and executing the necessary action
	 *
	 * @param string $controllerClassPath
	 * @param array|string $parametersOrAction
	 * @param array $arguments
	 * @return void
	 * @throws InvalidControllerException
	 * @throws InvalidActionException
	 */
	public function handleRoute($controllerClassPath, $parametersOrAction, $arguments) {
		$controller = $this->getController($controllerClassPath);
		if (is_string($parametersOrAction)) {
			$parameters = ['action' => $parametersOrAction];
		} else {
			$parameters = $parametersOrAction;
		}
		$actionMethodName = $parameters['action'] . Settings::get('Craod.Api.rest.controller.actionMethodSuffix', 'Action');
		try {
			$result = json_encode(call_user_func_array([$controller, $actionMethodName], $arguments), JSON_NUMERIC_CHECK);
			if ($result === '[]') {
				$result = '{}';
			}
			$this->response->write($result);
		} catch (\ErrorException $exception) {
			if (strpos($exception->getMessage(), 'argument') !== FALSE) {
				throw new InvalidActionException($exception->getMessage(), 1448243486);
			} else if (strpos($exception->getMessage(), 'to be a valid callback') !== FALSE) {
				throw new InvalidActionException('Controller ' . $controllerClassPath . ' has no method called ' . $actionMethodName, 1448243487);
			} else {
				throw new Exception($exception);
			}
		}
	}

	/**
	 * Show the errors exclusively as JSON
	 *
	 * @param \Exception $exception
	 */
	public function handleError(\Exception $exception) {
		if (!$exception instanceof RestException) {
			$exception = new RestException($exception);
		}
		$this->response->setStatus($exception->getStatusCode());
		$this->response->write(json_encode($exception->jsonSerialize(), JSON_NUMERIC_CHECK));
	}

	/**
	 * Handle a "not found" route by throwing an exception and allowing the error handler handle the error
	 *
	 * @return void
	 * @throws NotFoundException
	 */
	public function handleNotFound() {
		throw new NotFoundException('Invalid route specified', 1448240606);
	}
}