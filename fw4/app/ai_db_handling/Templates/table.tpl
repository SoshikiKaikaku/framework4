
<table>
	<thead>
		<tr>
			{foreach $fields as $field}
				<th>{$field.parameter_title}</th>
				{/foreach}
			<th></th>
		</tr>
	</thead>

	<tbody>
		{foreach $list as $row}
			<tr>
				{foreach $fields as $field}
					<td>
						{include file="{$base_template_dir}/__item_viewer.tpl"}			
					</td>
				{/foreach}
				<td>
				    {if $ai_setting_delete}
				    	<button class="ajax-link listbutton" data-class="{$class}" data-function="delete" data-id="{$row.id}" data-ai_setting_id="{$data.ai_setting_id}" style="float:right;color:black;margin-right:5px;"><span class="ui-icon ui-icon-trash"></span></button>
				    {/if}
				    {if $ai_setting_edit}
					<button class="ajax-link listbutton" data-class="{$class}" data-function="edit" data-id="{$row.id}" data-ai_setting_id="{$data.ai_setting_id}" style="float:right;color:black;"><span class="ui-icon ui-icon-pencil"></span></button>
				    {/if}
				</td>
			</tr>
		{/foreach}
	</tbody>
</table>

