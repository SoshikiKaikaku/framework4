<table style="margin-top:20px;" class="moredata">
	<thead>
		<tr class="table-head">
			{foreach $table_settings as $element}
				{if !$element['list_flg']}
					{continue}
				{/if}

				<th class="lang">{ucwords(str_replace("_", " ", $element['field_name']))}</th>
				{/foreach}
			<th></th>
		</tr>
	</thead>

	<tbody>
		[CURLY_OPEN]foreach $items as $item}
		<tr>
			{foreach $table_settings as $element}
				{if !$element['list_flg']}
					{continue}
				{/if}

				{if $element['field_type'] == 'file'}
					<td>
						[CURLY_OPEN]assign var="time" value=(explode("-", $item.{$element['field_name']}))}
						[CURLY_OPEN]assign var="filename" value=([CURLY_OPEN]trim(str_replace($time[0], "", str_replace("-", " ", $item.{$element['field_name']})))})}
						<a class="download-link" data-class="[CURLY_OPEN]$class}" data-function="download" data-file="[CURLY_OPEN]$item.{$element['field_name']}}" style="cursor: pointer;" data-filename="[CURLY_OPEN]$filename}">[CURLY_OPEN]$filename}</a>
					</td>
				{elseif ($element['field_type'] == 'select' || $element['field_type'] == 'radio')}
					<td>[CURLY_OPEN]${$element['field_name']}_options[$item.{$element['field_name']}]}</td>
				{elseif $element['field_type'] == 'checkbox'}
					[CURLY_OPEN]assign var="checkbox_arr" value=explode(", ", $item.{$element['field_name']})}
					<td>
						<ul>
							[CURLY_OPEN]foreach $checkbox_arr as $check_item}
							<li style="list-style-type:disc">[CURLY_OPEN]${$element['field_name']}_options[$check_item]}</li>
							[CURLY_OPEN]/foreach}
						</ul>
					</td>
				{else}
					<td>[CURLY_OPEN]$item.{$element['field_name']}}</td>
				{/if}
			{/foreach}
			<td>
				{if $enable_delete}
					{* delete button *}
					<button class="ajax-link listbutton" data-class="[CURLY_OPEN]$class}" data-function="delete" data-id="[CURLY_OPEN]$item.id}" style="float:right;color:black;margin-right:5px;"><span class="ui-icon ui-icon-trash"></span></button>
					{/if}

				{if $enable_edit}
					{* edit button *}
					<button class="ajax-link listbutton" data-class="[CURLY_OPEN]$class}" data-function="edit" data-id="[CURLY_OPEN]$item.id}" style="float:right;color:black;"><span class="ui-icon ui-icon-pencil"></span></button>
					{/if}
			</td>
		</tr>
		[CURLY_OPEN]/foreach}
	</tbody>
</table>
