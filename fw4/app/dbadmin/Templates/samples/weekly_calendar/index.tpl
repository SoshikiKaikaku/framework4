<div style="min-height: 500px;margin-top:20px;">

	{* header *}
	<div>
		<div>
			<input type="hidden" id="class_name" value="[CURLY_OPEN]$class}">
			<input type="hidden" id="start_date" value="[CURLY_OPEN]$start_date}">
			<input type="hidden" id="end_date" value="[CURLY_OPEN]$end_date}">

			<div class="date-range" style="display: flex; margin-left:0px;">
				<span class="" style="float:left;">
					<button data-class="[CURLY_OPEN]$class}" data-function="page" data-date_range="lastweek_[CURLY_OPEN]$start_date}" class="ajax-link lang" id="lastweek" style="margin-top: 0px; margin-left: 0px; width: max-content; padding:10px 15px; background-color: white;"><span class="ui-icon ui-icon-triangle-1-w" style="transform: scale(2);"></span></button>
				</span>

				<span class="" style="padding-right: 10px;float:left;">
					<button data-class="[CURLY_OPEN]$class}" data-function="page" data-date_range="nextweek_[CURLY_OPEN]$end_date}" class="ajax-link lang" id="nextweek" style="margin-top: 0px; width: max-content; padding:10px 15px; background-color: white;"><span class="ui-icon ui-icon-triangle-1-e" style="transform: scale(2);"></span></button>
				</span>

				<div style="margin-top: 2px; display:flex; justify-content:space-between; width:100%;">
					<div>
						<span class="lang">From: </span>
						<input type="text" id="search_schedule_date" name="search_schedule_date" value="[CURLY_OPEN]$start_date}"
							   class="datepicker"
							   style="width: auto; margin: 0 10px; border-width: 0px; padding: 0px 10px; height: 30px;">
						<span class="lang" style="margin-right: 10px;">To:</span> [CURLY_OPEN]$end_date}
					</div>
				</div>
			</div>

		</div>
	</div>

	[CURLY_OPEN]foreach $date_range as $day}

	<div class="day_box">
		<span class="day">[CURLY_OPEN]$day['day_number']}</span><span class="day lang"
																	  style="margin-right:4px;">[CURLY_OPEN]$day['day_superscript']}</span><span class="day lang">[CURLY_OPEN]$day['day_name']}</span>

		[CURLY_OPEN]for $time=6 to 21}

		[CURLY_OPEN]if $day_bg[$day['date']][$time] }
		[CURLY_OPEN]assign "dbg" "dbg_on"}
		[CURLY_OPEN]else}
		[CURLY_OPEN]assign "dbg" "dbg_off"}
		[CURLY_OPEN]/if}

		{* cell *}
		<div data-date="[CURLY_OPEN]$day['date']}" data-user_id="[CURLY_OPEN]$task_step['user_id']}" data-start_time="[CURLY_OPEN]$time}" class="[CURLY_OPEN]$dbg} ui-widget-header droppable-weekly-calendar">

			[CURLY_OPEN]assign var="task_exist" value=false}
			[CURLY_OPEN]if !empty($task_step_list[$day['date']][$time])}
			[CURLY_OPEN]foreach $task_step_list[$day['date']][$time] as $task_step}

			[CURLY_OPEN]$task_exist = true}

			<div id="draggable_[CURLY_OPEN]$task_step.id}" data-id="[CURLY_OPEN]$task_step.id}" class="ui-widget-content draggable-weekly-calendar" [CURLY_OPEN]* *}style='border:1px [CURLY_OPEN]$task_step.background_color} solid;cursor: pointer; background:[CURLY_OPEN]($task_step["status"]==2)?"gray":(($task_step.is_second_person)?"#E4E7F9":"")}'>
				z
				{* add *}
				<button class="ajax-link listbutton" data-class="[CURLY_OPEN]$class}" data-function="add_task_step"
						data-scheduled_date="[CURLY_OPEN]$day['date']}" data-start_time="[CURLY_OPEN]$time}:00" data-end_time="[CURLY_OPEN]$time+1}:00"
						style="float:right;color:black;margin:-3px 0 0 0; " data-user_id="[CURLY_OPEN]$task_step['user_id']}" data-start_date="[CURLY_OPEN]$start_date}" data-end_date="[CURLY_OPEN]$end_date}">
					<span class="ui-icon ui-icon-plus" style="margin-left: 0px;"></span>
				</button>

				{* delete *}
				{if isset($delete_field)}
					<button class="ajax-link listbutton" data-class="[CURLY_OPEN]$class}" data-function="delete_task_step"
							data-id="[CURLY_OPEN]$task_step.id}" data-page="scheduled_task" data-start_date="[CURLY_OPEN]$start_date}" data-end_date="[CURLY_OPEN]$end_date}" data-department="[CURLY_OPEN]$department}" style="float:right;color:black;margin-top:-3px; " data-filter_status="[CURLY_OPEN]$task_step.status}"><span class="ui-icon ui-icon-trash"></span>
					</button>
				{/if}

				{* edit *}
				<button class="ajax-link listbutton" data-class="[CURLY_OPEN]$class}" data-function="edit_task_step"
						data-task_id="[CURLY_OPEN]$task_step.task_id}" data-id="[CURLY_OPEN]$task_step.id}" data-page="scheduled_task" data-start_date="[CURLY_OPEN]$start_date}" data-end_date="[CURLY_OPEN]$end_date}" data-department="[CURLY_OPEN]$department}" style="float:right;color:black;margin-top:-3px; " data-filter_status="[CURLY_OPEN]$task_step.status}"><span class="ui-icon ui-icon-pencil"></span>
				</button>

				<p class="time">[CURLY_OPEN]$task_step.start_time}-[CURLY_OPEN]$task_step.end_time}</p>

				{* elements *}
				{foreach $table_settings as $element}
					{if !$element['list_flg']}
						{continue}
					{/if}

					<p class="task_name">
						<b style="font-size: larger;">{ucwords(str_replace("_", " ", $element['field_name']))}:</b>

						{if $element['field_type'] == 'file'}
						<td>
							[CURLY_OPEN]assign var="time" value=(explode("-", $task_step.{$element['field_name']}))}
							[CURLY_OPEN]assign var="filename" value=([CURLY_OPEN]trim(str_replace($time[0], "", str_replace("-", " ", $task_step.{$element['field_name']})))})}
							<a class="download-link" data-class="[CURLY_OPEN]$class}" data-function="download" data-file="[CURLY_OPEN]$task_step.{$element['field_name']}}" style="cursor: pointer;" data-filename="[CURLY_OPEN]$filename}">[CURLY_OPEN]$filename}</a>
						</td>
					{elseif ($element['field_type'] == 'select' || $element['field_type'] == 'radio')}
						<td>[CURLY_OPEN]${$element['field_name']}_options[$task_step.{$element['field_name']}]}</td>
					{elseif $element['field_type'] == 'checkbox'}
						[CURLY_OPEN]assign var="checkbox_arr" value=explode(", ", $task_step.{$element['field_name']})}
						<td>
							<ul>
								[CURLY_OPEN]foreach $checkbox_arr as $check_item}
								<li style="list-style-type:disc">[CURLY_OPEN]${$element['field_name']}_options[$check_item]}</li>
								[CURLY_OPEN]/foreach}
							</ul>
						</td>
					{else}
						<td>[CURLY_OPEN]$task_step.{$element['field_name']}}</td>
					{/if}
					</p>
				{/foreach}

			</div>
			[CURLY_OPEN]/foreach}

			[CURLY_OPEN]/if}

			<div class="[CURLY_OPEN](($task_exist)?'':'')}">
				<span class="number_of_time">[CURLY_OPEN]$time}:00</span>
				<button class="ajax-link listbutton" data-class="[CURLY_OPEN]$class}" data-function="add_task_step"
						data-scheduled_date="[CURLY_OPEN]$day['date']}" data-start_time="[CURLY_OPEN]$time}:00" data-end_time="[CURLY_OPEN]$time+1}:00" style="position:absolute; right:0; color:black; margin-top:-3px;" data-user_id="[CURLY_OPEN]$task_step['user_id']}" data-start_date="[CURLY_OPEN]$start_date}" data-end_date="[CURLY_OPEN]$end_date}">
					<span class="ui-icon ui-icon-plus" style="margin-top: 7px;"></span>
				</button>
			</div>

		</div>

		[CURLY_OPEN]/for}
	</div>

	[CURLY_OPEN]/foreach}
</div>
