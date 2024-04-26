{* header *}
<div style="margin-top:10px;">
    <div>
        <input type="hidden" id="class_name" value="[CURLY_OPEN]$class}">

        <div class="date-range" style="display: flex; margin-left:0px;">
            <span class="" style="float:left;">
                <button data-class="[CURLY_OPEN]$class}" data-function="page" data-current_start_date="[CURLY_OPEN]$current_start_date}" data-month="previous" class="ajax-link lang" id="lastweek"><span class="ui-icon ui-icon-triangle-1-w" style="transform: scale(2);"></span></button>
            </span>

            <span class="" style="padding-right: 10px;float:left;">
                <button data-class="[CURLY_OPEN]$class}" data-function="page" data-current_start_date="[CURLY_OPEN]$current_start_date}" data-month="next"  class="ajax-link lang" id="nextweek"><span class="ui-icon ui-icon-triangle-1-e" style="transform: scale(2);"></span></button>
            </span>

            <div style="margin-top: 2px; display:flex; justify-content:space-between; width:100%;">
                <div>
                    <span class="lang">Month: </span>
                    <input type="text" id="current_start_datepicker" name="current_start_datepicker" value="[CURLY_OPEN]$start_month_year}" class="year_month_picker">
                </div>
            </div>
        </div>

    </div>
</div>

<br>
<center>

    <div class="calendar">
        <table class="calendar-table">
            <tr class="calendar-header-row" style="background: #064683; color: white;">
                <th style="width:calc(100%/7);">日</th>
                <th style="width:calc(100%/7);">月</th>
                <th style="width:calc(100%/7);">火</th>
                <th style="width:calc(100%/7);">水</th>
                <th style="width:calc(100%/7);">木</th>
                <th style="width:calc(100%/7);">金</th>
                <th style="width:calc(100%/7);">土</th>
            </tr>
            [CURLY_OPEN]assign var="count_days" value=0}

            [CURLY_OPEN]foreach $days_of_date_range as $day}

			[CURLY_OPEN]if $day['day'] == "Sunday"}
			<tr class="[CURLY_OPEN]((count($days_of_date_range)-7<= $count_days)?'calendar-row-bottom':'')}">
                [CURLY_OPEN]/if}

                <td class="[CURLY_OPEN](($day['day']=="Saturday")?'calendar-cell-last':'calendar-cell')}">

                    <div class="day">
                        <p class="">[CURLY_OPEN]$day['day_number']}</p>

                        {* add *}
                        <button class="ajax-link listbutton" data-class="[CURLY_OPEN]$class}" data-function="add" data-scheduled_date="[CURLY_OPEN]$day.date}" data-current_start_date="[CURLY_OPEN]$start_month_year}"><span class="ui-icon ui-icon-plus"></span></button>
                    </div>

                    <div class="droppable-monthly-calendar" style="height: 75%;" data-date="[CURLY_OPEN]$day.date}">
                        [CURLY_OPEN]foreach $tasks[$day['month_and_day']] as $task}
						<div class="draggable-monthly-calendar" data-id="[CURLY_OPEN]$task.id}" style="background: #4BA3FF;border:1px #4BA3FF solid;margin-top: 2px;border-radius:10px;padding-left: 8px;padding-top: 3px;color:#FFF; position:relative;">

							<p>[CURLY_OPEN]$task['start_time']} - [CURLY_OPEN]$task['end_time']}</p>

							{* edit *}
							<button class="ajax-link listbutton" data-class="[CURLY_OPEN]$class}" data-function="edit"  data-id="[CURLY_OPEN]$task['id']}" data-current_start_date="[CURLY_OPEN]$start_month_year}" style="position:absolute; right: 20px; top: 0;"><span class="ui-icon ui-icon-pencil"></span></button>

							{* delete *}
							<button class="ajax-link listbutton" data-class="[CURLY_OPEN]$class}" data-function="delete"  data-id="[CURLY_OPEN]$task['id']}" data-current_start_date="[CURLY_OPEN]$start_month_year}" style="position:absolute; right: 0; top: 0;"><span class="ui-icon ui-icon-trash"></span></button>

							{* elements *}
							{foreach $table_settings as $element}
								{if !$element['list_flg']}
									{continue}
								{/if}

								<p class="task_name">
									<b style="font-size: larger;">{ucwords(str_replace("_", " ", $element['field_name']))}:</b>

									{if $element['field_type'] == 'file'}

										[CURLY_OPEN]assign var="time" value=(explode("-", $task.{$element['field_name']}))}
										[CURLY_OPEN]assign var="filename" value=([CURLY_OPEN]trim(str_replace($time[0], "", str_replace("-", " ", $task.{$element['field_name']})))})}
										<a class="download-link" data-class="[CURLY_OPEN]$class}" data-function="download" data-file="[CURLY_OPEN]$task.{$element['field_name']}}" style="cursor: pointer;" data-filename="[CURLY_OPEN]$filename}">[CURLY_OPEN]$filename}</a>

									{elseif ($element['field_type'] == 'select' || $element['field_type'] == 'radio')}
										[CURLY_OPEN]${$element['field_name']}_options[$task.{$element['field_name']}]}
									{elseif $element['field_type'] == 'checkbox'}
										[CURLY_OPEN]assign var="checkbox_arr" value=explode(", ", $task.{$element['field_name']})}
									<ul>
										[CURLY_OPEN]foreach $checkbox_arr as $check_item}
										<li style="list-style-type:disc">[CURLY_OPEN]${$element['field_name']}_options[$check_item]}</li>
										[CURLY_OPEN]/foreach}
									</ul>
								{else}
									[CURLY_OPEN]$task.{$element['field_name']}}
								{/if}
								</p>
							{/foreach}
						</div>
                        [CURLY_OPEN]/foreach}
                    </div>
                </td>

                [CURLY_OPEN]if $day['day'] == "Saturday"}
			</tr>
			[CURLY_OPEN]/if}

			[CURLY_OPEN]assign var="count_days" value=$count_days+1}
            [CURLY_OPEN]/foreach}
        </table>
    </div>
</center>