<?php

interface Nano_Editable {

	/**
	 * @return Nano_Editable
	 * @param array $data
	 */
	public function populate(array $data);

	/**
	 * @return boolean
	 */
	public function save();

	/**
	 * @return boolean
	 */
	public function delete();

	/**
	 * @return array
	 */
	public function toForm();

}