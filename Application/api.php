<?php

require_once 'Classes/Craod/Api/Core/Bootstrap.php';

use Craod\Api\Core\Bootstrap;
use Craod\Api\Rest\Application;

Bootstrap::initialize([
	Bootstrap::DEPENDENCY_INJECTOR,
	Bootstrap::CACHE,
	Bootstrap::CONFIGURATION,
	Bootstrap::DATABASE,
	Bootstrap::ANNOTATIONS
]);

Bootstrap::run(Application::class);