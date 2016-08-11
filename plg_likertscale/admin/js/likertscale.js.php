<script type="text/javascript">
var quest_type = <?php echo isset($q_om_type) ? $q_om_type : 0; ?>;
var field_name = '';
var field_id = '';

function Redeclare_element_inputs2(object) {
	if (object.hasChildNodes()) {
		var children = object.childNodes;
		for (var i = 0; i < children.length; i++) {
			if (children[i].nodeName.toLowerCase() == 'input') {
				var inp_name = children[i].name;

				var inp_value = children[i].value;
				object.removeChild(object.childNodes[i]);

				var input_hidden = document.createElement("input");
				input_hidden.type = "hidden";
				input_hidden.name = inp_name;
				input_hidden.value = inp_value;
				object.appendChild(input_hidden);
			}
		}
	}
}

function analyze_cat() {
	var element = getObj('inp_tmp');
	if (element) {
		var parent = element.parentNode;

		var inpu_value = element.value;
		parent.removeChild(element);
		var cat_id_sss = '0';
		if (parent.hasChildNodes()) {
			var children = parent.childNodes;
			for (var i = 0; i < children.length; i++) {
				if (children[i].nodeName.toLowerCase() == 'input') {
					if (children[i].name == field_id) {
						cat_id_sss = children[i].value;
					}
				}
			}
		}
		var input_cat2 = document.createElement("input");
		input_cat2.type = "hidden";
		input_cat2.name = field_name;
		input_cat2.value = inpu_value;
		var input_id2 = document.createElement("input");
		input_id2.type = "hidden";
		input_id2.name = field_id;
		input_id2.value = cat_id_sss;

		var span = document.createTextNode(inpu_value);
		parent.innerHTML = '';
		parent.appendChild(input_cat2);
		parent.appendChild(input_id2);

		parent.appendChild(span);

	}

}

var edit_id = '';
function edit_name(e, field, field2) {
	analyze_cat()
	field_name = field;
	field_id = field2;
	if (!e) {
		e = window.event;
	}
	var cat2 = e.target ? e.target : e.srcElement;
	Redeclare_element_inputs2(cat2);
	var cat_name_value = '';
	var found = false;
	if (cat2.hasChildNodes()) {
		var children = cat2.childNodes;
		var children_count = children.length;
		for (var i = 0; i < children_count; i++) {
			if (children[i].nodeName.toLowerCase() == 'input') {
				if (children[i].name == field_name) {
					cat_name_value = children[i].value;
					found = true;
				}
			}
		}
		if (!found)
			return;
		for (var i = 0; i < children.length; i++) {
			if (children[i].nodeName.toLowerCase() != 'input') {
				cat2.removeChild(cat2.childNodes[i]);
			}
		}
	}
	var input_cat3 = document.createElement("input");
	input_cat3.type = "text";
	input_cat3.id = "inp_tmp";
	input_cat3.name = "inp_tmp";

	input_cat3.value = cat_name_value;
	input_cat3.setAttribute("style", "z-index:5000");
	if (window.addEventListener) {
		input_cat3.addEventListener('dblclick', analyze_cat, false);
	} else {
		input_cat3.attachEvent('ondblclick', analyze_cat);
	}
	cat2.appendChild(input_cat3);

}

function ReAnalize_tbl_Rows(start_index, tbl_id) {
	start_index = 1;
	var tbl_elem = getObj(tbl_id);

	if (tbl_elem.rows[start_index]) {
		var count = start_index;
		var row_k = 1 - start_index % 2;//0;
		for (var i = start_index; i < tbl_elem.rows.length; i++) {
			tbl_elem.rows[i].cells[0].innerHTML = count;
			if (i > 1) {
				tbl_elem.rows[i].cells[3].innerHTML = '<a href="javascript: void(0);" onClick="javascript:Up_tbl_row(this); return false;" title="<?php echo JText::_('COM_SURVEYFORCE_MOVE_UP'); ?>"><img src="<?php echo JURI::root() ?>administrator/components/com_surveyforce/assets/images/uparrow.png"  border="0" alt="<?php echo JText::_('COM_SURVEYFORCE_MOVE_UP'); ?>"></a>';
			} else {
				tbl_elem.rows[i].cells[3].innerHTML = '';
			}
			if (i < (tbl_elem.rows.length - 1)) {
				tbl_elem.rows[i].cells[4].innerHTML = '<a href="javascript: void(0);" onClick="javascript:Down_tbl_row(this); return false;" title="<?php echo JText::_('COM_SURVEYFORCE_MOVE_DOWN'); ?>"><img src="<?php echo JURI::root() ?>administrator/components/com_surveyforce/assets/images/downarrow.png"  border="0" alt="<?php echo JText::_('COM_SURVEYFORCE_MOVE_DOWN'); ?>"></a>';
				;
			} else {
				tbl_elem.rows[i].cells[4].innerHTML = '';
			}
			tbl_elem.rows[i].className = 'row' + row_k;
			count++;
			row_k = 1 - row_k;
		}
	}

}

