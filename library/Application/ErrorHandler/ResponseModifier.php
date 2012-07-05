<?php

namespace Nano\Application\ErrorHandler;

interface ResponseModifier {

	public function update(\Nano_C_Response $response);

}