<?php

namespace Craod\Api\Repository;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\EntityRepository;

/**
 * Class UserRoleRepository
 *
 * @package Craod\Api\Repository
 */
class AbstractRepository extends EntityRepository {

	/**
	 * Count all objects of this entity type
	 *
	 * @return integer
	 */
	public function count() {
		return $this->countBy([]);
	}

	/**
	 * Perform a "count by" operation on the given criteria
	 *
	 * @param $criteria
	 * @return integer
	 */
	public function countBy($criteria) {
		$persister = $this->_em->getUnitOfWork()->getEntityPersister($this->_entityName);
		$statement = $this->_em->getConnection()->prepare($persister->getCountSQL($criteria));
		$statement->execute(array_values($criteria));
		return $statement->fetchColumn(0);
	}
}