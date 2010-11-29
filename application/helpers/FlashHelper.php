<?php

class FlashHelper extends Nano_Helper {

	/**
	 * @return void
	 * @param string $message
	 * @param boolean $error
	 * @param string $location
	 */
	public function invoke($message = '', $error = false, $location = null) {
		if ($message) {
			$_SESSION['flash']       = $message;
			$_SESSION['flash_error'] = $error ? 1 : 0;
		}
		if (null !== $location && $this->dispatcher()) {
			$this->dispatcher()->controllerInstance()->markRendered();
			header('Location: ' . $location);
			exit();
		}
	}

}