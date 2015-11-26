<?php

namespace Craod\Api\Model;

use Craod\Api\Utility\Database;

use Doctrine\ORM\Mapping as ORM;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;

/**
 * Class AbstractEntity
 *
 * @package Craod\Api\Model
 * @ORM\MappedSuperclass
 * @ORM\HasLifecycleCallbacks
 */
abstract class AbstractEntity implements \JsonSerializable {

	/**
	 * @var string
	 * @ORM\Id
	 * @ORM\Column(type="string", unique=TRUE)
	 * @ORM\GeneratedValue(strategy="CUSTOM")
	 * @ORM\CustomIdGenerator(class="Kiefernwald\DoctrineUuid\Doctrine\ORM\UuidGenerator")
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
	public function beforeCreate() {
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
	 * @return EntityRepository
	 */
	public static function getRepository() {
		/** @var EntityManager $entityManager */
		$entityManager = Database::getEntityManager();
		return $entityManager->getRepository(get_called_class());
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
}