function Redeclare_element_inputs(object) {
	if (object.hasChildNodes()) {
		var children = object.childNodes;
		for (var i = 0; i < children.length; i++) {
			if (children[i].nodeName.toLowerCase() == 'input') {
				var inp_name = children[i].name;
				var inp_value = children[i].value;
				object.removeChild(object.childNodes[i]);
				var input_hidden = document.createElement("input");
				input_hidden.type = "hidden";
				input_hidden.name = inp_name;
				input_hidden.value = inp_value;
				object.appendChild(input_hidden);
			}
		}
		;
	}
	;
}


function Delete_tbl_row(element) {
	var del_index = element.parentNode.parentNode.sectionRowIndex;
	var tbl_id = element.parentNode.parentNode.parentNode.parentNode.id;
	element.parentNode.parentNode.parentNode.deleteRow(del_index);
	ReAnalize_tbl_Rows(del_index - 1, tbl_id);
}

function Up_tbl_row(element) {
	if (element.parentNode.parentNode.sectionRowIndex > 1) {
		var sec_indx = element.parentNode.parentNode.sectionRowIndex;
		var table = element.parentNode.parentNode.parentNode;
		var tbl_id = table.parentNode.id;
		var cell2_tmp = element.parentNode.parentNode.cells[1].innerHTML;
		var td_value = element.parentNode.parentNode.cells[1].childNodes[0].value;
		var td_id = element.parentNode.parentNode.cells[1].childNodes[1].value;

		var name1 = element.parentNode.parentNode.cells[1].childNodes[0].name;
		var name2 = element.parentNode.parentNode.cells[1].childNodes[1].name;
		element.parentNode.parentNode.parentNode.deleteRow(element.parentNode.parentNode.sectionRowIndex);
		// nel'zya prosto skopirovat' staryi innerHTML, t.k. ne sozdadutsya DOM elementy (for IE, Opera compatible).
		var row = table.insertRow(sec_indx - 1);
		var cell1 = document.createElement("td");
		var cell2 = document.createElement("td");
		var cell3 = document.createElement("td");
		var cell4 = document.createElement("td");

		var input_hidden = document.createElement("input");
		var input_hidden2 = document.createElement("input");
		var span = document.createElement("span");

		cell1.align = 'center';
		cell1.innerHTML = 0;
		cell2.align = 'left';

		input_hidden.type = "hidden";
		input_hidden.value = td_value;
		input_hidden.name = name1;
		input_hidden.setAttribute('name', name1);

		input_hidden2.type = "hidden";
		input_hidden2.value = td_id;
		input_hidden2.name = name2;
		input_hidden2.setAttribute('name', name2);

		span.innerHTML = td_value;
		cell2.appendChild(input_hidden);
		cell2.appendChild(input_hidden2);
		cell2.appendChild(span);

		cell3.innerHTML = '<a href="javascript: void(0);" onClick="javascript:Delete_tbl_row(this); return false;" title="<?php echo JText::_('COM_SURVEYFORCE_DELETE'); ?>"><img src="<?php echo JURI::root() ?>administrator/components/com_surveyforce/assets/images/publish_x.png"  border="0" alt="<?php echo JText::_('COM_SURVEYFORCE_DELETE'); ?>"></a>';
		cell4.innerHTML = '<a href="javascript: void(0);" onClick="javascript:Up_tbl_row(this); return false;" title="<?php echo JText::_('COM_SURVEYFORCE_MOVE_UP'); ?>"><img src="<?php echo JURI::root() ?>administrator/components/com_surveyforce/assets/images/uparrow.png"  border="0" alt="<?php echo JText::_('COM_SURVEYFORCE_MOVE_UP'); ?>"></a>';
		row.appendChild(cell1);
		row.appendChild(cell2);
		row.appendChild(cell3);
		row.appendChild(cell4);
		row.appendChild(document.createElement("td"));
		row.appendChild(document.createElement("td"));

		ReAnalize_tbl_Rows(sec_indx - 2, tbl_id);
	}
}

