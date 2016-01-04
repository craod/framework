<?php

namespace Craod\Core\Utility;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Types\Type;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Configuration;
use Doctrine\ORM\Mapping\Table;
use Doctrine\ORM\Mapping\Driver\AnnotationDriver;
use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\Common\Annotations\AnnotationRegistry;

/**
 * The database utility
 *
 * @package Craod\Core\Utility
 */
class Database implements AbstractUtility {

	/**
	 * @var string
	 */
	protected static $modelPath;

	/**
	 * @var string
	 */
	protected static $modelProxyPath;

	/**
	 * @var Connection
	 */
	protected static $connection;

	/**
	 * @var EntityManager
	 */
	protected static $entityManager;

	/**
	 * @var array
	 */
	protected static $tableNames;

	/**
	 * Initialize the Predis cache client
	 *
	 * @return void
	 */
	public static function initialize() {
		$driver = new AnnotationDriver(new AnnotationReader(), [self::$modelPath]);
		AnnotationRegistry::registerLoader('class_exists');

		$configuration = new Configuration();
		$configuration->setMetadataDriverImpl($driver);
		$configuration->setProxyDir(self::$modelProxyPath);
		$configuration->setProxyNamespace('Craod\Core\Proxy');
		$configuration->setAutoGenerateProxyClasses(TRUE);

		if (Cache::isInitialized()) {
			$configuration->setMetadataCacheImpl(Cache::getProvider());
			$configuration->setQueryCacheImpl(Cache::getProvider());
		}

		$parameters = Settings::get('Craod.Core.Database.settings');

		foreach (Settings::get('Craod.Core.Database.doctrine.functions', []) as $type => $functions) {
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
		foreach (Settings::get('Craod.Core.Database.doctrine.types', []) as $type => $typeClassPath) {
			Type::addType($type, $typeClassPath);
			$databasePlatform->registerDoctrineTypeMapping($type, $type);
		}

		self::$tableNames = Cache::getAsObject('Database:tableNames', []);
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
	 * Get the table name for the given domain model
	 *
	 * @param object|string $domainModelOrClassPath
	 * @return string
	 */
	public static function getTableName($domainModelOrClassPath) {
		if (is_string($domainModelOrClassPath)) {
			$classPath = $domainModelOrClassPath;
		} else {
			$classPath = get_class($domainModelOrClassPath);
		}
		if (!isset(self::$tableNames[$classPath])) {
			$reader = Annotations::getReader();
			$reflectionClass = new \ReflectionClass($classPath);
			$tableAnnotation = $reader->getClassAnnotation($reflectionClass, Table::class);
			self::$tableNames[$classPath] = $tableAnnotation->name;
			Cache::setAsObject('Database:tableNames', self::$tableNames);
		}
		return self::$tableNames[$classPath];
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

	/**
	 * @return string
	 */
	public static function getModelPath() {
		return self::$modelPath;
	}

	/**
	 * @param string $modelPath
	 * @return void
	 */
	public static function setModelPath($modelPath) {
		self::$modelPath = $modelPath;
	}

	/**
	 * @return string
	 */
	public static function getModelProxyPath() {
		return self::$modelProxyPath;
	}

	/**
	 * @param string $modelProxyPath
	 * @return void
	 */
	public static function setModelProxyPath($modelProxyPath) {
		self::$modelProxyPath = $modelProxyPath;
	}
}