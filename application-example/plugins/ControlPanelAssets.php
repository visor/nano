<?php

class ControlPanelAssets implements Nano_C_Plugin {

	/**
	 * @return void
	 * @param Nano_C $controller
	 */
	public function init(Nano_C $controller) {
		if ('control-panel' !== $controller->layout) {
			return;
		}

		session_start();
		$controller->application()->message->load('control-panel');
//TODO: Use assets module
//		Assets::style()
//			->variable('images', '/resources/images')
//			->append($controller->application()->publicDir . '/resources/styles/960.css')
//			->append($controller->application()->publicDir . '/resources/styles/reset.css')
//			->append($controller->application()->publicDir . '/resources/styles/text.css')
//			->append($controller->application()->publicDir . '/resources/styles/default.css')
//			->append($controller->application()->publicDir . '/resources/styles/blue.css')
//			->append($controller->application()->publicDir . '/resources/styles/green.css')
//			->append($controller->application()->publicDir . '/resources/styles/red.css')
//			->append($controller->application()->publicDir . '/resources/styles/smoothness-ui.css')
//			->append($controller->application()->publicDir . '/resources/styles/actions.css')
//		;
//
//		Assets::script()
//			->append($controller->application()->publicDir . '/resources/scripts/jquery.min.js')
//			->append($controller->application()->publicDir . '/resources/scripts/jquery.blend.js')
//			->append($controller->application()->publicDir . '/resources/scripts/ui.core.js')
//			->append($controller->application()->publicDir . '/resources/scripts/ui.sortable.js')
//			->append($controller->application()->publicDir . '/resources/scripts/ui.dialog.js')
//			->append($controller->application()->publicDir . '/resources/scripts/ui.datepicker.js')
//			->append($controller->application()->publicDir . '/resources/scripts/effects.js')
//			->append($controller->application()->publicDir . '/resources/scripts/cp.js')
//		;
	}

	/**
	 * @return boolean
	 * @param Nano_C $controller
	 */
	public function before(Nano_C $controller) {}

	/**
	 * @return void
	 * @param Nano_C $controller
	 */
	public function after(Nano_C $controller) {}

}