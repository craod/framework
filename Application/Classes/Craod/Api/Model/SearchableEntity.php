<?php

namespace Craod\Api\Model;

use Craod\Api\Rest\Annotation\Api\Searchable;
use Craod\Api\Repository\SearchableRepository;
use Craod\Api\Utility\Annotations;
use Craod\Api\Utility\Cache;
use Craod\Api\Utility\Database;
use Craod\Api\Utility\Search;

use Doctrine\ORM\Mapping as ORM;

/**
 * A SearchableEntity has a parallel type in elasticsearch which is composed of the properties configured using the Searchable annotation
 *
 * @package Craod\Api\Model
 * @method static SearchableRepository getRepository()
 * @ORM\HasLifecycleCallbacks
 */
abstract class SearchableEntity extends AbstractEntity {

	/**
	 * Index this class on creation or edition
	 *
	 * @return void
	 * @ORM\PostPersist
	 * @ORM\PostUpdate
	 */
	public function index() {
		Search::index($this);
	}

	/**
	 * Remove this class from the index when it is deleted
	 *
	 * @return void
	 * @ORM\PreRemove
	 */
	public function removeFromIndex() {
		Search::delete($this);
	}

	/**
	 * Get a list of property names that have the searchable annotation
	 *
	 * @return array
	 */
	public static function getSearchableProperties() {
		return Annotations::getPropertiesByAnnotation(get_called_class(), Searchable::class);
	}
}