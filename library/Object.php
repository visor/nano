<?php

class Object {

	/**
	 * @return stdClass
	 * @param string[] $properties
	 */
	public static function make(array $properties) {
		$result = new stdClass;
		foreach ($properties as $name) {
			$result->$name = null;
		}
		return $result;
	}

	/**
	 * @return stdClass
	 * @param array[string] $data
	 */
	public static function makeUsing(array $data) {
		$result = new stdClass;
		foreach ($data as $name => $value) {
			$result->$name = $value;
		}
		return $result;
	}

	/**
	 * @return stdClass
	 * @param mixed[string] $properties
	 * @param string[] $properties
	 */
	public static function makeFrom(array $data, array $properties) {
		$result = new stdClass;
		foreach ($properties as $name) {
			$result->$name = isset($data[$name]) ? $data[$name] : null;
		}
		return $result;
	}

}