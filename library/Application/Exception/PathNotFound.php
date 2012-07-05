<?php

namespace Nano\Application\Exception;

class PathNotFound extends \Nano\Application\Exception {

	public function __construct($path) {
		parent::__construct('Path not found: ' . $path);
	}

}