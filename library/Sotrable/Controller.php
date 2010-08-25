<?php

/**
 * @method Sortable_DbObject getEditable()
 */
abstract class Sortable_Controller extends Article_Controller {

	/**
	 * @return Nano_Form
	 */
	public function getForm() {
		return new Sortable_Form();
	}

	public function moveTopAction() {
		$this->backUrl = $this->helper->request()->saveReferer()->restoreReferer();
		if ($this->getEditable()->moveTop()) {
			$this->goBack(true, $this->messageKey . 'move-ok');
		} else {
			$this->goBack(false, $this->messageKey . 'move-fails');
		}
	}

	public function moveUpAction() {
		$this->backUrl = $this->helper->request()->saveReferer()->restoreReferer();
		if ($this->getEditable()->moveUp()) {
			$this->goBack(true, $this->messageKey . 'move-ok');
		} else {
			$this->goBack(false, $this->messageKey . 'move-fails');
		}
	}

	public function moveDownAction() {
		$this->backUrl = $this->helper->request()->saveReferer()->restoreReferer();
		if ($this->getEditable()->moveDown()) {
			$this->goBack(true, $this->messageKey . 'move-ok');
		} else {
			$this->goBack(false, $this->messageKey . 'move-fails');
		}
	}

	public function moveBottomAction() {
		$this->backUrl = $this->helper->request()->saveReferer()->restoreReferer();
		if ($this->getEditable()->moveBottom()) {
			$this->goBack(true, $this->messageKey . 'move-ok');
		} else {
			$this->goBack(false, $this->messageKey . 'move-fails');
		}
	}

}