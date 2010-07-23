<?php

class Nano_Validator_Composite extends Nano_Validator implements Nano_Validator_Interface {

	/**
	 * @return Nano_Validator
	 * @param Nano_Validator_Interface $validator
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
		foreach ($this->validators as $validator) { /* $var $validator Nano_Validator_Interface */
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