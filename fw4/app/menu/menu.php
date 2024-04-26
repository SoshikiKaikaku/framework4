<?php

class menu {

	private $ffm;
	private $table_name;
	private $is_parent = false;
	private $dialog_name;
	private $dialog_name_edit;
	private $ajax_option;
	private $days = ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'];

	function page(Controller $ctl) {

		// Calling function of this class
		$list_type = $this->ffm->get_list_type();
		$function_name = "list_" . $list_type;
		$this->$function_name($ctl);
	}

	function my_validation(&$errors, &$result, $ctl) {
		/*
		 * You can add your validation code here
		 */
		//		// ex)
		// if($this->table_name == "xxxxxx" && $ctl->POST("itemname") == "xxxxxx"){
		//	$errors["itemname"] = "error message";
		//	$result = false;
		// }else{
		//	$errors["itemname"] = "";
		// }
	}

	function add(Controller $ctl) {
		$add_items = $this->ffm->get_screen_items(FFM::SCREEN_ADD);
		$ctl->assign("add_items", $add_items);
		$ctl->assign("item_setting", $this->ffm->get_all_item_setting());
		$ctl->show_multi_dialog("add", "add.tpl", "Add", 1000, "_add_button.tpl");
	}

	function add_exe(Controller $ctl) {

		$row = $ctl->POST();
		$form_id = $ctl->POST("_form_id");

		// validation
		[$errors, $result] = $this->ffm->validation($row, FFM::SCREEN_ADD);
		$this->my_validation($errors, $result, $ctl);
		foreach ($errors as $itemname => $error) {
			$ctl->reload_area("#$form_id .error_$itemname", $error);
		}
		if (!$result) {
			return;
		}

		// set parent id
		$parent_id_encrypted = $ctl->POST("parent_id");
		$parent_id = $ctl->decrypt($parent_id_encrypted);
		$parent = $this->ffm->get_parent();
		$row[$parent . "_id"] = $parent_id;

		// run insert
		$this->ffm->insert($row);

		// save files
		$this->save_files($ctl, $row, FFM::SCREEN_ADD);
		$this->ffm->update($row);

		$ctl->close_multi_dialog("add");

		$reload_table = $ctl->POST("reload_table");
		$reload_table_id = $ctl->POST("reload_table_id");
		if (empty($reload_table)) {
			$ctl->ajax($ctl->get_classname(), "page", $this->ajax_option);
		} else {
			$this->ajax_option = [];
			$this->ajax_option["parent_id"] = $reload_table_id;
			$this->ajax_option["table_name"] = $reload_table;
			$ctl->ajax($ctl->get_classname(), "page", $this->ajax_option);
		}
	}

	function edit(Controller $ctl) {
		$id_encrypted = $ctl->POST("id_encrypted");
		$id = $ctl->decrypt($id_encrypted);

		$child_table_name = $ctl->POST("child_table_name");
		if (!empty($child_table_name)) {
			$ffm_c = $ctl->db($child_table_name);
		} else {
			$ffm_c = $this->ffm;
		}

		$row = $this->ffm->get($id);
		$ctl->assign("values", $row);

		/*
		 * If you need change parent data like the following code.
		 * 
		 * if($this->table_name == "xxxxxx"){
		 *     // getting parent row
		 *     $parent = $this->ffm->get_parent();
		 *     $ffm_parent = $ctl->db($parent);
		 *     $parent_id = $row[$parent . '_id'];
		 *     $parent_row = $ffm_parent->get($parent_id);
		 * 
		 *     // save
		 *     $ffm_parent->update($parent_row);
		 * }
		 */

		// Assign format informations
		$edit_items = $this->ffm->get_screen_items(FFM::SCREEN_EDIT);
		$ctl->assign("edit_items", $edit_items);
		$ctl->assign("item_setting", $this->ffm->get_all_item_setting());
		$ctl->assign("id_encrypted", $id_encrypted);

		// Add tab for myself
		$ctl->add_tab($this->dialog_name_edit, "parent", "Edit", true,
			[
			    "class" => $ctl->get_classname(),
			    "function" => "edit",
			    "id_encrypted" => $id_encrypted
		]);

		// Add tabs for children

		$child_tables = $this->ffm->get_child_tables();
		foreach ($child_tables as $key => $child_table) {
			if (!($this->ffm->get_list_type() == FFM::LIST_DRAG_DROP && $this->ffm->get_list_child() == $child_table)) {
				$ffm_child = $ctl->db($child_table);
				$child_table_name = $ffm_child->get_table_title();

				$ctl->add_tab($this->dialog_name_edit, $child_table, $child_table_name, false,
					[
					    "class" => $ctl->get_classname(),
					    "function" => "page",
					    "table_name" => $child_table,
					    "parent_id" => $id_encrypted
				]);
			}
		}

		$ctl->show_multi_dialog($this->dialog_name_edit, "edit.tpl", "Edit", 1000, "_edit_button.tpl");
	}

