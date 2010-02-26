<?php

class Nano_Routes implements IteratorAggregate {

	/**
	 * @var ArrayObject
	 */
	protected $routes;

	public function __construct() {
		$this->routes = new ArrayObject();
	}

	public function add($pattern, $controller = 'index', $action = 'index') {
		$this->addRoute(Nano_Route::create($pattern, $controller, $action));
		return $this;
	}

	public function addRoute(Nano_Route $route) {
		$this->routes->offsetSet($route->pattern(), $route);
		return $this;
	}

	public function getIterator() {
		return $this->routes->getIterator();
	}
}