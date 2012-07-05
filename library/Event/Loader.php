<?php

namespace Nano\Event;

class Loader {

	const DEFAULT_MASK = '*.php';

	/**
	 * @var \ArrayObject
	 */
	protected $files, $directories;

	protected $loaded = false;

	public function __construct() {
		$this->files       = new \ArrayObject();
		$this->directories = new \ArrayObject();
	}

	/**
	 * @return \Nano\Event\Loader
	 * @param $filePathName
	 */
	public function useFile($filePathName) {
		$this->files->offsetSet($filePathName, true);
		return $this;
	}

	/**
	 * @return \Nano\Event\Loader
	 * @param string $dirName
	 * @param string|null $fileMask
	 */
	public function useDirectory($dirName, $fileMask = null) {
		$this->directories->offsetSet($dirName, $fileMask);
		return $this;
	}

	/**
	 * @return boolean
	 */
	public function alreadyLoaded() {
		return $this->loaded;
	}

	/**
	 * @return \Nano\Event\Loader
	 * @param \Nano\Event\Manager $manager
	 */
	public function load(\Nano\Event\Manager $manager) {
		if ($this->loaded) {
			return $this;
		}

		foreach ($this->files as $filePath => $nop) {
			$this->loadHandlersFromFile($manager, $filePath);
		}

		foreach ($this->directories as $dirName => $mask) {
			$this->loadHandlerFromDirectory($manager, $dirName, $mask);
		}

		$this->loaded = true;
		return $this;
	}

	/**
	 * @param \Nano\Event\Manager $manager
	 * @param string $fileName
	 */
	protected function loadHandlersFromFile(\Nano\Event\Manager $manager, $fileName) {
		if (!file_exists($fileName)) {
			return;
		}
		$loader = function () use ($manager, $fileName) {
			include $fileName;
		};
		$loader();
	}

	/**
	 * @param \Nano\Event\Manager $manager
	 * @param string $dirName
	 * @param string $mask
	 */
	protected function loadHandlerFromDirectory(\Nano\Event\Manager $manager, $dirName, $mask) {
		if (null === $mask) {
			$mask = self::DEFAULT_MASK;
		}
		$iterator = new \GlobIterator($dirName . DIRECTORY_SEPARATOR . $mask, \GlobIterator::SKIP_DOTS);
		foreach ($iterator as /** @var \GlobIterator $fileName */ $fileName) {
			if ($fileName->isDir()) {
				continue;
			}
			$this->loadHandlersFromFile($manager, $fileName->getPathName());
		}
	}

}