<?php

namespace Craod\Api\Rest\Controller;

use Craod\Api\Model\User;
use Craod\Api\Rest\Application;
use Craod\Api\Model\AbstractEntity;
use Craod\Api\Repository\AbstractRepository;
use Craod\Api\Rest\Exception\ControllerException;

/**
 * Controller class for default CRUD actions
 *
 * @package Craod\Api\Rest\Controller
 */
class CrudController extends AbstractController {

	const FILTER = 2;
	const PAGINATE = 4;
	const SORT = 8;

	/**
	 * The entity this controller is dealing with. Must be provided as a string but is treated as a class
	 *
	 * @var string
	 */
	protected $entityClass;

	/**
	 * The repository for the entity we are dealing with
	 *
	 * @var AbstractRepository
	 */
	protected $entityRepository;

	/**
	 * Initialize this controller with the given application
	 *
	 * @param Application $application
	 * @throws ControllerException
	 */
	public function __construct(Application $application) {
		parent::__construct($application);
		if ($this->entityClass === NULL) {
			throw new ControllerException('Controller ' . get_called_class() . ' has no entity class defined', 1448731957);
		}
		/** @var AbstractEntity $entityClass */
		$entityClass = $this->entityClass;
		$this->entityRepository = $entityClass::getRepository();
	}

	/**
	 * Count the number of entities, optionally filtered with the given filters
	 *
	 * @param integer $flags Whether to FILTER the results
	 * @return integer
	 * @throws ControllerException
	 */
	public function count($flags = 0) {
		$filters = [];
		if (($flags & self::FILTER) === self::FILTER) {
			$filters = $this->getRequestedFilters();
		}
		try {
			return $this->entityRepository->countBy($filters);
		} catch (\Exception $exception) {
			throw new ControllerException('There was an error processing your request', 1448734691);
		}
	}

	/**
	 * Get a list of all the entities. If pagination or sorting are required, alter the resulting array in kind
	 *
	 * @param integer $flags Whether to FILTER, SORT and/or PAGINATE the results
	 * @return array
	 * @throws ControllerException
	 */
	public function getAll($flags = 0) {
		$orderBy = NULL;
		$filters = [];
		if (($flags & self::FILTER) === self::FILTER) {
			$filters = $this->getRequestedFilters();
		}
		if (($flags & self::SORT) === self::SORT) {
			$sortBy = $this->getRequestVariable('sortBy', FILTER_SANITIZE_STRING);
			$order = $this->getRequestVariable('order', FILTER_SANITIZE_STRING);
			if ($sortBy !== NULL) {
				if ($order !== NULL && ($order == 'asc' || $order == 'desc')) {
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
				$count = $this->entityRepository->countBy($filters);
				if ($limit === NULL) {
					$limit = $count;
				}
				return [
					'offset' => $offset,
					'limit' => $limit,
					'total' => $count,
					'items' => $this->entityRepository->findBy($filters, $orderBy, $limit, $offset)
				];
			} catch (\Exception $exception) {
				throw new ControllerException('There was an error processing your request', 1448734691);
			}
		} else {
			try {
				return $this->entityRepository->findBy($filters, $orderBy);
			} catch (\Exception $exception) {
				throw new ControllerException('There was an error processing your request', 1448734691);
			}
		}
	}

	/**
	 * Get the filters requested through the uri, sanitized by the user role and removing unwanted property names
	 *
	 * @return array
	 */
	protected function getRequestedFilters() {
		$filters = $this->getApplication()->request->get('filters');
		if (!is_array($filters)) {
			$filters = [];
		} else {
			// "password" is always considered an insecure filter
			if (isset($filters['password'])) {
				unset($filters['password']);
			}

			// "token" is always considered an insecure filter
			if (isset($filters['token'])) {
				unset($filters['token']);
			}
		}

		// Only administrators get to see inactive entities
		if (!$this->getApplication()->getCurrentUser()->hasRole(User::ADMINISTRATOR)) {
			$filters['active'] = TRUE;
		}
		return $filters;
	}
}