	function edit_exe(Controller $ctl) {
		$id_encrypted = $ctl->POST("id_encrypted");
		$id = $ctl->decrypt($id_encrypted);
		$form_id = $ctl->POST("_form_id");

		// validation
		[$errors, $result] = $this->ffm->validation($ctl->POST(), FFM::SCREEN_EDIT);
		$this->my_validation($errors, $result, $ctl);
		foreach ($errors as $itemname => $error) {
			$ctl->reload_area("#$form_id .error_$itemname", $error);
		}
		if (!$result) {
			return;
		}

		// create update data
		$row = $this->ffm->get($id);
		foreach ($ctl->POST() as $key => $val) {
			$row[$key] = $val;
		}

		// save files
		$this->save_files($ctl, $row, FFM::SCREEN_EDIT);

		// vimeo
		$this->delete_previous_vimeo($ctl, $row);

		// update
		$this->ffm->update($row);

		// close edit screen and reload the list
		$ctl->close_multi_dialog($this->dialog_name_edit);

		$reload_table = $ctl->POST("reload_table");
		$reload_table_id = $ctl->POST("reload_table_id");
		if (empty($reload_table)) {
			$ctl->ajax($ctl->get_classname(), "page", $this->ajax_option);
		} else {
			$this->ajax_option = [];
			$this->ajax_option["parent_id"] = $reload_table_id;
			$this->ajax_option["table_name"] = $reload_table;
			$ctl->ajax($ctl->get_classname(), "page", $this->ajax_option);
		}
	}

	function delete(Controller $ctl) {
		$id_encrypted = $ctl->POST("id_encrypted");
		$id = $ctl->decrypt($id_encrypted);

		$row = $this->ffm->get($id);
		$ctl->assign("values", $row);

		$delete_items = $this->ffm->get_screen_items(FFM::SCREEN_DELETE);
		$ctl->assign("delete_items", $delete_items);
		$ctl->assign("item_setting", $this->ffm->get_all_item_setting());
		$ctl->assign("id_encrypted", $id_encrypted);
		$ctl->show_multi_dialog("delete", "delete.tpl", "Delete", 1000, "_delete_button.tpl");
	}

	function delete_exe(Controller $ctl) {
		$id_encrypted = $ctl->POST("id_encrypted");
		$id = $ctl->decrypt($id_encrypted);

		// run delete
		$this->ffm->delete($id);

		$ctl->close_multi_dialog("delete");

		$reload_table = $ctl->POST("reload_table");
		$reload_table_id = $ctl->POST("reload_table_id");
		if (empty($reload_table)) {
			$ctl->ajax($ctl->get_classname(), "page", $this->ajax_option);
		} else {
			$this->ajax_option = [];
			$this->ajax_option["parent_id"] = $reload_table_id;
			$this->ajax_option["table_name"] = $reload_table;
			$ctl->ajax($ctl->get_classname(), "page", $this->ajax_option);
		}
	}

	function view(Controller $ctl) {
		$id_encrypted = $ctl->POST("id_encrypted");
		$id = $ctl->decrypt($id_encrypted);

		$row = $this->ffm->get($id);
		$ctl->assign("values", $row);

		$items = $this->ffm->get_screen_items(FFM::SCREEN_VIEW);
		$ctl->assign("items", $items);
		$ctl->assign("item_setting", $this->ffm->get_all_item_setting());
		$ctl->assign("id_encrypted", $id_encrypted);
		$ctl->show_multi_dialog("view", "view.tpl", "View", 1000);
	}

