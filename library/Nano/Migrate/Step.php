<?php

class Nano_Migrate_Step {

	/**
	 * @var string[]
	 */
	protected $queries = array();

	/**
	 * @var Nano_Migrate_Script
	 */
	protected $script  = null;

	/**
	 * @var string
	 */
	protected $path = null;

	public function __construct($path) {
		$this->path = $path;
	}

	/**
	 * @return string[]
	 */
	public function getQueries() {
		return $this->queries;
	}

	/**
	 * @return Nano_Migrate_Script
	 */
	public function getScript() {
		return $this->script;
	}

	/**
	 * @return void
	 * @param Nano_Db $db
	 */
	public function run(Nano_Db $db) {
		$this->load();
		foreach ($this->queries as $query) {
			$db->query($query);
		}
		$this->getScript()->run($db);

		unset($this->queries);
		unset($this->script);
	}

	public function load() {
		$queries = $this->path . '/queries.php';
		$script  = $this->path . '/script.php';

		if (file_exists($queries)) {
			$sql = null;
			include $queries;
			if (is_array($sql)) {
				$this->queries = $sql;
			}
		}
		if (file_exists($script)) {
			include_once $script;
			$suffix = str_replace('-', '_', baseName($this->path));
			$class  = 'Nano_Migrate_Script_' . $suffix;
			if (class_exists($class, false)) {
				$reflection = new ReflectionClass($class);
				if ($reflection->isInstantiable() && $reflection->isSubclassOf('Nano_Migrate_Script')) {
					$this->script = $reflection->newInstance();
				} else {
					$this->script = Nano_Migrate_ScriptEmpty::instance();
				}
			} else {
				$this->script = Nano_Migrate_ScriptEmpty::instance();
			}
		} else {
			$this->script = Nano_Migrate_ScriptEmpty::instance();
		}
	}

}