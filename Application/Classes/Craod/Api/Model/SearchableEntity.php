<?php

namespace Craod\Api\Model;

use Craod\Api\Rest\Annotation\Searchable;
use Craod\Api\Repository\SearchableRepository;
use Craod\Api\Utility\Annotations;
use Craod\Api\Utility\Cache;
use Craod\Api\Utility\Database;
use Craod\Api\Utility\Search;

use Doctrine\ORM\Mapping as ORM;

/**
 * A SearchableEntity has a parallel type in elasticsearch which is composed of the properties configured using the Searchable annotation
 *
 * @package Craod\Api\Model
 * @method static SearchableRepository getRepository()
 * @ORM\HasLifecycleCallbacks
 */
abstract class SearchableEntity extends AbstractEntity {

	/**
	 * Index this class on creation or edition
	 *
	 * @return void
	 * @ORM\PostPersist
	 * @ORM\PostUpdate
	 */
	public function index() {
		Search::index($this);
	}

	/**
	 * Remove this class from the index when it is deleted
	 *
	 * @return void
	 * @ORM\PostRemove
	 */
	public function removeFromIndex() {
		Search::delete($this);
	}

	/**
	 * Get a list of property names that have the searchable annotations
	 *
	 * @return array
	 */
	public static function getSearchableProperties() {
		$classPath = get_called_class();
		if (!Cache::has($classPath . ':searchableProperties')) {
			$searchableProperties = [];
			$entityManager = Database::getEntityManager();
			$metadata = $entityManager->getClassMetadata($classPath);
			$reflectionClass = new \ReflectionClass($classPath);
			$reader = Annotations::getReader();
			foreach ($reflectionClass->getProperties() as $property) {
				/** @var Searchable $propertyAnnotation */
				$propertyAnnotation = $reader->getPropertyAnnotation($property, Searchable::class);
				if ($propertyAnnotation !== NULL) {
					$propertyName = $property->getName();
					$searchableProperties[$propertyName] = $metadata->getTypeOfField($propertyName);
				}
			}
			Cache::setAsObject($classPath . ':searchableProperties', $searchableProperties);
		}

		return Cache::getAsObject($classPath . ':searchableProperties');
	}
}