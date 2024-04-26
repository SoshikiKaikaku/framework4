<?php

class bughistory {

	private $fmt_bug_history;

	function __construct(Controller $ctl) {
		$this->fmt_bug_history = $ctl->db("bug_history");
	}

	//index page
	function page(Controller $ctl) {

		$post = $ctl->POST();
		$ctl->assign('post', $post);
		$max = $ctl->increment_post_value('max', 10);

		$items = $this->fmt_bug_history->filter(["bugs_id"], [$post["bugs_id"]], false, 'AND', 'sort', SORT_ASC, $max, $is_last);
		$ctl->assign("max", $max);
		$ctl->assign("is_last", $is_last);
		$ctl->assign("items", $items);

		$res = $ctl->fetch("index_history.tpl");
		$ctl->reload_area("#bughistory_bug_history_" . $post['bugs_id'], $res);
	}

	function add_report_history(Controller $ctl) {
		$post = $ctl->POST();
		$id = $this->fmt_bug_history->insert($post);
	}

	//view add page
	function add(Controller $ctl) {
		$post = $ctl->POST();
		$post['date'] = date("Y/m/d");
		$ctl->assign('post', $post);
		$ctl->show_multi_dialog("add_bug_history", "add_history.tpl", "Add Bug History", 600, true, true);
	}

	//save add data
	function add_exe(Controller $ctl) {
		$post = $ctl->POST();
		$ctl->assign('post', $post);
		//validation
		$errors = $this->validate_bug_history_data($ctl, $post, "add");
		if (count($errors)) {
			$ctl->assign('errors', $errors);
			$this->add($ctl);
			return;
		}


		$post['created_at'] = time();
		$id = $this->fmt_bug_history->insert($post);
		$ctl->ajax('bugmanage', 'update_at', ['id' => $post['bugs_id']]);
		$ctl->ajax('bugmanage', 'page', ['id' => $post['bugs_id']]);
		//close adding page
		$ctl->close_multi_dialog("add_bug_history");

		$this->page($ctl);
	}

	//validation
	function validate_bug_history_data(Controller $ctl, $post, $page) {
		$errors = [];

		return $errors;
	}

	//view edit page
	function edit(Controller $ctl) {
		$post = $ctl->POST();
		$ctl->assign("post", $post);

		$data = $this->fmt_bug_history->get($post['id']);
		$data = array_merge($data, $post);
		$ctl->assign("data", $data);
		$ctl->show_multi_dialog("edit_bug_history_" . $post['id'], "edit_history.tpl", "Edit Bug History", 600, true, true);
	}

	//save edited data
	function edit_exe(Controller $ctl) {
		$post = $ctl->POST();
		$ctl->assign('post', $post);

		//validation
		$errors = $this->validate_bug_history_data($ctl, $post, "edit");
		if (count($errors)) {
			$ctl->assign('errors', $errors);
			$this->edit($ctl);
			return;
		}
		$data = $this->fmt_bug_history->get($post['id']);
		foreach ($_POST as $key => $value) {
			$data[$key] = $value;
		}


		$data['updated_at'] = time();
		$this->fmt_bug_history->update($data);
		$ctl->ajax('bugmanage', 'update_at', ['id' => $post['bugs_id']]);
		$ctl->ajax('bugmanage', 'page', ['id' => $post['bugs_id']]);
		$ctl->close_multi_dialog("edit_bug_history_" . $post['id']);
		$this->page($ctl);
	}

	//view delete page
	function delete(Controller $ctl) {
		$id = $ctl->POST("id");
		$data = $this->fmt_bug_history->get($id);
		$ctl->assign("data", $data);
		$ctl->show_multi_dialog("delete", "delete_history.tpl", "Delete Bug History", 500, true, true);
	}

	//delete data form database
	function delete_exe(Controller $ctl) {
		$post = $ctl->POST();
		$id = $ctl->POST("id");

		//file delete
		$data = $this->fmt_bug_history->get($id);

		//deleting child data
		$this->fmt_bug_history->delete($id);
		$ctl->close_multi_dialog("delete");
		//$this->page($ctl);
		$res = $ctl->fetch("index_history.tpl");
		$ctl->reload_area("#bughistory_bug_history_" . $post['bugs_id'], $res);
		//$ctl->ajax('bugmanage', 'edit', ['id' => $id]);
	}

	//delete child data from this db of a parent
	function delete_foreign_data(Controller $ctl) {
		$post = $ctl->POST();
		$child_items = $this->fmt_bug_history->select([$post['foreign_key']], [$post['foreign_id']]);
		foreach ($child_items as $item) {
			$_POST['id'] = $item['id'];
			//$this->delete_exe($ctl);
			$this->fmt_bug_history->delete($item['id']);
		}
	}

	function sort(Controller $ctl) {
		$post = $ctl->POST();
		$logArr = explode(',', $post['log']);
		$c = 0;
		foreach ($logArr as $id) {
			$d = $this->fmt_bug_history->get($id);
			$d['sort'] = $c;
			if (!empty($post["parent_id"])) {
				$d["bugs_id"] = $post["parent_id"];
			}
			$this->fmt_bug_history->update($d);
			$c++;
		}
	}
}
