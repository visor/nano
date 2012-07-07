<?php

namespace Nano\Application\ErrorHandler;

interface ResponseModifier {

	public function update(\Nano\Controller\Response $response);

}