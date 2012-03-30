<?php

class Nano_C_Redirect {

	const PARAM_MESSAGE = 'flashMessage';

	/**
	 * @var Nano_C_Response
	 */
	protected $response;

	/**
	 * @param Nano_C_Response $response
	 */
	function __construct(Nano_C_Response $response) {
		$this->response = $response;
	}

	/**
	 * @return string|null
	 */
	public static function getMessage() {
		if (isSet($_SESSION[self::PARAM_MESSAGE])) {
			$result = $_SESSION[self::PARAM_MESSAGE];
			unSet($_SESSION[self::PARAM_MESSAGE]);
			return $result;
		}
		return null;
	}

	/**
	 * @return Nano_C_Redirect
	 * @param string $location
	 * @param int $status
	 */
	public function to($location, $status = 302) {
		$this->response->addHeader('Location', $location);
		$this->response->setStatus($status);

		return $this;
	}

	/**
	 * @return Nano_C_Redirect
	 */
	public function home() {
		return $this->to('/');
	}

	/**
	 * @return Nano_C_Redirect
	 */
	public function back() {
		if (isSet($_SERVER['HTTP_REFERER']) && isSet($_SERVER['HTTP_HOST']) && strStr($_SERVER['HTTP_REFERER'], $_SERVER['HTTP_HOST'])) {
			return $this->to($_SERVER['HTTP_REFERER']);
		}
		return $this->home();
	}

	/**
	 * @return Nano_C_Redirect
	 */
	public function permanent() {
		$this->response->setStatus(301);
		return $this;
	}

	/**
	 * @return Nano_C_Redirect
	 * @param string $message
	 */
	public function withMessage($message) {
		$_SESSION[self::PARAM_MESSAGE] = $message;
		return $this;
	}

}