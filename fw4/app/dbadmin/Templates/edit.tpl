<form id="form_update{$data.id}">

	<input type="hidden" name="dbclass" value="{$post.dbclass}" />
	<input type="hidden" name="db" value="{$post.db}" />
	<input type="hidden" name="id" value="{$data.id}" />

	{foreach $keys as $key}

		{if $key.0 != 'id'}
			<p>{$key.0}: ({$key.2})</p>
			<input type="text" name="{$key.0}" value="{$data[$key.0]}">	
		{/if}

	{/foreach}

	<button class="ajax-link" data-form="form_update{$data.id}" data-class="{$class}" data-function="edit_exe">Submit</button>

</form>

