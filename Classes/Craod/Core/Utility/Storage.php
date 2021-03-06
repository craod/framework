<?php

namespace Craod\Core\Utility;

use Craod\Core\Application;
use Craod\Core\Bootstrap;

use Craod\Core\Exception\AuthenticationException;

/**
 * The S3 utility
 *
 * @package Craod\Core\Utility
 */
class Storage implements AbstractUtility {

	const ACCESS_PUBLIC = 'public';
	const ACCESS_PROTECTED = 'protected';

	/**
	 *
	 *
	 * @var array
	 */
	protected static $buckets;

	/**
	 * @var string
	 */
	protected static $endpoint;

	/**
	 * @var array
	 */
	protected static $cloudFrontSettings;

	/**
	 * The S3 class used to communicate with and edit the S3 instance
	 *
	 * @var \S3
	 */
	protected static $s3;

	/**
	 * Parse the configuration files for the current context
	 *
	 * @return void
	 */
	public static function initialize() {
		$settings = Settings::get('Craod.Core.Storage.settings');
		self::$cloudFrontSettings = Settings::get('Craod.Core.Storage.cloudFront');
		self::$buckets = $settings['buckets'];
		self::$endpoint = $settings['endpoint'];
		self::$s3 = new \S3($settings['accessKey'], $settings['secretKey'], TRUE, self::$endpoint);
	}

	/**
	 * Returns TRUE if the S3 manager has been initialized, FALSE otherwise
	 *
	 * @return boolean
	 */
	public static function isInitialized() {
		return self::$s3 !== NULL;
	}

	/**
	 * This utility cannot run if the settings have not been loaded yet
	 *
	 * @return array
	 */
	public static function getRequiredUtilities() {
		return [
			Settings::class
		];
	}

	/**
	 * Gets the public URL for the given file path - that is to say, gets the url that will retrieve the file in the given path assuming
	 * it has been stored publicly
	 *
	 * @param string $filePath
	 * @return string
	 */
	public static function getPublicUrl($filePath) {
		return 'https://' . self::$buckets[self::ACCESS_PUBLIC] . '.' . self::$endpoint . '/' . Bootstrap::getContext() . '/' . $filePath;
	}

	/**
	 * Returns a signed URL that can be used to retrieve the file publicly (does not check for existence, however)
	 *
	 * @param string $filePath
	 * @param integer $timeout
	 * @return string
	 * @throws AuthenticationException
	 */
	public static function getSignedUrl($filePath, $timeout = 300) {
		$keyPairId = self::$cloudFrontSettings['keyPair'];
		$domain = self::$cloudFrontSettings['domain'];
		$resource = 'https://' . $domain . '/' . $filePath;

		$ipAddress = $_SERVER['REMOTE_ADDR'] . '/32';
		$expires = time() + $timeout;
		$statement = [
			'Statement' => [
				[
					'Resource' => $resource,
					'Condition' => [
						'IpAddress' => ['AWS:SourceIp' => $ipAddress],
						'DateLessThan' => ['AWS:EpochTime' => $expires]
					]
				]
			]
		];

		if (Bootstrap::getContext() !== Application::PRODUCTION) {
			unset ($statement['Statement'][0]['Condition']['IpAddress']);
		}

		$key = openssl_get_privatekey(file_get_contents(Bootstrap::getRootPath() . '/Authentication/pk-' . $keyPairId . '.pem'));
		if (!$key) {
			throw new AuthenticationException('Unable to load private key', 1451707912);
		}

		if (!openssl_sign(json_encode($statement, JSON_UNESCAPED_SLASHES), $signedPolicy, $key, OPENSSL_ALGO_SHA1)) {
			throw new AuthenticationException('Failed to sign policy: ' . openssl_error_string(), 1451707913);
		}

		$base64SignedPolicy = base64_encode($signedPolicy);
		$signature = str_replace(['+','=','/'], ['-','_','~'], $base64SignedPolicy);

		return $resource . '?Expires=' . $expires . '&Signature=' . $signature . '&Key-Pair-Id=' . $keyPairId;
	}

	/**
	 * Copy the contents of the file given to the destination URI in the desired bucket
	 *
	 * @param string $filePath
	 * @param string $destinationUri
	 * @param string $bucket
	 * @return boolean
	 * @throws AuthenticationException
	 * @throws \Exception
	 */
	public static function copyFileToBucket($filePath, $destinationUri, $bucket = self::ACCESS_PUBLIC) {
		try {
			$acl = ($bucket === self::ACCESS_PUBLIC ? \S3::ACL_PUBLIC_READ : \S3::ACL_PRIVATE);
			$resource = Bootstrap::getContext() . '/' . $destinationUri;
			$response = self::$s3->putObjectFile($filePath, self::$buckets[$bucket], $resource, $acl);
		} catch (\Exception $exception) {
			if (strpos($exception->getMessage(), 'AccessDenied') !== FALSE) {
				throw new AuthenticationException('Access denied to the S3 service', 1452576492);
			} else {
				throw $exception;
			}
		}
		return $response;
	}

	/**
	 * Delete the file given from the destination URI in the desired bucket
	 *
	 * @param string $destinationUri
	 * @param string $bucket
	 * @return boolean
	 * @throws AuthenticationException
	 * @throws \Exception
	 */
	public static function deleteFileFromBucket($destinationUri, $bucket = self::ACCESS_PUBLIC) {
		try {
			$resource = Bootstrap::getContext() . '/' . $destinationUri;
			$response = self::$s3->deleteObject(self::$buckets[$bucket], $resource);
		} catch (\Exception $exception) {
			if (strpos($exception->getMessage(), 'AccessDenied') !== FALSE) {
				throw new AuthenticationException('Access denied to the S3 service', 1452576493);
			} else {
				throw $exception;
			}
		}
		return $response;
	}

	/**
	 * Checks whether the file exists in the path given in S3 in the given bucket
	 *
	 * @param string $filePath
	 * @param string $bucket
	 * @return boolean
	 */
	public static function fileExists($filePath, $bucket = self::ACCESS_PUBLIC) {
		return (self::$s3->getObjectInfo(self::$buckets[$bucket], Bootstrap::getContext() . '/' . $filePath) !== FALSE);
	}
}