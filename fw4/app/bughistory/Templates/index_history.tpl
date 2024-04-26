{*<div>
<p style="width: 50%; display: inline-block; font-size: 30px; margin-top: 10px;">Bug History</p>

</div>*}
<div style="clear:both;"></div>
<table style="margin-top:20px;" class="moredata">
	<thead>
		<tr class="table-head">
			<th class="lang">Id</th>
			<th class="lang">Date</th>
			<th class="lang">Memo</th>
			<th class="lang">Name</th>
			<th></th>
		</tr>
	</thead>

	<tbody>
		{foreach $items as $item}
			<tr>
				<td>{$item.id}</td>
				<td>{$item.date}</button></td>
				<td>{$item.memo|escape|nl2br nofilter}</td>
				<td>{$item.name}</td>


				<td>
					<button class="ajax-link listbutton" data-class="{$class}" data-function="delete" data-id="{$item.id}" data-bugs_id={$item.bugs_id} style="float:right;color:black;margin-right:5px;"><span class="ui-icon ui-icon-trash"></span></button>

					<button class="ajax-link listbutton" data-class="{$class}" data-function="edit" data-id="{$item.id}" data-bugs_id={$item.bugs_id} style="float:right;color:black;"><span class="ui-icon ui-icon-pencil"></span></button>
				</td>
			</tr>
		{/foreach}
	</tbody>
</table>


