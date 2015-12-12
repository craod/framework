<?php

namespace Craod\Api\Repository;

use Craod\Api\Model\SearchableEntity;
use Craod\Api\Utility\Search;

use Doctrine\ORM\Mapping as ORM;

/**
 * Extends the AbstractRepository by adding functions for searching the attached type in the index using Elasticsearch
 *
 * @package Craod\Api\Repository
 */
abstract class SearchableRepository extends AbstractRepository {

	/**
	 * Perform a search operation on the parallel Elasticsearch index using the given criteria and return the number of hits
	 *
	 * @param array $criteria
	 * @return integer
	 */
	public function countSearchBy(array $criteria) {
		$client = Search::getClient();
		$search = [
			'index' => Search::getIndexName(),
			'type' => Search::getTypeNameForEntity($this->_entityName),
			'body' => [
				'query' => [
					'match' => []
				]
			]
		];

		foreach ($criteria as $property => $value) {
			$search['body']['query']['match'][$property] = [
					'query' => $value,
					'fuzziness' => 'auto',
					'operator' => 'and'
			];
		}

		$response = $client->search($search);
		return $response['hits']['total'];
	}

	/**
	 * Perform a search operation on the parallel Elasticsearch index using the given criteria
	 *
	 * @param array $criteria
	 * @param array $orderBy
	 * @param integer $limit
	 * @param integer $offset
	 * @return SearchableEntity[]
	 */
	public function searchBy(array $criteria, array $orderBy = NULL, $limit = NULL, $offset = NULL) {
		$client = Search::getClient();
		$search = [
			'index' => Search::getIndexName(),
			'type' => Search::getTypeNameForEntity($this->_entityName),
			'body' => [
				'query' => [
					'match' => []
				]
			]
		];

		foreach ($criteria as $property => $value) {
			$search['body']['query']['match'][$property] = [
				'query' => $value,
				'fuzziness' => 'auto',
				'operator' => 'and'
			];
		}

		$response = $client->search($search);
		$queryBuilder = $this->createQueryBuilder('entity');
		foreach ($response['hits']['hits'] as $index => $hit) {
			if (isset($hit['_id'])) {
				$queryBuilder->orWhere('entity.guid = :guid' . $index);
				$queryBuilder->setParameter('guid' . $index, $hit['_id']);
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