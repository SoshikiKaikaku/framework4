<div>
	<form id="email_format_email_format_search_form" class="search-form">
		<div style="display:flex; flex-wrap:wrap;">

			<div style="width:25%; padding-right: 10px;">
				<p class="lang">Template Name</p>
				<input type="text" name="search_template_name" value="{$MYSESSION.search_template_name_em_tmp}" style="width:100%;">
			</div>

			<div style="width:15%;">
				<P style="visibility: hidden;"></p>
				<input data-class="{$class}" data-function="page" data-form="email_format_email_format_search_form" class="ajax-link search-btn lang" type="button" value="Search">
			</div>
			<div style="width:15%; margin-left:10px;">
				<P style="visibility: hidden;"></p>
				<input data-class="{$class}" data-function="page" data-button="reset" class="ajax-link search-btn lang " type="button" value="Reset">
			</div>
		</div>
	</form>
</div>
<div style="margin-top: 10px;">
    <button class="ajax-link lang" data-class="{$class}" data-function="add" style="margin-top: 0px;">Add Email Templates</button>
    <button class="ajax-link lang" data-class="{$class}" data-function="json_upload" style="margin-top: 0px;">JSON Upload</button>
    <button class="download-link lang" data-filename="email_template.json" data-class="{$class}" data-function="json_download" style="margin-top: 0px;">JSON Download</button>    
</div>
<table style="margin-top:20px;" class="moredata">
	<thead>
		<tr class="table-head" style="background-color: #FFF;color: black;border-top: none;">
			<th class="lang">Template Name</th>
			<th class="lang">Key</th>
			<th class="lang">Subject</th>

			<th></th>
		</tr>
	</thead>
	<tbody>
		{foreach $items as $item}
			<tr>

				<td>{$item.template_name}</td>
				<td>{$item.key}</td>
				<td>{$item.subject}</td>

				<td>

					<button class="ajax-link listbutton" data-class="{$class}" data-function="delete" data-id="{$item.id}" style="float:right;color:black;margin-right:5px;"><span class="ui-icon ui-icon-trash"></span></button>

					<button class="ajax-link listbutton" data-class="{$class}" data-function="edit" data-id="{$item.id}" style="float:right;color:black;"><span class="ui-icon ui-icon-pencil"></span></button>
				</td>
			</tr>
		{/foreach}
	</tbody>
</table>

{if $is_last == false}
	<div class="ajax-auto" data-form="email_format_email_format_search_form" data-class="{$class}" data-function="page" data-max="{$max}"><div>
		{/if}
