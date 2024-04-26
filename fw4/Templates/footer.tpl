

<div id="windowcode" data-code="{$windowcode}"></div>
	
<div id="dialog"></div>
<div id="multi_dialog"></div>

{if $testserver}
	<div id="testserver" style="display: none;">true</div>
{else}
	<div id="testserver" style="display: none;">false</div>
{/if}
<div id="display_errors" style="display: none;">{$setting.display_errors}</div>

<div id="lang_priority" style="display:none;">{$setting.lang_priority}</div>
<div id="lang_default" style="display:none;">{$setting.lang_default}</div>
		
<div id="session"></div>

<script src="js/function.js?{$timestamp}"></script>

<script src="appjs.php?class=base&windowcode={$windowcode}&{$timestamp}"></script>

{foreach $js_class_list as $jsclass}
	<script src="appjs.php?class={$jsclass}&windowcode={$windowcode}&{$timestamp}"></script>
{/foreach}

