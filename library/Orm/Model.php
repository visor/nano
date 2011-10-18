<?php

abstract class Orm_Model {

	/**
	 * @var stdClass
	 */
	protected $data, $original;

	/**
	 * @var string
	 */
	protected $changedFields;

	/**
	 * @var boolean
	 */
	protected $new;

	/**
	 * @param array $data
	 * @param mixed $new;
	 */
	public function __construct(array $data = array(), $new = true) {
		$this->data = new stdClass();
		$this->new  = 0 == func_num_args() || 1 == func_num_args();

		$this->markUnchanged();
		static::mapper()->mapToModel($this->data, $data);
	}

	/**
	 * @return Orm_Mapper
	 */
	public static function mapper() {
		return Orm::mapper(get_called_class());
	}

	/**
	 * @return boolean
	 */
	public function isNew() {
		return $this->new;
	}

	/**
	 * @return boolean
	 */
	public function changed() {
		return count($this->changedFields) > 0;
	}

	public function save() {
		if (!$this->changed()) {
			return true;
		}
		if ($this->new) {
			$this->beforeInsert();
			if (static::mapper()->insert($this)) {
				$this->new = false;
				$this->markUnchanged();
				$this->afterInsert();
				$this->afterSave();
				return true;
			}
			return false;
		}

		$this->beforeUpdate();
		if (static::mapper()->update($this)) {
			$this->markUnchanged();
			$this->afterUpdate();
			$this->afterSave();
			return true;
		}
		return false;
	}

	public function delete() {
		if ($this->new) {
			return false;
		}
		return static::mapper()->delete($this);
	}

	/**
	 * @return stdClass
	 */
	public function getData() {
		return $this->data;
	}

	/**
	 * @return boolean
	 * @param string $name
	 */
	public function __isSet($name) {
		return property_exists($this->data, $name);
	}

	/**
	 * @return mixed
	 * @param string $name
	 * @throws Orm_Exception_UnknownField
	 */
	public function __get($name) {
		if (property_exists($this->data, $name)) {
			return $this->data->$name;
		}
		if (static::mapper()->getResource()->relationExists($name)) {
			return static::mapper()->findRelated($name);
		}

		throw new Orm_Exception_UnknownField(static::mapper()->getResource(), $name);
	}

	/**
	 * @return void
	 * @param string $name
	 * @param mixed $value
	 */
	public function __set($name, $value) {
		if (property_exists($this->data, $name)) {
			if (static::mapper()->getResource()->isReadOnly($name)) {
				throw new Orm_Exception_ReadonlyField(static::mapper()->getResource(), $name);
			}
			if (isSet($this->original->$name) && $value === $this->original->$name) {
				$this->data->$name = $this->original->$name;
				unSet($this->changedFields[$name], $this->original->$name);
			} elseif ($this->data->$name !== $value) {
				if (isSet($this->data->$name) && !isSet($this->original->$name)) {
					$this->original->$name = $this->data->$name;
				}
				$this->data->$name = $value;
				$this->changedFields[$name] = $name;
			}
			return;
		}

		throw new Orm_Exception_UnknownField(static::mapper()->getResource(), $name);
	}

	protected function markUnchanged() {
		$this->original      = new stdClass();
		$this->changedFields = array();
	}

	protected function beforeInsert() {}

	protected function beforeUpdate() {}

	protected function afterInsert() {}

	protected function afterUpdate() {}

	protected function afterSave() {}

}