<?php

class ai_db_handling {

	private $table_name;   // Tar
	private $ffm;   // Target DB Instance
	private $ai_setting;   // AI Setting called by screen or ai
	private $ai_db; // ai_db instance
	private $fields;       // Field list from AI Setting (NOT FROM DATABASE)
	private $ai_setting_parameters;
	private $fmt_ai_setting_subtable;
	private $fmt_ai_db; 
	private $fmt_ai_fields; 
	private $fmt_ai_setting;
	private $fmt_ai_setting_parameters;
	private $database_handling_function_name = [
	    1 => "add",
	    2 => "edit",
	    3 => "delete",
	    4 => "search",
	    5 => "table",
	    6 => "pdf",
	    7 => "csv",
	    8 => "mail",
	];

	function __construct(Controller $ctl) {
		
		// This class is common management side and pubic side
		if($ctl->GET("public") == "true" || $ctl->POST("public") == "true"){
			$this->flg_public = true;
			$ctl->set_check_login(false);
			$this->ajax_option = ["public"=>"true"];
			$ctl->assign("public","true");
		}else{
			$this->ajax_option = ["pubic"=>"false"];
		}
		
		
		// Getting ai_setting from ai_setting_id posted
		$ffm_ai_setting = $ctl->db("ai_setting", "ai_settings");
		$this->ai_setting = $ffm_ai_setting->get($ctl->POST("ai_setting_id"));
		$this->fmt_ai_setting = $ffm_ai_setting;
		
		$ffm_ai_setting_parameters = $ctl->db("ai_setting_parameters", "ai_settings");
		$this->ai_setting_parameters = $ffm_ai_setting_parameters->select('ai_setting_id', $ctl->POST("ai_setting_id"), false, "AND", 'sort', SORT_ASC);
		$this->fmt_ai_setting_parameters = $ffm_ai_setting_parameters;
		
		$this->fmt_ai_setting_subtable = $ctl->db("ai_setting_subtable", "ai_settings");

		// Getting ai_db from table_name posted
		$ffm_ai_db = $ctl->db("ai_db", "ai_db");
		$this->ai_db = $ffm_ai_db->get($this->ai_setting["ai_db_id"]);

		// Getting target DB and make DB Instance
		$this->table_name = $this->ai_db["tb_name"];
		$this->ffm = $ctl->db($this->table_name, "common");

		// Getting field list related ai_db
		$ffm_fields = $ctl->db("ai_fields", "ai_db");
		$this->fields = $ffm_fields->select("ai_db_id", $this->ai_setting["ai_db_id"], true, "AND", "sort", SORT_DESC);
		foreach ($this->fields as $key => $value) {
		    if($value['type']=='dropdown' || $value['type']=='checkbox' || $value['type']=='radio'){
			$constant_array = $ctl->get_constant_array($value['constant_array_name']);
			$this->fields[$key]['options']=$constant_array;
		    }
		    
		}
		//var_dump($ctl->POST());
		$this->fmt_ai_db = $ctl->db("ai_db", "ai_db");
		$this->fmt_ai_fields = $ctl->db("ai_fields", "ai_db");
	}

	function table(Controller $ctl,$is_csv=null) {

		// get search items and values from session
		$search_items = $ctl->get_session("_SEARCH_ITEMS");
		$search_values = $ctl->get_session("_SEARCH_VALUES");

		if (!is_array($search_items)) {
			$search_items = [];
		}
		if (!is_array($search_values)) {
			$search_values = [];
		}

		// Getting datas
		$max = $ctl->POST("max");
		$list = $this->ffm->filter($search_items, $search_values, false, "AND", "ID", SORT_DESC, $max);
		
		foreach ($list as $key => $value) {
		    $checkbox_val=[];
		    foreach ($this->fields as $keyf => $valuef) {
			if(is_array($value[$valuef['parameter_name']])){
			    foreach ($value[$valuef['parameter_name']] as $item){
				$checkbox_val[]=$valuef["options"][$item];
			    }
			    $list[$key][$valuef['parameter_name']]= implode(', ', $checkbox_val);
			}
		    }
		}
		$ai_setting_edit=false;
		$ai_setting_delete=false;
		$all_ai_setting= $this->fmt_ai_setting->select('ai_db_id', $this->ai_setting['ai_db_id']);
		    foreach ($all_ai_setting as $key => $value) {
			if($value['handling']==2){
			    $ai_setting_edit=true;
			}
			if($value['handling']==3){
			    $ai_setting_delete=true;
			}
		    }
		$ctl->assign("ai_setting_edit", $ai_setting_edit);
		$ctl->assign("ai_setting_delete", $ai_setting_delete);

		// Assign parameters
		$ctl->assign("list", $list);
		$ctl->assign("fields", $this->fields);
		$data = $ctl->POST();
		$data['is_csv']=$is_csv;
		$ctl->assign("data", $data);
		$ctl->assign("search_values", $search_values);
		
		// Show the data in chat
		$ctl->chat_clear();
		$ctl->chat_show_text($this->ai_setting["information"], "orange");
		$ctl->chat_show_html("table.tpl");
	}

