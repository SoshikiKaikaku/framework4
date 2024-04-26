<?php

class ai_settings {

	private $fmt_ai_setting;
	private $fmt_ai_setting_parameters;
	private $fmt_ai_setting_subtable;
	private $fmt_ai_db;
	private $fmt_ai_fields;
	private $ai_text_flg_opt = [
	    0 => "Manual",
	    1 => "CSV"
	];
	private $limit_user_type_opt = [
	    0 => "Allow ALL",
	    1 => "Limited"
	];
	private $limit_workflow_opt = [
	    0 => "Allow ALL",
	    1 => "Limited"
	];
	private $upload_list = [
	    0 => "Add new data",
	    1 => "Delete exiting data befor upload"
	];

	private $ai_type_opt = [
	    0 => "Field",
	    1 => "Text",
	    2 => "Subtable"
	];
	
	private $manual_crawl_opt = [0 => "Manual", 1 => "Crawl",];
	private $sitemap_opt = [1 => "Site Map for AI Training",];


	private $predefined_function_opt = [
	    1 => "Login",
	    2 => "Payment",
	    3 => "AI Training Text ",
	    4 => "Create Account",
	    5 => "Sitemap for AI Training"
	];
	private $type_opt = [
	    1 => "Database Handling",
	    0 => "Execute Original Code",
	    2 => "Predefined Functions"
	];
	private $database_handling_opt = [
	    1 => "Add",
	    2 => "Edit",
	    3 => "Delete",
	    4 => "Search",
	    5 => "Table",
	    6 => "PDF",
	    7 => "CSV",
	    8 => "Mail",
	];
	private $design_type_opt = [
	    1 => "Form(Add/Edit)",
	    2 => "View",
	    3 => "Table",
	    4 => "PDF",
	    5 => "CSV",
	    6 => "Mail",
	];
	private $pdf_orientation_opt = [
	    'L' => "Landscape",
	    'P' => "Portrait"
	];
	//mincho/Pmincho/EXmincho/Pgothic/gothic/EXgothic/migmix-1p-bold/migmix-1p-regular/gnu
	private $pdf_font_opt = [
	    'mincho' => "mincho",
	    'Pmincho' => "Pmincho",
	    'EXmincho' => "EXmincho",
	    'Pgothic' => "Pgothic",
	    'gothic' => "gothic",
	    'EXgothic' => "EXgothic",
	    'migmix-1p-bold' => "migmix-1p-bold",
	    'migmix-1p-regular' => "migmix-1p-regular",
	    'gnu' => "gnu",
	];
	private $pagenumber_opt = [
	    'on' => "on",
	    'off' => "off",
	];
	private $pagenumber_firstpage_opt = [
	    'on' => "on",
	    'off' => "off",
	];
	private $publish_opt = [
	    'off' => "off",
	    'on' => "on",
	];
	private $img_grayscale_opt = [
	    'off' => "off",
	    'on' => "on",
	];
	// L:left, R:right C:center
	private $align_opt = [
	    'L' => "left",
	    'R' => "right",
	    'C' => "center",
	];
	//B:Bottom T:Top BLTR:RECT
	private $border_opt = [
	    '' => "None",
	    'B' => "Bottom",
	    'T' => "Top",
	    'BLTR' => "RECT",
	];
	private $validation_opt = [0 => "No required", 1 => "Required"];
	private $workon_opt = [1 => "Both", 2 => "Management Side", 3 => "Public Side"];
	private $code_type_opt = [
	    1 => "Called from AI",
	    2 => "Cron (Once an Hour)",
	    3 => "Initialize Chat"
	];
	private $change_after_workflow_status_opt = [0=>"No change",1=>"Change"];
	private $change_after_type_opt = [0=>"Users specify the type at the time of account registration.",1=>"Specify the type"];

