<!DOCTYPE html>
<html>
	<head>
		{include file="{$base_template_dir}/publicsite_header.tpl"}
	</head>

	<body>
		<article class="class_style_{$class} lang_check_area" data-classname="{$class}">

			<div class="contents">




				<div style="display: block;overflow:hidden;margin-top:20px;margin-bottom:20px;">
					<button class="ajax-link lang" data-class="{$class}" data-function="add" style="float:right;margin-top:0px;">Add Report</button>
					{html_options id="lang_selector" name="lang_selector" options=$arr_lang style="display:block;width:100px;float:right;margin-right:30px;"}
				</div>

				<div>
					<form id="bugmanage_bugs_search_form" class="search-form">
						<div style="display:flex; flex-wrap:wrap;">

							<div style="width:20%; padding-right: 10px;">
								<p class="lang">Id</p>
								<input type="text" name="search_id" value="{$post.search_id}" style="width:100%;">
							</div>

							<div style="width:20%; padding-right: 10px;">
								<p class="lang">Status</p>
								{*<input type="text" name="search_status" value="{$post.search_status}" style="width:100%;">*}
								{html_options name="search_status" selected=$post.search_status options=$status}
							</div>

							<div style="width:20%;">
								<P style="visibility: hidden;"></p>
								<input data-class="{$class}" data-function="search" data-form="bugmanage_bugs_search_form" class="ajax-link search-btn lang" type="button" value="Search">
							</div>
						</div>
					</form>
				</div>

				<div class="list_area" style="margin-bottom:40px;">

				</div>


			</div>
		</article>



		{include file="{$base_template_dir}/publicsite_footer.tpl"}
	</body>
</html>
