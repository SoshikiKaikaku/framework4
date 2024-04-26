

{** add button **}
{if $count_add_items > 0}
    <div class="add_button_area">
		<button class="ajax-link" data-class="{$class}" data-function="add" data-table_name="{$table_name}" data-parent_id="{$parent_id}">Add</button>
    </div>
{/if}


{** data **}
<div class="sortable flex_dragdrop" data-class="{$class}" data-table_name="{$table_name}">
	{foreach $list as $row}
		<div class="sort" id="{$row.id}">
			<div class="col col_handle"><span class="material-symbols-outlined handle">swap_vert</span></div>

			{foreach $list_items as $name}
				<div class="col">
					{assign values $row}
					{include file="__item_viewer_edit_onchange.tpl"}
				</div>
			{/foreach}


			{* place buttons *}
			<div class="col" style="white-space: nowrap;text-align: right;float:right;">
				{if $count_view_items > 0 }
					<span class="material-symbols-outlined ajax-link" data-class="{$class}" data-function="view" data-table_name="{$table_name}" data-id_encrypted="{$row.id_encrypted}" data-parent_id="{$parent_id}">pageview</span>
				{/if}
				{if $count_edit_items > 0 }
					<span class="material-symbols-outlined ajax-link" data-class="{$class}" data-function="edit" data-table_name="{$table_name}" data-id_encrypted="{$row.id_encrypted}" data-parent_id="{$parent_id}">edit_square</span>
				{/if}
				{if $count_delete_items > 0 }
					<span class="material-symbols-outlined ajax-link" data-class="{$class}" data-function="delete" data-table_name="{$table_name}" data-id_encrypted="{$row.id_encrypted}" style="padding-top:2px;white-space: nowrap;" data-parent_id="{$parent_id}">delete</span>
				{/if}
			</div>
		</div>
	{/foreach}
</div>


