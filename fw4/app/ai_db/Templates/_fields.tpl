
<div style="clear:both;"></div>
<table style="margin-top:20px;" class="moredata">
	<thead>
		<tr class="table-head">
			<th></th>
			<th class="lang">Parameter Name</th>
			<th class="lang">Parameter Title</th>
			<th class="lang">Type</th>
			<th class="lang">Length</th>
			<th class="lang">Options</th>
			<th></th>
		</tr>
	</thead>

	<tbody class="sort_parameters">
		{foreach $parameters as $item}
			<tr id="{$item.id}">

				<td><div class="col col_handle"><span class="material-symbols-outlined handle">swap_vert</span></div></td>
				<td>{$item.parameter_name}</td>
				<td>{$item.parameter_title}</td>
				<td>{$item.type}</td>
				<td>{$item.length}</td>
				<td>{$item.constant_array_name}</td>
				<td>
				    {if $item.parameter_name != 'parent_field'}
					<button class="ajax-link listbutton" data-class="{$class}" data-function="delete_fiedls" data-id="{$item.id}" style="float:right;color:black;margin-right:5px;"><span class="ui-icon ui-icon-trash"></span></button>

					<button class="ajax-link listbutton" data-class="{$class}" data-function="edit_fiedls" data-id="{$item.id}" style="float:right;color:black;"><span class="ui-icon ui-icon-pencil"></span></button>
				    {/if}
				</td>
			</tr>
		{/foreach}
	</tbody>
</table>
<div style="float:right;margin-bottom: 8px;">
	<button class="ajax-link lang" data-class="{$class}" data-function="add_fields" data-id="{$data.id}">Add Parameters</button>
</div>