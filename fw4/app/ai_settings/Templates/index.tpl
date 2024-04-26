<div>

	<div style="float:right;margin-bottom: 8px;">
		<button class="ajax-link lang" data-class="{$class}" data-function="add_step1">Add Ai Setting</button>
		<button class="ajax-link lang" data-class="ai_cron" data-function="execute_all">Execute Cron</button>
		<button class="ajax-link lang" data-class="{$class}" data-function="upload_csv">Upload CSV for AI Training</button>
	</div>
</div>
<div style="clear:both;"></div>


<table style="margin-top:10px;" class="moredata">
	<thead>
		<tr class="table-head">
			<th></th>
			<th class="lang"></th>
			<th class="lang"></th>
			<th class="lang"></th>
			<th class="lang"></th>
			<th class="lang"></th>
			<th class="lang"></th>
			<th class="lang"></th>
			<th></th>
		</tr>
	</thead>

	<tbody class="sort indextable">
		{foreach $items as $item}
			<tr id="{$item.id}" class="dragable-item">
				<td><div class="col col_handle"><span class="material-symbols-outlined handle">swap_vert</span></div></td>
				
				{if $item.type == 0}
					<td>
						<p class="type_code">Code<br />{$code_type_opt[$item.code_type]}</p>						
					</td>
					<td><span>Class Name: <br /></span>{$item.class_name}<br>
					<span>Function Name: <br /></span>{$item.function_name}</td>
					<td>
						<span>Menu: </span>{$item.menu_name}<br>
						<span>Information: </span>{$item.information}<br>
						{include file="_limit_user_row.tpl"}
					</td>
				{else if $item.type == 1}
					<td>
						<p class="type_db">DB</p>
					</td>
					<td><span>Table Name: <br /></span>{$ai_db_opt[$item.ai_db_id]}<br>
					<span>Handling Type: <br /></span>{$database_handling_opt[$item.handling]}</td>
					<td>
						<span>Menu: </span>{$item.menu_name}<br>
						<span>Information: </span>{$item.information}<br>
						{include file="_limit_user_row.tpl"}
					</td>
				{else if $item.type == 2}
					<td>
						<p class="type_design">Predefined<br>Functions</p>
					</td>
					<td>
					  <span>Predefined Function:<br /></span>{$predefined_function_opt[$item.predefined_function]}
					</td>
					<td>
						{if $item.predefined_function == 1}
							{include file="_limit_user_row.tpl"}
						{else if $item.predefined_function == 2}
							<span>Price: </span>{number_format($item.price)}<br>
							{include file="_limit_user_row.tpl"}
						{else if $item.predefined_function == 3}
							<span>Title: </span>{$item.ai_title}<br>
							{include file="_limit_user_row.tpl"}
						{else if $item.predefined_function == 4}
							{include file="_limit_user_row.tpl"}
						{/if}
					</td>
				{/if}
						
				<td>
				    {if $item.type == 1}
					{if $item.work_on==1 || $item.work_on==2}
					    <a class="ajax-link lang" data-class="ui_chat" data-function="page" data-ai_setting_id="{$item.id}" data-from_ai="true">Run</a>
					{/if}
				    {/if}
				    {if $item.type == 2}
					{if $item.work_on==1 || $item.work_on==2}
					    {if $item.predefined_function==4}
					    <a class="ajax-link lang" data-class="ui_chat" data-function="creat_acc">Run</a>
					    {/if}
					    {if $item.predefined_function==1}
					    <a class="ajax-link lang" data-class="ui_chat" data-function="login_popup">Run</a>
					    {/if}
					    {if $item.predefined_function==2}
					    <a class="ajax-link lang" data-class="ui_chat" data-function="payment" data-payment_id="{$item.id}">Run</a>
					    {/if}
					{/if}
				    {/if}
				</td>
				<td>
				    {if $item.type == 1}
				    {if $item.work_on==1 || $item.work_on==3}
					<a href="app.php?class=ui_chat&public=true&function=page&ai_setting={$item.en_id}" target="_blank">UI Chat Public</a>
				
				    {/if}
				    {/if}
				    {if $item.type == 2}
					{if $item.work_on==1 || $item.work_on==2}
					    {if $item.predefined_function==4}
						<a href="app.php?class=ui_chat&public=true&function=page&pf_type=creat_acc" target="_blank">UI Chat Public</a>
					    {/if}
					    {if $item.predefined_function==1}
						<a href="app.php?class=ui_chat&public=true&function=page&pf_type=login_popup" target="_blank">UI Chat Public</a>
					    {/if}
					    {if $item.predefined_function==2}
						<a href="app.php?class=ui_chat&public=true&function=page&pf_type=payment&payment_id={$item.id}" target="_blank">UI Chat Public</a>
					    {/if}
					{/if}
				    {/if}
				</td>
				<td>
					<button class="ajax-link listbutton" data-class="{$class}" data-function="delete" data-id="{$item.id}" style="float:right;color:black;margin-right:5px;"><span class="ui-icon ui-icon-trash"></span></button>

					<button class="ajax-link listbutton" data-class="{$class}" data-function="edit" data-id="{$item.id}" style="float:right;color:black;"><span class="ui-icon ui-icon-pencil"></span></button>
				</td>
			</tr>
		{/foreach}
	</tbody>
</table>
{if $is_last == false}
	<div class="ajax-auto" data-form="ai_settings_ai_setting_search_form" data-class="{$class}" data-function="page" data-max="{$max}"></div>
		{/if}

