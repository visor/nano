<?php

/**
 * $meta keys:
 * - name:    string
 * - fields   array   object fields description
 *   - name
 *   - type
 *   - null
 *   - values
 *   - readonly
 *   - default
 * - identity array   object primary key
 * - hasOne   array   one-to-one relations description
 *   - model
 * - hasMany          one-to-many relations description
 *   - model
 * - belongsTo        [one|many]-to-one relations description (second side for hasOne and hasMany)
 *   - model
 *
 * @throws Orm_Exception_UnknownField
 */
class Orm_Resource {

	/**
	 * @var array
	 */
	protected $meta;

	/**
	 * @param array $meta
	 */
	public function __construct(array $meta) {
		$this->meta = $meta;
	}

	/**
	 * @return string
	 */
	public function name() {
		return $this->meta['name'];
	}

	/**
	 * @return Orm_DataSource
	 */
	public function source() {
		return Orm::instance()->source($this->meta['source']);
	}

	/**
	 * @return Orm_DataSource
	 */
	public function sourceName() {
		return $this->meta['source'];
	}

	/**
	 * @return array
	 */
	public function fields() {
		return $this->meta['fields'];
	}

	/**
	 * @return string[]
	 */
	public function fieldNames() {
		return array_keys($this->fields());
	}

	/**
	 * @return array
	 * @param string $name
	 */
	public function field($name) {
		if (isSet($this->meta['fields'][$name])) {
			return $this->meta['fields'][$name];
		}
		throw new Orm_Exception_UnknownField($this, $name);
	}

	/**
	 * @param string $name
	 * @return string
	 */
	public function typeOf($name) {
		$field = $this->field($name);
		return $field['type'];
	}

	/**
	 * @return boolean
	 * @param string $name
	 */
	public function isReadOnly($name) {
		if ($this->incrementalField() === $name) {
			return true;
		}
		if (isSet($this->meta['fields'][$name])) {
			if (isSet($this->meta['fields'][$name]['readonly'])) {
				return $this->meta['fields'][$name]['readonly'];
			}
			return false;
		}
		throw new Orm_Exception_UnknownField($this, $name);
	}

	/**
	 * @return boolean
	 */
	public function isIncremental() {
		return isSet($this->meta['incremental']);
	}

	/**
	 * @return string|null
	 */
	public function incrementalField() {
		if ($this->isIncremental()) {
			return $this->meta['incremental'];
		}
		return null;
	}

	/**
	 * @return mixed
	 * @param string $name
	 */
	public function defaultValue($name) {
		if (isSet($this->meta['fields'][$name])) {
			if (isSet($this->meta['fields'][$name]['default'])) {
				return $this->meta['fields'][$name]['default'];
			}
			return null;
		}
		throw new Orm_Exception_UnknownField($this, $name);
	}

	/**
	 * @return mixed
	 * @param string $field
	 * @param mixed $value
	 */
	public function castToModel($field, $value) {
		return $this->source()->type($this->typeOf($field))->castToModel($value);
	}

	/**
	 * @return mixed
	 * @param string $field
	 * @param mixed $value
	 */
	public function castToDataSource($field, $value) {
		return $this->source()->type($this->typeOf($field))->castToDataSource($value);
	}

	/**
	 * @return string[]
	 */
	public function identity() {
		return $this->meta['identity'];
	}

	public function hasOne() {
		return $this->meta['hasOne'];
	}

	public function hasMany() {
		return $this->meta['hasMany'];
	}

	public function belongsTo() {
		return $this->meta['belongsTo'];
	}

}