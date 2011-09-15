<?php
	$links = array(
		  'dashboard' => array(
			  'title' => 'Dashboard elements'
			, 'url'   => '/cp'
		)
		, 'items' => array(
			  'title' => 'List example'
			, 'url'   => '/cp/items'
		)
		, 'edit' => array(
			  'title' => 'Form example'
			, 'url'   => '/cp/edit/321'
		)
		, 'variables' => array(
			  'title' => 'Dump variables example'
			, 'url'   => '/cp/variables'
		)
	);

	foreach ($links as $linkAction => $link) {
		echo '<li><a href="' . $link['url'] . '"' . ($action === $linkAction ? ' class="current"' : '') . '><span>' . $link['title'] . '</span></a></li>';
	}
?>