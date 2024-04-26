{*<div style="display: flex;">
<div style="width: 30%;">
<button class="download-link" data-class="lang" data-function="csv_download" data-filename="lang-app-{$appcode}.csv" style="float: left;">Download</button>
</div>
<hr style="margin-right: 40px;">
<div style="display: flex; width: 55%;">
<form id="csvform">
<input type="file" name="csvfile">
</form>
<button class="ajax-link" data-class="lang" data-function="csv_upload" data-form="csvform">Upload</button>
<p>{$csvresult}</p>
</div>
<div style="width: 30%;">

</div>
</div>*}
<button class="ajax-link" data-class="lang" data-function="all_clear" style="float: right;">Clear All</button>
<button class="ajax-link" data-class="lang" data-function="blank_clear" style="float: right;">Clear Blank</button>
<div style="margin-top: 10px;">
	<form id="lang_search_form" style="width: 100%; display: flex; ">
		<div style="width: 50%;">
			<input type="text" name="lang_search" value="{$MYSESSION.lang_search}">
		</div>
		<div>
			<button class="ajax-link" data-class="lang" data-function="edit" data-form="lang_search_form" style="float: left;">Search</button>
		</div>
	</form>
</div>

<div style="clear:both; border-bottom:1px #ccc solid; width:100%; padding-top:20px;"></div>

<table>
	<tr>
		<th style="width:30%;text-align: left;">Class</th>
		<th style="width:30%;text-align: left;">English</th>
		<th style="width:30%;text-align: left;">Japanese</th>
		<th style="width:10%;"></th>
	</tr>

	{foreach $list as $d}
		<tr>
			<td>{$d.classname}</td>
			<td>{$d.en}</td>
			<td><input type="text" class="lang_change" data-id="{$d.id}" value="{$d.jp}"></td>
			<td><span class="ajax-link" data-class="lang" data-function="delete" data-id="{$d.id}" style="cursor: pointer"><span class="ui-icon ui-icon-trash"></span></span></td>
		</tr>
	{/foreach}

</table>

<script>
	$(".lang_change").on("change", function () {
		var val = $(this).val();
		var url = "app.php";
		var fd = new FormData();
		fd.append("class", "lang");
		fd.append("function", "edit_exe");
		fd.append("id", $(this).data("id"));
		fd.append("jp", val);
		appcon(url, fd, function (e) {
			get_lang_list();
		});
	});

</script>