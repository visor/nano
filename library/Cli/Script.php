<?php

namespace Nano\Cli;

abstract class Script {

	/**
	 * @var string
	 */
	protected $name = null;

	/**
	 * @var string[]
	 */
	protected $docTags = null;

	/**
	 * @var \Nano\Cli
	 */
	protected $cli;

	/**
	 * @return int
	 * @param string[] $args
	 */
	abstract public function run(array $args);

	/**
	 * @param string $name
	 * @param \Nano\Cli $cli
	 */
	public function __construct($name, \Nano\Cli $cli) {
		$this->name = $name;
		$this->cli  = $cli;
	}

	/**
	 * @return boolean
	 */
	public function needApplication() {
		return true;
	}

	/**
	 * @return \Nano\Application
	 */
	public function getApplication() {
		return $this->cli->getApplication();
	}

	/**
	 * @return string
	 */
	public function usage() {
		$result =
			$this->name . ' - ' . $this->getDescription() . PHP_EOL . PHP_EOL
			. 'Usage' . PHP_EOL . '  ' . \Nano\Cli::getPhpBinary() . ' ' . \Nano\Cli::getCliScriptPath(). ' ' . $this->name
		;
		$params = '';
		foreach ($this->getDocTags() as $tag) {
			if ('param' !== $tag['name']) {
				continue;
			}
			$result .= ' ' . $tag['param'];
			$params .= '   - ' . ($tag['optional']) . '  ' . $tag['param'] . '  ' . $tag['description'] . PHP_EOL;
		}
		return $result . PHP_EOL . (empty($params) ? '' : PHP_EOL . '  Where' . PHP_EOL . $params) . PHP_EOL;
	}

	/**
	 * @return string
	 */
	public function getDescription() {
		foreach ($this->getDocTags() as $tag) {
			if ('description' !== $tag['name']) {
				continue;
			}
			return $tag['value'];
		}
		return '';
	}

	/**
	 * @return string
	 */
	public function getName() {
		return $this->name;
	}

	/**
	 * @return \ReflectionClass
	 */
	protected function getReflector() {
		return $this->cli->getScript($this->name);
	}

	/**
	 * @return string[]
	 */
	protected function getDocTags() {
		if (null === $this->docTags) {
			$this->docTags = DocBlockParser::parse($this->getReflector()->getDocComment());
		}
		return $this->docTags;
	}

	/**
	 * @return int
	 * @param string $message
	 * @param int $code
	 * @param boolean $usage
	 */
	protected function stop($message = null, $code = 0, $usage = true) {
		if (null !== $message) {
			echo $message, PHP_EOL;
		}
		if (true === $usage) {
			echo PHP_EOL, $this->usage();
		}
		return (int)$code;
	}

}