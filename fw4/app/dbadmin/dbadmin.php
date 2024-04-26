<?php

/*
 *  YOU CAN'T CHANGE THIS PROJECT.
 *  It will be overwritten when the framework updates.
 */

// gossi/php-code-formatter
// require __DIR__ . '/vendor/autoload.php';
include("formatter.php");

class dbadmin {

	private $ffm_customer;
	private $app_path;
	private $newctl;
	private $fmt_table_settings;
	private $fmt_child_tables;
	private $fmt_options;

	function __construct(Controller $ctl) {

		$dir = new Dirs();

		$this->app_path = $dir->appdir_user . "/";
		$this->newctl = $this->get_appctl();

		$this->fmt_table_settings = $ctl->db('table_settings');
		$this->fmt_child_tables = $ctl->db('child_tables');
		$this->fmt_options = $ctl->db('options');
	}

	//show database list available in the project
	function index(Controller $ctl) {



		$dirlist = array_diff(scandir($this->app_path), array('.', '..', '.htaccess'));

		$dblist = [];
		foreach ($dirlist as $key => $class) {
			if ($class == 'dbadmin' || $class == 'setting') {
				continue;
			}
			$path = $this->app_path . $class . '/fmt';
			if (!file_exists($path))
				continue;
			$fmtlist = array_diff(scandir($path), array('.', '..'));
			foreach ($fmtlist as $k => $db) {
				$dbname = str_replace('.fmt', '', $db);
				//check this is a cild
				$parent_table = $this->fmt_child_tables->select(['child_class', 'child_table'], [$class, $dbname]);
				if (empty($parent_table)) {
					$is_child = false;
				} else {
					$is_child = true;
				}

				$dblist[$class][] = $dbname;
				$dblist_child[$class][$dbname] = $is_child;
			}
		}
		$ctl->assign("dblist", $dblist);
		$ctl->assign("dblist_child", $dblist_child);
		$ctl->show_multi_dialog("db_list", "index.tpl", "Database List", 800, true, true);
	}

	//view database data
	function select(Controller $ctl, $dbclass = null, $db = null, $reload = false) {
		$post = $ctl->POST();

		if ($dbclass == null)
			$dbclass = $post['dbclass'];
		if ($db == null)
			$db = $post['db'];

		$database = $this->newctl->db($db, $dbclass);

		//read database columns
		$file = file($this->app_path . $dbclass . '/fmt/' . $db . '.fmt');
		foreach ($file as $key => $value) {
			$arr = explode(',', $value);
			$keys[] = $arr;
			$search_keys[] = $arr[0];
			$search_items[] = $reload ? "" : $post[$arr[0]];
		}
		$ctl->assign("keys", $keys);
		$ctl->assign("reload", $reload);

		// $data = $database->getall();
		$max = $ctl->increment_post_value('max', 10);
		$data = $database->filter($search_keys, $search_items, true, 'AND', null, SORT_ASC, $max, $is_last);
		$ctl->assign("data", $data);
		$ctl->assign("max", $max);
		$ctl->assign("is_last", $is_last);

		$ctl->assign("post", $post);
		$dbtitle = $dbclass . "\\" . $db;
		$dbname = $dbclass . "-" . $db;
		$ctl->show_multi_dialog("database-$dbname", "database.tpl", "Database: $dbtitle", 1000, true, true);
	}

	//view recorde adding page
	function insert(Controller $ctl) {
		$post = $ctl->POST();

		//read database columns
		$file = file($this->app_path . $post['dbclass'] . '/fmt/' . $post['db'] . '.fmt');
		foreach ($file as $key => $value) {
			$keys[] = explode(',', $value);
		}
		$ctl->assign("keys", $keys);

		$ctl->assign("post", $post);
		$ctl->show_multi_dialog("insert_data", "add.tpl", "Add Recorde", 500, true, true);
	}

	//add recorde data into the database
	function insert_exe(Controller $ctl) {
		$post = $ctl->POST();
		$dbclass = $post['dbclass'];
		$db = $post['db'];

		$database = $this->newctl->db($db, $dbclass);

		$database->insert($post);

		//close recorde adding page
		$ctl->close_multi_dialog("insert_data");

		//refresh db list
		$this->select($ctl, $dbclass, $db, true);
	}

	//view confirmation page when delete a recorede
	function delete(Controller $ctl) {
		$post = $ctl->POST();
		$ctl->assign("post", $post);
		$ctl->show_multi_dialog("delete", "delete.tpl", "delete", 500, true, true);
	}

	//delete recorde form database
	function delete_exe(Controller $ctl) {
		$post = $ctl->POST();
		$db = $post['db'];
		$dbclass = $post['dbclass'];

		$database = $this->newctl->db($db, $dbclass);

		$database->delete($post['id']);

		$ctl->close_multi_dialog("delete");
		//refresh recorde list
		$this->select($ctl, null, null, true);
	}

	//view edit page
	function edit(Controller $ctl) {
		$post = $ctl->POST();

		//read database columns
		$file = file($this->app_path . $post['dbclass'] . '/fmt/' . $post['db'] . '.fmt');
		foreach ($file as $key => $value) {
			$keys[] = explode(',', $value);
		}
		$ctl->assign("keys", $keys);

		$db = $post['db'];
		$dbclass = $post['dbclass'];

		$database = $this->newctl->db($db, $dbclass);

		$data = $database->get($post['id']);
		$ctl->assign("data", $data);

		$ctl->assign("post", $post);
		$ctl->show_multi_dialog("edit-" . $post['id'], "edit.tpl", "Edit - " . $post['id'], 500, true, true);
	}

