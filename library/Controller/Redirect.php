<?php

namespace Nano\Controller;

class Redirect {

	/**
	 * @var \Nano\Controller\Response
	 */
	protected $response;

	/**
	 * @param \Nano\Controller\Response $response
	 */
	function __construct(\Nano\Controller\Response $response) {
		$this->response = $response;
	}

	/**
	 * @return \Nano\Controller\Redirect
	 * @param string $location
	 * @param int $status
	 */
	public function to($location, $status = 302) {
		$this->response->addHeader('Location', $location);
		$this->response->setStatus($status);

		return $this;
	}

	/**
	 * @return \Nano\Controller\Redirect
	 */
	public function home() {
		return $this->to('/');
	}

	/**
	 * @return \Nano\Controller\Redirect
	 */
	public function back() {
		if (isSet($_SERVER['HTTP_REFERER']) && isSet($_SERVER['HTTP_HOST']) && strStr($_SERVER['HTTP_REFERER'], $_SERVER['HTTP_HOST'])) {
			return $this->to($_SERVER['HTTP_REFERER']);
		}
		return $this->home();
	}

	/**
	 * @return \Nano\Controller\Redirect
	 */
	public function permanent() {
		$this->response->setStatus(301);
		return $this;
	}

}