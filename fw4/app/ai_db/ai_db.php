<?php

class ai_db {

	private $fmt_ai_db;
	private $fmt_ai_fields;
	private $type_opt = [
	    "text" => "Text"
	    , "number" => "Number(Integer)"
	    , "float" => "Number(Float)"
	    , "textarea" => "Textarea"
	    , "textarea_links" => "Textarea replaced html links"
	    , "markdown" => "Markdown"
	    , "dropdown" => "Dropdown"
	    , "checkbox" => "Checkbox"
	    , "radio" => "Radio"
	    , "date" => "Date"
	    , "time" => "Time"
	    , "year_month" => "Year/Month"
	    , "color" => "Color"
	    , "file" => "File"
	    , "image" => "Image"
	    , "vimeo" => "Vimeo"
	];
	// Default length
	private $type_length = [
	    "text" => 255
	    , "number" => 24
	    , "float" => 24
	    , "textarea" => 1000
	    , "textarea_links" => 1000
	    , "markdown" => 1000
	    , "dropdown" => 3
	    , "checkbox" => 255
	    , "radio" => 3
	    , "date" => 15
	    , "time" => 10
	    , "year_month" => 15
	    , "color" => 15
	    , "file" => 255
	    , "image" => 255
	    , "vimeo" => 255
	];
	// Validation
	private $validation_opt = [
	    0 => "No Check",
	    1 => "Required",
	];

	function __construct(Controller $ctl) {
		$this->fmt_ai_db = $ctl->db("ai_db");
		$this->fmt_ai_fields = $ctl->db("ai_fields");

		$ctl->assign('type_opt', $this->type_opt);
		$ctl->assign("validation_opt", $this->validation_opt);
		$ctl->assign("constant_array_opt", $ctl->get_all_constant_array_names(true));
	}

	function get_parent_opt($my_id) {

		$list = $this->fmt_ai_db->getall("sort", SORT_ASC);
		$opt = [0 => ""];
		foreach ($list as $key => $d) {
			if ($d["id"] != $my_id) {
				$opt[$d["id"]] = $d["tb_name"];
			}
		}
		return $opt;
	}

	function get_default_length(Controller $ctl) {
		$type = $ctl->POST("type");
		$arr = [
		    "length" => $this->type_length[$type]
		];
		$ctl->res_json($arr);
	}

	//index page
	function page(Controller $ctl) {

		$post = $ctl->POST();
		$ctl->assign('post', $post);
		$items = $this->fmt_ai_db->getall("sort", SORT_ASC);
		$ctl->assign("items", $items);

		$ctl->assign("parents_opt", $this->get_parent_opt(null));

		$ctl->show_main_area("ai_db", "index.tpl", "Database");

		// update FFM
		$ctl->ajax("ai_db", "make_table_format");
	}

	//view add page
	function add(Controller $ctl) {
		$post = $ctl->POST();
		$ctl->assign('post', $post);

		$ctl->assign("parents_opt", $this->get_parent_opt(null));

		$ctl->show_multi_dialog("add_ai_db", "add.tpl", "Add AI Setting", 1000, true, true);
	}

	//save add data
	function add_exe(Controller $ctl) {
		$post = $ctl->POST();
		$ctl->assign('post', $post);
		

		//validation
		$errors = $this->validate_ai_db_data($ctl, $post, "add");
		if (count($errors)) {
			$ctl->assign('errors', $errors);
			$this->add($ctl);
			return;
		}
		

		$id = $this->fmt_ai_db->insert($post);
		
		if($post['parent_tb_id']){
		    $data['ai_db_id'] = $id;
		    $data['type'] = 'text';
		    $data["length"] = $this->type_length[$data["type"]];
		    $data['parameter_name']='parent_field';
		    $data['parameter_title']='parent field';
		    $id_f = $this->fmt_ai_fields->insert($data);
		}
		
		$ctl->close_multi_dialog("add_ai_db");

		$this->page($ctl);
	}

	//validation
	function validate_ai_db_data(Controller $ctl, $post, $page) {
		$errors = [];

		if (empty($post["tb_name"])) {
			$errors["tb_name"] = "Table Name is required!";
		}

		if (!preg_match('/^[a-z0-9_]+$/', $post["tb_name"])) {
			$errors["tb_name"] = "You can use lowercase or \"_\" for this field.";
		}

		// Duplicate error check
		$list = $this->fmt_ai_db->getall();
		foreach ($list as $d) {
			if ($post["tb_name"] == $d["tb_name"]) {
				if ($post["id"] != $d["id"]) {
					$errors["tb_name"] = "Table Name is duplicated!";
				}
			}
		}

		return $errors;
	}