	function __construct(Controller $ctl) {
		$this->fmt_ai_db = $ctl->db("ai_db", "ai_db");
		$this->fmt_ai_fields = $ctl->db("ai_fields", "ai_db");
		$this->fmt_ai_setting = $ctl->db("ai_setting");
		$this->fmt_ai_setting_parameters = $ctl->db("ai_setting_parameters");
		$this->fmt_ai_setting_subtable = $ctl->db("ai_setting_subtable");
		$ctl->assign('validation_opt', $this->validation_opt);
		$ctl->assign('workon_opt', $this->workon_opt);
		$ctl->assign('type_opt', $this->type_opt);
		$ctl->assign('database_handling_opt', $this->database_handling_opt);
		$ctl->assign('tables_opt', $this->get_tables_opt());
		$ctl->assign("design_type_opt", $this->design_type_opt);
		$ctl->assign("code_type_opt", $this->code_type_opt);
		$ctl->assign("pdf_orientation_opt", $this->pdf_orientation_opt);
		$ctl->assign("pdf_font_opt", $this->pdf_font_opt);
		$ctl->assign("pagenumber_opt", $this->pagenumber_opt);
		$ctl->assign("pagenumber_firstpage_opt", $this->pagenumber_firstpage_opt);
		$ctl->assign("publish_opt", $this->publish_opt);
		$ctl->assign("img_grayscale_opt", $this->img_grayscale_opt);
		$ctl->assign("align_opt", $this->align_opt);
		$ctl->assign("border_opt", $this->border_opt);
		$ctl->assign("ai_type_opt", $this->ai_type_opt);
		$ctl->assign("limit_workflow_opt", $this->limit_workflow_opt);
		$ctl->assign("limit_user_type_opt", $this->limit_user_type_opt);
		$ctl->assign("predefined_function_opt", $this->predefined_function_opt);
		$ctl->assign("func_status_opt", $this->func_status_opt);
		$settings = $ctl->get_setting();
		$ctl->assign("currency", $settings['currency']);
		$ctl->assign("upload_list", $this->upload_list);
		$ctl->assign("change_after_workflow_status_opt", $this->change_after_workflow_status_opt);
		$ctl->assign("change_after_type_opt",$this->change_after_type_opt);
		$ctl->assign("ai_text_flg_opt", $this->ai_text_flg_opt);
		$ctl->assign("manual_crawl_opt", $this->manual_crawl_opt);
		$ctl->assign("sitemap_opt", $this->sitemap_opt);
		$ctl->assign("user_type_opt",$ctl->get_constant_array("user_type_opt"));

	}

	function get_tables_opt() {

		$list = $this->fmt_ai_db->getall("sort", SORT_ASC);
		$opt = [];
		foreach ($list as $key => $d) {
			$opt[$d["id"]] = $d["tb_name"];
		}
		return $opt;
	}

	//index page
	function page(Controller $ctl) {

		$post = $ctl->POST();
		$ctl->assign('post', $post);
		$max = $ctl->increment_post_value('max', 10);
		$items = $this->fmt_ai_setting->filter(["class_name", "function_name"], [$post["search_class_name"], $post["search_function_name"]], false, 'AND', 'sort', SORT_ASC, $max, $is_last);

		$user_type_opt = $ctl->get_constant_array('user_type_opt');
		$workflow_status_opt = $ctl->get_constant_array('workflow_status_opt');

		foreach ($items as $key1 => $value1) {
			//var_dump($value1['user_type']);
			$items[$key1]['en_id'] = $ctl->encrypt($value1['id']);
			if ($value1['user_type']) {
				$arr_u = [];
				foreach ($value1['user_type'] as $key => $value) {
					$arr_u[] = $user_type_opt[$value];
				}
				$items[$key1]['user_type'] = $arr_u;
			}
			if ($value1['workflow_status']) {
				$arr_w = [];
				foreach ($value1['workflow_status'] as $key => $value) {
					$arr_w[] = $workflow_status_opt[$value];
				}
				$items[$key1]['workflow_status'] = $arr_w;
			}
		}
		$ctl->assign("max", $max);
		$ctl->assign("is_last", $is_last);
		$ctl->assign("items", $items);

		$ai_db_list = $this->fmt_ai_db->getall();
		$ai_db_opt = [];
		foreach ($ai_db_list as $ai_db) {
			$ai_db_opt[$ai_db["id"]] = $ai_db["tb_name"];
		}
		$ctl->assign("ai_db_opt", $ai_db_opt);

		$ctl->show_main_area("ai_setting", "index.tpl", "AI Setting");
	}

