<?php


namespace CliScript;

/**
 * @description Replaces indentation spaces into tabs, converts line endings into Unix and removes trailing spaces
 */
class FixSpaces extends \Nano\Cli\Script {

	/**
	 * @return void
	 * @param string[] $args
	 */
	public function run(array $args) {
		$iterator = new \RecursiveIteratorIterator(
			new \RecursiveDirectoryIterator($this->getApplication()->rootDir)
			, \RecursiveIteratorIterator::CHILD_FIRST
		);

		foreach ($iterator as $file) { /** @var \DirectoryIterator $file */
			if ($file->isDir()) {
				continue;
			}
			if ('php' != pathInfo($file->getBaseName(), PATHINFO_EXTENSION)) {
				continue;
			}

			$source = file_get_contents($file->getPathName());
			$result = $this->convertLineEnds($source);
			$result = $this->removeTrailingSpaces($result);
			$result = $this->convertIndentationSpaces($result);
			if ($source === $result) {
				continue;
			}
			file_put_contents($file->getPathName(), $result);
			echo $file->getPathName(), PHP_EOL;
		}

	}

	/**
	 * @return string
	 * @param string $source
	 */
	protected function convertLineEnds($source) {
		return preg_replace("/\r\n|\r/m", "\n", $source);
	}

	/**
	 * @return string
	 * @param string $source
	 */
	protected function removeTrailingSpaces($source) {
		return preg_replace("/[ \t]+\n/m", "\n", $source);
	}

	/**
	 * @return string
	 * @param string $source
	 */
	protected function convertIndentationSpaces($source) {
		$callBack = function($matches) {
			$count = strLen($matches[1]) / 4;
			return str_repeat("\t", $count);
		};
		return preg_replace_callback("/^((?:    )+)/m", $callBack, $source);
	}

}