
<p class="lang">Delete the following data.</p>

<p class="lang" style="margin-bottom:20px;">If you perform this process, it will not be restored. Do you want to process it?</p>

<table>
	{foreach $delete_items as $name}
		<tr>
			<td>{$item_setting[$name]["title"]}</td>
			<td>{include file="__item_viewer.tpl"}</td>
		</tr>
	{/foreach}
</table>