	//view add page
	function add_step1(Controller $ctl) {
		$post = $ctl->POST();
		$ctl->assign('post', $post);
		$ctl->show_multi_dialog("add_ai_setting", "add_step1.tpl", "Add AI Setting", 1000, true, true);
	}

	//view add page
	function add_step2(Controller $ctl) {

		$post = $ctl->POST();
		$ctl->assign('post', $post);
		if ($post["type"] == 0) {
			$ctl->show_multi_dialog("add_ai_setting", "add_select_code_type.tpl", "Add AI Setting", 1000, true, true);
			return;
		} else {
			$ctl->show_multi_dialog("add_ai_setting", "add_step2.tpl", "Add AI Setting", 1000, true, true);
		}
	}

	//view add page
	function add_step3(Controller $ctl) {

		$post = $ctl->POST();
		$ctl->assign('post', $post);
		$ctl->show_multi_dialog("add_ai_setting", "add_step2.tpl", "Add AI Setting", 1000, true, true);
	}

	function add_pre_func(Controller $ctl) {

		$post = $ctl->POST();
		$ctl->assign('post', $post);
		$ctl->assign('data', $post);
		$list = $this->fmt_ai_setting->getall("sort", SORT_ASC);
		if ($post['predefined_function'] == 4 || $post['predefined_function'] == 1) {
			foreach ($list as $key => $value) {
				if ($value['predefined_function'] == $post['predefined_function']) {
					$txt = $this->predefined_function_opt[$value['predefined_function']] . ' is already defined.';
					$ctl->show_notification_text($txt);
					return;
				}
			}
		}

		$ctl->show_multi_dialog("add_ai_setting", "add_pre_func.tpl", "Add AI Setting", 1000, true, true);
	}

	//save add data
	function add_exe(Controller $ctl) {
		$post = $ctl->POST();
		$ctl->assign('post', $post);
		//var_dump($post);
		//validation
		$errors = $this->validate_ai_setting_data($post);
		if (count($errors)) {
			$ctl->assign('errors', $errors);
			if ($post['type'] == 2) {
				$this->add_pre_func($ctl);
			} else {
				$this->add_step2($ctl);
			}
			return;
		}

		$post['created_at'] = time();
		$ctl->console_log($post);
		$id = $this->fmt_ai_setting->insert($post);

		// resort
		$list = $this->fmt_ai_setting->getall("sort", SORT_ASC);
		$c = 0;
		foreach ($list as $d) {
			$c++;
			$d["sort"] = $c;
			$this->fmt_ai_setting->update($d);
		}

		//close adding page
		$ctl->close_multi_dialog("add_ai_setting");

		$this->page($ctl);
	}

	//validation
	function validate_ai_setting_func($post) {
		$errors = [];

		if (empty($post["function_name"])) {
			$errors["function_name"] = "Function Name is required!";
		}

		return $errors;
	}

	function validate_ai_setting_data($post) {
		$errors = [];

		if ($post["type"] == 1) {
			if (empty($post["ai_db_id"])) {
				$errors["ai_db_id"] = "Table is required!";
			}
		}

		if ($post["type"] == 0) {
			if (empty($post["class_name"])) {
				$errors["class_name"] = "Class Name is required!";
			}

			if (empty($post["function_name"])) {
				$errors["function_name"] = "Function Name is required!";
			}
		}

		if ($post["type"] == 2) {

			if ($post["predefined_function"] == 2 && empty($post["price"])) {
				$errors["price"] = "Price is required!";
			}
			if ($post["predefined_function"] == 3 && empty($post["ai_title"])) {
				$errors["ai_title"] = "Title is required!";
			}
			if ($post["predefined_function"] == 3 && empty($post["ai_text"])) {
				$errors["ai_text"] = "Text is required!";
			}
		}



		return $errors;
	}

