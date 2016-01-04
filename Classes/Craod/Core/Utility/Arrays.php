<?php

namespace Craod\Core\Utility;

/**
 * Utility to manipulate arrays
 *
 * @package Craod\Core\Utility
 */
class Arrays {

	/**
	 * Merge settings from the second array into the first array. If sequential arrays overlap, add them together and reindex
	 *
	 * @param array $firstArray First array
	 * @param array $secondArray Second array, overruling the first array
	 * @return array Resulting array where $secondArray values has overruled $firstArray values
	 */
	public static function merge(array $firstArray, array $secondArray) {
		if (!self::isAssociative($secondArray)) {
			foreach ($secondArray as $value) {
				$firstArray[] = $value;
			}
		} else {
			foreach ($secondArray as $name => $value) {
				if (isset($firstArray[$name]) && is_array($firstArray[$name]) && is_array($value)) {
					$firstArray[$name] = self::merge($firstArray[$name], $value);
				} else {
					$firstArray[$name] = $value;
				}
			}
		}
		return $firstArray;
	}

	/**
	 * Checks whether the object is an associative or sequential array
	 *
	 * @param $object
	 * @return boolean
	 */
	public static function isAssociative($object) {
		return (array_keys($object) !== range(0, count($object) - 1));
	}
}