	//view edit page
	function edit(Controller $ctl) {
		$post = $ctl->POST();
		$id = $post["id"];

		$data = $this->fmt_ai_db->get($id);
		$ctl->assign("data", $data);

		$parameters = $this->fmt_ai_fields->select('ai_db_id', $id, false, "AND", 'sort', SORT_ASC);
		$ctl->assign("parameters", $parameters);

		$ctl->assign("parents_opt", $this->get_parent_opt($id));

		$ctl->show_multi_dialog("edit_ai_db", "edit.tpl", "Edit DB Setting", 1000, "_edit_button.tpl", true);

		// update FFM
		$ctl->ajax("ai_db", "make_table_format");
	}

	//save edited data
	function edit_exe(Controller $ctl) {
		$post = $ctl->POST();
		$ctl->assign('post', $post);

		//validation
		$errors = $this->validate_ai_db_data($ctl, $post, "edit");
		if (count($errors)) {
			$ctl->assign('errors', $errors);
			$this->edit($ctl);
			return;
		}

		$data = $this->fmt_ai_db->get($post['id']);
		foreach ($_POST as $key => $value) {
			$data[$key] = $value;
		}

		$this->fmt_ai_db->update($data);

		$ctl->close_multi_dialog("edit_ai_db");
		$this->page($ctl);
	}

	//view delete page
	function delete(Controller $ctl) {
		$id = $ctl->POST("id");
		$data = $this->fmt_ai_db->get($id);
		$ctl->assign("data", $data);
		$ctl->show_multi_dialog("delete", "delete.tpl", "Delete AI Setting", 500, true, true);
	}

	//delete data form database
	function delete_exe(Controller $ctl) {
		$id = $ctl->POST("id");
		//file delete
		$data = $this->fmt_ai_db->get($id);

		//deleting child data
		$this->fmt_ai_db->delete($id);

		$ctl->close_multi_dialog("delete");
		$this->page($ctl);
	}

	function sort(Controller $ctl) {
		$post = $ctl->POST();
		$logArr = explode(',', $post['log']);
		$c = 1;
		foreach ($logArr as $id) {
			$d = $this->fmt_ai_db->get($id);
			$d['sort'] = $c;
			$this->fmt_ai_db->update($d);
			$c++;
		}
	}

	function sort_fields(Controller $ctl) {
		$post = $ctl->POST();
		$logArr = explode(',', $post['log']);
		$c = 1;
		foreach ($logArr as $id) {
			$d = $this->fmt_ai_fields->get($id);
			$d['sort'] = $c;
			$this->fmt_ai_fields->update($d);
			$c++;
		}
	}

	//view add page
	function add_fields(Controller $ctl) {
		$post = $ctl->POST();
		$post['ai_db_id'] = $post['id'];
		$ctl->assign('post', $post);
		$ctl->show_multi_dialog("add_ai_fields", "add_fields.tpl", "Add AI Setting Parameters", 1000, true, true);
	}

	//save add data
	function add_fields_exe(Controller $ctl) {
		$post = $ctl->POST();
		$ctl->assign('post', $post);

		//validation
		$errors = $this->validate_ai_fields_data($ctl, $post, "add_fields");
		if (count($errors)) {
			$ctl->assign('errors', $errors);
			$this->add_fields($ctl);
			return;
		}


		// Set default length
		$post["length"] = $this->type_length[$post["type"]];

		$id = $this->fmt_ai_fields->insert($post);

		//close adding page
		$ctl->close_multi_dialog("add_ai_fields");
		$ctl->ajax("ai_db", "edit", ["id" => $post['ai_db_id']]);
	}

	//validation
	function validate_ai_fields_data(Controller $ctl, $post, $page) {
		$errors = [];

		if (empty($post["parameter_name"])) {
			$errors["parameter_name"] = "Parameter Name is required!";
		}

		if (!preg_match('/^[a-z0-9_]+$/', $post["parameter_name"])) {
			$errors["parameter_name"] = "You can use lowercase or \"_\" for the Parameter Name.";
		}

		// Duplicate error check
		$list = $this->fmt_ai_fields->select("ai_db_id", $post['ai_db_id']);
		foreach ($list as $d) {
			if ($post["parameter_name"] == $d["parameter_name"]) {
				if ($post["id"] != $d["id"]) {
					
				}
			}
		}

		// check prohibition_item_name
		foreach ($this->fmt_ai_fields->get_prohibition_items() as $name) {
			if ($post["parameter_name"] == $name) {
				$errors["parameter_name"] = "This value is prohibited to use as parameter name.";
			}
		}


		// title
		if (empty($post["parameter_title"])) {
			$errors["parameter_title"] = "Parameter Title is required!";
		}

		// length
		if (empty($post["length"])) {
			$errors["length"] = "Parameter Length is required!";
		}



		return $errors;
	}