	//save edited data into the database
	function edit_exe(Controller $ctl) {
		$post = $ctl->POST();

		$db = $post['db'];
		$dbclass = $post['dbclass'];

		$database = $this->newctl->db($db, $dbclass);

		$data = $database->get($post['id']);
		foreach ($_POST as $key => $val) {
			$data[$key] = $val;
		}

		//update database
		$database->update($data);

		//close the editing form
		$ctl->close_multi_dialog("edit-" . $post['id']);

		//refresh db list
		$this->select($ctl, null, null, true);
	}

	private function get_appctl() {
		return new Controller_class("", null);
	}

	//------------- code generator -------------
	//code generator step one
	function code_generator_step_one(Controller $ctl) {
		$post = $ctl->POST();

		//save parent data when open child page
		if (isset($post['save'])) {
			$tmp_dbclass = $post['dbclass'];
			$tmp_db = $post['db'];
			$_POST['dbclass'] = $post['parent_class'];
			$_POST['db'] = $post['parent_db'];
			$this->code_generator_save_settings($ctl);
			$_POST['dbclass'] = $tmp_dbclass;
			$_POST['db'] = $tmp_db;
		}

		$file = file($this->app_path . $post['dbclass'] . '/fmt/' . $post['db'] . '.fmt');
		foreach ($file as $key => $value) {
			$arr = explode(',', $value);
			$keys[] = $arr;
		}
		$ctl->assign("keys", $keys);
		$ctl->assign("post", $post);

		if (isset($post['level']) && !empty($post['level'])) {
			$ctl->assign('level', $post['level'] + 1);
		} else {
			$ctl->assign('level', 1);
		}

		//get params of fields
		$params = [];
		$params_exist = [];
		$table_settings = [];
		foreach ($keys as $key => $field) {
			$field_name = $field[0];
			$table_setting = $this->fmt_table_settings->select(['class_name', 'table_name', 'field_name'], [$post['dbclass'], $post['db'], $field_name]);
			$table_settings[$field_name] = $table_setting[0];
			if (empty($table_setting[0]['array_data'])) {
				$params_exist[$field_name] = 0;
				$params[$field_name] = ['1' => 'Yes', '2' => 'No'];
			} else {
				$params_exist[$field_name] = 1;
				$params[$field_name] = json_decode($table_setting[0]['array_data'], true);
			}
		}
		$ctl->assign('params', $params);
		$ctl->assign('params_exist', $params_exist);
		$ctl->assign('table_settings', $table_settings);

		$options = $this->fmt_options->select(['class_name', 'table_name'], [$post['dbclass'], $post['db']]);
		$ctl->assign('options', $options[0]);
		// var_dump($options);
		//child tables
		$child_tables = $this->fmt_child_tables->select(['parent_class', 'parent_table'], [$post['dbclass'], $post['db']]);
		$ctl->assign('child_tables', $child_tables);

		//check parents
		$parent_table = $this->fmt_child_tables->select(['child_class', 'child_table'], [$post['dbclass'], $post['db']]);
		if (!empty($parent_table)) {
			$ctl->assign('parent_class', $parent_table[0]['parent_class']);
			$ctl->assign('parent_db', $parent_table[0]['parent_table']);
		} else {
			$ctl->assign('parent_class', $post['parent_class']);
			$ctl->assign('parent_db', $post['parent_db']);
		}
		$ctl->show_multi_dialog("cg_step_one", "code_gen_1.tpl", "Code Generator Step 1", 1000, true, true);
	}

	public function code_generator_save_settings(Controller $ctl) {
		$post = $ctl->POST();

		//regenerate fmt file
		$this->table_designer_2_exe($ctl, true);

		//remove unrelated settings
		$table_settings = $this->fmt_table_settings->select(['class_name', 'table_name'], [$post['dbclass'], $post['db']]);
		foreach ($table_settings as $key => $setting) {
			if (!in_array($setting['field_name'], $post['fields'])) {
				$this->fmt_table_settings->delete($setting['id']);
			}
		}

		//save settings
		foreach ($post['fields'] as $key => $field_name) {
			$table_settings = $this->fmt_table_settings->select(['class_name', 'table_name', 'field_name'], [$post['dbclass'], $post['db'], $field_name]);

			$table_setting = $table_settings[0];
			$table_setting['field_name'] = $field_name;
			$table_setting['field_type'] = $post['types'][$field_name];
			$table_setting['validation_text_types'] = $post['text_types'][$field_name];
			$table_setting['validation_file_types'] = $post['file_types'][$field_name];
			$table_setting['list_flg'] = !empty($post['list'][$field_name]) ? 1 : 0;
			$table_setting['add_flg'] = !empty($post['add'][$field_name]) ? 1 : 0;
			$table_setting['edit_flg'] = !empty($post['edit'][$field_name]) ? 1 : 0;
			$table_setting['validate_flg'] = !empty($post['validation'][$field_name]) ? 1 : 0;
			$table_setting['search_flg'] = !empty($post['search'][$field_name]) ? 1 : 0;
			$table_setting['delete_flg'] = ($post['delete'] == $field_name) ? 1 : 0;

			if (empty($table_settings)) {
				$table_setting['class_name'] = $post['dbclass'];
				$table_setting['table_name'] = $post['db'];
				$this->fmt_table_settings->insert($table_setting);
			} else {
				$this->fmt_table_settings->update($table_setting);
			}
		}

		//save options
		$options = $this->fmt_options->select(['class_name', 'table_name'], [$post['dbclass'], $post['db']]);
		$option = $options[0];
		if ($post['page_style'] == 'edit_page') {
			$option['list_type'] = 'list';
			$option['edit_type'] = 'page';
		} elseif ($post['page_style'] == 'edit_inline') {
			$option['list_type'] = 'list';
			$option['edit_type'] = 'inline';
		} elseif ($post['page_style'] == 'drag_drop') {
			$option['list_type'] = 'combine';
			$option['edit_type'] = 'page';
		}
		$option['page_style'] = $post['page_style'];
		if (empty($options)) {
			$option['class_name'] = $post['dbclass'];
			$option['table_name'] = $post['db'];
			$this->fmt_options->insert($option);
		} else {
			$this->fmt_options->update($option);
		}

		if ($post['back'] == 'true') {
			$_POST['dbclass'] = $post['parent_class'];
			$_POST['db'] = $post['parent_db'];
			$_POST['level'] = $post['level'] - 2;
			$this->code_generator_step_one($ctl);
		}
	}

