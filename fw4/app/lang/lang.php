<?php

/*
 *  YOU CAN'T CHANGE THIS PROJECT.
 *  It will be overwritten when the framework updates.
 */

class lang {

	private $ffm;

	function __construct(Controller $ctl) {
		$this->ffm = $ctl->db("lang");
		$ctl->set_check_login(false);
	}

	function csv_download(Controller $ctl) {

		$list = $this->ffm->getall();
		$csv_data = [];
		foreach ($list as $key => $d) {
			$row = ['en' => $d["en"], 'jp' => $d["jp"]];
			$ctl->res_csv($row, 'sjis-win');
		}
	}

	function csv_upload(Controller $ctl) {

		if ($ctl->is_posted_file('csvfile')) {

			//save uploaded file as csvdata.csv
			$ctl->save_posted_file('csvfile', 'csvdata.csv');

			//get saved file path
			$filepath = $ctl->get_saved_filepath('csvdata.csv');

			//open saved file
			$fp = fopen($filepath, 'r');

			//set encoding for japanese
			stream_filter_register('convert.mbstring.*', 'Stream_Filter_Mbstring');
			$filtername = 'convert.mbstring.encoding.SJIS-win:UTF-8';
			stream_filter_append($fp, $filtername, STREAM_FILTER_READ);

			//read each line as csv
			$txt = "";
			$counter = 0;
			while ($row = fgetcsv($fp)) {
				$clist = $this->ffm->select(["en"], [$row[0]], true);
				if (count($clist) > 0) {
					$counter++;
					$clist[0]["jp"] = $row[1];
					$this->ffm->update($clist[0]);
				} else {
					if (!(empty($row[1]) && empty($row[0]))) {
						$newdata = array();
						$newdata["en"] = $row[0];
						$newdata["jp"] = $row[1];
						$this->ffm->insert($newdata);
					}
					$counter++;
				}
			}

			//close file
			fclose($fp);

			//response csv data to the view
			$ctl->assign("csvresult", $counter . " datas are updated/inserted.");
		}

		$this->edit($ctl);
	}

	function append(Controller $ctl) {

		$lang_array_json = $ctl->POST("lang_array");
		$lang_array = json_decode($lang_array_json, true);

		foreach ($lang_array as $data) {
			if (empty($data["en"])) {
				continue;
			}

			if ($data["classname"] == "" || $data["en"] == "undefined") {
				continue;
			}

			//重複チェック
			$list = $this->ffm->select(["classname", "en"], [$data["classname"], $data["en"]], true, "AND");
			if (count($list) > 0) {
				continue;
			}

			$this->ffm->insert($data);
		}

		$this->save_json($ctl);
		$this->list($ctl);
	}

	function delete(Controller $ctl) {
		$id = $ctl->POST("id");
		$this->ffm->delete($id);

		$this->save_json($ctl);

		$ctl->ajax("lang", "showlist");
	}

	function showlist(Controller $ctl) {
		$lang_search = $ctl->get_session("lang_search");

		if (empty($lang_search)) {
			$list = $this->ffm->getall("en", SORT_ASC);
		} else {
			$list = $this->ffm->filter(["en"], [$lang_search], false, 'AND', 'en', SORT_ASC);
		}

		$ctl->assign("list", $list);

		$ctl->show_multi_dialog("Edit_Translation", "index.tpl", "Edit Translation", 1000);
	}

	function edit(Controller $ctl) {
		$post = $ctl->POST();

		$ctl->set_session("lang_search", $post["lang_search"]);

		$ctl->ajax("lang", "showlist");
	}

	function edit_exe(Controller $ctl) {
		$id = $ctl->POST("id");
		$data = $this->ffm->get($id);
		foreach ($ctl->POST() as $key => $val) {
			$data[$key] = $val;
		}
		$this->ffm->update($data);

		$this->save_json($ctl);
	}

	function update(Controller $ctl) {
		$classname = $ctl->POST("classname");
		$en = $ctl->POST("en");
		$jp = $ctl->POST("jp");

		$list = $this->ffm->select(["classname", "en"], [$classname, $en]);
		if (count($list) == 0) {
			$d = array();
			$d["classname"] = $classname;
			$d["en"] = $en;
			$d["jp"] = $jp;
			$this->ffm->insert($d);
		} else {
			$d = $list[0];
			$d["jp"] = $jp;
			$this->ffm->update($d);
		}

		$this->save_json($ctl);
	}

	function all_clear(Controller $ctl) {

		$ctl->show_multi_dialog("DeleteAll", "all_clear.tpl", "Edit Translation");
	}

	function all_clear_exe(Controller $ctl) {
		$this->ffm->allclear();

		$ctl->close_multi_dialog("DeleteAll");

		$ctl->ajax("lang", "showlist");
	}

	function blank_clear(Controller $ctl) {

		$list = $this->ffm->getall("en", SORT_ASC);

		foreach ($list as $d) {
			if (empty($d["jp"])) {
				$this->ffm->delete($d["id"]);
			}
		}

		$ctl->ajax("lang", "edit");
	}

	function list(Controller $ctl) {

		$this->read_json($ctl);

		$list = $this->ffm->getall("en", SORT_ASC);

		$newlist = array();
		foreach ($list as $d) {
			$newlist[$d["classname"]][$d["en"]] = $d;
		}

		// 古いデータ（classnameが空欄の場合がある）の互換
		if (!empty($newlist[""])) {
			foreach ($newlist[""] as $key => $n) {
				foreach ($newlist as $class => $nc) {
					foreach ($nc as $en => $d) {
						if ($key == $en && empty($d["jp"])) {
							$d["jp"] = $newlist[""][$key]["jp"];
							$newlist[$class][$en] = $d;
							$this->ffm->update($d);
						}
					}
				}
			}
		}

		$ctl->append_res_data("list", $newlist);
	}

	function save_json(Controller $ctl) {
		if ($ctl->get_appcode() == "framework4") {
			$list = $this->ffm->getall("en", SORT_ASC);
			$json = json_encode($list);
			$fp = fopen(dirname(__FILE__) . "/base.json", 'w');
			flock($fp, LOCK_EX);
			fwrite($fp, $json);
			flock($fp, LOCK_UN);
			fclose($fp);
		}
	}

	function read_json(Controller $ctl) {
		if ($ctl->get_appcode() != "framework4") {
			if (is_file(dirname(__FILE__) . "/base.json")) {
				$fp = fopen(dirname(__FILE__) . "/base.json", 'r');
				flock($fp, LOCK_EX);
				$json = "";
				while ($line = fgets($fp)) {
					$json .= $line;
				}
				flock($fp, LOCK_UN);
				fclose($fp);

				unlink(dirname(__FILE__) . "/base.json");

				$list = json_decode($json, true);
				foreach ($list as $d) {
					$checklist = $this->ffm->select(["classname", "en"], [$d["classname"], $d["en"]]);
					if (count($checklist) == 0) {
						$this->ffm->insert($d);
					} else {
						$save_d = $checklist[0];
						foreach ($d as $key => $val) {
							if ($key != "id") {
								$save_d[$key] = $val;
							}
						}
						$this->ffm->update($save_d);
					}
				}
			}
		}
	}
}
