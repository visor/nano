<?php

class Nano_Dispatcher {

	const SUFFIX_CONTROLLER = 'Controller';
	const SUFFIX_ACTION     = 'Action';

	const ERROR_NOT_FOUND   = 404;
	const ERROR_INTERNAL    = 500;

	const CONTEXT           = 'context';

	/**
	 * @var Nano_Dispatcher_Custom
	 */
	protected $custom             = null;

	/**
	 * @var Nano_Dispatcher_Context
	 */
	protected $context            = null;

	/**
	 * @var string
	 */
	protected $module             = null;

	/**
	 * @var string
	 */
	protected $controller         = null;

	/**
	 * @var Nano_C
	 */
	protected $controllerInstance = null;

	/**
	 * @var string
	 */
	protected $action             = null;

	/**
	 * @var string[string]
	 */
	protected $params             = array();

	/**
	 * @var boolean
	 */
	protected $throw              = false;

	/**
	 * @var Nano_C_Response
	 */
	protected $response           = null;

	/**
	 * @return string
	 * @param string $name
	 * @param boolean $controller
	 * @param string|null $module
	 */
	public static function formatName($name, $controller = true, $module = null) {
		$result = Nano::stringToName($name);
		if ($controller) {
			$result .= self::SUFFIX_CONTROLLER;
			if (null !== $module) {
				$result = $module . '\\' . $result;
			}
		} else {
			$result  = strToLower($result[0]) . subStr($result, 1);
			$result .= self::SUFFIX_ACTION;
		}

		return $result;
	}

	/**
	 * @return Nano_Dispatcher
	 * @param Nano_Dispatcher_Custom $value
	 */
	public function setCustom(Nano_Dispatcher_Custom $value) {
		$this->custom = $value;
		return $this;
	}

	/**
	 * @return Nano_Dispatcher
	 * @param Nano_Dispatcher_Context $value
	 */
	public function setContext(Nano_Dispatcher_Context $value) {
		$this->context = $value;
		return $this;
	}

	/**
	 * @return Nano_Dispatcher
	 * @param boolean $value
	 */
	public function throwExceptions($value) {
		$this->throw = $value;
		return $this;
	}

	/**
	 * @return boolean|null
	 * @param Nano_Routes $routes
	 * @param string $url
	 *
	 * @throws Nano_Exception_NotFound
	 */
	public function dispatch(Nano_Routes $routes, $url) {
		if ($this->context) {
			$this->context->detect();
			if ($this->context->needRedirect()) {
				$this->context->redirect($url);
				return null;
			}
		}
		$route = $this->getRoute($routes, $url);
		if (null !== $route) {
			$this->run($route);
			return null;
		}
		if ($this->custom) {
			$result = $this->custom->dispatch();
			if (false === $result) {
				throw new Nano_Exception_NotFound('Custom dispatcher fails for: ' . $url, $route);
			}
			return $result;
		}
		Nano::app()->errorHandler()->notFound('Route not found for: ' . $url);
		return null;
	}

	/**
	 * @return string
	 * @param Nano_Route $route
	 */
	public function run(Nano_Route $route) {
		if (isset($_SERVER['REQUEST_METHOD']) && 'HEAD' === strToUpper($_SERVER['REQUEST_METHOD']) && Nano::isTesting()) {
			return null;
		}
		if ($route instanceof Nano_Route_Runnable) {
			/* @var $route Nano_Route_Runnable */
			$route->run();
			return null;
		}

		$this->controllerInstance = $this->getController($route);
		$this->controllerInstance->setResponse($this->getResponse());

		if ($this->param('context')) {
			$this->controllerInstance->context = $this->param('context');
		} elseif ($this->context) {
			$this->controllerInstance->context = $this->context->get();
		}

		$this->controllerInstance->run($this->action());
	}

	/**
	 * @return Nano_C
	 * @param Nano_Route $route
	 *
	 * @throws Nano_Exception_NotFound
	 * @throws Nano_Exception_InternalError
	 */
	public function getController(Nano_Route $route) {
		$this->setUpController($route);
		$className = $route->controllerClass();
		if (!class_exists($className)) {
			throw new Nano_Exception_NotFound('Controller class not found: '. $className, $route);
		}
		$class = new ReflectionClass($className);
		if (false === $class->isInstantiable() || false === $class->isSubclassOf('Nano_C')) {
			throw new Nano_Exception_InternalError('Not a controller class: ' . $className);
		}
		return $class->newInstance();
	}

