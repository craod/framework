<?php

namespace Craod\Core\Http;

/**
 * HTTP status codes
 *
 * @package Craod\Core\Http
 */
class HttpStatusCodes {

	// Continue is a reserved keyword so we need the HTTP_
	const HTTP_CONTINUE = 100;
	const SWITCHING_PROTOCOLS = 101;

	const OK = 200;
	const CREATED = 201;
	const ACCEPTED = 202;
	const NON_AUTHORITATIVE_INFORMATION = 203;
	const NO_CONTENT = 204;
	const RESET_CONTENT = 205;
	const PARTIAL_CONTENT = 206;

	const MULTIPLE_CHOICES = 300;
	const MOVED_PERMANENTLY = 301;
	const FOUND = 302;
	const SEE_OTHER = 303;
	const NOT_MODIFIED = 304;
	const USE_PROXY = 305;
	const UNUSED = 306;
	const TEMPORARY_REDIRECT = 307;

	const BAD_REQUEST = 400;
	const UNAUTHORIZED  = 401;
	const PAYMENT_REQUIRED = 402;
	const FORBIDDEN = 403;
	const NOT_FOUND = 404;
	const METHOD_NOT_ALLOWED = 405;
	const NOT_ACCEPTABLE = 406;
	const PROXY_AUTHENTICATION_REQUIRED = 407;
	const REQUEST_TIMEOUT = 408;
	const CONFLICT = 409;
	const GONE = 410;
	const LENGTH_REQUIRED = 411;
	const PRECONDITION_FAILED = 412;
	const REQUEST_ENTITY_TOO_LARGE = 413;
	const REQUEST_URI_TOO_LONG = 414;
	const UNSUPPORTED_MEDIA_TYPE = 415;
	const REQUESTED_RANGE_NOT_SATISFIABLE = 416;
	const EXPECTATION_FAILED = 417;

	const INTERNAL_SERVER_ERROR = 500;
	const NOT_IMPLEMENTED = 501;
	const BAD_GATEWAY = 502;
	const SERVICE_UNAVAILABLE = 503;
	const GATEWAY_TIMEOUT = 504;
	const VERSION_NOT_SUPPORTED = 505;

	/**
	 * For a given code, let us know if it represents an error
	 *
	 * @param integer $code
	 * @return boolean
	 */
	public static function isError($code) {
		return is_numeric($code) && $code >= self::BAD_REQUEST;
	}
}