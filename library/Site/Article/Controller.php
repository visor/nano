<?php

abstract class Site_Article_Controller extends Site_Editable_Controller {

	/**
	 * Class name of the article object
	 *
	 * @var string
	 */
	protected $articleClass = null;

	/**
	 * @return Site_Article_DbObject
	 */
	public function getEditable() {
		$class = $this->articleClass;
		$id    = $this->p('id');
		if (null === $id) {
			return new $class(null, true);
		}
		$result = $class::get($this->p('id'));
		if ($result->isNew()) {
			throw new RuntimeException();
		}
		return $result;
	}

	/**
	 * @return Nano_Form
	 */
	public function getForm() {
		return new Article_Form();
	}

	/**
	 * @return array
	 */
	public function getData() {
		return $_POST;
	}

	public function listAction() {
		$class = $this->articleClass;
		$pager = $this->helper->pager()->show('simple', '?page=%d', isset($_GET['page']) ? $_GET['page'] : 1, $class::countAll(), self::ITEMS_PER_PAGE);
		/** @var $pager Site_Pager_Simple */
		$this->pager = $pager;
		$this->items = $class::getAll($this->pager->getCurrentPage(), $this->pager->getItemsPerPage());
	}

	public function publishAction() {
		$this->backUrl = $this->helper->request()->saveReferer()->restoreReferer();
		try {
			$date = Date::create($_GET['date']);
			$this->getEditable()->publish($date)->save();
			$this->goBack(true, $this->messageKey . '-publish-success');
		} catch(Exception $e) {
			$this->goBack(false, $this->messageKey . '-publish-fails');
		}
	}

	public function unpublishAction() {
		$this->backUrl = $this->helper->request()->saveReferer()->restoreReferer();
		try {
			$this->getEditable()->unpublish()->save();
			$this->goBack(true, $this->messageKey . '-unpublish-success');
		} catch (Exception $e) {
			$this->goBack(false, $this->messageKey . '-unpublish-fails');
		}
	}

}