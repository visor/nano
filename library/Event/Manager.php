<?php

class Event_Manager {

	/**
	 * @var ArrayObject
	 */
	protected $handlers;

	/**
	 * @var Event_Loader
	 */
	protected $loader = null;

	public function __construct() {
		$this->handlers = new ArrayObject();
	}

	/**
	 * @return Event_Manager
	 * @param string $eventType
	 * @param string|array|Closure $handler
	 * @param int $priority
	 *
	 * @throws Event_Exception
	 */
	public function attach($eventType, $handler, $priority = 100) {
		if ($handler instanceof Closure) {
			$this->addEventHandler($eventType, $handler, (int)$priority);
			return $this;
		}

		if (is_callable($handler, false)) {
			$handlerFunction = function(Event $event) use ($handler) {
				call_user_func($handler, $event);
			};

			$this->addEventHandler($eventType, $handlerFunction, (int)$priority);
			return $this;
		}

		throw new Event_Exception('Passed handler not callable');
	}

	/**
	 * @return Event
	 * @param string|Event $eventOrType
	 * @param array $arguments
	 */
	public function trigger($eventOrType, array $arguments = array()) {
		$this->loadEvents();

		$event = $eventOrType instanceof Event ? $eventOrType : new Event($eventOrType);

		if (!$this->handlerExists($eventOrType)) {
			return $event;
		}

		foreach ($arguments as $name => $value) {
			$event->setArgument($name, $value);
		}

		foreach ($this->handlers->offsetGet($event->getType()) as /** @var Closure $handler */ $handler) {
			$handler($event);
		}
		return $event;
	}

	/**
	 * @return boolean
	 * @param string|Event $eventOrType
	 */
	public function handlerExists($eventOrType) {
		return $this->handlers->offsetExists($eventOrType instanceof Event ? $eventOrType->getType() : $eventOrType);
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
	protected function addEventHandler($event, Closure $handler, $priority) {
		if (!$this->handlers->offsetExists($event)) {
			$this->handlers->offsetSet($event, new Event_Queue());
		}

		$this->handlers->offsetGet($event)->insert($handler, $priority);
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