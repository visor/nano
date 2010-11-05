<?php

class TestFixtureForTest extends TestUtils_Fixture {

	protected $activeRecord = 'ActiveRecordBasic';

	protected function dataForDefault($index) {
		return array(
			'text' => 'example text for record ' . sprintf('%03d', $index)
		);
	}

}
