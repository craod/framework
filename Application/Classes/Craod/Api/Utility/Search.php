<?php

namespace Craod\Api\Utility;

use Craod\Api\Exception\Exception;
use Craod\Api\Exception\ProtectedAccessException;
use Craod\Api\Object\ObjectAccessor;
use Craod\Api\Model\SearchableEntity;
use Craod\Api\Model\AbstractEntity;

use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

use Elasticsearch\ClientBuilder;
use Elasticsearch\Client;
use Elasticsearch\Common\Exceptions\Missing404Exception;

/**
 * The elastic search indexer utility
 *
 * @package Craod\Api\Utility
 */
class Search implements AbstractUtility {

	/**
	 * @var Client
	 */
	protected static $client;

	/**
	 * The name of the craod index in elasticsearch
	 *
	 * @var string
	 */
	protected static $indexName;

	/**
	 * Initialize the Predis cache client
	 *
	 * @return void
	 */
	public static function initialize() {
		self::$client = ClientBuilder::create()->build();
		self::$indexName = Settings::get('Craod.Api.search.index');
	}

	/**
	 * Returns TRUE if the client has been initialized, FALSE otherwise
	 *
	 * @return boolean
	 */
	public static function isInitialized() {
		return self::$client !== NULL;
	}

	/**
	 * Index the given entity - set it inside the craod index
	 *
	 * @param SearchableEntity $entity
	 * @return boolean
	 * @throws ProtectedAccessException
	 */
	public static function index(SearchableEntity $entity) {
		$entityClassPath = get_class($entity);
		$indexArray = [
			'index' => self::$indexName,
			'type' => self::getTypeNameForEntity($entityClassPath),
			'id' => $entity->getGuid(),
			'body' => []
		];

		foreach ($entity::getSearchableProperties() as $propertyName => $columnType) {
			$value = ObjectAccessor::getProperty($entity, $propertyName);
			$indexArray['body'][$propertyName] = self::castValueForIndexing($value, $columnType);
		}

		$response = self::$client->index($indexArray);

		// Elasticsearch returns a "created": 1 value if the delete operation went well
		return (is_array($response) && isset($response['created']) && $response['created']);
	}

	/**
	 * Remove the given entity from the index
	 *
	 * @param AbstractEntity $entity
	 * @return boolean
	 * @throws ProtectedAccessException
	 */
	public static function delete(AbstractEntity $entity) {
		$entityClassPath = get_class($entity);
		$indexArray = [
			'index' => self::$indexName,
			'type' => self::getTypeNameForEntity($entityClassPath),
			'id' => $entity->getGuid()
		];
		try {
			$response = self::$client->delete($indexArray);
		} catch (Missing404Exception $exception) {
			$response = NULL;
		}

		// Elasticsearch returns a "found": 1 value if the delete operation went well
		return (is_array($response) && isset($response['found']) && $response['found']);
	}

	/**
	 * Cast the given value to the necessary format for indexing based on its column type (the type in the Column annotation)
	 *
	 * @param mixed $value
	 * @param string $columnType
	 * @return mixed
	 */
	public static function castValueForIndexing($value, $columnType) {
		switch ($columnType) {
			default:
				if (is_subclass_of($columnType, AbstractEntity::class)) {
					if ($value instanceof Collection) {
						$castValue = [];
						foreach ($value->getValues() as $propertyValue) {
							$castValue[] = ($propertyValue instanceof AbstractEntity) ? $propertyValue->getGuid() : $propertyValue;
						}
					} else if ($value instanceof AbstractEntity) {
						$castValue = $value->getGuid();
					} else {
						$castValue = $value;
					}
				} else {
					$castValue = $value;
				}
				break;
			case 'datetimetz':
				/** @var \DateTime $value */
				$castValue = $value->format('Y-m-d H:i:s');
				break;
		}
		return $castValue;
	}

	/**
	 * Get the Elasticsearch type name for the given entity
	 *
	 * @param SearchableEntity|string $entityOrClassPath
	 * @return string
	 */
	public static function getTypeNameForEntity($entityOrClassPath) {
		return Database::getTableName($entityOrClassPath);
	}

		/**
	 * This utility cannot run if the settings have not been loaded yet
	 *
	 * @return array
	 */
	public static function getRequiredUtilities() {
		return [
			Annotations::class,
			Cache::class,
			Database::class
		];
	}

	/**
	 * @return Client
	 */
	public static function getClient() {
		return self::$client;
	}

	/**
	 * @return string
	 */
	public static function getIndexName() {
		return self::$indexName;
	}
}