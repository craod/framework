<?php

namespace Craod\Core\Utility;

use Doctrine\Common\Cache\PredisCache;

use Predis\Client;

/**
 * The cache utility
 *
 * @package Craod\Core\Utility
 */
class Cache implements AbstractUtility {

	/**
	 * The cache client
	 *
	 * @var Client
	 */
	protected static $client;

	/**
	 * The doctrine cache provider
	 *
	 * @var PredisCache
	 */
	protected static $provider;

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
		self::$provider = new PredisCache(self::$client);
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
	 * Retrieve a value from the client
	 *
	 * @param string $key
	 * @return string
	 */
	public static function get($key, $defaultValue = NULL) {
		if (self::$client->exists($key)) {
			return self::$client->get($key);
		} else {
			return $defaultValue;
		}
	}

	/**
	 * Retrieve a value from the client and json_decode it
	 *
	 * @param string $key
	 * @return mixed
	 */
	public static function getAsObject($key, $defaultValue = NULL) {
		if (self::$client->exists($key)) {
			return json_decode(self::$client->get($key), JSON_NUMERIC_CHECK);
		} else {
			return $defaultValue;
		}
	}

	/**
	 * Set a value in the client
	 *
	 * @param string $key
	 * @param string $value
	 * @return mixed
	 */
	public static function set($key, $value) {
		return self::$client->set($key, $value);
	}

	/**
	 * Set a json encoded value in the client
	 *
	 * @param string $key
	 * @param mixed $value
	 * @return mixed
	 */
	public static function setAsObject($key, $value) {
		return self::set($key, json_encode($value, JSON_NUMERIC_CHECK));
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

	/**
	 * @return PredisCache
	 */
	public static function getProvider() {
		return self::$provider;
	}
}