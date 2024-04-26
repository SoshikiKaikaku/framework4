
<div style="clear:both;"></div>
<table style="margin-top:20px;" class="moredata">

	<tr class="table-head">
		<th colspan="4">Parameters</th>
	</tr>


	<tbody class="sort_parameters">

		{foreach $parameters as $item}
			{assign var="f" value=$item.fields}
			<tr id="{$item.id}">

				<td><div class="col col_handle"><span class="material-symbols-outlined handle">swap_vert</span></div></td>

				<td>
				    {if $item.para_type==0}
					<p><span class="pt">Title:</span><span class="pv">{$f.parameter_title}</span><span class="pt">Name:</span><span class="pv">{$f.parameter_name}</span></p>
					<p><span class="pt">Validation:</span><span class="pv">{$validation_opt[$f.validation]}</span><span class="pt">Type:</span><span class="pv">{$f.type}</span><span class="pt">Default:</span><span class="pv">{$f.default_value}</span></p>
				    {/if}
				    {if $item.para_type==1}
					{$item.text|escape|nl2br nofilter}
				    {/if}
				    {if $item.para_type==2}
					{$item.sub_tb_name}
				    {/if}
				</td>
				<td>{$f.options|escape|nl2br nofilter}</td>
				<td>
					<button class="ajax-link listbutton" data-class="{$class}" data-function="delete_parameters" data-id="{$item.id}" style="float:right;color:black;margin-right:5px;"><span class="ui-icon ui-icon-trash"></span></button>
					{if $data.handling == 6}
					<button class="ajax-link listbutton" data-class="{$class}" data-function="edit_parameters" data-id="{$item.id}" style="float:right;color:black;margin-right:5px;"><span class="ui-icon ui-icon-pencil"></span></button>
					{/if}

				</td>
			</tr>
		{/foreach}
	</tbody>
</table>
<div style="float:right;margin-bottom: 8px;">
	<button class="ajax-link lang" data-class="{$class}" data-function="add_parameters" data-id="{$data.id}">Add Parameters</button>
</div>