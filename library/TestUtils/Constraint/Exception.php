<?php

class TestUtils_Constraint_Exception extends PHPUnit_Framework_Constraint {

	/**
	 * @var string
	 */
	protected $exceptionClass;

	/**
	 * @var string
	 */
	protected $exceptionMessage;

	/**
	 * @var string
	 */
	protected $description = null;

	/**
	 * @param string $exceptionClass
	 * @param string $exceptionMessage
	 */
	public function __construct($exceptionClass, $exceptionMessage) {
		$this->exceptionClass   = $exceptionClass;
		$this->exceptionMessage = $exceptionMessage;
	}

	/**
	 * Evaluates the constraint for parameter $other. Returns TRUE if the
	 * constraint is met, FALSE otherwise.
	 *
	 * @return bool
	 * @param mixed $other Value or object to evaluate.
	 */
	public function evaluate($other) {
		$this->description = null;
		try {
			$other();
		} catch (Exception $e) {
			if ($e instanceof $this->exceptionClass) {
				if (null === $this->exceptionMessage) {
					return true;
				}
				if (false === strIPos($e->getMessage(), $this->exceptionMessage)) {
					$this->description =
						'Exception message not matches'
						. PHP_EOL . PHP_EOL
						. PHPUnit_Util_Diff::diff($this->exceptionMessage, $e->getMessage())
					;
					return false;
				}
				return true;
			} else {
				$this->description =
					'Exception class not matches'
					. PHP_EOL . PHP_EOL
					. PHPUnit_Util_Diff::diff($this->exceptionClass, get_class($e))
				;
				return false;
			}
		}
		$this->description = 'No exception thrown';
		return false;
	}

	/**
	 * @return string
	 */
	public function toString() {
		return 'exception <' . $this->exceptionClass . '>'. (null === $this->exceptionMessage ? '' : ' with message \'' . $this->exceptionMessage . '\'');
	}

	protected function customFailureDescription($other, $description, $not) {
		return $this->toString() . ' should throw' . (null === $this->description ? '' : PHP_EOL . $this->description);
	}

}