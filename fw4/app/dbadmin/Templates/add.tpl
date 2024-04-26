<form id="form_insert">

	<input type="hidden" name="dbclass" value="{$post.dbclass}" />
	<input type="hidden" name="db" value="{$post.db}" />

	{foreach $keys as $key}

		{if $key.0 != 'id'}
			<p>{$key.0}: ({$key.2})</p>
			<input type="text" name="{$key.0}" value="">	
		{/if}

	{/foreach}

	<button class="ajax-link" data-form="form_insert" data-class="{$class}" data-function="insert_exe">Submit</button>

</form>