	//view edit page
	function edit(Controller $ctl) {
		$post = $ctl->POST();
		$id = $post["id"];

		$data = $this->fmt_ai_setting->get($id);
		$data_aidb = $this->fmt_ai_db->get($data['ai_db_id']);
		if ($data_aidb['parent_tb_id']) {
			$f_pdb = $this->fmt_ai_fields->select("ai_db_id", $data_aidb['parent_tb_id'], true, "AND", "sort", SORT_ASC);
			foreach ($f_pdb as $key => $value) {
				$parent_fields[$value['id']] = $value['parameter_title'];
			}
			//var_dump($f_pdb);
			$ctl->assign("parent_field_opt", $parent_fields);
		}
		if (!$data['pdf_pagesize']) {
			!$data['pdf_pagesize'] = '210x297mm';
		}
		if (!$data['page_margin_top']) {
			!$data['page_margin_top'] = '20';
		}
		if (!$data['page_margin_left']) {
			!$data['page_margin_left'] = '30';
		}
		if (!$data['page_margin_right']) {
			!$data['page_margin_right'] = '30';
		}
		if (!$data['page_margin_bottom']) {
			!$data['page_margin_bottom'] = '23';
		}
		if (!$data['pagenumber_y_position']) {
			!$data['pagenumber_y_position'] = '0';
		}

		$ctl->assign("data", $data);

		$parameters = $this->fmt_ai_setting_parameters->select('ai_setting_id', $id, false, "AND", 'sort', SORT_ASC);

		foreach ($parameters as $key => $p) {
			$f = $this->fmt_ai_fields->get($p["ai_fields_id"]);
			$data_ai = $this->fmt_ai_db->get($p['subtable_id']);
			$parameters[$key]["fields"] = $f;
			$parameters[$key]["sub_tb_name"] = $data_ai['tb_name'];
		}
		$ctl->assign("parameters", $parameters);
		/* subtable */
		$data_ai = $this->fmt_ai_db->select("parent_tb_id", $ai_db_id, true, "AND", "sort", SORT_ASC);
		$subtb_arr = [];
		foreach ($data_ai as $key => $value) {
			$subtb_arr[$value['id']] = $value['tb_name'];
		}
		$ctl->assign("subtable_opt", $subtb_arr);
		/* subtable */

		$ctl->show_multi_dialog("edit_ai_setting_" . $id, "edit.tpl", "Edit AI Setting", 1000, "_edit_button.tpl", true);
	}

	//save edited data
	function edit_exe(Controller $ctl) {
		$post = $ctl->POST();
		$ctl->assign('data', $post);

		//validation
		$errors = $this->validate_ai_setting_data($post);
		if (count($errors)) {
			$ctl->assign('errors', $errors);
			$this->edit($ctl);
			return;
		}
		$data = $this->fmt_ai_setting->get($post['id']);
		foreach ($_POST as $key => $value) {
			$data[$key] = $value;
		}

		$data['updated_at'] = time();
		$this->fmt_ai_setting->update($data);

		$ctl->close_multi_dialog("edit_ai_setting_" . $post['id']);
		$this->page($ctl);
	}

	//view delete page
	function delete(Controller $ctl) {
		$id = $ctl->POST("id");
		$data = $this->fmt_ai_setting->get($id);
		$ctl->assign("data", $data);
		$ctl->show_multi_dialog("delete", "delete.tpl", "Delete AI Setting", 500, true, true);
	}

	//delete data form database
	function delete_exe(Controller $ctl) {
		$id = $ctl->POST("id");
		//file delete
		$data = $this->fmt_ai_setting->get($id);

		//deleting child data
		$this->fmt_ai_setting->delete($id);

		$ctl->close_multi_dialog("delete");
		$this->page($ctl);
	}

	function sort(Controller $ctl) {
		$post = $ctl->POST();
		$logArr = explode(',', $post['log']);
		$c = 1;
		foreach ($logArr as $id) {
			$d = $this->fmt_ai_setting->get($id);
			$d['sort'] = $c;
			$this->fmt_ai_setting->update($d);
			$c++;
		}
	}

