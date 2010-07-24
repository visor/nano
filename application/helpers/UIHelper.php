<?php

class UIHelper extends Nano_Helper {

	public function invoke() {
		return $this;
	}

	public function blockStart($title, $image = null, $titleClass = null, $contentClass = null) {
		return
			'<div class="portlet">'
				. '<div class="portlet-header' . ($titleClass ? ' ' . $titleClass : '') . '">'
					. ($image ? '<img src="/resources/images/icons/' . $images . '" width="16" height="16" alt="' . $title .'" title="' . $title .'" />' : '')
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

}