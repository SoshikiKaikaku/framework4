<table style="margin-top:20px;" class="moredata">
	<thead>
		<tr class="table-head">
			<th class="lang" style="width: 60px;">Id</th>
			<th class="lang" style="width: 140px;">Status</th>
			<th class="lang" style="width: 140px;">Priority</th>
			<th class="lang" style="width: 140px;">Image</th>
			<th class="lang">Detail</th>
			<th></th>
		</tr>
	</thead>

	<tbody>
		{foreach $items as $item}
			<tr>
				<td style="white-space:nowrap;">{$item.id}</td>

				<td><button class="status status{$item.status} lang">{$status[$item.status]}</button></td>
				<td><button class="priority priority{$item.priority} lang">{$priority[$item.priority]}</button></td>
				<td>

					{if $item.image}
						{if $item.thumb_image}
							<div class="listimg"> <a href="app.php?class={$class}&function=view_image&file={$item.image}&{$timestamp}"  target="_blank"><img src="app.php?class={$class}&function=view_image&file={$item.thumb_image}" style="width: 100%;height: 100%;"/></a></div>
								{else}
							<div class="listimg"> <a href="app.php?class={$class}&function=view_image&file={$item.image}&{$timestamp}"  target="_blank"><img src="app.php?class={$class}&function=view_image&file={$item.image}" style="width: 100%;height: 100%;"/></a></div>
								{/if}
							{/if}
				</td>
				<td style="position: relative;height: 200px;width:100%;"><div style="overflow-y: auto;position: absolute;top: 5px;right: 0;bottom: 5px;left: 0;padding: 5px;">
						{if $item.desk_japanees != ""}
							<p>{$item.desk_japanees|escape|nl2br nofilter}</p><br>
						{/if}
						{$item.desk_english|escape|nl2br nofilter}</div>
				</td>


				<td>
					<button class="ajax-link listbutton" data-class="{$class}" data-function="delete" data-id="{$item.id}" style="float:right;color:black;margin-right:5px;"><span class="ui-icon ui-icon-trash"></span></button>

					<button class="ajax-link listbutton" data-class="{$class}" data-function="edit" data-id="{$item.id}" data-form="bugmanage_bugs_search_form" style="float:right;color:black;"><span class="ui-icon ui-icon-pencil"></span></button>
				</td>
			</tr>
		{/foreach}
	</tbody>
</table>

{if $is_last == false}
	<div class="ajax-auto" data-form="bugmanage_bugs_search_form" data-class="{$class}" data-function="list" data-max="{$max}">aaaaaa<div>
		{/if}





