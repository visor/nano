<?php

class Nano_Validator_Composite extends Nano_Validator implements Nano_Validator_Interface {

	/**
	 * @var Nano_Validator[]
	 */
	protected $validators = array();

	/**
	 * @return Nano_Validator_Composite
	 * @param Nano_Validator $validator
	 * @param string|null $message
	 */
	public function append(Nano_Validator $validator, $message = null) {
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