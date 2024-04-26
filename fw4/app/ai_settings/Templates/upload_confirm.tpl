
<div>
	<table>
		<tr>
			<td class="status"></td>
			<td class="lang">Title</td>
			<td class="lang">Text</td>
		</tr>
		{foreach $list as $row}
			<tr>
				{if count($row.errors) == 0}
					<td>OK</td>
				{else}
					<td>
						{foreach $row.errors as $e}
							<p class="error">{$e}</p>
						{/foreach}
					</td>
				{/if}
				<td>{$row.title}</td>
				<td>{$row.text}</td>
			</tr>
		{/foreach}

	</table>
	
</div>


<div>
	{if $next_flg}
	<button class="ajax-link lang" data-class="{$class}" data-function="upload_csv_exe" data-upload_option="{$data.upload}">Add</button>
	{else}
		<p class="error">There are errors, please fix it first.</p>
	{/if}
</div>