	function sort_parameters(Controller $ctl) {
		$post = $ctl->POST();
		$logArr = explode(',', $post['log']);
		$c = 1;
		foreach ($logArr as $id) {
			$d = $this->fmt_ai_setting_parameters->get($id);
			$d['sort'] = $c;
			$this->fmt_ai_setting_parameters->update($d);
			$c++;
		}
	}

	//view add page
	function add_parameters(Controller $ctl) {
		$post = $ctl->POST();
		$post['ai_setting_id'] = $post['id'];
		$ai_setting = $this->fmt_ai_setting->get($post["id"]);
		$ai_db_id = $ai_setting["ai_db_id"];
		$fields_list = $this->fmt_ai_fields->select("ai_db_id", $ai_db_id, true, "AND", "sort", SORT_ASC);
		$fields_opt = [];
		foreach ($fields_list as $f) {
			$fields_opt[$f["id"]] = $f["parameter_name"] . " / " . $f["parameter_title"];
		}
		/* subtable */
		$data_ai = $this->fmt_ai_db->select("parent_tb_id", $ai_db_id, true, "AND", "sort", SORT_ASC);
		$subtb_arr = [];
		foreach ($data_ai as $key => $value) {
			$subtb_arr[$value['id']] = $value['tb_name'];
		}
		$ctl->assign("subtable_opt", $subtb_arr);
		/* subtable */
		$ctl->assign("fields_opt", $fields_opt);
		$ctl->assign("ai_setting", $ai_setting);
		$ctl->assign('data', $post);
		$ctl->show_multi_dialog("add_ai_setting_parameters", "add_parameters.tpl", "Add AI Setting Parameters", 1000, true, true);
	}

	//save add data
	function add_parameters_exe(Controller $ctl) {
		$post = $ctl->POST();
		$ctl->assign('post', $post);

		$ai_setting = $this->fmt_ai_setting->get($post['ai_setting_id']);

		if ($ai_setting["handling"] == 4) {
			$field = $this->fmt_ai_fields->get($post['ai_fields_id']);
			if ($field["type"] == 'image' || $field["type"] == 'vimeo' || $field["type"] == 'file' || $field["type"] == 'checkbox') {
				$txt = "Can't search " . $field["type"] . " field!";
				$ctl->show_notification_text($txt);
				return;
			}
		}


		if ($ai_setting["type"] == 1) {
			// DB
			$ctl->console_log($post);
			$id = $this->fmt_ai_setting_parameters->insert($post);
		} else {
			// CODE
			//validation
			$errors = $this->validate_ai_setting_parameters_data($ctl, $post, "add_parameters");
			if (count($errors)) {
				$ctl->assign('errors', $errors);
				$this->add_parameters($ctl);
				return;
			}
			$id = $this->fmt_ai_setting_parameters->insert($post);
		}

		// resort
		$list = $this->fmt_ai_setting_parameters->getall("sort", SORT_ASC);
		$c = 0;
		foreach ($list as $d) {
			$c++;
			$d["sort"] = $c;
			$this->fmt_ai_setting_parameters->update($d);
		}

		//close adding page
		$ctl->close_multi_dialog("add_ai_setting_parameters");
		$ctl->ajax("ai_settings", "edit", ["id" => $post['ai_setting_id']]);
	}

	//validation
	function validate_ai_setting_parameters_data(Controller $ctl, $post, $page) {
		$errors = [];

		if (empty($post["parameter_name"]) && empty($post["text"]) && empty($post["subtable_id"])) {
			$errors["parameter_name"] = "Parameter Name is required!";
		}



		return $errors;
	}

