<?php

namespace Craod\Api\Utility;
use Predis\Client;

/**
 * The cache utility
 *
 * @package Craod\Api\Utility
 */
class Cache implements AbstractUtility {

	/**
	 * The cache client
	 *
	 * @var Client
	 */
	protected static $client;

	/**
	 * Initialize the Predis cache client
	 *
	 * @return void
	 */
	public static function initialize() {
		self::$client = new Client([
			'scheme' => 'tcp',
			'host' => '127.0.0.1',
			'port' => 6379
		]);
		if (DependencyInjector::isInitialized()) {
			DependencyInjector::set('cache', self::$client);
		}
	}

	/**
	 * Returns TRUE if the client has been initialized, FALSE otherwise
	 *
	 * @return boolean
	 */
	public static function isInitialized() {
		return self::$client !== NULL;
	}

	/**
	 * Get the utilities required by this utility
	 *
	 * @return array
	 */
	public static function getRequiredUtilities() {
		return [];
	}

	/**
	 * Get the client for direct manipulation
	 *
	 * @return Client
	 */
	public static function getClient() {
		return self::$client;
	}

	/**
	 * Retrieve a key from the client
	 *
	 * @param string $key
	 * @return string
	 */
	public static function get($key) {
		return self::$client->get($key);
	}

	/**
	 * Set a key in the client
	 *
	 * @param string $key
	 * @param string $value
	 * @return mixed
	 */
	public static function set($key, $value) {
		return self::$client->set($key, $value);
	}

	/**
	 * Return TRUE if the client has the key, FALSE otherwise
	 *
	 * @param string $key
	 * @return boolean
	 */
	public static function has($key) {
		return self::$client->exists($key);
	}

	/**
	 * Clear one or all keys
	 *
	 * @param string $key
	 * @return boolean
	 */
	public static function clear($key = NULL) {
		if ($key === NULL) {
			return self::$client->flushall();
		} else {
			return self::$client->set($key, NULL, 0);
		}
	}
}