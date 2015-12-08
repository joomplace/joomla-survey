<?php
/**
 * Survey Force component for Joomla
 * @version $Id: surveyforce.html.php 2009-11-16 17:30:15
 * @package Survey Force
 * @subpackage surveyforce.html.php
 * @author JoomPlace Team
 * @Copyright Copyright (C) JoomPlace, www.joomplace.com
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */
// no direct access
defined('_JEXEC') or die('Restricted access');
// Component Helper
jimport('joomla.application.component.helper');

class SurveyforceHtml {

    public function processTemplate($template, $sf_config, $survey, $show_description) {

        $template = str_replace('{SURVEY_NAME}', $survey->sf_name, $template);

        if ($survey->sf_image) {
            $template = preg_replace('/\{BACKGROUND_IMAGE\}/', "background: url(media/com_surveyforce/" . $survey->sf_image . ") no-repeat;", $template, 1);
        }
        $template = preg_replace('/\{PROGRESS_BAR\}/', SurveyforceHtml::get_progress_bar($sf_config), $template, 1);
        $template = preg_replace('/\{ERROR_MESSAGE_TOP\}/', SurveyforceHtml::get_error_message_top($sf_config), $template, 1);
        $template = preg_replace('/\{ERROR_MESSAGE_BOTTOM\}/', SurveyforceHtml::get_error_message_bottom($sf_config), $template, 1);

        $template = str_replace('{SURVEY_BODY}', SurveyforceHtml::get_container($survey->sf_descr, $show_description), $template);

        $count = substr_count($template, '{START_BUTTON}');
        for ($i = 0; $i < $count; $i++) {
            $template = preg_replace('/\{START_BUTTON\}/', '<span class="start_bt_container" id="start_' . $i . '">' . surveyforce_template_class::SF_GetStartButton() . '</span>', $template, 1);
        }

        $count = substr_count($template, '{PREV_BUTTON}');
        for ($i = 0; $i < $count; $i++) {
            $template = preg_replace('/\{PREV_BUTTON\}/', SurveyforceHtml::get_button('prev', $i), $template, 1);
        }

        $count = substr_count($template, '{NEXT_BUTTON}');
        for ($i = 0; $i < $count; $i++) {
            $template = preg_replace('/\{NEXT_BUTTON\}/', SurveyforceHtml::get_button('next', $i), $template, 1);
        }

        $count = substr_count($template, '{FINISH_BUTTON}');
        for ($i = 0; $i < $count; $i++) {
            $template = preg_replace('/\{FINISH_BUTTON\}/', SurveyforceHtml::get_button('finish', $i), $template, 1);
        }

        $regex = '/{.*?}/i';
        $template = preg_replace($regex, '', $template);
        return $template;
    }

    public function get_progress_bar($sf_config) {
        
        $str = '<div id="progress" style="display:none">
					<div style="border: 1px solid ' . $sf_config->get('color_border') . '; width:100%; background-color: ' . $sf_config->get('color_uncompleted') . '; height:15px;text-align:left;">
						<div id="progress_bar" style="text-align:left !important; width:0%; background-color: ' . $sf_config->get('color_completed') . '; color: ' . $sf_config->get('color_text') . '; height:15px;">&nbsp;</div>
					</div>
					<div id="progress_bar_txt"  style="float: center; position:relative; top:-16px;color: ' . $sf_config->get('color_text') . '; ">' . JText::_('SF_PROGRESS') . ' 0%</div>
				</div>';

        return $str;
    }

    public function get_error_message_top() {
        $str = '<span id="error_messagebox" style="visibility:hidden;">Error text here</span>';

        return $str;
    }

    public function get_error_message_bottom() {
        $str = '<span id="error_messagebox2" style="visibility:hidden;">Error text here</span>';

        return $str;
    }

    public function get_container($text, $show_description = 1) {
        $str = ' <div id="survey_container" ><div id="start_div">';
        if (!$show_description) {
            $str .= '<script language="javascript" type="text/javascript">sf_StartSurveyOn();</script>';
        } else {
            $str .= $text;
        }
        $str .= '<br/></div></div>';

        return $str;
    }

    public function get_button($button, $i = 0) {
        $str = '<span class="' . $button . '_bt_container" id="' . $button . '_' . $i . '" style="display:none;"></span>';
        return $str;
    }

    public function Survey_blocked($sf_config) {
        global $mosConfig_live_site;
        ?>		
        <script language="JavaScript" src="/components/com_surveyforce/assets/js/bits_message.js" type="text/javascript"></script>

        <script language="JavaScript" src="/components/com_surveyforce/assets/js/pagination.js" type="text/javascript"></script>
        <script language="JavaScript" type="text/javascript">
            <!--//--><![CDATA[//><!--
                var mes_not_avail = '<?php echo JText::_('SURVEY_NOT_AVAIL') ?>';
            function sf_AddEvent(obj, evType, fn) {
                if (obj.addEventListener) {
                    obj.addEventListener(evType, fn, true);
                    return true;
                } else if (obj.attachEvent) {
                    var r = obj.attachEvent("on" + evType, fn);
                    return r;
                } else {
                    return false;
                }
            }
            function do_show_mes() {
                ShowMessage('error_messagebox', 1, mes_not_avail);
            }
            function sf_SetTimer() {
                timerID = setTimeout("do_show_mes()", 300);
            }
            sf_AddEvent(window, 'load', sf_SetTimer);
            //--><!]]>
        </script>

        <div class="componentheading"><?php echo JText::_('COMPONENT_HEADER') ?></div>
        <table id="survey_container_tbl" cellpadding="0" cellspacing="0" border="0" width="100%">
            <tr><td colspan="3" align="center">
                    <br />
                    <span id="error_messagebox" style="visibility:hidden;">Error text here</span>
                    <br /><br />
                </td></tr>
            <?php if ($sf_config->get('sf_show_dev_info', 1)) { ?>	
                <tr><td colspan="3" align="center" style="text-align:center "><div style="text-align:center;">Powered by <span title='JoomPlace'><a target='_blank' title='JoomPlace' href='http://www.joomplace.com/e-learning/surveyforce-deluxe.html'>Joomla component</a></span> SurveyForce Deluxe Software</div></td></tr>
            <?php } ?>
        </table>

        <?php
    }

