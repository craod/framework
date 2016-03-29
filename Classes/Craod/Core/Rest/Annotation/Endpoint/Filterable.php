<?php

namespace Craod\Core\Rest\Annotation\Endpoint;

/**
 * Marks the endpoint referred to by this action method as filterable
 *
 * @Annotation
 * @Target("METHOD")
 */
final class Filterable extends Descriptor {

	/**
	 * The schema property this descriptor sets
	 *
	 * @var string
	 */
	public $property = 'filterable';
}