function Down_tbl_row(element) {
	if (element.parentNode.parentNode.sectionRowIndex < element.parentNode.parentNode.parentNode.rows.length - 1) {
		var sec_indx = element.parentNode.parentNode.sectionRowIndex;
		var table = element.parentNode.parentNode.parentNode;
		var tbl_id = table.parentNode.id;
		var cell2_tmp = element.parentNode.parentNode.cells[1].innerHTML;
		var td_value = element.parentNode.parentNode.cells[1].childNodes[0].value;
		var td_id = element.parentNode.parentNode.cells[1].childNodes[1].value;

		var name1 = element.parentNode.parentNode.cells[1].childNodes[0].name;
		var name2 = element.parentNode.parentNode.cells[1].childNodes[1].name;

		element.parentNode.parentNode.parentNode.deleteRow(element.parentNode.parentNode.sectionRowIndex);
		var row = table.insertRow(sec_indx + 1);
		var cell1 = document.createElement("td");
		var cell2 = document.createElement("td");
		var cell3 = document.createElement("td");
		var cell4 = document.createElement("td");

		var input_hidden = document.createElement("input");
		var input_hidden2 = document.createElement("input");
		var span = document.createElement("span");

		input_hidden.type = "hidden";
		input_hidden.name = name1;
		input_hidden.value = td_value;

		input_hidden2.type = "hidden";
		input_hidden2.name = name2;
		input_hidden2.value = td_id;

		cell1.align = 'center';
		cell1.innerHTML = 0;
		cell2.align = 'left';

		span.innerHTML = td_value;
		cell2.appendChild(input_hidden);
		cell2.appendChild(input_hidden2);
		cell2.appendChild(span);

		cell3.innerHTML = '<a href="javascript: void(0);" onClick="javascript:Delete_tbl_row(this); return false;" title="<?php echo JText::_('COM_SURVEYFORCE_DELETE'); ?>"><img src="<?php echo JURI::root() ?>administrator/components/com_surveyforce/assets/images/publish_x.png"  border="0" alt="<?php echo JText::_('COM_SURVEYFORCE_DELETE'); ?>"></a>';
		cell4.innerHTML = '<a href="javascript: void(0);" onClick="javascript:Up_tbl_row(this); return false;" title="<?php echo JText::_('COM_SURVEYFORCE_MOVE_UP'); ?>"><img src="<?php echo JURI::root() ?>administrator/components/com_surveyforce/assets/images/uparrow.png"  border="0" alt="<?php echo JText::_('COM_SURVEYFORCE_MOVE_UP'); ?>"></a>';
		row.appendChild(cell1);
		row.appendChild(cell2);
		row.appendChild(cell3);
		row.appendChild(cell4);
		row.appendChild(document.createElement("td"));
		row.appendChild(document.createElement("td"));
		ReAnalize_tbl_Rows(sec_indx, tbl_id);
	}
}

