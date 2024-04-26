<form id="multi_pay_form">
	
	<input type="hidden" name="public" value="{$public}">
	
	<div class="form-wrap form-wrap-validation has-error">
		<h6 class="lang">Select payment</h6>
		{html_options name='payment_id' options=$items selected=$data.payment_id}

		<h6 class="lang">Name</h6>
		<input type="text" name="name" value="{$data.name}">
		<p class="error lang">{$error_name}</p>
	
		<h6 class="lang">Email</h6>
		<input type="text" name="email" value="{$data.email}">
		<p class="error lang">{$error_email}</p>
	
		<h6 class="lang">Address</h6>
		<input type="text" name="address" value="{$data.address}">
		<p class="error lang">{$error_address}</p>
	</div>	

	<button class="ajax-link lang" data-form="multi_pay_form" data-class="{$class}" data-function="payment" style="float:right;margin-top:18px;">Go Next</button>

</form>
