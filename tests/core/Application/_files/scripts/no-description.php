<?php

namespace App\CliScript;

/**
 * @param required $testParam Required test parameter
 * @param optional $optionalParam Optional test parameter
 */
class NoDescription extends \Nano\Cli\Script {

	/**
	 * @return int
	 * @param string[] $args
	 */
	public function run(array $args) {
		return $this->stop('[script stop message]', 200);
	}

}