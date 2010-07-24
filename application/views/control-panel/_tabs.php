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
	);

	foreach ($links as $linkAction => $link) {
		echo '<li><a href="' . $link['url'] . '"' . ($action === $linkAction ? ' class="current"' : '') . '><span>' . $link['title'] . '</span></a></li>';
	}
?>