<?php

namespace Craod\Api\Utility;

/**
 * Utility to generate passwords
 *
 * @package Craod\Api\Utility
 */
class Password {

	const SYMBOLS = '$&%!?@#*_-+';

	/**
	 * Generate a random, strong password. The generated password will fulfill these requirements:
	 * - At least 15 characters long
	 * - At least one uppercase character
	 * - At least one lowercase character
	 * - At least one number
	 * - At least one symbol of $&%!?@#*_-+
	 *
	 * @return string
	 */
	public static function generate() {
		$desiredLength = rand(11, 13);
		$symbolPool = str_split(self::SYMBOLS);
		$letterPool = str_split('abcdefghijklmnopqrstuvwxyz');
		$numberPool = range(0, 9);
		$password = [
			$symbolPool[array_rand($symbolPool)],
			$numberPool[array_rand($numberPool)],
			$letterPool[array_rand($letterPool)],
			strtoupper($letterPool[array_rand($letterPool)])
		];

		for ($index = 0; $index < $desiredLength; $index++) {
			if (rand(0, 10) < 3) {
				$password[] = $symbolPool[array_rand($symbolPool)];
			} else if (rand(0, 10) < 4) {
				$password[] = $numberPool[array_rand($numberPool)];
			} else {
				$digit = $letterPool[array_rand($letterPool)];
				if (rand(0, 10) >= 5) {
					$digit = strtoupper($digit);
				}
				$password[] = $digit;
			}
			if (rand(0, 10) < 4) {
				shuffle($password);
			}
		}
		shuffle($password);
		return implode('', $password);
	}
}