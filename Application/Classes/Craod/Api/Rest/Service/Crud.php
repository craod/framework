<?php

namespace Craod\Api\Rest\Service;

use Craod\Api\Model\AbstractEntity;
use Craod\Api\Model\SearchableEntity;
use Craod\Api\Model\User;
use Craod\Api\Repository\SearchableRepository;
use Craod\Api\Repository\AbstractRepository;
use Craod\Api\Object\ObjectAccessor;
use Craod\Api\Utility\CastingUtility;
use Craod\Api\Rest\Application;
use Craod\Api\Rest\Exception\ControllerException;
use Craod\Api\Rest\Exception\InvalidPropertyException;
use Craod\Api\Rest\Exception\NotFoundException;
use Craod\Api\Exception\InvalidCriteriaException;
use Craod\Api\Exception\ModelNotSearchableException;

use Doctrine\DBAL\Types\Type;

/**
 * Service class for default CRUD actions for a given entity
 *
 * @package Craod\Api\Rest\Service
 */
class Crud {

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
	 * Initialize this service for the given entity class
	 *
	 * @param string $entityClass
	 * @throws ControllerException
	 */
	public function __construct($entityClass) {
		/** @var AbstractEntity $entityClass */
		$this->entityClass = $entityClass;
		$this->entityRepository = $entityClass::getRepository();
	}

	/**
	 * Count the number of entities, optionally filtered with the given filters
	 *
	 * @param integer $flags Whether to FILTER the results
	 * @return integer
	 * @throws InvalidCriteriaException
	 */
	public function count($flags = 0) {
		$filters = [];
		if (($flags & self::FILTER) === self::FILTER) {
			$filters = $this->getRequestedFilters();
		}
		try {
			return $this->entityRepository->countBy($filters);
		} catch (\Exception $exception) {
			throw new InvalidCriteriaException('There was an error processing your request', 1448734691);
		}
	}

