<?php

namespace Craod\Api\Rest\Annotation\Api;

use Doctrine\ORM\Mapping\Annotation;

/**
 * The Writable annotation marks a property as writable by the Api, so that it may automatically be modified by the CRUD controller
 *
 * @Annotation
 * @Target({"PROPERTY"})
 */
final class Writable implements Annotation {
}
