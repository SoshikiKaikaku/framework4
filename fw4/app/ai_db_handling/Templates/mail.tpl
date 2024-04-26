<div>
{foreach $list as $list_data}
    <div style="border: 1px solid;margin-bottom: 15px;padding: 10px;">
    {foreach $setting_parameters as $param}
	{if $param['para_type'] == 1}
	    <p>{$param['text']}	</p>
	{/if}
	{if $param['para_type'] == 0}
	    <div>{include file="{$base_template_dir}/__item_mail.tpl"}</div>
	{/if}

	{if $param['para_type'] == 2}

	    <p>{$param['sub_tb']['sub_fields']}</p>
	    {foreach $param['sub_tb']['sub_values'] as $val}
		<p>{$val}</p>	
	    {/foreach}
	{/if}

    {/foreach}
    <hr>
    <p>{$ai_setting['signiture']}</p>
    </div>
{/foreach}
</div>