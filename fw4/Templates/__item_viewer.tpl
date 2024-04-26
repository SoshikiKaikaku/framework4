
{**
This template need the following parameters.
$field : The field format array of ai_db/ai_field.fmt
$row : array of the values.
**}

{assign name $field["parameter_name"]}
{assign type $field["type"]}
{assign title $field["parameter_title"]}

{if $type == "text"}
	<p>{$row.$name}</p>
	
{else if $type == "number"}
	<p style="white-space: nowrap;text-align: right;">{$row.$name|number_format}</p>
	
{else if $type == "float"}
	<p style="white-space: nowrap;text-align: right;">{$row.$name}</p>
	
{else if $type == "textarea"}
	<p>{$row.$name|nl2br|escape nofilter}</p>

	
{else if $type == "textarea_links"}
	<p>{$row.$name|nl2br|url_link nofilter}</p>
	
{else if $type == "markdown"}
	<p>{$row.$name|markdown nofilter}</p>
	
{else if $type == "dropdown"}
	<p>{$field["options"][$row[$name]]}</p>
	
{else if $type == "date"}
	<p style="white-space: nowrap;text-align: center;">{html_date value=$row.$name}</p>
	
{else if $type == "year_month"}
	<p style="white-space: nowrap;text-align: center;">{$row.$name}</p>
	
{else if $type == "checkbox"}
	<p style="text-align: center;"><span style='background: #{$item_setting[$name]["options"][$row[$name]]["color"]};padding:7px;white-space:nowrap;'>{$row.$name}</span></p>
			
{else if $type == "radio"}
	<p style="text-align: center;"><span style='background: #{$item_setting[$name]["options"][$row[$name]]["color"]};padding:7px;white-space:nowrap;'>{$field["options"][$row[$name]]}</span></p>
	
{else if $type == "color"}
	<p><span style="display:block;width:30px;height:30px;border-radius:50px;background:{$row[$name]};margin:0 auto;">&nbsp;</span></p>
	
{else if $type == "image"}
    {if $row[$name]}
	<p><img src="app.php?class=ai_db&function=view_image&file={$row[$name]}&{$timestamp}" style="width:100px;"></p>
    {/if}
{else if $type == "file"}
    {if $row[$name]}
	<button class="download-link lang download-btn-file" data-class="ai_db" data-function="download_file" data-filename="{$row[$name]}" data-id="{$data['id']}"><span class="material-symbols-outlined">cloud_download</span></button>
    {/if}
{else if $type == "vimeo"}
    {if $row[$name] != ""}
	<div class="vimeo" data-vimeo_id="{$row[$name]}" style="min-height:300px;width:150px;"></div>
    {/if}
    
{else if $type == "time"}
    <p>{$row.$name}</p>
	
{/if}


