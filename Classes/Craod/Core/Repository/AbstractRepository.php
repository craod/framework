<?php

namespace Craod\Core\Repository;

use Craod\Core\Orm\Expression\ContainedExpression;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query\QueryException;

/**
 * Extends the standard EntityRepository by providing methods for count operations as well as using "like", and finding by jsonb fields
 *
 * @package Craod\Core\Repository
 */
abstract class AbstractRepository extends EntityRepository {

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
	 * @param array $criteria
	 * @return integer
	 * @throws QueryException
	 */
	public function countBy(array $criteria) {
		$queryBuilder = $this->createQueryBuilder('entity');
		$queryBuilder->select('COUNT(entity)');
		$index = 0;
		foreach ($criteria as $parentProperty => $value) {
			$parentPropertyType = $this->_class->fieldMappings[$parentProperty];
			if (is_array($value)) {
				if ($parentPropertyType['type'] !== 'jsonb') {
					throw new QueryException('Cannot do jsonb search inside property that is not of jsonb type: ' . $parentProperty, 1448919557);
				}
				foreach ($value as $property => $propertyValue) {
					$queryBuilder->andWhere('GET_JSON_FIELD(entity.' . $parentProperty . ', \'' . $property . '\') = :property' . $index);
					$queryBuilder->setParameter('property' . $index, $propertyValue);
					$index++;
				}
			} else {
				if ($value instanceof ContainedExpression) {
					$queryBuilder->andWhere(':property' . $index . ' MEMBER OF entity.' . $value->expression);
				} else {
					$queryBuilder->andWhere('entity.' . $parentProperty . ' = :property' . $index);
				}
				$queryBuilder->setParameter('property' . $index, $value);
			}
		}
		return $queryBuilder->getQuery()->getSingleScalarResult();
	}

	/**
	 * Count criteria using "like" comparison. This is not to be used for intensive tasks, as that should be taken care of by ElasticSearch.
	 * To search inside jsonb fields, the value must be an associative array with the sub-property names and their values.
	 *
	 * @param array $criteria
	 * @return integer
	 * @throws QueryException
	 */
	public function countLike(array $criteria) {
		$queryBuilder = $this->createQueryBuilder('entity');
		$queryBuilder->select('COUNT(entity.*)');
		$index = 0;
		foreach ($criteria as $parentProperty => $value) {
			$parentPropertyType = $this->_class->fieldMappings[$parentProperty];
			if (is_array($value)) {
				if ($parentPropertyType['type'] !== 'jsonb') {
					throw new QueryException('Cannot do jsonb search inside property that is not of jsonb type: ' . $parentProperty, 1448919557);
				}
				foreach ($value as $property => $propertyValue) {
					$queryBuilder->andWhere('GET_JSON_FIELD(entity.' . $parentProperty . ', \'' . $property . '\') like :property' . $index);
					$queryBuilder->setParameter('property' . $index, $propertyValue);
					$index++;
				}
			} else {
				switch ($parentPropertyType['type']) {
					case 'string':
						$queryBuilder->andWhere('entity.' . $parentProperty . ' like :property' . $index);
						$queryBuilder->setParameter('property' . $index, $value);
						$index++;
						break;
					default:
						$queryBuilder->andWhere('CAST(entity.' . $parentProperty . ' AS string) like :property' . $index);
						$queryBuilder->setParameter('property' . $index, $value);
						$index++;
						break;
				}
			}
		}
		return $queryBuilder->getQuery()->getSingleScalarResult();
	}

	/**
	 * We extend findOneBy so that if a search is done for jsonb properties, we take over, if not we allow the parent to take over
	 *
	 * @param array $criteria
	 * @param array|null $orderBy
	 * @return object|null The entity instance or NULL if the entity can not be found.
	 */
	public function findOneBy(array $criteria, array $orderBy = NULL) {
		$result = $this->findBy($criteria, $orderBy, 1);
		if (count($result) > 0) {
			return $result[0];
		} else {
			return NULL;
		}
	}

