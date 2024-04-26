

{** add button **}
{if $count_add_items > 0}
    <div class="add_button_area">
		<button class="ajax-link" data-class="{$class}" data-function="add" data-table_name="{$table_name}" data-parent_id="{$parent_id}">Add</button>
    </div>
{/if}

<button class="ajax-link ui-button ui-corner-all change_week_button" data-d="{$time_previous}" data-class="{$class}" data-function="set_datetime" data-table_name="{$table_name}"><span class="material-symbols-outlined">chevron_left</span></button>
<button class="ajax-link ui-button ui-corner-all change_week_button" data-d="{$time_today}" data-class="{$class}" data-function="set_datetime" data-table_name="{$table_name}"><span class="material-symbols-outlined">today</span></button>
<button class="ajax-link ui-button ui-corner-all change_week_button" data-d="{$time_next}" data-class="{$class}" data-function="set_datetime" data-table_name="{$table_name}"><span class="material-symbols-outlined">chevron_right</span></button>

<div class="schedule_datepicker_area">
	<form id="schedule_datepicer_form_{$timestamp}">
		<input type="text" name="d" class="datepicker" id="schedule_datepicker" value="{$schedule_datepicker_d}">
		<button class="ajax-link" data-form="schedule_datepicer_form_{$timestamp}" data-class="{$class}" data-function="set_datetime" data-table_name="{$table_name}">Jump</button>
	</form>
</div>

{if $MYSESSION.move_status == 1}
	<div class="schedule_move_area">
		<p class="schedule_move_message">Click a icon where you move to.</p>
		<button class="ajax-link" data-class="{$class}" data-function="move_cancel">Cancel moving</button>
	</div>
{/if}

<div id="schedule_area">

    <div id="schedule_title">

		<div class="schedule">

			<div class="schedule_box date"></div>

			{foreach $schedule_arr as $s}
				<div class="schedule_box days_{$s.w}" style="width:calc((100% - 70px) / 7);">
					<p class="month_date"><span class="year">{$s.year}</span><span class="month">{$s.month}</span><span class="d">{$s.date}</span><span class="day">（{$s.day}）</span></p>

				</div>
			{/foreach}
		</div>

    </div>


    <div class="schedule">

		{for $t=$start_hour to $end_hour}

			<div class="schedule_box date">{$t}:00</div>


			{foreach $schedule_arr as $s}
				{if $s["occupied"][$t]}
					{assign bg "occupied"}
				{else}
					{assign bg ""}
				{/if}
				<div class="schedule_box {$bg}" style="width:calc((100% - 70px) / 7);" data-class="{$class}" data-table_name="{$table_name}" data-date="{$s.datetime}" data-time="{$t}">				

					{if $MYSESSION.move_status == 1}
						<span class="ajax-link material-symbols-outlined" data-class="{$class}"  data-table_name="{$table_name}" data-function="move_paste" data-date="{$s.datetime}" data-time="{$t}" style="color:#ff002e">move_down</span>
					{/if}

					{foreach $s["itemlist"][$t] as $row}
						<div class="task" id="{$row.id}">
							<p class="start_end">{$row.start_time} - {$row.end_time}</p>
							{assign values $row}
							{foreach $list_items as $name}
								<p>
									{include file="__item_viewer_list.tpl"}

									{if $count_edit_items > 0 }
										<span class="material-symbols-outlined ajax-link" data-class="{$class}" data-function="edit" data-table_name="{$table_name}" data-id_encrypted="{$row.id_encrypted}" data-parent_id="{$parent_id}">edit_square</span>
									{/if}
									{if $count_delete_items > 0 }
										<span class="material-symbols-outlined ajax-link" data-class="{$class}" data-function="delete" data-table_name="{$table_name}" data-id_encrypted="{$row.id_encrypted}" style="padding-top:2px;white-space: nowrap;" data-parent_id="{$parent_id}">delete</span>
									{/if}
									{if $MYSESSION.move_status == 0}
										<span class="material-symbols-outlined ajax-link" data-class="{$class}" data-function="move_start" data-table_name="{$table_name}" data-id_encrypted="{$row.id_encrypted}" style="padding-top:2px;white-space: nowrap;" data-parent_id="{$parent_id}">move_up</span>
									{/if}
								</p>
							{/foreach}
						</div>
					{/foreach}
				</div>
			{/foreach}

		{/for}


    </div>
</div>

