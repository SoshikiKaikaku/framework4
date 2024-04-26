<!DOCTYPE html>
<html>
	{include file="{$base_template_dir}/header.tpl"}
	<body>
		{include file="topbar.tpl"}
		<article>

			<div id="menu_area" class="lang_check_area" data-classname="base">
				{* <button id="left-sidebar-hide-btn"><img src="images/left-arrow.png" /></button> *}

				<div id="left_menu">

					<div id="accordion_menu1">
						{$menu_html nofilter}
					</div>
				</div>

			</div>

			<div class="content">
				<div >
					{* <button id="left-sidebar-show-btn"><img src="images/menu-hamburger.png" /></button> *}
					<button id="left-sidebar-show-btn" class="ajax-link" data-class="base" data-function="show_left_sidemenu"><img src="images/menu-hamburger.png" /></button>
				</div>

				<div id="work_area">

					<img src="app.php?class={$class}&function=img&file=logo_bg.png&windowcode={$windowcode}" class="work_area_bglogo">

				</div>
			</div>

		</article>

		<footer>
			<div id="appcode" style="display: none;">{$appcode}</div>
			{if $testserver}
				<button id="show_debug">Debug</button>
			{/if}
			<div class="copyright">FOCUS Business Platform</div>
		</footer>


		{include file="{$base_template_dir}/footer.tpl"}


	</body>

</html>

