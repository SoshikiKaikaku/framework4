

{foreach $items as $name}
    {if $values[$name] !== ""}
		<h6 style="margin-top:20px;">{$item_setting[$name]["title"]}</h6>
		<p>{include file="__item_viewer.tpl"}</p>
    {/if}
{/foreach}
