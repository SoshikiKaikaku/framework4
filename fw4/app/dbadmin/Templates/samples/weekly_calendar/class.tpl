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

function page(Controller $ctl){
$post_data = $ctl->POST();

//define start date and end date
if (isset($post_data['date_range'])) {
$date_range = $ctl->POST("date_range"); 
$array_date = explode('_', $date_range);

if ($array_date[0] == "lastweek") {
$start_date = date("Y-m-d", strtotime("-7 day", strtotime($array_date[1])));
$end_date = $array_date[1];
} else if ($array_date[0] == "nextweek") {
$start_date = $array_date[1];
$end_date = date("Y-m-d", strtotime("+7 day", strtotime($array_date[1])));
} 
} else {
if (isset($post_data['start_date']) && isset($post_data['end_date'])) {
$start_date = $post_data['start_date'];
} else {
$start_date = date("Y-m-d", strtotime('monday this week'));
}
$end_date = date("Y-m-d", strtotime("+7 day", strtotime($start_date)));
}

$day_bg[] = [];
$task_step_list = [];
$date_range = $this->getDateRange($start_date, $end_date);

foreach ($date_range as $date) {

$task_steps = $this->fmt_{$db_name}->filter(['scheduled_date'], [$date['date']]);
foreach ($task_steps as $task_step) {

$start_ex = explode(":",$task_step["start_time"]);
if(count($start_ex)==2){
$start = $start_ex[0];
}else{
$start = 6;
}

$end_ex = explode(":",$task_step["end_time"]);
if(count($end_ex)==2){
$end = $end_ex[0];
}else{
$end = 6;
}

if($end_ex[1] != 0){
$check_end = $end+1;
}else{
$check_end = $end;
}

for($i=$start;$i<$check_end;$i++){
$day_bg[$task_step["scheduled_date"]][$i] = true;
}

$task_step_list[$task_step["scheduled_date"]][$start][] = $task_step;
}
}

$ctl->assign("day_bg", $day_bg);
$ctl->assign("start_date", $start_date);
$ctl->assign("end_date", $end_date);
$ctl->assign("date_range", $date_range);
$ctl->assign("task_step_list", $task_step_list);

{if $is_child}
	$res = $ctl->fetch("index.tpl");
	$ctl->reload_area("#{$class_name}_{$db_name}_".$post['{$parent_db}_id'], $res);
{else}
	$ctl->show_main_area("{$db_name}", "index.tpl", "{$db_name_str}");
{/if}
}

function getDateRange($start_date, $end_date){
$period = new DatePeriod(
new DateTime($start_date),
new DateInterval('P1D'),
new DateTime($end_date)
);
$days_of_date_range = [];
foreach ($period as $key => $value) {
$date = $value->format('Y/m/d');
$days_of_date_range[$date] = [
"day" => date("jS l",strtotime($value->format('Y-m-d'))),
"date" => $date,
"day_number" => date("j",strtotime($value->format('Y-m-d'))),
"day_superscript" => date("S",strtotime($value->format('Y-m-d'))),
"day_name" => substr(date("l",strtotime($value->format('Y-m-d'))), 0, 3),
];
}
return $days_of_date_range;
}

function add_task_step(Controller $ctl)
{
$post = $ctl->POST();
$ctl->assign('post', $post);

$ctl->show_multi_dialog("add_{$db_name}", 'add.tpl', "Add {$db_name_str}", 600, true, true);
}

function add_task_step_exe(Controller $ctl){
$post = $ctl->POST();

//validation
$errors = $this->validate_{$db_name}_data($ctl, $post, "add");
if (count($errors)){
$ctl->assign('errors', $errors);
$this->add_task_step($ctl);
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

function edit_task_step(Controller $ctl)
{
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

function edit_task_step_exe(Controller $ctl){
$post = $ctl->POST();
$data = $this->fmt_{$db_name}->get($post['id']);

//validation
$errors = $this->validate_{$db_name}_data($ctl, $post, "edit");
if (count($errors)){
$ctl->assign('errors', $errors);
$this->edit_task_step($ctl);
return;
}

foreach ($_POST as $key => $val) {
$data[$key] = $val;
}
$this->fmt_{$db_name}->update($data);

$ctl->close_multi_dialog('edit_{$db_name}');
$this->page($ctl);
}

function delete_task_step(Controller $ctl){
$task_step_id = $ctl->POST("id");
$data = $this->fmt_{$db_name}->get($task_step_id);
$ctl->assign("data", $data);
$ctl->assign("post", $ctl->POST());
$ctl->show_multi_dialog("delete_{$db_name}", "delete.tpl", "Delete {$db_name_str}", 500, true, true);
}

function delete_task_step_exe(Controller $ctl){
$task_step_id = $ctl->POST("id");
$this->fmt_{$db_name}->delete($task_step_id);

$ctl->close_multi_dialog('delete_{$db_name}');
$this->page($ctl);
}

function update_task_steps_drag_and_drop(Controller $ctl){        
$post_data = $ctl->POST();
$task_step = $this->fmt_{$db_name}->get($post_data["id"]);

// 時間を取得
$start_ex = explode(":", $task_step["start_time"]);
if(count($start_ex) == 2){
$start_time = $start_ex[0] * 60 + $start_ex[1];
}else{
$start_time = 0;
}
$end_ex = explode(":", $task_step["end_time"]);
if(count($end_ex) == 2){
$end_time = $end_ex[0] * 60 + $end_ex[1];
}else{
$end_time = 0;
}
if($end_time > $start_time){
$duration = $end_time - $start_time;
$add_hour = floor($duration / 60);
$add_min = $duration - ($add_hour * 60);
}else{
$add_hour = 1;
$add_min = 0;
}

foreach ($post_data as $key => $val) {
if($key == "start_time"){
$task_step["start_time"] = $val . ":00";
$task_step["end_time"] = ($val + $add_hour) . ":" . sprintf('%02d', $add_min);
}else{
$task_step[$key] = $val;
}
}        
$this->fmt_{$db_name}->update($task_step);
}

}
