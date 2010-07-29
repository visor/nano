<?php

class Nano_Migrate_Script_20100729182116_example_settings extends Nano_Migrate_Script {

	/**
	 * @return void
	 * @param Nano_Db $db
	 */
	public function run(Nano_Db $db) {
		Setting_Category::append('core', 'Core settings');
		Setting::append('core', 'string', 'version', 'Core version', null, '1.0.0');
		Setting::append('core', 'bool', 'enabled', 'Enable core', null, '0');

		Setting_Category::append('application', 'Application settings');
		Setting::append('application', 'string', 'name', 'Application name');
		Setting::append('application', 'text', 'desc', 'Application description');
		Setting::append('application', 'html', 'footer', 'Application footer text');

		Setting_Category::append('email', 'E-Mail settings');
		Setting::append('email', 'string', 'admin', 'Administrator e-mail addres');
	}

}