<?php

/**
 * @group core
 * @group error-handler
 */
class Application_ErrorHandlerInternalsTest extends TestUtils_TestCase {

	protected function setUp() {
		$this->app->backup();
		Nano::setApplication($GLOBALS['application']);

		require_once __DIR__ . '/_files/PublicErrorHandler.php';
		require_once __DIR__ . '/_files/AbstractResponseModifier.php';
		$this->reloadConfig();
	}

	public function testShouldNotCallResponseModifierWhenNoErrorsSection() {
		$application = Nano::app();
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
		$application = Nano::app();
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
		$application = Nano::app();
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
		$application = Nano::app();
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
		$application = Nano::app();
		$application->config->name();

		$config = self::getObjectProperty($application->config, 'config');
		$config->errors->response = 'AbstractResponseModifier';
		self::setObjectProperty($application->config, 'config', $config);

		$response = new Nano_C_Response_Test($application);
		$handler  = new PublicErrorHandler($application, true);
		$handler->updateResponse($response);
		self::assertFalse($response->hasHeader('X-Modified'));
	}

	protected function reloadConfig() {
		self::setObjectProperty(Nano::app()->config, 'config', null);
		self::setObjectProperty(Nano::app()->config, 'routes', null);
		Nano::app()->config->name();
	}

	protected function tearDown() {
		$this->reloadConfig();
		$this->app->restore();
	}

}