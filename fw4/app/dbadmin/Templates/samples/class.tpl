<?php

class {$class_name} {

private $fmt_{$db_name};

{foreach $params as $element => $arr_html}
	public ${$element}_options = {$arr_html nofilter};
{/foreach}

{foreach $child_params as $element => $arr_html}
	public $child_{$element}_options = {$arr_html nofilter};
{/foreach}

function __construct(Controller $ctl){
$this->fmt_{$db_name} = $ctl->db("{$db_name}");

{foreach $table_settings as $element}
	{if $element['field_type'] == 'select' || $element['field_type'] == 'radio' || $element['field_type'] == 'checkbox'}
		$ctl->assign('{$element['field_name']}_options', $this->{$element['field_name']}_options);
	{/if}
{/foreach}

{foreach $child_table_settings as $element}
	{if $element['field_type'] == 'select' || $element['field_type'] == 'radio' || $element['field_type'] == 'checkbox'}
		$ctl->assign('child_{$element['field_name']}_options', $this->child_{$element['field_name']}_options);
	{/if}
{/foreach}
}

//index page
function page(Controller $ctl)
{
{if $parent_combine_enabled}
	$ctl->ajax("{$parent_class}", "page");
{else}

	$post = $ctl->POST();
	$ctl->assign('post', $post);

	$max = $ctl->increment_post_value('max', 10);
	{* $items = $this->fmt_{$db_name}->filter({$filter_name_str}, {$filter_var_str}, false, 'AND', 'id', SORT_DESC, $max, $is_last); *}
	$items = $this->fmt_{$db_name}->filter({$filter_name_str}, {$filter_var_str}, false, 'AND', 'sort', SORT_ASC, $max, $is_last);
	$ctl->assign("max", $max);
	$ctl->assign("is_last", $is_last);
	$ctl->assign("items", $items);

	{if $combine_enabled}
		$this->fmt_{$child_db_name} = $ctl->db("{$child_db_name}", "{$child_class_name}");
		$child_items = [];
		foreach($items as $item) {
		$child_items[$item['id']] = $this->fmt_{$child_db_name}->select(["{$db_name}_id"], [$item['id']], false, 'AND', 'sort', SORT_ASC);
		}
		$ctl->assign("child_items", $child_items);
	{/if}

	{if $is_child}
		$res = $ctl->fetch("index.tpl");
		$ctl->reload_area("#{$class_name}_{$db_name}_".$post['{$parent_db}_id'], $res);
	{else}
		$ctl->show_main_area("{$db_name}", "index.tpl", "{$db_name_str}");
	{/if}

{/if}

}

//view add page
function add(Controller $ctl){
$post = $ctl->POST();
$ctl->assign('post', $post);
$ctl->show_multi_dialog("add_{$db_name}", "add.tpl", "Add {$db_name_str}", 600, true, true);
}

//save add data
function add_exe(Controller $ctl){
$post = $ctl->POST();
$ctl->assign('post', $post);

//validation
$errors = $this->validate_{$db_name}_data($ctl, $post, "add");
if (count($errors)){
$ctl->assign('errors', $errors);
$this->add($ctl);
return;
}

{foreach $table_settings as $element}
	{if !$element['add_flg']}
		{continue}
	{/if}

	{if $element['field_type'] == 'file'}
		{assign var="enable_uploading" value=true}
		$post['{$element['field_name']}'] = $this->upload($ctl, '{$element['field_name']}', 'add');
	{/if}
	{if $element['field_type'] == 'checkbox'}
		$post['{$element['field_name']}'] = implode(", ", $post['{$element['field_name']}']);
	{/if}
{/foreach}

$post['created_at'] = time();
$id = $this->fmt_{$db_name}->insert($post);

//close adding page
$ctl->close_multi_dialog("add_{$db_name}");

$this->page($ctl);
}

//validation
function validate_{$db_name}_data(Controller $ctl, $post, $page)
{
$errors = [];

{foreach $table_settings as $element}
	{if !$element['validate_flg']}
		{continue}
	{/if}

	{if $element['field_type']=="file"}
		$filetype = $ctl->get_posted_file_extention("{$element['field_name']}");
		if (empty($filetype) && $page=='add')
		$errors["{$element['field_name']}"] = "{ucwords(str_replace("_", " ", $element['field_name']))} is required!";

		{* validation for images *}
		{if $element['validation_file_types']=="image"}
			elseif(!empty($filetype) && $filetype != 'jpg' && $filetype != 'png')
			$errors["{$element['field_name']}"] = "JPEG/PNG Only"; //JPEG/PNGのみアップロード可能です

			{* validation for pdf *}
		{elseif $element['validation_file_types']=="pdf"}
			elseif(!empty($filetype) && $filetype != 'pdf')
			$errors["{$element['field_name']}"] = "PDF Only";

			{* validation for videos *}
		{elseif $element['validation_file_types']=="video"}
			elseif(!empty($filetype) && $filetype != 'mp4')
			$errors["{$element['field_name']}"] = "MP4 Only";

		{/if}
	{else}
		if (empty($post["{$element['field_name']}"]))
		$errors["{$element['field_name']}"] = "{ucwords(str_replace("_", " ", $element['field_name']))} is required!";
	{/if}

	{if $element['field_type']=="text"}
		{if $element['validation_text_types']=="email"}
			elseif (!filter_var($post["{$element['field_name']}"], FILTER_VALIDATE_EMAIL))
			$errors["{$element['field_name']}"] = "Invalid email format";
		{elseif $element['validation_text_types']=="number"}
			elseif (!is_numeric($post["{$element['field_name']}"]))
			$errors["{$element['field_name']}"] = "Number Only";
		{elseif $element['validation_text_types']=="text"}
			elseif (!ctype_alpha($post["{$element['field_name']}"]))
			$errors["{$element['field_name']}"] = "Text Only";
		{/if}
	{/if}

	{if $element['field_type']=="number" || $element['field_type']=="float"}
		elseif (!is_numeric($post["{$element['field_name']}"]))
		$errors["{$element['field_name']}"] = "Number Only";
	{/if}

{/foreach}

return $errors;
}

//view edit page
function edit(Controller $ctl){
$post = $ctl->POST();
$ctl->assign("post", $post);

$data = $this->fmt_{$db_name}->get($post['id']);
$data = array_merge($data, $post);
$ctl->assign("data", $data);

$ctl->show_multi_dialog("edit_{$db_name}_".$post['id'], "edit.tpl", "Edit {$db_name_str}", {(empty($childs)||$combine_enabled)?'600':'900'}, true, true);

{foreach $childs as $child}
	$ctl->ajax('{$child.child_class}', 'page', ['{$db_name}_id' => $post['id']]);
{/foreach}

}

//save edited data
function edit_exe(Controller $ctl){
$post = $ctl->POST();
$ctl->assign('post', $post);

//validation
$errors = $this->validate_{$db_name}_data($ctl, $post, "edit");
if (count($errors)){
$ctl->assign('errors', $errors);
$this->edit($ctl);
return;
}

$data = $this->fmt_{$db_name}->get($post['id']);
foreach ($_POST as $key => $value) {
$data[$key] = $value;
}

{foreach $table_settings as $element}
	{if !$element['edit_flg']}
		{continue}
	{/if}


	{if $element["field_type"] == 'file'}
		//upload files
		{assign var="enable_uploading" value=true}
		$old_filename = $data['{$element['field_name']}'];
		$new_filename = $this->upload($ctl, '{$element['field_name']}', 'edit');
		if (!empty($new_filename)){
		$data['{$element['field_name']}'] = $new_filename;
		$ctl->remove_saved_file($old_filename);
		}
	{/if}

	{if $element["field_type"] == 'checkbox'}
		//checkbox data
		$data['{$element['field_name']}'] = implode(", ", $post['{$element['field_name']}']);
	{/if}
{/foreach}

$data['updated_at'] = time();
$this->fmt_{$db_name}->update($data);

$ctl->close_multi_dialog("edit_{$db_name}_".$post['id']);
$this->page($ctl);
}

//view delete page
function delete(Controller $ctl){
$id = $ctl->POST("id");
$data = $this->fmt_{$db_name}->get($id);
$ctl->assign("data", $data);
$ctl->show_multi_dialog("delete", "delete.tpl", "Delete {$db_name_str}", 500, true, true);
}

//delete data form database
function delete_exe(Controller $ctl){
$id = $ctl->POST("id");

//file delete
$data = $this->fmt_{$db_name}->get($id);
{foreach $table_settings as $element}
	{if !$element['add_flg']}
		{continue}
	{/if}

	{if $element['field_type'] == 'file'}
		$ctl->remove_saved_file($data['{$element['field_name']}']);
	{/if}
{/foreach}

//deleting child data
{foreach $childs as $child}
	$ctl->ajax('{$child.child_class}', 'delete_foreign_data', ['foreign_key' => "{$db_name}_id", 'foreign_id' => $id]);
{/foreach}

$this->fmt_{$db_name}->delete($id);

$ctl->close_multi_dialog("delete");
$this->page($ctl);
}

{if $is_child}
    //delete child data from this db of a parent
    function delete_foreign_data(Controller $ctl){
	$post = $ctl->POST();

	$child_items = $this->fmt_{$db_name}->select([$post['foreign_key']], [$post['foreign_id']]);
	foreach($child_items as $item) {
	$_POST['id'] = $item['id'];
	$this->delete_exe($ctl);
	}
    }

{/if}

{if $enable_uploading}
    //upload files
    function upload(Controller $ctl, $element, $page)
    {
	$filename = $ctl->get_posted_filename($element);

	if (!empty($filename)) {
	$saved_filename = floor(microtime(true)*1000) . '-' . str_replace(" ", "-", $filename);
	try {
	$ctl->save_posted_file($element, $saved_filename);
	} catch (\Throwable $th) {
	$errors[$element] = "Something went wrong with ".ucwords(str_replace("_", " ", $element)).", Please try again leter!";
	$ctl->assign('errors', $errors);
	if ($page == 'add')
	$this->add($ctl);
	else
	$this->edit($ctl);
	}
	}

	return $saved_filename;
    }

    function download(Controller $ctl)
    {
	$ctl->res_saved_file($ctl->POST('file'));
    }

{/if}

function sort(Controller $ctl)
{
$post = $ctl->POST();
$logArr = explode(',', $post['log']);
$c = 0;
foreach ($logArr as $id) {
$d = $this->fmt_{$db_name}->get($id);
$d['sort'] = $c;
{if $parent_db!=''}
	if (!empty($post["parent_id"])) {
	$d["{$parent_db}_id"] = $post["parent_id"];
	}
{/if}
$this->fmt_{$db_name}->update($d);
$c++;
}
}

}
