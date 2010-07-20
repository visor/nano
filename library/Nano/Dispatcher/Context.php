<?php

interface Nano_Dispatcher_Context {

	const CONTEXT_DEFAULT = 'default';
	const CONTEXT_MOBILE  = 'mobile';

	/**
	 * @return void
	 */
	public function detect();

	/**
	 * @return string
	 */
	public function get();

	/**
	 * @return boolean
	 */
	public function needRedirect();

	/**
	 * @return void
	 * @param string $url
	 */
	public function redirect($url);

}