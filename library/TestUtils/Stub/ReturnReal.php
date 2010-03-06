<?php

class TestUtils_Stub_ReturnReal implements PHPUnit_Framework_MockObject_Stub {

	public function __construct() {
	}

	public function toString() {
		return 'call original method';
	}

	public function invoke(PHPUnit_Framework_MockObject_Invocation $invocation) {
		return call_user_func_array(array($invocation->object, $invocation->methodName), $invocation->parameters);
	}

}