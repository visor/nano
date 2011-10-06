<?php

/**
 * @property int $id
 * @property string $location
 */
class Library_Orm_Example_Address extends Orm_Model {

	public $beforeInsert = 0;
	public $beforeUpdate = 0;
	public $afterInsert  = 0;
	public $afterUpdate  = 0;
	public $afterSave    = 0;

	protected function beforeInsert() {
		$this->beforeInsert = 1;
	}

	protected function beforeUpdate() {
		$this->beforeUpdate = 1;
	}

	protected function afterInsert() {
		$this->afterInsert = 1;
	}

	protected function afterUpdate() {
		$this->afterUpdate = 1;
	}

	protected function afterSave() {
		$this->afterSave = 1;
	}

}