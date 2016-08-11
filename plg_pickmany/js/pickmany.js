function getPickOne(n) {
    var data = questions[n].response;
    var acount = SF_getElement(data, 'alt_fields_count', 0);
    var mcount = SF_getElement(data, 'main_fields_count', 0);
    var ans_count = SF_getElement(data, 'ans_count', 0);
    var quest_style = SF_getElement(data, 'sf_qstyle', 0);
    var selected = '';

    //if dropdown list style
    if (quest_style == 1) {
        if (acount > 0) {
            selected = " selected='selected' ";
        }
        var return_str = '<br/>' +
            '<div class="pick_one_div">' +
            '<form name="quest_form' + questions[n].cur_quest_id + '">' +
            '<select onchange="javascript: check_answer(' + n + ');" class="po_select" name="quest_select_po_' + questions[n].cur_quest_id + '" id="quest_select_po_' + questions[n].cur_quest_id + '">' +
            '<option value="0" ' + selected + '>'+select_ans+'</option>';
    } else {
        //if rodiobuttons style
        var return_str = '<div align="left" class="pick_one_div">' +
            '<form name="quest_form' + questions[n].cur_quest_id + '">' +
            '<br/>' +
            '<table id="quest_table" class="pick_one_table" >';
    }

    var i = 0;
    for (i = 0; i < mcount; i++) {
        selected = '';
        if (ans_count > 0) {
            if (SF_getElement(data, 'a_quest_id', 0) == SF_getElement(data, 'mfield_id', i)) {
                if (quest_style == 1)
                    selected = " selected='selected' ";
                else
                    selected = " checked='checked' ";
            }
        }
        if (quest_style == 1) {
            return_str = return_str + '<option value="' + SF_getElement(data, 'mfield_id', i) + '" ' + selected + ' >' +
                SF_getElement(data, 'mfield_text', i) +
                '</option>';
        }
        else {
            return_str = return_str +
                '<tr>' +
                '<td class="po_answer_cell">' +
                '<input onclick="javascript: check_answer(' + n + ');" onchange="javascript: check_answer(' + n + ');" class="po_radio" type="radio" name="quest_radio' + questions[n].cur_quest_id + '" id="quest_radio' + questions[n].cur_quest_id + i + '" value="' + SF_getElement(data, 'mfield_id', i) + '" ' + selected + '>' +
                '</td>' +
                '<td class="po_quest_cell">' +
                '<label for="quest_radio' + questions[n].cur_quest_id + i + '">' + SF_getElement(data, 'mfield_text', i) + '</label>' +
                '<br/>' +
                '</td>' +
                '</tr>';
        }
    }
    if (acount > 0) {
        selected = '';
        var other_val = '';
        if (ans_count > 0) {
            if (SF_getElement(data, 'a_quest_id', 0) == SF_getElement(data, 'afield_id', 0)) {
                if (quest_style == 1)
                    selected = " selected='selected' ";
                else
                    selected = " checked='checked' ";
            }
            other_val = SF_getElement(data, 'ans_txt', 0);
            if (other_val == '!!!---!!!') {
                other_val = '';
            }
        }
        if (quest_style == 1) {
            return_str = return_str +
                '<option value="' + SF_getElement(data, 'afield_id', 0) + '" ' + selected + ' >' + SF_getElement(data, 'afield_text', 0) + '</option>' +
                '</select>' +
                '<br/>' +
                other_answer +
                '<br/>' +
                '<input class="po_other" type="text" id="other_op_' + questions[n].cur_quest_id + '" name="other_op_' + questions[n].cur_quest_id + '" value="' + other_val + '"/>';
        }
        else {
            return_str = return_str + '<tr>' +
                '<td class="po_answer_cell">' +
                '<input onchange="javascript: check_answer(' + n + ');" class="po_radio" type="radio" name="quest_radio' + questions[n].cur_quest_id + '" id="quest_radio' + questions[n].cur_quest_id + 'e" value="' + SF_getElement(data, 'afield_id', 0) + '" ' + selected + '>' +
                '</td>' +
                '<td class="po_quest_cell">' +
                '<label for="quest_radio' + questions[n].cur_quest_id + 'e">' + SF_getElement(data, 'afield_text', 0) + '</label>' +
                '&nbsp;<input class="po_other" type="text" id="other_op_' + questions[n].cur_quest_id + '" name="other_op_' + questions[n].cur_quest_id + '" value="' + other_val + '"/>' +
                '<br/>' +
                '</td>' +
                '</tr>';
        }
    }

    if (quest_style == 1) {
        if (acount > 0)
            return_str = return_str + '</form></div>';
        else
            return_str = return_str + '</select></form></div>';
    }
    else {
        return_str = return_str + '</table></form></div>';
    }
    return return_str;
}