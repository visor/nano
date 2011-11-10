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
			return $_SESSION[self::PARAM_MESSAGE];
		}
		return null;
	}

	/**
	 * @return Nano_C_Redirect
	 * @param string $location
	 */
	public function to($location) {
		$this->response->addHeader('Location', $location);
		$this->response->setStatus(302);

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
	 * @param boolean|array $id
	 */
	public function withMessage($message, $id = false) {
		$text = $message;
		if (true === $id) {
			$text = Nano::message()->m($message);
		} elseif (is_array($id)) {
			$text = Nano::message()->fa($message, $id);
		}
		$_SESSION[self::PARAM_MESSAGE] = $text;
		return $this;
	}

}