	function add(Controller $ctl) {

		$data = $ctl->POST();
		if($this->ai_setting['parent_field']){
		    
		    $parent_f = $this->fmt_ai_fields->get($this->ai_setting['parent_field']);
		    $parent_tb = $this->fmt_ai_db->get($parent_f['ai_db_id']);
		    $parent_fields = $this->fmt_ai_fields->select("ai_db_id", $parent_f['ai_db_id'], true, "AND", "sort", SORT_ASC);
		    foreach ($parent_fields as $key => $value) {
			$parent_field_opt[$value['id']]=$value['parameter_title'];
		    }
		    //var_dump($parent_fields);
		    $ctl->assign("parent_f", $parent_f);
		    $ctl->assign("parent_tb", $parent_tb['tb_name']);
		    $ctl->assign("parent_field_opt", $parent_field_opt);
		    $data['parent_field']=$this->ai_setting['parent_field'];
		}
		$add_fields=[];
		foreach ($this->fields as $key => $value) {
			foreach ($this->ai_setting_parameters as $keyp => $valuep) {
			    if($value['id']==$valuep['ai_fields_id']){
				$add_fields[]=$value;
			    }
			}
		    }
		$ctl->assign("fields", $add_fields);
		$ctl->assign("data", $data);
		//$ctl->show_multi_dialog("add_data", "add.tpl", "Add Data", 600, true, true);
		
		$ctl->chat_clear();
		$ctl->chat_show_text($this->ai_setting["information"],"orange");
		$ctl->chat_show_html("add.tpl");
	}

	function add_exe(Controller $ctl) {
		// execute add
		$post = $ctl->POST();
		//validation
		$errors = $this->validate_fields_data($ctl, $post, "add");
		if (count($errors)) {
			$ctl->assign('errors', $errors);
			$this->add($ctl);
			return;
		}
		//-----------------------------------
		// INSERT A DATA  
		//-----------------------------------
		$row = [];
		foreach ($this->fields as $field) {
		    $row[$field["parameter_name"]] = $post[$field["parameter_name"]];
		    
		    if($field['type']=='file' || $field['type']=='image'){
			
			$filename = $ctl->get_posted_filename($field["parameter_name"]);
			//check whether filename is empty
			if (!empty($filename)) {
				$image_extension = $ctl->get_posted_file_extention($field["parameter_name"]);
				$saved_image_file = $field['type'].'-' . time() . '.' . $image_extension;
				if ($ctl->is_saved_file($saved_image_file)) {
					$ctl->remove_saved_file($saved_image_file);
				}
				// Upload file to server 
				$saved = $ctl->save_posted_file($field["parameter_name"], $saved_image_file);
				$row[$field["parameter_name"]] = $saved_image_file;
			}
		    }
			
		}
		$this->ffm->insert($row);
		//$ctl->close_multi_dialog('add_data');
		$ctl->chat_clear();
		$ctl->chat_show_text("One record has been added.", "orange");
	}
	
