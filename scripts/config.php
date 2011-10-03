<?php

namespace CliScript;

/**
 * @description Created empty configuration with given name and parents
 * @param required $name Name of new configuration
 * @param required $parent One ore more parent configurations. Pass NONE if no parents required
 */
class Config extends \Nano_Cli_Script {

	/**
	 * @param string[] $args
	 * @return void
	 */
	public function run(array $args) {
		if (0 == count($args)) {
			$this->stop('Please pass new configuration name and it\'s parent(s)', 1);
		}
		if (1 == count($args)) {
			$this->stop('Please pass new configuration parent(s) or string NONE if no parents', 1);
		}

		$name    = $args[0];
		$parents = $args;
		$base    = $this->getApplication()->getRootDir() . DIRECTORY_SEPARATOR . 'settings';
		$new     = $base . DIRECTORY_SEPARATOR . $name;
		array_shift($parents);

		if (file_exists($new)) {
			echo 'Using setup directory', PHP_EOL, "\t", $new, PHP_EOL;
		} else {
			echo 'Creating new setup directory', PHP_EOL, "\t", $new, PHP_EOL;
			mkDir($new, 0755, true);
		}

		if (in_array('NONE', $parents)) {
			echo "\t\t", 'no parents', PHP_EOL;
			$parents = array();
		} else {
			file_put_contents(
				$new . DIRECTORY_SEPARATOR . \Nano_Config_Builder::PARENTS_FILE
				, '<?php return ' . var_export($parents, true) . ';'
			);
			echo "\t\t", \Nano_Config_Builder::PARENTS_FILE, PHP_EOL;
		}

		foreach ($parents as $parent) {
			$i = new \DirectoryIterator($base . DIRECTORY_SEPARATOR . $parent);
			foreach ($i as /** @var \DirectoryIterator $file */$file) {
				if ($file->isDir() || $file->isDir() || !$file->isReadable()) {
					continue;
				}
				if (\Nano_Config_Builder::PARENTS_FILE === $file->getBaseName()) {
					continue;
				}
				if (\Nano_Config_Builder::ROUTES_FILE === $file->getBaseName()) {
					continue;
				}
				if ('php' !== pathInfo($file->getBaseName(), PATHINFO_EXTENSION)) {
					continue;
				}
				$newFile = $new . DIRECTORY_SEPARATOR . $file->getBaseName();
				if (file_exists($newFile)) {
					continue;
				}

				file_put_contents($newFile, '<?php return array(' . PHP_EOL . ');');
				echo "\t\t", $file->getBaseName(), PHP_EOL;
			}
		}
		echo "\t\t", \Nano_Config_Builder::ROUTES_FILE, PHP_EOL;
		file_put_contents(
			$new . DIRECTORY_SEPARATOR . \Nano_Config_Builder::ROUTES_FILE
			, '<?php' . PHP_EOL . PHP_EOL
		);
		echo 'Done', PHP_EOL;
	}

}