
<form id="delete_form_{$data.id}">
	
	<input type="hidden" name="id" value="{$data.id}">
	
	<span class="lang">Delete the following data</span>
	<p>
	<b>
	{foreach $del_params as $param}
	    {if $param['type'] == 'image'}
		{if $list[$param['parameter_name']]}
		<p><img src="app.php?class=ai_db&function=view_image&file={$list[$param['parameter_name']]}&{$timestamp}"></p>
		{/if}
	    {else if $param['type'] == "color"}
		<p>{$param['parameter_title']}: <span style="display:inline-block;width:30px;height:30px;border-radius:50px;background:{$list[$param['parameter_name']]};margin:0 auto;">&nbsp;</span></p>
	    {else if $param['type'] == "vimeo"}
		{if $list[$param['parameter_name']]}
		    <div class="vimeo" data-vimeo_id="{$list[$param['parameter_name']]}" style="min-height:300px;width:150px;"></div>
		{/if}
	    {else}
		{if $param['options']}
		    <p>{$param['parameter_title']}: {$param['options'][$list[$param['parameter_name']]]}</p>
		{else}
		    {if $list[$param['parameter_name']]}
		    <p>{$param['parameter_title']}: {$list[$param['parameter_name']]}</p>
		    {/if}
		{/if}
	    {/if}
	{/foreach}
	</b>
	</p>
	<br>
	<p class="lang">If you perform this process, it will not be restored. Do you want to process it?</p>
	
</form>
<button class="cancel_delete lang">No</button>
<button class="ajax-link lang" data-form="delete_form_{$data.id}" data-class="{$class}" data-function="delete_exe" data-ai_setting_id="{$data.ai_setting_id}">Delete</button>

<script>
	$('.cancel_delete').click(function () {
	$(this).parent().closest(".multi_dialog").children('.multi_dialog_title_area').find('.multi_dialog_close').click();
	});
</script>
