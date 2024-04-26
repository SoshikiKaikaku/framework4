<div class="dbadmin_database_header">
	{* <div style="display: inline-block;">
	<form id="form_search">
	<div style="display: inline-block;"><input type="text" name="search_word" value="{$search_word}" style=""></div>
	<div style="display: inline-block;"><button class="ajax-link" data-form="form_search" data-class="customer" data-function="search" style="float:none;">Search</button></div>
	</form>
	</div> *}

	<div style="display: inline-block; float:right;">
		<button class="ajax-link" data-class="{$class}" data-function="insert" data-dbclass="{$post.dbclass}" data-db="{$post.db}" style="margin-top:0;">Add Recorde</button>
	</div>
</div>

<div style="clear:both;"></div>

<form id="form_search">
	<table style="margin-top:40px;" class="moredata">
		{* table head *}
		<tr>
			{foreach $keys as $key}
				<th>{$key.0} ({$key.2})</th>
				{/foreach}
		</tr>

		{* search bar *}
		<tr>
			{foreach $keys as $key}
				{if $reload}
					{assign var="value" value=''}
				{else}
					{assign var="value" value=$post[$key.0]}	
				{/if}
				<td><input type="text" name="{$key.0}" value="{$value}" /></td>
				{/foreach}
			<td>
				<button class="ajax-link" data-class="{$class}" data-function="select" data-form="form_search" data-dbclass="{$post.dbclass}" data-db="{$post.db}" style="margin: 0; padding: 10px; min-width: max-content;">
					Search
				</button>
			</td>
		</tr>

		{foreach $data as $value}
			<tr>
				{foreach $keys as $key}
					{assign var="k" value=$key.0}
					<td>{$value.$k}</td>
				{/foreach}
				<td>
					{* delete button *}
					<button class="ajax-link listbutton" data-class="{$class}" data-function="delete" data-id="{$value['id']}"  data-dbclass="{$post.dbclass}" data-db="{$post.db}" style="float:right;color:black;margin-right:5px;"><span class="ui-icon ui-icon-trash"></span></button>

					{* edit button *}
					<button class="ajax-link listbutton" data-class="{$class}" data-function="edit" data-id="{$value['id']}" data-dbclass="{$post.dbclass}" data-db="{$post.db}" style="float:right;color:black;"><span class="ui-icon ui-icon-pencil"></span></button>
				</td>
			</tr>
		{/foreach}

	</table>
</form>

{if $is_last == false}
	<div class="ajax-auto" data-form="form_search" data-class="{$class}" data-function="select" data-dbclass="{$post.dbclass}" data-db="{$post.db}" data-max="{$max}"><div>
		{/if}


		{include file="database_style.txt"}