	/**
	 * @return Nano_Route|null
	 * @param Nano_Routes $routes
	 * @param string $url
	 */
	public function getRoute(Nano_Routes $routes, $url) {
		$method  = isSet($_SERVER['REQUEST_METHOD']) ? strToLower($_SERVER['REQUEST_METHOD']) : 'get';
		$testUrl = trim($url, '/');
		foreach ($routes->getRoutes($method)->getArrayCopy() as $route) { /** @var $route Nano_Route */
			if ($this->test($route, $testUrl)) {
				return $route;
			}
		}
		return null;
	}

	/**
	 * @return boolean
	 * @param Nano_Route $route
	 * @param string $url
	 */
	public function test(Nano_Route $route, $url) {
		if (false === $route->match($url)) {
			return false;
		}
		$this->buildParams($route->params());
		return true;
	}

	/**
	 * @return Nano_C
	 */
	public function controllerInstance() {
		return $this->controllerInstance;
	}

	public function module() {
		return $this->module;
	}

	/**
	 * @return string
	 */
	public function controller() {
		return $this->controller;
	}

	/**
	 * @return string
	 */
	public function action() {
		return $this->action;
	}

	/**
	 * @return array
	 */
	public function params() {
		return $this->params;
	}

	/**
	 * @return void
	 * @param array $value
	 */
	public function setParams(array $value) {
		$this->buildParams($value);
	}

	/**
	 * @return string
	 * @param string $name
	 * @param mixed $default
	 */
	public function param($name, $default = null) {
		if (array_key_exists($name, $this->params)) {
			return $this->params[$name];
		}
		return $default;
	}

	/**
	 * @return Nano_C_Response
	 */
	public function getResponse() {
		if (null === $this->response) {
			$this->setResponse(new Nano_C_Response);
		}
		return $this->response;
	}

	/**
	 * @return Nano_Dispatcher
	 * @param Nano_C_Response $value
	 */
	public function setResponse(Nano_C_Response $value) {
		$this->response = $value;
		return $this;
	}

	/**
	 * @return void
	 * @param array $data
	 */
	protected function buildParams(array $data) {
		$this->params = array();
		foreach ($data as $name => $value) {
			if ('module' === $name) {
				$this->module = $value;
				continue;
			}
			if ('controller' === $name) {
				$this->controller = $value;
				continue;
			}
			if ('action' === $name) {
				$this->action = $value;
				continue;
			}
			$this->params[$name] = $value;
		}
	}

	/**
	 * @return void
	 * @param Nano_Route $route
	 */
	protected function setUpController(Nano_Route $route) {
		$this->action     = $route->action();
		$this->controller = $route->controller();
		$this->module     = $route->module();
	}

	/**
	 * @return void
	 * @param Exception $error
	 * @throws Exception
	 */
	protected function handleError(Exception $error) {
		//todo: log message
		$errorController = isSet(Nano::app()->config->get('web')->errorController) ? Nano::app()->config->get('web')->errorController : null;
		if ($this->throw || null === $errorController) {
			$this->getResponse()->addHeader('Content-Type', 'text/plain');
			$this->getResponse()->setBody($error);
			if ($error instanceof Nano_Exception_NotFound) {
				$this->getResponse()->setStatus(Nano_C_Response::STATUS_NOT_FOUND);
			} else {
				$this->getResponse()->setStatus(Nano_C_Response::STATUS_ERROR);
			}
			$this->getResponse()->send();
			return;
		}

		$controllerName = Nano::app()->config->get('web')->errorController;
		$className      = self::formatName($controllerName, true);
		$controller     = new $className($this->application); /* @var $controller Nano_C */
		$action         = 'custom';

		if ($error instanceof Nano_Exception_NotFound) {
			$action = 'e404';
		}
		if ($error instanceof Nano_Exception_InternalError) {
			$action = 'e500';
		}

		$this->controller         = $controllerName;
		$this->controllerInstance = $controller;
		$this->action             = $action;
		$controller->error        = $error;

		$controller->setResponse($this->getResponse());
		$controller->run($action);
	}

}