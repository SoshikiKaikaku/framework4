<?php

class bugmanage {

	private $fmt_bugs;
	private $status;
	private $priority;
	private $fmt_setting;
	private $login = false;

	function __construct(Controller $ctl) {

		$ctl->set_check_login(false);

		//独自認証
		if ($ctl->GET("sec") != "") {
			$sec = $ctl->decrypt($ctl->GET("sec"));
			if ($sec != "login") {
				$ctl->display("error.tpl");
				$this->login = false;
				$ctl->set_session("bug_login", false);
				return;
			} else {
				$ctl->set_session("bug_login", true);
				$this->login = true;
			}
		} else {
			if ($ctl->get_session("bug_login") == true) {
				$this->login = true;
			} else {
				$this->login = false;
				return;
			}
		}


		$this->status = ['' => '', 1 => "Pending", 2 => "Fixing", 3 => "Fixed", 4 => "Fail", 5 => "Confirm"];
		$this->priority = ['' => '', 1 => "Low (Demand)", 2 => "Middle", 3 => "High (BUG)"];

		$ctl->assign("status", $this->status);
		$ctl->assign("priority", $this->priority);

		$this->fmt_bugs = $ctl->db("bugs");
		$this->fmt_setting = $ctl->db("setting", "setting");
	}

	//index page
	function page(Controller $ctl) {
		if ($this->login == false) {
			return;
		}

		$ctl->ajax("bugmanage", "list");

		$ctl->display("index.tpl");
	}

	function search(Controller $ctl) {
		$ctl->set_session("bug_mamnage_search", $ctl->POST());
		$ctl->ajax("bugmanage", "list");
	}

	function list(Controller $ctl) {
		if ($this->login == false) {
			return;
		}

		$post = $ctl->get_session("bug_mamnage_search");
		$ctl->assign('post', $post);
		$max = $ctl->increment_post_value('max', 10);
		$items = $this->fmt_bugs->filter(["id", "status", "priority", "desk_japanees", "desk_english"], [$post["search_id"], $post["search_status"], $post["search_priority"], $post["search_desk"], $post["search_desk"]], false, 'AND', 'id', SORT_DESC, $max, $is_last);
		$ctl->assign("max", $max);
		$ctl->assign("is_last", $is_last);
		$ctl->assign("items", $items);

		$html = $ctl->fetch("_list.tpl");
		$ctl->reload_area(".list_area", $html);
	}

	//view add page
	function add(Controller $ctl) {
		if ($this->login == false) {
			return;
		}

		$post = $ctl->POST();
		$post["status"] = 1;
		$post["priority"] = 2;
		$ctl->assign('post', $post);
		$ctl->show_multi_dialog("add_bugs", "add.tpl", "Add", 1000, "add_button.tpl", true);
	}

	//save add data
	function add_exe(Controller $ctl) {
		if ($this->login == false) {
			return;
		}

		$post = $ctl->POST();
		$ctl->assign('post', $post);
		//validation
		$errors = $this->validate_bugs_data($ctl, $post, "add");
		if (count($errors)) {
			$ctl->assign('errors', $errors);
			$this->add($ctl);
			return;
		}
		//get file name
		$filename = $ctl->get_posted_filename("image");
		//check whether filename is empty
		if (!empty($filename)) {
			//$image_file = $ctl->get_posted_filename('image_file');

			$image_file_extension = $ctl->get_posted_file_extention('image');
			$saved_image_file = 'image-' . time() . '.' . $image_file_extension;
			if ($ctl->is_saved_file($saved_image_file)) {
				$ctl->remove_saved_file($saved_image_file);
			}

			$saved = $ctl->save_posted_file('image', $saved_image_file);
			//$thumbsaved = $ctl->save_posted_file('image', $thumb);
			$inputfile = $saved_image_file;
			$outputfile = 'thumb-' . $saved_image_file;
			$width = '200px';
			if ($ctl->is_saved_file($outputfile)) {
				$ctl->remove_saved_file($outputfile);
			}
			$thumb = $ctl->resize_saved_image($inputfile, $outputfile, $width, $quality = 100);
			//var_dump($thumb);
			//var_dump($saved);
			$post["image"] = $saved_image_file;
			$post["thumb_image"] = $outputfile;
		}

		$post['created_at'] = time();
		$post['updated_at'] = time();
		$id = $this->fmt_bugs->insert($post);
		$current_date = date("Y/m/d");
		$current_time = time();

		$bug = $this->fmt_bugs->get($id);
		$url = explode(".", $_SERVER['SERVER_NAME']);
		$setting = $this->fmt_setting->getall();

		if (!empty($setting[0]['bug_report_server'])) {
			$bug_report_server = $setting[0]['bug_report_server'];
			$res = $ctl->api($bug_report_server, "bug_server", "add_bug", ['bug' => $bug, 'server' => $url[1], 'appcode' => $url[0]]);
		}

		//close adding page
		$ctl->close_multi_dialog("add_bugs");
		$ctl->ajax("bugmanage", "list");
	}

	//validation
	function validate_bugs_data(Controller $ctl, $post, $page) {
		$errors = [];

		return $errors;
	}

