function renderTimelineHoursAdminSelector(parent, window_length){
	var selector = document.createElement("table");
	document.getElementById(parent).appendChild(selector);
		
	var selector_labels_row = document.createElement("tr");
	selector.appendChild(selector_labels_row);
	var selector_inputs_row = document.createElement("tr");
	selector.appendChild(selector_inputs_row);
	
	for(i = 0 ; i < 24 ; i++){
		selector_label_cell = document.createElement("td");
		selector_labels_row.appendChild(selector_label_cell);
		selector_label = document.createElement("label");
		selector_label.setAttribute("for", "hour_"+i);
		selector_label.textContent = i + ":00 - " + (i + window_length) + ":00";
		selector_label_cell.appendChild(selector_label);
		
		selector_input_cell = document.createElement("td");
		selector_inputs_row.appendChild(selector_input_cell);
		selector_input = document.createElement("input");
		selector_input.setAttribute("type", "checkbox");
		selector_input.setAttribute("name", "date");
		selector_input.setAttribute("value", i);
		selector_input.setAttribute("id", "hour_"+i);
		selector_input_cell.appendChild(selector_input);
	}
}

function renderTimelineSelector(parent, datetimes, window_length){
	var timeline_div = document.createElement("div");
	document.getElementById(parent).appendChild(timeline_div);
	
	datetimes.forEach(function(year, index, array){
		//console.log(year);
		year_div = document.createElement("div");
		year_div.className = "timeline-year";
		year_div.textContent = year.year;
		timeline_div.appendChild(year_div);
		
		year.months.forEach(function(month){
			//console.log(month);
			month_div = document.createElement("div");
			month_div.className = "timeline-month";
			month_div.textContent = month.month;
			year_div.appendChild(month_div);
			
			month.days.forEach(function(day){
				//console.log(day);
				day_div = document.createElement("div");
				day_div.className = "timeline-day";
				day_div.textContent = day.day;
				month_div.appendChild(day_div);
				
				day.hours.forEach(function(hour){
					//console.log(hour);
					hour_div = document.createElement("div");
					hour_div.className = "timeline-hour";
					day_div.appendChild(hour_div);
					
					hour_label = document.createElement("label");
					hour_label.setAttribute("for", "id_" + year.year.toString() + month.month.toString() + day.day.toString() + hour.toString());
					hour_label.textContent = hour + ":00 - " + (hour + window_length) + ":00";
					hour_div.appendChild(hour_label);
					
					hour_input = document.createElement("input");
					hour_input.setAttribute("type", "checkbox");
					hour_input.setAttribute("name", "date");
					hour_input.setAttribute("id", "id_" + "id_" + year.year.toString() + month.month.toString() + day.day.toString() + hour.toString());
					hour_input.setAttribute("value", year.year.toString() + month.month.toString() + day.day.toString() + hour.toString());
					hour_div.appendChild(hour_input);
				});
			});
		});
	});
}

$(document).ready(renderTimelineHoursAdminSelector("selector-container", 2));

var dates = [
	{
		"year": 2014, 
		"months": [
			{
				"month": 12,
				"days": [
					{
						"day": 29,
						"hours": [
							10, 14, 16
						]
					},
					{
						"day": 30,
						"hours": [
							10, 14, 16
						]
					}
				]
			},
		]
	},
	{
		"year": 2015, 
		"months": [
			{
				"month": 1,
				"days": [
					{
						"day": 30,
						"hours": [
							10, 14, 16
						]
					}
				]
			},
		]
	}
];

$(document).ready(renderTimelineSelector("voter-container", dates, 2));