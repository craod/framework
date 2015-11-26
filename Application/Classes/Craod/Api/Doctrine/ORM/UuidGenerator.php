<?php

namespace Craod\Api\Doctrine\ORM;

use Craod\Api\Model\AbstractEntity;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\EntityManager;


/**
 * Extends v4 UUID generator by preventing uuid generation if a guid is already present
 *
 * @package Craod\Api\Doctrine\ORM
 */
class UuidGenerator extends \Kiefernwald\DoctrineUuid\Doctrine\ORM\UuidGenerator {

	/**
	 * Generates an identifier for an entity. If the entity is an AbstractEntity that already has a guid, return that one instead
	 *
	 * @param EntityManager $entityManager
	 * @param Entity $entity
	 *
	 * @return mixed
	 */
	public function generate(EntityManager $entityManager, $entity) {
		if ($entity instanceof AbstractEntity && $entity->getGuid() !== NULL) {
			return $entity->getGuid();
		} else {
			return parent::generate($entityManager, $entity);
		}
	}
}
