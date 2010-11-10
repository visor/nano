<?php

/**
 * @group articles
 * @group framework
 */
class Article_FormTest extends TestUtils_TestCase {

	protected $valid = array();

	public function testRequiredValues() {
		self::markTestSkipped('need refactoring');
		$form  = new Site_Article_Form();
		$tests = array(
			  'empty'    => array()
			, 'no-title' => array(
				  'announce'  => 'announce'
				, 'body'      => 'body'
				, 'published' => '2010-01-01'
				, 'active'    => '1'
			)
			, 'empty-title' => array(
				  'title'     => ''
				, 'announce'  => 'announce'
				, 'body'      => 'body'
				, 'published' => '2010-01-01'
				, 'active'    => '1'
			)
			, 'no-body' => array(
				  'title'     => 'title'
				, 'announce'  => 'announce'
				, 'published' => '2010-01-01'
				, 'active'    => '1'
			)
			, 'empty-body' => array(
				  'title'     => 'title'
				, 'announce'  => 'announce'
				, 'body'      => ''
				, 'published' => '2010-01-01'
				, 'active'    => '1'
			)
		);
		foreach ($tests as $message => $data) {
			$form->populate($data);
			self::assertFalse($form->isValid(), $message);
		}

		$form->populate(array(
			  'title'     => 'title'
			, 'announce'  => 'announce'
			, 'body'      => 'body'
			, 'published' => '2010-01-01'
			, 'active'    => '1'
		));
		self::assertTrue($form->isValid(), $message);
	}

}