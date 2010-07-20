<?php

class Strings {

	/**
	 * @return string
	 * @param string $text
	 */
	public static function toLower($text) {
		return String::create($text)->toLower()->__toString();
	}

	/**
	 * @return string
	 * @param string $text
	 */
	public static function ucFirst($text) {
		return String::create($text)->ucFirst()->__toString();
	}

	/**
	 * @return string
	 * @param string $text
	 * @param int $width
	 * @param string $break
	 */
	public static function wrap($text, $width = 80, $break = PHP_EOL) {
		return String::create($text)->wrap($width, $break)->__toString();
	}

}