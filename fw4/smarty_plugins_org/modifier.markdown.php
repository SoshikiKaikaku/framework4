<?php
/*
 * Smarty plugin
 * URL to link
 *
 * @param  string $value
 * @param  string $target
 * @return string
 */
function smarty_modifier_markdown($text) 
{
	include_once(dirname(__FILE__) . "/../markdown/Parsedown.php");

	$Parsedown = new Parsedown();
	$html = $Parsedown->text($text);
    return $html; 
}