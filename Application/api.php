<?php

require_once 'Classes/Craod/Api/Core/Bootstrap.php';

use Craod\Api\Core\Bootstrap;
use Craod\Api\Rest\Application;

Bootstrap::initialize([
	Bootstrap::CACHE,
	Bootstrap::CONFIGURATION,
	Bootstrap::DATABASE,
	Bootstrap::ANNOTATIONS,
	Bootstrap::SEARCH,
	Bootstrap::STORAGE
]);

Bootstrap::run(Application::class);