	//validation
	function validate_fields_data(Controller $ctl, $post, $page) {
		$errors = [];
		//var_dump($post);
		foreach ($this->fields as $key => $field) {
		    if($field['validation']==1){
			 //var_dump($field);
			 if (empty($post[$field["parameter_name"]])) {
				$errors[$field["parameter_name"]] = $field["parameter_title"]." is required!";
			}
		    }
		   
		}
		//die();
		return $errors;
	}

	function edit(Controller $ctl) {
		// show confirm window
	    $post = $ctl->POST();
	    $ctl->assign("data", $post);
	    $list = $this->ffm->get($post['id']);
	    $all_ai_setting= $this->fmt_ai_setting->select('ai_db_id', $this->ai_setting['ai_db_id']);
	    foreach ($all_ai_setting as $key => $value) {
		if($value['handling']==2){
		    $add_ai_setting_id=$value['id'];
		}
	    }
	    if($add_ai_setting_id){
		$add_ai_params= $this->fmt_ai_setting_parameters->select('ai_setting_id', $add_ai_setting_id);
	    }
	    $row = [];
	    $add_fields=[];
		foreach ($this->fields as $field) {
		    $row[$field["parameter_name"]] = $list[$field["parameter_name"]];
		    if($add_ai_params){
			foreach ($add_ai_params as $keyp => $valuep) {
			    if($field['id']==$valuep['ai_fields_id']){
				$add_fields[]=$field;
			    }
			}
		    }
		}
		//var_dump($this->fields);
	    $ctl->assign("row", $row);
	    $ctl->assign("list", $list);
	    $ctl->assign("fields", $add_fields);
	    $ctl->show_multi_dialog("edit_data", "edit.tpl", "Edit Data", 600, true, true);
	}

	function edit_exe(Controller $ctl) {
		// execute edit
	    $post = $ctl->POST();
	    //validation
		$errors = $this->validate_fields_data($ctl, $post, "edit");
		if (count($errors)) {
			$ctl->assign('errors', $errors);
			$this->edit($ctl);
			return;
		}
	    $row = $this->ffm->get($post['id']);
	    
		foreach ($this->fields as $field) {
		    if($post[$field["parameter_name"]]){
			$row[$field["parameter_name"]] = $post[$field["parameter_name"]];
		    }
			if($field['type']=='file' || $field['type']=='image'){
			    $filename = $ctl->get_posted_filename($field["parameter_name"]);
			    //check whether filename is empty
			    if (!empty($filename)) {
				    $image_extension = $ctl->get_posted_file_extention($field["parameter_name"]);
				    $saved_image_file = $field['type'].'-' . time() . '.' . $image_extension;
				    if ($ctl->is_saved_file($saved_image_file)) {
					    $ctl->remove_saved_file($saved_image_file);
				    }
				    // Upload file to server 
				    $saved = $ctl->save_posted_file($field["parameter_name"], $saved_image_file);
				    $row[$field["parameter_name"]] = $saved_image_file;
			    }
			}
		    
		}
	    $this->ffm->update($row);
	    $ctl->close_multi_dialog('edit_data');
	    $this->table($ctl);
	}

	function delete(Controller $ctl) {
		// show confirm window
	    $post = $ctl->POST();
	    $ctl->assign("data", $post);
	    $list = $this->ffm->get($post['id']);
	    $items = $this->fmt_ai_setting->select('ai_db_id', $this->ai_setting['ai_db_id']);
	    foreach ($items as $key => $value) {
		if($value['handling']==3){
		    $del_stting_id=$value['id'];
		}
		
	    }
	    if($del_stting_id){
		$del_params = $this->fmt_ai_setting_parameters->select('ai_setting_id', $del_stting_id);
		$del_fields=[];
		foreach ($del_params as $key => $value) {
		    $del_fields[]=$value['ai_fields_id'];
		}
		
	    }
	    $del_params_list=[];
	    foreach ($this->fields as $key => $value) {
		if(in_array($value['id'], $del_fields)){
		    $del_params_list[] = $value;
		}
	    }
//	    var_dump($del_params_list);
//	    die();
	    $ctl->assign("list", $list);
	    $ctl->assign("del_params", $del_params_list);
	    $ctl->show_multi_dialog("delete_data", "delete.tpl", "Delete Data", 600, true, true);
	}

