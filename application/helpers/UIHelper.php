<?php

class UIHelper extends Nano_Helper {

	public function invoke() {
		return $this;
	}

	public function blockStart($title, $image = null, $titleClass = null, $contentClass = null) {
		return
			'<div class="portlet ui-widget ui-widget-content ui-helper-clearfix ui-corner-all">'
				. '<div class="portlet-header ui-widget-header ui-corner-top' . ($titleClass ? ' ' . $titleClass : '') . '">'
					. '<span class="ui-icon ui-icon-triangle-1-n"></span>'
					. ($image ? '<img src="/resources/images/icons/' . $image . '" width="16" height="16" alt="' . $title .'" title="' . $title .'" />' : '')
					. $title
				. '</div>'
				. '<div class="portlet-content' . ($contentClass ? ' ' . $contentClass : '') . '">'
		;
	}

	public function blockEnd() {
		return '</div></div>';
	}

	public function message($type, $text) {
		return '<p class="info ' . $type . '"><span class="info_inner">' . $text . '</span></p>';
	}

	public function input() {
	}

}