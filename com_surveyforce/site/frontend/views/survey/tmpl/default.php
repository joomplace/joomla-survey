<?php
/**
 * Surveyforce Deluxe Component for Joomla 3
 * @package Surveyforce Deluxe
 * @author JoomPlace Team
 * @copyright Copyright (C) JoomPlace, www.joomplace.com
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */
defined('_JEXEC') or die('Restricted Access');

$survey = @$this->survey;
$sf_config = @$this->sf_config;
$is_invited = @$this->is_invited;
$invite_num = @$this->invite_num;
$rules = @$this->rules;
$preview = @$this->preview;

$tag = JFactory::getLanguage()->getTag();
$lang = JFactory::getLanguage();
$lang->load(COMPONENT_OPTION, JPATH_SITE, $tag, true);

if (isset($survey->error) && $survey->error) {
    echo $survey->message;
} else {

    $my = JFactory::getUser();

    SurveyforceHelper::SF_load_template($survey->template);
    $template = surveyforce_template_class::SF_MainLayout();

    $document = JFactory::getDocument();
	JHtml::_('jquery.framework');
//	JHtml::_('jquery.ui');
//    $document->addScript(JURI::root() . "components/com_surveyforce/assets/js/jquery-1.9.1.min.js");
    $document->addScript(JURI::root() . "components/com_surveyforce/assets/js/jquery-ui-1.8.13.custom.min.js");
    $document->addScript(JURI::root() . "components/com_surveyforce/assets/js/bits_message.js");
    $document->addScript(JURI::root() . "components/com_surveyforce/assets/js/pagination.js");
    $document->addScript(JURI::root() . "components/com_surveyforce/assets/js/surveyforce.js?v1.1");
    $document->addStyleSheet(JURI::root() . 'components/com_surveyforce/assets/css/surveyforce.css');
    ?>

    <script language="JavaScript" type="text/javascript">
    <!--//--><![CDATA[//><!--

	<?php

		if ( !JFactory::getApplication()->input->get('survey_blocked', 0) ) {

    echo 'var rules = new Array();' . "\n";
    if (is_array($rules) && count($rules) > 0) {
        foreach ($rules as $nn => $rule) {
            echo "rules[" . $nn . "] = new Array('" . $rule->quest_id . "', '" . $rule->quest_id_a . "', '" . $rule->answer . "', '" . $rule->ans_field . "');\n";
        }
    }


    ?>
        var mosConfig_live_site = '<?php echo JUri::base(); ?>';
        var debug_mode = <?php echo (isset($_REQUEST['sf_debug'])) ? 1 : 0; ?>;

        var url_prefix = 'index.php?no_html=1&tmpl=component&option=com_surveyforce&task=survey.SF_ajaxAction<?php echo ($preview ? '&preview=1' : '') ?>';

        var quest_count = 1;
        var questions = {};

        var invited_url = '<?php echo ($is_invited == 1) ? "&invite=$invite_num" : "" ?>';
        var start_id = 0;
        var survey_id = <?php echo ($survey->id) ? $survey->id : 0; ?>;

        // *** DRAG'and'DROP CODE *** //
        var color_cont = '<?php echo JComponentHelper::getParams('com_surveyforce')->get('color_cont') ?>';
        var color_drag = '<?php echo $sf_config->get('color_drag') ?>';
        var color_highlight = '<?php echo $sf_config->get('color_highlight') ?>';
        var last_drag_id = '';
        var last_drag_id_drag = '';
        var last_drag_quest_n = -1;
        // *** end of DRAG'and'DROP CODE *** //

        var kol_main_elems = 0;
        var main_ids_array = new Array(kol_main_elems); //for likert quest
        // *** MESSAGES *** 
        var mes_enter_some_words = '<?php echo JText::_('COM_SURVEYFORCE_COMPLETE_SHORT_ANSWER') ?>';
        var mes_select_one_radio = '<?php echo JText::_('COM_SURVEYFORCE_COMPLETE_PICK_ONE') ?>';
        var mes_select_some_checks = '<?php echo JText::_('COM_SURVEYFORCE_COMPLETE_PICK_MANY') ?>';
        var mes_select_your_choice = '<?php echo JText::_('COM_SURVEYFORCE_COMPLETE_DROP_DOWN') ?>';
        var mes_select_your_rank = '<?php echo JText::_('COM_SURVEYFORCE_COMPLETE_RANK') ?>';
        var mes_complete_this_part = '<?php echo JText::_('COM_SURVEYFORCE_COMPLETE_LIKERT') ?>';
        var mes_complete_this_part_drag = '<?php echo JText::_('COM_SURVEYFORCE_COMPLETE_DRAG_AND_DROP') ?>';
        var mes_complete_imp_scale = '<?php echo JText::_('COM_SURVEYFORCE_COMPLETE_IMPORTANT_SCALE') ?>';

        var mes_loading = '<?php echo JText::_('COM_SURVEYFORCE_LOAD_DATA') ?>';
        var mes_failed = '<?php echo JText::_('COM_SURVEYFORCE_FAILED') ?>';
        var mes_invite_complete = '<?php echo JText::_('COM_SURVEYFORCE_INVITED_COMPLETE') ?>';
        var mes_reg_complete = '<?php echo JText::_('COM_SURVEYFORCE_REG_COMPLETE') ?>';
        var mes_pub_complete = '<?php echo JText::_('COM_SURVEYFORCE_PUB_COMPLETE') ?>';
        var mes_please_wait = '<?php echo JText::_('COM_SURVEYFORCE_PLEASE_WAIT') ?>';

        var mes_session_timed_out = '<?php echo JText::_('COM_SURVEYFORCE_SESSION_TIMED_OUT') ?>';
        // *** some script variables ***
        var user_unique_id = '';
        var response;
        var survey_blocked = 0; // set block after each question (release after 2 seconds).
        var is_final_question = 0;
        var is_prev = 1;

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

         /*   if (jQuery(window).width() <= '480') {
                regexp = /&tmpl=component/
                sf_uri = window.location.href;
                if (!regexp.test(sf_uri)) {
    <?php if (!$is_invited && !$preview) { ?>
                        window.location.href = "<?php echo JURI::root() ?>index.php?option=com_surveyforce&id=<?php echo $survey->id ?>&tmpl=component";
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
                        ShowMessage('error_messagebox', 1, '<?php echo JText::_('COM_SURVEYFORCE_CANNOT_CHECK_ANYMORE'); ?>');
                        elem.checked = false;
                        return false;
                    }
                }
            }
            ShowMessage('error_messagebox', 1, '&nbsp;');
            return true;
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
            survey_blocked = 1;
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

        function sf_AnalizeRequest(http_request) {
            var finish_count = <?php echo substr_count($template, '{FINISH_BUTTON}'); ?>;
            var next_count = <?php echo substr_count($template, '{NEXT_BUTTON}'); ?>;

            if (http_request.readyState == 4) {
                if ((http_request.status == 200)) {
                    response = http_request.responseXML.documentElement;

                    var task = response.getElementsByTagName('task')[0].firstChild.data;

					if ( task != 'failed' )
					{
                    	quest_count = response.getElementsByTagName('quest_count')[0].firstChild.data;

					   questions = {};
						is_final_question = response.getElementsByTagName('is_final_question')[0].firstChild.data;
					}

				ShowMessage('error_messagebox', 0, '');
				ShowMessage('error_messagebox2', 0, '');

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
                            
                            for (var i in questions) {
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
                            for (var i in questions) {
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
                    ShowMessage('error_messagebox', 1, '<?php echo JText::_('COM_SURVEYFORCE_FAILED_REQUEST') ?> (' + http_request.status + ')');
                }
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
                if (!confirm("<?php echo JText::_('COM_SURVEYFORCE_ALREADY_COMPLETED_REG'); ?>")) {
                    ShowMessage('error_messagebox', 1, '');
                    window.history.go(-1);
                    return;
                }
    <?php } ?>
            sf_MakeRequest('action=start&survey=<?php echo $survey->id ?>' + invited_url);
        }

        function sf_escape(txt) {
            var text = txt;

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
                    ShowMessage('error_messagebox', 1, '<?php echo JText::_('COM_SURVEYFORCE_UNKNOWN_ERROR') ?>');
                    ShowMessage('error_messagebox2', 1, '<?php echo JText::_('COM_SURVEYFORCE_UNKNOWN_ERROR') ?>');
                    return false;
                    break;
            }
            return '&quest_id[]=' + questions[n].cur_quest_id + '&answer[]=' + answer + imp_scale_req;
        }

        function sf_UpdateTaskDiv(task) {

            var start_count = jQuery('.start_bt_container').length;
            var finish_count = jQuery('.finish_bt_container').length;
            var prev_count = jQuery('.prev_bt_container').length;
            var next_count = jQuery('.next_bt_container').length;
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
                       
                                sf_getObj('next_' + i).style.display = '';
                                sf_getObj('next_' + i).innerHTML = getButton('next', '<?php echo JText::_('COM_SURVEYFORCE_NEXT_QUEST') ?>', 'sf_SurveyNextOn()');
                           
                    }
                    break;
                case 'next':
                    for (i = 0; i < next_count; i++) {
                        
                                sf_getObj('next_' + i).style.display = '';
                                sf_getObj('next_' + i).innerHTML = getButton('next', '<?php echo JText::_('COM_SURVEYFORCE_NEXT_QUEST') ?>', 'sf_SurveyNextOn()');
                           
                    }

                    if (parseInt(is_prev)) {
                        for (i = 0; i < prev_count; i++) {
                            
                                    sf_getObj('prev_' + i).style.display = '';
                                    sf_getObj('prev_' + i).innerHTML = getButton('prev', '<?php echo JText::_('COM_SURVEYFORCE_PREV_QUEST') ?>', 'sf_SurveyPrevOn()');
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
                            sf_getObj('finish_' + i).innerHTML = getButton('finish', '<?php echo JText::_('COM_SURVEYFORCE_SUBMIT_SURVEY') ?>', 'sf_SurveyNextOn()');
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
                            sf_getObj('finish_' + i).innerHTML = getButton('finish', '<?php echo JText::_('COM_SURVEYFORCE_SUBMIT_SURVEY') ?>', 'sf_SurveyNextOn()');
                        } catch (e) {
                        }
                    }

                    if (parseInt(is_prev)) {
                        for (i = 0; i < prev_count; i++) {
                            
                                    sf_getObj('prev_' + i).style.display = '';
                                    sf_getObj('prev_' + i).innerHTML = getButton('prev', '<?php echo JText::_('COM_SURVEYFORCE_PREV_QUEST') ?>', 'sf_SurveyPrevOn()');
                                
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

        function sf_GetQuestionHtml(qtype, n) {
            var quest_html = '';
            switch (qtype) {
                case '1': //LIKERT SCALE			
                    quest_html = quest_html + SF_getElement(questions[n].response, 'html', 0);
                    break;
                case '2': // PICK ONE			
                    quest_html = quest_html + SF_getElement(questions[n].response, 'html', 0);
                    break;
                case '3': // PICK MANY			
                    quest_html = quest_html + SF_getElement(questions[n].response, 'html', 0);
                    break;
                case '4': // SHORT ANSWER
                    quest_html = quest_html + SF_getElement(questions[n].response, 'html', 0);
                    break;
                case '5': // RANKING DROP-DOWN			
                    quest_html = quest_html + SF_getElement(questions[n].response, 'html', 0);
                    break;
                case '6': // RANKING DRAG-AND-DROP
                    quest_html = quest_html + SF_getElement(questions[n].response, 'html', 0);
                    break;
                case '7': // Boilerplate
                    quest_html = quest_html + SF_getElement(questions[n].response, 'html', 0);
                    break;
                case '9': // RANKING 			
                    quest_html = quest_html + SF_getElement(questions[n].response, 'html', 0);
                    break;
            }
            //var imp_scl_html = getImpScale(n)
            return quest_html;//+imp_scl_html;
        }

        //--><!]]></script>
    <?php
    echo '<span id="surveyforce_top" style="visibility:hidden; height:1px;"></span>';

    $listQuestionTypes = SurveyforceHelper::listQuestionTypes($survey->id);
    SurveyforceHelper::getJsCss($sf_config->get('template'),$listQuestionTypes);


    echo SurveyforceTemplates::processTemplate($template, $sf_config, $survey, $survey->sf_enable_descr);


    $word = 'component';
    if (intval(md5(JPATH_SITE . 'survey')) % 2 == 0)
        $word = 'extension';

    if ($sf_config->get('sf_show_dev_info', 1)) {
        ?><br/><div style="text-align:center;">Powered by <span title='JoomPlace'><a target='_blank' title='JoomPlace' href='http://www.joomplace.com/e-learning/surveyforce-deluxe.html' rel="nofollow">Joomla <?php echo $word; ?></a></span> Survey Force Deluxe Software</div><br/><?php
    }
}
}