	//view edit page
	function edit_fiedls(Controller $ctl) {
		$post = $ctl->POST();
		$ctl->assign("post", $post);

		$data = $this->fmt_ai_fields->get($post['id']);
		$data = array_merge($data, $post);
		$ctl->assign("data", $data);
		$ctl->show_multi_dialog("edit_ai_fields_" . $post['id'], "edit_fiedls.tpl", "Edit AI Setting Parameters", 1000, true, true);
	}

	//save edited data
	function edit_fiedls_exe(Controller $ctl) {
		$post = $ctl->POST();
		$ctl->assign('post', $post);
		//validation
		$errors = $this->validate_ai_fields_data($ctl, $post, "edit_fiedls");
		if (count($errors)) {
			$ctl->assign('errors', $errors);
			$this->edit_fiedls($ctl);
			return;
		}

		$data = $this->fmt_ai_fields->get($post['id']);
		foreach ($_POST as $key => $value) {
			$data[$key] = $value;
		}

		if (!in_array($post["type"], ["dropdown", "chedkbox", "radio"])) {
			$data["constant_array_name"] = "";
		}

		$this->fmt_ai_fields->update($data);

		$ctl->close_multi_dialog("edit_ai_fields_" . $post['id']);
		//$this->page($ctl);
		$ctl->ajax("ai_db", "edit", ["id" => $data['ai_db_id']]);
	}

	//view delete page
	function delete_fiedls(Controller $ctl) {
		$id = $ctl->POST("id");
		$data = $this->fmt_ai_fields->get($id);
		$ctl->assign("data", $data);
		$ctl->show_multi_dialog("delete_fiedls", "delete_fiedls.tpl", "Delete AI Setting Parameters", 500, true, true);
	}

	//delete data form database
	function delete_fiedls_exe(Controller $ctl) {
		$id = $ctl->POST("id");
		//file delete
		$data = $this->fmt_ai_fields->get($id);

		//deleting child data		
		$this->fmt_ai_fields->delete($id);
		$ctl->close_multi_dialog("delete_fiedls");

		$ctl->ajax("ai_db", "edit", ["id" => $data['ai_db_id']]);
	}

	// make table format
	function make_table_format(Controller $ctl) {

		$dirs = new Dirs();
		$dir = $dirs->get_class_dir("common") . "/fmt/";

		// ディレクトリ作成・既存ファイル削除
		if (is_dir($dir)) {
			$files = glob($dir . '*'); // ディレクトリ内のすべてのファイルを取得
			foreach ($files as $file) {
				if (is_file($file)) {
					unlink($file); // ファイルを削除
				}
			}
		} else {
			mkdir($dir);
		}

		$tables = $this->fmt_ai_db->getall("sort", SORT_ASC);

		foreach ($tables as $table) {
			$ai_db_id = $table["id"];

			$txt = "id,24,N\n";

			$fields = $this->fmt_ai_fields->select("ai_db_id", $ai_db_id, true, "AND", "sort", SORT_ASC);
			foreach ($fields as $field) {
				$t = "";
				if ($field["type"] == "number" || $field["type"] == "dropdown" || $field["type"] == "radio"
				) {
					$t = "N";
				} else if ($field["type"] == "float") {
					$t = "F";
				} else if ($field["type"] == "checkbox") {
					$t = "A";
				} else {
					$t = "T";
				}
				

				$txt .= $field["parameter_name"] . "," . $field["length"] . "," . $t . "\n";
			}
			file_put_contents($dir . $table["tb_name"] . ".fmt", $txt);
		}
	}
	function view_image(Controller $ctl) {
		$image_file = $ctl->GET("file");
		$ctl->res_saved_image($image_file);
	}
	
	function download_file(Controller $ctl) {
	    $filename= $ctl->POST("filename");
	    //var_dump($filename);
	    $ctl->res_saved_file($filename);
	}
}