	function onchange_update(Controller $ctl) {

		$id = $ctl->POST("id");
		$name = $ctl->POST("name");
		$value = $ctl->POST("value");
		$row = $this->ffm->get($id);
		$row[$name] = $value;

		// validation
		[$errors, $result] = $this->ffm->validation($row, FFM::SCREEN_LIST);
		$this->my_validation($errors, $result, $ctl);
		$msg = "";
		foreach ($errors as $itemname => $error) {
			if ($error != "") {
				$msg .= "$itemname : $error\n";
			}
		}
		if (!$result) {
			$ctl->assign("msg", $msg);
			$ctl->show_notification("notification.tpl");
			return;
		}

		$this->ffm->update($row);
	}

	function sort(Controller $ctl) {

		$log = $ctl->POST("log");
		$ex = explode(",", $log);

		$table_name = $ctl->POST("table_name");

		$parent_id_encrypted = $ctl->POST("parent_id");
		$parent_id = $ctl->decrypt($parent_id_encrypted);
		$parent_id_item = $ctl->POST("table_name") . "_id";

		$child_table_name = $ctl->POST("child_table_name");
		if (!empty($child_table_name)) {
			$ffm_c = $ctl->db($child_table_name);
		} else {
			$ffm_c = $this->ffm;
		}

		$i = 1;
		foreach ($ex as $id) {
			if (!empty($id)) {
				$row = $ffm_c->get($id);
				$row["sort"] = $i;
				$row[$parent_id_item] = $parent_id;
				$i++;
				$ffm_c->update($row);
			}
		}
	}

	function list_monthly_calendar(Controller $ctl) {

		$ctl->assign("schedule_datepicker_d", $ctl->get_session("schedule_datepicker_d"));

		// Getting Item Configurations
		$list_items = $this->ffm->get_screen_items(FFM::SCREEN_LIST);

		// filtered by parent id
		if (!$this->is_parent) {
			$parent_id_encrypted = $ctl->POST("parent_id");
			$parent_id = $ctl->decrypt($parent_id_encrypted);
			$parent_item_name = $this->ffm->get_parent() . "_id";
		} else {
			$parent_item_name = "";
			$parent_id = "";
		}

		$d = $ctl->get_session("YMD-time");
		if (empty($d)) {
			$d = time();
		}
		$d = strtotime(date("Y-m-01", $d));
		$d2 = $d;
		$d = get_beginning_week_date($d); //週の月曜日に変換
		$ctl->assign("time_previous", strtotime("previous month", $d2));
		$ctl->assign("time_next", strtotime("next month", $d2));
		$ctl->assign("time_today", time());
		$ctl->assign("year", date("Y", $d2));
		$dateObj = DateTime::createFromFormat('!m', date('m', $d2));
		$monthName = $dateObj->format('F');
		$ctl->assign("monthName", $monthName);

		$lastday = strtotime("+1 day", strtotime("next sunday", strtotime("last day of this month", $d2)));

		$schedule_arr = array();
		while ($d < $lastday) {
			$dateObj = DateTime::createFromFormat('!m', date('m', $d));
			$monthName = $dateObj->format('F');
			$filter_item = [$parent_item_name, "date"];
			$filter_value = [$parent_id, $d];
			$list = $this->ffm->select($filter_item, $filter_value, true, "AND", "sort", SORT_ASC);
			foreach ($list as $key => &$row) {
				$row["id_encrypted"] = $ctl->encrypt($row["id"]);
			}
			$schedule_arr[] = [
			    "list" => $list,
			    "date" => date("d", $d),
			    "month" => $monthName,
			    "day" => $this->days[$w],
			    "datetime" => $d,
			];
			$d = strtotime("+1 day", $d);
		}

		// Assign datas
		$ctl->assign("schedule_arr", $schedule_arr);

		// Assign format informations 
		$ctl->assign("list_items", $list_items);
		$ctl->assign("item_setting", $this->ffm->get_all_item_setting(true));

		// Show 
		if ($this->is_parent) {
			$ctl->show_main_area("", "list_monthly.tpl", $this->ffm->get_table_title());
		} else {
			$ctl->show_multi_dialog($this->dialog_name, "list_monthly.tpl", "Edit", 1000);
		}
	}

