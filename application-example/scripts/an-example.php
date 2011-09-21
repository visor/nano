<?php
/**
 * Example application script. Prints configured application modules
 */

if (0 == Application::current()->getModules()->count()) {
	echo 'No application modules', PHP_EOL;
	return;
}

echo 'Application modules:', PHP_EOL;
foreach (Application::current()->getModules()->count() as $name => $path) {
	echo ' - ', $name, '    ', $path, PHP_EOL;
}