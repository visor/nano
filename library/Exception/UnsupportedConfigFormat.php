<?php

namespace Nano\Exception;

class UnsupportedConfigFormat extends \Nano\Exception {

	public function __construct($name) {
		parent::__construct('Unsupported format: ' . $name);
	}

}