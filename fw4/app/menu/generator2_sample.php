<?php

class generator2_sample {

	private $ffm;
	private $table_name;
	private $is_parent = false;
	private $dialog_name;
	private $dialog_name_edit;
	private $ajax_option;

	function page(Controller $ctl) {

		$list_type = $this->ffm->get_list_type();

		/*
		 * You can get table name by $this->table_name to make a condition.
		 * You can duplicate functions like list_xxx and call new function by the condition.
		 */

		if ($list_type == FFM::LIST_NORMAL) {
			$this->list_normal($ctl);
		} else if ($list_type == FFM::LIST_INLINE) {
			$this->list_inline($ctl);
		} else if ($list_type == FFM::LIST_DRAG_DROP) {
			$this->list_dragdrop($ctl);
		} else if ($list_type == FFM::LIST_WEEKLY_CALENDAR) {
			$this->list_weekly($ctl);
		} else if ($list_type == FFM::LIST_MONTHLY_CALENDAR) {
			$this->list_monthly($ctl);
		}
	}

	// Show list inline
	function list_weekly(Controller $ctl) {

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
			$ctl->show_multi_dialog($this->dialog_name, "list_inline.tpl", "Edit", 800);
		}
	}

	/*
	 * You can add your validation code here
	 */

	function my_validation(&$errors, &$result, $ctl) {
		// ex)
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
		$ctl->show_multi_dialog("add", "add.tpl", "Add", 800, "_add_button.tpl");
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
		$ctl->ajax($ctl->get_classname(), "page", $this->ajax_option);
	}

	function edit(Controller $ctl) {
		$id_encrypted = $ctl->POST("id_encrypted");
		$id = $ctl->decrypt($id_encrypted);

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

		$ctl->show_multi_dialog($this->dialog_name_edit, "edit.tpl", "Edit", 800, "_edit_button.tpl");
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
		$ctl->ajax($ctl->get_classname(), "page", $this->ajax_option);
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
		$ctl->show_multi_dialog("delete", "delete.tpl", "Delete", 800, "_delete_button.tpl");
	}

	function delete_exe(Controller $ctl) {
		$id_encrypted = $ctl->POST("id_encrypted");
		$id = $ctl->decrypt($id_encrypted);

		// run delete
		$this->ffm->delete($id);

		$ctl->close_multi_dialog("delete");
		$ctl->ajax($ctl->get_classname(), "page", $this->ajax_option);
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
		$ctl->show_multi_dialog("view", "view.tpl", "View", 800);
	}

	// Update on change
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

	// save sort order
	function sort(Controller $ctl) {

		$log = $ctl->POST("log");
		$ex = explode(",", $log);

		$parent_id_encrypted = $ctl->POST("parent_id");
		$parent_id = $ctl->decrypt($parent_id_encrypted);
		$parent_id_item = $ctl->POST("parent_table_name") . "_id";

		$i = 1;
		foreach ($ex as $id) {
			if (!empty($id)) {
				$row = $this->ffm->get($id);
				$row["sort"] = $i;
				$row[$parent_id_item] = $parent_id;
				$i++;
				$this->ffm->update($row);
			}
		}
	}

	// List combine
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
			$ctl->show_multi_dialog($this->dialog_name, "list_dragdrop.tpl", "Edit", 800);
		}
	}

	// Show list inline
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
			$ctl->show_multi_dialog($this->dialog_name, "list_inline.tpl", "Edit", 800);
		}
	}

	// Show list normal
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
			$ctl->show_multi_dialog($this->dialog_name, "list_normal.tpl", "Edit", 800);
		}
	}

	// Set search values;
	function search(Controller $ctl) {
		$ctl->set_session("search_" . $this->table_name, $ctl->POST());
		$ctl->ajax($ctl->get_classname(), "page", $this->ajax_option);
	}

	// Reset search values
	function search_reset(Controller $ctl) {
		$ctl->set_session("search_" . $this->table_name, array());
		$ctl->ajax($ctl->get_classname(), "page", $this->ajax_option);
	}

	// Download image which has uploaded
	function image(Controller $ctl) {
		$file = $ctl->GET("file");
		$ctl->res_saved_image($file);
	}

	// Delete old vimeo
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

	// Save images and files
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
			$this->ajax_option = ["table_name" => $ctl->POST("reload_table"), "parent_id" => $ctl->POST("parent_id")];
		} else {
			$this->ajax_option = ["table_name" => $this->table_name, "parent_id" => $ctl->POST("parent_id")];
		}

		$ctl->assign("reload_table", $ctl->POST("reload_table"));

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
