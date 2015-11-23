<?php

require_once 'Classes/Craod/Api/Core/Bootstrap.php';

use Craod\Api\Core\Bootstrap;
use Craod\Api\Rest\Application;


Bootstrap::initialize();
Bootstrap::loadDependencyInjector();
Bootstrap::loadConfiguration();
Bootstrap::initializeCache();
Bootstrap::initializeDatabase();
Bootstrap::run(Application::class);