	function delete_exe(Controller $ctl) {
		// execute delete
	    $post = $ctl->POST();
	    $ctl->assign("data", $post);
	    $this->ffm->delete($post['id']);
	    $ctl->close_multi_dialog('delete_data');
	    $this->table($ctl);
	}

	function search(Controller $ctl) {
	    $post = $ctl->POST();
	    $ctl->assign("data", $post);
	    $search_fields=[];
		foreach ($this->fields as $key => $value) {
		    foreach ($this->ai_setting_parameters as $keyp => $valuep) {
			if($value['id']==$valuep['ai_fields_id']){
			    $search_fields[]=$value;
			}
		    }
		}
	    $ctl->assign("fields", $search_fields);
	    $ctl->chat_clear();
	    $ctl->chat_show_text($this->ai_setting["information"], "orange");
	    $ctl->chat_show_html("search.tpl");
	}

	function search_exe(Controller $ctl) {
		// set search items and values to session
		// and call $ctl->ajax to show the table.
	    	// show search from
	    $post = $ctl->POST();
	    $ctl->assign("data", $post);
	    $search_key = [];
	    $search_val = [];
	    foreach ($post as $key => $value) {
		if (strpos($key, 'search_') !== false) {
		    
		    if($value){
			$search_key[] = str_replace("search_","",$key);
			$search_val[str_replace("search_","",$key)] = $value;
		    }
		    
		}
	    }
	    $ctl->set_session('_SEARCH_ITEMS', $search_key);
	    $ctl->set_session('_SEARCH_VALUES', $search_val);
	    
		// Getting datas
		$max = $ctl->POST("max");
		$list = $this->ffm->filter($search_key, $search_val, false, "AND", "ID", SORT_DESC, $max);
		$result_count = count($list);
		$search_msg = $result_count.' rows filterd.';
		$items = $this->fmt_ai_setting->getall();
//		var_dump($post);
//		echo '<br><br>';
		//var_dump($items);
		foreach ($items as $key => $value) {
		    if($value['id']==$post['ai_setting_id']){
			$search_db_id=$value['ai_db_id'];
			$ai_db = $this->fmt_ai_db->get($value["ai_db_id"]);
		    }
		}
		$manage_items = [];
		foreach ($items as $key => $value) {
		    if($value['ai_db_id']==$search_db_id && ($value['handling'] == 5 || $value['handling'] == 6)){
			$arr = [];
			$arr["class_name"] = "ai_db_handling";
			$arr["function_name"] = $this->database_handling_function_name[$value["handling"]];
			$arr["description"] = $value["description"];
			$arr["menu_name"] = $value["menu_name"];
			$arr["information"] = $value["information"];
			$arr["table_name"] = $ai_db["tb_name"];
			$arr["ai_setting_id"] = $value["id"];
			$manage_items[] = $arr;
		    }
		}
		$ctl->assign("manage_items", $manage_items);
		$ctl->chat_show_text($search_msg, "orange");
		$ctl->chat_show_html("search_btn.tpl");
	    //$this->table($ctl,'search');
	}

