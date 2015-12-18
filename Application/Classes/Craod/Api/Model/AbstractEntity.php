<?php

namespace Craod\Api\Model;

use Craod\Api\Rest\Annotation\Api\Writable;
use Craod\Api\Utility\Annotations;
use Craod\Api\Utility\Cache;
use Craod\Api\Utility\Database;
use Craod\Api\Repository\AbstractRepository;

use Craod\Api\Utility\Search;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\EntityManager;

/**
 * Class AbstractEntity
 *
 * @package Craod\Api\Model
 * @ORM\MappedSuperclass
 * @ORM\HasLifecycleCallbacks
 */
abstract class AbstractEntity implements \JsonSerializable {

	/**
	 * @var boolean
	 * @ORM\Column(type="boolean")
	 */
	protected $active = FALSE;

	/**
	 * @var string
	 * @ORM\Id
	 * @ORM\Column(type="guid", unique=TRUE)
	 * @ORM\GeneratedValue(strategy="CUSTOM")
	 * @ORM\CustomIdGenerator(class="Craod\Api\Doctrine\ORM\UuidGenerator")
	 */
	protected $guid;

	/**
	 * @var \DateTime
	 * @ORM\Column(type="datetimetz")
	 */
	protected $created;

	/**
	 * Assign a creation date before create if one does not exist
	 *
	 * @return void
	 * @ORM\PrePersist
	 */
	public function addCreationDate() {
		if (!$this->created) {
			$this->created = new \DateTime();
		}
	}

	/**
	 * Persist this entity and flush
	 *
	 * @return AbstractEntity
	 */
	public function save() {
		$entityManager = Database::getEntityManager();
		$entityManager->persist($this);
		$entityManager->flush();
		return $this;
	}

	/**
	 * Delete this entity
	 *
	 * @return void
	 */
	public function delete() {
		$entityManager = Database::getEntityManager();
		$entityManager->remove($this);
		$entityManager->flush();
	}

	/**
	 * Serialize this object into a json array
	 *
	 * @return array
	 */
	public function jsonSerialize() {
		$value = [];
		$entityManager = Database::getEntityManager();
		$metadata = $entityManager->getClassMetadata(get_called_class());
		foreach ($metadata->getReflectionProperties() as $property => $reflectionProperty) {
			$getterMethodName = 'get' . ucfirst($property);
			if (!method_exists($this, $getterMethodName)) {
				$getterMethodName = 'is' . ucfirst($property);
			}
			if (!method_exists($this, $getterMethodName)) {
				continue;
			}
			try {
				$value[$property] = call_user_func([$this, $getterMethodName]);
			} catch (\Exception $exception) {
			}
		}

		return $value;
	}

	/**
	 * Get the repository for this model
	 *
	 * @return AbstractRepository
	 */
	public static function getRepository() {
		/** @var EntityManager $entityManager */
		$entityManager = Database::getEntityManager();
		return $entityManager->getRepository(get_called_class());
	}

	/**
	 * @return boolean
	 */
	public function isActive() {
		return $this->active;
	}

	/**
	 * @param boolean $active
	 * @return User
	 */
	public function setActive($active) {
		$this->active = $active;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getGuid() {
		return $this->guid;
	}

	/**
	 * @param string $guid
	 * @return AbstractEntity
	 */
	public function setGuid($guid) {
		$this->guid = $guid;
		return $this;
	}

	/**
	 * @return \DateTime
	 */
	public function getCreated() {
		return $this->created;
	}

	/**
	 * @param \DateTime $created
	 * @return AbstractEntity
	 */
	public function setCreated($created) {
		$this->created = $created;
		return $this;
	}

	/**
	 * Get a list of property names that have the writable annotation
	 *
	 * @return array
	 */
	public static function getWritableProperties() {
		$classPath = get_called_class();
		if (!Cache::has($classPath . ':writableProperties')) {
			$writableProperties = [];
			$entityManager = Database::getEntityManager();
			$metadata = $entityManager->getClassMetadata($classPath);
			$reflectionClass = new \ReflectionClass($classPath);
			$reader = Annotations::getReader();
			foreach ($reflectionClass->getProperties() as $property) {
				/** @var Writable $propertyAnnotation */
				$propertyAnnotation = $reader->getPropertyAnnotation($property, Writable::class);
				if ($propertyAnnotation !== NULL) {
					$propertyName = $property->getName();
					$writableProperties[$propertyName] = $metadata->getTypeOfField($propertyName);
				}
			}
			Cache::setAsObject($classPath . ':writableProperties', $writableProperties);
		}

		return Cache::getAsObject($classPath . ':writableProperties');
	}
}