	//step_one_exe and show download page
	function code_generator_step_two(Controller $ctl) {
		$post = $ctl->POST();
		$ctl->assign("post", $post);

		//save settings and get params
		$this->code_generator_save_settings($ctl);

		$settings = $ctl->get_setting();
		$svn_repo_url = $settings['svn_repo_url'];
		$svn_username = $settings['svn_username'];
		$svn_password = $settings['svn_password'];

		$zip_folder_main = __DIR__ . '/../../data';
		if ($post['download']) {
			@mkdir($zip_folder_main);
			$zip_folder_main .= '/upload';
			@mkdir($zip_folder_main);
			$zip_folder_main .= '/code_generator';
			@mkdir($zip_folder_main);
			$zip_folder_main .= '/' . $post['dbclass'] . '_' . $post['db'];
			@mkdir($zip_folder_main);
			@mkdir($zip_folder_main . '/common');
		}

		//svn checkout
		$dir = new Dirs();
		$tmp_path = $dir->tmpdir;
		if ($post['svn']) {
			//check credentials
			if (empty($svn_repo_url) || empty($svn_username) || empty($svn_password)) {
				$error = 'SVN credentials are not added.';
				$ctl->reload_area('#codegen_error', $error);
				return;
			}

			//svn checkout
			chdir($tmp_path);
			exec("/usr/bin/svn checkout $svn_repo_url --username $svn_username --password $svn_password", $svn_res);
			if (empty($svn_res)) {
				$error = 'Something wrong, SVN is not responed.';
				$ctl->reload_area('#codegen_error', $error);
				return;
			}
			$svn_res = '';

			$repo_path = $tmp_path . "/repo";
			if (is_dir($repo_path . "/classes")) {
				$svn_class_path = $repo_path . "/classes/app";
			} elseif (is_dir($repo_path . "/app")) {
				$svn_class_path = $repo_path . "/app";
			} else {
				$svn_class_path = "";
				$error = 'Something wrong, app folder not found.';
				$ctl->reload_area('#codegen_error', $error);
				return;
			}

			// dump files
			// $files = array_diff(scandir($repo_path), array('.', '..'));
			// var_dump($files);
		}

		$class_name_str = ucwords(str_replace("_", " ", $post['dbclass']));

		$this->generate_code($ctl, $post['dbclass'], $post['db'], $zip_folder_main);

		//save menu
		$menu_path = $this->app_path . "/common/menu.tpl";
		$menu_txt = file_get_contents($menu_path);
		if (strstr($menu_txt, "data-class='" . $post["dbclass"] . "'") === false) {
			$menu_txt .= "\n <a class='ajax-link lang' data-class='" . $post['dbclass'] . "' data-function='page'>" . $class_name_str . "</a>";
		}
		if ($post['server'] || $post['svn'])
			file_put_contents($menu_path, $menu_txt);
		if ($post['download'])
			copy($menu_path, $zip_folder_main . '/common/menu.tpl');
		if ($post['svn'])
			copy($menu_path, $svn_class_path . '/common/menu.tpl');

		//svn commit
		if ($post['svn']) {
			chdir($repo_path);
			// $svn_res = [];
			exec("/usr/bin/svn add . --force", $svn_res);
			// var_dump($svn_res); 
			// $svn_res = [];
			exec("/usr/bin/svn commit -m 'Code Generator - " . $post['dbclass'] . "' --username $svn_username --password $svn_password", $svn_res);
			// var_dump($svn_res);$svn_res = [];
			// exec("/usr/bin/svn update", $svn_res);
		}

		// dump files
		// $files = array_diff(scandir($this->app_path."/b_child/"), array('.', '..'));
		// $files = array_diff(scandir($repo_path."/app/b_test"), array('.', '..'));
		// $files = array_diff(scandir($zip_folder_main), array('.', '..'));
		// $files = array_diff(scandir($zip_folder_main."/../"), array('.', '..'));
		// $files = array_diff(scandir($zip_folder_main."/common"), array('.', '..'));
		// $files = array_diff(scandir($repo_path."/classes/app/"), array('.', '..'));
		// var_dump($files);

		if ($post['svn'])
			$this->removeDirectory($repo_path);

		$ctl->close_multi_dialog("cg_step_one");

		if (!empty($post['download']))
			$ctl->show_multi_dialog("cg_step_two", "code_gen_2.tpl", "Code Generator Step 2", 500, true, true);
	}

