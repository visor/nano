<?php

abstract class Nano_Cli_Script {

	/**
	 * @var Nano_Cli
	 */
	protected $cli;

	/**
	 * @var Application
	 */
	protected $application;

	public function __construct(Nano_Cli $cli) {
		$this->cli = $cli;
	}

/**
	 * @return boolean
	 */
	public function needApplication() {
		return true;
	}

	/**
	 * @return void
	 * @param Application $application
	 */
	public function setApplication(Application $application) {
		$this->application = $application;
	}

	/**
	 * @return Application
	 */
	public function getApplication() {
		return $this->application;
	}

	/**
	 * @param string[] $args
	 * @return void
	 */
	abstract public function run(array $args);

}