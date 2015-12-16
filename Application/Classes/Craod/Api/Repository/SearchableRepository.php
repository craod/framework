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

	const MULTI_MATCH = 'multi_match';

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
			'body' => ['query' => $this->createQueryArray($criteria)]
		];

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
			'body' => ['query' => $this->createQueryArray($criteria)]
		];

		if ($limit !== NULL) {
			$search['size'] = $limit;
		}

		if ($offset !== NULL) {
			$search['from'] = $offset;
		}

		if ($orderBy !== NULL) {
			$search['body']['sort'] = [];
			foreach ($orderBy as $value) {
				$parts = explode(' ', $value);
				if (count($parts) > 0) {
					$search['body']['sort'][] = [$parts[0] => $parts[1]];
				} else {
					$search['body']['sort'][] = $parts[0];
				}
			}
		}

		$response = $client->search($search);
		$queryBuilder = $this->createQueryBuilder('entity');
		foreach ($response['hits']['hits'] as $index => $hit) {
			if (isset($hit['_id'])) {
				$queryBuilder->orWhere('entity.guid = :guid' . $index);
				$queryBuilder->setParameter('guid' . $index, $hit['_id']);
			}
		}

		return $queryBuilder->getQuery()->getResult();
	}

	/**
	 * Create an Elasticsearch query array based on the given criteria
	 *
	 * @param array $criteria
	 * @return array
	 */
	protected function createQueryArray(array $criteria) {
		$searchArray = [];
		$type = $criteria['type'];
		unset($criteria['type']);
		switch ($type) {
			case self::MULTI_MATCH:
				$searchArray[$type] = [
					'fuzziness' => 'auto',
					'operator' => 'and'
				];
				break;
		}

		$searchArray[$type] = array_merge($searchArray[$type], $criteria);
		return $searchArray;
	}
}