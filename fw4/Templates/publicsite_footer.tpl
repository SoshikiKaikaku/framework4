
<div id="appcode" style="display: none;">{$appcode}</div>
{if $testserver}
	<div id="testserver" style="display: none;">true</div>
{else}
	<div id="testserver" style="display: none;">false</div>
{/if}

<div id="display_errors" style="display: none;">{$setting.display_errors}</div>

<div id="dialog"></div>
<div id="multi_dialog"></div>

<div id="download_view">
	<div id="download_bar">
		<div id="download_message" style="margin-left:10px;"></div>
		<div id="download_progress"></div>
	</div>
</div>

<div id="lang_priority" style="display:none;">{$setting.lang_priority}</div>
<div id="lang_default" style="display:none;">{$setting.lang_default}</div>

<div id="windowcode" data-code="{$windowcode}"></div>

{include file="{$base_template_dir}/scripts.tpl"}

<script src="js/function.js?{$timestamp}"></script>

<script src="appjs.php?class={$class}&windowcode={$windowcode}&{$timestamp}"></script>

<div id="page_classname" data-class="{$class}" style="display: none;"></div>


