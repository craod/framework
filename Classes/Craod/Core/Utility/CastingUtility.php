<?php

namespace Craod\Core\Utility;

/**
 * The casting utility
 *
 * @package Craod\Core\Utility
 */
class CastingUtility {

	const DATE = 'date';
	const GUID = 'guid';
	const JSONB = 'jsonb';

	/**
	 * Cast the given string raw value to the necessary column type
	 *
	 * @param string $rawValue
	 * @param string $type
	 * @return mixed
	 */
	public static function castTo($rawValue, $type) {
		switch ($type) {
			case self::DATE:
				$value = new \DateTime($rawValue);
				break;

/*			case self::GUID:
				$value = new thetypeofobjectobject($rawValue);
				break;*/

			default:
				$value = $rawValue;
				break;
		}

		return $value;
	}
}