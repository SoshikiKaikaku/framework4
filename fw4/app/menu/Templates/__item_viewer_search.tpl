
{**
このテンプレートを呼ぶ前に下記を設定しておく必要がある。
$item_setting : $ctl->get_all_item_setting() で受け取った配列
$name : 項目名
$values : 値の配列 $values[$name]
**}

{assign type $item_setting[$name]["itemtype"]}
{assign title $item_setting[$name]["title"]}

{if $type == "text"}
	<h6>{$title}</h6>
	<input type="text" name="{$name}" value="{$values.$name}">

{else if $type == "number"}
	<h6>{$title}</h6>
	<input type="text" name="{$name}" value="{$values.$name}">

{else if $type == "float"}
	<h6>{$title}</h6>
	<input type="text" name="{$name}" value="{$values.$name}">

{else if $type == "textarea"}
	<h6>{$title}</h6>
	<input type="text" name="{$name}" value="{$values.$name}">

{else if $type == "textarea_links"}
	<h6>{$title}</h6>
	<input type="text" name="{$name}" value="{$values.$name}">

{else if $type == "markdown"}
	<h6>{$title}</h6>
	<input type="text" name="{$name}" value="{$values.$name}">

{else if $type == "dropdown"}
	<h6>{$title}</h6>
	{html_options name=$name options=$item_setting[$name]["options"] selected=$values[$name]}

{else if $type == "date"}
	<h6>{$title}</h6>
	{html_input_date name="{$name}" value="{$values.$name}"}

{else if $type == "year_month"}
	<h6>{$title}</h6>
	<input type="text" name="{$name}" value="{$values.$name}" class="year_month_picker">

{else if $type == "checkbox"}
	<h6>{$title}</h6>
	<div class="fr_checkbox" data-search="1">
		<span class="material-symbols-outlined unselected" title="no filter">indeterminate_check_box</span>
		<span class="material-symbols-outlined unchecked" title="unchecked">check_box_outline_blank</span>
		<span class="material-symbols-outlined checked" title="checked">check_box</span>
		<input type="text" name="{$name}" class="fr_checkbox" value="{$values[$name]}">
	</div>

{else if $type == "radio"}
	<h6>{$title}</h6>
	<fieldset>
		{foreach $item_setting[$name]["options"] as $key=>$val}
			{if $key == ""}
				<label for="{$class}_{$dialog_name}_{$name}_{$key}_{$timestamp}">No filter</label>
			{else}
				<label for="{$class}_{$dialog_name}_{$name}_{$key}_{$timestamp}">{$val.value}</label>
			{/if}

			{if $values[$name] == $key}
				{assign checked "checked"}
			{else}
				{assign checked ""}
			{/if}
			<input type="radio" name="{$name}" value="{$key}" id="{$class}_{$dialog_name}_{$name}_{$key}_{$timestamp}" class="checkboxradio" {$checked}>
		{/foreach}
	</fieldset>

{/if}


