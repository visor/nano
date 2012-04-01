<?php

class Application_ErrorHandler {

	protected $application;

	/**
	 * @var boolean
	 */
	protected $errorHandled = false;

	/**
	 * @var array
	 */
	private static $levels = array(
		E_ERROR               => 'Error'
		, E_WARNING           => 'Warning'
		, E_PARSE             => 'Parse Error'
		, E_NOTICE            => 'Notice'
		, E_USER_ERROR        => 'User Error'
		, E_USER_WARNING      => 'User Warning'
		, E_USER_NOTICE       => 'User Notice'
		, E_USER_DEPRECATED   => 'Deprecated'
		, E_STRICT            => 'String'
		, E_RECOVERABLE_ERROR => 'Recoverable Error'
	);

	/**
	 * @var int
	 */
	private static $defaultLevel = E_ERROR;

	public function __construct(Application $application) {
		ob_start();
		register_shutdown_function(array($this, 'shutdownFunction'));
		set_error_handler(array($this, 'handleError'));
		set_exception_handler(array($this, 'handleException'));
		$this->application = $application;
	}

	public function notFound($message) {
		$response = $this->createDefaultResponse(Nano_C_Response::STATUS_NOT_FOUND);
		$response->appendToBody($message);
		$this->updateResponse($response);
		return $this->send($response);
	}

	public function internalError($message) {
		$response = $this->createDefaultResponse();
		$response->appendToBody($message);
		$this->updateResponse($response);
		return $this->send($response);
	}

	public function shutdownFunction() {
		$lastError = error_get_last();
		if (null === $lastError || true === $this->errorHandled) {
			return;
		}

		$response = $this->generateErrorResponse($lastError, $this->getOutput());
		$this->updateResponse($response);
		$this->send($response);
	}

	public function handleError($level, $message, $file, $line, array $context) {
		$error = array(
			'type'      => $level
			, 'message' => $message
			, 'file'    => $file
			, 'line'    => $line
			, 'context' => $context
		);
		$this->errorHandled = true;
		$response = $this->generateErrorResponse($error, $this->getOutput());
		$this->updateResponse($response);
		return $this->send($response);
	}

	public function handleException(Exception $exception) {
		$this->errorHandled = true;
		$response = $this->generateExceptionResponse($exception, $this->getOutput());
		$this->updateResponse($response);
		return $this->send($response);
	}

	/**
	 * @return Nano_C_Response
	 * @param int $status
	 */
	public function createDefaultResponse($status = Nano_C_Response::STATUS_ERROR) {
		$result = new Nano_C_Response($this->application);
		$result->setStatus($status);
		return $result;
	}

	/**
	 * @return Nano_C_Response
	 * @param Nano_C_Response $response
	 */
	protected function updateResponse(Nano_C_Response $response) {
		if (!$this->application->config->exists('errors')) {
			return $response;
		}
		$errors = $this->application->config->get('errors');
		if (!isSet($errors->response)) {
			return $response;
		}

		$responseClass  = $errors->response;
		if (!class_exists($responseClass)) {
			return $response;
		}
		$class = new ReflectionClass($responseClass);
		if (!$class->implementsInterface('Application_ErrorHandler_ResponseModifier')) {
			return $response;
		}
		if (!$class->isInstantiable()) {
			return $response;
		}

		$customResponse = $class->newInstance();
		/** @var Application_ErrorHandler_ResponseModifier $customResponse */
		$response->addHeader('X-Modified', 'true');
		$customResponse->update($response);

		return $response;
	}

	/**
	 * @return Nano_C_Response
	 * @param array $error
	 * @param string|null $buffer
	 */
	protected function generateErrorResponse(array $error, $buffer = null) {
		$result = $this->createDefaultResponse();
		$result->appendToBody($this->errorToString($error) . PHP_EOL);
		$this->appendOutput($result, $buffer);
		return $result;
	}

	/**
	 * @return Nano_C_Response
	 * @param Exception $exception
	 * @param string|null $buffer
	 */
	protected function generateExceptionResponse(Exception $exception, $buffer = null) {
		$result = $this->createDefaultResponse();
		$result->appendToBody($this->exceptionToString($exception));
		$this->appendOutput($result, $buffer);
		return $result;
	}

	/**
	 * @return null|string
	 */
	protected function getOutput() {
		if (ob_get_level() > 0) {
			return ob_get_clean();
		}
		return null;
	}

	protected function appendOutput(Nano_C_Response $response, $buffer) {
		if (0 != strLen($buffer)) {
			$response->appendToBody('<hr />' . PHP_EOL);
			$response->appendToBody('Generated output: <pre>' . htmlSpecialChars($buffer) . '</pre>');
		}
	}

	/**
	 * @return null
	 * @param Nano_C_Response $response
	 * @param boolean $forceExit
	 */
	protected function send(Nano_C_Response $response, $forceExit = false) {
		if (ob_get_level() > 0) {
			ob_end_clean();
		}
		if ($this->application->dispatcher->controllerInstance() && false === $forceExit) {
			$this->application->dispatcher->controllerInstance()->markRendered();
			$this->application->dispatcher->controllerInstance()->setResponse($response);
		}
		$response->send();
	}

	protected function errorToString(array $error) {
		$newLine = PHP_EOL . '<br />';
		return
			$this->getErrorLevelString($error['type']) . ': ' . $error['message']
			. $newLine . 'File: ' . $error['file']
			. $newLine . 'Line: ' . $error['line']
		;
	}

	/**
	 * @return string
	 * @param Exception $exception
	 */
	protected function exceptionToString(Exception $exception) {
		$newLine = PHP_EOL . '<br />';
		return
			'Exception: "' . get_class($exception) . '" with message "' . $exception->getMessage() . '"'
			. $newLine . 'File: ' . $exception->getFile()
			. $newLine . 'Line: ' . $exception->getLine()
			. $newLine . 'Stack trace: <pre>' . $exception->getTraceAsString() . '</pre>'
		;
	}

	/**
	 * @return string
	 * @param int $level
	 */
	protected function getErrorLevelString($level) {
		if (isSet(self::$levels[$level])) {
			return self::$levels[$level];
		}
		return self::$levels[self::$defaultLevel];
	}

}