	/**
	 * We extend findBy so that if a search is done for jsonb properties, we take over, if not we allow the parent to take over
	 *
	 * @param array $criteria
	 * @param array $orderBy
	 * @param integer $limit
	 * @param integer $offset
	 * @return array
	 * @throws QueryException
	 */
	public function findBy(array $criteria, array $orderBy = NULL, $limit = NULL, $offset = NULL) {
		$queryBuilder = $this->createQueryBuilder('entity');
		$index = 0;
		foreach ($criteria as $parentProperty => $value) {
			$parentPropertyType = $this->_class->fieldMappings[$parentProperty];
			if (is_array($value)) {
				if ($parentPropertyType['type'] !== 'jsonb') {
					throw new QueryException('Cannot do jsonb search inside property that is not of jsonb type: ' . $parentProperty, 1448919557);
				}
				foreach ($value as $property => $propertyValue) {
					$queryBuilder->andWhere('GET_JSON_FIELD(entity.' . $parentProperty . ', \'' . $property . '\') = :property' . $index);
					$queryBuilder->setParameter('property' . $index, $propertyValue);
					$index++;
				}
			} else {
				if ($value instanceof ContainedExpression) {
					$queryBuilder->andWhere(':property' . $index . ' MEMBER OF entity.' . $value->expression);
				} else {
					$queryBuilder->andWhere('entity.' . $parentProperty . ' = :property' . $index);
				}
				$queryBuilder->setParameter('property' . $index, $value);
				$index++;
			}
		}
		if ($orderBy !== NULL) {
			foreach ($orderBy as $value) {
				$parts = explode(' ', $value);
				if (count($parts) > 0) {
					$queryBuilder->addOrderBy('entity.' . $parts[0], $parts[1]);
				} else {
					$queryBuilder->addOrderBy('entity.' . $value);
				}
			}
		}
		if ($offset !== NULL) {
			$queryBuilder->setFirstResult($offset);
		}
		if ($limit !== NULL) {
			$queryBuilder->setMaxResults($limit);
		}
		return $queryBuilder->getQuery()->getResult();
	}

	/**
	 * Find criteria using "like" comparison. This is not to be used for intensive tasks, as that should be taken care of by ElasticSearch.
	 * To search inside jsonb fields, the value must be an associative array with the sub-property names and their values.
	 *
	 * @param array $criteria
	 * @param array $orderBy
	 * @param integer $limit
	 * @param integer $offset
	 * @return array
	 * @throws QueryException
	 */
	public function findLike(array $criteria, array $orderBy = NULL, $limit = NULL, $offset = NULL) {
		$queryBuilder = $this->createQueryBuilder('entity');
		$index = 0;
		foreach ($criteria as $parentProperty => $value) {
			$parentPropertyType = $this->_class->fieldMappings[$parentProperty];
			if (is_array($value)) {
				if ($parentPropertyType['type'] !== 'jsonb') {
					throw new QueryException('Cannot do jsonb search inside property that is not of jsonb type: ' . $parentProperty, 1448919557);
				}
				foreach ($value as $property => $propertyValue) {
					$queryBuilder->andWhere('GET_JSON_FIELD(entity.' . $parentProperty . ', \'' . $property . '\') like :property' . $index);
					$queryBuilder->setParameter('property' . $index, $propertyValue);
					$index++;
				}
			} else {
				switch ($parentPropertyType['type']) {
					case 'string':
						$queryBuilder->andWhere('entity.' . $parentProperty . ' like :property' . $index);
						$queryBuilder->setParameter('property' . $index, $value);
						$index++;
						break;
					default:
						$queryBuilder->andWhere('CAST(entity.' . $parentProperty . ' AS string) like :property' . $index);
						$queryBuilder->setParameter('property' . $index, $value);
						$index++;
						break;
				}
			}
		}
		if ($orderBy !== NULL) {
			foreach ($orderBy as $value) {
				$parts = explode(' ', $value);
				if (count($parts) > 0) {
					$queryBuilder->addOrderBy('entity.' . $parts[0], $parts[1]);
				} else {
					$queryBuilder->addOrderBy('entity.' . $value);
				}
			}
		}
		if ($offset !== NULL) {
			$queryBuilder->setFirstResult($offset);
		}
		if ($limit !== NULL) {
			$queryBuilder->setMaxResults($limit);
		}
		return $queryBuilder->getQuery()->getResult();
	}
}