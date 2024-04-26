
<input type="file" name="" id="" style="display: none;">
<button class="ajax-link" data-class="{$class}" data-function="table_designer_1" style="margin: 0 0 10px 0;">Add New</button>


<hr style="clear:both; margin-bottom:10px;">

<div>
	<table>
		{foreach $dblist as $class => $dbs}
			{foreach $dbs as $db}
				<tr>
					<td style="width: 50%;">
						{* table desinger *}
						<button class="ajax-link listbutton" data-class="dbadmin" data-function="table_designer_2" data-dbclass="{$class}" data-db="{$db}" style="color:black; float: left; margin: 4px 8px 0 0; border: solid 1px; display:none;"><span class="ui-icon ui-icon-pencil"></span></button>

						<p style="font-size: 20px;">{$class}\{$db}</p>
					</td>

					{if !$dblist_child[$class][$db]}
						<td>
							<button class="ajax-link" data-class="dbadmin" data-function="code_generator_step_one" style="margin-top:0;" data-dbclass="{$class}" data-db="{$db}">
								Code Generator
							</button>
						</td>
					{else}
						<td></td>
					{/if}

					<td>
						<button class="ajax-link" data-class="dbadmin" data-function="select" data-dbclass="{$class}" data-db="{$db}" style="cursor: pointer; margin-top:0px;">
							View Data
						</button>
					</td>
				</tr>
			{/foreach}
		{/foreach}
	</table>
</div>
