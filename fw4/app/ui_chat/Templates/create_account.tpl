<div  style="width:100%;overflow: hidden;display: block;min-height:400px;">
<form id="input_form">

	<input type="hidden" name="vc" value={$vc}>
	<input type="hidden" name="ai_setting_id" value={$ai_setting_id}>
	<input type="hidden" name="public" value="{$public}">
	
	<h3 class="lang">Create your account</h3>
	
	<img src="app.php?class={$class}&function=img&file=step00{$step}.png&public={$public}" style="height:20%;"/>
	
	{if $step==1}
		<h5 class="lang">Please enter your email.</h5>
		<input type="text" name="email" value="{$data.email}">
	{else if $step==2}
		<h5 class="lang">Please enter the verification code mentioned in the email you received.</h5>
		<input type="text" name="verification_code" value="{$data.verification_code}">
	{else if $step==3}
		<h5 class="lang">Please enter your name.</h5>
		<input type="text" name="name" value="{$data.name}">
		
		{if $ai_setting.change_after_type == 0}
			<h5 class="lang">Please choose a user type</h5>
			{html_options name="type" options=$user_type_opt}
		{/if}
		
	{else if $step==4}
		<h5 class="lang">Please enter your desired password to create your account.</h5>
		<input type="text" name="password" value="{$data.password}">
	{/if}
	
	<p class="error lang">{$error}</p>


</form>

<button class="ajax-link lang" data-class="{$class}" data-function="creat_acc" data-form="input_form" data-step="{$step}">Submit</button>
</div>