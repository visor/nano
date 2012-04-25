<?php

class Event_Manager {

	/**
	 * @var ArrayObject
	 */
	protected $callbacks;

	/**
	 * @var Event_Handler[]|SplObjectStorage
	 */
	protected $handlers;

	/**
	 * @var Event_Loader
	 */
	protected $loader = null;

	public function __construct() {
		$this->callbacks = new ArrayObject;
		$this->handlers  = new SplObjectStorage;
	}

	/**
	 * @return Event_Manager
	 * @param string $eventType
	 * @param string|array|Closure $callback
	 * @param int $priority
	 *
	 * @throws Event_Exception
	 */
	public function attach($eventType, $callback, $priority = 100) {
		if ($callback instanceof Closure) {
			$this->addEventCallback($eventType, $callback, (int)$priority);
			return $this;
		}

		if (is_callable($callback, false)) {
			$callbackFunction = function(Event $event) use ($callback) {
				call_user_func($callback, $event);
			};

			$this->addEventCallback($eventType, $callbackFunction, (int)$priority);
			return $this;
		}

		throw new Event_Exception('Passed handler not callable');
	}

	/**
	 * @return Event_Manager
	 * @param Event_Handler $handler
	 */
	public function attachHandler(Event_Handler $handler) {
		if ($this->handlers->contains($handler)) {
			return $this;
		}

		$this->handlers->attach($handler);
		return $this;
	}

	/**
	 * @return Event
	 * @param string|Event $eventOrType
	 * @param array $arguments
	 */
	public function trigger($eventOrType, array $arguments = array()) {
		$this->loadEvents();

		$event = $eventOrType instanceof Event ? $eventOrType : new Event($eventOrType);
		foreach ($arguments as $name => $value) {
			$event->setArgument($name, $value);
		}

		$methodName = 'on' . Nano::stringToName($event->getType());
		foreach ($this->handlers as $instance) {
			if (method_exists($instance, $methodName)) {
				call_user_func(array($instance, $methodName), $event);
			}
		}

		if ($this->callbackExists($eventOrType)) {
			foreach ($this->callbacks->offsetGet($event->getType()) as /** @var Closure $handler */ $handler) {
				$handler($event);
			}
		}
		return $event;
	}

	/**
	 * @return boolean
	 * @param string|Event $eventOrType
	 */
	public function callbackExists($eventOrType) {
		return $this->callbacks->offsetExists($eventOrType instanceof Event ? $eventOrType->getType() : $eventOrType);
	}

	/**
	 * @return Event_Loader
	 */
	public function loader() {
		if (null === $this->loader) {
			$this->loader = new Event_Loader();
		}
		return $this->loader;
	}

	/**
	 * @param string $event
	 * @param Closure $handler
	 * @param int $priority
	 */
	protected function addEventCallback($event, Closure $handler, $priority) {
		if (!$this->callbacks->offsetExists($event)) {
			$this->callbacks->offsetSet($event, new Event_Queue());
		}

		$this->callbacks->offsetGet($event)->insert($handler, $priority);
	}

	protected function loadEvents() {
		if (null === $this->loader) {
			return;
		}
		if ($this->loader->alreadyLoaded()) {
			return;
		}
		$this->loader()->load($this);
	}

}