	private function generate_code($ctl, $class_name, $db_name, $zip_folder_main, $is_child = false, $parent_class = '', $parent_db = '', $parent_combine_enabled = false) {
		//check wheter this class has childs, then call this function recursively
		$childs = $this->fmt_child_tables->select(['parent_class', 'parent_table'], [$class_name, $db_name]);
		$options = $this->fmt_options->select(['class_name', 'table_name'], [$class_name, $db_name]);
		$combine_enabled = false;
		$sort_enabled = false;
		if ($options[0]['list_type'] == 'combine' && !empty($childs)) {
			$combine_enabled = true;
			$sort_enabled = true;
		}

		//generate child data
		foreach ($childs as $key => $child) {
			$this->generate_code($ctl, $child['child_class'], $child['child_table'], $zip_folder_main, true, $child['parent_class'], $child['parent_table'], $combine_enabled);
		}
		$ctl->assign('childs', $childs);
		$ctl->assign('parent_combine_enabled', $parent_combine_enabled);

		//
		$post = $ctl->POST();
		$ctl->assign('class_name', $class_name);
		$ctl->assign('db_name', $db_name);
		$ctl->assign('is_child', $is_child);
		$ctl->assign('parent_class', $parent_class);
		$ctl->assign('parent_db', $parent_db);

		$db_name_str = ucwords(str_replace("_", " ", $db_name));
		$ctl->assign('db_name_str', $db_name_str);

		$table_settings = $this->fmt_table_settings->select(['class_name', 'table_name'], [$class_name, $db_name], true, "AND", 'id', SORT_ASC);
		$ctl->assign('table_settings', $table_settings);
		// var_dump($table_settings);

		if ($options[0]['edit_type'] == 'inline' || $combine_enabled || $parent_combine_enabled)
			$sort_enabled = true;
		$ctl->assign('options', $options[0]);
		$ctl->assign('sort_enabled', $sort_enabled);
		$ctl->assign('combine_enabled', $combine_enabled);

		//get options params of the db
		$params = [];
		$search_elements = [];
		$enable_add = false;
		$enable_edit = false;
		$enable_list = false;
		$enable_delete = false;
		$enable_search = false;
		foreach ($table_settings as $key => $item) {
			$field_type = $item['field_type'];
			if ($field_type == 'select' || $field_type == 'radio' || $field_type == 'checkbox') {
				if (empty($item['array_data'])) {
					if ($field_type == 'select')
						$params[$item['field_name']] = "[''=>'', '1'=>'Yes', '2'=>'No']";
					else
						$params[$item['field_name']] = "['1'=>'Yes', '2'=>'No']";
				} else {
					$param_arr = json_decode($item['array_data'], true);
					if (!empty($param_arr)) {
						if ($field_type == 'select')
							$param_arr = ['' => ''] + $param_arr;
						$param_str = "[";
						foreach ($param_arr as $key => $value) {
							$param_str .= "'$key'=>'" . str_replace("'", "\'", $value) . "',";
						}
						$param_str = rtrim($param_str, ",");
						$param_str .= "]";
						$params[$item['field_name']] = $param_str;
					}
				}
			}

			//get delete field
			if ($item['delete_flg']) {
				$enable_delete = true;
				$delete_field = $item['field_name'];
			}
			//get search field list
			if ($item['search_flg']) {
				$enable_search = true;
				$search_elements[] = $item['field_name'];
			}
			if ($item['add_flg'])
				$enable_add = true;
			if ($item['list_flg'])
				$enable_list = true;
			if ($item['edit_flg'])
				$enable_edit = true;
		}
		$ctl->assign('params', $params);
		$ctl->assign('delete_field', $delete_field);
		$ctl->assign('enable_add', $enable_add);
		$ctl->assign('enable_edit', $enable_edit);
		$ctl->assign('enable_delete', $enable_delete);
		$ctl->assign('enable_list', $enable_list);
		$ctl->assign('enable_search', $enable_search);

		/* if (empty($post['add'])) $post['add'] = [];
		  if (empty($post['edit'])) $post['edit'] = [];
		  if (empty($post['list'])) $post['list'] = [];
		  if (empty($post['search'])) $post['search'] = []; */

		//difine paths
		$dir = new Dirs();
		$tmp_path = $dir->tmpdir;
		$repo_path = $tmp_path . "/repo";
		if (is_dir($repo_path . "/classes")) {
			$svn_class_path = $repo_path . "/classes/app/" . $class_name;
		} elseif (is_dir($repo_path . "/app")) {
			$svn_class_path = $repo_path . "/app/" . $class_name;
		} else {
			$svn_class_path = "";
		}

		//create folders
		$class_folder = $this->app_path . "/" . $class_name;
		@mkdir($class_folder . "/Templates");
		if ($post['svn']) {
			@mkdir($svn_class_path);
			@mkdir($svn_class_path . "/Templates");
			@mkdir($svn_class_path . "/fmt");
		}
		$zip_folder = $zip_folder_main . "/" . $class_name;
		if ($post['download']) {
			@mkdir($zip_folder);
			@mkdir($zip_folder . "/Templates");
			@mkdir($zip_folder . "/fmt");
		}

		//define sample pages path
		if ($options[0]['page_style'] == 'weekly_calendar')
			$sample_path = "samples/weekly_calendar";
		elseif ($options[0]['page_style'] == 'monthly_calendar')
			$sample_path = "samples/monthly_calendar";
		else
			$sample_path = "samples";

		//generate list template
		if ($combine_enabled) {

			$ctl->assign('child_class_name', $childs[0]['child_class']);
			$ctl->assign('child_db_name', $childs[0]['child_table']);
			$child_table_settings = $this->fmt_table_settings->select(['class_name', 'table_name'], [$childs[0]['child_class'], $childs[0]['child_table']], true, "AND", 'id', SORT_ASC);
			$ctl->assign('child_table_settings', $child_table_settings);
			$params = [];
			foreach ($child_table_settings as $child_key => $child_item) {
				$field_type = $child_item['field_type'];
				if ($field_type == 'select' || $field_type == 'radio' || $field_type == 'checkbox') {
					if (empty($child_item['array_data'])) {
						if ($field_type == 'select')
							$child_params[$child_item['field_name']] = "[''=>'', '1'=>'Yes', '2'=>'No']";
						else
							$child_params[$child_item['field_name']] = "['1'=>'Yes', '2'=>'No']";
					} else {
						$param_arr = json_decode($child_item['array_data'], true);
						if (!empty($param_arr)) {
							if ($field_type == 'select')
								$param_arr = ['' => ''] + $param_arr;
							$param_str = "[";
							foreach ($param_arr as $child_key => $value) {
								$param_str .= "'$child_key'=>'" . str_replace("'", "\'", $value) . "',";
							}
							$param_str = rtrim($param_str, ",");
							$param_str .= "]";
							$child_params[$child_item['field_name']] = $param_str;
						}
					}
				}
			}
			$ctl->assign('child_params', $child_params);
			$index_page_txt = $ctl->fetch($sample_path . '/index-combine.tpl');
		} else {
			$index_page_txt = $ctl->fetch($sample_path . '/index.tpl');
		}
		$index_page_txt = str_replace('[CURLY_OPEN]', "{", $index_page_txt);
		if ($post['download'])
			$this->create_file($zip_folder . "/Templates/index.tpl", $index_page_txt, "tpl");
		if ($post['server'] || $post['svn'])
			$this->create_file($class_folder . "/Templates/index.tpl", $index_page_txt, "tpl");
		if ($post['svn'])
			$this->create_file($svn_class_path . "/Templates/index.tpl", $index_page_txt, "tpl");

		//generate add template
		$add_page_txt = $ctl->fetch($sample_path . '/add.tpl');
		$add_page_txt = str_replace('[CURLY_OPEN]', "{", $add_page_txt);
		if ($post['download'])
			$this->create_file($zip_folder . "/Templates/add.tpl", $add_page_txt, "tpl"); //create file in zip folder
		if ($post['server'] || $post['svn'])
			$this->create_file($class_folder . "/Templates/add.tpl", $add_page_txt, "tpl"); //create file in app folder
		if ($post['svn'])
			$this->create_file($svn_class_path . "/Templates/add.tpl", $add_page_txt, "tpl"); //create file to commit



			
//generate edit template
		$edit_page_txt = $ctl->fetch($sample_path . '/edit.tpl');
		$edit_page_txt = str_replace('[CURLY_OPEN]', "{", $edit_page_txt);
		if ($post['download'])
			$this->create_file($zip_folder . "/Templates/edit.tpl", $edit_page_txt, "tpl");
		if ($post['server'] || $post['svn'])
			$this->create_file($class_folder . "/Templates/edit.tpl", $edit_page_txt, "tpl");
		if ($post['svn'])
			$this->create_file($svn_class_path . "/Templates/edit.tpl", $edit_page_txt, "tpl");

		//generate delete template
		if (isset($delete_field)) {
			$delete_page_txt = $ctl->fetch($sample_path . '/delete.tpl');
			$delete_page_txt = str_replace('[CURLY_OPEN]', "{", $delete_page_txt);
			if ($post['download'])
				$this->create_file($zip_folder . "/Templates/delete.tpl", $delete_page_txt, "tpl");
			if ($post['server'] || $post['svn'])
				$this->create_file($class_folder . "/Templates/delete.tpl", $delete_page_txt, "tpl");
			if ($post['svn'])
				$this->create_file($svn_class_path . "/Templates/delete.tpl", $delete_page_txt, "tpl");
		}

		// copy fmt files
		$fmt_files = glob($this->app_path . "/" . $class_name . "/fmt/*");
		foreach ($fmt_files as $fmt_file) {
			$fmt_edited = false;
			//if this is a child, there should be a foreign key
			if ($parent_db != '') {
				$fmt_content = file_get_contents($fmt_file);
				if (strpos($fmt_content, $parent_db . '_id') === false) {
					$fmt_content .= "\n" . $parent_db . "_id,24,N";
					file_put_contents($fmt_file, $fmt_content);
					$fmt_edited = true;
				}
			}

			//check sort field available if sort enable, inline list default sort is enabled
			if ($sort_enabled) {
				$fmt_content = file_get_contents($fmt_file);
				if (strpos($fmt_content, 'sort') === false) {
					$fmt_content .= "\nsort,24,N";
					file_put_contents($fmt_file, $fmt_content);
					$fmt_edited = true;
				}
			}

			//remove emtpy lines
			if ($fmt_edited)
				file_put_contents($fmt_file, preg_replace('/\R+/', "\n", file_get_contents($fmt_file)));

			if ($post['download'])
				copy($fmt_file, $zip_folder . '/fmt/' . basename($fmt_file));
			if ($post['svn'])
				copy($fmt_file, $svn_class_path . '/fmt/' . basename($fmt_file));
		}

		//filter string
		$filter_name_str = '[';
		$filter_var_str = '[';
		$first = true;
		if (!empty($search_elements)) {
			foreach ($search_elements as $key => $value) {
				$filter_name_str .= (($first) ? '' : ', ') . '"' . $value . '"';
				$filter_var_str .= (($first) ? '' : ', ') . '$post["search_' . $value . '"]';
				$first = false;
			}
		}
		if ($is_child) {
			$filter_name_str .= ((empty($search_elements)) ? '' : ', ') . '"' . $parent_db . '_id"';
			$filter_var_str .= ((empty($search_elements)) ? '' : ', ') . '$post["' . $parent_db . '_id"]';
		}
		$ctl->assign('filter_name_str', $filter_name_str . ']');
		$ctl->assign('filter_var_str', $filter_var_str . ']');

		//generate class
		$class_txt = $ctl->fetch($sample_path . '/class.tpl');
		$class_txt = str_replace('[CURLY_OPEN]', "{", $class_txt);
		$class_txt = str_replace('&quot;', '"', $class_txt);
		if ($post['download'])
			$this->create_file($zip_folder . "/" . $class_name . ".php", $class_txt, "php");
		if ($post['server'] || $post['svn'])
			$this->create_file($class_folder . "/" . $class_name . ".php", $class_txt, "php");
		if ($post['svn'])
			$this->create_file($svn_class_path . "/" . $class_name . ".php", $class_txt, "php");

		//copy style and script files
		if ($post['download']) {
			copy(__DIR__ . '/Templates/' . $sample_path . '/script.js', $zip_folder . '/script.js');
			copy(__DIR__ . '/Templates/' . $sample_path . '/style.css', $zip_folder . '/style.css');
		}
		if ($post['server'] || $post['svn']) {
			copy(__DIR__ . '/Templates/' . $sample_path . '/script.js', $class_folder . '/script.js');
			copy(__DIR__ . '/Templates/' . $sample_path . '/style.css', $class_folder . '/style.css');
		}
		if ($post['svn']) {
			copy(__DIR__ . '/Templates/' . $sample_path . '/script.js', $svn_class_path . '/script.js');
			copy(__DIR__ . '/Templates/' . $sample_path . '/style.css', $svn_class_path . '/style.css');
		}
	}