function Add_new_tbl_field(elem_field, tbl_id, field_name, field_name2) {
	var new_element_txt = getObj(elem_field).value;
	if (TRIM_str(new_element_txt) == '') {
		alert("<?php echo JText::_('COM_SURVEYFORCE_PLEASE_ENTER_TEXT_TO_FIELD'); ?>");
		return;
	}
	getObj(elem_field).value = '';
	var tbl_elem = getObj(tbl_id);
	var row = tbl_elem.insertRow(tbl_elem.rows.length);
	var cell1 = document.createElement("td");
	var cell2 = document.createElement("td");
	var cell3 = document.createElement("td");
	var cell4 = document.createElement("td");
	var cell5 = document.createElement("td");
	var cell6 = document.createElement("td");
	var input_hidden = document.createElement("input");
	var input_hidden2 = document.createElement("input");
	var span = document.createElement("span");
	input_hidden.type = "hidden";
	input_hidden.name = field_name;
	input_hidden.value = new_element_txt;

	input_hidden2.type = "hidden";
	input_hidden2.name = field_name2;
	input_hidden2.value = 0;
	cell1.align = 'center';
	cell1.innerHTML = 0;
	cell2.setAttribute("ondblclick", "javascript:edit_name(event, '" + field_name + "','" + field_name2 + "');");
	span.innerHTML = new_element_txt;
	cell2.appendChild(input_hidden);
	cell2.appendChild(input_hidden2);
	cell2.appendChild(span);
	cell3.innerHTML = '<a href="javascript: void(0);" onClick="javascript:Delete_tbl_row(this); return false;" title="<?php echo JText::_('COM_SURVEYFORCE_DELETE'); ?>"><img src="<?php echo JURI::root() ?>administrator/components/com_surveyforce/assets/images/publish_x.png"  border="0" alt="<?php echo JText::_('COM_SURVEYFORCE_DELETE'); ?>"></a>';
	cell4.innerHTML = '<a href="javascript: void(0);" onClick="javascript:Up_tbl_row(this); return false;" title="<?php echo JText::_('COM_SURVEYFORCE_MOVE_UP'); ?>"><img src="<?php echo JURI::root() ?>administrator/components/com_surveyforce/assets/images/uparrow.png"  border="0" alt="<?php echo JText::_('COM_SURVEYFORCE_MOVE_UP'); ?>"></a>';
	cell5.innerHTML = '';
	row.appendChild(cell1);
	row.appendChild(cell2);
	row.appendChild(cell3);
	row.appendChild(cell4);
	row.appendChild(cell5);
	row.appendChild(cell6);
	ReAnalize_tbl_Rows(tbl_elem.rows.length - 2, tbl_id);
}

function Delete_tbl_row2(element) {
	var del_index = element.parentNode.parentNode.sectionRowIndex;
	var tbl_id = element.parentNode.parentNode.parentNode.parentNode.id;
	element.parentNode.parentNode.parentNode.deleteRow(del_index);
	ReAnalize_tbl_Rows2(del_index - 1, tbl_id);
}

function ReAnalize_tbl_Rows2(start_index, tbl_id) {
	start_index = 1;
	var tbl_elem = getObj(tbl_id);
	if (tbl_elem.rows[start_index]) {
		var count = start_index;
		var row_k = 1 - start_index % 2;
		for (var i = start_index; i < tbl_elem.rows.length; i++) {
			tbl_elem.rows[i].cells[0].innerHTML = count;
			Redeclare_element_inputs(tbl_elem.rows[i].cells[1]);
			Redeclare_element_inputs(tbl_elem.rows[i].cells[2]);
			tbl_elem.rows[i].className = 'row' + row_k;
			count++;
			row_k = 1 - row_k;
		}
	}
}



