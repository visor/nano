<?php

class TestArticle extends Site_Article_DbObject {

	const NAME = 'phpunit_test_articles';

	protected $table = self::NAME;

	public static function createTable() {
		self::db()->query(
			'create table `' . self::NAME . '` ('
				. '`article_id` bigint(11) not null auto_increment'
				. ', `title` varchar(255) not null'
				. ', `announce` text null'
				. ', `body` text not null'
				. ', `created` datetime not null'
				. ', `modified` datetime not null'
				. ', `published` datetime default null'
				. ', `active` bool not null default "0"'
				. ', primary key (`article_id`)'
				. ', key `article_active` (`active`)'
				. ', key `article_published` (`published`)'
				. ', key `article_public` (`active`, `published`)'
			. ') engine=InnoDB default charset=utf8'
		);
	}

	public static function dropTable() {
		self::db()->query('drop table if exists `' . self::NAME . '`');
	}

	/**
	 * @return TestArticle
	 * @param string $title
	 * @param string $body
	 */
	public static function createNew($title, $body) {
		return parent::create(get_called_class(), array(
			  'title' => $title
			, 'body' => $body
		));
	}

}