<?php

namespace Craod\Core\Object;

use Craod\Core\Exception\ProtectedAccessException;

/**
 * Utility to manipulate objects through their getter and setter functions
 *
 * @package Craod\Api\Object
 */
class ObjectAccessor {

	/**
	 * Find the property getter method name to retrieve the requested property from the object
	 *
	 * @param mixed $object
	 * @param string $propertyName
	 * @return string
	 * @throws ProtectedAccessException
	 */
	public static function getPropertyGetterFunctionName($object, $propertyName) {
		$getterMethodName = 'get' . ucfirst($propertyName);
		if (!method_exists($object, $getterMethodName)) {
			$getterMethodName = 'is' . ucfirst($propertyName);
		}
		if (!method_exists($object, $getterMethodName)) {
			throw new ProtectedAccessException('Property ' . $propertyName . ' of class ' . get_class($object) . 'has no public getter function', 1449698015);
		}
		return $getterMethodName;
	}

	/**
	 * Call the getter method to retrieve the requested property from the object
	 *
	 * @param mixed $object The object to retrieve properties from
	 * @param string $propertyName The property to retrieve
	 * @return mixed The returned value
	 * @throws ProtectedAccessException
	 */
	public static function getProperty($object, $propertyName) {
		$getterMethodName = self::getPropertyGetterFunctionName($object, $propertyName);
		return call_user_func([$object, $getterMethodName]);
	}

	/**
	 * Find the property setter method name to assign a value to the requested property from the object
	 *
	 * @param mixed $object
	 * @param string $propertyName
	 * @return string
	 * @throws ProtectedAccessException
	 */
	public static function getPropertySetterFunctionName($object, $propertyName) {
		$setterMethodName = 'set' . ucfirst($propertyName);
		if (!method_exists($object, $setterMethodName)) {
			throw new ProtectedAccessException('Property ' . $propertyName . ' of class ' . get_class($object) . ' has no public setter function', 1449698016);
		}
		return $setterMethodName;
	}

	/**
	 * Call the setter method to assign a value to the requested property from the object
	 *
	 * @param mixed $object The object to set the valeus for
	 * @param string $propertyName The property to change
	 * @param ... The parameters to pass to the setter
	 * @return mixed The returned value
	 * @throws ProtectedAccessException
	 */
	public static function setProperty($object, $propertyName) {
		$setterMethodName = self::getPropertySetterFunctionName($object, $propertyName);
		return call_user_func_array([$object, $setterMethodName], array_slice(func_get_args(), 2));
	}
}