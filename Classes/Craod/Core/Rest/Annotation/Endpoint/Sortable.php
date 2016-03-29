<?php

namespace Craod\Core\Rest\Annotation\Endpoint;

/**
 * Marks the endpoint referred to by this action method as sortable
 *
 * @Annotation
 * @Target("METHOD")
 */
final class Sortable extends Descriptor {

	/**
	 * The schema property this descriptor sets
	 *
	 * @var string
	 */
	public $property = 'sortable';
}
