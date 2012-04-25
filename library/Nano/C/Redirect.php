<?php

class Nano_C_Redirect {

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

}