	function edit_parameters(Controller $ctl) {

		$id = $ctl->POST("id");
		$data = $this->fmt_ai_setting_parameters->get($id);
		$ai_setting = $this->fmt_ai_setting->get($data["ai_setting_id"]);
		$selected_field = $this->fmt_ai_fields->get($data["ai_fields_id"]);
		$ai_db_id = $ai_setting["ai_db_id"];
		$fields_list = $this->fmt_ai_fields->select("ai_db_id", $ai_db_id, true, "AND", "sort", SORT_ASC);
		$fields_opt = [];
		foreach ($fields_list as $f) {
			$fields_opt[$f["id"]] = $f["parameter_name"] . " / " . $f["parameter_title"];
		}
		/* subtable */
		$data_ai = $this->fmt_ai_db->get($data['subtable_id']);
		$ctl->assign("subtable", $data_ai);
		/* subtable */
		/* sub table fields */
		$subtb_fields = $this->fmt_ai_fields->select("ai_db_id", $data['subtable_id'], true, "AND", "sort", SORT_ASC);
		$subtb_fields_arr = [];
		foreach ($subtb_fields as $key => $value) {
			$subtb_fields_arr[$value['id']] = $value['parameter_title'];
		}
		$ctl->assign("ai_sub_fields_opt", $subtb_fields_arr);
		/* sub table fields */

		/* get sub table */
		$subtb_details = $this->fmt_ai_setting_subtable->select("ai_settings_parameter_id", $id, true, "AND", "sort", SORT_ASC);
		foreach ($subtb_details as $key => $value) {
			$subtb_fields2 = $this->fmt_ai_fields->get($value['ai_sub_field_id']);
			$subtb_details[$key]['parameter_title'] = $subtb_fields2['parameter_title'];
		}

		$ctl->assign("subtb_details", $subtb_details);

		if (!$data['margintop']) {
			!$data['margintop'] = '1';
		}
		if (!$data['marginbottom']) {
			!$data['marginbottom'] = '0';
		}
		if (!$data['marginleft']) {
			!$data['marginleft'] = '0';
		}
		if (!$data['marginright']) {
			!$data['marginright'] = '0';
		}
		if (!$data['lineheight']) {
			!$data['lineheight'] = '4';
		}
		if (!$data['fontsize']) {
			!$data['fontsize'] = '10';
		}
		if (!$data['before']) {
			!$data['before'] = $selected_field['parameter_title'] . ':';
		}
		if (!$data['rotate']) {
			!$data['rotate'] = '0';
		}
		if ($data['para_type'] == 0) {
			$data['field_type'] = $selected_field['type'];
		}

		$ctl->assign("fields_opt", $fields_opt);
		$ctl->assign("selected_field", $selected_field);
		$ctl->assign("data", $data);
		$ctl->assign("ai_setting", $ai_setting);

		$ctl->show_multi_dialog("edit_ai_setting_parameters_" . $id, "edit_parameters.tpl", "Edit AI Setting Parameters", 1000, "_para_edit_btn.tpl", true);
	}

	function edit_parameters_exe(Controller $ctl) {
		$post = $ctl->POST();
		$ctl->assign('data', $post);

		//validation
		$errors = $this->validate_ai_setting_parameters_data($ctl, $post, "edit_parameters");
		if (count($errors)) {
			$ctl->assign('errors', $errors);
			$this->edit_parameters($ctl);
			return;
		}
		$data = $this->fmt_ai_setting_parameters->get($post['id']);
		foreach ($post as $key => $value) {
			$data[$key] = $value;
		}

		$data['updated_at'] = time();
		$this->fmt_ai_setting_parameters->update($data);

		$ctl->close_multi_dialog("edit_ai_setting_parameters_" . $post['id']);
		$this->page($ctl);
	}

	//view delete page
	function delete_parameters(Controller $ctl) {
		$id = $ctl->POST("id");
		$data = $this->fmt_ai_setting_parameters->get($id);
		$ctl->assign("data", $data);
		$ctl->show_multi_dialog("delete_parameters", "delete_parameters.tpl", "Delete AI Setting Parameters", 500, true, true);
	}

	//delete data form database
	function delete_parameters_exe(Controller $ctl) {
		$id = $ctl->POST("id");
		//file delete
		$data = $this->fmt_ai_setting_parameters->get($id);

		//deleting child data		
		$this->fmt_ai_setting_parameters->delete($id);
		$ctl->close_multi_dialog("delete_parameters");

		$ctl->ajax("ai_settings", "edit", ["id" => $data['ai_setting_id']]);
	}

