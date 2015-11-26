<?php

namespace Craod\Api\Utility;

use DI\ContainerBuilder;
use DI\Container;

/**
 * The dependency injector
 *
 * @package Craod\Api\Utility
 */
class DependencyInjector implements AbstractUtility {

	/**
	 * The actual DI container
	 *
	 * @var Container
	 */
	protected static $container;

	/**
	 * Parse the configuration files for the current context
	 *
	 * @return void
	 */
	public static function initialize() {
		$builder = new ContainerBuilder();
		self::$container = $builder->build();
	}

	/**
	 * Checks whether we have built a container yet, which happens at initialization
	 *
	 * @return boolean
	 */
	public static function isInitialized() {
		return self::$container !== NULL;
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
	 * Get a value from the dependency injector
	 *
	 * @param string $name
	 * @return mixed
	 * @throws \DI\NotFoundException
	 */
	public static function get($name) {
		return self::$container->get($name);
	}

	/**
	 * Set a value on the dependency injector
	 *
	 * @param string $name
	 * @param mixed $value
	 * @return void
	 */
	public static function set($name, $value) {
		self::$container->set($name, $value);
	}

	/**
	 * Checks whether the dependency injector has a dependency set
	 *
	 * @param string $name
	 * @return boolean
	 */
	public static function has($name) {
		return self::$container->has($name);
	}

	/**
	 * Returns the actual DI container in case extra functions need to be used
	 *
	 * @return Container
	 */
	public static function getContainer() {
		return self::$container;
	}
}