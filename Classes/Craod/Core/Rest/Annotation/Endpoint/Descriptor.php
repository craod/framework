<?php

namespace Craod\Core\Rest\Annotation\Endpoint;

use Doctrine\ORM\Mapping\Annotation;

/**
 * Annotation that describes an endpoint property, tells the application that the action method being called is identified by whatever extends this class
 */
abstract class Descriptor implements Annotation {

	/**
	 * The schema property this descriptor sets
	 *
	 * @var string
	 */
	public $property;
}
