<?php

namespace Craod\Api\Core;

interface ApplicationInterface {

	const DEVELOPMENT = 'development';
	const STAGING = 'staging';
	const PRODUCTION = 'production';

	/**
	 * Initialize the application
	 *
	 * @return void
	 */
	public function initialize ();

	/**
	 * Run the application
	 *
	 * @return void
	 */
	public function run();
}