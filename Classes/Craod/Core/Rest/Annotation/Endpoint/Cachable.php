<?php

namespace Craod\Core\Rest\Annotation\Endpoint;

/**
 * Marks the endpoint referred to by this action method as cachable
 *
 * @Annotation
 * @Target("METHOD")
 */
final class Cachable extends Descriptor {

	/**
	 * The schema property this descriptor sets
	 *
	 * @var string
	 */
	public $property = 'cachable';
}
