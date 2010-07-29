<div class="grid_15 textcontent">
	<form action="/cp/settings/save" method="post">
<?php

foreach ($settings as $setting) {
	echo $helper->setting()->field($setting);
}
echo $helper->ui()->submit('Save');

?>
	</form>
</div>