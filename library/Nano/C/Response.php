<?php

class Nano_C_Response {

	const STATUS_DEFAULT   = 200;
	const STATUS_NOT_FOUND = 404;
	const STATUS_ERROR     = 500;

	const VERSION_10       = '1.0';
	const VERSION_11       = '1.1';

	/**
	 * @var string[]
	 * @link http://www.w3.org/Protocols/rfc2616/rfc2616-sec6.html
	 */
	protected static $messages = array(
		//1xx: Informational - Request received, continuing process
		100   => 'Continue'
		, 101 => 'Switching Protocols'

		//2xx: Success - The action was successfully received, understood, and accepted
		, 200 => 'OK'
		, 201 => 'Created'
		, 202 => 'Accepted'
		, 203 => 'Non-Authoritative Information'
		, 204 => 'No Content'
		, 205 => 'Reset Content'
		, 206 => 'Partial Content'

		//3xx: Redirection - Further action must be taken in order to complete the request
		, 300 => 'Multiple Choices'
		, 301 => 'Moved Permanently'
		, 302 => 'Found'
		, 303 => 'See Other'
		, 304 => 'Not Modified'
		, 305 => 'Use Proxy'
		, 307 => 'Temporary Redirect'

		//4xx: Client Error - The request contains bad syntax or cannot be fulfilled
		, 400 => 'Bad Request'
		, 401 => 'Unauthorized'
		, 402 => 'Payment Required'
		, 403 => 'Forbidden'
		, 404 => 'Not Found'
		, 405 => 'Method Not Allowed'
		, 406 => 'Not Acceptable'
		, 407 => 'Proxy Authentication Required'
		, 408 => 'Request Timeout'
		, 409 => 'Conflict'
		, 410 => 'Gone'
		, 411 => 'Length Required'
		, 412 => 'Precondition Failed'
		, 413 => 'Request Entity Too Large'
		, 414 => 'Request-URI Too Long'
		, 415 => 'Unsupported Media Type'
		, 416 => 'Requested Range Not Satisfiable'
		, 417 => 'Expectation Failed'

		//5xx: Server Error - The server failed to fulfill an apparently valid request
		, 500 => 'Internal Server Error'
		, 501 => 'Not Implemented'
		, 502 => 'Bad Gateway'
		, 503 => 'Service Unavailable'
		, 504 => 'Gateway Timeout'
		, 505 => 'HTTP Version Not Supported'
		, 509 => 'Bandwidth Limit Exceeded'
	);

	/**
	 * HTTP version 1.0 or 1.1
	 *
	 * @var string
	 */
	protected $version;

	/**
	 * @var int
	 */
	protected $status;

	/**
	 * @var ArrayObject
	 */
	protected $headers;

	/**
	 * @var string
	 */
	protected $body;

	function __construct() {
		$this->status  = self::STATUS_DEFAULT;
		$this->version = self::VERSION_10;
		$this->headers = new ArrayObject();
		$this->body    = null;
	}

	/**
	 * @return Nano_C_Response
	 * @param string $value
	 */
	public function setVersion($value) {
		$this->version = $value;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getVersion() {
		return $this->version;
	}

	/**
	 * @return Nano_C_Response
	 * @param int $value
	 */
	public function setStatus($value) {
		$this->status = $value;
		return $this;
	}

	/**
	 * @return int
	 */
	public function getStatus() {
		return $this->status;
	}

	/**
	 * @return Nano_C_Response
	 * @param array $headers
	 */
	public function addHeaders(array $headers) {
		foreach ($headers as $name => $value) {
			$this->addHeader($name, $value);
		}
		return $this;
	}

	/**
	 * @return Nano_C_Response
	 * @param string $name
	 * @param string $value
	 */
	public function addHeader($name, $value) {
		$this->headers->offsetSet($name, $value);
		return $this;
	}

	/**
	 * @return boolean
	 * @param string $name
	 */
	public function hasHeader($name) {
		return $this->headers->offsetExists($name);
	}

	/**
	 * @return string
	 * @param string $name
	 */
	public function getHeader($name) {
		return $this->hasHeader($name) ? $this->headers->offsetGet($name) : null;
	}

	/**
	 * @return void
	 */
	public function sendHeaders() {
		$status =
			$this->status
			. (isSet(self::$messages[$this->status]) ? ' ' . self::$messages[$this->status]: '')
		;
		header('HTTP/' . $this->version . ' ' . $status, true, $this->status);
		header('Status: ' . $status, true);

		foreach ($this->headers as $name => $value) {
			header($name . ': ' . $value, true);
		}
	}

	/**
	 * @return Nano_C_Response
	 * @param string $value
	 */
	public function setBody($value) {
		$this->body = $value;
		return $this;
	}

	/**
	 * @return Nano_C_Response
	 * @param string $value
	 */
	public function appendToBody($value) {
		$this->body .= $value;
		return $this;
	}

	/**
	 * @return boolean
	 */
	public function hasBody() {
		if (null === $this->body) {
			return false;
		}
		return true;
	}

	/**
	 * @return string
	 */
	public function getBody() {
		return $this->body;
	}

	/**
	 * @return void
	 */
	public function sendBody() {
		echo $this->body;
	}

	/**
	 * @return void
	 */
	public function send() {
		$this->sendHeaders();
		$this->sendBody();
	}

}