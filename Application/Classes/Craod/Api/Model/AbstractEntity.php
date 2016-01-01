<?php

namespace Craod\Api\Model;

use Craod\Api\Annotation\CachedAnnotationAccessor;
use Craod\Api\Rest\Annotation as Craod;
use Craod\Api\Rest\Annotation\Api\Readable;
use Craod\Api\Rest\Annotation\Api\Writable;
use Craod\Api\Object\ObjectAccessor;
use Craod\Api\Utility\Annotations;
use Craod\Api\Utility\Cache;
use Craod\Api\Utility\Database;
use Craod\Api\Repository\AbstractRepository;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\EntityManager;
use Doctrine\Common\Collections\Collection;

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
	 * @Craod\Api\Readable
	 */
	protected $active = FALSE;

	/**
	 * @var string
	 * @ORM\Id
	 * @ORM\Column(type="guid", unique=TRUE)
	 * @ORM\GeneratedValue(strategy="CUSTOM")
	 * @ORM\CustomIdGenerator(class="Craod\Api\Orm\UuidGenerator")
	 * @Craod\Api\Readable
	 */
	protected $guid;

	/**
	 * @var \DateTime
	 * @ORM\Column(type="datetimetz")
	 * @Craod\Api\Readable
	 * @Craod\Api\Searchable
	 */
	protected $created;

	/**
	 * Assign a creation date on create
	 */
	public function __construct() {
		$this->created = new \DateTime();
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
		foreach (self::getReadableProperties() as $property => $_) {
			$propertyValue = ObjectAccessor::getProperty($this, $property);
			if ($propertyValue instanceof Collection) {
				$value[$property] = [];
				foreach ($propertyValue->getValues() as $subPropertyValue) {
					$value[$property][] = ($subPropertyValue instanceof AbstractEntity) ? $subPropertyValue->getGuid() : $subPropertyValue;
				}
			} else if ($propertyValue instanceof AbstractEntity) {
				$value[$property] = $propertyValue->getGuid();
			} else {
				$value[$property] = $propertyValue;
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
	 * Get a list of property names that have the readable annotation
	 *
	 * @return array
	 */
	public static function getReadableProperties() {
		return Annotations::getPropertiesByAnnotation(get_called_class(), Readable::class);
	}

	/**
	 * Get a list of property names that have the writable annotation
	 *
	 * @return array
	 */
	public static function getWritableProperties() {
		return Annotations::getPropertiesByAnnotation(get_called_class(), Writable::class);
	}
}