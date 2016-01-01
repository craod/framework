<?php

namespace Craod\Api\Utility;

use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\Common\Annotations\CachedReader;

/**
 * The annotation utility
 *
 * @package Craod\Api\Utility
 */
class Annotations implements AbstractUtility {

	/**
	 * The annotation reader
	 *
	 * @var CachedReader
	 */
	protected static $reader;

	/**
	 * Initialize the Predis cache client
	 *
	 * @return void
	 */
	public static function initialize() {
		self::$reader = new CachedReader(
			new AnnotationReader(),
			Cache::getProvider(),
			Settings::get('Craod.Api.annotations.settings.debug')
		);
	}

	/**
	 * Returns TRUE if the reader has been initialized, FALSE otherwise
	 *
	 * @return boolean
	 */
	public static function isInitialized() {
		return self::$reader !== NULL;
	}

	/**
	 * Get the utilities required by this utility
	 *
	 * @return array
	 */
	public static function getRequiredUtilities() {
		return [Settings::class];
	}

	/**
	 * Get the client for direct manipulation
	 *
	 * @return CachedReader
	 */
	public static function getReader() {
		return self::$reader;
	}

	/**
	 * Get and cache a list of property names that have the requested annotation, skipping the need for constant reflection and also
	 * obtaining the type of property being handled
	 *
	 * @param string $classPath
	 * @param string $annotationClass
	 * @return array Returns an associative array with the property names as keys and their column types or class names as values
	 */
	public static function getPropertiesByAnnotation($classPath, $annotationClass) {
		$cacheKey = $classPath . ':propertiesByAnnotation:' . $annotationClass;
		if (!Cache::has($cacheKey)) {
			$properties = [];
			$entityManager = Database::getEntityManager();
			$metadata = $entityManager->getClassMetadata($classPath);
			$reflectionClass = new \ReflectionClass($classPath);
			$reader = Annotations::getReader();
			foreach ($reflectionClass->getProperties() as $property) {
				$propertyAnnotation = $reader->getPropertyAnnotation($property, $annotationClass);
				if ($propertyAnnotation !== NULL) {
					$propertyName = $property->getName();
					$propertyType = $metadata->getTypeOfField($propertyName);
					if ($propertyType === NULL) {
						$mapping = $metadata->getAssociationMapping($propertyName);
						$propertyType = $mapping['targetEntity'];
					}
					$properties[$propertyName] = $propertyType;
				}
			}
			Cache::setAsObject($cacheKey, $properties);
		}

		return Cache::getAsObject($cacheKey);
	}
}