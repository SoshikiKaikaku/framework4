<?php

class constant_array {

	private $fmt_constant_array;
	private $fmt_values;

	function __construct(Controller $ctl) {
		$this->fmt_constant_array = $ctl->db("constant_array");
		$this->fmt_values = $ctl->db("values");
	}

	//index page

	function page(Controller $ctl) {
		$post = $ctl->POST();
		$ctl->assign('post', $post);
		$items = $this->fmt_constant_array->filter(["array_name"], [$post["search_name"]], false, 'AND', 'id', SORT_DESC, $max, $is_last);

		$ctl->assign("items", $items);
		$ctl->show_main_area("constant_array", "index.tpl", "Constant Array");
	}

	function add(Controller $ctl) {
		$post = $ctl->POST();
		$ctl->assign('post', $post);
		$ctl->show_multi_dialog("add_constant_array", "add.tpl", "Add Constant Array", 800, true, true);
	}

	//save add data

	function add_exe(Controller $ctl) {
		$post = $ctl->POST();
		$ctl->assign('post', $post);
		//validation
		$errors = $this->validate_array_data($ctl, $post, "add");
		if (count($errors)) {
			$ctl->assign('errors', $errors);
			$this->add($ctl);
			return;
		}
		$post['updated_at'] = time();
		$id = $this->fmt_constant_array->insert($post);
		//close adding page
		$ctl->close_multi_dialog("add_constant_array");
		$this->page($ctl);
	}

	//validation

	function validate_array_data(Controller $ctl, $post, $page) {
		$errors = [];
		if (empty($post["array_name"]))
			$errors["array_name"] = "Array name is required!";

		if ($post["array_name"]) {
			$endsWith = '_opt';

			if (!endsWith($post["array_name"], $endsWith)) {
				$errors["array_name"] = "Please create a variable name that ends with '_opt'.";
			}

			$validate_duplicate = $ctl->validate_duplicate('constant_array', 'constant_array', 'array_name', $post["array_name"], $post["id"]);
			if (!$validate_duplicate) {
				$errors["array_name"] = $post["array_name"] . " is already exist!";
			}
		}
		return $errors;
	}

	function endsWith($haystack, $needle) {
		$length = strlen($needle);
		if (!$length) {
			return true;
		}
		return substr($haystack, -$length) === $needle;
	}

	//view edit page

	function edit(Controller $ctl, $id = null) {
		if ($id == null)
			$id = $ctl->POST("id");

		$post = $ctl->POST();
		$ctl->assign("post", $post);

		$data = $this->fmt_constant_array->get($id);

		//filter related values to the post (use post_id field)
		$values = $this->fmt_values->select(['constant_array_id'], [$id]);
		$ctl->assign("values", $values);
		$ctl->assign("data", $data);
		$ctl->show_multi_dialog("edit_array_" . $id, "edit.tpl", "Edit Constant Array", 800, true, true);
	}

	//save edited data

	function edit_exe(Controller $ctl) {
		$post = $ctl->POST();
		$ctl->assign('post', $post);
		//validation
		$errors = $this->validate_array_data($ctl, $post, "edit");
		if (count($errors)) {
			$ctl->assign('errors', $errors);
			$this->edit($ctl);
			return;
		}
		$data = $this->fmt_constant_array->get($post['id']);
		//if ($data['status']!=$post['status']) {
		$data['updated_at'] = time();
		//}
		foreach ($_POST as $key => $value) {
			$data[$key] = $value;
		}
		$data['order_date_timestamp'] = strtotime($post['order_date']);
		$this->fmt_constant_array->update($data);
		$ctl->close_multi_dialog("edit_array_" . $post['id']);
		$this->page($ctl);
	}

	//view delete page

	function delete(Controller $ctl) {
		$id = $ctl->POST("id");
		$data = $this->fmt_constant_array->get($id);
		$ctl->assign("data", $data);
		$ctl->show_multi_dialog("delete", "delete.tpl", "Delete Projects", 500, true, true);
	}

	//delete data form database

