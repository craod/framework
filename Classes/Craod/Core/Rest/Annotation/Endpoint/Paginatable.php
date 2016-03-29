<?php

namespace Craod\Core\Rest\Annotation\Endpoint;

/**
 * Marks the endpoint referred to by this action method as paginatable
 *
 * @Annotation
 * @Target("METHOD")
 */
final class Paginatable extends Descriptor {

	/**
	 * The schema property this descriptor sets
	 *
	 * @var string
	 */
	public $property = 'paginatable';
}
