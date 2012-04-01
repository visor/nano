<?php

/**
 * @group core
 * @group error-handler
 */
class Application_ErrorHandlerInternalsTest extends TestUtils_TestCase {

	protected function setUp() {
		require_once __DIR__ . '/_files/PublicErrorHandler.php';
		require_once __DIR__ . '/_files/AbstractResponseModifier.php';
		$this->reloadConfig();
	}

	public function testShouldNotCallResponseModifierWhenNoErrorsSection() {
		$application = $GLOBALS['application'];
		$application->config->name();

		$config = self::getObjectProperty($application->config, 'config');
		unSet($config->errors);
		self::setObjectProperty($application->config, 'config', $config);

		$response = new Nano_C_Response_Test($application);
		$handler  = new PublicErrorHandler($application, true);
		$handler->updateResponse($response);
		self::assertFalse($response->hasHeader('X-Modified'));
	}

	public function testShouldNotCallResponseModifierWhenNoResponseSetting() {
		$application = $GLOBALS['application'];
		$application->config->name();

		$config = self::getObjectProperty($application->config, 'config');
		unSet($config->errors->response);
		self::setObjectProperty($application->config, 'config', $config);

		$response = new Nano_C_Response_Test($application);
		$handler  = new PublicErrorHandler($application, true);
		$handler->updateResponse($response);
		self::assertFalse($response->hasHeader('X-Modified'));
	}

	public function testShouldNotCallResponseModifierWhenClassNotExists() {
		$application = $GLOBALS['application'];
		$application->config->name();

		$config = self::getObjectProperty($application->config, 'config');
		$config->errors->response = 'ClassNotExists';
		self::setObjectProperty($application->config, 'config', $config);

		$response = new Nano_C_Response_Test($application);
		$handler  = new PublicErrorHandler($application, true);
		$handler->updateResponse($response);
		self::assertFalse($response->hasHeader('X-Modified'));
	}

	public function testShouldNotCallResponseModifierClassNotImplementsInterface() {
		$application = $GLOBALS['application'];
		$application->config->name();

		$config = self::getObjectProperty($application->config, 'config');
		$config->errors->response = 'stdClass';
		self::setObjectProperty($application->config, 'config', $config);

		$response = new Nano_C_Response_Test($application);
		$handler  = new PublicErrorHandler($application, true);
		$handler->updateResponse($response);
		self::assertFalse($response->hasHeader('X-Modified'));
	}

	public function testShouldNotCallResponseModifierClassIsAbstract() {
		$application = $GLOBALS['application'];
		$application->config->name();

		$config = self::getObjectProperty($application->config, 'config');
		$config->errors->response = 'AbstractResponseModifier';
		self::setObjectProperty($application->config, 'config', $config);

		$response = new Nano_C_Response_Test($application);
		$handler  = new PublicErrorHandler($application, true);
		$handler->updateResponse($response);
		self::assertFalse($response->hasHeader('X-Modified'));
	}

//	public function testGetOutputShouldReturnNullWhenNoOutputBuffering() {
//		$handler = new PublicErrorHandler($GLOBALS['application'], true);
//		self::assertNull($handler->getOutput());
//	}

	protected function reloadConfig() {
		self::setObjectProperty($GLOBALS['application']->config, 'config', null);
		self::setObjectProperty($GLOBALS['application']->config, 'routes', null);
		$GLOBALS['application']->config->name();
	}

	protected function tearDown() {
		$this->reloadConfig();
	}

}