	function list_weekly_calendar(Controller $ctl) {

		// set start hour and end hour
		$START_HOUR = 6;
		$END_HOUR = 22;
		$ctl->assign("start_hour", $START_HOUR);
		$ctl->assign("end_hour", $END_HOUR);

		$ctl->assign("schedule_datepicker_d", $ctl->get_session("schedule_datepicker_d"));

		// Getting Item Configurations
		$list_items = $this->ffm->get_screen_items(FFM::SCREEN_LIST);

		// filtered by parent id
		if (!$this->is_parent) {
			$parent_id_encrypted = $ctl->POST("parent_id");
			$parent_id = $ctl->decrypt($parent_id_encrypted);
			$parent_item_name = $this->ffm->get_parent() . "_id";
		} else {
			$parent_item_name = "";
			$parent_id = "";
		}

		$d = $ctl->get_session("YMD-time");
		if (empty($d)) {
			$d = time();
		}
		$d = get_beginning_week_date($d); //月曜日に変換
		$ctl->assign("time_previous", strtotime("previous week", $d));
		$ctl->assign("time_next", strtotime("next week", $d));
		$ctl->assign("time_today", time());

		$schedule_arr = array();
		for ($i = 0; $i < 7; $i++) {
			$target_time = strtotime($i . " day", $d);
			$w = date('w', $target_time);
			$dateObj = DateTime::createFromFormat('!m', date('m', $target_time));
			$monthName = $dateObj->format('F');
			$filter_item = [$parent_item_name, "date"];
			$filter_value = [$parent_id, $target_time];
			$list = $this->ffm->select($filter_item, $filter_value, true, "AND", "sort", SORT_ASC);
			$timelist = array();
			$occupied = array();
			foreach ($list as $key => &$row) {
				$row["id_encrypted"] = $ctl->encrypt($row["id"]);
				$start_time = $row["start_time"];
				$end_time = $row["end_time"];
				$ex_start = explode(":", $start_time);
				$ex_end = explode(":", $end_time);
				$start_hour = intval($ex_start[0]);
				$end_hour = intval($ex_end[0]);
				if ($ex_end[1] > 0) {
					$end_hour++;
				}
				if ($start_hour < $START_HOUR) {
					$start_hour = $START_HOUR;
				}
				if ($start_hour > $END_HOUR) {
					$start_hour = $END_HOUR;
				}
				$timelist[$start_hour][] = $row;
				$occupied[$start_hour] = true;
				for ($t = $start_hour; $t < $end_hour; $t++) {
					$occupied[$t] = true;
				}
			}
			$schedule_arr[] = [
			    "itemlist" => $timelist,
			    "occupied" => $occupied,
			    "year" => date("Y", $target_time),
			    "month" => $monthName,
			    "date" => date("d", $target_time),
			    "day" => $this->days[$w],
			    "w" => $w,
			    "datetime" => $target_time
			];
		}

		// Assign datas
		$ctl->assign("schedule_arr", $schedule_arr);

		// Assign format informations 
		$ctl->assign("list_items", $list_items);
		$ctl->assign("item_setting", $this->ffm->get_all_item_setting(true));

		// Show 
		if ($this->is_parent) {
			$ctl->show_main_area("", "list_weekly.tpl", $this->ffm->get_table_title());
		} else {
			$ctl->show_multi_dialog($this->dialog_name, "list_weekly.tpl", "Edit", 1000);
		}
	}

