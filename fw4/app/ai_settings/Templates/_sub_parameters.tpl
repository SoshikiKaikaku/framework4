
<div style="clear:both;"></div>
<table style="margin-top:20px;" class="moredata">

	<tr class="table-head">
		<th>Parameters</th>
		<th>Title</th>
		<th>Width</th>
		<th>Align</th>
		<th></th>
	</tr>


	<tbody class="sort_parameters">

		{foreach $subtb_details as $item}
			<tr id="{$item.id}">

				<td><div class="col col_handle"><span class="material-symbols-outlined handle">swap_vert</span></div></td>

				<td>{$item.parameter_title}</td>
				<td>
				    <form class="ai_sub_para_form">
					<input type="hidden" name="id" value="{$item.id}">
					<input type="text" name="width" value="{$item.width}">
				    </form>
				</td>
				<td>
				    <form class="ai_sub_para_form">
					<input type="hidden" name="id" value="{$item.id}">
					{html_options name="align" selected=$item.align options=$align_opt}
				    </form>
				    
				</td>
				<td>
					<button class="ajax-link listbutton" data-class="{$class}" data-function="delete_sub_parameters" data-id="{$item.id}" style="float:right;color:black;margin-right:5px;"><span class="ui-icon ui-icon-trash"></span></button>

				</td>
			</tr>
		{/foreach}
	</tbody>
</table>
<form id="ai_subtable_add_form" class="flex">
    <div style="width:50%;">
			<p class="lang">Field:</p>
			{html_options name="ai_sub_field_id" selected=$data.ai_sub_field_id options=$ai_sub_fields_opt}
		    </div>
		    <div>
			<button class="ajax-link lang" data-form="ai_subtable_add_form" data-class="{$class}" data-function="add_subtable" data-ai_settings_parameter_id="{$data.id}">Add Field</button>
		    </div>
</form>