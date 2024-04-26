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
	{if $sort_enabled}
		{include file="samples/_list_edit.tpl" table_settings=$table_settings enable_delete=$enable_delete enable_edit=$enable_edit}
	{else}
		{include file="samples/_list_default.tpl" table_settings=$table_settings enable_delete=$enable_delete enable_edit=$enable_edit}
	{/if}

	[CURLY_OPEN]if $is_last == false}
	<div class="ajax-auto" data-form="{$class_name}_{$db_name}_search_form" data-class="[CURLY_OPEN]$class}" data-function="page" data-max="[CURLY_OPEN]$max}"><div>
			[CURLY_OPEN]/if}
		{/if}
