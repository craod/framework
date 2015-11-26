<?php

namespace Craod\Api\Utility;

/**
 * The file utility
 *
 * @package Craod\Api\Utility
 */
class File {

	/**
	 * Get the contents of a file. If the cache has been initialized and the file has not been touched since the cache date, return the cached data instead
	 *
	 * @return string
	 */
	public static function getContents($filename) {
		if (Cache::isInitialized()) {
			$lastModification = filemtime($filename);
			if (!Cache::has($filename . '-modification') || !Cache::has($filename) || Cache::get($filename . '-modification') != $lastModification) {
				Cache::set($filename, file_get_contents($filename));
				Cache::set($filename . '-modification', $lastModification);
			}
			return Cache::get($filename);
		} else {
			return file_get_contents($filename);
		}
	}
}