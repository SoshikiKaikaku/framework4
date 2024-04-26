<form id="dx_constant_array_edit_form_{$data.id}">

	<input type="hidden" name="id" value="{$data.id}">

	<div style="">
		<div style="padding:5px;">
			<p class="lang">Array Name(This name should end with "_opt")</p>
			<input type="text" name="array_name" value="{$data.array_name}">
			<p class="error lang">{$errors['array_name']}</p>
		</div>
	</div>


	<div>
		<button class="ajax-link lang" data-form="dx_constant_array_edit_form_{$data.id}" data-class="{$class}"
				data-function="edit_exe">Update</button>
	</div>

</form>
{* interactive values section *}
<div>
	<div class="flex-between" style="width:100%;">
		<div class="" style=""><h6 class="lang">Values</h6></div>

	</div>
</div>

<table style="margin-top:20px;" class="moredata">
	<tr class="table-head">
		<th class="lang" style="width: 13%;">Key</th>
		<th class="lang" style="width: 13%;">Value</th>
		<th class="lang" style="width: 13%;">Color</th>
		<th style="width: 13%;"></th>
	</tr>

	{foreach $values as $key => $note}
		<tr style="background-color:{$row_color[$key]};">
			<td>{$note.key}</td>
			<td>{$note.value}</td>
			<td><span class="square" style="background-color:{$note.color}"></span><span style="vertical-align: middle;">{$note.color}</span></td>
			<td>
				{* delete button *}
				<button class="ajax-link listbutton" data-class="{$class}" data-function="delete_values" data-id="{$note.id}" data-constant_array_id="{$note.constant_array_id}" style="float:right;color:black;margin-right:5px;"><span class="ui-icon ui-icon-trash"></span></button>

				{* edit button *}
				<button class="ajax-link listbutton" data-class="{$class}" data-function="edit_values" data-id="{$note.id}" data-constant_array_id="{$note.constant_array_id}" style="float:right;color:black;"><span class="ui-icon ui-icon-pencil"></span></button>

			</td>
		</tr>
	{/foreach}

</table>

<div class="" style="">
	<button data-class="{$class}" data-function="add_values" data-constant_array_id="{$data.id}" class="ajax-link lang">Add Values</button>
</div>