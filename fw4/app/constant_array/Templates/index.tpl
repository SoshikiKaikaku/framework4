<div>
	<div style="float:right;margin-bottom: 8px;">
		<button class="ajax-link lang" data-class="{$class}" data-function="add">Add Constant Array</button>
	</div>
</div>

<div style="clear:both;"></div>

<div>
	<form id="dx_constant_array_search_form" class="search-form">

		<div style="display:flex; flex-wrap:wrap;">


			<div style="width:25%; padding-right: 10px;">
				<p class="lang">Array Name</p>
				<input type="text" name="search_name" value="{$post.search_name}"
					   style="width:100%;">
			</div>


			<div style="width:15%;">
				<P style="visibility: hidden;"></p>
				<input data-class="{$class}" data-function="page" data-form="dx_constant_array_search_form"
					   class="ajax-link search-btn lang" type="button" value="Search">
			</div>
		</div>
	</form>
</div>

<table style="margin-top:20px;" class="moredata">
	<thead>
		<tr class="table-head">
			<th class="lang" style="width: 20%;">Array Name</th>

			<th style="width: 20%;"></th>
		</tr>
	</thead>

	<tbody>
		{foreach $items as $item}
			<tr>

				<td>{$item.array_name}</td>

				<td>

					<button class="ajax-link listbutton" data-class="{$class}" data-function="delete" data-id="{$item.id}" style="float:right;color:black;margin-right:5px;"><span class="ui-icon ui-icon-trash"></span></button>
					<button class="ajax-link listbutton" data-class="{$class}" data-function="edit" data-id="{$item.id}" style="float:right;color:black;"><span class="ui-icon ui-icon-pencil"></span></button>
				</td>
			</tr>
		{/foreach}
	</tbody>
</table>



