<?php

require './../library/Nano.php';

TestUtils_WebTest::startCoverage();
try {
	Nano::run();
} catch (Exception $e) {
	TestUtils_WebTest::stopCoverage();
	throw $e;
}
TestUtils_WebTest::stopCoverage();