	public function code_generator_set_params(Controller $ctl) {
		$post = $ctl->POST();
		$ctl->assign('post', $post);

		$table_settings = $this->fmt_table_settings->select(['class_name', 'table_name', 'field_name'], [$post['dbclass'], $post['db'], $post['field_name']]);
		if (empty($table_settings[0]['array_data'])) {
			$params = ['1' => 'Yes', '2' => 'No'];
		} else {
			$params = json_decode($table_settings[0]['array_data'], true);
		}
		$ctl->assign('params', $params);

		$ctl->show_multi_dialog('code_generator_set_params', 'code_gen_set_param.tpl', "Code Generator - Set Params");
	}

	public function code_generator_set_params_exe(Controller $ctl) {
		$post = $ctl->POST();
		$ctl->assign('post', $post);

		$table_settings = $this->fmt_table_settings->select(['class_name', 'table_name', 'field_name'], [$post['dbclass'], $post['db'], $post['field_name']]);
		if (empty($table_settings)) {
			$new_settings['class_name'] = $post['dbclass'];
			$new_settings['table_name'] = $post['db'];
			$new_settings['field_name'] = $post['field_name'];
			$params = ['1' => 'Yes', '2' => 'No'];
			$new_settings['array_data'] = json_encode($params);
			$this->fmt_table_settings->insert($new_settings);
		} else {
			$params = [];
			foreach ($post['param_value'] as $key => $value) {
				$params[$value] = $post['param_name'][$key];
			}
			if (empty($params)) {
				$params = ['1' => 'Yes', '2' => 'No'];
			}
			$new_settings = $table_settings[0];
			$new_settings['array_data'] = json_encode($params);
			$this->fmt_table_settings->update($new_settings);
		}

		$options_html = '';
		foreach ($params as $key => $value) {
			$options_html .= "$key:$value &nbsp;&nbsp;";
		}

		// $ctl->reload_area('#element_params_hidden_'.$post['field_name'], $hidden_html);
		$ctl->reload_area('#element_params_' . $post['field_old_name'], substr($options_html, 0, 200));
		$ctl->close_multi_dialog('code_generator_set_params');
	}

