<?php

require_once 'Application/Classes/Craod/Api/Core/Bootstrap.php';

use Craod\Api\Core\Bootstrap;
use Craod\Api\Utility\Settings;

Bootstrap::initialize([
	Bootstrap::CONFIGURATION
]);

return Settings::get('Craod.Api.database.settings');