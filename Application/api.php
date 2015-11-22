<?php

require_once 'Classes/Craod/Api/Core/Bootstrap.php';

use Craod\Api\Core\Bootstrap;
use Craod\Api\Core\Api;

Bootstrap::initialize();
Bootstrap::run(Api::class);