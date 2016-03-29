<?php

namespace Craod\Core\Rest;

use Craod\Core\Application as CraodApplication;
use Craod\Core\Bootstrap;
use Craod\Core\Http\HttpStatusCodes;
use Craod\Core\Rest\Exception\Exception;
use Craod\Core\Rest\Exception\InvalidControllerException;
use Craod\Core\Rest\Exception\InvalidActionException;

use Craod\Core\Rest\Exception\Exception as RestException;
use Craod\Core\Rest\Annotation\Validate;
use Craod\Core\Rest\Controller\AbstractController;
use Craod\Core\Rest\Exception\NotFoundException;
use Craod\Core\Rest\Validator\AbstractControllerValidator;
use Craod\Core\Utility\Annotations;
use Craod\Core\Utility\Settings;

use Slim\Slim;

/**
 * The REST API
 *
 * @package Craod\Api\Rest
 */
class Application extends Slim implements CraodApplication {

	/**
	 * The global application singleton
	 *
	 * @var Application
	 */
	protected static $application;

	/**
	 * The array of controllers to the available methods, with their routes
	 *
	 * @var array
	 */
	protected static $endpointSchema;

	/**
	 * Cli constructor
	 */
	public function __construct() {
		parent::__construct(Settings::get('Craod.Core.Application.settings'));
		self::$application = $this;
		$this->initialize();
	}

	/**
	 * Initialize the class by loading the Routes yaml files
	 *
	 * @return void
	 */
	public function initialize() {
		$this->contentType('application/json');
		$this->error([$this, 'handleError']);
		$this->notFound([$this, 'handleNotFound']);
		$this->addCorsSupport();
		$this->loadRoutes();
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
			$self->response->header('Access-Control-Allow-Headers', 'Cache-Control, Pragma, Origin, Authorization, Content-Type, X-Requested-With, Craod-Guid, Craod-Token');
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
	 */
	public function loadRoutes() {
		$self = $this;
		$routesBundle = Settings::get('Craod.Core.Application.routes.bundle', 'Routes');
		Settings::loadBundle($routesBundle);
		$rawRouteMap = Settings::getLoadedData($routesBundle);
		self::$endpointSchema = [];
		foreach ($rawRouteMap as $groupRoute => $groupData) {
			$controllerClassPath = $groupData['controller'];
			$schemaObject = substr(array_pop(explode('\\', $controllerClassPath)), 0, -10);
			self::$endpointSchema[$schemaObject] = [];
			foreach ($groupData['routes'] as $partialRoute => $actions) {
				$route = $groupRoute . $partialRoute;
				foreach ($actions as $method => $parametersOrAction) {
					if (is_string($parametersOrAction)) {
						$parameters = ['action' => $parametersOrAction];
					} else {
						$parameters = $parametersOrAction;
					}
					self::$endpointSchema[$schemaObject][$parameters['action']] = [
						'route' => $route,
						'method' => $method
					];
					$this->map($route, function () use ($self, $route, $controllerClassPath, $parameters) {
						$self->handleRoute($controllerClassPath, $parameters, func_get_args());
					})->via(strtoupper($method));
				}
			}
		}
	}

	/**
	 * Handle a route by instantiating a controller and executing the necessary action
	 *
	 * @param string $controllerClassPath
	 * @param array $parameters
	 * @param array $arguments
	 * @return void
	 * @throws InvalidControllerException
	 * @throws InvalidActionException
	 * @throws Exception
	 */
	public function handleRoute($controllerClassPath, $parameters, $arguments) {
		$controller = $this->getController($controllerClassPath);
		$actionMethodName = $parameters['action'] . Settings::get('Craod.Core.Application.controller.actionMethodSuffix', 'Action');
		try {
			$result = json_encode($this->executeControllerAction($controller, $actionMethodName, $arguments), JSON_NUMERIC_CHECK | JSON_FORCE_OBJECT);
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
	 * Validate the controller action and execute it if validation passes
	 *
	 * @param AbstractController $controller
	 * @param string $actionMethodName
	 * @param array $arguments
	 * @return mixed
	 * @throws \Exception
	 */
	public function executeControllerAction(AbstractController $controller, $actionMethodName, $arguments) {
		$reader = Annotations::getReader();
		$controllerReflection = new \ReflectionClass($controller);
		$actionMethodReflection = $controllerReflection->getMethod($actionMethodName);
		foreach ($reader->getMethodAnnotations($actionMethodReflection) as $annotation) {
			if ($annotation instanceof Validate) {
				$validatorClass = $annotation->validator;
				/** @var AbstractControllerValidator $validator */
				$validator = new $validatorClass($controller, $actionMethodName, $arguments, $annotation);
				$validator->validate();
			}
		}
		return call_user_func_array([$controller, $actionMethodName], $arguments);
	}

	/**
	 * Show the errors exclusively as JSON
	 *
	 * @param \Exception $exception
	 */
	public function handleError(\Exception $exception) {
		if (!($exception instanceof RestException)) {
			$exception = new RestException($exception);
		}
		$exceptionAsArray = $exception->jsonSerialize();
		if (Bootstrap::getContext() === self::PRODUCTION && strpos($exceptionAsArray['message'], 'SQLSTATE') !== FALSE) {
			// We do not wish to expose the SQL query
			$exceptionAsArray['message'] = 'SQLSTATE' . explode('SQLSTATE', $exceptionAsArray['message'])[1];
		}
		$this->halt($exception->getStatusCode(), json_encode($exceptionAsArray, JSON_NUMERIC_CHECK | JSON_FORCE_OBJECT));
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

	/**
	 * @return Application
	 */
	public static function getApplication() {
		return self::$application;
	}

	/**
	 * @return array
	 */
	public static function getEndpointSchema() {
		return self::$endpointSchema;
	}
}