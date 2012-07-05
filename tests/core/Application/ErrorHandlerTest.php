<?php

/**
 * @group core
 * @group error-handler
 */
class Core_Application_ErrorHandlerTest extends TestUtils_HttpTest {

	protected function setUp() {
		parent::setUp();
		if ($this->needHttpAuth()) {
			$this->request->setOptions(array(
				'httpauthtype' => HTTP_AUTH_BASIC
				, 'httpauth'   => $this->application->config->get('web')->username . ':' . $this->application->config->get('web')->password
			));
		}
	}

	public function testNormalOutputShouldDisplayWhenNoErrors() {
		$this->request->setUrl($this->getUrl('/error/no-errors'));
		$this->request->setMethod(HttpRequest::METH_GET);
		$this->request->send();

		self::assertEquals('This contents should be displayed!', $this->request->getResponseBody());
		self::assertEquals(Nano_C_Response::STATUS_DEFAULT, $this->request->getResponseCode());
	}

	public function testShouldHandleFatalErrorsInActions() {
		$this->request->setUrl($this->getUrl('/error/action-fatal'));
		$this->request->setMethod(HttpRequest::METH_GET);
		$this->request->send();

		self::assertContains('Error: Call to undefined function generateFatalError()', $this->request->getResponseBody());
		self::assertEquals(Nano_C_Response::STATUS_ERROR, $this->request->getResponseCode());
	}

	public function testShouldHandleGeneratedOutput() {
		$this->request->setUrl($this->getUrl('/error/view-fatal'));
		$this->request->setMethod(HttpRequest::METH_GET);
		$this->request->send();

		self::assertEquals(Nano_C_Response::STATUS_ERROR, $this->request->getResponseCode());
		self::assertContains('Error: Call to undefined function generateFatalError()', $this->request->getResponseBody());
		self::assertContains('Generated output: <pre>This contents should handled in &quot;generated output&quot; section&lt;br /&gt;', $this->request->getResponseBody());
	}

	public function testShouldHandleCompileErrors() {
		$this->request->setUrl($this->getUrl('/error/compile'));
		$this->request->setMethod(HttpRequest::METH_GET);
		$this->request->send();

		self::assertEquals(Nano_C_Response::STATUS_ERROR, $this->request->getResponseCode());
		self::assertContains('Parse Error: syntax error, unexpected $end', $this->request->getResponseBody());
	}

	public function testShouldHandleWarnings() {
		$this->request->setUrl($this->getUrl('/error/warning'));
		$this->request->setMethod(HttpRequest::METH_GET);
		$this->request->send();

		self::assertEquals(Nano_C_Response::STATUS_ERROR, $this->request->getResponseCode());
		self::assertContains('Warning: Division by zero', $this->request->getResponseBody());
	}

	public function testShouldHandleNotice() {
		$this->request->setUrl($this->getUrl('/error/notice'));
		$this->request->setMethod(HttpRequest::METH_GET);
		$this->request->send();

		self::assertEquals(Nano_C_Response::STATUS_ERROR, $this->request->getResponseCode());
		self::assertContains('Notice: Undefined variable: notDefined', $this->request->getResponseBody());
	}

	public function testShouldHanldeExceptions() {
		$this->request->setUrl($this->getUrl('/error/exception'));
		$this->request->setMethod(HttpRequest::METH_GET);
		$this->request->send();

		self::assertEquals(Nano_C_Response::STATUS_ERROR, $this->request->getResponseCode());
		self::assertContains('Exception: "RuntimeException" with message "Exception message"', $this->request->getResponseBody());
	}

	public function testShouldSend404CodeForNotFound() {
		$this->request->setUrl($this->getUrl('/error/404'));
		$this->request->setMethod(HttpRequest::METH_GET);
		$this->request->send();

		self::assertEquals(Nano_C_Response::STATUS_NOT_FOUND, $this->request->getResponseCode());
		self::assertContains('Message from action', $this->request->getResponseBody());
	}

	public function testShouldSend404WhenRouteNotFound() {
		$this->request->setUrl($this->getUrl('/this-page-not-routed'));
		$this->request->setMethod(HttpRequest::METH_GET);
		$this->request->send();

		self::assertEquals(Nano_C_Response::STATUS_NOT_FOUND, $this->request->getResponseCode());
		self::assertContains('Route not found for: this-page-not-routed', $this->request->getResponseBody());
	}

	public function testShouldSend500CodeForInternalError() {
		$this->request->setUrl($this->getUrl('/error/500'));
		$this->request->setMethod(HttpRequest::METH_GET);
		$this->request->send();

		self::assertEquals(Nano_C_Response::STATUS_ERROR, $this->request->getResponseCode());
		self::assertContains('Message from action', $this->request->getResponseBody());
	}

	public function testShouldReturnDefaultErrorLevelForUnknownLevel() {
		$this->request->setUrl($this->getUrl('/error/custom'));
		$this->request->setMethod(HttpRequest::METH_GET);
		$this->request->send();

		self::assertEquals(Nano_C_Response::STATUS_ERROR, $this->request->getResponseCode());
		self::assertContains('Error: Message from action', $this->request->getResponseBody());
	}

	public function testShouldCallResponseModifierWhenConfigured() {
		$this->testShouldHandleFatalErrorsInActions();

		self::assertContains('<h1>Unexpected Error</h1>', $this->request->getResponseBody());
		self::assertEquals('true', $this->request->getResponseHeader('X-Modified'));
	}

	public function testShouldSendResponseDirectlyWhenNoControllerDispatched() {
		$this->request->setUrl($this->getUrl('/error/no-class'));
		$this->request->setMethod(HttpRequest::METH_GET);
		$this->request->send();

		self::assertEquals(Nano_C_Response::STATUS_ERROR, $this->request->getResponseCode());
		self::assertContains(
			'Exception: "Nano\Exception\NotFound" with message "Controller class not found: App\Controller\NoClass (route: no-class::index() when location matches [/no-class])"'
			, $this->request->getResponseBody()
		);
	}

	public function testGeneratedOutputShouldBeNullWhenBufferingStoppedBeforeError() {
		$this->request->setUrl($this->getUrl('/error/null-output'));
		$this->request->setMethod(HttpRequest::METH_GET);
		$this->request->send();

		self::assertEquals(Nano_C_Response::STATUS_ERROR, $this->request->getResponseCode());
		self::assertNotContains('Generated output: ', $this->request->getResponseBody());
	}

	/**
	 * @return boolean
	 */
	protected function needHttpAuth() {
		if (!isSet($this->application->config->get('web')->auth)) {
			return false;
		}
		return true === $this->application->config->get('web')->auth;
	}

}