<div id="portlets">
	<div class="column" id="left">
		<?php echo $helper->ui()->blockStart('Some block', null, 'fixed'); ?>
		....<br />
		....<br />
		....<br />
		....<br />
		....<br />
		<?php echo $helper->ui()->blockEnd(); ?>

		<?php echo $helper->ui()->blockStart('information message examples', 'exclamation.gif', 'fixed'); ?>
			<?php echo $helper->ui()->message('success', 'Lorem ipsum dolor sit amet, consectetuer adipiscing elit'); ?>
			<?php echo $helper->ui()->message('warning', 'Lorem ipsum dolor sit amet, consectetuer adipiscing elit'); ?>
			<?php echo $helper->ui()->message('error', 'Lorem ipsum dolor sit amet, consectetuer adipiscing elit'); ?>
			<?php echo $helper->ui()->message('info', 'Lorem ipsum dolor sit amet, consectetuer adipiscing elit'); ?>
		<?php echo $helper->ui()->blockEnd(); ?>
	</div>

	<div class="column" id="right">
		<?php echo $helper->ui()->blockStart('Another one block', null, 'fixed'); ?>
		....<br />
		....<br />
		....<br />
		....<br />
		....<br />
		<?php echo $helper->ui()->blockEnd(); ?>
	</div>
</div>
