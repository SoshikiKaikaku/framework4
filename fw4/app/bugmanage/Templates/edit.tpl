<form id="bugmanage_bugs_edit_form_{$data.id}">

	<input type="hidden" name="id" value="{$data.id}">
	<input type="hidden" name="oldimage" value="{$data.image}">
	<input type="hidden" name="oldstatus" value="{$data.status}">

	{* search params *}
	<input type="hidden" name="search_id" value="{$post.search_id}">
	<input type="hidden" name="search_status" value="{$post.search_status}">
	<input type="hidden" name="search_priority" value="{$post.search_priority}">
	<input type="hidden" name="search_desk" value="{$post.search_desk}">

	<div class="first-div">
        {if $data.image}
			<div class="photo">
				<a href="app.php?class={$class}&function=view_image&file={$data.image}&{$timestamp}" target="_blank"><img src="app.php?class={$class}&function=view_image&file={$data.image}&{$timestamp}" style="width: 100%"/></a>
			</div>
        {/if}
        <div class="bugimage">
			<p class="lang">File Upload:</p><input type="file" name="image">
			<p class="error lang">{$errors['image']}</p>
		</div>
	</div>
	<div class="second-div">

		<div class="addstatus">
			<p class="lang">Status:</p>
			{html_options name="status" selected=$data.status options=$status class="lang"}
			<p class="error lang">{$errors['status']}</p>
		</div>

		<div class="addprio">
			<p class="lang">Priority:</p>
			{html_options name="priority" selected=$data.priority options=$priority class="lang"}

			<p class="error lang">{$errors['priority']}</p>
		</div>



		<div class="desk-jp">
			<p class="lang">Japanees:</p>
			<textarea name="desk_japanees" class="wordcounter" data-counter_max="2000">{$data.desk_japanees}</textarea>

			<p class="error lang">{$errors['desk_japanees']}</p>
		</div>

		<div class="desk-en">
			<p class="lang">English:</p>
			<textarea name="desk_english" class="wordcounter" data-counter_max="2000">{$data.desk_english}</textarea>

			<p class="error lang">{$errors['desk_english']}</p>
		</div>
	</div>



	<div>

	</div>

</form>

<!--<div style="clear:both; border-bottom:1px #acacac solid; width:100%; padding-top: 50px; padding-bottom: 10px;"></div>
<div>
    <button class="ajax-link lang" data-form="bughistory_bug_history_edit_form_{$data.id}" data-bugs_id="{$data.id}" data-class="bughistory" data-function="add">Add History</button>
</div>
{*<div id="bughistory_bug_history_{$data.id}" style="min-height: 300px;"></div>*}
<div id="bughistory_bug_history_{$data.id}" >
{include file="../../bughistory/Templates/index_history.tpl" bug_id=$data.id}
</div>-->


