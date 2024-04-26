{if $enable_add || $is_child}
	<div>
		{if $is_child}
			<p style="width: 50%; display: inline-block; font-size: 30px; margin-top: 10px;">{$db_name_str}</p>
		{/if}

		{if $enable_add}
			<div style="float:right;margin-bottom: 8px;">
				{if $is_child}
					<button class="ajax-link lang" data-class="[CURLY_OPEN]$class}" data-function="add" data-{$parent_db}_id="[CURLY_OPEN]$post.{$parent_db}_id}">Add {$db_name_str}</button>
				{else}
					<button class="ajax-link lang" data-class="[CURLY_OPEN]$class}" data-function="add">Add {$db_name_str}</button>
				{/if}
			</div>
		{/if}
	</div>

	<div style="clear:both;"></div>
{/if}

{if $enable_search}
	<div>
		<form id="{$class_name}_{$db_name}_search_form" class="search-form">
			{if $is_child}
				<input type="hidden" name="{$parent_db}_id" value="[CURLY_OPEN]$post.{$parent_db}_id}">
			{/if}

			<div style="display:flex; flex-wrap:wrap;">

				{foreach $table_settings as $element}
					{if !$element['search_flg']}
						{continue}
					{/if}

					<div style="width:25%; padding-right: 10px;">
						<p class="lang">{ucwords(str_replace("_", " ", $element['field_name']))}</p>
						{if $element['field_type'] == 'select'}
							[CURLY_OPEN]html_options name="search_{$element['field_name']}" options=${$element['field_name']}_options selected=$post.search_{$element['field_name']}  style="width:100%;"}
						{elseif ($element['field_type'] == 'checkbox' || $element['field_type'] == 'radio')}
							[CURLY_OPEN]html_options name="search_{$element['field_name']}" options=array_merge([""], ${$element['field_name']}_options) selected=$post.search_{$element['field_name']}  style="width:100%;"}
							{* {elseif $element['field_type'] == 'radio'}
							[CURLY_OPEN]html_radios name="search_{$element['field_name']}" options=array_merge(["Any"], ${$element['field_name']}_options) selected=$post.search_{$element['field_name']}  style="width:100%;"} *}
						{elseif $element['field_type'] == 'date'}
							<input type="text" name="search_{$element['field_name']}" value="[CURLY_OPEN]$post.search_{$element['field_name']}}" class="datepicker" style="width:100%;">
						{else}
							<input type="text" name="search_{$element['field_name']}" value="[CURLY_OPEN]$post.search_{$element['field_name']}}" style="width:100%;">
						{/if}
					</div>
				{/foreach}

				<div style="width:15%;">
					<P style="visibility: hidden;"></p>
					<input data-class="[CURLY_OPEN]$class}" data-function="page" data-form="{$class_name}_{$db_name}_search_form" class="ajax-link search-btn lang" type="button" value="Search">
				</div>
			</div>
		</form>
	</div>
{/if}

