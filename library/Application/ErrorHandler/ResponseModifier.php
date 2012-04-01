<?php

interface Application_ErrorHandler_ResponseModifier {

	public function update(Nano_C_Response $response);

}