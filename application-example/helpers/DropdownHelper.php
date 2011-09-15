<?php

class DropdownHelper extends Nano_Helper {

	/**
	 * @return string
	 * @param string[string] $attributes
	 * @param string[string] $values
	 * @param string $selected
	 */
	public function invoke(array $attributes = null, array $values = null, $selected = null, $default = null) {
		$result = '<select';
		foreach ($attributes as $name => $value) {
			$result .= ' ' . $name . '="' . htmlSpecialChars($value) . '"';
		}
		$result .= '>';
		if ($default) {
			$result .= '<option value=""' . ($selected == 0 ? ' selected="selected"' : '') . '>' . $default . '</option>';
		}
		foreach ($values as $value => $title) {
			$result .= '<option value="' . $value . '"' . ($selected == $value ? ' selected="selected"' : '') . '>' . $title . '</option>';
		}
		$result .= '</select>';
		return $result;
	}

}