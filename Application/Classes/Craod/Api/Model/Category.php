<?php

namespace Craod\Api\Model;

use Craod\Api\Rest\Annotation as Craod;
use Craod\Api\Utility\Settings;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

use Cpliakas\Password\Password;

/**
 * Class User
 *
 * @package Craod\Api\Model
 * @ORM\Table(name="categories")
 * @ORM\Entity(repositoryClass="Craod\Api\Repository\CategoryRepository")
 * @ORM\HasLifecycleCallbacks
 */
class Category extends SearchableEntity {

	/**
	 * @var string
	 * @ORM\Column(type="string")
	 * @Craod\Api\Readable
	 * @Craod\Api\Writable
	 * @Craod\Api\Searchable({"analyzer": "simple"})
	 */
	protected $name;

	/**
	 * @var User
	 * @ORM\OneToOne(targetEntity="User", fetch="LAZY")
	 * @ORM\JoinColumn(name="author", referencedColumnName="guid")
	 * @Craod\Api\Readable
	 * @Craod\Api\Searchable({"analyzer": "simple"})
	 */
	protected $author;

	/**
	 * @var array
	 * @ORM\Column(type="jsonb")
	 * @Craod\Api\Readable
	 * @Craod\Api\Writable
	 */
	protected $settings = [];

	/**
	 * The time of the last activity in this category
	 *
	 * @var \DateTime
	 * @ORM\Column(type="datetimetz")
	 * @Craod\Api\Readable
	 * @Craod\Api\Searchable
	 */
	protected $lastActivity;

	/**
	 * @var ArrayCollection<Category>
	 * @ORM\ManyToMany(targetEntity="Category")
	 * @ORM\JoinTable(name="categories_relations",
	 *   joinColumns={@ORM\JoinColumn(name="parentcategory", referencedColumnName="guid")},
	 *   inverseJoinColumns={@ORM\JoinColumn(name="childcategory", referencedColumnName="guid")}
	 * )
	 * @Craod\Api\Readable
	 * @Craod\Api\Searchable
	 */
	protected $parents;

	/**
	 * @var ArrayCollection<Category>
	 * @ORM\ManyToMany(targetEntity="Category")
	 * @ORM\JoinTable(name="categories_relations",
	 *   joinColumns={@ORM\JoinColumn(name="childcategory", referencedColumnName="guid")},
	 *   inverseJoinColumns={@ORM\JoinColumn(name="parentcategory", referencedColumnName="guid")}
	 * )
	 * @Craod\Api\Readable
	 * @Craod\Api\Searchable
	 */
	protected $children;

	/**
	 * Initialize parents and children
	 */
	public function __construct() {
		parent::__construct();
		$this->parents = new ArrayCollection();
		$this->children = new ArrayCollection();
	}

	/**
	 * @return string
	 */
	public function getName() {
		return $this->name;
	}

	/**
	 * @param string $name
	 * @return User
	 */
	public function setName($name) {
		$this->name = $name;
		return $this;
	}

	/**
	 * @return User
	 */
	public function getAuthor() {
		return $this->author;
	}

	/**
	 * @param User $author
	 * @return Category
	 */
	public function setAuthor($author) {
		$this->author = $author;
		return $this;
	}

	/**
	 * @return array
	 */
	public function getSettings() {
		return $this->settings;
	}

	/**
	 * @param array $settings
	 * @return User
	 */
	public function setSettings($settings) {
		$this->settings = $settings;
		return $this;
	}

	/**
	 * @return $this
	 */
	public function updateLastActivity() {
		$this->lastActivity = new \DateTime();
		return $this;
	}
	/**
	 * @return \DateTime
	 */
	public function getLastActivity() {
		return $this->lastActivity;
	}
	
	/**
	 * @return ArrayCollection
	 */
	public function getParents() {
		return $this->parents;
	}
	/**
	 * @param ArrayCollection $parents
	 * @return User
	 */
	public function setParents($parents) {
		$this->parents = $parents;
		return $this;
	}
	/**
	 * @param Category $parent
	 * @return boolean
	 */
	public function hasParent(Category $parent) {
		foreach ($this->getParents() as $parentTest) {
			if ($parent->getGuid() === $parentTest->getGuid()) {
				return TRUE;
			}
		}
		return FALSE;
	}
	/**
	 * @param Category $parent
	 * @return $this
	 */
	public function addParent(Category $parent) {
		if (!$this->hasParent($parent)) {
			$this->parents->add($parent);
		}
		return $this;
	}
	/**
	 * @param Category $parent
	 * @return $this
	 */
	public function removeParent(Category $parent) {
		foreach ($this->getParents() as $key => $parentTest) {
			if ($parent->getGuid() === $parentTest->getGuid()) {
				$this->parents->remove($key);
			}
		}
		return $this;
	}

	/**
	 * @return ArrayCollection
	 */
	public function getChildren() {
		return $this->children;
	}
	/**
	 * @param ArrayCollection $children
	 * @return User
	 */
	public function setChildren($children) {
		$this->children = $children;
		return $this;
	}
	/**
	 * @param Category $parent
	 * @return boolean
	 */
	public function hasChild(Category $parent) {
		foreach ($this->getChildren() as $parentTest) {
			if ($parent->getGuid() === $parentTest->getGuid()) {
				return TRUE;
			}
		}
		return FALSE;
	}
	/**
	 * @param Category $parent
	 * @return $this
	 */
	public function addChild(Category $parent) {
		if (!$this->hasChild($parent)) {
			$this->children->add($parent);
		}
		return $this;
	}
	/**
	 * @param Category $parent
	 * @return $this
	 */
	public function removeChild(Category $parent) {
		foreach ($this->getChildren() as $key => $parentTest) {
			if ($parent->getGuid() === $parentTest->getGuid()) {
				$this->children->remove($key);
			}
		}
		return $this;
	}
}