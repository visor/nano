<?php

$sql = array();

$sql[] = 'create table `settings_categories` ('
	. '`setting_category_id` int(11) not null auto_increment'
	. ', `name` varchar(100) not null'
	. ', `title` varchar(255)'
	. ', `description` text null'
	. ', `order` int(11) not null'
	. ', unique key (`name`)'
	. ', unique key (`order`)'
	. ', primary key (`setting_category_id`)'
. ')';


$sql[] = 'create table `settings` ('
	. '`setting_id` bigint(20) not null auto_increment'
	. ', `setting_category_id` int(11) not null'
	. ', `type` enum("string", "list", "text", "bool", "html") not null default "text"'
	. ', `name` varchar(100) not null'
	. ', `value` text null'
	. ', `default` text null'
	. ', `values` text null'
	. ', `title` varchar(255)'
	. ', `description` text null'
	. ', `order` bigint(20) not null'
	. ', unique key (`setting_category_id`, `name`)'
	. ', unique key (`setting_category_id`, `order`)'
	. ', primary key (`setting_id`)'
. ')';

$sql[] = 'alter table `settings`'
	. ' add constraint `setting_category_fk` foreign key (`setting_category_id`) references `settings_categories`(`setting_category_id`) on delete cascade on update cascade'
;
