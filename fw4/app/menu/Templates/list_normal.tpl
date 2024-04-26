

{** add button **}
{if $count_add_items > 0}
	<div class="add_button_area">
		<button class="ajax-link" data-class="{$class}" data-function="add" data-table_name="{$table_name}" data-parent_id="{$parent_id}">Add</button>
	</div>
{/if}

{** search form **}
{if count($screen_items_search) > 0}
	<form id="form_search" class="search-form">

		<div class="search_flex">
			{assign values $search_values}
			{foreach $screen_items_search as $name}
				<div class="search-item">
					{include file="__item_viewer_search.tpl"}
				</div>
			{/foreach}
		</div>

		{**
		You can put a editable item manually here adding like the following two lines. "address" is the item name you want to add.
		{assign name "address"}
		{include file="item_viewer_search.tpl"}
		**}

		<div class="search_button_area">
			<button class="ajax-link" data-class="{$class}" data-function="search_reset" data-table_name="{$table_name}" data-parent_id="{$parent_id}">Reset</button>
			<button class="ajax-link" data-class="{$class}" data-function="search" data-table_name="{$table_name}" data-parent_id="{$parent_id}">Search</button>
		</div>
	</form>
{/if}

{** data table **}
<table class="table_list">

	{** title **}
	<tr>
		{foreach $list_items as $name}
			{if $name == "id"}
				<th style="width:80px">{$item_setting[$name]["title"]}</th>
				{else}
				<th>{$item_setting[$name]["title"]}</th>
				{/if}
			{/foreach}
		<th></th>
	</tr>

	{** data **}
	{foreach $list as $row}
		<tr class="table_list_row">

			{foreach $list_items as $name}
				<td>
					{assign values $row}
					{include file="__item_viewer_list.tpl"}
				</td>
			{/foreach}


			{* place buttons *}
			<td style="white-space: nowrap;width:100px;text-align: right;">
				{if $count_view_items > 0 }
					<span class="material-symbols-outlined ajax-link" data-class="{$class}" data-function="view" data-table_name="{$table_name}" data-id_encrypted="{$row.id_encrypted}" data-parent_id="{$parent_id}">pageview</span>
				{/if}
				{if $count_edit_items > 0 }
					<span class="material-symbols-outlined ajax-link" data-class="{$class}" data-function="edit" data-table_name="{$table_name}" data-id_encrypted="{$row.id_encrypted}" data-parent_id="{$parent_id}">edit_square</span>
				{/if}
				{if $count_delete_items > 0 }
					<span class="material-symbols-outlined ajax-link" data-class="{$class}" data-function="delete" data-table_name="{$table_name}" data-id_encrypted="{$row.id_encrypted}" style="padding-top:2px;white-space: nowrap;" data-parent_id="{$parent_id}">delete</span>
				{/if}
			</td>
		</tr>
	{/foreach}
</table>

{if $is_last == false}
	<div class="ajax-auto" data-class="{$class}" data-function="page" data-table_name="{$table_name}" data-max="{$max}" ><div>

		{/if}
