<?php

abstract class Site_Editable_Controller extends Nano_C {

	const ITEMS_PER_PAGE = 20;

	/**
	 * @var string
	 */
	protected $messagesKey = null;

	/**
	 * @var string
	 */
	protected $backUrl     = null;

	/**
	 * @return Nano_DbObject
	 */
	abstract public function getEditable();

	/**
	 * @return Nano_Form
	 */
	abstract public function getForm();

	/**
	 * @return array
	 */
	abstract public function getData();

	/**
	 * @return void
	 */
	public function indexAction() {
		$this->template = 'list';
		$this->listAction();
	}

	abstract public function listAction();

	public function createAction() {
		$this->template = 'edit';
		$this->editAction();
	}

	public function editAction() {
		$this->form = $this->getForm();
		$this->form->populate(
			$this->helper->request()->restore()
			? $this->helper->request()->data()
			: $this->getEditable()->toForm()
		);
		$this->helper->request()->saveUrl()->saveReferer();
	}

	public function saveAction() {
		$form = $this->getForm();
		$form->populate($this->getData());
		if ($form->isValid()) {
			$editable = $this->getEditable();
			$created  = $editable->isNew();
			$editable->populate($form->getValues());
			if ($editable->save()) {
				$this->backUrl = $this->helper->request()->restoreReferer();
				$this->goBack(true, $this->messageKey . '-' . ($created ? 'create' : 'save') . '-success');
				$this->helper->request()->restoreUrl();
			} else {
				$this->backUrl = $this->helper->request()->restoreUrl();
				$this->goBack(false, $this->messageKey . '-' . ($created ? 'create' : 'save') . '-fails');
			}
		} else {
			$errors = array();
			foreach ($form->getErrors() as $field => $messages) {
				$errors = array_merge($errors, $messages);
			};
			$this->helper->flash(implode('<br />', $errors), true);
			$this->backUrl = $this->helper->request()->restoreUrl();
			$this->goBack(false);
		}
	}

	public function viewAction() {
		$this->editable = $this->getEditable();
	}

	public function deleteAction() {
		$this->backUrl = $this->helper->request()->saveReferer()->restoreReferer();
		if ($this->getEditable()->delete()) {
			$this->goBack(true, $this->messageKey . '-delete-success');
		} else {
			$this->goBack(false, $this->messageKey . '-delete-fails');
		}
	}

	protected function init() {
		parent::init();
		if (null === $this->messagesKey) {
			$this->messagesKey = Nano::dispatcher()->controller();
		}
		Nano::message()->load($this->messagesKey);
	}

	protected function goBack($success, $messageId = null) {
		if (!$success) {
			$this->helper->request()->save();
		}
		$this->helper->flash(null === $messageId ? null : Nano::message()->m($messageId), !$success, $this->backUrl);
	}

}