	function list_dragdrop(Controller $ctl) {

		// Getting Item Configurations
		$list_items = $this->ffm->get_screen_items(FFM::SCREEN_LIST);

		// filtered by parent id
		if (!$this->is_parent) {
			$parent_id_encrypted = $ctl->POST("parent_id");
			$parent_id = $ctl->decrypt($parent_id_encrypted);
			$parent_item_name = $this->ffm->get_parent() . "_id";
		} else {
			$parent_item_name = [];
			$parent_id = [];
		}

		// get list
		$list = $this->ffm->filter($parent_item_name, $parent_id, false, "AND", "sort", SORT_ASC);

		// get child ffm
		$child_table = $this->ffm->get_list_child();
		$ffm_child = $ctl->db($child_table);
		$parent_id_name = $this->table_name . "_id";

		// iterate for the list
		foreach ($list as $key => &$row) {

			// put encrypted id
			$row["id_encrypted"] = $ctl->encrypt($row["id"]);

			$child_list = $ffm_child->select($parent_id_name, $row["id"], true, "AND", "sort", SORT_ASC);

			// iterate for the child list
			foreach ($child_list as $c_key => &$child) {
				// put encrypted id
				$child["id_encrypted"] = $ctl->encrypt($child["id"]);
				// ------------------------------------
				// You can modify table datas of the array $child here
				// ------------------------------------
			}
			$row["child"] = $child_list;

			// ------------------------------------
			// You can modify table datas of the array $row here
			// ------------------------------------
		}

		// Assign datas
		$ctl->assign("list", $list);

		// Assign format informations 
		$ctl->assign("list_items", $list_items);
		$ctl->assign("parent_item_setting", $this->ffm->get_all_item_setting(true));
		$ctl->assign("child_items", $ffm_child->get_screen_items(FFM::SCREEN_LIST));
		$ctl->assign("child_item_setting", $ffm_child->get_all_item_setting(true));
		$ctl->assign("child_table_name", $child_table);

		// count items for screen type
		$count_add_items = count($ffm_child->get_screen_items(FFM::SCREEN_ADD));
		$ctl->assign("child_count_add_items", $count_add_items);
		$count_view_items = count($ffm_child->get_screen_items(FFM::SCREEN_VIEW));
		$ctl->assign("child_count_view_items", $count_view_items);
		$count_edit_items = count($ffm_child->get_screen_items(FFM::SCREEN_EDIT));
		$ctl->assign("child_count_edit_items", $count_edit_items);
		$count_delete_items = count($ffm_child->get_screen_items(FFM::SCREEN_DELETE));
		$ctl->assign("child_count_delete_items", $count_delete_items);

		// Show 
		if ($this->is_parent) {
			$ctl->show_main_area("", "list_dragdrop.tpl", $this->ffm->get_table_title());
		} else {
			$ctl->show_multi_dialog($this->dialog_name, "list_dragdrop.tpl", "Edit", 1000);
		}
	}

	function list_inline(Controller $ctl) {

		// Getting Item Configurations
		$list_items = $this->ffm->get_screen_items(FFM::SCREEN_LIST);

		// filtered by parent id
		if (!$this->is_parent) {
			$parent_id_encrypted = $ctl->POST("parent_id");
			$parent_id = $ctl->decrypt($parent_id_encrypted);
			$parent_item_name = $this->ffm->get_parent() . "_id";
		} else {
			$parent_item_name = [];
			$parent_id = [];
		}

		// get list
		$list = $this->ffm->filter($parent_item_name, $parent_id, false, "AND", "sort", SORT_ASC);

		// iterate for the list
		foreach ($list as $key => &$row) {

			// put encrypted id
			$row["id_encrypted"] = $ctl->encrypt($row["id"]);

			// ------------------------------------
			// You can modify table datas of the array $row here
			// ------------------------------------
		}

		// Assign datas
		$ctl->assign("list", $list);

		// Assign format informations 
		$ctl->assign("list_items", $list_items);
		$ctl->assign("item_setting", $this->ffm->get_all_item_setting(true));

		// Show 
		if ($this->is_parent) {
			$ctl->show_main_area("", "list_inline.tpl", $this->ffm->get_table_title());
		} else {
			$ctl->show_multi_dialog($this->dialog_name, "list_inline.tpl", "Edit", 1000);
		}
	}

