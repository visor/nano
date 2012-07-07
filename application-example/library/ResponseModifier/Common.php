<?php

namespace App\ResponseModifier;

class Common implements \Nano\Application\ErrorHandler\ResponseModifier {

	public function update(\Nano\Controller\Response $response) {
		$contents = $response->getBody();
		$response->setBody('<h1>Unexpected Error</h1>');
		$response->appendToBody($contents);
	}

}