	function pdf_table(Controller $ctl) {
		// Show PDFs filtered by "search"
	    $post = $ctl->POST();
	    //var_dump($post);
	    $ctl->assign("data", $post);
	    $ctl->chat_clear();
	    $ctl->chat_show_text("Please click the following button to show the PDF.", "orange");
	    $ctl->chat_show_html("pdf_btn.tpl");
	}
	function pdf_table_exe(Controller $ctl) {
		// Show PDFs filtered by "search"
	    $post = $ctl->POST();
	    $ctl->assign("data", $post);
	    $list = $this->ffm->filter([], [], false, 'AND', 'id', SORT_ASC);
	    //var_dump($this->ai_setting_parameters);
	    
	    $fields_count = count($this->ai_setting_parameters)+1;
	    $col_size= round(100/$fields_count);
	    $row3 = [];
	    $row2 = [];
	    $col_sizearr = [];
	    $col_alignarr = [];
	    $row2[]= 'ID';
	    $row3[]= 'id';
	    $col_sizearr[]=$col_size;
	    $col_alignarr[]='L';
	    $images=[];
		
		foreach ($this->ai_setting_parameters as $param) {
		    //var_dump($param['ai_fields_id']);
		    //var_dump($this->fields);
		    foreach ($this->fields as $field) {
			if($field['id'] == $param['ai_fields_id']){
			    $row2[]=$field["parameter_title"];
			    $row3[]=$field["parameter_name"];
			    $col_sizearr[]=$col_size;
			    $col_alignarr[]='L';
			    if($field['type']=='image'){
				//var_dump($param);
				$images[]=$field["parameter_name"];
			    }
			}
		    }
		    //$row2[]=$field["parameter_title"];
		}
	    $val_strarr = [];
	    $valarr=[];
	    $valarr2=[];
		//var_dump($row3);
		foreach ($list as $key => $value) {
		    foreach ($value as $key2 => $value2) {
			//var_dump($value2);
			if (in_array($key2, $images)){
			    $value2 = "\[image:".$value2."]";
			}
			if (in_array($key2, $row3)){
			    $valarr[$key2] = $value2;
			}
		    }
		    $valarr2[]=$valarr;
			
		}
		
		foreach ($valarr2 as $key3 => $value3) {
		    $val_strarr[]=implode("|",$value3);
		}
	    $col_size_str = implode(",",$col_sizearr);
	    $col_alignstr = implode(",",$col_alignarr);
	    $field_str = implode("|",$row2);
	    //var_dump($col_size_str);
	    //$ctl->assign("row", $row);
	    $ctl->assign("list", $list);
	    $ctl->assign("fields", $this->fields);
	    $ctl->assign("ai_setting", $this->ai_setting);
	    $ctl->assign("fields_count", $fields_count);
	    $ctl->assign("col_size", $col_size_str);
	    $ctl->assign("col_align", $col_alignstr);
	    $ctl->assign("field_str", $field_str);
	     $ctl->assign("val_arr", $val_strarr);
	    //var_dump($val_strarr);
	    //$ctl->res_pdf("images","pdf_table.tpl","sample.pdf");
	     $ctl->show_pdf("pdf_table.tpl","sample.pdf","Print",1200);
	}
	
