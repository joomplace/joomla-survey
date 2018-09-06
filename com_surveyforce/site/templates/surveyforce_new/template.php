<?php
/**
 * Survey Force component for Joomla
 * @version $Id: template.php 2009-11-16 17:30:15
 * @package Survey Force
 * @subpackage template.php
 * @author JoomPlace Team
 * @Copyright Copyright (C) JoomPlace, www.joomplace.com
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */
// no direct access
defined('_JEXEC') or die('Restricted access');



if (!class_exists('surveyforce_template_class')) {

    class surveyforce_template_class {
        /*         * *********************************************************************************************************************

          {SURVEY_NAME} � survey�s name will be placed there
          {BACKGROUND_IMAGE} - text for background image will be placed there
          {PROGRESS_BAR} - progress bar will be placed there
          {ERROR_MESSAGE_TOP} - first (top) error message will be placed there
          {ERROR_MESSAGE_BOTTOM}- second (bottom) error massage will be placed there
          {SURVEY_BODY}- survey will be placed there -  survey�s start page (description), questions and answers, final page
          (in function `SF_SurveyBody` you can define a place for each item - question text(description), answers, importance scale)

          {START_BUTTON}{PREV_BUTTON}{NEXT_BUTTON}{FINISH_BUTTON} � survey�s control buttons will be placed there

         * ********************************************************************************************************************* */

		public static function SF_MainLayout() {

			self::getJsCssTmpl();
            $return_str = <<<EOF_RES
			<!-- DON'T FORGET CHANGE HREF '-->
			<div class="contentpane surveyforce">

			<div class="componentheading" id="surveyforce"><h2>{SURVEY_NAME}</h2></div>
			<table class="contentpane" id="survey_container_tbl" style="min-height:250px; height:auto !important; height:250px; width:100%;{BACKGROUND_IMAGE} background-size: cover;" cellpadding="0" cellspacing="0" border="0" >
			<tr><td id="sf_progressbar" colspan="3" align="left">
				{PROGRESS_BAR}

			</td></tr>
			<tr><td id="td_survey_task" width="10%">
					{PREV_BUTTON}
			</td>
			<td align="center" id="sf_error_message" width="80%" align="center">
					{ERROR_MESSAGE_TOP}
			</td>
			<td id="td_survey_task" width="10%">
					{START_BUTTON}{NEXT_BUTTON}{FINISH_BUTTON}
			</td>
			</tr>		
			<tr><td id="sf_survey_body" colspan="3" valign="top">
				{SURVEY_BODY}
			</td></tr>		
			{SURVEY_USER_EMAIL}				
			</table>
			</div>	
EOF_RES;
            return $return_str;
        }

		public static function SF_SurveyBody() {
            $return_str = <<<EOFTMPL

			<div align="left" style="padding-left:10px;text-align:left;">{QUESTION_TEXT}</div>
			<div>{ANSWERS}</div>
			{IMPORTANCE_SCALE}

EOFTMPL;
            //remove new line characters
            $return_str = str_replace("\n", '', $return_str);
            $return_str = str_replace("\r", '', $return_str);
            $return_str = str_replace("'", "\\'", $return_str);
            return $return_str;
        }

        /*         * **********************************************************************************************************************************

          In this function a javascript function is placed that creates survey elements - answers for questions, importance scale, buttons, etc.
          Be careful editing it.

         * ************************************************************************************************************************************ */

		public static function SF_GetElements($sf_config) {
           
            ?>
            <script language="javascript" type="text/javascript"><!--//--><![CDATA[//><!--
                    var new_template = 0;
                function changeImage(id, j, scount) {
                    var checked = sf_getObj(id + '_' + j).checked;
                    var i = 0;
                    for (i = 0; i < scount; i++) {
                        sf_getObj(id + '_' + i).checked = '';
                        sf_getObj('img_' + id + '_' + i).className = 'no_img';
                    }

                    if (!checked) {
                        sf_getObj(id + '_' + j).checked = 'checked';
                        sf_getObj('img_' + id + '_' + j).className = 'yes_img';
                    }
                }
                function getLikertScale(n) {
                    var data = questions[n].response;
                    //question option count
                    var mcount = SF_getElement(data, 'main_fields_count', 0);
                    //question scale count
                    var scount = SF_getElement(data, 'scale_fields_count', 0);
                    //answers count
                    var ans_count = SF_getElement(data, 'ans_count', 0);
                    var factor_name = SF_getElement(data, 'factor_name', 0);

                    questions[n].kol_main_elems = mcount;
                    questions[n].main_ids_array = new Array(mcount);

                    var j;
                    for (j = 0; j < mcount; j++) {
                        questions[n].main_ids_array[j] = SF_getElement(data, 'mfield_id', j);
                    }

                    var return_str = '<div align="left" class="likert_scale_div">' +
                            '<form name="quest_form' + questions[n].cur_quest_id + '">' +
                            '<br/>' +
                            '<table id="quest_table" class="likert_scale_table" cellpadding="3" cellspacing="0">';

                    return_str = return_str + '<tr><td class="ls_factor_name">' + factor_name + '</td>';

                    //scale cells
                    for (j = 0; j < scount; j++) {
                        return_str = return_str + '<td class="ls_scale_field">' + SF_getElement(data, 'sfield_text', j) + '</td>';
                    }
                    return_str = return_str + '</tr>';

                    var k = 1;
                    var i = 0;
                    var ii = 0;
                    var jj = 0;
                    var checked = '';
                    //question option rows
                    for (i = 0; i < mcount; i++) {
                        //question option text
                        return_str = return_str + '<tr class="sectiontableentry' + k + '"><td class="ls_quest_field" ><div id="qoption_' + questions[n].cur_quest_id + '_' + SF_getElement(data, 'mfield_id', i) + '">' + SF_getElement(data, 'mfield_text', i) + '</div></td>';
                        //get row number for answers for this otpion
                        if (ans_count > 0) {
                            for (ii = 0; ii < ans_count; ii++) {
                                if (SF_getElement(data, 'a_quest_id', ii) == SF_getElement(data, 'mfield_id', i))
                                    jj = ii;
                            }
                        }
                        for (j = 0; j < scount; j++) {
                            checked = '';
                            class_name = 'no_img';
                            if (ans_count > 0) {
                                //if selected current scale
                                if (SF_getElement(data, 'ans_id', jj) == SF_getElement(data, 'sfield_id', j)) {
                                    checked = " checked='checked' ";
                                    class_name = 'yes_img';
                                }
                            }
                            return_str = return_str +
                                    '<td class="ls_answer_cell" onclick="javascript: check_answer(' + n + ');changeImage(\'quest_radio_' + questions[n].cur_quest_id + '_' + SF_getElement(data, 'mfield_id', i) + '\', ' + j + ', ' + scount + ');">' +
                                    '<div class="' + class_name + '" id="img_quest_radio_' + questions[n].cur_quest_id + '_' + SF_getElement(data, 'mfield_id', i) + '_' + j + '">&nbsp;</div>'
                                    +
                                    '<input onchange="javascript: check_answer(' + n + ');" class="ls_radio" type="radio" name="quest_radio_' + questions[n].cur_quest_id + '_' + SF_getElement(data, 'mfield_id', i) + '" value="' + SF_getElement(data, 'sfield_id', j) + '" ' + checked + ' id="quest_radio_' + questions[n].cur_quest_id + '_' + SF_getElement(data, 'mfield_id', i) + '_' + j + '">' +
                                    '</td>';
                        }
                        return_str = return_str + '</tr>';
                        k = 3 - k;
                    }
                    return_str = return_str + '</table></form></div>';
                    return return_str;
                }

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
                                '<option value="0" ' + selected + '><?php echo JText::_('SF_SELECT_ANS') ?></option>';
                    } else {
                        //if radiobuttons style
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
                                    '<?php echo JText::_('SF_OTHER_ANSWER'); ?>' +
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

                function getPickMany(n) {
                    var data = questions[n].response;
                    //question option count
                    var mcount = SF_getElement(data, 'main_fields_count', 0);
                    var acount = SF_getElement(data, 'alt_fields_count', 0);
                    var ans_count = SF_getElement(data, 'ans_count', 0);
                    var return_str = '<div align="left" class="pick_many_div">' +
                            '<form name="quest_form' + questions[n].cur_quest_id + '">' +
                            '<br/>' +
                            '<table class="pick_many_table" id="quest_table">';
                    var i = 0;
                    var selected = '';
                    for (i = 0; i < mcount; i++) {
                        selected = '';
                        if (ans_count > 0) {
                            for (ii = 0; ii < ans_count; ii++) {
                                if (SF_getElement(data, 'a_quest_id', ii) == SF_getElement(data, 'mfield_id', i))
                                    selected = " checked='checked' ";
                            }
                        }
                        return_str = return_str + '<tr>' +
                                '<td class="pm_answer_cell">' +
                                '<input onclick="javascript: return check_num_opt(' + n + ', this)" onchange="javascript:  if (check_num_opt(' + n + ', this)) check_answer(' + n + ');" class="pm_checkbox" type="checkbox" name="quest_check' + questions[n].cur_quest_id + '" id="quest_check' + questions[n].cur_quest_id + i + '" value="' + SF_getElement(data, 'mfield_id', i) + '" ' + selected + '>' +
                                '</td>' +
                                '<td class="pm_quest_cell">' +
                                '<label onclick="javascript: return check_num_opt(' + n + ', this);" for="quest_check' + questions[n].cur_quest_id + i + '">' +
                                SF_getElement(data, 'mfield_text', i) +
                                '</label>' +
                                '<br/>' +
                                '</td>' +
                                '</tr>';
                    }

                    if (acount > 0) {
                        selected = '';
                        var other_val = '';
                        if (ans_count > 0) {
                            for (ii = 0; ii < ans_count; ii++) {
                                if (SF_getElement(data, 'a_quest_id', ii) == SF_getElement(data, 'afield_id', 0))
                                    selected = " checked='checked' ";
                            }
                            other_val = SF_getElement(data, 'ans_txt', 0);
                            if (other_val == '!!!---!!!')
                                other_val = '';
                        }
                        return_str = return_str + '<tr>' +
                                '<td class="pm_answer_cell">' +
                                '<input onclick="javascript: return check_num_opt(' + n + ', this)" onchange="javascript: if (check_num_opt(' + n + ', this)) check_answer(' + n + ');" type="checkbox" class="pm_checkbox" name="quest_check' + questions[n].cur_quest_id + '" id="quest_check' + questions[n].cur_quest_id + 'e" value="' + SF_getElement(data, 'afield_id', 0) + '" ' + selected + '>' +
                                '</td>' +
                                '<td class="pm_quest_cell">' +
                                '<label onclick="javascript: return check_num_opt(' + n + ', this)" for="quest_check' + questions[n].cur_quest_id + 'e">' +
                                SF_getElement(data, 'afield_text', 0) +
                                '</label>' +
                                '&nbsp;<input class="pm_other" type="text" id="other_op_' + questions[n].cur_quest_id + '" name="other_op_' + questions[n].cur_quest_id + '" value="' + other_val + '"/>' +
                                '<br/>' +
                                '</td>' +
                                '</tr>';
                    }
                    return_str = return_str + '</table></form></div>';
                    return return_str;
                }

                function getShortAnswer(n) {
                    var data = questions[n].response;
                    var ans_count = SF_getElement(data, 'ans_count', 0);
                    var quest_inp_count = SF_getElement(data, 'quest_inp_count', 0);

                    var ans_text = '';
                    var return_str = '';
                    var i = 0;

                    //if use one huge text area
                    if (quest_inp_count == 0) {
                        if (ans_count > 0) {
                            ans_text = SF_getElement(data, 'ans_txt', 0);
                        }
                        return_str = return_str +
                                '<div align="left" class="short_ans_div">' +
                                '<br/>' +
                                '<textarea id="inp_short' + questions[n].cur_quest_id + '" class="short_ans_textarea" rows="5" >' +
                                ans_text +
                                '</textarea>' +
                                '</div>';
                    } else {
                        //if use some small input boxes and textarea's in question text
                        var tmp_str = '';

                        var x_pos = -1;
                        var y_pos = -1;
                        for (i = 0; i < quest_inp_count; i++) {
                            ans_text = '';
                            if (ans_count > 0) {
                                ans_text = SF_getElement(data, 'ans_txt', i);
                            }

                            x_pos = questions[n].cur_quest_text.indexOf('{x}');
                            y_pos = questions[n].cur_quest_text.indexOf('{y}');

                            if ((x_pos < y_pos || y_pos == -1) && x_pos != -1) {
                                tmp_str = '<input class="sa_input_text" type="text" id="short_ans_' + questions[n].cur_quest_id + '_' + i + '" name="short_ans_' + questions[n].cur_quest_id + '_' + i + '" value="' + ans_text + '" />';
                                questions[n].cur_quest_text = questions[n].cur_quest_text.replace(/\{x\}/, tmp_str);
                            } else if (y_pos != -1) {
                                tmp_str = '<textarea id="short_ans_' + questions[n].cur_quest_id + '_' + i + '" name="short_ans_' + questions[n].cur_quest_id + '_' + i + '" class="short_ans_textarea" rows="5" >' +
                                        ans_text +
                                        '</textarea>';
                                questions[n].cur_quest_text = questions[n].cur_quest_text.replace(/\{y\}/, tmp_str);
                            }
                        }

                    }

                    return return_str;
                }

                function getDropDown(n) {
                    var data = questions[n].response;
                    var acount = SF_getElement(data, 'alt_fields_count', 0);
                    var mcount = SF_getElement(data, 'main_fields_count', 0);
                    var ans_count = SF_getElement(data, 'ans_count', 0);
                    var make_select = new Array(mcount);
                    var i = 0;
                    for (i = 0; i < mcount; i++) {
                        make_select[i] = '<option class="dd_option" value="0"><?php echo JText::_('SURVEY_DROP_DOWN_FIRST_ELEMENT') ?></option>';
                    }
                    var j = 0;
                    var selected = '';
                    var jj = 0;
                    for (i = 0; i < mcount; i++) {
                        if (ans_count > 0) {
                            for (ii = 0; ii < ans_count; ii++) {
                                if (SF_getElement(data, 'a_quest_id', ii) == SF_getElement(data, 'mfield_id', i))
                                    jj = ii;
                            }
                        }
                        for (j = 0; j < acount; j++) {
                            selected = '';
                            if (ans_count > 0) {
                                if (jj >= 0 && SF_getElement(data, 'ans_id', jj) == SF_getElement(data, 'afield_id', j))
                                    selected = " selected ";
                            }
                            make_select[i] = make_select[i] + '<option class="dd_option" value ="' + SF_getElement(data, 'afield_id', j) + '" ' + selected + '>' +
                                    SF_getElement(data, 'afield_text', j) +
                                    '</option>';
                        }
                        jj = -1;
                    }
                    var return_str = '<div align="left" class="dp_n_dn_div">' +
                            '<form name="quest_form' + questions[n].cur_quest_id + '">' +
                            '<br/>' +
                            '<table id="quest_table" class="drop_down_table">';
                    for (i = 0; i < mcount; i++) {
                        return_str = return_str + '<tr>' +
                                '<td class="dd_left_cell">' + SF_getElement(data, 'mfield_text', i) + '</td>' +
                                '<td class="dd_right_cell">' +
                                '<select onchange="javascript: check_answer(' + n + ');" class="dd_select" name="quest_select_' + questions[n].cur_quest_id + '_' + SF_getElement(data, 'mfield_id', i) + '">' + make_select[i] +
                                '</select>' +
                                '</td>' +
                                '</tr>';
                    }
                    return_str = return_str + '</table></form></div>';

                    return return_str;
                }

                function getDragDrop(n) {
                    var data = questions[n].response;
                    var acount = SF_getElement(data, 'alt_fields_count', 0);
                    var mcount = SF_getElement(data, 'main_fields_count', 0);
                    var ans_count = SF_getElement(data, 'ans_count', 0);

                    questions[n].kol_drag_elems = acount; // !!!
                    questions[n].drag_array = new Array(questions[n].kol_drag_elems);
                    questions[n].coord_left = new Array(questions[n].kol_drag_elems);
                    questions[n].coord_top = new Array(questions[n].kol_drag_elems);
                    questions[n].ids_in_cont = new Array(questions[n].kol_drag_elems);
                    questions[n].cont_for_ids = new Array(questions[n].kol_drag_elems);
                    questions[n].answ_ids = new Array(questions[n].kol_drag_elems);
                    cont_index = 0;
                    last_drag_id = '';
                    last_drag_id_drag = '';
                    var return_str = '<div class="drag_drop_div">' +
                            '<table class="drag_drop_table" id="quest_table' + questions[n].cur_quest_id + '">';
                    var i = 0;
                    for (i = 0; i < mcount; i++) {
                        questions[n].answ_ids[i] = SF_getElement(data, 'afield_id', i);
                        return_str = return_str + '<tr>' +
                                '<td width="2%">' +
                                '<div id="cdiv' + questions[n].cur_quest_id + '_' + (i + 1) + '" style="position:relative; width:250px; height:30px; margin:10px; background: url(/components/com_surveyforce/cont_img.gif) no-repeat; background-position:right; background-color: <?php echo $sf_config->get('color_cont') ?>; border: 1px solid #000; cursor:default; text-align:center; vertical-align:middle;">' +
                                SF_getElement(data, 'mfield_text', i) +
                                '</div>' +
                                '</td>' +
                                '<td width="auto">' +
                                '<div id="ddiv' + questions[n].cur_quest_id + '_' + (i + 1) + '" onmousedown="startDrag(event, ' + n + ');" onmouseup="stopDrag(event, ' + n + ');" style="position:relative; width:250px; height:30px; margin:10px; background: url(/components/com_surveyforce/drag_img.gif) no-repeat; background-position:left; background-color: <?php echo $sf_config->get('color_drag') ?>; border: 1px solid #000; cursor:default; text-align:center; vertical-align:middle;">' +
                                SF_getElement(data, 'afield_text', i) +
                                '</div>' +
                                '</td>' +
                                '</tr>';
                    }
                    return_str = return_str + '</table></div>';

                    return return_str;
                }

                function getRanking(n) {
                    var data = questions[n].response;
                    var acount = SF_getElement(data, 'alt_fields_count', 0);
                    var mcount = SF_getElement(data, 'main_fields_count', 0);
                    var ans_count = SF_getElement(data, 'ans_count', 0);
                    var make_select = new Array(mcount);
                    var i = 0;
                    for (i = 0; i < mcount; i++) {
                        make_select[i] = '<option class="r_option" value="0"><?php echo JText::_("SURVEY_RANK_FIRST_ELEMENT") ?></option>';
                    }
                    var j = 0;
                    var selected = '';
                    var jj = -1;
                    var mfield_id;
                    for (i = 0; i < mcount; i++) {
                        mfield_id = SF_getElement(data, 'mfield_id', i);
                        if (ans_count > 0) {
                            for (ii = 0; ii < ans_count; ii++) {
                                if (SF_getElement(data, 'a_quest_id', ii) == mfield_id)
                                    jj = ii;
                            }

                        }
                        for (j = 0; j < acount; j++) {
                            selected = '';
                            if (ans_count > 0) {
                                if (jj >= 0 && SF_getElement(data, 'ans_id', jj) == SF_getElement(data, 'afield_id', j))
                                    selected = " selected ";
                            }
                            make_select[i] = make_select[i] + '<option class="r_option" value ="' + SF_getElement(data, 'afield_id', j) + '" ' + selected + '>' + SF_getElement(data, 'afield_text', j) + '</option>';
                        }
                        jj = -1;
                    }
                    var return_str = '<div class="ranking_div" align="left" >' +
                            '<form name="quest_form' + questions[n].cur_quest_id + '">' +
                            '<br/>' +
                            '<table class="ranking_table" id="quest_table" >';
                    var mfield_type = 0;
                    var other_inp = '';
                    var other_val = '';
                    for (i = 0; i < mcount; i++) {
                        mfield_type = SF_getElement(data, 'mfield_is_true', i);
                        if (mfield_type == 2) {
                            other_val = SF_getElement(data, 'ans_txt', 0);
                            if (other_val == '!!!---!!!') {
                                other_val = '';
                            }
                            other_inp = '<input class="r_other" type="text" id="other_op_' + questions[n].cur_quest_id + '" name="other_op_' + questions[n].cur_quest_id + '" value="' + other_val + '"/>';
                        }
                        else {
                            other_inp = '';
                            other_val = '';
                        }

                        return_str = return_str + '<tr>' +
                                '<td class="r_left_cell">' +
                                SF_getElement(data, 'mfield_text', i) + " " + other_inp +
                                '</td>' +
                                '<td class="r_right_cell">' +
                                '<select class="r_select" onchange="javascript:removeSameRank(this, ' + n + ');" name="quest_select_' + questions[n].cur_quest_id + '_' + SF_getElement(data, 'mfield_id', i) + '" id="quest_select_' + questions[n].cur_quest_id + '_' + SF_getElement(data, 'mfield_id', i) + '">' +
                                make_select[i] +
                                '</select>' +
                                '</td>' +
                                '</tr>';
                    }
                    return_str = return_str + '</table></form></div>';
                    return return_str;
                }

                function getImpScale(n) {
                    var data = questions[n].response;
                    cur_impscale_ex = 0;
                    var iscount = SF_getElement(data, 'impscale_fields_count', 0);
                    var return_str = '';
                    if (iscount > 0) {
                        questions[n].cur_impscale_ex = 1;
                        var ans_imp_count = SF_getElement(data, 'ans_imp_count', 0);
                        var iscale_name = SF_getElement(data, 'impscale_name', 0);
                        return_str = '<div align="left" class="importance_div">' +
                                '<form name="iscale_form' + questions[n].cur_quest_id + '">' +
                                '<br/>' +
                                '<br/>' +
                                '<table class="importance_table" id="iscale_table" cellpadding="0" cellspacing="0">';
                        return_str = return_str + '<tr class="sectiontableentry2"><td class="i_quest" colspan="' + iscount + '" >&nbsp;&nbsp;' + iscale_name + '<\/td><\/tr>';
                        return_str = return_str + '<tr class="sectiontableentry1">';
                        var j;
                        for (j = 0; j < iscount; j++) {
                            return_str = return_str + '<td class="i_text_cell" onclick="javascript: sf_getObj(\'iscale_radio' + questions[n].cur_quest_id + '_' + j + '\').checked=\'checked\';">' +
                                    '<label for="iscale_radio' + questions[n].cur_quest_id + '_' + j + '" style="cursor: pointer;">' +
                                    SF_getElement(data, 'isfield_text', j) +
                                    '</label>' +
                                    '</td>';
                        }
                        return_str = return_str + '</tr>';
                        var i;
                        return_str = return_str + '<tr class="sectiontableentry2">';
                        var selected = '';
                        for (j = 0; j < iscount; j++) {
                            selected = '';
                            if (ans_imp_count > 0) {
                                if (SF_getElement(data, 'isfield_id', j) == SF_getElement(data, 'ans_imp_id', 0)) {
                                    selected = " checked='checked' ";
                                }
                            }
                            return_str = return_str + '<td class="i_ans_cell" onclick="javascript: sf_getObj(\'iscale_radio' + questions[n].cur_quest_id + '_' + j + '\').checked=\'checked\';">' +
                                    '<input class="i_radio" type="radio" name="iscale_radio' + questions[n].cur_quest_id + '" id="iscale_radio' + questions[n].cur_quest_id + '_' + j + '" value="' + SF_getElement(data, 'isfield_id', j) + '" ' + selected + '/>' +
                                    '</td>';
                        }
                        return_str = return_str + "<\/tr>";
                        return_str = return_str + "<\/table><\/form><\/div>";
                    }
                    return return_str;
                }

                function getQuestionTemplate() {
                    var return_str = '<?php echo surveyforce_template_class::SF_SurveyBody(); ?>';
                    return return_str;
                }

                function getQuestionDelimeter() {
                    return '<br/><hr/><br/>';
                }

                function getButton(type, label, onclick) {
                    /* type = prev, next, finish
                     */
                    var return_str = '<div class="' + type + '_button"><a class="' + type + '_link" onfocus="javascript: this.blur();"  href="javascript: void(0);" onclick="javascript: ' + onclick + '">' + label + '</a></div>';

                    if (type == 'prev') {
                        return_str = '<div class="' + type + '_button"><a class="' + type + '_link" onfocus="javascript: this.blur();"  href="javascript: void(0);" onclick="javascript: ' + onclick + '">' + label + '</a></div>';
                    }

                    return return_str;
                }
                //--><!]]>
            </script>
            <?php
        }

		public static function SF_GetStartButton() {
            
            return '<div class="start_button"><a class="start_link" onfocus="javascript: this.blur();" href="javascript: void(0);" onclick="javascript: sf_StartSurveyOn()">' . JText::_('COM_SURVEYFORCE_START') . '</a></div>';
        }

		public static function getJsCssTmpl(){
			$document = JFactory::getDocument();
			$document->addStyleSheet(JUri::root().'components/com_surveyforce/templates/surveyforce_new/surveyforce.css');
		}

    }

    //class..
}//if 
?>