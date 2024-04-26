<head>
<!--	<meta name="viewport" content="user-scalable=0.7, width=device-width, initial-scale=1" />-->
	<meta name="viewport" content="{$viewport_base}">

	<meta charset="utf-8">

	<link rel="stylesheet" href="css/iconfont/material-icons.css">
	<link rel="stylesheet" href="https://code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css">
	<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" />
	<link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/timepicker/1.3.5/jquery.timepicker.min.css">
	<link href="https://cdn.jsdelivr.net/npm/air-datepicker@3.3.0/air-datepicker.min.css" rel="stylesheet" type="text/css">
	
	<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" />

	<link rel="stylesheet" href="css/appstyle.css?{$timestamp}">
	
	<link rel="stylesheet" href="appcss.php?class={$class}&{$timestamp}">
	
	<link rel="icon" href="/fw4/images/favicon.ico" type="image/x-icon" id="favicon">
	
	{if $testserver }
		<title>/DEV/ {$pagetitle}</title>
	{else}
		<title>{$pagetitle}</title>
	{/if}
	
{include file="{$base_template_dir}/scripts.tpl"}
		
	
</head>


