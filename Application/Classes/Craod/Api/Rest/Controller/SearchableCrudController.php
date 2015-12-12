<?php

namespace Craod\Api\Rest\Controller;

use Craod\Api\Exception\ModelNotSearchableException;
use Craod\Api\Model\SearchableEntity;
use Craod\Api\Rest\Application;
use Craod\Api\Repository\SearchableRepository;
use Craod\Api\Rest\Exception\ControllerException;

/**
 * Controller class for default CRUD actions
 *
 * @package Craod\Api\Rest\Controller
 * @property SearchableRepository $entityRepository
 */
class SearchableCrudController extends CrudController {

	/**
	 * Ensure this controller throws an error if the entity class is not a SearchableEntity
	 *
	 * @param Application $application
	 * @throws ModelNotSearchableException
	 */
	public function __construct(Application $application) {
		parent::__construct($application);
		if (!is_subclass_of($this->entityClass, SearchableEntity::class)) {
			throw new ModelNotSearchableException('The SearchableCrudController requires the entity class to be a SearchableEntity, ' . $this->entityClass . ' is not', 1449878314);
		}
	}

	/**
	 * Search for entities using the provided search terms. If pagination or sorting are required, alter the resulting array in kind
	 *
	 * @param array $criteria The search criteria with the key being the property name and the value being the search term
	 * @param integer $flags Whether to SORT and/or PAGINATE the results
	 * @return array
	 * @throws ControllerException
	 */
	public function search($criteria, $flags = 0) {
		$orderBy = NULL;
		if (($flags & self::SORT) === self::SORT) {
			$sortBy = $this->getRequestVariable('sortBy', FILTER_SANITIZE_STRING);
			$order = $this->getRequestVariable('order', FILTER_SANITIZE_STRING);
			if ($sortBy !== '') {
				if ($order !== '' && ($order == 'asc' || $order == 'desc')) {
					$orderBy = [$sortBy . ' ' . $order];
				} else {
					$orderBy = [$sortBy];
				}
			}
		}
		if (($flags & self::PAGINATE) === self::PAGINATE) {
			$offset = $this->getRequestVariable('offset', FILTER_SANITIZE_NUMBER_INT);
			$limit = $this->getRequestVariable('limit', FILTER_SANITIZE_NUMBER_INT);
			if (!$offset) {
				$offset = 0;
			}
			if (!$limit) {
				$limit = NULL;
			}
			try {
				$count = $this->entityRepository->countSearchBy($criteria);
				if ($limit === NULL) {
					$limit = $count;
				}
				return [
					'offset' => $offset,
					'limit' => $limit,
					'total' => $count,
					'items' => $this->entityRepository->searchBy($criteria, $orderBy, $limit, $offset)
				];
			} catch (\Exception $exception) {
				throw new ControllerException('There was an error processing your request', 1448734691);
			}
		} else {
			try {
				return $this->entityRepository->searchBy($criteria, $orderBy);
			} catch (\Exception $exception) {
				throw new ControllerException('There was an error processing your request', 1448734691);
			}
		}
	}
}