    public function PreLoadSurvey($survey, $sf_config, $is_invited = 0, $invite_num = '', $rules = array(), $preview = 0) {
        global $mosConfig_live_site, $my;

        $template = surveyforce_template_class::SF_MainLayout();
        ?>
        <script language="JavaScript" src="/components/com_surveyforce/assets/js/bits_message.js" type="text/javascript"></script>

        <script language="JavaScript" src="/components/com_surveyforce/assets/js/pagination.js" type="text/javascript"></script>
        <script language="JavaScript" type="text/javascript">
        <!--//--><![CDATA[//><!--

        <?php
        echo 'var rules = new Array();' . "\n";
        if (is_array($rules) && count($rules) > 0) {
            foreach ($rules as $nn => $rule) {
                echo "rules[" . $nn . "] = new Array('" . $rule->quest_id . "', '" . $rule->quest_id_a . "', '" . $rule->answer . "', '" . $rule->ans_field . "');\n";
            }
        }
        ?>
            var invited_url = '<?php echo ($is_invited == 1) ? "&invite=$invite_num" : "" ?>';
            var start_id = 0;

            jQuery(document).ready(function() {

                if ((jQuery(window).width() >= '768') && (jQuery(window).width() <= '1024')) {
                    jQuery('#surveyforce').removeClass('compact').removeClass('narrow').addClass('normal');
                }
                ;

                if ((jQuery(window).width() >= '480') && (jQuery(window).width() <= '768')) {
                    jQuery('#surveyforce').removeClass('normal').removeClass('narrow').addClass('compact');
                }
                ;

                if (jQuery(window).width() <= '480') {
                    jQuery('#surveyforce').removeClass('compact').removeClass('normal').addClass('narrow');
                }
                ;

               /* if (jQuery(window).width() <= '480') {
                    regexp = /&tmpl=component/
                    sf_uri = window.location.href;
                    if (!regexp.test(sf_uri)) {
        <?php if (!$is_invited && !$preview) { ?>
                            window.location.href = "<?php echo JURI::root() ?>index.php?option=com_surveyforce&survey=<?php echo $survey->id ?>&tmpl=component";
        <?php } else { ?>
                            window.location.href = sf_uri + "&tmpl=component";
        <?php } ?>
                    }
                }*/

                jQuery(window).resize(function() {
                    if ((jQuery(window).width() >= '768') && (jQuery(window).width() <= '1024')) {
                        jQuery('#surveyforce').removeClass('compact').removeClass('narrow').addClass('normal');
                    }
                    ;

                    if ((jQuery(window).width() >= '480') && (jQuery(window).width() <= '768')) {
                        jQuery('#surveyforce').removeClass('normal').removeClass('narrow').addClass('compact');
                    }
                    ;

                    if ((jQuery(window).width() >= '320') && (jQuery(window).width() <= '480')) {
                        jQuery('#surveyforce').removeClass('compact').removeClass('normal').addClass('narrow');
                    }
                    ;

                });
            });

            function preload(arrayOfImages) {
                jQuery(arrayOfImages).each(function() {
                    jQuery('<img/>')[0].src = this;
                });
            }

            function ScrollToElement(theElement) {

                var selectedPosX = 0;
                var selectedPosY = 0;

                while (theElement != null) {
                    try {
                        selectedPosX += theElement.offsetLeft;
                        selectedPosY += theElement.offsetTop;
                        theElement = theElement.offsetParent;
                    } catch (e) {
                    }
                }
                try {
                    window.scrollTo(selectedPosX, selectedPosY);
                } catch (e) {
                }

            }

            function check_num_opt(n, elem) {
                if (questions[n].cur_quest_type == 3) {
                    var sf_num_options = 0;
                    try {
                        sf_num_options = parseInt(questions[n].response.getElementsByTagName('sf_num_options')[0].firstChild.data)

                        if (sf_num_options > 0)
                            sf_num_options++;

                    } catch (e) {
                        sf_num_options = 0;
                    }

                    if (sf_num_options > 0) {
                        var selItem = eval('document.quest_form' + questions[n].cur_quest_id + '.quest_check' + questions[n].cur_quest_id);
                        var checked_item = 0;
                        if (selItem) {
                            if (selItem.length) {
                                var i;
                                for (i = 0; i < selItem.length; i++) {
                                    if (selItem[i].checked) {
                                        checked_item++;
                                    }
                                }
                            }
                        }

                        if (checked_item >= sf_num_options) {
                            ShowMessage('error_messagebox', 1, '<?php echo JText::_('CANNOT_CHECK_ANYMORE'); ?>');
                            elem.checked = false;
                            return false;
                        }
                    }
                }
                ShowMessage('error_messagebox', 1, '&nbsp;');
                return true;
            }

            function jq_in_array(needle, haystack, argStrict) {
                var key = '', strict = !!argStrict;
                if (strict) {
                    for (key in haystack) {
                        if (haystack[key] === needle) {
                            return true;
                        }
                    }
                } else {
                    for (key in haystack) {
                        if (haystack[key] == needle) {
                            return true;
                        }
                    }
                }
                return false;
            }

            function getQNumberByID(qid) {
                for (n = 0; n < quest_count; n++) {
                    if (qid == questions[n].cur_quest_id) {
                        return n;
                    }
                }
                return NULL;
            }

            //RULES CODE
            function check_answer(n) {

                if (rules.length > 0) {
                    var len = rules.length;
                    var jj;
                    var done_questions = new Array();
                    var dq_i = 0;
                    for (jj = 0; jj < len; jj++) {
                        if (rules[jj][1] == questions[n].cur_quest_id) {
                            if (jq_in_array(rules[jj][0], done_questions))
                                continue;
                            switch (questions[n].cur_quest_type) {
                                case '1':
                                    var mcount = questions[n].response.getElementsByTagName('main_fields_count')[0].firstChild.data;
                                    var i;
                                    var id_ans;
                                    for (i = 0; i < mcount; i++) {
                                        id_ans = sf_Check_selectRadio('quest_radio_' + questions[n].cur_quest_id + '_' + questions[n].main_ids_array[i], 'quest_form' + questions[n].cur_quest_id);
                                        if (id_ans && rules[jj][2] == questions[n].main_ids_array[i] && rules[jj][3] == id_ans) {
                                            var div = sf_getObj('quest_div' + rules[jj][0]);
                                            if (div != null) {
                                                div.style.display = 'none';
                                                sf_getObj('a_quest_div' + rules[jj][0]).style.display = 'none';
                                                sf_getObj('is_quest_div' + rules[jj][0]).style.display = 'none';
                                                sf_getObj('dl_quest_div' + rules[jj][0]).style.display = 'none';
                                                done_questions[dq_i] = rules[jj][0];
                                                dq_i++;
                                                continue;
                                            }
                                        }
                                        else if (rules[jj][2] == questions[n].main_ids_array[i] && rules[jj][3]) {
                                            var div = sf_getObj('quest_div' + rules[jj][0]);
                                            if (div != null) {
                                                div.style.display = '';
                                                sf_getObj('a_quest_div' + rules[jj][0]).style.display = '';
                                                sf_getObj('is_quest_div' + rules[jj][0]).style.display = '';
                                                sf_getObj('dl_quest_div' + rules[jj][0]).style.display = '';
                                            }
                                        }
                                    }
                                    break;
                                case '2':
                                    var acount = questions[n].response.getElementsByTagName('alt_fields_count')[0].firstChild.data;
                                    var quest_style = questions[n].response.getElementsByTagName('sf_qstyle')[0].firstChild.data;
                                    if (quest_style == 1) {
                                        var answer = sf_getObj('quest_select_po_' + questions[n].cur_quest_id).value;
                                    }
                                    else {
                                        var answer = sf_Check_selectRadio('quest_radio' + questions[n].cur_quest_id, 'quest_form' + questions[n].cur_quest_id);
                                    }
                                    if (answer && rules[jj][2] == answer) {
                                        var div = sf_getObj('quest_div' + rules[jj][0]);
                                        if (div != null) {
                                            div.style.display = 'none';
                                            sf_getObj('a_quest_div' + rules[jj][0]).style.display = 'none';
                                            sf_getObj('is_quest_div' + rules[jj][0]).style.display = 'none';
                                            sf_getObj('dl_quest_div' + rules[jj][0]).style.display = 'none';
                                            done_questions[dq_i] = rules[jj][0];
                                            dq_i++;
                                            continue;
                                        }
                                    }
                                    else {
                                        var div = sf_getObj('quest_div' + rules[jj][0]);
                                        if (div != null) {
                                            div.style.display = '';
                                            sf_getObj('a_quest_div' + rules[jj][0]).style.display = '';
                                            sf_getObj('is_quest_div' + rules[jj][0]).style.display = '';
                                            sf_getObj('dl_quest_div' + rules[jj][0]).style.display = '';
                                        }
                                    }

                                    break;
                                case '3':

                                    var selItem = eval('document.quest_form' + questions[n].cur_quest_id + '.quest_check' + questions[n].cur_quest_id);
                                    var rrr = '';
                                    if (selItem) {
                                        if (selItem.length) {
                                            var i;
                                            var is_checked = false;
                                            for (i = 0; i < selItem.length; i++) {
                                                if (selItem[i].checked) {
                                                    is_checked = true;
                                                    if (selItem[i].value > 0) {
                                                        if (rules[jj][2] == selItem[i].value) {
                                                            var div = sf_getObj('quest_div' + rules[jj][0]);
                                                            if (div != null) {
                                                                div.style.display = 'none';
                                                                sf_getObj('a_quest_div' + rules[jj][0]).style.display = 'none';
                                                                sf_getObj('is_quest_div' + rules[jj][0]).style.display = 'none';
                                                                sf_getObj('dl_quest_div' + rules[jj][0]).style.display = 'none';
                                                                done_questions[dq_i] = rules[jj][0];
                                                                dq_i++;
                                                                continue;
                                                            }
                                                        }
                                                    }
                                                }
                                                else if (!jq_in_array(rules[jj][0], done_questions)) {
                                                    var div = sf_getObj('quest_div' + rules[jj][0]);
                                                    if (div != null) {
                                                        div.style.display = '';
                                                        sf_getObj('a_quest_div' + rules[jj][0]).style.display = '';
                                                        sf_getObj('is_quest_div' + rules[jj][0]).style.display = '';
                                                        sf_getObj('dl_quest_div' + rules[jj][0]).style.display = '';
                                                    }
                                                }
                                            }
                                            //should get back to initial state of "default hidden" questions
                                            if (!is_checked) {
                                                var zn = getQNumberByID(rules[jj][0]);
                                                if (questions[zn].default_hided) {
                                                    var div = sf_getObj('quest_div' + rules[jj][0]);
                                                    if (div != null) {
                                                        div.style.display = 'none';
                                                        sf_getObj('a_quest_div' + rules[jj][0]).style.display = 'none';
                                                        sf_getObj('is_quest_div' + rules[jj][0]).style.display = 'none';
                                                        sf_getObj('dl_quest_div' + rules[jj][0]).style.display = 'none';
                                                    }
                                                }
                                            }

                                            rrr = rrr.substring(0, rrr.length - 6)
                                        } else {
                                            if (selItem.checked) {
                                                if (selItem.value > 0) {
                                                    if (rules[jj][2] == selItem.value) {
                                                        var div = sf_getObj('quest_div' + rules[jj][0]);
                                                        if (div != null) {
                                                            div.style.display = 'none';
                                                            sf_getObj('a_quest_div' + rules[jj][0]).style.display = 'none';
                                                            sf_getObj('is_quest_div' + rules[jj][0]).style.display = 'none';
                                                            sf_getObj('dl_quest_div' + rules[jj][0]).style.display = 'none';
                                                            done_questions[dq_i] = rules[jj][0];
                                                            dq_i++;
                                                            continue;
                                                        }
                                                    }
                                                }
                                            }
                                            else {
                                                var div = sf_getObj('quest_div' + rules[jj][0]);
                                                if (div != null) {
                                                    div.style.display = '';
                                                    sf_getObj('a_quest_div' + rules[jj][0]).style.display = '';
                                                    sf_getObj('is_quest_div' + rules[jj][0]).style.display = '';
                                                    sf_getObj('dl_quest_div' + rules[jj][0]).style.display = '';
                                                }
                                            }
                                        }
                                    }

                                    break
                                case '5':
                                    var mcount = questions[n].response.getElementsByTagName('main_fields_count')[0].firstChild.data;
                                    var i_id;
                                    var i_value;
                                    var i;
                                    var answer = '';
                                    var complete = true;
                                    for (i = 0; i < mcount; i++) {
                                        i_id = questions[n].response.getElementsByTagName('mfield_id')[i].firstChild.data;
                                        i_value = eval('document.quest_form' + questions[n].cur_quest_id + '.quest_select_' + questions[n].cur_quest_id + '_' + i_id).value;


                                        if (i_id && i_value && rules[jj][2] == i_id && rules[jj][3] == i_value) {
                                            var div = sf_getObj('quest_div' + rules[jj][0]);
                                            if (div != null) {
                                                div.style.display = 'none';
                                                sf_getObj('a_quest_div' + rules[jj][0]).style.display = 'none';
                                                sf_getObj('is_quest_div' + rules[jj][0]).style.display = 'none';
                                                sf_getObj('dl_quest_div' + rules[jj][0]).style.display = 'none';
                                                done_questions[dq_i] = rules[jj][0];
                                                dq_i++;
                                                continue;
                                            }
                                        }
                                        else {
                                            var div = sf_getObj('quest_div' + rules[jj][0]);
                                            if (div != null) {
                                                div.style.display = '';
                                                sf_getObj('a_quest_div' + rules[jj][0]).style.display = '';
                                                sf_getObj('is_quest_div' + rules[jj][0]).style.display = '';
                                                sf_getObj('dl_quest_div' + rules[jj][0]).style.display = '';
                                            }
                                        }
                                    }
                                    break;
                                case '6':
                                    var mcount = questions[n].response.getElementsByTagName('main_fields_count')[0].firstChild.data;
                                    var i_id;
                                    var i_value;
                                    var answer = '';
                                    var complete = true;
                                    var mas_ans = new Array(questions[n].kol_drag_elems);
                                    var i;
                                    for (i = 0; i < questions[n].kol_drag_elems; i++) {
                                        mas_ans[i] = 0;
                                        if ((questions[n].ids_in_cont[i] > 0) && (questions[n].ids_in_cont[i] <= questions[n].kol_drag_elems)) {
                                            if (questions[n].cont_for_ids[questions[n].ids_in_cont[i] - 1] == i + 1) {
                                                mas_ans[i] = questions[n].ids_in_cont[i];
                                                i_id = questions[n].response.getElementsByTagName('mfield_id')[i].firstChild.data;


                                                if (i_id && rules[jj][2] == i_id && rules[jj][3] == questions[n].answ_ids[questions[n].ids_in_cont[i] - 1]) {
                                                    var div = sf_getObj('quest_div' + rules[jj][0]);
                                                    if (div != null) {
                                                        div.style.display = 'none';
                                                        sf_getObj('a_quest_div' + rules[jj][0]).style.display = 'none';
                                                        sf_getObj('is_quest_div' + rules[jj][0]).style.display = 'none';
                                                        sf_getObj('dl_quest_div' + rules[jj][0]).style.display = 'none';
                                                        done_questions[dq_i] = rules[jj][0];
                                                        dq_i++;
                                                        continue;
                                                    }
                                                }
                                                else {
                                                    var div = sf_getObj('quest_div' + rules[jj][0]);
                                                    if (div != null) {
                                                        div.style.display = '';
                                                        sf_getObj('a_quest_div' + rules[jj][0]).style.display = '';
                                                        sf_getObj('is_quest_div' + rules[jj][0]).style.display = '';
                                                        sf_getObj('dl_quest_div' + rules[jj][0]).style.display = '';
                                                    }
                                                }
                                            }
                                            else {
                                                i_id = questions[n].response.getElementsByTagName('mfield_id')[i].firstChild.data;

                                                if (i_id && rules[jj][2] == i_id && rules[jj][3] == 0) {
                                                    var div = sf_getObj('quest_div' + rules[jj][0]);
                                                    if (div != null) {
                                                        div.style.display = 'none';
                                                        sf_getObj('a_quest_div' + rules[jj][0]).style.display = 'none';
                                                        sf_getObj('is_quest_div' + rules[jj][0]).style.display = 'none';
                                                        sf_getObj('dl_quest_div' + rules[jj][0]).style.display = 'none';
                                                        done_questions[dq_i] = rules[jj][0];
                                                        dq_i++;
                                                        continue;
                                                    }
                                                }
                                                else {
                                                    var div = sf_getObj('quest_div' + rules[jj][0]);
                                                    if (div != null) {
                                                        div.style.display = '';
                                                        sf_getObj('a_quest_div' + rules[jj][0]).style.display = '';
                                                        sf_getObj('is_quest_div' + rules[jj][0]).style.display = '';
                                                        sf_getObj('dl_quest_div' + rules[jj][0]).style.display = '';
                                                    }
                                                }
                                            }
                                        }
                                        else {
                                            i_id = questions[n].response.getElementsByTagName('mfield_id')[i].firstChild.data;
                                            if (i_id && rules[jj][2] == i_id && rules[jj][3] == 0) {
                                                var div = sf_getObj('quest_div' + rules[jj][0]);
                                                if (div != null) {
                                                    div.style.display = 'none';
                                                    sf_getObj('a_quest_div' + rules[jj][0]).style.display = 'none';
                                                    sf_getObj('is_quest_div' + rules[jj][0]).style.display = 'none';
                                                    sf_getObj('dl_quest_div' + rules[jj][0]).style.display = 'none';
                                                    done_questions[dq_i] = rules[jj][0];
                                                    dq_i++;
                                                    continue;
                                                }
                                            }
                                            else {
                                                var div = sf_getObj('quest_div' + rules[jj][0]);
                                                if (div != null) {
                                                    div.style.display = '';
                                                    sf_getObj('a_quest_div' + rules[jj][0]).style.display = '';
                                                    sf_getObj('is_quest_div' + rules[jj][0]).style.display = '';
                                                    sf_getObj('dl_quest_div' + rules[jj][0]).style.display = '';
                                                }
                                            }
                                        }
                                    }
                                    break;
                                case '9':
                                    var mcount = questions[n].response.getElementsByTagName('main_fields_count')[0].firstChild.data;
                                    var acount = questions[n].response.getElementsByTagName('alt_fields_count')[0].firstChild.data;

                                    var mfield_type = 0;
                                    var i_id;
                                    var i_value;
                                    var i;
                                    var answer = '';
                                    var complete = true;
                                    var r = 0;
                                    for (i = 0; i < mcount; i++) {
                                        mfield_type = questions[n].response.getElementsByTagName('mfield_is_true')[i].firstChild.data;
                                        i_id = questions[n].response.getElementsByTagName('mfield_id')[i].firstChild.data;
                                        i_value = parseInt(eval('document.quest_form' + questions[n].cur_quest_id + '.quest_select_' + questions[n].cur_quest_id + '_' + i_id).value);

                                        if (i_id && i_value && rules[jj][2] == i_id && rules[jj][3] == i_value) {
                                            var div = sf_getObj('quest_div' + rules[jj][0]);
                                            if (div != null) {
                                                div.style.display = 'none';
                                                sf_getObj('a_quest_div' + rules[jj][0]).style.display = 'none';
                                                sf_getObj('is_quest_div' + rules[jj][0]).style.display = 'none';
                                                sf_getObj('dl_quest_div' + rules[jj][0]).style.display = 'none';
                                                done_questions[dq_i] = rules[jj][0];
                                                dq_i++;
                                                continue;
                                            }
                                        }
                                        else {
                                            var div = sf_getObj('quest_div' + rules[jj][0]);
                                            if (div != null) {
                                                div.style.display = '';
                                                sf_getObj('a_quest_div' + rules[jj][0]).style.display = '';
                                                sf_getObj('is_quest_div' + rules[jj][0]).style.display = '';
                                                sf_getObj('dl_quest_div' + rules[jj][0]).style.display = '';
                                            }
                                        }
                                    }
                                    break;
                            }
                        }
                    }
                }

            }

            // *** DRAG'and'DROP CODE *** //
		var color_cont = '<?php echo JComponentHelper::getParams('com_surveyforce')->get('color_cont', '#666666') ?>';
		var color_drag = '<?php echo JComponentHelper::getParams('com_surveyforce')->get('color_drag', '#CCCCCC') ?>';
		var color_highlight = '<?php echo JComponentHelper::getParams('com_surveyforce')->get('color_highlight', '#EEEEEE') ?>';
            var last_drag_id = '';
            var last_drag_id_drag = '';
            var last_drag_quest_n = -1;

            function startDrag(e, n) {
                last_drag_quest_n = n;
                // determine event object
                if (!e) {
                    var e = window.event
                }
                ;
                // determine target element
                var targ = e.target ? e.target : e.srcElement;
                //break if not draggable element
                if (targ.id.substring(0, 4) != 'ddiv') {
                    return;
                }
                if (last_drag_id_drag != '') {
                    //break if target not last draggable div(div on mouse)
                    if (last_drag_id_drag != targ.id) {
                        return;
                    }
                }
                //Bring draggable div to top, other div's to back
                for (i = 1; i <= questions[n].kol_drag_elems; i++) {
                    an_div = sf_getObj('ddiv' + questions[n].cur_quest_id + '_' + i);
                    an_div.style.zIndex = 500;
                }
                targ.style.zIndex = 1000;
                //set some config options
                targ.style.position = 'relative';
                last_drag_id = targ.id;
                last_drag_id_drag = targ.id;
                // calculate event X,Y coordinates
                offsetX = e.clientX;
                offsetY = e.clientY;
                // assign default values for top and left properties
                if (!targ.style.left) {
                    targ.style.left = '0px'
                }
                ;
                if (!targ.style.top) {
                    targ.style.top = '0px'
                }
                ;
                // calculate integer values for top and left properties
                coordX = parseInt(targ.style.left);
                coordY = parseInt(targ.style.top);
                drag = true;
                questions[n].cont_index = 0;
                // move div element
                document.onmousemove = dragDiv;
            }
            // continue dragging
            function dragDiv(e) {
                var n = last_drag_quest_n;
                if (!drag) {
                    return
                }
                ;
                if (!e) {
                    var e = window.event
                }
                ;
                var targ = e.target ? e.target : e.srcElement;
                //set old coordinates to other div's (because it's position is relative and they 'prygayut'
                if (last_drag_id_drag != '') {
                    if (last_drag_id_drag != targ.id) {
                        var ddd = sf_getObj(last_drag_id_drag);
                        ddd.style.left = coordX + e.clientX - offsetX + 'px';
                        ddd.style.top = coordY + e.clientY - offsetY + 'px';
                        return;
                    }
                }
                if (targ.id.substring(0, 4) != 'ddiv') {
                    return;
                }
                // move div element
                targ.style.left = coordX + e.clientX - offsetX + 'px';
                targ.style.top = coordY + e.clientY - offsetY + 'px';
                var is_on_cont = false;
                for (i = 1; i <= questions[n].kol_drag_elems; i++) {
                    an_div = sf_getObj('cdiv' + questions[n].cur_quest_id + '_' + i);
                    FDIV_RightX = an_div.offsetLeft + an_div.offsetWidth;
                    SDIV_LeftX = targ.offsetLeft;//+coordX+e.clientX-offsetX;
                    FDIV_TopY = an_div.offsetTop;
                    FDIV_DownY = an_div.offsetTop + an_div.offsetHeight;
                    SDIV_MiddleY = targ.offsetTop + parseInt(targ.offsetHeight / 2);
                    if (((parseInt(FDIV_RightX) + 10) > (parseInt(SDIV_LeftX))) &&
                            ((parseInt(FDIV_DownY) + 10) > (parseInt(SDIV_MiddleY))) &&
                            ((parseInt(FDIV_TopY) + 10) < (parseInt(SDIV_MiddleY)))) {
                        an_div.style.backgroundColor = color_highlight;
                        an_div.className = 'jb_survey_dragdrop_left js_dragdrop_highlight';
                        questions[n].cont_index = i;
                        is_on_cont = true;
                    }
                    else {
                        an_div.style.backgroundColor = color_cont;
                        an_div.className = 'jb_survey_dragdrop_left';
                    }
                }
                var dr_number = parseInt(last_drag_id.substring(last_drag_id.lastIndexOf("_") + 1));
                //! id of the div is 'ddiv_xxx' - five simbols! plus number
                for (i = 1; i <= questions[n].kol_drag_elems; i++) {
                    if (i != dr_number) {
                        an_div = sf_getObj('ddiv' + questions[n].cur_quest_id + '_' + i);
                        if ((questions[n].coord_left[i]) && (questions[n].coord_left[i] != '')) {
                            an_div.style.left = questions[n].coord_left[i];
                        }
                        if ((questions[n].coord_top[i]) && (questions[n].coord_top[i] != '')) {
                            an_div.style.top = questions[n].coord_top[i];
                        }
                    }
                }
                if (!is_on_cont) {
                    questions[n].cont_index = 0;
                }
                return false;
            }
            // stop dragging
            function stopDrag() {
                var n = last_drag_quest_n;
                var dr_obj = sf_getObj(last_drag_id);
                if (n < 0) {
                    return;
                }
                if (dr_obj) {
                    var dr_number = parseInt(last_drag_id.substring(last_drag_id.indexOf('_') + 1));
                    //! id of the div is 'ddivxx_xxx' - ddiv plus quest_id plus '_' plus number
                    if (questions[n].cont_index) {
                        dr_obj.style.position = 'relative';
                        dr_obj.style.left = '-57px';
                        dr_obj.style.top = parseInt((questions[n].cont_index - 1) * 56 - (56 * (dr_number - 1)) + 7) + 'px';
                        questions[n].ids_in_cont[questions[n].cont_index - 1] = dr_number;
                    }

                    questions[n].cont_for_ids[dr_number - 1] = questions[n].cont_index;

                    questions[n].coord_left[dr_number] = dr_obj.style.left;
                    questions[n].coord_top[dr_number] = dr_obj.style.top;
                    dr_obj.style.zIndex = 499;
                }
                last_drag_id_drag = '';
                for (i = 1; i <= questions[n].kol_drag_elems; i++) {
                    an_div = sf_getObj('cdiv' + questions[n].cur_quest_id + '_' + i);
                    an_div.style.backgroundColor = color_cont;
                    an_div.className = 'jb_survey_dragdrop_left';
                }
                last_drag_quest_n = -1;
                drag = false;
                check_answer(n);
            }
            // *** end of DRAG'and'DROP CODE *** //
            var kol_main_elems = 0;
            var main_ids_array = new Array(kol_main_elems); //for likert quest
            // *** MESSAGES *** 
            var mes_enter_some_words = '<?php echo JText::_('COMPLETE_SHORT_ANSWER') ?>';
            var mes_select_one_radio = '<?php echo JText::_('COMPLETE_PICK_ONE') ?>';
            var mes_select_some_checks = '<?php echo JText::_('COMPLETE_PICK_MANY') ?>';
            var mes_select_your_choice = '<?php echo JText::_('COMPLETE_DROP_DOWN') ?>';
            var mes_select_your_rank = '<?php echo JText::_("COMPLETE_RANK") ?>';
            var mes_complete_this_part = '<?php echo JText::_('COMPLETE_LIKERT') ?>';
            var mes_complete_this_part_drag = '<?php echo JText::_('COMPLETE_DRAG_AND_DROP') ?>';
            var mes_complete_imp_scale = '<?php echo JText::_('COMPLETE_IMPORTANT_SCALE') ?>';

            var mes_loading = '<?php echo JText::_('SURVEY_LOAD_DATA') ?>';
            var mes_failed = '<?php echo JText::_('SURVEY_FAILED') ?>';
            var mes_invite_complete = '<?php echo JText::_('SURVEY_INVITED_COMPLETE') ?>';
            var mes_reg_complete = '<?php echo JText::_('SURVEY_REG_COMPLETE') ?>';
            var mes_pub_complete = '<?php echo JText::_('SURVEY_PUB_COMPLETE') ?>';
            var mes_please_wait = '<?php echo JText::_('SURVEY_PLEASE_WAIT') ?>';

            var mes_session_timed_out = '<?php echo JText::_('SESSION_TIMED_OUT') ?>';
            // *** some script variables ***
            var user_unique_id = '';
            var response;
            var survey_blocked = 0; // set block after each question (release after 2 seconds).
            var is_final_question = 0;
            var is_prev = 1;

        <?php
        $live_site = $GLOBALS['mosConfig_live_site'];
        if (substr($_SERVER['HTTP_HOST'], 0, 4) == 'www.') {
            if (strpos($GLOBALS['mosConfig_live_site'], 'www.') !== false)
                $live_site = $GLOBALS['mosConfig_live_site'];
            else {
                $live_site = str_replace(substr($_SERVER['HTTP_HOST'], 4), $_SERVER['HTTP_HOST'], $GLOBALS['mosConfig_live_site']);
            }
        } else {
            if (strpos($GLOBALS['mosConfig_live_site'], 'www.') !== false)
                $live_site = str_replace('www.' . $_SERVER['HTTP_HOST'], $_SERVER['HTTP_HOST'], $GLOBALS['mosConfig_live_site']);
            else
                $live_site = $GLOBALS['mosConfig_live_site'];
        }

        $live_site_parts = parse_url($live_site);

        $live_url = $live_site_parts['scheme'] . '://' . $live_site_parts['host'] . (isset($live_site_parts['port']) ? ':' . $live_site_parts['port'] : '') . (isset($live_site_parts['path']) ? $live_site_parts['path'] : '/');

        if (substr($live_url, strlen($live_url) - 1, 1) !== '/')
            $live_url .= '/';
        ?>

            var mosConfig_live_site = '<?php echo $live_url; ?>';

            var url_prefix = 'index.php?no_html=1&tmpl=component&option=com_surveyforce&task=ajax_action<?php echo ($preview ? '&preview=1' : '') ?>';

            var quest_count = 1;
            var questions = new Array(quest_count);

            function question_data()
            {
                // *** DRAG'and'DROP CODE *** //
                this.kol_drag_elems = 0;
                this.drag_array = new Array(this.kol_drag_elems);
                this.coord_left = new Array(this.kol_drag_elems);
                this.coord_top = new Array(this.kol_drag_elems);
                this.ids_in_cont = new Array(this.kol_drag_elems); // what div id's in containers
                this.cont_for_ids = new Array(this.kol_drag_elems); //in that container this id
                this.answ_ids = new Array(this.kol_drag_elems);
                this.cont_index = 0;
                // *** end of DRAG'and'DROP CODE *** //
                this.kol_main_elems = 0;
                this.main_ids_array = new Array(kol_main_elems);

                this.cur_quest_type = '';
                this.cur_quest_id = 0;
                this.cur_impscale_ex = 0;
                this.compulsory = 1;
                this.default_hided = 0;

                this.cur_quest_text = '';
                this.div_id = '';
                this.response = null;
            }


            function sf_MakeRequest(url) {
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
                sf_getObj('survey_container').innerHTML = '';
                survey_blocked == 1;
                http_request.onreadystatechange = function() {
                    sf_AnalizeRequest(http_request);
                };

        <?php if (isset($_REQUEST['sf_debug_gets'])) { ?>

                    http_request.open('GET', mosConfig_live_site + url_prefix + '&' + url, true);
                    http_request.send(null);

        <?php } else { ?>

                    var post_target = mosConfig_live_site + url_prefix;
                    http_request.open("POST", post_target, true);
                    http_request.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
                    http_request.setRequestHeader("Content-length", url.length);
                    http_request.setRequestHeader("Connection", "close");
                    http_request.send(url);

        <?php } ?>

            }

            function sf_GetQuestionData(question, n) {
                questions[n].cur_quest_text = question.getElementsByTagName('quest_text')[0].firstChild.data
                questions[n].cur_quest_type = question.getElementsByTagName('quest_type')[0].firstChild.data;
                questions[n].cur_quest_id = question.getElementsByTagName('quest_id')[0].firstChild.data;
                questions[n].compulsory = question.getElementsByTagName('compulsory')[0].firstChild.data;
                questions[n].default_hided = question.getElementsByTagName('default_hided')[0].firstChild.data;
                questions[n].div_id = 'quest_div' + questions[n].cur_quest_id;
                questions[n].response = question;
            }

            function sf_CreateQuestions() {
                var html_data;
                var i = 0;

                sf_getObj('survey_container').innerHTML = '';
                for (i = 0; i < quest_count; i++) {
                    questions[i] = new question_data();
                    sf_GetQuestionData(response.getElementsByTagName('question_data')[i], i);
                    html_data = sf_GetQuestionHtml(questions[i].cur_quest_type, i);

                    var question_template = getQuestionTemplate();
                    var hided = (questions[i].default_hided == '1' ? ' style="display:none;" ' : '');

                    question_template = question_template.replace(/\{QUESTION_TEXT\}/, '<div id="' + questions[i].div_id + '" ' + hided + '>' + questions[i].cur_quest_text + '</div>');
                    question_template = question_template.replace(/\{ANSWERS\}/, '<div id="a_' + questions[i].div_id + '" ' + hided + '>' + html_data[0] + '</div>');
                    question_template = question_template.replace(/\{IMPORTANCE_SCALE\}/, '<div id="is_' + questions[i].div_id + '" ' + hided + '>' + html_data[1] + '</div>');

                    var div_inside = document.createElement("div");
                    div_inside.innerHTML = question_template + (quest_count > 1 ? '<div id="dl_' + questions[i].div_id + '" ' + hided + '>' + getQuestionDelimeter() + '</div>' : '');
                    sf_getObj('survey_container').appendChild(div_inside);
                    if (questions[i].cur_quest_type == 5 || questions[i].cur_quest_type == 9) {
                        if (window.getChoosenSelect)
                            getChoosenSelect();
                    }
                }
            }

            function sf_AnalizeRequest(http_request) {
                var finish_count = <?php echo substr_count($template, '{FINISH_BUTTON}'); ?>;
                var next_count = <?php echo substr_count($template, '{NEXT_BUTTON}'); ?>;

                if (http_request.readyState == 4) {
                    if ((http_request.status == 200)) {
                        response = http_request.responseXML.documentElement;

                        var task = response.getElementsByTagName('task')[0].firstChild.data;
                        quest_count = response.getElementsByTagName('quest_count')[0].firstChild.data;
                        questions = new Array(quest_count);
                        ShowMessage('error_messagebox', 0, '');
                        ShowMessage('error_messagebox2', 0, '');
                        is_final_question = response.getElementsByTagName('is_final_question')[0].firstChild.data;

                        switch (task) {
                            case 'start':
                                is_prev = response.getElementsByTagName('is_prev')[0].firstChild.data;
                                survey_blocked = 1;
                                setTimeout("sf_releaseBlock()", 1000);
                                user_unique_id = response.getElementsByTagName('user_id')[0].firstChild.data;
                                start_id = response.getElementsByTagName('start_id')[0].firstChild.data;
                                sf_CreateQuestions();
                                sf_getObj("progress").style.display = "<?php echo ($survey->sf_progressbar == 1 ? '' : 'none'); ?>";//"none"
                                sf_getObj('progress_bar').style.width = response.getElementsByTagName('progress_bar')[0].firstChild.data;
                                sf_getObj('progress_bar_txt').innerHTML = response.getElementsByTagName('progress_bar_txt')[0].firstChild.data;
                                if (response.getElementsByTagName('is_resume')[0].firstChild.data == 0)
                                    sf_UpdateTaskDiv('start');
                                else
                                    sf_UpdateTaskDiv('next');
                                break;
                            case 'prev0':
                            case 'prev':
                                is_prev = response.getElementsByTagName('is_prev')[0].firstChild.data;
                                survey_blocked = 1;
                                setTimeout("sf_releaseBlock()", 1000);
                                sf_CreateQuestions();
                                var i = 0;
                                for (i = 0; i < quest_count; i++) {
                                    if (questions[i].cur_quest_type == 6) {
                                        setDrnDnAnswers(i);
                                    }
                                }
                                sf_getObj('progress_bar').style.width = response.getElementsByTagName('progress_bar')[0].firstChild.data;
                                sf_getObj('progress_bar_txt').innerHTML = response.getElementsByTagName('progress_bar_txt')[0].firstChild.data;
                                if (task == 'prev')
                                    sf_UpdateTaskDiv('next');
                                else
                                    sf_UpdateTaskDiv('start');
                                break;
                            case 'start_last_question':
                            case 'last_question':
                                is_prev = response.getElementsByTagName('is_prev')[0].firstChild.data;
                                sf_getObj("progress").style.display = "<?php echo ($survey->sf_progressbar == 1 ? '' : 'none'); ?>";//"none"		
                                try {
                                    user_unique_id = response.getElementsByTagName('user_id')[0].firstChild.data;
                                    start_id = response.getElementsByTagName('start_id')[0].firstChild.data;
                                } catch (e) {
                                }
                            case 'next':
                                is_prev = response.getElementsByTagName('is_prev')[0].firstChild.data;
                                survey_blocked = 1;
                                setTimeout("sf_releaseBlock()", 1000);
                                sf_CreateQuestions();
                                var i = 0;
                                for (i = 0; i < quest_count; i++) {
                                    if (questions[i].cur_quest_type == 6) {
                                        setDrnDnAnswers(i);
                                    }
                                }
                                sf_getObj('progress_bar').style.width = response.getElementsByTagName('progress_bar')[0].firstChild.data;
                                sf_getObj('progress_bar_txt').innerHTML = response.getElementsByTagName('progress_bar_txt')[0].firstChild.data;
                                sf_UpdateTaskDiv(task);
                                break;
                            case 'finish':
                                is_prev = response.getElementsByTagName('is_prev')[0].firstChild.data;
                                fpage_type = response.getElementsByTagName('fpage_type')[0].firstChild.data;
                                fpage_text = response.getElementsByTagName('fpage_text')[0].firstChild.data;
                                redirect_enable = response.getElementsByTagName('fpage_redirect_enable')[0].firstChild.data;
                                sf_getObj("progress").style.display = "none";
                                if (fpage_type == 1)
                                    sf_getObj('survey_container_tbl').style.background = '';
                                if (parseInt(redirect_enable)) {
                                    fpage_redirect_url = response.getElementsByTagName('fpage_redirect_url')[0].firstChild.data;
                                    fpage_redirect_delay = response.getElementsByTagName('fpage_redirect_delay')[0].firstChild.data;
                                    if (fpage_redirect_url)
                                        setTimeout("SF_do_redirect('" + fpage_redirect_url + "')", fpage_redirect_delay * 1000);
                                }
                                sf_getObj('survey_container').innerHTML = fpage_text;
                                sf_UpdateTaskDiv('finish');
                                break;

                            case 'invite_complete':
        <?php if ($survey->sf_after_start) { ?>
                                    fpage_type = response.getElementsByTagName('fpage_type')[0].firstChild.data;
                                    fpage_text = response.getElementsByTagName('fpage_text')[0].firstChild.data;
                                    sf_getObj("progress").style.display = "none";
                                    if (fpage_type == 1)
                                        sf_getObj('survey_container_tbl').style.background = '';
                                    sf_getObj('survey_container').innerHTML = fpage_text;
                                    sf_UpdateTaskDiv('finish');
        <?php } ?>
                                ShowMessage('error_messagebox', 1, mes_invite_complete);
                                survey_blocked = 1;
                                setTimeout("sf_releaseBlock()", 1000);
                                break;

                            case 'reg_complete':
        <?php if ($survey->sf_after_start) { ?>
                                    fpage_type = response.getElementsByTagName('fpage_type')[0].firstChild.data;
                                    fpage_text = response.getElementsByTagName('fpage_text')[0].firstChild.data;
                                    sf_getObj("progress").style.display = "none";
                                    if (fpage_type == 1)
                                        sf_getObj('survey_container_tbl').style.background = '';
                                    sf_getObj('survey_container').innerHTML = fpage_text;
                                    sf_UpdateTaskDiv('finish');
        <?php } ?>
                                ShowMessage('error_messagebox', 1, mes_reg_complete);
                                survey_blocked = 1;
                                setTimeout("sf_releaseBlock()", 1000);
                                break;

                            case 'pub_complete':
        <?php if ($survey->sf_after_start) { ?>
                                    fpage_type = response.getElementsByTagName('fpage_type')[0].firstChild.data;
                                    fpage_text = response.getElementsByTagName('fpage_text')[0].firstChild.data;
                                    sf_getObj("progress").style.display = "none";
                                    if (fpage_type == 1)
                                        sf_getObj('survey_container_tbl').style.background = '';
                                    sf_getObj('survey_container').innerHTML = fpage_text;
                                    sf_UpdateTaskDiv('finish');
        <?php } ?>
                                ShowMessage('error_messagebox', 1, mes_pub_complete);
                                survey_blocked = 1;
                                setTimeout("sf_releaseBlock()", 1000);
                                break;

                            case 'timed_out':
                                sf_getObj("progress").style.display = "none";
                                ShowMessage('error_messagebox', 1, mes_session_timed_out);
                                survey_blocked = 1;
                                setTimeout("sf_releaseBlock()", 1000);
                                break;

                            case 'failed':
                                sf_getObj("progress").style.display = "none";
                                ShowMessage('error_messagebox', 1, mes_failed);
                                survey_blocked = 1;
                                setTimeout("sf_releaseBlock()", 1000);
                                break;

                            default:
                                break;
                        }

                        if (parseInt(is_final_question)) {
                            for (i = 0; i < finish_count; i++) {
                                try {
                                    sf_getObj('finish_' + i).style.display = '';
                                    sf_getObj('finish_' + i).innerHTML = getButton('finish', '<?php echo JText::_('SURVEY_FINISH_SURVEY'); ?>', 'sf_SurveyFinishOn()');

                                } catch (e) {
                                }
                            }
                            for (i = 0; i < next_count; i++) {
                                try {
                                    sf_getObj('next_' + i).style.display = 'none';
                                } catch (e) {
                                }
                            }
                        }
                    } else {
                        ShowMessage('error_messagebox', 1, '<?php echo JText::_('SURVEY_FAILED_REQUEST') ?> (' + http_request.status + ')');
                    }
                }
            }

            function SF_do_redirect(redirect_url) {
                if (!redirect_url)
                    return false;
                redirect_url = redirect_url + '';
                if (redirect_url.indexOf('javascript:') === -1) {
                    window.location.href = redirect_url;
                } else {
                    redirect_url = redirect_url.replace("javascript:", "");
                    eval(redirect_url);
                }
                return true;
            }

            function sf_AddEvent(obj, evType, fn) {
                if (obj.addEventListener) {
                    obj.addEventListener(evType, fn, true);
                    return true;
                } else if (obj.attachEvent) {
                    var r = obj.attachEvent("on" + evType, fn);
                    return r;
                } else {
                    return false;
                }
            }
            function sf_SetTimer() {
                timerID = setTimeout("sf_InitAjax()", 300);
            }
            function sf_releaseBlock() {
                survey_blocked = 0;
            }
            function sf_InitFunc() {
                sf_getObj('survey_container').removeChild(sf_getObj('wait_div'));
                var div_inside1 = document.createElement("div");
                div_inside1.setAttribute("style", "padding:40px ");
                div_inside1.innerHTML = "<br\/>Load complete.";
                sf_getObj('survey_container').appendChild(div_inside1);
            }
            function sf_StartSurveyOn() {
                if (!survey_blocked) {
                    ShowMessage('error_messagebox', 1, mes_loading);
                    timerID = setTimeout("sf_StartSurvey()", 300);
                } else {
                    ShowMessage('error_messagebox', 1, mes_please_wait);
                }
            }
            function sf_StartSurvey() {
        <?php
        if (($is_invited && $survey->sf_inv_voting == 2 && $survey->is_complete == 1) ||
                ($my->id > 0 && $survey->sf_reg_voting == 2 && $survey->is_complete == 1) ||
                ($my->id > 0 && $survey->sf_friend_voting == 2 && $survey->is_complete == 1) ||
                ($my->id < 1 && $survey->sf_pub_voting == 2 && $survey->is_complete == 1)
        ) {
            ?>
                    if (!confirm("<?php echo JText::_('SF_ALREADY_COMPLETED_REG'); ?>")) {
                        ShowMessage('error_messagebox', 1, '');
                        window.history.go(-1);
                        return;
                    }
        <?php } ?>
                sf_MakeRequest('action=start&survey=<?php echo $survey->id ?>' + invited_url);
            }
            function sf_Check_selectRadio(rad_name, form_name) {
                var selItem = eval('document.' + form_name + '.' + rad_name);
                if (selItem) {
                    if (selItem.length) {
                        var i;
                        for (i = 0; i < selItem.length; i++) {
                            if (selItem[i].checked) {
                                if (selItem[i].value > 0) {
                                    return selItem[i].value;
                                }
                            }
                        }
                    }
                    else if (selItem.checked) {
                        return selItem.value;
                    }
                }
                return false;
            }
            function sf_Check_selectCheckbox(n) {
                var acount = questions[n].response.getElementsByTagName('alt_fields_count')[0].firstChild.data;
                var other_id = 0;
                if (acount > 0) {
                    other_id = questions[n].response.getElementsByTagName('afield_id')[0].firstChild.data;
                }
                var selItem = eval('document.quest_form' + questions[n].cur_quest_id + '.quest_check' + questions[n].cur_quest_id);
                var rrr = '';
                if (selItem) {
                    if (selItem.length) {
                        var i;
                        for (i = 0; i < selItem.length; i++) {
                            if (selItem[i].checked) {
                                if (selItem[i].value > 0) {
                                    if (selItem[i].value == other_id) {
                                        if (sf_getObj('other_op_' + questions[n].cur_quest_id).value != '') {
                                            rrr = rrr + selItem[i].value + '!!--!!' + sf_escape(sf_getObj('other_op_' + questions[n].cur_quest_id).value) + '!!,!! ';
                                        }
                                    }
                                    else {
                                        rrr = rrr + selItem[i].value + '!!,!! ';
                                    }
                                }
                            }
                        }
                        rrr = rrr.substring(0, rrr.length - 6)
                    } else if (selItem.checked) {
                        if (selItem.value == other_id) {
                            if (sf_getObj('other_op_' + questions[n].cur_quest_id).value != '') {
                                rrr = rrr + selItem.value + '!!--!!' + sf_getObj('other_op_' + questions[n].cur_quest_id).value;
                            }
                        }
                        else {
                            rrr = rrr + selItem.value;
                        }
                    }
                }
                return rrr;
            }

            function sf_SurveyFinishOn() {
                try {
                    ScrollToElement(sf_getObj('surveyforce_top'));
                } catch (e) {
                }
                var request_str = '';
                var tmp;
                var i;
                if (!survey_blocked) {
                    var no_error = true;
                    for (i = 0; i < quest_count; i++) {
                        if (sf_getObj('quest_div' + questions[i].cur_quest_id).style.display != 'none') {
                            tmp = sf_SurveyNextData(i);
                            if (tmp != false)
                                request_str = request_str + tmp;
                            else
                                no_error = false;
                        }
                    }
                    if (!no_error)
                        return false;
                    sf_UpdateTaskDiv('null');
                    sf_MakeRequest('action=next&survey=<?php echo $survey->id ?>' + invited_url + '&start_id=' + start_id + '&user_id=' + user_unique_id + '&finish=1' + request_str);
                } else {
                    ShowMessage('error_messagebox', 1, mes_please_wait);
                    ShowMessage('error_messagebox2', 1, '');
                }
            }

            function sf_SurveyNextOn() {
                try {
                    ScrollToElement(sf_getObj('surveyforce_top'));
                } catch (e) {
                }
                var request_str = '';
                var tmp;
                var i;
                if (!survey_blocked) {
                    ShowMessage('error_messagebox', 1, mes_loading);
                    ShowMessage('error_messagebox2', 1, '');
                    var no_error = true;
                    for (i = 0; i < quest_count; i++) {
                        if (sf_getObj('quest_div' + questions[i].cur_quest_id).style.display != 'none') {
                            tmp = sf_SurveyNextData(i);
                            if (tmp != false)
                                request_str = request_str + tmp;
                            else
                                no_error = false;
                        }
                    }
                    if (!no_error)
                        return false;
                    sf_UpdateTaskDiv('null');
                    sf_MakeRequest('action=next&survey=<?php echo $survey->id ?>' + invited_url + '&start_id=' + start_id + '&user_id=' + user_unique_id + request_str);
                } else {
                    ShowMessage('error_messagebox', 1, mes_please_wait);
                    ShowMessage('error_messagebox2', 1, '');
                }
            }

            function sf_escape(txt) {
                var text = txt;
        <?php if (!_JOOMLA15) { ?>
                    text = escape(txt);
                    if (text.indexOf('%u', 0) >= 0)
                        return encodeURIComponent(txt);
        <?php } ?>
                return text;
            }

            function sf_SurveyNextData(n) { //proveriaem vse li otmecheno i vozvraschaem stroku s otvetami
                if (survey_blocked) {
                    ShowMessage('error_messagebox', 1, mes_please_wait);
                    ShowMessage('error_messagebox2', 1, '');
                    return false;
                }

                var imp_scale_req = '';
                if (questions[n].cur_impscale_ex == 1) {
                    var imp_scale_choice = sf_Check_selectRadio('iscale_radio' + questions[n].cur_quest_id, 'iscale_form' + questions[n].cur_quest_id);
                    if (!imp_scale_choice && questions[n].compulsory == 1) {
                        ShowMessage('error_messagebox', 1, mes_complete_imp_scale);
                        ShowMessage('error_messagebox2', 1, mes_complete_imp_scale);
                        return false;
                    } else {
                        if (imp_scale_choice == "")
                            imp_scale_choice = 0;
                        imp_scale_req = '&is_imp_scale[]=1&imp_scale[]=' + imp_scale_choice;
                    }
                }

                switch (questions[n].cur_quest_type) {
                    case '1':
                        var mcount = questions[n].response.getElementsByTagName('main_fields_count')[0].firstChild.data;
                        var i;
                        var id_ans;
                        var answer = '';
                        var complete = true;
                        for (i = 0; i < mcount; i++) {
                            id_ans = sf_Check_selectRadio('quest_radio_' + questions[n].cur_quest_id + '_' + questions[n].main_ids_array[i], 'quest_form' + questions[n].cur_quest_id);
                            if (!id_ans) {
                                try {
                                    sf_getObj('qoption_' + questions[n].cur_quest_id + '_' + questions[n].main_ids_array[i]).className = 'ls_not_selected';
                                } catch (e) {
                                }
                                complete = false;
                                answer = answer + questions[n].main_ids_array[i] + '-' + 0 + ', ';
                            } else {
                                try {
                                    sf_getObj('qoption_' + questions[n].cur_quest_id + '_' + questions[n].main_ids_array[i]).className = '';
                                } catch (e) {
                                }
                                answer = answer + questions[n].main_ids_array[i] + '-' + id_ans + ', ';
                            }
                        }
                        if (!complete && questions[n].compulsory == 1) {
                            ShowMessage('error_messagebox', 1, mes_complete_this_part);
                            ShowMessage('error_messagebox2', 1, mes_complete_this_part);
                            return false;
                        } else {
                            answer = answer.substring(0, answer.length - 2);
                        }
                        break;
                    case '2':
                        var acount = questions[n].response.getElementsByTagName('alt_fields_count')[0].firstChild.data;
                        var quest_style = questions[n].response.getElementsByTagName('sf_qstyle')[0].firstChild.data;
                        if (quest_style == 1) {
                            var answer = sf_getObj('quest_select_po_' + questions[n].cur_quest_id).value;
                            if (answer == 0)
                                answer = false;
                        }
                        else {
                            var answer = sf_Check_selectRadio('quest_radio' + questions[n].cur_quest_id, 'quest_form' + questions[n].cur_quest_id);
                        }

                        if (acount > 0) {
                            if (answer == questions[n].response.getElementsByTagName('afield_id')[0].firstChild.data) {
                                answer = answer + '!!--!!' + sf_escape(sf_getObj('other_op_' + questions[n].cur_quest_id).value);
                                if (sf_getObj('other_op_' + questions[n].cur_quest_id).value == '')
                                    answer = false;
                            }
                        }
                        if (!answer && questions[n].compulsory == 1) {
                            try {
                                sf_getObj('quest_div' + questions[n].cur_quest_id).className = 'ls_not_selected';
                            } catch (e) {
                            }
                            ShowMessage('error_messagebox', 1, mes_select_one_radio);
                            ShowMessage('error_messagebox2', 1, mes_select_one_radio);
                            return false;
                        } else {
                            try {
                                sf_getObj('quest_div' + questions[n].cur_quest_id).className = '';
                            } catch (e) {
                            }
                        }
                        break;
                    case '3':
                        var answer = sf_Check_selectCheckbox(n);
                        if (answer == '' && questions[n].compulsory == 1) {
                            try {
                                sf_getObj('quest_div' + questions[n].cur_quest_id).className = 'ls_not_selected';
                            } catch (e) {
                            }
                            ShowMessage('error_messagebox', 1, mes_select_some_checks);
                            ShowMessage('error_messagebox2', 1, mes_select_some_checks);
                            return false;
                        } else {
                            try {
                                sf_getObj('quest_div' + questions[n].cur_quest_id).className = '';
                            } catch (e) {
                            }
                        }
                        break
                    case '4':
                        var answer = ''
                        var quest_inp_count = questions[n].response.getElementsByTagName('quest_inp_count')[0].firstChild.data;
                        if (quest_inp_count == 0) {
                            if (sf_getObj('inp_short' + questions[n].cur_quest_id)) {
                                answer = sf_escape(sf_getObj('inp_short' + questions[n].cur_quest_id).value);
                            }
                        }
                        else {
                            for (i = 0; i < quest_inp_count; i++) {
                                answer = answer + i + "!!--!!" + sf_escape(sf_getObj('short_ans_' + questions[n].cur_quest_id + '_' + i).value) + "!!,!! ";
                                if (questions[n].compulsory == 1 && sf_getObj('short_ans_' + questions[n].cur_quest_id + '_' + i).value == '') {
                                    try {
                                        sf_getObj('quest_div' + questions[n].cur_quest_id).className = 'ls_not_selected';
                                    } catch (e) {
                                    }
                                    ShowMessage('error_messagebox', 1, mes_enter_some_words);
                                    ShowMessage('error_messagebox2', 1, mes_enter_some_words);
                                    return false;
                                }
                            }
                            answer = answer.substring(0, answer.length - 6)
                        }
                        if (answer == '' && questions[n].compulsory == 1) {
                            try {
                                sf_getObj('quest_div' + questions[n].cur_quest_id).className = 'ls_not_selected';
                            } catch (e) {
                            }
                            ShowMessage('error_messagebox', 1, mes_enter_some_words);
                            ShowMessage('error_messagebox2', 1, mes_enter_some_words);
                            return false;
                        } else {
                            try {
                                sf_getObj('quest_div' + questions[n].cur_quest_id).className = '';
                            } catch (e) {
                            }
                        }
                        break;
                    case '5':
                        var mcount = questions[n].response.getElementsByTagName('main_fields_count')[0].firstChild.data;
                        var i_id;
                        var i_value;
                        var i;
                        var answer = '';
                        var complete = true;
                        for (i = 0; i < mcount; i++) {
                            i_id = questions[n].response.getElementsByTagName('mfield_id')[i].firstChild.data;
                            i_value = eval('document.quest_form' + questions[n].cur_quest_id + '.quest_select_' + questions[n].cur_quest_id + '_' + i_id).value;
                            answer = answer + i_id + '-' + i_value + ', ';
                            if ((i_value == 0) || (i_value == '0'))
                                complete = false;
                        }
                        if (!complete && questions[n].compulsory == 1) {
                            try {
                                sf_getObj('quest_div' + questions[n].cur_quest_id).className = 'ls_not_selected';
                            } catch (e) {
                            }
                            ShowMessage('error_messagebox', 1, mes_select_your_choice);
                            ShowMessage('error_messagebox2', 1, mes_select_your_choice);
                            return false;
                        } else {
                            answer = answer.substring(0, answer.length - 2);
                        }
                        break;
                    case '6':
                        var mcount = questions[n].response.getElementsByTagName('main_fields_count')[0].firstChild.data;
                        var i_id;
                        var i_value;
                        var answer = '';
                        var complete = true;
                        var mas_ans = new Array(questions[n].kol_drag_elems);
                        var i;
                        for (i = 0; i < questions[n].kol_drag_elems; i++) {
                            mas_ans[i] = 0;
                            if ((questions[n].ids_in_cont[i] > 0) && (questions[n].ids_in_cont[i] <= questions[n].kol_drag_elems)) {
                                if (questions[n].cont_for_ids[questions[n].ids_in_cont[i] - 1] == i + 1) {
                                    mas_ans[i] = questions[n].ids_in_cont[i];
                                    i_id = questions[n].response.getElementsByTagName('mfield_id')[i].firstChild.data;
                                    answer = answer + i_id + '-' + questions[n].answ_ids[questions[n].ids_in_cont[i] - 1] + ', ';
                                }
                                else {
                                    i_id = questions[n].response.getElementsByTagName('mfield_id')[i].firstChild.data;
                                    answer = answer + i_id + '-' + 0 + ', ';
                                    complete = false;
                                }
                            }
                            else {
                                i_id = questions[n].response.getElementsByTagName('mfield_id')[i].firstChild.data;
                                answer = answer + i_id + '-' + 0 + ', ';
                                complete = false;
                            }
                        }
                        if (!complete && questions[n].compulsory == 1) {
                            try {
                                sf_getObj('quest_div' + questions[n].cur_quest_id).className = 'ls_not_selected';
                            } catch (e) {
                            }
                            ShowMessage('error_messagebox', 1, mes_complete_this_part_drag);
                            ShowMessage('error_messagebox2', 1, mes_complete_this_part_drag);
                            return false;
                        } else {
                            answer = answer.substring(0, answer.length - 2);
                        }
                        break;
                    case '7':
                        imp_scale_req = '&is_imp_scale[]=0&imp_scale[]=0';
                        break;
                    case '9':
                        var mcount = questions[n].response.getElementsByTagName('main_fields_count')[0].firstChild.data;
                        var acount = questions[n].response.getElementsByTagName('alt_fields_count')[0].firstChild.data;

                        var mfield_type = 0;
                        var i_id;
                        var i_value;
                        var i;
                        var answer = '';
                        var complete = true;
                        var r = 0;
                        for (i = 0; i < mcount; i++) {
                            mfield_type = questions[n].response.getElementsByTagName('mfield_is_true')[i].firstChild.data;
                            i_id = questions[n].response.getElementsByTagName('mfield_id')[i].firstChild.data;
                            i_value = parseInt(eval('document.quest_form' + questions[n].cur_quest_id + '.quest_select_' + questions[n].cur_quest_id + '_' + i_id).value);
                            if (i_value != 0) {
                                if (mfield_type == 2 && sf_getObj('other_op_' + questions[n].cur_quest_id).value == '') {
                                    complete = false;
                                }

                                r++;

                                if (mfield_type == 2 && sf_getObj('other_op_' + questions[n].cur_quest_id).value != '') {
                                    answer = answer + i_id + '!!--!!' + i_value + '!!-,-!!' + sf_escape(sf_getObj('other_op_' + questions[n].cur_quest_id).value) + '!!,!! ';
                                }
                                else {
                                    answer = answer + i_id + '!!--!!' + i_value + '!!,!! ';
                                }
                            }
                        }
                        if (complete && r != mcount)
                            complete = false;
                        if (!complete && questions[n].compulsory == 1) {
                            try {
                                sf_getObj('quest_div' + questions[n].cur_quest_id).className = 'ls_not_selected';
                            } catch (e) {
                            }
                            ShowMessage('error_messagebox', 1, mes_select_your_rank);
                            ShowMessage('error_messagebox2', 1, mes_select_your_rank);
                            return false;
                        } else {
                            try {
                                sf_getObj('quest_div' + questions[n].cur_quest_id).className = '';
                            } catch (e) {
                            }
                            answer = answer.substring(0, answer.length - 6);
                        }
                        break;
                    default:
                        ShowMessage('error_messagebox', 1, '<?php echo JText::_('SURVEY_UNKNOWN_ERROR') ?>');
                        ShowMessage('error_messagebox2', 1, '<?php echo JText::_('SURVEY_UNKNOWN_ERROR') ?>');
                        return false;
                        break;
                }
                return '&quest_id[]=' + questions[n].cur_quest_id + '&answer[]=' + answer + imp_scale_req;
            }

            function sf_SurveyPrevOn() {
                window.scroll(0, 0);
                if (!survey_blocked) {
                    ShowMessage('error_messagebox', 1, mes_loading);
                    timerID = setTimeout("sf_SurveyPrev()", 300);
                } else {
                    ShowMessage('error_messagebox', 1, mes_please_wait);
                }
            }

            function sf_SurveyPrev() { //send 'TASK = prev'
                var request_str = '';
                var i;
                for (i = 0; i < quest_count; i++) {
                    request_str = request_str + '&quest_id[]=' + questions[i].cur_quest_id;
                }
                ShowMessage('error_messagebox2', 1, '');
                sf_UpdateTaskDiv('null');
                sf_MakeRequest('action=prev&survey=<?php echo $survey->id ?>' + invited_url + '&start_id=' + start_id + '&user_id=' + user_unique_id + request_str);
            }

            function sf_UpdateTaskDiv(task) {

                var start_count = <?php echo substr_count($template, '{START_BUTTON}'); ?>;
                var finish_count = <?php echo substr_count($template, '{FINISH_BUTTON}'); ?>;
                var prev_count = <?php echo substr_count($template, '{PREV_BUTTON}'); ?>;
                var next_count = <?php echo substr_count($template, '{NEXT_BUTTON}'); ?>;
                var i = 0;

                try {
                    for (i = 0; i < start_count; i++) {
                        sf_getObj('start_' + i).style.display = 'none';
                    }
                    for (i = 0; i < finish_count; i++) {
                        sf_getObj('finish_' + i).style.display = 'none';
                    }
                } catch (e) {
                }

                switch (task) {
                    case 'start':
                        for (i = 0; i < next_count; i++) {
                            try {
                                if (!new_template) {
                                    sf_getObj('next_' + i).style.display = '';
                                    sf_getObj('next_' + i).innerHTML = getButton('next', '<?php echo JText::_('SURVEY_NEXT_QUEST') ?>', 'sf_SurveyNextOn()');
                                } else {
                                    sf_getObj('next_' + i).style.display = '';
                                    sf_getObj('next_' + i).innerHTML = getButton('next', '<?php echo JText::_('SURVEY_NEXT_QUEST') ?>' + " &#9658;", 'sf_SurveyNextOn()');
                                }
                            } catch (e) {
                            }
                        }
                        break;
                    case 'next':
                        for (i = 0; i < next_count; i++) {
                            try {
                                if (!new_template) {
                                    sf_getObj('next_' + i).style.display = '';
                                    sf_getObj('next_' + i).innerHTML = getButton('next', '<?php echo JText::_('SURVEY_NEXT_QUEST') ?>', 'sf_SurveyNextOn()');
                                } else {
                                    sf_getObj('next_' + i).style.display = '';
                                    sf_getObj('next_' + i).innerHTML = getButton('next', '<?php echo JText::_('SURVEY_NEXT_QUEST') ?>' + " &#9658;", 'sf_SurveyNextOn()');
                                }
                            } catch (e) {
                            }
                        }

                        if (parseInt(is_prev)) {
                            for (i = 0; i < prev_count; i++) {
                                try {
                                    if (!new_template) {
                                        sf_getObj('prev_' + i).style.display = '';
                                        sf_getObj('prev_' + i).innerHTML = getButton('prev', '<?php echo JText::_('SURVEY_PREV_QUEST') ?>', 'sf_SurveyPrevOn()');
                                    } else {
                                        sf_getObj('prev_' + i).style.display = '';
                                        sf_getObj('prev_' + i).innerHTML = getButton('prev', "&#9668; " + '<?php echo JText::_('SURVEY_PREV_QUEST') ?>', 'sf_SurveyPrevOn()');
                                    }
                                } catch (e) {
                                }
                            }
                        }
                        break;
                    case 'start_last_question':
                        for (i = 0; i < next_count; i++) {
                            try {
                                sf_getObj('next_' + i).style.display = 'none';
                            } catch (e) {
                            }
                        }

                        for (i = 0; i < finish_count; i++) {
                            try {
                                sf_getObj('finish_' + i).style.display = '';
                                sf_getObj('finish_' + i).innerHTML = getButton('finish', '<?php echo JText::_('SURVEY_SUBMIT_SURVEY') ?>', 'sf_SurveyNextOn()');
                            } catch (e) {
                            }
                        }

                        if (parseInt(is_prev)) {
                            for (i = 0; i < prev_count; i++) {
                                try {
                                    sf_getObj('prev_' + i).style.display = 'none';
                                } catch (e) {
                                }
                            }
                        }
                        break;
                    case 'last_question':
                        for (i = 0; i < next_count; i++) {
                            try {
                                sf_getObj('next_' + i).style.display = 'none';
                            } catch (e) {
                            }
                        }

                        for (i = 0; i < finish_count; i++) {
                            try {
                                sf_getObj('finish_' + i).style.display = '';
                                sf_getObj('finish_' + i).innerHTML = getButton('finish', '<?php echo JText::_('SURVEY_SUBMIT_SURVEY') ?>', 'sf_SurveyNextOn()');
                            } catch (e) {
                            }
                        }

                        if (parseInt(is_prev)) {
                            for (i = 0; i < prev_count; i++) {
                                try {
                                    if (!new_template) {
                                        sf_getObj('prev_' + i).style.display = '';
                                        sf_getObj('prev_' + i).innerHTML = getButton('prev', '<?php echo JText::_('SURVEY_PREV_QUEST') ?>', 'sf_SurveyPrevOn()');
                                    } else {
                                        sf_getObj('prev_' + i).style.display = '';
                                        sf_getObj('prev_' + i).innerHTML = getButton('prev', "&#9668; " + '<?php echo JText::_('SURVEY_PREV_QUEST') ?>', 'sf_SurveyPrevOn()');
                                    }
                                } catch (e) {
                                }
                            }
                        }
                        break;
                    case 'finish':
                        try {

                            for (i = 0; i < finish_count; i++) {
                                sf_getObj('finish_' + i).style.display = 'none';
                            }

                            if (parseInt(is_prev)) {
                                for (i = 0; i < prev_count; i++) {
                                    sf_getObj('prev_' + i).style.display = 'none';
                                }
                            }

                            for (i = 0; i < next_count; i++) {
                                sf_getObj('next_' + i).style.display = 'none';
                            }

                            for (i = 0; i < start_count; i++) {
                                sf_getObj('start_' + i).style.display = 'none';
                            }

                        } catch (e) {
                        }
                        break;
                    case 'null':
                        try {
                            for (i = 0; i < finish_count; i++) {
                                sf_getObj('finish_' + i).style.display = 'none';
                            }

                            for (i = 0; i < prev_count; i++) {
                                sf_getObj('prev_' + i).style.display = 'none';
                            }

                            for (i = 0; i < next_count; i++) {
                                sf_getObj('next_' + i).style.display = 'none';
                            }

                            for (i = 0; i < start_count; i++) {
                                sf_getObj('start_' + i).style.display = 'none';
                            }

                        } catch (e) {
                        }
                        break;
                    default:
                        break;
                }
            }

            function setDrnDnAnswers(n) {
                var ans_count = questions[n].response.getElementsByTagName('ans_count')[0].firstChild.data;
                var i = 0;
                var j = 0;
                if (ans_count > 0) {
                    var mfield_id = 0;
                    var ans_id = 0;
                    var div_n = 0;
                    for (i = 1; i <= questions[n].kol_drag_elems; i++) {
                        mfield_id = questions[n].response.getElementsByTagName('mfield_id')[i - 1].firstChild.data;
                        ans_id = 0;
                        for (j = 0; j < ans_count; j++) {
                            if (questions[n].response.getElementsByTagName('a_quest_id')[j].firstChild.data == mfield_id)
                                ans_id = questions[n].response.getElementsByTagName('ans_id')[j].firstChild.data;
                        }

                        div_n = 0;
                        for (j = 0; j < ans_count; j++) {
                            if (ans_id == questions[n].response.getElementsByTagName('afield_id')[j].firstChild.data)
                                div_n = j + 1;
                        }
                        if (div_n > 0) {
                            an_div = sf_getObj('cdiv' + questions[n].cur_quest_id + '_' + i);
                            targ = sf_getObj('ddiv' + questions[n].cur_quest_id + '_' + div_n);
                            targ.style.left = parseInt((targ.offsetLeft - an_div.offsetLeft) / -2) + 'px';
                            targ.style.top = parseInt((an_div.offsetLeft - targ.offsetLeft) + 10) + 'px';
                            last_drag_id = 'ddiv' + questions[n].cur_quest_id + '_' + div_n;
                            last_drag_quest_n = n;
                            questions[n].cont_index = i;
                            stopDrag();
                        }
                    }
                }
            }

            function removeSameRank(e, n) {
                var targ = e;
                if (targ.id.substring(0, 12) != 'quest_select') {
                    return;
                }
                var cur = targ.value;
                var mcount = questions[n].response.getElementsByTagName('main_fields_count')[0].firstChild.data;
                var sel = null;
                for (i = 0; i < mcount; i++) {
                    sel = sf_getObj("quest_select_" + questions[n].cur_quest_id + "_" + questions[n].response.getElementsByTagName('mfield_id')[i].firstChild.data);
                    if (sel.id != targ.id && sel.value == cur)
                        sel.value = 0;
                }
                check_answer(n);
            }

            function sf_GetQuestionHtml(qtype, n) {
                var quest_html = '';
                switch (qtype) {
                    case '1': //LIKERT SCALE			
                        quest_html = quest_html + getLikertScale(n);
                        break;
                    case '2': // PICK ONE			
                        quest_html = quest_html + getPickOne(n);
                        break;
                    case '3': // PICK MANY			
                        quest_html = quest_html + getPickMany(n);
                        break;
                    case '4': // SHORT ANSWER
                        quest_html = quest_html + getShortAnswer(n);
                        break;
                    case '5': // RANKING DROP-DOWN			
                        quest_html = quest_html + getDropDown(n);
                        break;
                    case '6': // RANKING DRAG-AND-DROP
                        quest_html = quest_html + getDragDrop(n);
                        break;
                    case '9': // RANKING 			
                        quest_html = quest_html + getRanking(n);
                        break;
                }
                var imp_scl_html = getImpScale(n)
                return [quest_html, imp_scl_html];
            }

            function SF_getElement(data, name, i) {

                try {
                    return data.getElementsByTagName(name)[i].firstChild.data;
                } catch (e) {
        <?php if (isset($_REQUEST['sf_debug'])) { ?>
                        alert(name + ' ' + i);
        <?php } ?>
                }
            }

            //--><!]]></script>
        <?php
        surveyforce_template_class::SF_GetElements($sf_config);
        echo '<span id="surveyforce_top" style="visibility:hidden; height:1px;"></span>';
        echo SurveyforceHtml::processTemplate($template, $sf_config, $survey, $survey->sf_enable_descr);

        global $mosConfig_absolute_path;
        $word = 'component';
        if (intval(md5($mosConfig_absolute_path . 'survey')) % 2 == 0)
            $word = 'extension';

        if ($sf_config->get('sf_show_dev_info', 1)) {
            ?><br/><div style="text-align:center;">Powered by <span title='JoomPlace'><a target='_blank' title='JoomPlace' href='http://www.joomplace.com/e-learning/surveyforce-deluxe.html'>Joomla <?php echo $word; ?></a></span> SurveyForce Deluxe Software</div><br/><?php
            }
        }

        public function showCategory($cat, $rows, $sf_config) {
           
            surveyforce_template_class::showCategoryView($cat, $rows);

            global $mosConfig_absolute_path;
            $word = 'component';
            if (intval(md5($mosConfig_absolute_path . 'survey')) % 2 == 0)
                $word = 'extension';

            if ($sf_config->get('sf_show_dev_info', 1)) {
                ?><br/><div style="text-align:center;">Powered by <span title='JoomPlace'><a target='_blank' title='JoomPlace' href='http://www.joomplace.com/e-learning/surveyforce-deluxe.html'>Joomla <?php echo $word; ?></a></span> SurveyForce Deluxe Software</div><br/><?php
        }
    }

}

