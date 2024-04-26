<?php

class {$class_name} {
private $fmt_{$db_name};

{foreach $params as $element => $arr_html}
	public ${$element}_options = {$arr_html nofilter};
{/foreach}

{foreach $child_params as $element => $arr_html}
	public $child_{$element}_options = {$arr_html nofilter};
{/foreach}

function __construct(Controller $ctl) {
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

function page(Controller $ctl) {
$post = $ctl->POST();
$month = $post['month'];
$current_start_date = $ctl->POST('current_start_date');

//define dates
if(empty($month)){
$dt = date("Y-m-d");
$month_start_date = date("Y-m-01", strtotime($dt));
$start_date = date('Y-m-d',strtotime('last sunday', strtotime($month_start_date)));
$end_date = date("Y-m-t", strtotime($dt));
$end_date = date('Y-m-d',strtotime('next saturday', strtotime($end_date)));
}elseif($month == "previous"){
$month_start_date = $start_date = date('Y-m-d', strtotime('-1 month', strtotime($current_start_date)));
$start_date = date('Y-m-d',strtotime('last sunday', strtotime($month_start_date)));
$end_date = date("Y-m-t", strtotime($month_start_date));
$end_date = date('Y-m-d',strtotime('next saturday', strtotime($end_date)));
}elseif($month == "next"){
$month_start_date = date('Y-m-d', strtotime('+1 month', strtotime($current_start_date)));
$start_date = date('Y-m-d',strtotime('last sunday', strtotime($month_start_date)));
$end_date = date("Y-m-t", strtotime($month_start_date));
$end_date = date('Y-m-d',strtotime('next saturday', strtotime($end_date)));
}elseif($month == "select"){
$month_start_date = date('Y-m-d', strtotime($current_start_date."/01"));
$start_date = date('Y-m-d',strtotime('last sunday', strtotime($month_start_date)));
$end_date = date("Y-m-t", strtotime($month_start_date));
$end_date = date('Y-m-d',strtotime('next saturday', strtotime($end_date)));
}

$current_month = date("Y/m", strtotime($month_start_date));
$dates_arr = $this->getBetweenDates($start_date, $end_date);

$tasks = [];
$tasks_data = $this->fmt_{$db_name}->getall();
foreach($tasks_data as $task){
$month = date("Y/m", strtotime($task['scheduled_date']));
if(isset($dates_arr[$task['scheduled_date']])){
$month_date = date('m/d', strtotime($task['scheduled_date']));
$tasks[$month_date][] = $task;
}
}

$ctl->assign("tasks", $tasks);
$ctl->assign("current_start_date", $month_start_date);
$ctl->assign("start_month_year", $current_month);
$ctl->assign("days_of_date_range", $dates_arr);
$ctl->show_main_area("calendar", "index.tpl", "Calendar Sample");

{if $is_child}
	$res = $ctl->fetch("index.tpl");
	$ctl->reload_area("#{$class_name}_{$db_name}_".$post['{$parent_db}_id'], $res);
{else}
	$ctl->show_main_area("{$db_name}", "index.tpl", "{$db_name_str}");
{/if}
}

function getBetweenDates($startDate, $endDate) {
$startDate = strtotime($startDate);
$endDate = strtotime($endDate);
$days_of_date_range = [];

for ($currentDate = $startDate; $currentDate <= $endDate; $currentDate += (86400)) {
$date = date('Y/m/d', $currentDate);
$days_of_date_range[$date] = [
"day"=>date("l", $currentDate),
"date"=>$date,
"month_and_day"=>date('m/d', $currentDate),
"day_number"=>date('j ', $currentDate),
"day_superscript"=>date("S",$currentDate),
"day_name"=>substr(date("l",$currentDate), 0, 3)
];
}

return $days_of_date_range;
}

function add(Controller $ctl) {
$post = $ctl->POST();
$ctl->assign('post', $post);

$ctl->show_multi_dialog("add_{$db_name}", 'add.tpl', "Add {$db_name_str}", 600, true, true);
}

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

$post["user_id"] = $ctl->get_login_user_id();

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

$this->fmt_{$db_name}->insert($post);

$ctl->close_multi_dialog('add_{$db_name}');
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

function edit(Controller $ctl) {
$post = $ctl->POST();
$ctl->assign('post', $post);

$data = $this->fmt_{$db_name}->get($post['id']);
$data = array_merge($data, $post);
$ctl->assign('data', $data);

$ctl->show_multi_dialog('edit_{$db_name}', 'edit.tpl', 'Edit {$db_name_str}', 600);

{foreach $childs as $child}
	$ctl->ajax('{$child.child_class}', 'page', ['{$db_name}_id' => $post['id']]);
{/foreach}
}

function edit_exe(Controller $ctl){
$post = $ctl->POST();
$data = $this->fmt_{$db_name}->get($post['id']);

//validation
$errors = $this->validate_{$db_name}_data($ctl, $post, "edit");
if (count($errors)){
$ctl->assign('errors', $errors);
$this->edit($ctl);
return;
}

foreach ($_POST as $key => $val) {
$data[$key] = $val;
}
$this->fmt_{$db_name}->update($data);

$ctl->close_multi_dialog('edit_{$db_name}');
$this->page($ctl);
}

function update_task_steps_drag_and_drop(Controller $ctl){        
$post = $ctl->POST();
$task_step = $this->fmt_{$db_name}->get($post["id"]);

$task_step['scheduled_date'] = $post['date'];      
$this->fmt_{$db_name}->update($task_step);
}

function delete(Controller $ctl){
$task_step_id = $ctl->POST("id");
$data = $this->fmt_{$db_name}->get($task_step_id);
$ctl->assign("data", $data);
$ctl->assign("post", $ctl->POST());
$ctl->show_multi_dialog("delete_{$db_name}", "delete.tpl", "Delete {$db_name_str}", 500, true, true);
}

function delete_exe(Controller $ctl){
$task_step_id = $ctl->POST("id");
$this->fmt_{$db_name}->delete($task_step_id);

$ctl->close_multi_dialog('delete_{$db_name}');
$this->page($ctl);
}

}
