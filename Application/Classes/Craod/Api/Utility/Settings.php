<?php

namespace Craod\Api\Utility;

use Craod\Api\Core\Bootstrap;

use Craod\Api\Exception\InvalidSettingsBundleException;
use Symfony\Component\Yaml\Yaml;

/**
 * The settings utility
 *
 * @package Craod\Api\Utility
 */
class Settings {

	const SETTINGS_ROOT_PATH = Bootstrap::ROOT_PATH . 'Configuration/';
	const DEFAULT_BUNDLE = 'Settings';

	/**
	 * The actual settings
	 *
	 * @var array
	 */
	protected static $settings;

	/**
	 * Parse the configuration files for the current context
	 *
	 * @return void
	 */
	public static function initialize() {
		self::$settings = [];
		self::loadBundle(self::DEFAULT_BUNDLE);
	}

	/**
	 * Checks whether this utility has been initialized
	 *
	 * @return boolean
	 */
	public static function isInitialized() {
		return is_array(self::$settings);
	}

	/**
	 * This utility has no dependencies
	 *
	 * @return array
	 */
	public static function getRequiredUtilities() {
		return [];
	}

	/**
	 * Load the given bundle, search the configuration folder for the given bundle files
	 *
	 * @param string $bundle
	 * @return void
	 */
	public static function loadBundle($bundle) {
		self::parseForBundle($bundle);
	}

	/**
	 * Parse the given folder for configuration files for the given bundle
	 *
	 * @param string $bundle
	 * @return void
	 */
	public static function parseForBundle($bundle) {
		$pattern = realpath(self::SETTINGS_ROOT_PATH) . '/{,' . ucfirst(Bootstrap::getContext()) . '/}' . $bundle . '{,.*}.yaml';
		foreach (glob($pattern, GLOB_BRACE) as $filename) {
			self::loadFileIntoBundle($filename, $bundle);
		}
	}

	/**
	 * Load the given bundle, search the configuration folder for the given bundle files
	 *
	 * @param string $filename
	 * @param string $bundle
	 * @return void
	 */
	public static function loadFileIntoBundle($filename, $bundle) {
		if (!isset(self::$settings[$bundle])) {
			self::$settings[$bundle] = [];
		}
		self::$settings[$bundle] = Arrays::merge(self::$settings[$bundle], self::loadFile($filename));
	}

	/**
	 * Load the given file and parse it into yaml
	 *
	 * @param string $filename
	 * @return array
	 */
	public static function loadFile($filename) {
		return Yaml::parse(File::getContents($filename));
	}

	/**
	 * Get the loaded data for the given bundle
	 *
	 * @param string $bundle
	 * @return array
	 * @throws InvalidSettingsBundleException
	 */
	public static function getLoadedData($bundle) {
		if (!isset(self::$settings[$bundle])) {
			throw new InvalidSettingsBundleException('Settings bundle is not set: ' . $bundle, 1448160231);
		}
		return self::$settings[$bundle];
	}

	/**
	 * Checks whether the setting exists for the given bundle
	 *
	 * @param string $setting
	 * @param string $bundle
	 * @return boolean
	 */
	public static function settingExists($setting, $bundle = self::DEFAULT_BUNDLE) {
		$settingParts = explode('.', $bundle . '.' . $setting);
		$data = self::$settings;
		foreach ($settingParts as $part) {
			if (!isset($data[$part])) {
				return FALSE;
			} else {
				$data = $data[$part];
			}
		}

		return TRUE;
	}

	/**
	 * Get the setting at the given path for the given bundle, if it does not exist return the default value
	 *
	 * @param string $setting
	 * @param mixed $default
	 * @param string $bundle
	 * @return mixed
	 */
	public static function get($setting, $default = NULL, $bundle = self::DEFAULT_BUNDLE) {
		$settingParts = explode('.', $bundle . '.' . $setting);
		$data = self::$settings;
		foreach ($settingParts as $part) {
			if (!isset($data[$part])) {
				return $default;
			} else {
				$data = $data[$part];
			}
		}

		return $data;
	}
}