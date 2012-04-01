<?php

class PublicErrorHandler extends Application_ErrorHandler {

	public function __construct(Application $application, $noHandlers = false) {
		if (false === $noHandlers) {
			parent::__construct($application);
		} else {
			$this->application = $application;
		}
	}

	public function updateResponse(Nano_C_Response $response) {
		parent::updateResponse($response);
	}

	public function getOutput() {
		return parent::getOutput();
	}

}