	/**
	 * Get a list of all the entities. If pagination or sorting are required, alter the resulting array in kind
	 *
	 * @param integer $flags Whether to FILTER, SORT and/or PAGINATE the results
	 * @return array
	 * @throws InvalidCriteriaException
	 */
	public function getAll($flags = 0) {
		$orderBy = NULL;
		$filters = [];
		if (($flags & self::FILTER) === self::FILTER) {
			$filters = $this->getRequestedFilters();
		}
		if (($flags & self::SORT) === self::SORT) {
			$sortBy = filter_input(INPUT_GET, 'sortBy', FILTER_SANITIZE_STRING);
			$order = filter_input(INPUT_GET, 'order', FILTER_SANITIZE_STRING);
			if ($sortBy !== '' && $sortBy !== NULL) {
				if ($order !== '' && ($order == 'asc' || $order == 'desc')) {
					$orderBy = [$sortBy . ' ' . $order];
				} else {
					$orderBy = [$sortBy];
				}
			}
		}
		if (($flags & self::PAGINATE) === self::PAGINATE) {
			$offset = filter_input(INPUT_GET, 'offset', FILTER_SANITIZE_NUMBER_INT);
			$limit = filter_input(INPUT_GET, 'limit', FILTER_SANITIZE_NUMBER_INT);
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
				throw new InvalidCriteriaException('There was an error processing your request', 1448734691);
			}
		} else {
			try {
				return $this->entityRepository->findBy($filters, $orderBy);
			} catch (\Exception $exception) {
				throw new InvalidCriteriaException('There was an error processing your request', 1448734691);
			}
		}
	}

	/**
	 * Get an AbstractEntity by its guid, forcing an active check if the user is not an administrator, and throwing an exception if
	 * the entity is not found
	 *
	 * @param string $guid
	 * @return AbstractEntity
	 * @throws NotFoundException
	 */
	public function get($guid) {
		/** @var AbstractEntity $class */
		$class = $this->entityClass;
		$currentUser = Application::getApplication()->getCurrentUser();
		$criteria = ['guid' => $guid];
		if ($currentUser === NULL || !$currentUser->hasRole(User::ADMINISTRATOR)) {
			$criteria['active'] = TRUE;
		}
		$object = $class::getRepository()->findOneBy($criteria);
		if ($object === NULL) {
			throw new NotFoundException('There is no ' . $class . ' with guid ' . $guid, 1450218753);
		}
		return $object;
	}

	/**
	 * Create an entity using the request data
	 *
	 * @param array $requestData
	 * @return AbstractEntity
	 * @throws InvalidPropertyException
	 */
	public function create($requestData) {
		return $this->createOrUpdate($requestData);
	}

	/**
	 * Update an entity that matches the given guid
	 *
	 * @param array $requestData
	 * @param string $guid
	 * @return AbstractEntity
	 * @throws InvalidPropertyException
	 * @throws NotFoundException
	 */
	public function update($requestData, $guid) {
		return $this->createOrUpdate($requestData, $guid);
	}

	/**
	 * Create an entity or update the entity with the given guid, using the request data. Internal function that should not be used,
	 * use create or update instead
	 *
	 * @param array $requestData
	 * @param string $guid If given, edit the entity with the given guid instead of creating one
	 * @return AbstractEntity
	 * @throws InvalidPropertyException
	 * @throws NotFoundException
	 */
	protected function createOrUpdate($requestData, $guid = NULL) {
		/** @var AbstractEntity $class */
		$class = $this->entityClass;
		$repository = $class::getRepository();
		$currentUser = Application::getApplication()->getCurrentUser();

		if ($guid === NULL) {
			/** @var AbstractEntity $entity */
			$entity = new $class();
		} else {
			$parameters = ['guid' => $guid];
			if ($currentUser === NULL || !$currentUser->hasRole(User::ADMINISTRATOR)) {
				$parameters['active'] = TRUE;
			}
			$entity = $repository->findOneBy($parameters);
			if ($entity === NULL) {
				throw new NotFoundException('There is no entity of class ' . $entity . ' with guid ' . $guid, 1450309429);
			}
		}

		$propertyTypes = $entity::getWritableProperties();
		foreach ($requestData as $propertyName => $rawValue) {
			if (!isset($propertyTypes[$propertyName])) {
				throw new InvalidPropertyException('Entity class ' . $entity . ' does not have a writable property named ' . $propertyName, 1450309430);
			} else {
				$value = CastingUtility::castTo($rawValue, $propertyTypes[$propertyName]);
				ObjectAccessor::setProperty($entity, $propertyName, $value);
			}
		}

		$entity->save();
		return $entity;
	}

	/**
	 * Delete an AbstractEntity by its guid, forcing an active check if the user is not an administrator, and throwing an exception if
	 * the entity is not found
	 *
	 * @param string $guid
	 * @return boolean
	 * @throws NotFoundException
	 */
	public function delete($guid) {
		$entity = $this->get($guid);
		$entity->delete();
		return TRUE;
	}

	/**
	 * Search for entities using the provided search terms. If pagination or sorting are required, alter the resulting array in kind
	 *
	 * @param string $query The query to be applied to all searchable string properties
	 * @param integer $flags Whether to SORT and/or PAGINATE the results
	 * @return array
	 * @throws InvalidCriteriaException
	 * @throws ModelNotSearchableException
	 */
	public function search($query, $flags = 0) {
		if (!is_subclass_of($this->entityClass, SearchableEntity::class) || !is_subclass_of($this->entityRepository, SearchableRepository::class)) {
			throw new ModelNotSearchableException('Search operations are not supported for models that are not of type SearchableEntity, ' . $this->entityClass . ' is not', 1449878314);
		}

		/** @var SearchableEntity $entity */
		$entity = $this->entityClass;
		/** @var SearchableRepository $entityRepository */
		$entityRepository = $this->entityRepository;
		$criteria = [
			'type' => SearchableRepository::MULTI_MATCH,
			'fields' => [],
			'query' => $query
		];

		foreach ($entity::getSearchableProperties() as $propertyName => $propertyType) {
			if ($propertyType === Type::STRING) {
				$criteria['fields'][] = $propertyName;
			}
		}

		$orderBy = NULL;
		if (($flags & self::SORT) === self::SORT) {
			$sortBy = filter_input(INPUT_GET, 'sortBy', FILTER_SANITIZE_STRING);
			$order = filter_input(INPUT_GET, 'order', FILTER_SANITIZE_STRING);
			if ($sortBy !== '') {
				if ($order !== '' && ($order == 'asc' || $order == 'desc')) {
					$orderBy = [$sortBy . ' ' . $order];
				} else {
					$orderBy = [$sortBy];
				}
			}
		}
		if (($flags & self::PAGINATE) === self::PAGINATE) {
			$offset = filter_input(INPUT_GET, 'offset', FILTER_SANITIZE_NUMBER_INT);
			$limit = filter_input(INPUT_GET, 'limit', FILTER_SANITIZE_NUMBER_INT);
			if (!$offset) {
				$offset = 0;
			}
			if (!$limit) {
				$limit = NULL;
			}
			try {
				$count = $entityRepository->countSearchBy($criteria);
				if ($limit === NULL) {
					$limit = $count;
				}
				return [
					'offset' => $offset,
					'limit' => $limit,
					'total' => $count,
					'items' => $entityRepository->searchBy($criteria, $orderBy, $limit, $offset)
				];
			} catch (\Exception $exception) {
				throw new InvalidCriteriaException('There was an error processing your request', 1448734691);
			}
		} else {
			try {
				return $entityRepository->searchBy($criteria, $orderBy);
			} catch (\Exception $exception) {
				throw new InvalidCriteriaException('There was an error processing your request', 1448734691);
			}
		}
	}

	/**
	 * Get the filters requested through the uri, sanitized by the user role and removing unwanted property names
	 *
	 * @return array
	 */
	protected function getRequestedFilters() {
		$filters = Application::getApplication()->request->get('filters');
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
		$currentUser = Application::getApplication()->getCurrentUser();
		if ($currentUser === NULL || !$currentUser->hasRole(User::ADMINISTRATOR)) {
			$filters['active'] = TRUE;
		}
		return $filters;
	}
}