	function img(Controller $ctl) {
		$ctl->res_image("images", $ctl->GET("file"));
	}

	function add_subtable(Controller $ctl) {
		$post = $ctl->POST();
		$id = $this->fmt_ai_setting_subtable->insert($post);
		$ctl->ajax("ai_settings", "edit_parameters", ["id" => $post['ai_settings_parameter_id']]);
		//var_dump($post);
	}

	function edit_subtable_exe(Controller $ctl) {
		$post = $ctl->POST();
		$ctl->assign('data', $post);

		$data = $this->fmt_ai_setting_subtable->get($post['id']);
		foreach ($post as $key => $value) {
			$data[$key] = $value;
		}

		$data['updated_at'] = time();
		$this->fmt_ai_setting_subtable->update($data);
		$ctl->ajax("ai_settings", "edit_parameters", ["id" => $data['ai_settings_parameter_id']]);
	}

	function delete_sub_parameters(Controller $ctl) {
		$id = $ctl->POST("id");
		$data = $this->fmt_ai_setting_subtable->get($id);
		$ctl->assign("data", $data);
		$ctl->show_multi_dialog("delete_parameters_sub", "delete_parameters_sub.tpl", "Delete AI Setting subtable", 500, true, true);
	}

	//delete data form database
	function delete_sub_parameters_exe(Controller $ctl) {
		$id = $ctl->POST("id");
		//file delete
		$data = $this->fmt_ai_setting_subtable->get($id);

		//deleting child data		
		$this->fmt_ai_setting_subtable->delete($id);
		$ctl->close_multi_dialog("delete_parameters_sub");

		$ctl->ajax("ai_settings", "edit_parameters", ["id" => $data['ai_settings_parameter_id']]);
	}
	
	function upload_csv(Controller $ctl) {
		$post = $ctl->POST();
		$ctl->assign('post', $post);
		$code_list = ["UTF-8"=>"UTF-8(Google SpreadSheet/Mac)","win"=>"Windows(Excel)"];
		$ctl->assign("code_list",$code_list);
		$ctl->show_multi_dialog("upload_csv", "upload_csv.tpl", "Upload CSV", 800);
	}
	
	function upload_csv_confirm(Controller $ctl){
	    
	    $post = $ctl->POST();
	    $ctl->assign("data",$post);
	    
		if (!$ctl->is_posted_file("ai_training_csv")){
			if (empty($post["ai_training_csv"])){
				$errors["ai_training_csv"] = "必須項目です";
			}
			$ctl->assign("errors",$errors);
			$this->upload_csv($ctl);
			return;
		}
		
		$ctl->save_posted_file("ai_training_csv", "ai_training_csv.csv");
		$filepath = $ctl->get_saved_filepath("ai_training_csv.csv");

		//open saved file
		$fp = fopen($filepath,"r");

		//set encoding for japanese
		if($ctl->POST("code") == "win"||$ctl->POST("code") == "UTF-8"){
			stream_filter_register("convert.mbstring.*", "Stream_Filter_Mbstring");
			$filter_name = 'convert.mbstring.encoding.SJIS-win:UTF-8';
			stream_filter_append($fp, $filter_name, STREAM_FILTER_READ);
		}

		

		//read each line as csv
		$first = true;
		$list = [];
		$next_flg=true;
		while ($row = fgetcsv($fp)){
			
			$errors = [];
			
			if ($first){
				$first = false;
				continue;
			}

//			if (empty($row[0])){
//				$errors[] = "Name is empty";
//			}
//			if (empty($row[1])){
//				$errors[] = "Email is empty";
//			}
//			if (!filter_var($row[1], FILTER_VALIDATE_EMAIL)){
//				$errors[] = "Incorrect email format";
//			}
//			$users = $this->fmt_user->select(["login_id"], [$row[1]], true);
//			if(count($users) > 0){
//				$errors[] = "Email is duplicated";
//			}
			
			$rec = [
			    "errors" => $errors,
			    "title" => $row[0],
			    "text" => $row[1],
			];
			
			if(count($errors)>0){
				$next_flg=false;
			}

			$list[] = $rec;


		}
		
		$ctl->set_session("ai_list", $list);
		$ctl->assign("list",$list);
		$ctl->assign("next_flg",$next_flg);
		
		$ctl->show_multi_dialog("upload_csv", "upload_confirm.tpl", "Upload CSV", 800);

		fclose($fp);
	}
	
	
	function upload_csv_exe(Controller $ctl) {
		
		//$settings = $this->fmt_chapter_setting->getall()[0];
		$post = $ctl->POST();
		$list = $ctl->get_session("ai_list");
		
		if($post['upload_option']==1){
		    $ai_text = $this->fmt_ai_setting->select(['type','predefined_function','ai_text_flg'], [2,3,1]);
		    foreach ($ai_text as $key => $value) {
			$this->fmt_ai_setting->delete($value['id']);
		    }
		}

		foreach($list as $rec){       
			$insert_data=[
				'type'=>2,
				'predefined_function'=>3,
				'ai_title'=>$rec["title"],
				'ai_text'=>$rec["text"],
				'created_at' => time()
			];
			
			$this->fmt_ai_setting->insert($insert_data);
			//$this->fmt_user->insert($insert_data);

			try{
				//$this->send_welcome_email($ctl,$insert_data);
			}catch(Exception $e){
				echo $e;
			}
		}
		
		$ctl->assign("count",count($list));
		$ctl->show_multi_dialog("upload_csv", "upload_finish.tpl", "Upload CSV", 800);
		$this->page($ctl);
	}
	
