<?php

/**
 * todo: move into separate module
 */
class Assets {

	/**
	 * @var Assets_Styles
	 */
	private static $style = null;

	/**
	 * @var Assets_Scripts
	 */
	private static $script = null;

	/**
	 * @return Assets_Styles
	 */
	public static function style() {
		if (null === self::$style) {
			self::$style = new Assets_Styles();
			self::$style->setOutput(Nano::config('assets')->path);
		}
		return self::$style;
	}

	/**
	 * @return Assets_Scripts
	 */
	public static function script() {
		if (null === self::$script) {
			self::$script = new Assets_Scripts();
			self::$script->setOutput(Nano::config('assets')->path);
		}
		return self::$script;
	}

	/**
	 * Removes all generated assets
	 *
	 * @return void
	 * @param array $ignore
	 * @param boolean $verbose
	 */
	public static function clearCache(array $ignore = array(), $verbose = false) {
		self::style()->clearCache($ignore, $verbose);
		self::script()->clearCache($ignore, $verbose);
	}

}