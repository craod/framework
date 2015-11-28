<?php

namespace Craod\Api\Utility;

use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\Common\Annotations\CachedReader;

/**
 * The annotation utility
 *
 * @package Craod\Api\Utility
 */
class Annotations implements AbstractUtility {

	/**
	 * The annotation reader
	 *
	 * @var CachedReader
	 */
	protected static $reader;

	/**
	 * Initialize the Predis cache client
	 *
	 * @return void
	 */
	public static function initialize() {
		self::$reader = new CachedReader(
			new AnnotationReader(),
			Cache::getProvider(),
			Settings::get('Craod.Api.annotations.settings.debug')
		);
		if (DependencyInjector::isInitialized()) {
			DependencyInjector::set('annotationReader', self::$reader);
		}
	}

	/**
	 * Returns TRUE if the reader has been initialized, FALSE otherwise
	 *
	 * @return boolean
	 */
	public static function isInitialized() {
		return self::$reader !== NULL;
	}

	/**
	 * Get the utilities required by this utility
	 *
	 * @return array
	 */
	public static function getRequiredUtilities() {
		return [Settings::class];
	}

	/**
	 * Get the client for direct manipulation
	 *
	 * @return CachedReader
	 */
	public static function getReader() {
		return self::$reader;
	}
}