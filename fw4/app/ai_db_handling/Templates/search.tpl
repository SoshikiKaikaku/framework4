<div style="margin-bottom: 15px;">
    <form id="form_search_posts" class="search-form flex">
	{foreach $fields as $field}
	    <div style="margin-right: 10px;">
			{include file="{$base_template_dir}/__item_search.tpl"}	
		</div>
	{/foreach} 
		    <div style="margin-top: auto;">
			    <P style="visibility: hidden;"></p>
			    <input data-class="{$class}" data-function="search_exe" data-form="form_search_posts" class="ajax-link search-btn lang" type="button"  value="Search"  data-ai_setting_id="{$data.ai_setting_id}" style="margin-top: 0;">
		    </div>
    </form>
</div>	
			
