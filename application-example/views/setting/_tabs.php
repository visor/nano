<?php

	foreach ($helper->setting()->categories() as $category) { /* @var $category Setting_Category */
		echo '<li><a href="/cp/settings/' . $category->name . '"' . ($current->setting_category_id === $category->setting_category_id ? ' class="current"' : '') . '><span>' . $category->title . '</span></a></li>';
	}

?>