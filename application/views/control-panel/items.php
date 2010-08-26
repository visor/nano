<?php $actions = array('publish' => 'Publish', 'unpublish' => 'Unpublish', 'edit' => 'Edit', 'delete' => array('Delete', 'Are you sure?')); ?>
<div class="grid_15 textcontent">
<?php echo $helper->ui()->blockStart('Item list example', 'exclamation.gif', 'fixed', 'nopadding'); ?>
<table width="100%" cellpadding="0" cellspacing="0" class="box-table-a list">
	<thead>
		<tr>
			<th width="34" scope="col"><input type="checkbox" name="allbox" class="allbox" /></th>
			<th width="136" scope="col">Name</th>
			<th width="102" scope="col">Username</th>
			<th width="109" scope="col">Date</th>
			<th width="129" scope="col">Location</th>
			<th width="171" scope="col">E-mail</th>
			<th width="123" scope="col">Phone</th>
			<th width="90" scope="col">Actions</th>
		</tr>
	</thead>
	<tbody>
		<tr>
			<td width="34"><input type="checkbox" name="checkbox" id="checkbox" /></td>
			<td>Stephen C. Cox</td>
			<td>stephen</td>
			<td>20.06.2009</td>
			<td>Los Angeles, CA</td>
			<td>address1@yahoo.com</td>
			<td>332-5447879</td>
			<td width="90"><?php echo $helper->ui()->controls(1, '/cp/item', $actions); ?></td>
		</tr>
		<tr>
			<td width="34"><input type="checkbox" name="checkbox2" id="checkbox2" /></td>
			<td>Josephin Tan</td>
			<td>josephin</td>
			<td>20.06.2009</td>
			<td>Los Angeles, CA</td>
			<td>address1@yahoo.com</td>
			<td>332-5447879</td>
			<td width="90"><?php echo $helper->ui()->controls(2, '/cp/item', $actions); ?></td>
		</tr>
		<tr>
			<td width="34"><input type="checkbox" name="checkbox3" id="checkbox3" /></td>
			<td>Joyce Ming</td>
			<td>joyce_m</td>
			<td>20.06.2009</td>
			<td>Los Angeles, CA</td>
			<td>address1@yahoo.com</td>
			<td>332-5447879</td>
			<td width="90"><?php echo $helper->ui()->controls(3, '/cp/item', $actions); ?></td>
		</tr>
		<tr>
			<td width="34"><input type="checkbox" name="checkbox4" id="checkbox4" /></td>
			<td>James A. Pentel</td>
			<td>james_pent</td>
			<td>20.06.2009</td>
			<td>Los Angeles, CA</td>
			<td>address1@yahoo.com</td>
			<td>332-5447879</td>
			<td width="90"><?php echo $helper->ui()->controls(4, '/cp/item', $actions); ?></td>
		</tr>
		<tr class="footer">
			<td></td>
			<td colspan="4" align="right">&nbsp;</td>
			<td colspan="3" align="right">
				<div class="pagination">
					<span class="previous-off">&laquo; Previous</span>
					<span class="active">1</span>
					<a href="?page=2">2</a>
					<a href="?page=3">3</a>
					<a href="?page=4">4</a>
					<a href="?page=5">5</a>
					<a href="?page=6">6</a>
					<a href="?page=7">7</a>
					<a href="?page=2" class="next">Next &raquo;</a>
				</div>
			</td>
		</tr>
	</tbody>
</table>
<?php echo $helper->ui()->blockEnd(); ?>
</div>