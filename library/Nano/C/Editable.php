<?php

interface Nano_C_Editable {

	/**
	 * @return void
	 */
	public function listAction();

	/**
	 * @return Nano_Editable
	 */
	public function getEditable();

	/**
	 * @return Nano_Form
	 */
	public function getForm();

	/**
	 * @return array
	 */
	public function getData();

}