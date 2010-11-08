<?php

require dirName(__DIR__) . '/library/Nano.php';
Nano::instance();

$iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator(ROOT), RecursiveIteratorIterator::CHILD_FIRST);

foreach ($iterator as $file) { /** @var SplFileInfo $file */
	if ($file->isDir()) {
		continue;
	}
	if ('php' != pathInfo($file->getBaseName(), PATHINFO_EXTENSION)) {
		continue;
	}

	$source = file_get_contents($file->getPathName());
	$result = convertLineEnds($source);
	$result = removeTrailingSpaces($result);
	$result = convertIndentationSpaces($result);
	if ($source === $result) {
		continue;
	}
	file_put_contents($file->getPathName(), $result);
	echo $file->getPathName(), PHP_EOL;
}

function convertLineEnds($source) {
	return preg_replace("/\r\n|\r/m", "\n", $source);
}

function removeTrailingSpaces($source) {
	return preg_replace("/[ \t]+\n/m", "\n", $source);
}

function convertIndentationSpaces($source) {
	$callBack = function($matches) {
		$count = strLen($matches[1]) / 4;
		return str_repeat("\t", $count);
	};
	return preg_replace_callback("/^((?:    )+)/m", $callBack, $source);
}