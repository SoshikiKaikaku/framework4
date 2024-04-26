<?php

/**
 *
 * @author nakama
 */
interface FFM {
	

	//データを全消去
	function allclear();
	
	//挿入、挿入後に $dataset["id"] にidが入ります
	function insert(&$dataset);
	
	//削除
	function delete($id);
	
	//更新
	function update($dataset);
	
	//次のデータを返す。データがない場合はnullを返す
	function next();
	
	//seek(1)で最初のデータに移動
	function seek($start_number);
	
	//最後のデータに移動
	function seek_end();
	
	//前のデータを返す。データがない場合はnullを返す
	function before();
	
		//全てのデータを配列で返す
	function getall($sort_item,$sort_order);
	
	//IDと一致するデータを返す。ない場合はnullを返す
	function get($id);
	
	function get_path_dat();
	
	function close();

	function select($itemname,$value,$exact_match=true,$and_or="AND",$sortitem=null,$sort_order=SORT_DESC,$max=null,&$is_last=null);

	function filter($itemname,$value,$exact_match=false,$and_or="AND",$sortitem=null,$sort_order=SORT_DESC,$max=null,&$is_last=null);
	
	function get_prohibition_items();
	
	function get_unique_key();
}
