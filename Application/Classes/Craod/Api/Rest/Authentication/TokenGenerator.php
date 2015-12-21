<?php

namespace Craod\Api\Rest\Authentication;

use Craod\Api\Model\User;

/**
 * Class for generating unique tokens
 *
 * @package Craod\Api\Rest\Authentication
 */
class TokenGenerator {

	/**
	 * Generate a unique token to be used when logging in
	 *
	 * @param User $user
	 * @return string
	 */
	public static function generate(User $user) {
		return base64_encode(md5(mt_rand(0, time()) . $user->getGuid()));
	}
}