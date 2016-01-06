<?php

namespace Craod\Core\Rest\Annotation;

use Doctrine\ORM\Mapping\Annotation;

/**
 * The main validate annotation, tells the application that the action method being called must be validated with the given validator
 * class
 */
abstract class Validate implements Annotation {

	/**
	 * The validator class to run
	 *
	 * @var string
	 */
	public $validator;

}
