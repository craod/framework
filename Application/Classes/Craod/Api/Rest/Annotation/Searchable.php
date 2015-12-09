<?php

namespace Craod\Api\Rest\Annotation;

use Doctrine\ORM\Mapping\Annotation;

/**
 * The Searchable annotation ensures the domain model is allowed to be indexed and points out properties that can be indexed as well
 *
 * @Annotation
 * @Target({"CLASS", "PROPERTY"})
 */
class Searchable implements Annotation {
}