	function pdf(Controller $ctl) {
		// Show PDFs filtered by "search"
	    $post = $ctl->POST();
	    //var_dump($post);
	    $ctl->assign("data", $post);
	    $ctl->chat_clear();
	    $ctl->chat_show_text("Please click the following button to show the PDF.", "orange");
	    $ctl->chat_show_html("pdf_btn.tpl");
	}
	function pdf_exe(Controller $ctl) {
		// Show PDFs filtered by "search"
	    $post = $ctl->POST();
	    $ctl->assign("data", $post);
	    $search_key = $ctl->get_session("_SEARCH_ITEMS");
	    $search_val = $ctl->get_session("_SEARCH_VALUES");
	    //var_dump($search_values);
	    $max = $ctl->POST("max");
	    $list = $this->ffm->filter($search_key, $search_val, false, "AND", "ID", SORT_DESC, $max);
	    //var_dump($list);
	    $setting_parameters = $this->ai_setting_parameters;
	    $arr = [];
		foreach ($setting_parameters as $key => $param) {
		    if($param['para_type']==0){
			foreach ($this->fields as $field) {
			    if($field['id'] == $param['ai_fields_id']){
				$setting_parameters[$key]["parameter_title"]=$field["parameter_title"];
				$setting_parameters[$key]["parameter_name"]=$field["parameter_name"];
				$setting_parameters[$key]["type"]=$field["type"];
			    }
			}
		    }
		    if($param['para_type']==2){
			//var_dump($param);
			$data_ai = $this->fmt_ai_setting_subtable->select('ai_settings_parameter_id', $param['id']);
			//var_dump($data_ai);
			$fields_count = count($data_ai)+1;
			$col_size= round(100/$fields_count);
			$row3 = [];
			$row2 = [];
			$col_sizearr = [];
			$col_alignarr = [];
			//$row2[]= 'ID';
			//$row3[]= 'id';
			//$col_sizearr[]=$col_size;
			//$col_alignarr[]='L';
			
			$sub_fields = $this->fmt_ai_fields->select("ai_db_id", $param['subtable_id'], true, "AND", "sort", SORT_DESC);
			//$arr['sub_fields'] = $sub_fields;
			
			foreach ($data_ai as $data_ai_sub) {
			    //var_dump($data_ai_sub);
			    foreach ($sub_fields as $subfield) {
				//var_dump($subfield);
				if($subfield['id'] == $data_ai_sub['ai_sub_field_id']){
				    $row2[]=$subfield["parameter_title"];
				    $row3[]=$subfield["parameter_name"];
				    $col_sizearr[]=$data_ai_sub['width'];
				    $col_alignarr[]=$data_ai_sub['align'];
				}
			    }
			}
			
			$tb = $this->fmt_ai_db->get($param['subtable_id']);
			$sub_tb = $ctl->db($tb['tb_name'], "common");
			$sub_values = $sub_tb->getall();
			//$arr['sub_values'] = $sub_values;
			
			$val_strarr = [];
			$valarr=[];
			$valarr2=[];
			    //var_dump($row3);
			    foreach ($sub_values as $key1 => $value1) {
				foreach ($value1 as $key2 => $value2) {
				    //var_dump($key2);
				    if (in_array($key2, $row3)){
					$valarr[$key2] = $value2;
				    }
				}
				$valarr2[]=$valarr;
			    }

			    foreach ($valarr2 as $key3 => $value3) {
				$val_strarr[]=implode("|",$value3);
			    }
			$col_size_str = implode(",",$col_sizearr);
			$col_alignstr = implode(",",$col_alignarr);
			$field_str = implode("|",$row2);
			$arr['col_size'] = $col_size_str;
			$arr['col_align'] = $col_alignstr;
			$arr['sub_fields'] = $field_str;
			$arr['sub_values'] = $val_strarr;
			
			$setting_parameters[$key]['sub_tb']=$arr;
		    }
		}
		//var_dump($setting_parameters);
		
	    $ctl->assign("list", $list);
	    $ctl->assign("fields", $this->fields);
	    $ctl->assign("ai_setting", $this->ai_setting);
	    $ctl->assign("setting_parameters", $setting_parameters);
	    //var_dump($val_strarr);
	    //$ctl->res_pdf("images","pdf.tpl","sample.pdf");
	    $ctl->show_pdf("pdf.tpl","sample.pdf","Print",1200);
	}
	
	function csv(Controller $ctl) {
	    $post = $ctl->POST();
	    $ctl->assign("data", $post);
	    $ctl->chat_clear();
	    $ctl->chat_show_text("Please click the following button to download the CSV", "orange");
	    $ctl->chat_show_html("csv.tpl");
	}

	function csv_exe(Controller $ctl) {
		// Download data filtered by "search"
	    
	    $row = [];
	    $row['id'] = 'ID';
	    foreach ($this->fields as $field) {
			$row[$field["parameter_name"]] = $field["parameter_title"];
	    }
	    $csv_header_data[] = $row;
	    
	    $post = $ctl->POST();

	    $items = $this->ffm->filter([], [], false, 'AND', 'id', SORT_ASC);
	    
	    $formated_data = [];
	    foreach ($items as $key => $value) {
		$formated_data[$key] = $value;
		
	    }

	    $dataset =  array_merge($csv_header_data, $formated_data);

	    foreach ($dataset as $row){
		$ctl->res_csv($row, "sjis-win");
	    }
	}

