<form id="ai_dbs_ai_db_edit_form_{$data.id}">

	<input type="hidden" name="id" value="{$data.id}">

	<div>
		<h4>{$data.tb_name}</h4>
		<input type="hidden" name="tb_name" value="{$data.tb_name}">
		<p class="error lang">{$errors['tb_name']}</p>
	</div>

	<div>
		<p class="lang">What information do you keep in this table?(Example: "Customer Information", "Customer History")</p>
		<input type="text" name="description" value="{$data.description}">
	</div>

	<div>
		<p class="lang">Parent Table: (The data exists will broken if you change the parent table.)</p>
		{html_options name="parent_tb_id" options=$parents_opt selected=$data["parent_tb_id"]}
	</div>


</form>

<div style="clear:both;"></div>
<div id="parameters_area">
	{include file="_fields.tpl"}
</div>