<?php

namespace Craod\Core;

use Craod\Core\Utility as Utility;
use Craod\Core\Exception\UtilityInitializationException;

use Symfony\Component\ClassLoader\Psr4ClassLoader;

/**
 * Class Bootstrap
 *
 * @package Craod\Core
 */
class Bootstrap {

	/**
	 * @var string
	 */
	protected static $rootPath;

	/**
	 * @var Application
	 */
	protected static $application;

	/**
	 * @var Psr4ClassLoader
	 */
	protected static $classLoader;

	/**
	 * @var array
	 */
	protected static $loadedUtilities;

	/**
	 * Get the current application context
	 *
	 * @return string
	 */
	public static function getContext() {
		$environment = getenv('CRAOD_CONTEXT');
		if ($environment === FALSE) {
			return Application::DEVELOPMENT;
		} else {
			return $environment;
		}
	}

	/**
	 * Initialize the bootstrapper, optionally initializing the requested utilities in the order in which they are requested
	 *
	 * @param array $utilities
	 * @return void
	 * @throws UtilityInitializationException
	 */
	public static function initialize($rootPath, $utilities = []) {
		self::$rootPath = $rootPath;
		self::initializeClassLoader();
		self::$loadedUtilities = [];
		foreach ($utilities as $utilityClassName) {
			self::requireUtility($utilityClassName);
		}
	}

	/**
	 * Initialize the required utility by its class name
	 *
	 * @param string $utilityClassName
	 * @return void
	 * @throws UtilityInitializationException
	 */
	public static function requireUtility($utilityClassName) {
		if (!in_array($utilityClassName, self::$loadedUtilities)) {
			/** @var Utility\AbstractUtility $utility */
			$utility = $utilityClassName;
			foreach ($utility::getRequiredUtilities() as $requiredUtility) {
				if (!in_array($requiredUtility, self::$loadedUtilities)) {
					throw new UtilityInitializationException('Utility ' . $utilityClassName . ' depends on ' . $requiredUtility . ', ensure it is loaded first', 1448565354);
				}
			}

			$utility::initialize();
			self::$loadedUtilities[] = $utilityClassName;
		}
	}

	/**
	 * Initialize the class loader
	 *
	 * @return void
	 */
	public static function initializeClassLoader() {
		error_reporting(E_ALL);
		self::$classLoader = new Psr4ClassLoader();
		foreach (glob(self::$rootPath . '/Classes/*') as $filePath) {
			if (is_dir($filePath)) {
				self::$classLoader->addPrefix(basename($filePath), $filePath);
			}
		}
		self::$classLoader->register();
	}

	/**
	 * Run the application in the given class path. Returns TRUE if the class was found, FALSE otherwise
	 *
	 * @param string $classPath
	 * @return boolean
	 */
	public static function run($classPath) {
		if (!class_exists($classPath)) {
			return FALSE;
		} else {
			self::$application = new $classPath();
			self::$application->run();
			return TRUE;
		}
	}

	/**
	 * @return string
	 */
	public static function getRootPath() {
		return self::$rootPath;
	}

	/**
	 * @param string $rootPath
	 * @return void
	 */
	public static function setRootPath($rootPath) {
		self::$rootPath = $rootPath;
	}
}