function Add_new_tbl_field2(elem_field, tbl_id, field_name, elem_field2, field_name2) {
	var new_element_txt = getObj(elem_field).value;
	var new_element_txt2 = getObj(elem_field2).value;
	if (getObj(elem_field2).selectedIndex < 0)
		return;
	var new_element_txt2_text = getObj(elem_field2).options[getObj(elem_field2).selectedIndex].innerHTML;
	if (TRIM_str(new_element_txt) == '') {
		alert("<?php echo JText::_('COM_SURVEYFORCE_PLEASE_ENTER_TEXT_TO_FIELD'); ?>");
		return;
	}
	var tbl_elem = getObj(tbl_id);
	var row = tbl_elem.insertRow(tbl_elem.rows.length);
	var cell1 = document.createElement("td");
	var cell2 = document.createElement("td");
	var cell2b = document.createElement("td");
	var cell3 = document.createElement("td");
	var cell3b = document.createElement("td");
	var cell4 = document.createElement("td");
	var cell5 = document.createElement("td");
	var cell6 = document.createElement("td");
	var input_hidden = document.createElement("input");
	input_hidden.type = "hidden";
	input_hidden.name = field_name;
	input_hidden.value = new_element_txt;
	var input_hidden2 = document.createElement("input");
	input_hidden2.type = "hidden";
	input_hidden2.name = field_name2;
	input_hidden2.value = new_element_txt2;
	cell1.align = 'center';
	cell1.innerHTML = 0;
	cell2.innerHTML = new_element_txt;
	cell2.appendChild(input_hidden);
	cell2b.innerHTML = new_element_txt2_text;
	cell2b.appendChild(input_hidden2);
	cell3.innerHTML = '<a href="javascript: void(0);" onClick="javascript:Delete_tbl_row2(this); return false;" title="<?php echo JText::_('COM_SURVEYFORCE_DELETE'); ?>"><img src="<?php echo JURI::root() ?>administrator/components/com_surveyforce/assets/images/publish_x.png"  border="0" alt="<?php echo JText::_('COM_SURVEYFORCE_DELETE'); ?>"></a>';
	cell3b.innerHTML = '<input type="text" style="text-align:center" class="text_area" name="priority[]" size="3" value="' + getObj('new_priority').value + '" />';
	getObj('new_priority').value = '0';
	row.appendChild(cell3);
	cell4.innerHTML = '';
	cell5.innerHTML = '';
	row.appendChild(cell1);
	row.appendChild(cell2);
	row.appendChild(cell2b);
	row.appendChild(cell3b);
	row.appendChild(cell3);
	row.appendChild(cell4);
	row.appendChild(cell5);
	row.appendChild(cell6);
	ReAnalize_tbl_Rows2(tbl_elem.rows.length - 2, tbl_id);
}

function Add_fields_to_select() {
	var tbl_elem = getObj('qfld_tbl');
	var start_index = 1;
	document.adminForm.sf_field_list.options.length = 0;
	var option_ind = 0;
	if (tbl_elem.rows[start_index]) {
		var count = start_index;
		for (var i = start_index; i < tbl_elem.rows.length; i++) {
			if (tbl_elem.rows[i].cells[1].hasChildNodes()) {
				var children = tbl_elem.rows[i].cells[1].childNodes;
				for (var ii = 0; ii < children.length; ii++) {
					if (children[ii].nodeName.toLowerCase() == 'input' && children[ii].name == 'sf_hid_fields[]') {
						document.adminForm.sf_field_list.options[option_ind] = new Option(children[ii].value, children[ii].value);
						option_ind++;
					}
				}
				;
			}
			;

		}
	}
	if (getObj('other_option_cb').checked) {
		document.adminForm.sf_field_list.options[option_ind] = new Option(getObj('other_option').value, getObj('other_option').value);
		option_ind++;
	}
}

function Delete_row(element) {
	var del_index = element.parentNode.parentNode.sectionRowIndex;
	var tbl_id = element.parentNode.parentNode.parentNode.parentNode.id;
	element.parentNode.parentNode.parentNode.deleteRow(del_index);
}

