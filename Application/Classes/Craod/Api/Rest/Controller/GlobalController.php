<?php

namespace Craod\Api\Rest\Controller;

use Craod\Api\Rest\Annotation as Craod;
use Craod\Api\Utility\Settings;

/**
 * Controller class for global actions that do not have their own controller
 *
 * @package Craod\Api\Rest\Controller
 */
class GlobalController extends AbstractController {

	const SETTINGS_PATH = 'Craod.Api.rest.settings.exposed';

	/**
	 * Returns the exposed settings
	 *
	 * @return mixed
	 */
	public function getSettingsAction() {
		return Settings::get(self::SETTINGS_PATH);
	}
}