	function image_sample(Controller $ctl){
		$ctl->res_image("images", "sample2.png");
	}

//	function download_json(Controller $ctl) {
//
//		$ai_setting = $this->fmt_ai_setting->getall();
//		$ai_setting_parameters = $this->fmt_ai_setting_parameters->getall();
//		$ai_setting_subtable = $this->fmt_ai_setting_subtable->getall();
//
//		$arr = [
//		    "ai_setting" => $ai_setting,
//		    "ai_setting_parameters" => $ai_setting_parameters,
//		    "ai_setting_subtable" => $ai_setting_subtable
//		];
//		$ctl->res_json($arr, "ai_setting.json");
//	}
//
//	function upload_json(Controller $ctl) {
//
//		$ctl->show_multi_dialog("upload_json", "json_upload.tpl", "Upload JSON File", 800, true, true);
//	}
//
//	function upload_json_exe(Controller $ctl) {
//		$save_filename = 'ai_setting.json';
//		//save uploaded file as question_setting.json
//		$ctl->save_posted_file('jsonfile', $save_filename);
//
//		//get saved file path
//		$filepath = $ctl->get_saved_filepath($save_filename);
//
//		$jsonString = file_get_contents($filepath);
//		$jsonData = json_decode($jsonString, true);
//
//		$ai_setting = $jsonData["ai_setting"];
//		$ai_setting_parameters = $jsonData["ai_setting_parameters"];
//		$ai_setting_subtable = $jsonData["ai_setting_subtable"];
//		
//		//all clear
//		$this->fmt_ai_setting->allclear();
//		$this->fmt_ai_setting_parameters->allclear();
//		$this->fmt_ai_setting_subtable->allclear();
//
//		//Insert all
//		foreach($ai_setting as $a){
//			$old_id = $a["id"];
//			$this->fmt_ai_setting->insert($a);
//			foreach($ai_setting_parameters as $p){
//				$old_id_p = $p["id"];
//				if($p["ai_setting_id"] == $old_id){
//					$p["ai_setting_id"] = $a["id"];
//					$this->fmt_ai_setting_parameters->insert($p);
//				}
//				foreach($ai_setting_subtable as $s){
//					$old_id_s = $s["id"];
//					if($s[""])
//				}
//			}
//		}
//		
//		$ctl->close_multi_dialog("upload_json");
//		$this->page($ctl);
//	}
}