	//view edit page
	function edit(Controller $ctl) {
		if ($this->login == false) {
			return;
		}

		$post = $ctl->POST();
		$ctl->assign("post", $post);
		$data = $this->fmt_bugs->get($post['id']);
		$data = array_merge($data, $post);
		$ctl->assign("data", $data);

		$ctl->show_multi_dialog("edit_bugs_" . $post['id'], "edit.tpl", "Edit", 1200, "update_button.tpl");
	}

	//save edited data
	function edit_exe(Controller $ctl) {
		if ($this->login == false) {
			return;
		}

		$post = $ctl->POST();
		$ctl->assign('post', $post);
		//validation
		$errors = $this->validate_bugs_data($ctl, $post, "edit");
		if (count($errors)) {
			$ctl->assign('errors', $errors);
			$this->edit($ctl);
			return;
		}

		$data = $this->fmt_bugs->get($post['id']);
		foreach ($_POST as $key => $value) {
			$data[$key] = $value;
		}
		$filename = $ctl->get_posted_filename("image");
		$oldimage = $post['oldimage'];
		$oldimage_thumb = 'thumb-' . $post['oldimage'];
		//check whether filename is empty
		if (!empty($filename)) {
			if ($ctl->is_saved_file($oldimage_thumb)) {
				$ctl->remove_saved_file($oldimage_thumb);
			}
			if ($ctl->is_saved_file($oldimage)) {
				$ctl->remove_saved_file($oldimage);
			}
			//$image_file = $ctl->get_posted_filename('image_file');

			$image_file_extension = $ctl->get_posted_file_extention('image');
			$saved_image_file = 'image-' . time() . '.' . $image_file_extension;
			if ($ctl->is_saved_file($saved_image_file)) {
				$ctl->remove_saved_file($saved_image_file);
			}
			$ctl->save_posted_file('image', $saved_image_file);

			//save thumb
			$inputfile = $saved_image_file;
			$outputfile = 'thumb-' . $saved_image_file;
			$width = '200px';
			if ($ctl->is_saved_file($outputfile)) {
				$ctl->remove_saved_file($outputfile);
			}
			$thumb = $ctl->resize_saved_image($inputfile, $outputfile, $width, $quality = 100);

			$data["image"] = $saved_image_file;
			$data["thumb_image"] = $outputfile;
		}

		$data['updated_at'] = time();
		$this->fmt_bugs->update($data);
		$bug = $this->fmt_bugs->get($post['id']);
		$setting = $this->fmt_setting->getall();
		$url = explode(".", $_SERVER['SERVER_NAME']);
		if (!empty($setting[0]['bug_report_server'])) {
			$bug_report_server = $setting[0]['bug_report_server'];
			$res = $ctl->api($bug_report_server, "bug_server", "edit_bug", ['bug' => $bug, 'server' => $url[1], 'appcode' => $url[0]]);
		}
		$ctl->close_multi_dialog("edit_bugs_" . $post['id']);
		$ctl->ajax("bugmanage", "list");
	}

	//view delete page
	function delete(Controller $ctl) {
		if ($this->login == false) {
			return;
		}

		$id = $ctl->POST("id");
		$data = $this->fmt_bugs->get($id);
		$ctl->assign("data", $data);
		$ctl->show_multi_dialog("delete", "delete.tpl", "Delete", 500, true, true);
	}

	//delete data form database
	function delete_exe(Controller $ctl) {
		if ($this->login == false) {
			return;
		}

		$id = $ctl->POST("id");
		//file delete
		$data = $this->fmt_bugs->get($id);

		$image = $data["image"];
		$thumb_image = $data["thumb_image"];
		if ($ctl->is_saved_file($image)) {
			$ctl->remove_saved_file($image);
		}
		if ($ctl->is_saved_file($thumb_image)) {
			$ctl->remove_saved_file($thumb_image);
		}

		//deleting child data
		$setting = $this->fmt_setting->getall();

		if (!empty($setting[0]['bug_report_server'])) {
			$bug_report_server = $setting[0]['bug_report_server'];
			$res = $ctl->api($bug_report_server, "bug_server", "delete_bug", ['bug' => $data]);
		}

		$this->fmt_bugs->delete($id);
		$ctl->close_multi_dialog("delete");
		$ctl->ajax("bugmanage", "list");
	}

	function view_image(Controller $ctl) {
		$image_file = $ctl->GET("file");
		$ctl->res_saved_image($image_file);
	}

	function update_at(Controller $ctl) {
		$data = $ctl->POST();
		$databugs = $this->fmt_bugs->get($data['id']);
		$databugs['updated_at'] = time();
		$databugs['id'] = $data['id'];
		$this->fmt_bugs->update($databugs);
	}

	function img(Controller $ctl) {
		$image_file = $ctl->GET("file");
		$ctl->res_image("images", $image_file);
	}

	function translate(Controller $ctl) {
		$post = $ctl->POST();
		$ctl->assign('post', $post);
		$text = $ctl->translate($post['desk_japanees'], "ja", "en");
		$html = '<textarea name="desk_english" class="wordcounter" data-counter_max="2000">' . $text . '</textarea>';
		$ctl->reload_area('#desk_english', $html);
	}
}
