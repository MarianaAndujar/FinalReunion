/**
 * 
 */

/**
 * 
 * @param {Object} parent
 * @param {Object} window_length
 * @param {Object} origin_date
 */
function renderTimelineHoursAdminSelector(parent, window_length, origin_date){
	var ref_elem = document.getElementById("submit_form");
		
	var selector = document.createElement("table");
	parent.insertBefore(selector, ref_elem);
		
	var selector_labels_row = document.createElement("tr");
	selector.appendChild(selector_labels_row);
	var selector_inputs_row = document.createElement("tr");
	selector.appendChild(selector_inputs_row);
	
	var id_prefix = origin_date.name;
	for(i = 0 ; i < 24 ; i++){
		var selector_label_cell = document.createElement("td");
		selector_labels_row.appendChild(selector_label_cell);
		var selector_label = document.createElement("label");
		selector_label.setAttribute("for", id_prefix+"_hour_"+i);
		selector_label.textContent = i + ":00 - " + (i + parseInt(window_length)) + ":00";
		selector_label_cell.appendChild(selector_label);
		
		var selector_input_cell = document.createElement("td");
		selector_inputs_row.appendChild(selector_input_cell);
		var selector_input = document.createElement("input");
		selector_input.setAttribute("type", "checkbox");
		selector_input.setAttribute("name", "hours_"+id_prefix);
		selector_input.setAttribute("value", i);
		selector_input.setAttribute("id", id_prefix+"_hour_"+i);
		selector_input_cell.appendChild(selector_input);
	}
	
	var date_input = document.createElement("input");
	$(date_input).datepicker();
	date_input.setAttribute("name", date_input.id);
	parent.insertBefore(date_input, ref_elem);
	
	var add_button = document.createElement("button");
	add_button.setAttribute("type", "button");
	add_button.textContent = "ajouter";
	add_button.onclick = function(){
		renderTimelineHoursAdminSelector(parent, window_length, date_input);
		return false;
	};
	parent.insertBefore(add_button, ref_elem);
}

/**
 * 
 * @param {Object} parent_id
 * @param {Object} window_length
 */
function renderAdminForm(parent_id, window_length){
	var parent = document.getElementById(parent_id);
	
	var form = document.createElement("form");
	form.setAttribute("method", "post");
	form.setAttribute("action", "toast.html");
	form.setAttribute("autocomplete", "off");
	parent.appendChild(form);
	
	var window_length_input = document.createElement("input");
	window_length_input.setAttribute("value", window_length);
	var window_length_button = document.createElement("button");
	window_length_button.setAttribute("type", "button");
	window_length_button.textContent = "changer durée (réinitialise le formulaire)";
	window_length_button.onclick = function(){
		parent.parentNode.removeChild(parent);
		renderAdminForm(parent_id, window_length_input.value);
		return null;
	};
	form.appendChild(window_length_input);
	form.appendChild(window_length_button);
	form.appendChild(document.createElement("br"));
	
	var date_input = document.createElement("input");
	$(date_input).datepicker();
	date_input.setAttribute("name", date_input.id);
	form.appendChild(date_input);
	
	var add_button = document.createElement("button");
	add_button.setAttribute("type", "button");
	add_button.textContent = "ajouter";
	add_button.onclick = function(){
		renderTimelineHoursAdminSelector(form, window_length_input.value, date_input);
		return false;
	};
	form.appendChild(add_button);
	
	var submit_button = document.createElement("input");
	submit_button.setAttribute("type", "submit");
	submit_button.setAttribute("id", "submit_form");
	form.appendChild(submit_button);
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

$(document).ready(renderAdminForm("selector-container", 2));

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