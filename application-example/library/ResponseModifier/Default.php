<?php

class ResponseModifier_Default implements Application_ErrorHandler_ResponseModifier {

	public function update(Nano_C_Response $response) {
		$contents = $response->getBody();
		$response->setBody('<h1>Unexpected Error</h1>');
		$response->appendToBody($contents);
	}

}