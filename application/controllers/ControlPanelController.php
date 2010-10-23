<?php

class ControlPanelController extends Nano_C {

	public $layout = 'control-panel';

	public function dashboardAction() {
		$this->pageTitle = 'Dashboard';
		$this->pageClass = 'dashboard';
	}

	public function itemsAction() {
		$this->pageTitle = 'Items list example';
		$this->pageClass = 'content_edit';
		$this->page      = isset($_GET['page']) ? $_GET['page'] : 1;
		$this->items     = array();
	}

	public function editAction() {
		$this->pageTitle = 'Edit form example';
		$this->pageClass = 'content_edit';
		$this->id        = $this->p('id');
	}

	public function variablesAction() {
		$this->pageTitle = 'Edit form example';
		$this->pageClass = 'dashboard';
		$this->variable1 = new stdClass();
		$this->variable2 = Cache::instance();
		$this->variable3 = $_REQUEST;
		$this->variable4 = $_SERVER;
		$this->variable5 = '<some>with<xml>example<elements>contents</elements></xml></some>';
		return $this->render('common', 'variables');
	}

}