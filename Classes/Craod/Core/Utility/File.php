<?php

namespace Craod\Core\Utility;

/**
 * The file utility
 *
 * @package Craod\Core\Utility
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
			$cacheKey = 'File:' . $filename . '-modification';
			if (!Cache::has($cacheKey) || !Cache::has($filename) || Cache::get($cacheKey) != $lastModification) {
				Cache::set($filename, file_get_contents($filename));
				Cache::set($cacheKey, $lastModification);
			}
			return Cache::get($filename);
		} else {
			return file_get_contents($filename);
		}
	}
}