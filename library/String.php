<?php

class String {

	const DEFAULT_ENCODING = 'UTF-8';

	/**
	 * @var string
	 */
	protected $value = null;

	/**
	 * @var string
	 */
	protected $encoding;

	public function __construct($value, $encoding = self::DEFAULT_ENCODING) {
		$this->value    = $value;
		$this->encoding = $encoding;
	}

	/**
	 * @return String
	 * @param string $value
	 * @param string $encoding
	 */
	public static function create($value, $encoding = self::DEFAULT_ENCODING) {
		return new self($value, $encoding);
	}

	/**
	 * @return int
	 */
	public function length() {
		return mb_strLen($this->value, $this->encoding);
	}

	/**
	 * @return String
	 */
	public function toUpper() {
		$this->value = mb_strToUpper($this->value, $this->encoding);
		return $this;
	}

	/**
	 * @return String
	 */
	public function toLower() {
		$this->value = mb_strToLower($this->value, $this->encoding);
		return $this;
	}

	/**
	 * @return String
	 * @param int $start
	 * @param int $length
	 */
	public function subStr($start, $length = null) {
		return new self(mb_subStr($this->value, $start, $length, $this->encoding));
	}

	/**
	 * @return String
	 */
	public function ucFirst() {
		$this->value = $this->subStr(0, 1)->toUpper() . $this->subStr(1, $this->length())->toLower();
		return $this;
	}

	/**
	 * @return String
	 * @param int width
	 * @param string $break
	 */
	public function wrap($width = 80, $break = PHP_EOL) {
		$this->value = preg_replace('#(\S{' . $width . ',})#e', "chunk_split('$1', " . $width . ", '" . $break . "')", $this->value);
		Nano_Log::message(var_export($this->value, true));
		return $this;
	}

	/**
	 * @return string
	 */
	public function __toString() {
		return $this->value;
	}

	/**
	 * @return string
	 */
	public function __invoke() {
		return $this->value;
	}

}