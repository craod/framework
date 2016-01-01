<?php

namespace Craod\Api\Rest\Annotation\Api;

use Doctrine\ORM\Mapping\Annotation;

/**
 * The Readable annotation marks a property as readable by the Api when doing json encoding, so that it may automatically be shown by the
 * CRUD service
 *
 * @Annotation
 * @Target({"PROPERTY"})
 */
final class Readable implements Annotation {
}
