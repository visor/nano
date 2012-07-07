<?php

namespace Nano\Validator;

class Composite extends \Nano\Validator {

	/**
	 * @var \Nano\Validator[]
	 */
	protected $validators = array();

	/**
	 * @return \Nano\Validator\Composite
	 * @param \Nano\Validator $validator
	 * @param string|null $message
	 */
	public function append(\Nano\Validator $validator, $message = null) {
		$validator->setMessage($message);
		$this->validators[] = $validator;
		return $this;
	}

	/**
	 * @return boolean
	 * @param mixed $value
	 */
	public function isValid($value) {
		foreach ($this->validators as $validator) {
			if ($validator->isValid($value)) {
				continue;
			}
			if (null === $this->getMessage()) {
				$this->setMessage($validator->getMessage());
			}
			return false;
		}
		return true;
	}

}