{if $enable_list}
	<div class="sort-parent">
		[CURLY_OPEN]foreach $items as $item}
		<div class="child" id="[CURLY_OPEN]$item.id}" style="background: white;">

			{* parent content *}
			<div class="codegen-combine-parents">
				{foreach $table_settings as $element}
					{if !$element['list_flg']}
						{continue}
					{/if}

					<div>
						{if $element['field_type'] == 'file'}
							[CURLY_OPEN]assign var="time" value=(explode("-", $item.{$element['field_name']}))}
							[CURLY_OPEN]assign var="filename" value=([CURLY_OPEN]trim(str_replace($time[0], "", str_replace("-", " ", $item.{$element['field_name']})))})}
							<a class="download-link" data-class="[CURLY_OPEN]$class}" data-function="download" data-file="[CURLY_OPEN]$item.{$element['field_name']}}" style="cursor: pointer;" data-filename="[CURLY_OPEN]$filename}">[CURLY_OPEN]$filename}</a>
						{elseif ($element['field_type'] == 'select' || $element['field_type'] == 'radio')}
							[CURLY_OPEN]${$element['field_name']}_options[$item.{$element['field_name']}]}
						{elseif $element['field_type'] == 'checkbox'}
							[CURLY_OPEN]assign var="checkbox_arr" value=explode(", ", $item.{$element['field_name']})}
							<ul>
								[CURLY_OPEN]foreach $checkbox_arr as $check_item}
								<li style="list-style-type:disc">[CURLY_OPEN]${$element['field_name']}_options[$check_item]}</li>
								[CURLY_OPEN]/foreach}
							</ul>
						{else}
							[CURLY_OPEN]$item.{$element['field_name']}}
						{/if}
					</div>
				{/foreach}
				<div>
					{* delete button *}
					{if $enable_delete}
						<button class="ajax-link listbutton" data-class="[CURLY_OPEN]$class}" data-function="delete" data-id="[CURLY_OPEN]$item.id}" style="float:right;color:black;margin-right:5px;"><span class="ui-icon ui-icon-trash"></span></button>
						{/if}

					{* edit button *}
					{if $enable_edit}
						<button class="ajax-link listbutton" data-class="[CURLY_OPEN]$class}" data-function="edit" data-id="[CURLY_OPEN]$item.id}" style="float:right;color:black;"><span class="ui-icon ui-icon-pencil"></span></button>
						{/if}

					{* add child button *}
					{* {if $enable_child_add} *}
					<button class="ajax-link listbutton" data-class="{$child_class_name}" data-function="add" data-{$db_name}_id="[CURLY_OPEN]$item.id}" style="float:right;color:black;"><span class="ui-icon ui-icon-plusthick"></span></button>
						{* {/if} *}
				</div>
			</div>

			<hr>

			{* relevant childrens *}
			<div class="codegen-combine-childs sort-child" data-parent_id="[CURLY_OPEN]$item.id}">

				[CURLY_OPEN]foreach $child_items[$item.id] as $child_item}
				<div class="codegen-combine-child child" id="[CURLY_OPEN]$child_item.id}">
					<P>
						{foreach $child_table_settings as $child_element}
							{if !$child_element['list_flg']}
								{continue}
							{/if}

						<div>
							{if $child_element['field_type'] == 'file'}
								[CURLY_OPEN]assign var="time" value=(explode("-", $child_item.{$child_element['field_name']}))}
								[CURLY_OPEN]assign var="filename" value=([CURLY_OPEN]trim(str_replace($time[0], "", str_replace("-", " ", $child_item.{$child_element['field_name']})))})}
								<a class="download-link" data-class="[CURLY_OPEN]$class}" data-function="download" data-file="[CURLY_OPEN]$child_item.{$child_element['field_name']}}" style="cursor: pointer;" data-filename="[CURLY_OPEN]$filename}">[CURLY_OPEN]$filename}</a>
							{elseif ($child_element['field_type'] == 'select' || $child_element['field_type'] == 'radio')}
								[CURLY_OPEN]$child_{$child_element['field_name']}_options[$child_item.{$child_element['field_name']}]}
							{elseif $child_element['field_type'] == 'checkbox'}
								[CURLY_OPEN]assign var="checkbox_arr" value=explode(", ", $child_item.{$child_element['field_name']})}
								<ul>
									[CURLY_OPEN]foreach $checkbox_arr as $check_item}
									<li style="list-style-type:disc">[CURLY_OPEN]$child_{$child_element['field_name']}_options[$check_item]}</li>
									[CURLY_OPEN]/foreach}
								</ul>
							{else}
								[CURLY_OPEN]$child_item.{$child_element['field_name']}}
							{/if}
						</div>
					{/foreach}
					</P>
					<div class="codegen-combine-child-action">
						<button class="ajax-link listbutton" data-class="{$child_class_name}" data-function="edit" data-id="[CURLY_OPEN]$child_item.id}" style="float:right;color:black;"><span class="ui-icon ui-icon-pencil"></span></button>

						<br>

						<button class="ajax-link listbutton" data-class="{$child_class_name}" data-function="delete" data-id="[CURLY_OPEN]$child_item.id}" style="float:right;color:black;margin-right:5px;"><span class="ui-icon ui-icon-trash"></span></button>
					</div>
				</div>
				[CURLY_OPEN]/foreach}

			</div>
		</div>
		[CURLY_OPEN]/foreach}
	</div>

	[CURLY_OPEN]if $is_last == false}
	<div class="ajax-auto" data-form="{$class_name}_{$db_name}_search_form" data-class="[CURLY_OPEN]$class}" data-function="page" data-max="[CURLY_OPEN]$max}"><div>
			[CURLY_OPEN]/if}
		{/if}

		<script>
			$(".sort-parent").sortable({
				update: function () {
					var log = $(this).sortable("toArray");
					var fd = new FormData();
					fd.append('class', "[CURLY_OPEN]$class}");
					fd.append('function', 'sort');
					fd.append('log', log);
					appcon('app.php', fd);
				}
			}).disableSelection();

			$(".sort-child").sortable({
				connectWith: ".sort-child",
				update: function () {
					// var parent_id = $(this).closest('.sort-child').data('parent_id');
					var parent_id = $(this).data("parent_id");
					var log = $(this).sortable("toArray");
					var fd = new FormData();
					fd.append('class', "{$child_class_name}");
					fd.append('function', 'sort');
					fd.append('log', log);
					if (parent_id != undefined)
						fd.append('parent_id', parent_id);
					appcon('app.php', fd);
				}
			}).disableSelection();
		</script>