	function list_normal(Controller $ctl) {

		// Getting Item Configurations
		$list_items = $this->ffm->get_screen_items(FFM::SCREEN_LIST);
		$screen_items_search = $this->ffm->get_screen_items(FFM::SCREEN_SEARCH);
		$ctl->assign("screen_items_search", $screen_items_search);

		// set search values from session
		$search_value = [];
		$search_session = $ctl->get_session("search_" . $this->table_name);
		foreach ($screen_items_search as $item) {
			$search_value[] = $search_session[$item];
		}

		// Set sort
		$sortitem = $this->ffm->get_list_sortitem();
		$sort_order = $this->ffm->get_list_order();

		// Get list and assign to the HTML template.
		$max = $ctl->increment_post_value("max", 10);
		if (!$this->is_parent) {
			$parent_id_encrypted = $ctl->POST("parent_id");
			$parent_id = $ctl->decrypt($parent_id_encrypted);
			$parent_item_name = $this->ffm->get_parent() . "_id";
			$screen_items_search[] = $parent_item_name;
			$search_value[] = $parent_id;
		}

		$list = $this->ffm->filter($screen_items_search, $search_value, false, "AND", $sortitem, $sort_order, $max, $is_last);

		// iterate for the list
		foreach ($list as $key => &$row) {

			// put encrypted id
			$row["id_encrypted"] = $ctl->encrypt($row["id"]);

			// ------------------------------------
			// You can modify table datas of the array $row here
			// ------------------------------------
		}

		// Assign datas
		$ctl->assign("list", $list);
		$ctl->assign("max", $max);
		$ctl->assign("is_last", $is_last);

		// Assign format informations 
		$ctl->assign("list_items", $list_items);
		$ctl->assign("item_setting", $this->ffm->get_all_item_setting(true));

		// Values from user input
		$search_values = $ctl->get_session("search_" . $this->table_name);
		$ctl->assign("search_values", $search_values);

		// Show 
		if ($this->is_parent) {
			$ctl->show_main_area("", "list_normal.tpl", $this->ffm->get_table_title());
		} else {
			$ctl->show_multi_dialog($this->dialog_name, "list_normal.tpl", "Edit", 1000);
		}
	}

	function search(Controller $ctl) {
		$ctl->set_session("search_" . $this->table_name, $ctl->POST());
		$ctl->ajax($ctl->get_classname(), "page", $this->ajax_option);
	}

	function search_reset(Controller $ctl) {
		$ctl->set_session("search_" . $this->table_name, array());
		$ctl->ajax($ctl->get_classname(), "page", $this->ajax_option);
	}

	function image(Controller $ctl) {
		$file = $ctl->GET("file");
		$ctl->res_saved_image($file);
	}

	function delete_previous_vimeo(Controller $ctl, &$row) {
		$items = $this->ffm->get_screen_items(FFM::SCREEN_EDIT);
		foreach ($items as $key => $itemname) {
			if ($type == "vimeo") {
				$delete_vimeo_id = $row[$itemname];
				if (!empty($ctl->POST($itemname))) {
					$ctl->delete_vimeo($delete_vimeo_id);
				}
			}
		}
	}

	function save_files($ctl, &$row, $screen) {
		// image or file
		$items = $this->ffm->get_screen_items($screen);
		foreach ($items as $key => $itemname) {

			$filename = $this->table_name . "_" . $itemname . "_" . $row["id"];
			$filename_th = $this->table_name . "_" . $itemname . "_" . $row["id"] . "_" . "th";

			$type = $this->ffm->get_item_type($itemname);
			if ($type == "image") {
				if ($ctl->is_posted_file($itemname)) {

					// Save
					$ctl->save_posted_file($itemname, $filename);
					$row[$itemname] = $filename;

					// resize image
					$image_size = $this->ffm->get_item_param_value($itemname, "image_size");
					$thumbnail_size = $this->ffm->get_item_param_value($itemname, "thumbnail_size");
					$ctl->resize_saved_image($filename, $filename, $image_size, 95);
					$ctl->resize_saved_image($filename, $filename_th, $thumbnail_size, 95);
				}
			} else if ($type == "file") {
				if ($ctl->is_posted_file($itemname)) {
					// Save
					$ctl->save_posted_file($itemname, $filename);
					$row[$itemname] = $ctl->get_posted_filename($itemname);
				}
			}
		}
	}

	function calc_end_time($start_time, $end_time, $change_hour) {

		$ex_start = explode(":", $start_time);
		$ex_end = explode(":", $end_time);
		$s_min = $ex_start[0] * 60 + $ex_start[1];
		$e_min = $ex_end[0] * 60 + $ex_end[1];
		$c_min = $change_hour * 60;
		$diff = $c_min - $s_min;
		$e_min += $diff;

		$h = (int) ($e_min / 60);
		$m = $e_min - $h * 60;
		$ret = sprintf('%02d', $h) . ":" . sprintf('%02d', $m);
		return $ret;
	}

