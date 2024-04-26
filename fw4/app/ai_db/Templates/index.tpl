<div>

	<div style="float:right;margin-bottom: 8px;">
		<button class="ajax-link lang" data-class="{$class}" data-function="add">Add Table</button>
	</div>
</div>
<div style="clear:both;"></div>

<table style="margin-top:10px;" class="moredata">
	<thead>
		<tr class="table-head">
			<th></th>
			<th class="lang">Table Name</th>
			<th class="lang">Parent Name</th>
			<th class="lang">Description</th>
			<th></th>
		</tr>
	</thead>

	<tbody class="sort">
		{foreach $items as $item}
			<tr id="{$item.id}" class="dragable-item">
				<td><div class="col col_handle"><span class="material-symbols-outlined handle">swap_vert</span></div></td>

				<td>{$item.tb_name}</td>
				<td>{$parents_opt[$item.parent_tb_id]}</td>
				<td>{$item.description}</td>
				<td>
					<button class="ajax-link listbutton" data-class="{$class}" data-function="delete" data-id="{$item.id}" style="float:right;color:black;margin-right:5px;"><span class="ui-icon ui-icon-trash"></span></button>

					<button class="ajax-link listbutton" data-class="{$class}" data-function="edit" data-id="{$item.id}" style="float:right;color:black;"><span class="ui-icon ui-icon-pencil"></span></button>
				</td>
			</tr>
		{/foreach}
	</tbody>
</table>

