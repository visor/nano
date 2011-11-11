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
		Nano::message()->load('control-panel');
		Assets::style()
			->variable('images', '/resources/images')
			->append(WEB_ROOT . '/resources/styles/960.css')
			->append(WEB_ROOT . '/resources/styles/reset.css')
			->append(WEB_ROOT . '/resources/styles/text.css')
			->append(WEB_ROOT . '/resources/styles/default.css')
			->append(WEB_ROOT . '/resources/styles/blue.css')
//			->append(WEB_ROOT . '/resources/styles/green.css')
//			->append(WEB_ROOT . '/resources/styles/red.css')
			->append(WEB_ROOT . '/resources/styles/smoothness-ui.css')
			->append(WEB_ROOT . '/resources/styles/actions.css')
		;

		Assets::script()
			->append(WEB_ROOT . '/resources/scripts/jquery.min.js')
			->append(WEB_ROOT . '/resources/scripts/jquery.blend.js')
			->append(WEB_ROOT . '/resources/scripts/ui.core.js')
			->append(WEB_ROOT . '/resources/scripts/ui.sortable.js')
			->append(WEB_ROOT . '/resources/scripts/ui.dialog.js')
			->append(WEB_ROOT . '/resources/scripts/ui.datepicker.js')
			->append(WEB_ROOT . '/resources/scripts/effects.js')
			->append(WEB_ROOT . '/resources/scripts/cp.js')
		;
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