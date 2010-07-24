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

	public function textField($name, $title, $value = null, $css = null, $description = null) {
		return
			'<p>'
				. '<label for="' . $name . '">' . $title . '</label>'
				. '<input ' . ($css ? 'class="' . $css . '"' : '') . ' type="text" name="' . $name . '" id="' . $name . '" value="' . htmlSpecialChars($value) . '" />'
				. ($description ? '<small>' . $description . '</small>' : '')
			. '</p>'
		;
	}

	public function textareaField($name, $title, $value = null, $css = null, $description = null) {
		return
			'<p>'
				. '<label for="' . $name . '">' . $title . '</label>'
				. '<textarea ' . ($css ? 'class="' . $css . '"' : '') . ' name="' . $name . '" id="' . $name . '" style="height: 75px">'
					. htmlSpecialChars($value)
				. '</textarea>'
				. ($description ? '<small>' . $description . '</small>' : '')
			. '</p>'
		;
	}

	public function boolField($name, $title, $checked = null) {
		return
			'<p>'
				. '<label for="' . $name . '">'
					. '<input class="check" type="checkbox" name="' . $name . '" id="' . $name . '" value="1"' . ($checked ? ' checked="checked"' : '') . ' />'
				 	. '&nbsp;' . $title
				 . '</label>'
			. '</p>'
		;
	}

}