function addRow() {

	var qtype = jQuery('#sf_qtype2').get(0).value;
	var sf_field_data_m = jQuery('#sf_field_data_m').get(0).options[jQuery('#sf_field_data_m').get(0).selectedIndex].value;
	var q_id = jQuery('#sf_quest_list3').get(0).options[jQuery('#sf_quest_list3').get(0).selectedIndex].value;
	var sf_field_data_a = 0;

	var tbl_elem = jQuery('#show_quest').get(0);
	var row = tbl_elem.insertRow(tbl_elem.rows.length);

	var cell1 = document.createElement("td");
	var cell2 = document.createElement("td");
	var cell3 = document.createElement("td");
	var cell4 = document.createElement("td");
	var input_hidden = document.createElement("input");
	var input_hidden2 = document.createElement("input");
	var input_hidden3 = document.createElement("input");
	input_hidden.type = "hidden";
	input_hidden.name = 'sf_hid_rule2_id[]';
	input_hidden.value = sf_field_data_m;

	input_hidden2.type = "hidden";
	input_hidden2.name = 'sf_hid_rule2_alt_id[]';
	input_hidden2.value = sf_field_data_a;

	input_hidden3.type = "hidden";
	input_hidden3.name = 'sf_hid_rule2_quest_id[]';
	input_hidden3.value = q_id;
	cell1.width = '375px';
	cell1.innerHTML = '<?php echo JText::_('COM_SURVEYFORCE_FOR_QUESTION'); ?> "'+jQuery('#sf_quest_list3').get(0).options[jQuery('#sf_quest_list3').get(0).selectedIndex].innerHTML+'"';
	cell1.appendChild(input_hidden);
	cell1.appendChild(input_hidden2);
	cell1.appendChild(input_hidden3);

	cell2.innerHTML = ' <?php echo JText::_('COM_SURVEYFORCE_ANSWER_IS'); ?> "'+jQuery('#sf_field_data_m').get(0).options[jQuery('#sf_field_data_m').get(0).selectedIndex].innerHTML+'"';

	cell4.innerHTML = '<a href="javascript: void(0);" onclick="javascript:Delete_row(this); return false;" title="<?php echo JText::_('COM_SURVEYFORCE_DELETE'); ?>"><img src="<?php echo JURI::root()?>administrator/components/com_surveyforce/assets/images/publish_x.png"  border="0" alt="<?php echo JText::_('COM_SURVEYFORCE_DELETE'); ?>"></a>';
	row.appendChild(cell1);
	row.appendChild(cell2);
	row.appendChild(cell3);
	row.appendChild(cell4);
}

function getObj(name)
{
	if (document.getElementById)  {  return document.getElementById(name);  }
	else if (document.all)  {  return document.all[name];  }
	else if (document.layers)  {  return document.layers[name];  }
}

function TRIM_str(sStr) {
	return (sStr.replace(/^[\s\xA0]+/, "").replace(/[\s\xA0]+$/, ""));
}

function processReq(http_request) {
	if (http_request.readyState == 4) {
		if ((http_request.status == 200)) {
			var response = http_request.responseXML.documentElement;
			var text = '<?php echo JText::_('COM_SURVEYFORCE_REQUEST_ERROR'); ?>';
			try {
				text = response.getElementsByTagName('data')[0].firstChild.data;
			} catch (e) {
			}
			jQuery('div#quest_show_div').html(text);
		}
	}
}

function showOptions(val) {

	jQuery('input#add_button').get(0).style.display = 'none';
	jQuery('div#quest_show_div').html("<?php echo JText::_('COM_SURVEYFORCE_PLEASE_WAIT_LOADING'); ?>");
	var http_request = false;
	if (window.XMLHttpRequest) { // Mozilla, Safari,...
		http_request = new XMLHttpRequest();
		if (http_request.overrideMimeType) {
			http_request.overrideMimeType('text/xml');
		}
	} else if (window.ActiveXObject) { // IE
		try {
			http_request = new ActiveXObject("Msxml2.XMLHTTP");
		} catch (e) {
			try {
				http_request = new ActiveXObject("Microsoft.XMLHTTP");
			} catch (e) {
			}
		}
	}
	if (!http_request) {
		return false;
	}

	http_request.onreadystatechange = function() {
		processReq(http_request);
	};
	<?php

	if ( strpos(JUri::base(), 'administrator/') )
		$http_request_path = JUri::base().'index.php?no_html=1&option=com_surveyforce&task=question.get_options';
	else
		$http_request_path = JUri::base().'index.php?no_html=1&option=com_surveyforce&view=authoring&task=get_options';

	?>
	http_request.open('GET', '<?php echo $http_request_path; ?>&sf_qtype=' + quest_type + '&rand=<?php echo time(); ?>&quest_id=' + val, true);
	http_request.send(null);
	sf_is_loading = false;
}

jQuery(document).ready(function() {

	if (jQuery('#sf_quest_list3').get(0).options.length > 0)
		showOptions(jQuery('#sf_quest_list3').get(0).options[jQuery('#sf_quest_list3').get(0).selectedIndex].value);
	else {
		jQuery('table#show_quest').get(0).style.display = 'none';
		jQuery('table#show_quest').get(0).style.display = 'none';
	}
});
</script>

