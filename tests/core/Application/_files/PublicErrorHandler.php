<?php

class PublicErrorHandler extends \Nano\Application\ErrorHandler {

	public function __construct(\Nano\Application $application, $noHandlers = false) {
		if (false === $noHandlers) {
			parent::__construct($application);
		} else {
			$this->application = $application;
		}
	}

	public function updateResponse(\Nano\Controller\Response $response) {
		parent::updateResponse($response);
	}

	public function getOutput() {
		return parent::getOutput();
	}

}