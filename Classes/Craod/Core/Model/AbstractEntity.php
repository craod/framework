<?php

namespace Craod\Core\Model;

use Craod\Core\Utility\Database;
use Craod\Core\Repository\AbstractRepository;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\EntityManager;

/**
 * Class AbstractEntity
 *
 * @package Craod\Core\Model
 * @ORM\MappedSuperclass
 */
abstract class AbstractEntity implements \JsonSerializable {

	/**
	 * @var string
	 * @ORM\Id
	 * @ORM\Column(type="guid", unique=TRUE)
	 * @ORM\GeneratedValue(strategy="CUSTOM")
	 * @ORM\CustomIdGenerator(class="Craod\Core\Orm\UuidGenerator")
	 */
	protected $guid;

	/**
	 * @var \DateTime
	 * @ORM\Column(type="datetimetz")
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