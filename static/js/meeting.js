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
	for(i = 0 ; i + parseInt(window_length) <= 24 ; i++){
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
		selector_input.setAttribute("name", "hours_"+id_prefix+"[]");
		selector_input.setAttribute("value", i);
		selector_input.setAttribute("id", id_prefix+"_hour_"+i);
		selector_input_cell.appendChild(selector_input);
	}
	
	var date_input = document.createElement("input");
	$(date_input).datepicker({ dateFormat: 'yy-mm-dd' });
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
	form.setAttribute("action", "create");
	form.setAttribute("autocomplete", "off");
	parent.appendChild(form);
	
	var meeting_name_input = document.createElement("input");
	meeting_name_input.setAttribute("type", "text");
	meeting_name_input.setAttribute("name", "meeting_name");
	form.appendChild(meeting_name_input);
	
	var meeting_desc_input = document.createElement("input");
	meeting_desc_input.setAttribute("type", "text");
	meeting_desc_input.setAttribute("name", "meeting_description");
	form.appendChild(meeting_desc_input);
	
	var window_length_input = document.createElement("input");
	window_length_input.setAttribute("type", "number");
	window_length_input.setAttribute("name", "meeting_duration");
	window_length_input.setAttribute("value", window_length);
	var window_length_button = document.createElement("button");
	window_length_button.setAttribute("type", "button");
	window_length_button.textContent = "changer durée (réinitialise le formulaire)";
	window_length_button.onclick = function(){
		//TODO: get previous settings and rebind them to the new form
		parent.removeChild(form);
		renderAdminForm(parent_id, window_length_input.value);
		return null;
	};
	form.appendChild(window_length_input);
	form.appendChild(window_length_button);
	form.appendChild(document.createElement("br"));
	
	var date_input = document.createElement("input");
	$(date_input).datepicker({ dateFormat: 'yy-mm-dd' });
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