	function delete_exe(Controller $ctl) {
		$id = $ctl->POST("id");
		//file delete
		$data = $this->fmt_constant_array->get($id);
		//deleting child data
		$values = $this->fmt_values->select(['constant_array_id'], [$id]);
		foreach ($values as $key => $value) {
			$this->fmt_values->delete($value['id']);
		}
		$this->fmt_constant_array->delete($id);
		$ctl->close_multi_dialog("delete");
		$this->page($ctl);
	}

	function sort(Controller $ctl) {
		$post = $ctl->POST();
		$logArr = explode(',', $post['log']);
		$c = 0;
		foreach ($logArr as $id) {
			$d = $this->fmt_constant_array->get($id);
			$d['sort'] = $c;
			$this->fmt_constant_array->update($d);
			$c++;
		}
	}

	function add_values(Controller $ctl) {
		$constant_array_id = $ctl->POST('constant_array_id');
		$ctl->assign("constant_array_id", $constant_array_id);
		$data = $ctl->POST();
		$ctl->assign("data", $data);
		//var_dump($constant_array_id);
		$ctl->show_multi_dialog("add_values" . $constant_array_id, "add_values.tpl", "Add values", 500, true, true);
	}

	//save values
	function insert_values(Controller $ctl) {
		$data = $ctl->POST();
		//validation
		$errors = $this->validate_values_form($ctl, $data);
		if (count($errors)) {
			$ctl->assign('errors', $errors);
			$this->add_values($ctl);
			return;
		}
		$ctl->assign('data', $data);
		$data['updated_at'] = time();
		$this->fmt_values->insert($data);

		$ctl->close_multi_dialog("add_values" . $data['constant_array_id']);
		$this->edit($ctl, $data['constant_array_id']);
		$this->page($ctl);
	}

	function edit_values(Controller $ctl) {
		$id = $ctl->POST('id');
		$constant_array_id = $ctl->POST('constant_array_id');

		$values = $this->fmt_values->get($id);

		$ctl->assign("id", $id);
		$ctl->assign("values", $values);
		$ctl->assign("constant_array_id", $constant_array_id);
		$ctl->show_multi_dialog("edit_values" . $id, "edit_values.tpl", "Edit values", 500, true, true);
	}

	function edit_values_exe(Controller $ctl) {
		$data = $ctl->POST();
		$constant_array_id = $data['constant_array_id'];
		$data['updated_at'] = time();
		//validation
		$errors = $this->validate_values_form($ctl, $data);
		if (count($errors)) {
			$ctl->assign('errors', $errors);
			$this->edit_values($ctl);
			return;
		}

		$this->fmt_values->update($data);

		$ctl->close_multi_dialog("edit_values" . $data['id']);
		$this->edit($ctl, $constant_array_id);
		$this->page($ctl);
	}

	function delete_values(Controller $ctl) {
		$id = $ctl->POST("id");
		$values = $this->fmt_values->get($id);
		$ctl->assign("values", $values);
		$ctl->show_multi_dialog("delete_confirmation" . $id, "delete_confirmation.tpl", "Delete values", 1000, true, true);
	}

	function delete_values_exe(Controller $ctl) {

		$id = $ctl->POST('values_id');
		$constant_array_id = $ctl->POST('constant_array_id');
		$this->fmt_values->delete($id);
		$ctl->close_multi_dialog("delete_confirmation" . $id);
		$this->edit($ctl, $constant_array_id);
	}

	//validation values adding function
	function validate_values_form(Controller $ctl, $data) {
		$errors = [];

		if ($data['key'] == null)
			$errors['key'] = "Key is required!";

		if ($data['key'] && !is_numeric($data['key'])) {
			$errors['key'] = "Key should be a number.";
		}
		//var_dump($data);

		if ($data['key']) {
			$validate_duplicate = $ctl->validate_duplicate('values', 'constant_array', ['key', "constant_array_id"], [$data['key'], $data["constant_array_id"]], $data["id"]);
			//var_dump($is_duplicate);
			if (!$validate_duplicate) {
				$errors["key"] = $data['key'] . " is already exist!";
			}
		}
		//die();
		if (empty($data['value']))
			$errors['value'] = "Value is required!";

		return $errors;
	}
}
