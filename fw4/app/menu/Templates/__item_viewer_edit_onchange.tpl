
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
	<input type="text" name="{$name}" value="{$values.$name}" class="onchange_update"  data-class="{$class}" data-table_name="{$table_name}" data-id="{$values.id}">

{else if $type == "number"}
	{if $name != "id"}
		<h6>{$title}</h6>
		<input type="text" name="{$name}" value="{$values.$name}" class="onchange_update" data-class="{$class}" data-table_name="{$table_name}" data-id="{$values.id}">
	{/if}

{else if $type == "float"}
	<h6>{$title}</h6>
	<input type="text" name="{$name}" value="{$values.$name}" class="onchange_update" data-class="{$class}" data-table_name="{$table_name}" data-id="{$values.id}">

{else if $type == "textarea"}
	<h6>{$title}</h6>
	<textarea name="{$name}" class="wordcounter onchange_update" data-counter_max="{$item_setting[$name]["size"]}" data-class="{$class}" data-table_name="{$table_name}" data-id="{$values.id}">{$values.$name}</textarea>

{else if $type == "textarea_links"}
	<h6>{$title}</h6>
	<textarea name="{$name}" class="wordcounter onchange_update" data-counter_max="{$item_setting[$name]["size"]}" data-class="{$class}" data-table_name="{$table_name}" data-id="{$values.id}">{$values.$name}</textarea>

{else if $type == "markdown"}
	<h6>{$title}</h6>
	<textarea name="{$name}" class="wordcounter onchange_update" data-counter_max="{$item_setting[$name]["size"]}" data-class="{$class}" data-table_name="{$table_name}" data-id="{$values.id}">{$values.$name}</textarea>

{else if $type == "dropdown"}
	<h6>{$title}</h6>
	{html_options name=$name options=$item_setting[$name]["options"] selected=$values[$name] class="onchange_update" data-class="{$class}" data-table_name="{$table_name}" data-id="{$values.id}"}

{else if $type == "date"}
	<h6>{$title}</h6>
	{html_input_date name="{$name}" value="{$values.$name}" class="onchange_update" data-class="{$class}" data-table_name="{$table_name}" data-id="{$values.id}"}

{else if $type == "year_month"}
	<h6>{$title}</h6>
	<input type="text" name="{$name}" value="{$values.$name}" class="year_month_picker onchange_update" data-class="{$class}" data-table_name="{$table_name}" data-id="{$values.id}">

{else if $type == "checkbox"}
	<h6>{$title}</h6>
	<div class="fr_checkbox">
		<span class="material-symbols-outlined unchecked">check_box_outline_blank</span>
		<span class="material-symbols-outlined checked">check_box</span>
		<input type="text" name="{$name}" class="fr_checkbox onchange_update" value="{$values[$name]}" data-class="{$class}" data-table_name="{$table_name}" data-id="{$values.id}">
	</div>

{else if $type == "radio"}
	<h6>{$title}</h6>
	<fieldset>
		{foreach $item_setting[$name]["options"] as $key=>$val}
			<label for="{$class}_{$dialog_name}_{$name}_{$key}_{$timestamp}">{$val.value}</label>
			{if $values[$name] == $key}
				{assign checked "checked"}
			{else}
				{assign checked ""}
			{/if}
			<input type="radio" name="{$name}" value="{$key}" id="{$class}_{$dialog_name}_{$name}_{$key}_{$timestamp}" class="checkboxradio onchange_update" {$checked} data-class="{$class}" data-table_name="{$table_name}" data-id="{$values.id}">
		{/foreach}
	</fieldset>

{else if $type == "file"}
	<h6>{$title}</h6>
	<input type="file" name="{$name}" class="fr_image_paste onchange_update" data-text="File Uploader" data-class="{$class}" data-table_name="{$table_name}" data-id="{$values.id}">

{else if $type == "color"}
	<h6>{$title}</h6>
	<input type="text" name="{$name}" value="{$values[$name]}" class="colorpicker onchange_update" data-class="{$class}" data-table_name="{$table_name}" data-id="{$values.id}">



{/if}