	function mail(Controller $ctl) {
	    $post = $ctl->POST();
	    $ctl->assign("data", $post);
	    $ctl->chat_show_html("mail_btn.tpl");
	}
	function mail_exe(Controller $ctl) {
		// Show PDFs filtered by "search"
	    $post = $ctl->POST();
	    $ctl->assign("data", $post);
	    $search_key = $ctl->get_session("_SEARCH_ITEMS");
	    $search_val = $ctl->get_session("_SEARCH_VALUES");
	    //var_dump($search_values);
	    $max = $ctl->POST("max");
	    $list = $this->ffm->filter($search_key, $search_val, false, "AND", "ID", SORT_DESC, $max);
	    foreach ($list as $key => $value) {
		    $checkbox_val=[];
		    foreach ($this->fields as $keyf => $valuef) {
			if(is_array($value[$valuef['parameter_name']])){
			    foreach ($value[$valuef['parameter_name']] as $item){
				$checkbox_val[]=$valuef["options"][$item];
			    }
			    $list[$key][$valuef['parameter_name']]= implode(', ', $checkbox_val);
			}
		    }
		}
	    //var_dump($list);
	    $setting_parameters = $this->ai_setting_parameters;
	    $arr = [];
		foreach ($setting_parameters as $key => $param) {
		    if($param['para_type']==0){
			foreach ($this->fields as $field) {
			    if($field['id'] == $param['ai_fields_id']){
				$setting_parameters[$key]["parameter_title"]=$field["parameter_title"];
				$setting_parameters[$key]["parameter_name"]=$field["parameter_name"];
				$setting_parameters[$key]["type"]=$field["type"];
			    }
			}
		    }
		    if($param['para_type']==2){
			//var_dump($param);
			$data_ai = $this->fmt_ai_setting_subtable->select('ai_settings_parameter_id', $param['id']);
			//var_dump($data_ai);
			$fields_count = count($data_ai)+1;
			$col_size= round(100/$fields_count);
			$row3 = [];
			$row2 = [];
			$col_sizearr = [];
			$col_alignarr = [];
			//$row2[]= 'ID';
			//$row3[]= 'id';
			//$col_sizearr[]=$col_size;
			//$col_alignarr[]='L';
			
			$sub_fields = $this->fmt_ai_fields->select("ai_db_id", $param['subtable_id'], true, "AND", "sort", SORT_DESC);
			//$arr['sub_fields'] = $sub_fields;
			
			foreach ($data_ai as $data_ai_sub) {
			    //var_dump($data_ai_sub);
			    foreach ($sub_fields as $subfield) {
				//var_dump($subfield);
				if($subfield['id'] == $data_ai_sub['ai_sub_field_id']){
				    $row2[]=$subfield["parameter_title"];
				    $row3[]=$subfield["parameter_name"];
				    $col_sizearr[]=$data_ai_sub['width'];
				    $col_alignarr[]=$data_ai_sub['align'];
				}
			    }
			}
			
			$tb = $this->fmt_ai_db->get($param['subtable_id']);
			$sub_tb = $ctl->db($tb['tb_name'], "common");
			$sub_values = $sub_tb->getall();
			//$arr['sub_values'] = $sub_values;
			
			$val_strarr = [];
			$valarr=[];
			$valarr2=[];
			    //var_dump($row3);
			    foreach ($sub_values as $key1 => $value1) {
				foreach ($value1 as $key2 => $value2) {
				    //var_dump($key2);
				    if (in_array($key2, $row3)){
					$valarr[$key2] = $value2;
				    }
				}
				$valarr2[]=$valarr;
			    }

			    foreach ($valarr2 as $key3 => $value3) {
				$val_strarr[]=implode("|",$value3);
			    }
			$col_size_str = implode(",",$col_sizearr);
			$col_alignstr = implode(",",$col_alignarr);
			$field_str = implode("|",$row2);
			$arr['col_size'] = $col_size_str;
			$arr['col_align'] = $col_alignstr;
			$arr['sub_fields'] = $field_str;
			$arr['sub_values'] = $val_strarr;
			
			$setting_parameters[$key]['sub_tb']=$arr;
		    }
		}
		//var_dump($setting_parameters);
		
	    $ctl->assign("list", $list);
	    $ctl->assign("fields", $this->fields);
	    $ctl->assign("ai_setting", $this->ai_setting);
	    $ctl->assign("setting_parameters", $setting_parameters);
	    //var_dump($setting_parameters);
	    //$ctl->chat_show_html("mail.tpl");
	    $ctl->show_multi_dialog("mail", "mail.tpl", "Mail", 600, true, true);
	}
	
	function download_file(Controller $ctl) {
	    $filename= $ctl->POST("filename");
	    //var_dump($filename);
	    $ctl->res_saved_file($filename);
	}
	function view_image(Controller $ctl){
            $image_file = $ctl->GET("file");
            $ctl->res_saved_image($image_file);
        }
}
