<?php

class email_format {

	private $fmt_email_format;

	function __construct(Controller $ctl) {
		$this->fmt_email_format = $ctl->db("email_format");
	}

	//index page
	function page(Controller $ctl) {

		$post = $ctl->POST();
		$ctl->assign('post', $post);
		$max = $ctl->increment_post_value('max', 10);

		if ($post['button'] == "reset") {
			$search_template_name = "";
			$ctl->set_session("search_template_name_em_tmp",
				$search_template_name);
		}
		if (!empty($ctl->get_session("search_template_name_em_tmp"))) {
			$search_template_name = $ctl->get_session("search_template_name_em_tmp");
		}
		if (!empty($post['search_template_name'])) {
			$search_template_name = $post['search_template_name'];
			$ctl->set_session("search_template_name_em_tmp",
				$search_template_name);
		}

		$items = $this->fmt_email_format->filter(["template_name"], [$post["search_template_name"]], false, 'AND', 'sort', SORT_ASC, $max, $is_last);
		$ctl->assign("max", $max);
		$ctl->assign("is_last", $is_last);
		$ctl->assign("items", $items);

		$ctl->show_main_area("email_format", "index.tpl", "Email Templates");
	}

	//view add page
	function add(Controller $ctl) {
		$post = $ctl->POST();
		$ctl->assign('post', $post);
		$ctl->show_multi_dialog("add_email_format", "add.tpl", "Add Email Templates", 1000, true, true);
	}

	//save add data
	function add_exe(Controller $ctl) {
		$post = $ctl->POST();
		$ctl->assign('post', $post);
		//validation
		$errors = $this->validate_email_format_data($ctl, $post, "add");
		if (count($errors)) {
			$ctl->assign('errors', $errors);
			$this->add($ctl);
			return;
		}

		$post['created_at'] = time();
		$id = $this->fmt_email_format->insert($post);

		//close adding page
		$ctl->close_multi_dialog("add_email_format");
		$this->page($ctl);
	}

	//validation
	function validate_email_format_data(Controller $ctl, $post, $page) {
		$errors = [];

		return $errors;
	}

	//view edit page
	function edit(Controller $ctl) {
		$post = $ctl->POST();
		$ctl->assign("post", $post);
		$data = $this->fmt_email_format->get($post['id']);
		$data = array_merge($data, $post);
		$ctl->assign("data", $data);

		$ctl->show_multi_dialog("edit_email_format_" . $post['id'], "edit.tpl", "Edit Email Templates", 1000, true, true);
	}

	//save edited data
	function edit_exe(Controller $ctl) {
		$post = $ctl->POST();
		$ctl->assign('post', $post);
		//validation
		$errors = $this->validate_email_format_data($ctl, $post, "edit");
		if (count($errors)) {
			$ctl->assign('errors', $errors);
			$this->edit($ctl);
			return;
		}

		$data = $this->fmt_email_format->get($post['id']);
		foreach ($_POST as $key => $value) {
			$data[$key] = $value;
		}

		$data['updated_at'] = time();
		$this->fmt_email_format->update($data);

		$ctl->close_multi_dialog("edit_email_format_" . $post['id']);
		$this->page($ctl);
	}

	//view delete page
	function delete(Controller $ctl) {
		$id = $ctl->POST("id");
		$data = $this->fmt_email_format->get($id);
		$ctl->assign("data", $data);
		$ctl->show_multi_dialog("delete", "delete.tpl", "Delete Email Templates", 600, true, true);
	}

	//delete data form database
	function delete_exe(Controller $ctl) {
		$id = $ctl->POST("id");
		//file delete
		$data = $this->fmt_email_format->get($id);

		//deleting child data
		$this->fmt_email_format->delete($id);
		$ctl->close_multi_dialog("delete");
		$this->page($ctl);
	}

	function sort(Controller $ctl) {
		$post = $ctl->POST();
		$logArr = explode(',', $post['log']);
		$c = 0;
		foreach ($logArr as $id) {
			$d = $this->fmt_email_format->get($id);
			$d['sort'] = $c;
			$this->fmt_email_format->update($d);
			$c++;
		}
	}

	function json_upload(Controller $ctl) {
		$ctl->show_multi_dialog("upload", "upload.tpl", "Email Templates JSON Upload", 600, true, true);
	}

	function json_upload_exe(Controller $ctl) {

		$save_filename = 'email_template.json';
		$ctl->save_posted_file('email_templates_file', $save_filename);

		//get saved file path
		$file_path = $ctl->get_saved_filepath($save_filename);

		$json_string_templates = file_get_contents($file_path);
		$data_templates = json_decode($json_string_templates, true);

		foreach ($data_templates as $key => $value) {
			$already_exist_same_key = $this->fmt_email_format->select(['key'], [$value['key']])[0];
			if (empty($already_exist_same_key)) {
				$this->fmt_email_format->insert($value);
			}
		}
		$ctl->close_multi_dialog("upload");
		$this->page($ctl);
	}

	function json_download(Controller $ctl) {
		$email_templates = $this->fmt_email_format->getall();
		$ctl->res_json($email_templates, $post['filename']);
	}
}
