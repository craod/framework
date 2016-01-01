<?php

namespace Craod\Api\Utility;

/**
 * The S3 utility
 *
 * @package Craod\Api\Utility
 */
class Storage implements AbstractUtility {

	/**
	 * @var string
	 */
	protected static $bucketName;

	/**
	 * @var string
	 */
	protected static $endpoint;

	/**
	 * The S3 class used to communicate with and edit the S3 instance
	 *
	 * @var \S3
	 */
	protected static $s3;

	/**
	 * Parse the configuration files for the current context
	 *
	 * @return void
	 */
	public static function initialize() {
		$settings = Settings::get('Craod.Api.storage.settings');
		self::$bucketName = $settings['bucket'];
		self::$endpoint = $settings['endpoint'];
		self::$s3 = new \S3($settings['accessKey'], $settings['secretKey'], TRUE, self::$endpoint);
	}

	/**
	 * Returns TRUE if the S3 manager has been initialized, FALSE otherwise
	 *
	 * @return boolean
	 */
	public static function isInitialized() {
		return self::$s3 !== NULL;
	}

	/**
	 * This utility cannot run if the settings have not been loaded yet
	 *
	 * @return array
	 */
	public static function getRequiredUtilities() {
		return [
			Settings::class
		];
	}

	/**
	 * Returns a URL that can be used to retrieve the file publicly (does not check for existence, however)
	 *
	 * @param string $filePath
	 * @return string
	 */
	public static function getPublicUrl($filePath) {
		return 'https://' . self::$bucketName . '.' . self::$endpoint . '/' . $filePath;
	}

	/**
	 * Checks whether the file exists in the path given in S3
	 *
	 * @param string $filePath
	 * @return boolean
	 */
	public static function fileExists($filePath) {
		return (self::$s3->getObjectInfo(self::$bucketName, $filePath) !== FALSE);
	}
}