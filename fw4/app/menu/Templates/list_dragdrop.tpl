

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
			{assign item_setting $parent_item_setting}
			{assign values $row}
			{foreach $list_items as $name}
				<div class="sort_parent_value">
					{include file="__item_viewer_list.tpl"}
				</div>
			{/foreach}
			<div class="sort_parent_value">&nbsp;</div> {* for last virtical line *}

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


			<div class="droparea" data-parent_id="{$row.id_encrypted}" data-class="{$class}" data-table_name="{$child_table_name}" data-parent_table_name="{$table_name}">

				{if $child_count_add_items > 0}
					<div class="child_add_button_area">
						<span class="ajax-link material-symbols-outlined" data-class="{$class}" data-function="add" data-table_name="{$child_table_name}" data-parent_id="{$row.id_encrypted}" data-reload_table="{$table_name}">note_add</span>
					</div>
				{/if}


				{assign item_setting $child_item_setting}
				{foreach $row["child"] as $child}
					{assign values $child}
					<div class="child_box" id="{$child.id}">
						<span class="ajax-link material-symbols-outlined" data-class="{$class}" data-function="delete" data-table_name="{$child_table_name}" data-parent_id="{$row.id_encrypted}" data-reload_table="{$table_name}" data-id_encrypted="{$child.id_encrypted}" style="padding-top:3px">delete</span>
						<span class="ajax-link material-symbols-outlined" data-class="{$class}" data-function="edit" data-table_name="{$child_table_name}" data-parent_id="{$row.id_encrypted}" data-reload_table="{$table_name}" data-id_encrypted="{$child.id_encrypted}">edit_square</span>

						{foreach $child_items as $name}
							<div class="sort_child_value">
								{include file="__item_viewer_list.tpl"}
							</div>
						{/foreach}
					</div>
				{/foreach}
			</div>
		</div>
    {/foreach}
</div>


