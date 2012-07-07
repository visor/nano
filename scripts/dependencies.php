<?php

namespace CliScript;

/**
 * @description Install required modules and framework version
 */
class Dependencies extends \Nano\Cli\Script {

	const FILE_NAME    = '.dependencies.php';
	const FILE_MODULES = '.gitmodules';

	const KEY_SOURCE = 'source';
	const KEY_TAG    = 'tag';
	const KEY_BRANCH = 'branch';
	const KEY_COMMIT = 'commit';

	const DIR_DEPENCIES = 'dependencies';

	/**
	 * @var array
	 */
	protected $submodules = null;

	/**
	 * @return int
	 * @param string[] $args
	 */
	public function run(array $args) {
		$file = $this->getApplication()->rootDir . DIRECTORY_SEPARATOR . self::FILE_NAME;
		if (!file_exists($file)) {
			$this->showHelpForFile();
			return;
		}

		$dependencies = null;
		include $file;
		if (!is_array($dependencies)) {
			$this->showHelpForFile();
			return;
		}

		chDir($this->getApplication()->rootDir);
		$destination = $this->checkDestinationFolder();
		$this->updateDependencies($destination, $dependencies);
	}

	/**
	 * @return void
	 * @param string $destination
	 * @param array $dependencies
	 */
	protected function updateDependencies($destination, array $dependencies) {
		echo 'Checking for dependencies', PHP_EOL;
		$modules = array();
		foreach ($dependencies as $name => $info) {
			echo '  - ', $name;
			if (!isSet($info[self::KEY_SOURCE])) {
				echo PHP_EOL, '     Source not specified, ingored', PHP_EOL;
				continue;
			}
			if (!isSet($info[self::KEY_TAG]) && !isSet($info[self::KEY_BRANCH]) && !isSet($info[self::KEY_COMMIT])) {
				echo PHP_EOL, '     No version specified, ingored', PHP_EOL;
				continue;
			}
			$modules[] = self::DIR_DEPENCIES . DIRECTORY_SEPARATOR . $name;
			$this->updateDependency($destination, $name, $info);
			echo PHP_EOL;
		}
		echo 'Done', PHP_EOL;

		echo 'Checking for changes', PHP_EOL;
		$status = shell_exec('git status --porcelain');
		if (null !== $status) {
			$files = explode(PHP_EOL, trim($status));
			$add   = array();
			foreach ($files as $file) {
				$name = subStr($file, 3);
				if (self::FILE_MODULES == $name) {
					$add[] = self::FILE_MODULES;
				}
				if (in_array($name, $modules)) {
					$add[] = $name;
				}
			}
			if (count($add) > 0) {
				echo '   Commiting...';
				shell_exec('git add "' . implode('" "', $add) . '" >/dev/null 2>&1 && git commit -m "Dependencies updated" >/dev/null 2>&1');
				echo PHP_EOL;
			}
		}
		echo 'Done', PHP_EOL;
	}

	protected function updateDependency($destination, $name, $info) {
		$submoduleDir  = self::DIR_DEPENCIES . DIRECTORY_SEPARATOR . $name;
		$submodules    = $this->getSubModules();
		$commands      = array();
		$intoNull      = ' >/dev/null 2>&1';

		if (!isSet($submodules[$submoduleDir])) {
			$commands[]    = 'git submodule add ' . $info[self::KEY_SOURCE] . ' ' . $submoduleDir . $intoNull;
		}
		if (isSet($info[self::KEY_BRANCH])) {
			$commands[] = 'cd ' . $submoduleDir;
			$commands[] = 'git checkout ' . $info[self::KEY_BRANCH] . $intoNull;
			$commands[] = 'git pull' . $intoNull;
		}

		$result = shell_exec(implode(' && ', $commands));
	}

	/**
	 * @return string
	 */
	protected function checkDestinationFolder() {
		echo 'Checking destination directory', PHP_EOL;
		$destination = getCwd() . DIRECTORY_SEPARATOR . self::DIR_DEPENCIES;
		if (file_exists($destination) && !is_dir($destination)) {
			echo '   Not directory, ignore', PHP_EOL;
			exit();
		}
		if (!file_exists($destination)) {
			mkDir($destination, 0755, true);
		}
		echo 'Done', PHP_EOL;
		return $destination;
	}

	protected function getSubModules() {
		if (null === $this->submodules) {
			$command = 'git submodule status';
			$data    = shell_exec($command);
			$items   = explode(PHP_EOL, $data);
			foreach ($items as $item) {
				if (empty($item)) {
					continue;
				}
				list($commit, $dir, $ref) = preg_split('/\s+/', trim($item));
				if ('+' == $commit[0]) {
					$commit = subStr($commit, 1);
				}

				$this->submodules[$dir] = array(
					'commit' => $commit
					, 'ref'  => subStr($ref, 1, -1)
				);
			}
		}
		return $this->submodules;
	}

	protected function showHelpForFile() {
		//
	}

}