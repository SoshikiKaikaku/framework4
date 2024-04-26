
<form id="vimeo_upload_form">
	<input type="hidden" name="callback_parameter_array" value="{$callback_parameter_array}">
	<div class="vimeo_upload_area">
		<p class="lang">Video Title</p>
		<input type="text" name="vimeo_title" id="vimeo_title">
		<p class="lang">Video Description</p>
		<textarea name="vimeo_description" id="vimeo_description"></textarea>

		<h5 class="lang">Select Upload File</h5>
		<input id="vimeo_file" type="file" />
		<input type="hidden" id="vimeo_id" name="uploaded_vimeo_id" readonly style="margin-top:10px;">
		<p class="error" id="vimeo_error"></p>
	</div>
</form>

<button id="vimeo_send_button" class="ajax-link lang" data-class="{$callback_class_name}" data-function="{$callback_function_name}" data-form="vimeo_upload_form">Regist Video</button>

<div style="margin-bottom:80px;"></div>


