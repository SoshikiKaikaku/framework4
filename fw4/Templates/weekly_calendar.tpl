
<div class="prev-next" style="margin-bottom:10px;">
		<button class="ajax-link lang" data-class="{$class}" data-function="calendar" data-prev="1" data-next="{$data.prev}" data-week="{$data.week}" style="float:left;">
				<span class="lang">Previous Week</span>
		</button>

		<button class="ajax-link lang" data-class="{$class}" data-function="calendar" data-next="{$data.next}" data-week="{$data.week}" style="float:right;">
				<span class="lang">Next Week</span>
		</button>
</div>


<table id="slots" class="table is-bordered is-fullwidth">
	<thead>
		<tr>
			<th></th>
				{foreach $weekOfdays as $day}
				<th class="time" id="1">{$day}</th>
				{/foreach}

		</tr>
	</thead>
	<tbody>
		{foreach $timeslots as $key=>$slot}
			<tr>
				<td>
					<strong style="word-break: keep-all;">{$slot}</strong>
				</td>
				{foreach $schedule[$key] as $key=>$day}
					{if strtotime($data.today) > strtotime($day[0]) || date('D', strtotime($day[0])) === 'Mon' || date('D', strtotime($day[0])) === 'Wed'}
						<td class="is-blocked"></td>
					{else}
						{if $day[2] =='booked'}
							<td class="is-booked"></td>
						{else}
							<td class="ajax-link lang vacant" data-form="select_date_form" data-book_day="{$day[0]}" data-start_time="{$slot}" data-did="{$get.did}">{$day[2]}</td>
						{/if}
					{/if}
				{/foreach}

			</tr>
		{/foreach}

	</tbody>
</table>

