<header class="lang_check_area" data-classname="base">

    {if $smarty.session.{$windowcode}.testserver}
        <div class="testserver">
            <div class="testserver_title lang">DEV MODE</div>
            <div class="ajax-link release_button lang" data-class="release" data-function="page">Release</div>
        </div>
    {/if}

    <div class="logoarea">

        <a href="../fw4/app.php?class=base" class="topmenu_left" title="Home">
            <div style="height:47px;display: inline-block;margin-left:10px;margin-top: 7px;">
                <p style="font-size:22px;line-height: 10px;	color:#FFF;">{if $setting.system_name == null}FOCUS Business Platform{else}{$setting.system_name}{/if}</p>
                <p style="	color:#FFF;font-size:12px;line-height:28px;"> {if $setting.system_tag_line == null}Focus on more important things.{else}{$setting.system_tag_line}{/if}</p>
            </div>
        </a>

        <div>
            <button id="topbar_infomation_btn_mobile"><img src="images/menu-vdot.png" /></button>
        </div>

        <div class="topbar_infomation_area">
            <a href="app.php?class=login&function=logout" title="Logout" class="lang">Logout</a>

            {if $smarty.session.{$windowcode}.app_admin}
                <a class="ajax-link lang" data-class="user" data-function="page" style="cursor:pointer">User Management</a>
            {else}
                <a class="ajax-link topmenu_right lang" data-class="user" data-function="passchange"
				   style="cursor:pointer;">Change Password</a>
            {/if}

			{html_options id="lang_selector" name="lang_selector" options=$arr_lang}

            {if $smarty.session.{$windowcode}.app_admin}
                {if $smarty.session.{$windowcode}.testserver}
					<!--                    <a class="ajax-link lang" data-class="bugmanage" data-function="page" style="cursor: pointer;">Changing List</a>-->
					<a href="/app.php?class=bugmanage&function=page&sec={$bug_sec}" target="_blank">Changing List</a>

                {/if}
                {if $smarty.session.{$windowcode}.testserver}
                    <a class="ajax-link lang" data-class="lang" data-function="edit" style="cursor: pointer;">Edit Translation</a>
                {/if}

                {if $smarty.session.{$windowcode}.testserver}
                    <a class="ajax-link lang" data-class="dbadmin" data-function="index" style="cursor: pointer;">DB Admin</a>
                {/if}

                {if $smarty.session.{$windowcode}.testserver}
                    <a class="ajax-link lang" data-class="email_format" data-function="page" style="cursor: pointer;">Email Templates</a>
                {/if}
				<!--
				{if $smarty.session.{$windowcode}.testserver}
					<a class="ajax-link lang" data-class="menu" data-function="page" style="cursor: pointer;">Change Menu</a>
				{/if}
				-->
                <a class="ajax-link lang" data-class="setting" data-function="page" style="cursor: pointer;">Setting</a>
                {if $smarty.session.{$windowcode}.testserver}
                    <div class="autopilot-sec" style="cursor:pointer; display: flex;padding: 8px;">
                        <button class="ajax-link button autopilot-btn" id="autopilot-play" data-class="base" data-function="playback">
                            <span class="arrow"></span>
                        </button>
                        <span class="circle_border" id="record-button">
                            <center>
                                <span class="record-button"></span>
                            </center>
                        </span>
                        <div class="playpause" id="pause-autopilot" style="display:none;padding:0;margin: 0;">
                            <label></label>
                        </div>
                    </div>
				{/if}

				{if $smarty.session.{$windowcode}.testserver}
					<a class="ajax-link lang" data-class="constant_array" data-function="page" style="cursor:pointer">Constant Array</a>
					<a class="ajax-link lang" data-class="ai_settings" data-function="page" style="cursor:pointer">AI Setting</a>
					<a class="ajax-link lang" data-class="ai_db" data-function="page" style="cursor:pointer">Database</a>
				{/if}


            {/if}
        </div>



        <div id="download_view">
            <div id="download_bar">
                <div id="download_message"></div>
                <div id="download_progress"></div>
            </div>
        </div>

        {* mobile menu *}
        <ul id="mobile-nav-menu">


        </ul>

    </div>


</header>