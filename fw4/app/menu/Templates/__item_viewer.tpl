
{**
このテンプレートを呼ぶ前に下記を設定しておく必要がある。
$item_setting : $ctl->get_all_item_setting() で受け取った配列
$name : 項目名
$values : 値の配列 $values[$name]
**}

{assign type $item_setting[$name]["itemtype"]}
{assign title $item_setting[$name]["title"]}

{if $type == "text"}
	<p>{$values.$name}</p>

{else if $type == "number"}
	<p style="white-space: nowrap;text-align: right;">{$values.$name|number_format}</p>

{else if $type == "float"}
	<p style="white-space: nowrap;text-align: right;">{$values.$name}</p>

{else if $type == "textarea"}
	<p>{$values.$name|nl2br|escape nofilter}</p>


{else if $type == "textarea_links"}
	<p>{$values.$name|nl2br|url_link nofilter}</p>

{else if $type == "markdown"}
	<p>{$values.$name|markdown nofilter}</p>

{else if $type == "dropdown"}
	<p>{$item_setting[$name]["options"][$values[$name]]}</p>

{else if $type == "date"}
	<p style="white-space: nowrap;text-align: center;">{html_date value=$values.$name}</p>

{else if $type == "year_month"}
	<p style="white-space: nowrap;text-align: center;">{$values.$name}</p>

{else if $type == "checkbox"}
	<p style="text-align: center;"><span style='background: #{$item_setting[$name]["options"][$values[$name]]["color"]};padding:7px;white-space:nowrap;'>{$item_setting[$name]["options"][$values[$name]]["value"]}</span></p>

{else if $type == "radio"}
	<p style="text-align: center;"><span style='background: #{$item_setting[$name]["options"][$values[$name]]["color"]};padding:7px;white-space:nowrap;'>{$item_setting[$name]["options"][$values[$name]]["value"]}</span></p>

{else if $type == "color"}
	<p><span style="display:block;width:30px;height:30px;border-radius:50px;background:#{$values[$name]};margin:0 auto;">&nbsp;</span></p>

{else if $type == "image"}
	<p><img src="app.php?class={$class}&function=image&file={$values[$name]}_th&{$timestamp}"></p>

{else if $type == "vimeo"}
    {if $values[$name] != ""}
		<p><div class="vimeo" data-vimeo_id="{$values[$name]}" style="min-height:300px;"></div>
    {/if}

{/if}


