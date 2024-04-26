{if $item.predefined_function == 1}
	<span>Work on: </span>Pulic Side
{else}
	{if $item.work_on == 0}
		<span>Work on: </span>All
	{else}
		<span>Work on: </span>{$workon_opt[$item.work_on]}
	{/if}
{/if}
{if $item.limit_user_type == 0}
	<span>&nbsp;User type: </span>Allow All
{else}
	<span>&nbsp;User type: </span>{if $item.user_type}{implode(",",$item.user_type)}{/if}
{/if}
{if $item.limit_workflow == 0}
	<span>&nbsp;Workflow status: </span>Allow All
{else}
	<span>&nbsp;Workflow status: </span>{if $item.workflow_status}{implode(",",$item.workflow_status)}{/if}
{/if}
