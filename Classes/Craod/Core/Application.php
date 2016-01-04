<?php

namespace Craod\Core;

interface Application {

	const DEVELOPMENT = 'development';
	const STAGING = 'staging';
	const PRODUCTION = 'production';

	/**
	 * Initialize the application
	 *
	 * @return void
	 */
	public function initialize();

	/**
	 * Run the application
	 *
	 * @return void
	 */
	public function run();
}