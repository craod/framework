<?php

namespace Craod\Api\Rest\Controller;

use Craod\Api\Model\Category;
use Craod\Api\Repository\CategoryRepository;
use Craod\Api\Rest\Annotation as Craod;
use Craod\Api\Model\User;
use Craod\Api\Rest\Service\Crud;
use Craod\Api\Rest\Authentication\TokenGenerator;
use Craod\Api\Rest\Exception\InvalidTokenException;
use Craod\Api\Rest\Exception\NotFoundException;
use Craod\Api\Rest\Exception\AuthenticationException;
use Craod\Api\Utility\Mailer;
use Craod\Api\Utility\Password;

/**
 * Controller class for category actions
 *
 * @package Craod\Api\Rest\Controller
 */
class CategoryController extends AbstractController {

	/**
	 * @var string
	 */
	protected $entityClass = Category::class;

	/**
	 * Count all categories based on the filters
	 *
	 * @return integer
	 */
	public function countAction() {
		$crudService = new Crud($this->entityClass);
		return $crudService->count(Crud::FILTER);
	}

	/**
	 * Get all categories based on the filters
	 *
	 * @return array
	 */
	public function getAllAction() {
		$crudService = new Crud($this->entityClass);
		return $crudService->getAll(Crud::FILTER | Crud::PAGINATE | Crud::SORT);
	}

	/**
	 * Retrieve the information for the given category
	 *
	 * @param string $guid
	 * @return Category
	 */
	public function getAction($guid) {
		$crudService = new Crud($this->entityClass);
		return $crudService->get($guid);
	}

	/**
	 * Create a category
	 *
	 * @return Category
	 * @Craod\RequireUser
	 */
	public function createAction() {
		$crudService = new Crud($this->entityClass);
		return $crudService->create($this->requestData);
	}

	/**
	 * Update the given category
	 *
	 * @param string $guid
	 * @return Category
	 * @throws AuthenticationException
	 * @Craod\RequireUser
	 */
	public function updateAction($guid) {
		$crudService = new Crud($this->entityClass);
		/** @var Category $category */
		$category = Category::getRepository()->findOneBy(['guid' => $guid]);
		$currentUser = $this->getApplication()->getCurrentUser();
		if ($category->getUser()->getGuid() !== $currentUser->getGuid() && !$currentUser->hasRole(User::ADMINISTRATOR)) {
			throw new AuthenticationException('Only administrators or owners may edit a category', 1450310629);
		}
		return $crudService->update($this->requestData, $guid);
	}

	/**
	 * Delete the given user
	 *
	 * @param string $guid
	 * @return boolean
	 * @throws AuthenticationException
	 * @Craod\RequireRole("ADMINISTRATOR")
	 */
	public function deleteAction($guid) {
		$crudService = new Crud($this->entityClass);
		return $crudService->delete($guid);
	}

	/**
	 * Search for categories based on the criteria given
	 *
	 * @return Category[]
	 * @Craod\RequireRequestData({"searchTerms"})
	 */
	public function searchAction() {
		$crudService = new Crud($this->entityClass);
		return $crudService->search($this->requestData['searchTerms'], Crud::PAGINATE | Crud::SORT);
	}
}