<?php

/**
 * Survey Force Deluxe component for Joomla 3
 * @package Survey Force Deluxe
 * @author JoomPlace Team
 * @Copyright Copyright (C) JoomPlace, www.joomplace.com
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.controlleradmin');

class SurveyforceControllerSamples extends JControllerForm {

    public function __construct($config = array()) {
        parent::__construct($config);
    }

    public function installsample1() {
        $database = JFactory::getDbo();
        $application = JFactory::getApplication();

        $profile = new stdClass();
        $profile->id = NULL;
        $profile->sf_name = 'Customer Service Satisfaction Survey';
        $profile->sf_descr = '<img src="http://demo.joomplace.com/images/survey_icon.jpg" align="left" height="200px"><p style="text-align:justify">\r\nWe all know customer satisfaction is essential to the survival of our businesses. How do we find out whether our customers are satisfied? The best way to find out whether your customers are satisfied is to ask them.\r\n</p>\r\n<p style="text-align:justify">\r\nWhen you conduct a customer satisfaction survey, what you ask the customers is important. How, when , and how often you ask these questions are also important. However, the most important thing about conducting a customer satisfaction survey is what you do with their answers. \r\n</p>';
        $profile->sf_image = '';
        $profile->sf_cat = 1;
        $profile->sf_lang = 1;
        //$profile->sf_date_started = '0000-00-00 00:00:00'; // Not, necessary. Its property set default
        //$profile->sf_redirect_url = ??? //Field is not used
        $profile->sf_author = 42;
        $profile->sf_public = 1;
        $profile->sf_invite = 0;
        $profile->sf_reg = 1;
        $profile->sf_friend = 0;
        $profile->published = 1;
        $profile->sf_fpage_type = 0;
        $profile->sf_fpage_text = '<strong>End of the survey - Thank you for your time.</strong>';
        $profile->sf_special = '0';
        $profile->sf_auto_pb = 0;
        $profile->sf_progressbar = 0;
        $profile->sf_progressbar_type = 0;
        $profile->sf_use_css = 0;
        $profile->sf_enable_descr = 1;
        $profile->sf_reg_voting = 2;
        $profile->sf_friend_voting = 0;
        $profile->sf_inv_voting = 1;
        $profile->sf_template = 1;
        $profile->sf_pub_voting = 2;
        $profile->sf_pub_control = 3;
        $profile->surv_short_descr = NULL;
        $profile->sf_after_start = 0;
        $profile->sf_anonymous = 0;
        $profile->sf_random = 0;

        if (!$database->insertObject( '#__survey_force_survs', $profile, 'id' )) $application->enqueueMessage(JText::_($database->stderr()), 'error');
        else $new_survey_id = $profile->id;

        $query = "INSERT INTO `#__survey_force_quests` (`id`, `sf_survey`, `sf_qtype`, `sf_qtext`, `sf_impscale`, `sf_rule`, `sf_fieldtype`, `ordering`, `sf_compulsory`, `sf_section_id`, `published`, `sf_qstyle`, `sf_num_options`, `sf_default_hided`) VALUES (NULL, {$new_survey_id}, 4, '<b>What was your main reason for contacting technical support?</b>   &nbsp;', 0, 0, '', 1, 1, 0, 1, 0, 0, 0)";
        $database->setQuery($query);
        $database->execute();

        $query = "INSERT INTO `#__survey_force_quests` (`id`, `sf_survey`, `sf_qtype`, `sf_qtext`, `sf_impscale`, `sf_rule`, `sf_fieldtype`, `ordering`, `sf_compulsory`, `sf_section_id`, `published`, `sf_qstyle`, `sf_num_options`, `sf_default_hided`) VALUES (NULL, {$new_survey_id}, 3, '<b>How did you contact technical support?</b>  &nbsp;', 0, 0, '', 2, 1, 0, 1, 0, 0, 0)";
        $database->setQuery($query);
        $database->execute();
        $new_id = $database->insertid();

        $query = "INSERT INTO `#__survey_force_fields` (`id`, `quest_id`, `ftext`, `alt_field_id`, `is_main`, `is_true`, `ordering`) VALUES (NULL, {$new_id}, 'phone', 0, 1, 1, 2)";
        $database->setQuery($query);
        $database->execute();
        $query = "INSERT INTO `#__survey_force_fields` (`id`, `quest_id`, `ftext`, `alt_field_id`, `is_main`, `is_true`, `ordering`) VALUES (NULL, {$new_id}, 'Other', 0, 0, 1, 3)";
        $database->setQuery($query);
        $database->execute();


        $query = "INSERT INTO `#__survey_force_quests` (`id`, `sf_survey`, `sf_qtype`, `sf_qtext`, `sf_impscale`, `sf_rule`, `sf_fieldtype`, `ordering`, `sf_compulsory`, `sf_section_id`, `published`, `sf_qstyle`, `sf_num_options`, `sf_default_hided`) VALUES (NULL, {$new_survey_id}, 7, '<b>PLEASE TELL US HOW MUCH YOU AGREE OR DISAGREE WITH THE FOLLOWING STATEMENTS:</b> &nbsp;', 0, 0, '', 3, 0, 0, 1, 0, 0, 0)";
        $database->setQuery($query);
        $database->execute();

        $query = "INSERT INTO `#__survey_force_quests` (`id`, `sf_survey`, `sf_qtype`, `sf_qtext`, `sf_impscale`, `sf_rule`, `sf_fieldtype`, `ordering`, `sf_compulsory`, `sf_section_id`, `published`, `sf_qstyle`, `sf_num_options`, `sf_default_hided`) VALUES (NULL, {$new_survey_id}, 1, '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;', 0, 0, '', 4, 1, 0, 1, 0, 0, 0)";
        $database->setQuery($query);
        $database->execute();
        $new_id = $database->insertid();

        $query = "INSERT INTO `#__survey_force_fields` (`id`, `quest_id`, `ftext`, `alt_field_id`, `is_main`, `is_true`, `ordering`) VALUES (NULL, {$new_id}, 'How fast did you get a reply from a technical support staff member?', 0, 1, 1, 0)";
        $database->setQuery($query);
        $database->execute();

        $query = "INSERT INTO `#__survey_force_scales` (`id`, `quest_id`, `stext`, `ordering`) VALUES (NULL, {$new_id}, 'extremely slow', 0)";
        $database->setQuery($query);
        $database->execute();
        $query = "INSERT INTO `#__survey_force_scales` (`id`, `quest_id`, `stext`, `ordering`) VALUES (NULL, {$new_id}, 'slow', 1)";
        $database->setQuery($query);
        $database->execute();
        $query = "INSERT INTO `#__survey_force_scales` (`id`, `quest_id`, `stext`, `ordering`) VALUES (NULL, {$new_id}, 'fairly fast', 2)";
        $database->setQuery($query);
        $database->execute();
        $query = "INSERT INTO `#__survey_force_scales` (`id`, `quest_id`, `stext`, `ordering`) VALUES (NULL, {$new_id}, 'fast', 3)";
        $database->setQuery($query);
        $database->execute();
        $query = "INSERT INTO `#__survey_force_scales` (`id`, `quest_id`, `stext`, `ordering`) VALUES (NULL, {$new_id}, 'extremely fast', 4)";
        $database->setQuery($query);
        $database->execute();


        $query = "INSERT INTO `#__survey_force_quests` (`id`, `sf_survey`, `sf_qtype`, `sf_qtext`, `sf_impscale`, `sf_rule`, `sf_fieldtype`, `ordering`, `sf_compulsory`, `sf_section_id`, `published`, `sf_qstyle`, `sf_num_options`, `sf_default_hided`) VALUES (NULL, {$new_survey_id}, 1, '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;', 0, 0, '', 5, 1, 0, 1, 0, 0, 0)";
        $database->setQuery($query);
        $database->execute();
        $new_id = $database->insertid();

        $query = "INSERT INTO `#__survey_force_fields` (`id`, `quest_id`, `ftext`, `alt_field_id`, `is_main`, `is_true`, `ordering`) VALUES (NULL, {$new_id}, 'The technical support staff was helpful.', 0, 1, 1, 0)";
        $database->setQuery($query);
        $database->execute();

        $query = "INSERT INTO `#__survey_force_scales` (`id`, `quest_id`, `stext`, `ordering`) VALUES (NULL, {$new_id}, 'strongly disagree', 0)";
        $database->setQuery($query);
        $database->execute();
        $query = "INSERT INTO `#__survey_force_scales` (`id`, `quest_id`, `stext`, `ordering`) VALUES (NULL, {$new_id}, 'disagree', 1)";
        $database->setQuery($query);
        $database->execute();
        $query = "INSERT INTO `#__survey_force_scales` (`id`, `quest_id`, `stext`, `ordering`) VALUES (NULL, {$new_id}, 'more or less agree', 2)";
        $database->setQuery($query);
        $database->execute();
        $query = "INSERT INTO `#__survey_force_scales` (`id`, `quest_id`, `stext`, `ordering`) VALUES (NULL, {$new_id}, 'agree', 3)";
        $database->setQuery($query);
        $database->execute();
        $query = "INSERT INTO `#__survey_force_scales` (`id`, `quest_id`, `stext`, `ordering`) VALUES (NULL, {$new_id}, 'totally agree', 4)";
        $database->setQuery($query);
        $database->execute();


        $query = "INSERT INTO `#__survey_force_quests` (`id`, `sf_survey`, `sf_qtype`, `sf_qtext`, `sf_impscale`, `sf_rule`, `sf_fieldtype`, `ordering`, `sf_compulsory`, `sf_section_id`, `published`, `sf_qstyle`, `sf_num_options`, `sf_default_hided`) VALUES (NULL, {$new_survey_id}, 1, '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;', 0, 0, '', 6, 1, 0, 1, 0, 0, 0)";
        $database->setQuery($query);
        $database->execute();
        $new_id = $database->insertid();

        $query = "INSERT INTO `#__survey_force_fields` (`id`, `quest_id`, `ftext`, `alt_field_id`, `is_main`, `is_true`, `ordering`) VALUES (NULL, {$new_id}, 'Overall, how would you rate the quality of the assistance you received from technical support?', 0, 1, 1, 0)";
        $database->setQuery($query);
        $database->execute();


        $query = "INSERT INTO `#__survey_force_scales` (`id`, `quest_id`, `stext`, `ordering`) VALUES (NULL, {$new_id}, 'pure', 0)";
        $database->setQuery($query);
        $database->execute();
        $query = "INSERT INTO `#__survey_force_scales` (`id`, `quest_id`, `stext`, `ordering`) VALUES (NULL, {$new_id}, 'not very good', 1)";
        $database->setQuery($query);
        $database->execute();
        $query = "INSERT INTO `#__survey_force_scales` (`id`, `quest_id`, `stext`, `ordering`) VALUES (NULL, {$new_id}, 'good enough', 2)";
        $database->setQuery($query);
        $database->execute();
        $query = "INSERT INTO `#__survey_force_scales` (`id`, `quest_id`, `stext`, `ordering`) VALUES (NULL, {$new_id}, 'very good', 3)";
        $database->setQuery($query);
        $database->execute();
        $query = "INSERT INTO `#__survey_force_scales` (`id`, `quest_id`, `stext`, `ordering`) VALUES (NULL, {$new_id}, 'excellent', 4)";
        $database->setQuery($query);
        $database->execute();

        $query = "INSERT INTO `#__survey_force_quests` (`id`, `sf_survey`, `sf_qtype`, `sf_qtext`, `sf_impscale`, `sf_rule`, `sf_fieldtype`, `ordering`, `sf_compulsory`, `sf_section_id`, `published`, `sf_qstyle`, `sf_num_options`, `sf_default_hided`) VALUES (NULL, {$new_survey_id}, 4, '<b>Do you have any suggestions for improvement of our technical support services?</b>&nbsp;', 0, 0, '', 7, 0, 0, 1, 0, 0, 0)";
        $database->setQuery($query);
        $database->execute();
        
        $this->display();
        
    }

    public function installsample2() {
        $database = JFactory::getDbo();

        $query = "INSERT INTO `#__survey_force_survs` (`id`, `sf_name`, `sf_descr`, `sf_image`, `sf_cat`, `sf_lang`, `sf_date`, `sf_author`, `sf_public`, `sf_invite`, `sf_reg`, `sf_friend`, `published`, `sf_fpage_type`, `sf_fpage_text`, `sf_special`, `sf_auto_pb`, `sf_progressbar`, `sf_progressbar_type`, `sf_use_css`, `sf_enable_descr`, `sf_reg_voting`, `sf_friend_voting`, `sf_inv_voting`, `sf_template`, `sf_pub_voting`, `sf_pub_control`, `surv_short_descr`, `sf_after_start`, `sf_anonymous`, `sf_random`) VALUES (NULL, 'Sample Branching Survey', '<p>\r\nThis survey presents another template and question rules you can use in your survey like:\r\n<ul>\r\n<li>If the answer is ...  don''t show question ...</li>\r\n<li>If the answer is ... go to question ...</li>\r\n</ul>\r\n</p>\r\n<p>\r\nBy the way you can enable this welcome screen but can also disable it so users won''t see it. This survey will also show the progress bar and all questions on different pages. And when you''re done answering questions, you''ll see the survey results page at the end instead of the thank you message (you can choose whether to show them in a graph charts or pie charts, and chenge the size and colors of those).\r\n</p>', '', 1, 1, '0000-00-00 00:00:00', 42, 1, 0, 1, 0, 1, 1, '<strong>End of the survey - Thank you for your time.</strong>', '0', 1, 1, 0, 0, 1, 0, 0, 1, 2, 0, 0, NULL, 0, 0, 0)";
        $database->setQuery($query);
        $database->execute();
        $new_survey_id = $database->insertid();

        $query = "INSERT INTO `#__survey_force_quests` (`id`, `sf_survey`, `sf_qtype`, `sf_qtext`, `sf_impscale`, `sf_rule`, `sf_fieldtype`, `ordering`, `sf_compulsory`, `sf_section_id`, `published`, `sf_qstyle`, `sf_num_options`, `sf_default_hided`) VALUES (NULL, {$new_survey_id}, 4, '<p>\r\nHow would you describe our organization to a friend in a couple of words?\r\n</p>\r\n<p>\r\n{x}<br />{x}\r\n</p>\r\n<p style=\"font-size:0.8em\">\r\nYou can skip this question if you don''t want to answer - it wasn''t made compulsory.\r\n</p>', 0, 0, '', 1, 0, 0, 1, 0, 0, 0)";
        $database->setQuery($query);
        $database->execute();

        $query = "INSERT INTO `#__survey_force_quests` (`id`, `sf_survey`, `sf_qtype`, `sf_qtext`, `sf_impscale`, `sf_rule`, `sf_fieldtype`, `ordering`, `sf_compulsory`, `sf_section_id`, `published`, `sf_qstyle`, `sf_num_options`, `sf_default_hided`) VALUES (NULL, {$new_survey_id}, 8, 'Page Break', 0, 0, '', 2, 0, 0, 1, 0, 0, 0)";
        $database->setQuery($query);
        $database->execute();

        $query = "INSERT INTO `#__survey_force_quests` (`id`, `sf_survey`, `sf_qtype`, `sf_qtext`, `sf_impscale`, `sf_rule`, `sf_fieldtype`, `ordering`, `sf_compulsory`, `sf_section_id`, `published`, `sf_qstyle`, `sf_num_options`, `sf_default_hided`) VALUES (NULL, {$new_survey_id}, 2, '<p>\r\nIs this your first visit to our organization?\r\n</p>\r\n<p style=\"font-size:0.8em\">\r\nIf the answer is yes, you''ll be redirected to the question ''Will you recommend it to a friend or relative'', in case it''s no, you''ll be directed to ''How many times do you usually visit each year'' question.\r\n</p>', 0, 0, '', 3, 1, 0, 1, 0, 0, 0)";
        $database->setQuery($query);
        $database->execute();
        $new_id73 = $database->insertid();

        $query = "INSERT INTO `#__survey_force_fields` (`id`, `quest_id`, `ftext`, `alt_field_id`, `is_main`, `is_true`, `ordering`) VALUES (NULL, {$new_id73}, 'Yes', 0, 1, 1, 0)";
        $database->setQuery($query);
        $database->execute();
        $new_id196 = $database->insertid();
        $query = "INSERT INTO `#__survey_force_fields` (`id`, `quest_id`, `ftext`, `alt_field_id`, `is_main`, `is_true`, `ordering`) VALUES (NULL, {$new_id73}, 'No', 0, 1, 1, 1)";
        $database->setQuery($query);
        $database->execute();
        $new_id197 = $database->insertid();

        $query = "INSERT INTO `#__survey_force_quests` (`id`, `sf_survey`, `sf_qtype`, `sf_qtext`, `sf_impscale`, `sf_rule`, `sf_fieldtype`, `ordering`, `sf_compulsory`, `sf_section_id`, `published`, `sf_qstyle`, `sf_num_options`, `sf_default_hided`) VALUES (NULL, {$new_survey_id}, 8, 'Page Break', 0, 0, '', 4, 0, 0, 1, 0, 0, 0)";
        $database->setQuery($query);
        $database->execute();

        $query = "INSERT INTO `#__survey_force_quests` (`id`, `sf_survey`, `sf_qtype`, `sf_qtext`, `sf_impscale`, `sf_rule`, `sf_fieldtype`, `ordering`, `sf_compulsory`, `sf_section_id`, `published`, `sf_qstyle`, `sf_num_options`, `sf_default_hided`) VALUES (NULL, {$new_survey_id}, 2, '<p>\r\nHow many times do you usually visit each year?\r\n</p>\r\n<p style=\"font-size:0.8em\">\r\nIf the answer is once a year, you''ll be redirected to ''Will you recommend it to a friend or relative'' question,\r\nall other options will lead you to whether you are a member question.\r\n</p>', 0, 0, '', 5, 1, 0, 1, 0, 0, 0)";
        $database->setQuery($query);
        $database->execute();
        $new_id75 = $database->insertid();

        $query = "INSERT INTO `#__survey_force_fields` (`id`, `quest_id`, `ftext`, `alt_field_id`, `is_main`, `is_true`, `ordering`) VALUES (NULL, {$new_id75}, 'Once a year', 0, 1, 1, 0)";
        $database->setQuery($query);
        $database->execute();
        $new_id198 = $database->insertid();
        $query = "INSERT INTO `#__survey_force_fields` (`id`, `quest_id`, `ftext`, `alt_field_id`, `is_main`, `is_true`, `ordering`) VALUES (NULL, {$new_id75}, '2-3 times', 0, 1, 1, 1)";
        $database->setQuery($query);
        $database->execute();
        $new_id199 = $database->insertid();
        $query = "INSERT INTO `#__survey_force_fields` (`id`, `quest_id`, `ftext`, `alt_field_id`, `is_main`, `is_true`, `ordering`) VALUES (NULL, {$new_id75}, '5-10 times', 0, 1, 1, 2)";
        $database->setQuery($query);
        $database->execute();
        $new_id200 = $database->insertid();
        $query = "INSERT INTO `#__survey_force_fields` (`id`, `quest_id`, `ftext`, `alt_field_id`, `is_main`, `is_true`, `ordering`) VALUES (NULL, {$new_id75}, 'More than 10 times', 0, 1, 1, 3)";
        $database->setQuery($query);
        $database->execute();
        $new_id201 = $database->insertid();


        $query = "INSERT INTO `#__survey_force_quests` (`id`, `sf_survey`, `sf_qtype`, `sf_qtext`, `sf_impscale`, `sf_rule`, `sf_fieldtype`, `ordering`, `sf_compulsory`, `sf_section_id`, `published`, `sf_qstyle`, `sf_num_options`, `sf_default_hided`) VALUES (NULL, {$new_survey_id}, 8, 'Page Break', 0, 0, '', 6, 0, 0, 1, 0, 0, 0)";
        $database->setQuery($query);
        $database->execute();

        $query = "INSERT INTO `#__survey_force_quests` (`id`, `sf_survey`, `sf_qtype`, `sf_qtext`, `sf_impscale`, `sf_rule`, `sf_fieldtype`, `ordering`, `sf_compulsory`, `sf_section_id`, `published`, `sf_qstyle`, `sf_num_options`, `sf_default_hided`) VALUES (NULL, {$new_survey_id}, 2, '<p>\r\nWill you recommend it to a friend or relative?�\r\n</p>\r\n<p style=\"font-size:0.8em\">\r\nIf you answer Yes - you end the survey and are presented with the survey results.<br />\r\nIf no, it''s next question then.\r\n</p>', 0, 0, '', 9, 0, 0, 1, 0, 0, 0)";
        $database->setQuery($query);
        $database->execute();
        $new_id77 = $database->insertid();

        $query = "INSERT INTO `#__survey_force_fields` (`id`, `quest_id`, `ftext`, `alt_field_id`, `is_main`, `is_true`, `ordering`) VALUES (NULL, {$new_id77}, 'Yes', 0, 1, 1, 0)";
        $database->setQuery($query);
        $database->execute();
        $new_id202 = $database->insertid();
        $query = "INSERT INTO `#__survey_force_fields` (`id`, `quest_id`, `ftext`, `alt_field_id`, `is_main`, `is_true`, `ordering`) VALUES (NULL, {$new_id77}, 'No', 0, 1, 1, 1)";
        $database->setQuery($query);
        $database->execute();

        $query = "INSERT INTO `#__survey_force_quests` (`id`, `sf_survey`, `sf_qtype`, `sf_qtext`, `sf_impscale`, `sf_rule`, `sf_fieldtype`, `ordering`, `sf_compulsory`, `sf_section_id`, `published`, `sf_qstyle`, `sf_num_options`, `sf_default_hided`) VALUES (NULL, {$new_survey_id}, 8, 'Page Break', 0, 0, '', 8, 0, 0, 1, 0, 0, 0)";
        $database->setQuery($query);
        $database->execute();

        $query = "INSERT INTO `#__survey_force_quests` (`id`, `sf_survey`, `sf_qtype`, `sf_qtext`, `sf_impscale`, `sf_rule`, `sf_fieldtype`, `ordering`, `sf_compulsory`, `sf_section_id`, `published`, `sf_qstyle`, `sf_num_options`, `sf_default_hided`) VALUES (NULL, {$new_survey_id}, 2, 'Are you a member?\r\n&nbsp;', 0, 0, '', 7, 1, 0, 1, 0, 0, 0)";
        $database->setQuery($query);
        $database->execute();
        $new_id79 = $database->insertid();

        $query = "INSERT INTO `#__survey_force_fields` (`id`, `quest_id`, `ftext`, `alt_field_id`, `is_main`, `is_true`, `ordering`) VALUES (NULL, {$new_id79}, 'Yes', 0, 1, 1, 0)";
        $database->setQuery($query);
        $database->execute();
        $query = "INSERT INTO `#__survey_force_fields` (`id`, `quest_id`, `ftext`, `alt_field_id`, `is_main`, `is_true`, `ordering`) VALUES (NULL, {$new_id79}, 'No', 0, 1, 1, 1)";
        $database->setQuery($query);
        $database->execute();
        $query = "INSERT INTO `#__survey_force_fields` (`id`, `quest_id`, `ftext`, `alt_field_id`, `is_main`, `is_true`, `ordering`) VALUES (NULL, {$new_id79}, 'In the past, but not now', 0, 1, 1, 2)";
        $database->setQuery($query);
        $database->execute();

        $query = "INSERT INTO `#__survey_force_quests` (`id`, `sf_survey`, `sf_qtype`, `sf_qtext`, `sf_impscale`, `sf_rule`, `sf_fieldtype`, `ordering`, `sf_compulsory`, `sf_section_id`, `published`, `sf_qstyle`, `sf_num_options`, `sf_default_hided`) VALUES (NULL, {$new_survey_id}, 8, 'Page Break', 0, 0, '', 10, 0, 0, 1, 0, 0, 0)";
        $database->setQuery($query);
        $database->execute();

        $query = "INSERT INTO `#__survey_force_quests` (`id`, `sf_survey`, `sf_qtype`, `sf_qtext`, `sf_impscale`, `sf_rule`, `sf_fieldtype`, `ordering`, `sf_compulsory`, `sf_section_id`, `published`, `sf_qstyle`, `sf_num_options`, `sf_default_hided`) VALUES (81, {$new_survey_id}, 2, '<p>\r\nWhat would encourage you to come back here?\r\n</p>\r\n<p style=\"font-size:0.8em\">\r\nThis question is not asked if you sais you''d recommend this to friends previously.\r\n</p>', 0, 0, '', 11, 1, 0, 1, 0, 0, 0)";
        $database->setQuery($query);
        $database->execute();
        $new_id81 = $database->insertid();

        $query = "INSERT INTO `#__survey_force_quest_show` (`id`, `quest_id`, `survey_id`, `quest_id_a`, `answer`, `ans_field`) VALUES (NULL, {$new_id81}, {$new_survey_id}, {$new_id77}, {$new_id202}, 0)";
        $database->setQuery($query);
        $database->execute();

        $query = "INSERT INTO `#__survey_force_fields` (`id`, `quest_id`, `ftext`, `alt_field_id`, `is_main`, `is_true`, `ordering`) VALUES (NULL, {$new_id81}, 'Extended hours', 0, 1, 1, 0)";
        $database->setQuery($query);
        $database->execute();
        $query = "INSERT INTO `#__survey_force_fields` (`id`, `quest_id`, `ftext`, `alt_field_id`, `is_main`, `is_true`, `ordering`) VALUES (NULL, {$new_id81}, 'Discount', 0, 1, 1, 1)";
        $database->setQuery($query);
        $database->execute();
        $query = "INSERT INTO `#__survey_force_fields` (`id`, `quest_id`, `ftext`, `alt_field_id`, `is_main`, `is_true`, `ordering`) VALUES (NULL, {$new_id81}, 'Nothing', 0, 1, 1, 2)";
        $database->setQuery($query);
        $database->execute();
        $query = "INSERT INTO `#__survey_force_fields` (`id`, `quest_id`, `ftext`, `alt_field_id`, `is_main`, `is_true`, `ordering`) VALUES (NULL, {$new_id81}, 'Other', 0, 0, 1, 3)";
        $database->setQuery($query);
        $database->execute();

        $query = "INSERT INTO `#__survey_force_quests` (`id`, `sf_survey`, `sf_qtype`, `sf_qtext`, `sf_impscale`, `sf_rule`, `sf_fieldtype`, `ordering`, `sf_compulsory`, `sf_section_id`, `published`, `sf_qstyle`, `sf_num_options`, `sf_default_hided`) VALUES (NULL, {$new_survey_id}, 8, 'Page Break', 0, 0, '', 12, 0, 0, 1, 0, 0, 0)";
        $database->setQuery($query);
        $database->execute();


        $query = "INSERT INTO `#__survey_force_rules` (`id`, `quest_id`, `answer_id`, `next_quest_id`, `alt_field_id`, `priority`) VALUES (NULL, {$new_id73}, {$new_id197}, {$new_id75}, 0, 0)";
        $database->setQuery($query);
        $database->execute();
        $query = "INSERT INTO `#__survey_force_rules` (`id`, `quest_id`, `answer_id`, `next_quest_id`, `alt_field_id`, `priority`) VALUES (NULL, {$new_id73}, {$new_id196}, {$new_id77}, 0, 0)";
        $database->setQuery($query);
        $database->execute();
        $query = "INSERT INTO `#__survey_force_rules` (`id`, `quest_id`, `answer_id`, `next_quest_id`, `alt_field_id`, `priority`) VALUES (NULL, {$new_id75}, {$new_id198}, {$new_id77}, 0, 0)";
        $database->setQuery($query);
        $database->execute();
        $query = "INSERT INTO `#__survey_force_rules` (`id`, `quest_id`, `answer_id`, `next_quest_id`, `alt_field_id`, `priority`) VALUES (NULL, {$new_id75}, {$new_id199}, {$new_id79}, 0, 0)";
        $database->setQuery($query);
        $database->execute();
        $query = "INSERT INTO `#__survey_force_rules` (`id`, `quest_id`, `answer_id`, `next_quest_id`, `alt_field_id`, `priority`) VALUES (NULL, {$new_id75}, {$new_id200}, {$new_id79}, 0, 0)";
        $database->setQuery($query);
        $database->execute();
        $query = "INSERT INTO `#__survey_force_rules` (`id`, `quest_id`, `answer_id`, `next_quest_id`, `alt_field_id`, `priority`) VALUES (NULL, {$new_id75}, {$new_id201}, {$new_id79}, 0, 0)";
        $database->setQuery($query);
        $database->execute();
        
        $this->display();
    }

}
