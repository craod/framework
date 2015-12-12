<?php

namespace Craod\Api\Core;

use Craod\Api\Utility as Utility;
use Symfony\Component\ClassLoader\Psr4ClassLoader;
use Craod\Api\Exception\UtilityInitializationException;

/**
 * Class Bootstrap
 *
 * @package Craod\Api\Core
 */
class Bootstrap {

	const CACHE = Utility\Cache::class;
	const DATABASE = Utility\Database::class;
	const CONFIGURATION = Utility\Settings::class;
	const ANNOTATIONS = Utility\Annotations::class;
	const SEARCH = Utility\Search::class;

	const ROOT_PATH = __DIR__  . '/../../../../';

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
	public static function initialize($utilities = []) {
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
	 * Initialize the class loader - both ours and the composer one
	 *
	 * @return void
	 */
	public static function initializeClassLoader() {
		error_reporting(E_ALL);
		require_once self::ROOT_PATH . 'Vendor/autoload.php';
		self::$classLoader = new Psr4ClassLoader();
		self::$classLoader->addPrefix('Craod', self::ROOT_PATH . 'Classes/Craod');
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
}