<?php

class SettingHelper extends Nano_Helper {

	private static $functionMap = array(
		  'string' => 'textField'
		, 'list'   => 'selectField'
		, 'text'   => 'textareaField'
		, 'bool'   => 'boolField'
		, 'html'   => 'textareaField'
	);

	const CSS = 'smallInput wide';

	public function invoke() {
		return $this;
	}

	public function get($category, $name) {
		return Setting::get($category, $name);
	}

	public function categories() {
		return Setting_Category::all();
	}

	public function field(Setting $setting) {
		$category = Setting_Category::getById($setting->setting_category_id);
		$name     = $this->fieldName($category, $setting);
		$helper   = $setting->type . 'Field';
		return $this->{$helper}($setting, $name, $this->get($category->name, $setting->name));
	}

	protected function stringField(Setting $setting, $name, $value) {
		return $this->helper()->ui()->textField($name, $setting->title, $value, self::CSS, $setting->description);
	}

	protected function listField(Setting $setting, $name, $value) {
		$options = unSerialize($setting->values);
		return $this->helper()->ui()->selectField($name, $setting->title, $options, $value, self::CSS, $setting->description);
	}

	protected function textField(Setting $setting, $name, $value) {
		return $this->helper()->ui()->textareaField($name, $setting->title, $value, self::CSS, $setting->description);
	}

	protected function boolField(Setting $setting, $name, $value) {
		return $this->helper()->ui()->boolField($name, $setting->title, $value ? true : false);
	}

	protected function htmlField(Setting $setting, $name, $value) {
		return $this->helper()->ui()->textareaField($name, $setting->title, $value, self::CSS, $setting->description);
	}

	protected function fieldName(Setting_Category $category, Setting $setting) {
		return 'setting[' . $category->name .'][' . $setting->name . ']';
	}

}