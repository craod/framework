<?php

namespace Craod\Api\Utility;

/**
 * An interface that defines what a utility should have
 *
 * @package Craod\Api\Utility
 */
interface AbstractUtility {

	/**
	 * Initialize this utility
	 *
	 * @return void
	 */
	public static function initialize();

	/**
	 * Returns TRUE if the utility has been initialized, FALSE otherwise
	 *
	 * @return boolean
	 */
	public static function isInitialized();

	/**
	 * Get the class names of utilities required by this utility
	 *
	 * @return array
	 */
	public static function getRequiredUtilities();
}