<div class="grid_15 textcontent">
<form method="post">

	<?php echo $helper->ui()->textField('field-01', 'First field', '111<>', 'smallInput wide', 'field description and example'); ?>
	<?php echo $helper->ui()->textField('field-02', 'Large input field', '<h1>222</h1>', 'largeInput wide'); ?>
	<?php echo $helper->ui()->boolField('field-03', 'Third checkbox', true); ?>
	<?php echo $helper->ui()->boolField('field-04', 'Uncheced checkbox', false); ?>
	<?php echo $helper->ui()->textareaField('field-05', 'Textarea example', '', 'smallInput wide'); ?>
	<?php echo $helper->ui()->selectField('field-06', 'Select example', array(0 => 'value1', 1 => 'value2', 2 => 'value3'), 1, 'smallInput medium'); ?>
	<?php echo $helper->ui()->radioField('field-06', 'Select example', array('1' => 'Yes', '0' => 'No'), '0'); ?>
	<?php echo $helper->ui()->fileField('field-07', 'Example file upload', 'smallInput wide', 'field description and example'); ?>
	<?php echo $helper->ui()->inputField('password', 'field-10', 'Example password', null, 'smallInput medium'); ?>

</form>
</div>