	function change_datetime(Controller $ctl) {
		$date = $ctl->POST("date");
		$time = $ctl->POST("time");
		$log = $ctl->POST("log");
		$ex = explode(",", $log);

		$i = 1;
		foreach ($ex as $id) {
			if (!empty($id)) {
				$row = $this->ffm->get($id);
				$row["sort"] = $i;
				$row["date"] = $date;
				if ($time != -1) {
					$row["end_time"] = $this->calc_end_time($row["start_time"], $row["end_time"], $time);
					$row["start_time"] = sprintf('%02d', $time) . ":00";
				}
				$i++;
				$this->ffm->update($row);
			}
		}
		$ctl->ajax($ctl->get_classname(), "page", $this->ajax_option);
	}

	function move_paste(Controller $ctl) {
		$id = $ctl->get_session("move_id");
		$date = $ctl->POST("date");
		$time = $ctl->POST("time");

		$row = $this->ffm->get($id);
		$row["date"] = $date;
		if ($time != -1) {
			$row["end_time"] = $this->calc_end_time($row["start_time"], $row["end_time"], $time);
			$row["start_time"] = sprintf('%02d', $time) . ":00";
		}
		$this->ffm->update($row);

		$ctl->set_session("move_status", 0);
		$ctl->ajax($ctl->get_classname(), "page", $this->ajax_option);
	}

	function set_datetime(Controller $ctl) {
		$d = $ctl->POST("d");
		if (!is_numeric($d)) {
			$ctl->set_session("schedule_datepicker_d", $d);
			$r = strtotime($d);
			if (!$r) {
				$r = strtotime($d . "/01");
			}
		} else {
			$r = $d;
		}
		$ctl->set_session("YMD-time", $r);
		$ctl->ajax($ctl->get_classname(), "page", $this->ajax_option);
	}

	function move_start(Controller $ctl) {
		$id_encrypted = $ctl->POST("id_encrypted");
		$id = $ctl->decrypt($id_encrypted);

		$ctl->set_session("move_id", $id);
		$ctl->set_session("move_status", 1);

		$ctl->ajax($ctl->get_classname(), "page", $this->ajax_option);
	}

	function move_cancel(Controller $ctl) {
		$ctl->set_session("move_status", 0);
		$ctl->ajax($ctl->get_classname(), "page", $this->ajax_option);
	}

	public function __construct(Controller $ctl) {

		$this->table_name = $ctl->POST("table_name");
		if (empty($this->table_name) || $ctl->POST("table_name") == $ctl->get_classname()) {
			$this->table_name = $ctl->get_classname();
			$this->is_parent = true;
			$this->ffm = $ctl->db($this->table_name);
			$this->ffm->set_filter_zero(true);
			$this->dialog_name = "edit_" . $this->table_name;
			$this->dialog_name_edit = "edit_" . $this->table_name;
		} else {
			$this->ffm = $ctl->db($this->table_name);
			$this->ffm->set_filter_zero(true);
			$this->dialog_name = "edit_" . $this->ffm->get_parent();
			$this->dialog_name_edit = "edit_" . $this->table_name;
		}

		$ctl->assign("table_name", $this->table_name);
		$ctl->assign("parent_id", $ctl->POST("parent_id"));

		// These parameter is needed when a function ajax is called.
		// So. When you add a button with class ajax, you must add parameters 'data-table_name="{$table_name}" data-parent_id="{$parent_id}"'.
		if (!empty($ctl->POST("reload_table"))) {
			$this->ajax_option = ["table_name" => $this->table_name, "reload_table" => $ctl->POST("reload_table"), "reload_table_id" => $ctl->POST("reload_table_id"), "parent_id" => $ctl->POST("parent_id")];
		} else {
			$this->ajax_option = ["table_name" => $this->table_name, "parent_id" => $ctl->POST("parent_id")];
		}

		$ctl->assign("reload_table", $ctl->POST("reload_table"));
		$ctl->assign("reload_table_id", $ctl->POST("reload_table_id"));

		// count items for screen type
		$count_add_items = count($this->ffm->get_screen_items(FFM::SCREEN_ADD));
		$ctl->assign("count_add_items", $count_add_items);
		$count_view_items = count($this->ffm->get_screen_items(FFM::SCREEN_VIEW));
		$ctl->assign("count_view_items", $count_view_items);
		$count_edit_items = count($this->ffm->get_screen_items(FFM::SCREEN_EDIT));
		$ctl->assign("count_edit_items", $count_edit_items);
		$count_delete_items = count($this->ffm->get_screen_items(FFM::SCREEN_DELETE));
		$ctl->assign("count_delete_items", $count_delete_items);
	}
}