	//format the code and save file
	public function create_file($path, $txt, $format = false) {
		$myfile = fopen($path, "w") or die("Unable to open file!");

		//formatter
		/* if ($format) {
		  $formatter = new gossi\formatter\Formatter("default");
		  $txt = $formatter->format($txt);
		  $txt = str_replace('Controller$ctl', 'Controller $ctl', $txt);
		  } */

		fwrite($myfile, $txt);
		fclose($myfile);

		//Mr Nakama's Formatter
		if ($format == 'php') {
			$f = new formatter(true);
			$f->format($path, "{", "}");
		}
		if ($format == 'tpl') {
			$f = new formatter(true);
			$f->format($path, ["<div", "<form", "<script", "{if"], ["</div", "</form", "</script", "{/if"], "else");
		}
	}

	public function code_generator_download(Controller $ctl) {
		$post = $ctl->POST();
		$sub_folder = $post['dbclass'] . "_" . $post['db'];

		$folder = __DIR__ . "/../../data/upload/code_generator/$sub_folder/";
		$file = __DIR__ . "/../../data/upload/code_generator/$sub_folder.zip";

		if (file_exists($file)) {
			unlink($file);
		} else {
			//generate zip
			$this->Zip($folder, $file);
			$ctl->close_multi_dialog("cg_step_two");
			$ctl->res_saved_file("code_generator/$sub_folder.zip");
			unlink($file);
			$this->removeDirectory($folder);
		}
	}

