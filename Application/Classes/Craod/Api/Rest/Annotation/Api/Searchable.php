<?php

namespace Craod\Api\Rest\Annotation\Api;

use Doctrine\ORM\Mapping\Annotation;

/**
 * The Searchable annotation points out properties that can be indexed and explains to ElasticSearch how to do so
 *
 * @Annotation
 * @Target({"PROPERTY"})
 */
final class Searchable implements Annotation {

	/**
	 * The properties to pass to the elastic search indexer, as an associative array
	 *
	 * @var array
	 */
	public $value;

}
