<?php

class JsonpHelper extends Nano_Helper {

	/**
	 * @return JsonpHelper
	 */
	public function invoke() {
		return $this;
	}

	/**
	 * @return string
	 * @param mixed $data
	 */
	public function result($data) {
		$result =
			'<html><head><script type="text/javascript">window.name=\''
			. json_encode($data)
			. '\';</script></head><body></body></html>'
		;
		return $result;
	}

}