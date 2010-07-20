<?php

interface Nano_C_Plugin {

	public function before(Nano_C $controller);

	public function after(Nano_C $controller);

}