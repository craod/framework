<?php

namespace Craod\Api\Utility;

use Craod\Api\Core\Bootstrap;
use Doctrine\DBAL\Connection;
use Doctrine\ORM\EntityManager;
use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\Common\Annotations\AnnotationRegistry;
use Doctrine\DBAL\Types\Type;
use Doctrine\ORM\Configuration;
use Doctrine\ORM\Mapping\Driver\AnnotationDriver;

/**
 * The database utility
 *
 * @package Craod\Api\Utility
 */
class Database implements AbstractUtility {

	const MODEL_PATH = Bootstrap::ROOT_PATH . 'Classes/Craod/Api/Model';
	const MODEL_PROXY_PATH = Bootstrap::ROOT_PATH . 'Classes/Craod/Api/Proxy';

	/**
	 * @var Connection
	 */
	protected static $connection;

	/**
	 * @var EntityManager
	 */
	protected static $entityManager;

	/**
	 * Initialize the Predis cache client
	 *
	 * @return void
	 */
	public static function initialize() {
		$driver = new AnnotationDriver(new AnnotationReader(), [self::MODEL_PATH]);
		AnnotationRegistry::registerLoader('class_exists');

		$configuration = new Configuration();
		$configuration->setMetadataDriverImpl($driver);
		$configuration->setProxyDir(self::MODEL_PROXY_PATH);
		$configuration->setProxyNamespace('Craod\Api\Proxy');
		$configuration->setAutoGenerateProxyClasses(TRUE);

		if (Cache::isInitialized()) {
			$configuration->setMetadataCacheImpl(Cache::getProvider());
			$configuration->setQueryCacheImpl(Cache::getProvider());
		}

		$parameters = Settings::get('Craod.Api.database.settings');

		foreach (Settings::get('Craod.Api.doctrine.functions') as $type => $functions) {
			foreach ($functions as $name => $functionClassPath) {
				switch ($type) {
					case 'string':
						$configuration->addCustomStringFunction($name, $functionClassPath);
						break;
					case 'datetime':
						$configuration->addCustomDatetimeFunction($name, $functionClassPath);
						break;
					case 'numeric':
						$configuration->addCustomNumericFunction($name, $functionClassPath);
						break;
				}
			}
		}

		self::$entityManager = EntityManager::create($parameters, $configuration);
		self::$connection = self::$entityManager->getConnection();

		$databasePlatform = self::$connection->getDatabasePlatform();
		foreach (Settings::get('Craod.Api.doctrine.types') as $type => $typeClassPath) {
			Type::addType($type, $typeClassPath);
			$databasePlatform->registerDoctrineTypeMapping($type, $type);
		}
	}

	/**
	 * Returns TRUE if the entity manager has been initialized, FALSE otherwise
	 *
	 * @return boolean
	 */
	public static function isInitialized() {
		return self::$entityManager !== NULL;
	}

	/**
	 * This utility cannot run if the settings have not been loaded yet
	 *
	 * @return array
	 */
	public static function getRequiredUtilities() {
		return [
			Settings::class
		];
	}

	/**
	 * @return EntityManager
	 */
	public static function getEntityManager() {
		return self::$entityManager;
	}

	/**
	 * @return Connection
	 */
	public static function getConnection() {
		return self::$connection;
	}
}