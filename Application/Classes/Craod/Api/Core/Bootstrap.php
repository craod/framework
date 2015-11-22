<?php

namespace Craod\Api\Core;

use Craod\Api\Utility\DependencyInjector;
use Craod\Api\Utility\Settings;

use Doctrine\Common\Cache\RedisCache;
use Doctrine\DBAL\Types\Type;
use Doctrine\ORM\Configuration;
use Doctrine\ORM\EntityManager;

use Symfony\Component\ClassLoader\Psr4ClassLoader;

/**
 * Class Bootstrap
 *
 * @package Craod\Api\Core
 */
class Bootstrap {

	const ROOT_PATH = __DIR__  . '/../../../../';
	const MODEL_PATH = self::ROOT_PATH . 'Classes/Craod/Api/Model';
	const MODEL_PROXY_PATH = self::ROOT_PATH . 'Classes/Craod/Api/Proxy';

	/**
	 * @var ApplicationInterface
	 */
	protected static $application;

	/**
	 * @var Psr4ClassLoader
	 */
	protected static $classLoader;

	/**
	 * @var \Predis\Client
	 */
	protected static $cache;

	/**
	 * @var EntityManager
	 */
	protected static $entityManager;

	/**
	 * Get the current application context
	 *
	 * @return string
	 */
	public static function getContext () {
		return ApplicationInterface::DEVELOPMENT;
	}

	/**
	 * Initialize the class loader - both ours and the composer one
	 *
	 * @return void
	 */
	public static function initialize () {
		error_reporting(E_ALL);
		require_once self::ROOT_PATH . 'Vendor/autoload.php';
		self::$classLoader = new Psr4ClassLoader();
		self::$classLoader->addPrefix('Craod', self::ROOT_PATH . 'Classes/Craod');
		self::$classLoader->register();
	}

	/**
	 * Load the configuration for the given context
	 *
	 * @return void
	 */
	public static function loadConfiguration () {
		Settings::initialize();
	}

	/**
	 * Load the dependency injector
	 *
	 * @return void
	 */
	public static function loadDependencyInjector () {
		DependencyInjector::initialize();
	}

	/**
	 * Initialize Redis cache
	 *
	 * @return void
	 */
	public static function initializeCache() {
		self::$cache = new \Predis\Client(Settings::get('Craod.Api.cache'));
		DependencyInjector::set('cache', self::$cache);
	}

	/**
	 * Initialize the database
	 *
	 * @throws \Doctrine\ORM\ORMException
	 */
	public static function initializeDatabase () {
		$cache = new RedisCache();
		$configuration = new Configuration();
		$configuration->setMetadataCacheImpl($cache);
		$configuration->setMetadataDriverImpl($configuration->newDefaultAnnotationDriver(self::MODEL_PATH));
		$configuration->setQueryCacheImpl($cache);
		$configuration->setProxyDir(self::MODEL_PROXY_PATH);
		$configuration->setProxyNamespace('Craod\Api\Proxy');
		$configuration->setAutoGenerateProxyClasses(TRUE);

		$parameters = Settings::get('Craod.Api.database.settings');

		self::$entityManager = EntityManager::create($parameters, $configuration);
		DependencyInjector::set('entityManager', self::$entityManager);
		DependencyInjector::set('database', self::$entityManager->getConnection());

		$databasePlatform = self::$entityManager->getConnection()->getDatabasePlatform();
		foreach (Settings::get('Craod.Api.doctrine.types') as $type => $typeClassPath) {
			Type::addType($type, $typeClassPath);
			$databasePlatform->registerDoctrineTypeMapping($type, $type);
		}
	}

	/**
	 * Run the application in the given class path. Returns TRUE if the class was found, FALSE otherwise
	 *
	 * @param string $classPath
	 * @return boolean
	 */
	public static function run ($classPath) {
		if (!class_exists($classPath)) {
			return FALSE;
		} else {
			self::$application = new $classPath();
			self::$application->run();
			return TRUE;
		}
	}
}