	function Zip($source, $destination) {
		if (!extension_loaded('zip') || !file_exists($source)) {
			return false;
		}

		$zip = new ZipArchive();
		if (!$zip->open($destination, ZIPARCHIVE::CREATE)) {
			return false;
		}

		$source = str_replace('\\', '/', realpath($source));
		if (is_dir($source) === true) {
			$files = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($source), RecursiveIteratorIterator::SELF_FIRST);
			foreach ($files as $file) {
				$file = str_replace('\\', '/', $file);
				// Ignore "." and ".." folders
				if (in_array(substr($file, strrpos($file, '/') + 1), array('.', '..')))
					continue;
				$file = realpath($file);
				if (is_dir($file) === true) {
					$zip->addEmptyDir(str_replace($source . '/', '', $file . '/'));
				} else if (is_file($file) === true) {
					$zip->addFromString(str_replace($source . '/', '', $file), file_get_contents($file));
				}
			}
		} else if (is_file($source) === true) {
			$zip->addFromString(basename($source), file_get_contents($source));
		}

		return $zip->close();
	}

	public function removeDirectory($path) {
		$files = glob($path . '/{,.}*[!.]*', GLOB_MARK | GLOB_BRACE);
		// $files = array_diff(scandir($path."/"), array('.', '..'));
		// var_dump($files);
		// die();
		foreach ($files as $file) {
			is_dir($file) ? $this->removeDirectory($file) : unlink($file);
		}
		rmdir($path);
		return;
	}

	public function add_new_field(Controller $ctl) {
		$post = $ctl->POST();
		$fmt_file = $this->app_path . $post['dbclass'] . "/fmt/" . $post['db'] . ".fmt";
		// var_dump($fmt_file);
		$fmt_content = file_get_contents($fmt_file);
		$fmt_content .= "\nnew_codegen_" . floor(microtime(true) * 1000) . ",255,T";
		file_put_contents($fmt_file, preg_replace('/\R+/', "\n", $fmt_content));
		$_POST['level'] = $post['level'] - 1;
		$this->code_generator_step_one($ctl);
	}

	//----- table designer -----

	public function table_designer_1(Controller $ctl) {
		$post = $ctl->POST();
		if (isset($post['save'])) {
			$this->code_generator_save_settings($ctl);
		}
		$ctl->assign('post', $post);

		if (isset($post['level']))
			$ctl->assign('level', $post['level']);

		$ctl->show_multi_dialog("table_designer_step_one", "table_designer_1.tpl", "Table designer Step 1", 500, true, true);
	}

	public function table_designer_1_exe(Controller $ctl) {
		$post = $ctl->POST();
		$ctl->assign('post', $post);
		$class_name = strtolower($post['class_name']);
		$class_name = str_replace(" ", "_", $class_name);

		$table_name = strtolower($post['table_name']);
		$table_name = str_replace(" ", "_", $table_name);

		if (empty($class_name) || empty($table_name)) {
			$ctl->assign('error', "Validation Faild");
			$this->table_designer_1($ctl);
			return;
		}

		$fmt_file = $this->app_path . "/" . $class_name . "/fmt/" . $table_name . ".fmt";
		$class_file = $this->app_path . "/" . $class_name . "/" . $class_name . ".php";
		if (is_dir($this->app_path . "/" . $class_name) || file_exists($class_file)) {
			$ctl->assign('error', "Class already exist.");
			$this->table_designer_1($ctl);
			return;
		} elseif (file_exists($fmt_file)) {
			$ctl->assign('error', "Database already exist.");
			$this->table_designer_1($ctl);
			return;
		} else {
			@mkdir($this->app_path . "/" . $class_name);
			@mkdir($this->app_path . "/" . $class_name . "/fmt");
			if (!empty($post['parent_class']) && !empty($post['parent_db'])) {
				$fmt_txt = "id,24,N\n" . $post['parent_db'] . "_id,24,N";
				//add foeing key
				if (strstr($fmt_txt, $post['parent_db'] . "_id") === false) {
					$fmt_txt .= $post['parent_db'] . "_id,24,N";
				}

				//save in to child db
				$parent_row = $this->fmt_child_tables->select(['parent_class', 'parent_table', 'child_class', 'child_table'], [$post['parent_class'], $post['parent_db'], $class_name, $table_name]);
				if (empty($parent_row)) {
					$data = [
					    'parent_class' => $post['parent_class'], 'parent_table' => $post['parent_db'],
					    'child_class' => $class_name, 'child_table' => $table_name
					];
					$this->fmt_child_tables->insert($data);
				}
			} else {
				$fmt_txt = "id,24,N";
			}
			$this->create_file($fmt_file, $fmt_txt);
		}

		$_POST['dbclass'] = $class_name;
		$_POST['db'] = $table_name;
		$_POST['new'] = true;

		$this->index($ctl);

		if (!empty($post['parent_class']) && !empty($post['parent_db'])) {
			if ($post['level'] == 1)
				$_POST['level'] = "";
			else
				$_POST['level'] = $post['level'] - 1;

			$_POST['dbclass'] = $post['parent_class'];
			$_POST['db'] = $post['parent_db'];
			$_POST['parent_class'] = "";
			$_POST['parent_db'] = "";
			$this->code_generator_step_one($ctl);
		} else {
			$this->table_designer_2($ctl);
		}

		$ctl->close_multi_dialog("table_designer_step_one");
		// $this->code_generator_step_one($ctl);
	}

	public function table_designer_2(Controller $ctl) {
		$post = $ctl->POST();
		$file = file($this->app_path . $post['dbclass'] . '/fmt/' . $post['db'] . '.fmt');
		foreach ($file as $key => $value) {
			$arr = explode(',', $value);
			$keys[] = $arr;
		}
		$ctl->assign("keys", $keys);
		$ctl->assign("post", $post);

		if (isset($post['level']))
			$ctl->assign('level', $post['level']);

		$ctl->close_multi_dialog('table_designer_step_one');
		$ctl->show_multi_dialog("table_designer_step_two", "table_designer_2.tpl", "Table designer Step 2", 700, true, true);
	}

	public function table_designer_2_exe(Controller $ctl, $from_generator = false) {
		$post = $ctl->POST();
		$table_text = "";
		//validate inputs and create content of file
		foreach ($post['fields'] as $key => $value) {
			if (!empty($value)) {
				if (empty($post['field_length'][$key])) {
					// $ctl->assign('error', "Validation Faild");
					// $this->table_designer_2($ctl);
					if ($from_generator) {
						$ctl->reload_area('#codegen_error', 'Validation Faild');
					} else {
						$ctl->reload_area('#table_designer_2_error', 'Validation Faild');
					}
					return;
				} else {
					$col_name = strtolower($value);
					$col_name = str_replace(" ", "_", $col_name);
					if ($from_generator) {
						//define field type acording to element
						if ($post['types'][$value] == 'number' || $post['types'][$value] == 'select' || $post['types'][$value] == 'radio') {
							$field_type = "N";
						} elseif ($post['types'][$value] == 'text' || $post['types'][$value] == 'textarea' || $post['types'][$value] == 'date' || $post['types'][$value] == 'checkbox' || $post['types'][$value] == 'file') {
							$field_type = "T";
						} elseif ($post['types'][$value] == 'float') {
							$field_type = "F";
						} else {
							// $field_type = "T";
						}
					} else {
						$field_type = $post['field_type'][$key];
					}
					if ($col_name == 'id') {
						$field_type = "N";
					}
					$table_text .= $col_name . "," . $post['field_length'][$key] . "," . $field_type . "\n";
				}
			}
		}

		if (!$from_generator) {
			//check parent table exist or create
			if (!empty($post['parent_class']) && !empty($post['parent_db'])) {
				$parent_row = $this->fmt_child_tables->select(['parent_class', 'parent_table', 'child_class', 'child_table'], [$post['parent_class'], $post['parent_db'], $post['dbclass'], $post['db']]);
				if (empty($parent_row)) {
					$data = [
					    'parent_class' => $post['parent_class'], 'parent_table' => $post['parent_db'],
					    'child_class' => $post['dbclass'], 'child_table' => $post['db']
					];
					$this->fmt_child_tables->insert($data);
				}

				if (strstr($table_text, $post['parent_db'] . "_id") === false) {
					$table_text .= $post['parent_db'] . "_id,24,N";
				}
			}
		}

		$this->create_file($this->app_path . "/" . $post['dbclass'] . "/fmt/" . $post['db'] . ".fmt", $table_text);

		if (!$from_generator) {
			$ctl->close_multi_dialog('table_designer_step_two');
			$this->index($ctl);
			// $this->code_generator_step_one($ctl);
		}
	}

	public function delete_child_db(Controller $ctl) {
		$post = $ctl->POST();
		$data = $this->fmt_child_tables->get($post['id']);
		$ctl->assign('data', $data);
		$ctl->assign('level', $post['level'] - 1);
		$ctl->show_multi_dialog("delete_child_db", "delete_child_db.tpl", "Delete Child Table", 500, true, true);
	}

	public function delete_child_db_exe(Controller $ctl) {
		$id = $ctl->POST('id');
		$this->fmt_child_tables->delete($id);
		$ctl->close_multi_dialog("delete_child_db");
		$this->code_generator_step_one($ctl);
	}

	public function change_field_name_realtime(Controller $ctl) {
		$post = $ctl->POST();
		$table_setting = $this->fmt_table_settings->select(['class_name', 'table_name', 'field_name'], [$post['dbclass'], $post['db'], $post['field_old_name']]);

		if (!empty($table_setting)) {
			$table_setting[0]['field_name'] = str_replace(" ", "_", $post['field_new_name']);
			$this->fmt_table_settings->update($table_setting[0]);
		}
	}

	//codegenerator for calendar
	public function add_default_colums_to_weekly_calendar(Controller $ctl) {
		$this->code_generator_save_settings($ctl);

		$post = $ctl->POST();
		$fmt_file = $this->app_path . $post['dbclass'] . "/fmt/" . $post['db'] . ".fmt";
		$fmt_content = file_get_contents($fmt_file);

		if (strpos($fmt_content, 'user_id') === false)
			$fmt_content .= "\nuser_id,24,N";
		if (strpos($fmt_content, 'scheduled_date') === false)
			$fmt_content .= "\nscheduled_date,255,T";
		if (strpos($fmt_content, 'start_time') === false)
			$fmt_content .= "\nstart_time,255,T";
		if (strpos($fmt_content, 'end_time') === false)
			$fmt_content .= "\nend_time,255,T";

		//remove empty line and save db file
		file_put_contents($fmt_file, preg_replace('/\R+/', "\n", $fmt_content));

		$_POST['level'] = $_POST['level'] - 1;
		$this->code_generator_step_one($ctl);
	}
}
