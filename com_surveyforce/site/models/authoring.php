<?php

/**
 * Surveyforce Component for Joomla 3
 * @package   Surveyforce
 * @author    JoomPlace Team
 * @copyright Copyright (C) JoomPlace, www.joomplace.com
 * @license   GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.modellist');


class SurveyforceModelAuthoring extends JModelItem
{

	private $is_author = false;

	public function __construct()
	{
		$this->database = JFactory::getDbo();
		require_once JPATH_COMPONENT . DIRECTORY_SEPARATOR . 'helpers' . DIRECTORY_SEPARATOR . 'SFPageNav.class.php';
		require_once JPATH_COMPONENT . DIRECTORY_SEPARATOR . 'helpers' . DIRECTORY_SEPARATOR . 'surveyforce.class.php';
		require_once JPATH_COMPONENT . DIRECTORY_SEPARATOR . 'helpers' . DIRECTORY_SEPARATOR . 'component.lib.php';

		/*
		 * Get UserType
		 */
		if (!JFactory::getUser()->guest)
		{
			$this->database->setQuery("SELECT title FROM #__usergroups WHERE id IN (" . implode(',', JFactory::getUser()->getAuthorisedGroups()) . ")");
			$groupsNames = $this->database->loadColumn();
			JFactory::getUser()->set('usergroups', $groupsNames);

			if (in_array('Administrator', $groupsNames) || in_array('Super Users', $groupsNames))
				JFactory::getUser()->set('usertype', 'Super Administrator');
			else
				JFactory::getUser()->set('usertype', 'Registered');
		}

		parent::__construct();
	}

	public function getIs_author()
	{
		$user = JFactory::getUser();

		if (!$user->guest)
		{
			$database = JFactory::getDbo();
			$database->setQuery("SELECT * FROM #__survey_force_authors WHERE user_id = " . $user->id);
			$result = $database->loadResult();

			if (!$result)
			{
				$this->is_author = false;
				return false;
			}
			else
			{
				$this->is_author = true;
				return true;
			}
		}
		else
		{
			$this->is_author = false;
			return false;
		}
	}

	public function setUserState()
	{
	}

	public function getUserState()
	{
		$this->input = JFactory::getApplication()->input;
	}

	public function getPage()
	{
		$this->task = JFactory::getApplication()->input->get('task', 'surveys');
		ob_start();
		JLoader::register('SurveyforceEditHelper', JPATH_COMPONENT . DIRECTORY_SEPARATOR . 'helpers' . DIRECTORY_SEPARATOR . 'edit.surveyforce.php');

		switch ($this->task)
		{
			case 'start_invited':
				SurveyforceHelper::SF_ShowSurvey_Invited();
				break;
			case 'ajax_action':
				SurveyforceHelper::SF_analizeAjaxRequest();
				break;
			case 'insert_tag':
				die;
				break;

			default:
				if (SurveyforceHelper::SF_GetUserType() == 1 || SurveyforceHelper::SF_GetUserType() == 2)
				{
					$this->SF_analizeTask();
				}
				else
				{
					return false;
				}

				break;

		}

		$result = ob_get_contents();
		ob_clean();

		return trim($result);
	}

	public function _loadEditSurvey()
	{
		$user = JFactory::getUser();
		$db = JFactory::getDBO();
		$cid = mosGetParam($_REQUEST, 'cid', array(0));
		$base_url = JURI::root();

		$survey = '';
		if($cid[0]){
			
			$db->setQuery("SELECT * FROM `#__survey_force_survs` WHERE `id` = '".$cid[0]."'".(JFactory::getUser()->authorise( 'core.manage', 'com_surveyforce' )?"":" AND `sf_author` = '".$user->id."'"));
			$survey = $db->loadObject();

			$db->setQuery("SELECT * FROM `#__survey_force_quests` WHERE `sf_survey` = '".$cid[0]."' ORDER BY `id`");
			$questions = $db->loadObjectList();

			$db->setQuery("SELECT * FROM `#__survey_force_qsections` WHERE `sf_survey_id` = '".$cid[0]."' ORDER BY `ordering`");
			$qsections = $db->loadObjectList();
							
		}
		if(!$survey){
			$row = new stdClass;
			$row->id = '';
			$row->sf_name = 'New Survey';
			$row->sf_author = $user->id;
			$row->sf_template = 3;
			$row->sf_step = 1;

			$db->insertObject("#__survey_force_survs", $row, "id");
			$newID = $db->insertid();

			$db->setQuery("SELECT * FROM `#__survey_force_survs` WHERE `id` = '".$newID."'");
			$survey = $db->loadObject();

			$questions = $qsections = array();
		}

		if(count($questions)){
			foreach ($questions as $ii => $question) {
				
				$db->setQuery("SELECT * FROM `#__survey_force_quest_show` WHERE `survey_id` = '".$cid[0]."' AND `quest_id` = '".$question->id."'");
				$question->hides = $db->loadObjectList();

				$db->setQuery("SELECT * FROM `#__survey_force_rules` WHERE `quest_id` = '".$question->id."'");
				$question->rules = $db->loadObjectList();

				switch($question->sf_qtype){
					case '2':
					case '3':
						$question->answers = array();
						$db->setQuery("SELECT * FROM `#__survey_force_fields` WHERE `quest_id` = '".$question->id."' AND `is_main` = 1 ORDER BY `ordering`");
						$question->answers = $db->loadObjectList();

						$db->setQuery("SELECT * FROM `#__survey_force_fields` WHERE `quest_id` = '".$question->id."' AND `is_main` = 0");
						if(isset($question->answers[0]->sf_other)){
						$question->answers[0]->sf_other = $db->loadObject();
						}
					break;
					case '9':
					case '6':
						$question->answers = array();
						$db->setQuery("SELECT * FROM `#__survey_force_fields` WHERE `quest_id` = '".$question->id."' AND `is_main` = 1 ORDER BY `ordering`");
						$question->answers['left'] = $db->loadObjectList();

						$question->answers['right'] = array();
						if(count($question->answers['left'])){
							foreach ($question->answers['left'] as $left) {
								$db->setQuery("SELECT * FROM `#__survey_force_fields` WHERE `quest_id` = '".$question->id."' AND `is_main` = 0 AND `id` = '".$left->alt_field_id."'");
								$right = $db->loadObject();

								$question->answers['right'][] = $right;
							}
						}
					break;
					case '1':
					case '5':
						$question->answers = array();
						$db->setQuery("SELECT * FROM `#__survey_force_fields` WHERE `quest_id` = '".$question->id."' AND `is_main` = 1 ORDER BY `ordering`");
						$question->answers['options'] = $db->loadObjectList();

						$db->setQuery("SELECT * FROM `#__survey_force_fields` WHERE `quest_id` = '".$question->id."' AND `is_main` = 0 ORDER BY `ordering`");

						if($question->sf_qtype == 5){
							$question->answers['ranks'] = $db->loadObjectList();
						} else {

							$db->setQuery("SELECT * FROM `#__survey_force_scales` WHERE `quest_id` = '".$question->id."' ORDER BY `ordering`");

							$question->answers['scales'] = $db->loadObjectList();
						}

					break;
				}
			}
		}

		$result_section = array();
		if(count($qsections)){
			foreach ($qsections as $section) {
				$quest = new stdClass;
				$quest->id = $section->id;
				$quest->sf_qtext = $section->sf_name;
				$quest->sf_qtype = '10';
				$quest->published = '1';
				$quest->sf_compulsory = '0';
				$quest->sf_default_hided = '0';
				$quest->is_final_question = '0';
				$quest->sf_qstyle = '0';
				$quest->sf_impscale = '0';
				$quest->hides = array();
				$quest->rules = array();
				$quest->sections = array();

				$section_quests = array();
				$tmp_questions = $questions;
				$page_break = null;
				foreach ($tmp_questions as $n => $qst) {
					if($section->addname && $qst->sf_section_id == $section->id){
						$quest->sections[] = $qst->id;
						if($qst->sf_qtype == 8){
							$page_break = $qst;
						} else {
							$section_quests[] = $qst;
						}
						unset($questions[$n]);
					}

				}

				array_unshift($section_quests, $quest);
				if(isset($page_break) && $page_break){
					array_unshift($section_quests, $page_break);
				}
				$result_section = array_merge($result_section, $section_quests);
			}

			$result_section = array_merge($result_section, $questions);
		}

		$questions = (count($result_section)) ? $result_section : $questions;

		$db->setQuery("SELECT `id` as `value`, `sf_catname` as `text` FROM `#__survey_force_cats` WHERE `published` = '1'");
		$categories = $db->loadObjectList();

		$lists = array();
		$options = array();
		$options[] = JHTML::_('select.option', '0', '- Select category -');
		$categories = array_merge($options, $categories);

		$lists['categories'] = mosHTML::selectList($categories, 'sf_cat', 'class="selectpicker" id="sf_cat" size="1" ', 'value', 'text', $survey->sf_cat);

		$db->setQuery("SELECT `id` as `value`, `iscale_name` as `text` FROM `#__survey_force_iscales`");
		$i_scales = $db->loadObjectList();

		$options = array();
		$options[] = JHTML::_('select.option', '', '- Select scale -');
		$i_scales = array_merge($options, $i_scales);

		$lists['i_scales'] = mosHTML::selectList($i_scales, 'sf_iscale', 'class="selectpicker" id="sf_iscale" size="1" onchange="sfSetIscale(this.options[this.selectedIndex].value);"', 'value', 'text', '');

		$db->setQuery("SELECT `id` as value, `listname` as `text` FROM `#__survey_force_listusers`");
		$userlists = $db->loadObjectList();

		$options = array();
		$options[] = JHTML::_('select.option', '0', '- Select Userlist -');

		$userlists = array_merge($options, $userlists);

		$lists['userlists'] = mosHTML::selectList($userlists, 'sf_special', 'class="selectpicker" id="sf_special" size="1"', 'value', 'text', $survey->sf_special);

		include_once(JPATH_SITE.'/components/com_surveyforce/views/authoring/tmpl/authoring/survey.html.php');
		exit;
	}

	public function SF_analizeTask()
	{
		$id = intval(mosGetParam($_REQUEST, 'id', 0));
		$option = JFactory::getApplication()->input->get('option');

		$cid = mosGetParam($_REQUEST, 'cid', array(0));

		if (!is_array($cid))
		{
			$cid = array(0);
		}

		$sec = mosGetParam($_REQUEST, 'sec', array());
		if (!is_array($sec))
		{
			$sec = array(0);
		}
		elseif (count($sec) > 0)
		{
			$query = "SELECT id FROM #__survey_force_quests WHERE sf_section_id IN (" . implode(',', $sec) . ") ";
			$this->database->setQuery($query);
			$cid = array_merge($cid, $this->database->loadColumn());
		}

		$document = JFactory::getDocument();
		$document->addStyleSheet(JURI::root() . 'components/com_surveyforce/assets/css/surveyforce.css');
		$document->addStyleSheet(JURI::root() . 'components/com_surveyforce/assets/css/bootstrap.min.css');
		$document->addScript(JURI::root() . "components/com_surveyforce/assets/js/jquery-1.9.1.custom.min.js");
		$document->addScript(JURI::root() . "components/com_surveyforce/assets/js/surveyforce.js");
		

		$sf_config = JComponentHelper::getParams('com_surveyforce');
		if ($sf_config->get('sf_enable_jomsocial_integration'))
		{
			$query = "SELECT id FROM #__survey_force_authors WHERE user_id = '" . JFactory::getUser()->id . "'";
			$this->database->SetQuery($query);
			$a_id = $this->database->LoadResult();

			if (!$a_id || true)
			{
				$document = JFactory::getDocument();
				$document->addStyleSheet(JURI::root() . 'templates/system/css/system.css');
				$document->addStyleSheet(JURI::root() . 'templates/system/css/general.css');
				$_REQUEST['tmpl'] = 'component';
			}
		}

		// TODO: check questions permissions
		// Check Survey permissions
		$break = false;
		$surv_id = JFactory::getApplication()->input->get('surv_id');
		if ($surv_id)
			if (SurveyforceHelper::SF_GetUserType($surv_id) != 1)
			{
				echo SurveyforceTemplates::Survey_blocked(false,'_user_type_problems');
				$break = true;
			}

		SurveyforceEditHelper::clearPreviews();

		header("Cache-Control: no-cache, no-store, must-revalidate"); // HTTP 1.1.
		header("Pragma: no-cache"); // HTTP 1.0.
		header("Expires: 0"); // Proxies.

		if (!$break)
		{
			switch (JFactory::getApplication()->input->get('task', 'surveys'))
			{
				case 'uploadimage':
					SurveyforceEditHelper::SF_uploadImage($option);
					break;
				# --- CATEGORIES --- #
				case 'categories':
					SurveyforceEditHelper::SF_ListCategories($option);
					break;
				case 'add_cat':
					SurveyforceEditHelper::SF_editCategory('0', $option);
					break;
				case 'edit_cat':
					SurveyforceEditHelper::SF_editCategory(intval($cid[0]), $option);
					break;
				case 'editA_cat':
					SurveyforceEditHelper::SF_editCategory($id, $option);
					break;
				case 'apply_cat':
				case 'save_cat':
					SurveyforceEditHelper::SF_saveCategory($option);
					break;
				case 'del_cat':
					SurveyforceEditHelper::SF_removeCategory($cid, $option);
					break;
				case 'cancel_cat':
					SurveyforceEditHelper::SF_cancelCategory($option);
					break;
				# ---   SURVEYS  --- #
				case 'surveys':
				default:
					SurveyforceEditHelper::SF_ListSurveys($option);
					break;
				case 'add_surv':
					SurveyforceEditHelper::SF_editSurvey('0', $option);
					break;
				case 'edit_surv':
					self::_loadEditSurvey();
					break;
				case 'editA_surv':
					SurveyforceEditHelper::SF_editSurvey($id, $option);
					break;
				case 'apply_surv':
				case 'save_surv':
					SurveyforceEditHelper::SF_saveSurvey($option);
					break;
				case 'del_surv':
					SurveyforceEditHelper::SF_removeSurvey($cid, $option);
					break;
				case 'cancel_surv':
					SurveyforceEditHelper::SF_cancelSurvey($option);
					break;
				case 'publish_surv':
					SurveyforceEditHelper::SF_changeSurvey($cid, 1, $option);
					break;
				case 'unpublish_surv':
					SurveyforceEditHelper::SF_changeSurvey($cid, 0, $option);
					break;
				case 'move_surv_sel':
					SurveyforceEditHelper::SF_moveSurveySelect($option, $cid);
					break;
				case 'move_surv_save':
					SurveyforceEditHelper::SF_moveSurveySave($cid);
					break;
				case 'copy_surv_sel':
					SurveyforceEditHelper::SF_moveSurveySelect($option, $cid);
					break;
				case 'copy_surv_save':
					SurveyforceEditHelper::SF_copySurveySave($cid);
					break;
				case 'show_results':
					SurveyforceEditHelper::SF_show_results(intval($cid[0]), $option);
					break;
				case 'preview_survey':
					SurveyforceEditHelper::SF_preview_survey(intval($cid[0]), $option);
					break;

				# ---  QUESTIONS  --- #
				case 'publish_quest':
					echo(SurveyforceHelper::SF_GetUserType(0, $cid) != 1 ? SurveyforceTemplates::Survey_blocked() : SurveyforceEditHelper::SF_changeQuestion($cid, 1, $option));
					break;
				case 'unpublish_quest':
					echo(SurveyforceHelper::SF_GetUserType(0, $cid) != 1 ? SurveyforceTemplates::Survey_blocked() : SurveyforceEditHelper::SF_changeQuestion($cid, 0, $option));
					break;
				case 'questions':
					SurveyforceEditHelper::SF_ListQuestions($option);
					break;
				case 'new_question_type':
					SurveyforceEditHelper::SF_new_question_type();
					break;
				case 'add_new_section':
					SurveyforceEditHelper::SF_editSection('0', $option);
					break;
				case 'editA_sec':
					SurveyforceEditHelper::SF_editSection($id, $option);
					break;
				case 'apply_section':
				case 'save_section':
					SurveyforceEditHelper::SF_saveSection($option);
					break;
				case 'cancel_section':
					mosRedirect(SFRoute("index.php?option=com_surveyforce&task=questions"));
					break;
				case 'add_new':
					$new_qtype_id = intval(JApplication::getUserStateFromRequest("new_qtype_id", 'new_qtype_id', 0));
					SurveyforceEditHelper::SF_editQuestion('0', $option, $new_qtype_id);
					break;
				case 'add_ranking':
					SurveyforceEditHelper::SF_editQuestion('0', $option, 9);
					break;
				case 'add_pagebreak':
					SurveyforceEditHelper::SF_editQuestion('0', $option, 8);
					break;
				case 'add_boilerplate':
					SurveyforceEditHelper::SF_editQuestion('0', $option, 7);
					break;
				case 'add_likert':
					SurveyforceEditHelper::SF_editQuestion('0', $option, 1);
					break;
				case 'add_pickone':
					SurveyforceEditHelper::SF_editQuestion('0', $option, 2);
					break;
				case 'add_pickmany':
					SurveyforceEditHelper::SF_editQuestion('0', $option, 3);
					break;
				case 'add_short':
					SurveyforceEditHelper::SF_editQuestion('0', $option, 4);
					break;
				case 'add_drp_dwn':
					SurveyforceEditHelper::SF_editQuestion('0', $option, 5);
					break;
				case 'add_drg_drp':
					SurveyforceEditHelper::SF_editQuestion('0', $option, 6);
					break;
				case 'set_default':
					echo(SurveyforceHelper::SF_GetUserType(0, $id) != 1 ? SurveyforceTemplates::Survey_blocked() : SurveyforceEditHelper::SF_setDefault($id, $option));
					break;
				case 'save_default':
					echo(SurveyforceHelper::SF_GetUserType(0, $id) != 1 ? SurveyforceTemplates::Survey_blocked() : SurveyforceEditHelper::SF_saveDefault($id, $option));
					break;
				case 'cancel_default':
					SurveyforceEditHelper::SF_cancelDefault($id, $option);
					break;
				case 'edit_quest':
					if (isset($cid[0]) && intval($cid[0]) > 0)
					{
						echo(SurveyforceHelper::SF_GetUserType(0, (int) $cid[0]) != 1 ? SurveyforceTemplates::Survey_blocked() :
							SurveyforceEditHelper::SF_editQuestion(intval($cid[0]), $option));
						break;
					}
					if (isset($sec[0]) && intval($sec[0]) > 0)
					{
						mosRedirect(SFRoute("index.php?option=com_surveyforce&task=editA_sec&hidemainmenu=1&id=" . intval($sec[0])));
						break;
					}
					break;
				case 'editA_quest':
					echo(SurveyforceHelper::SF_GetUserType(0, $id) != 1 ? SurveyforceTemplates::Survey_blocked() : SurveyforceEditHelper::SF_editQuestion($id, $option));
					break;
				case 'apply_quest':
				case 'save_quest':
					SurveyforceEditHelper::SF_saveQuestion($option);
					break;
				case 'del_quest':
					echo(SurveyforceHelper::SF_GetUserType(0, $cid) != 1 ? SurveyforceTemplates::Survey_blocked() : SurveyforceEditHelper::SF_removeQuestion($cid, $sec, $option));
					break;
				case 'cancel_quest':
					SurveyforceEditHelper::SF_cancelQuestion($option);
					break;
				case 'orderup':
					SurveyforceEditHelper::SF_orderQuestion(intval($cid[0]), -1, $option);
					break;
				case 'orderdown':
					SurveyforceEditHelper::SF_orderQuestion(intval($cid[0]), 1, $option);
					break;
				case 'orderupS':
					SurveyforceEditHelper::SF_orderSection(intval($sec[0]), -1, $option);
					break;
				case 'orderdownS':
					SurveyforceEditHelper::SF_orderSection(intval($sec[0]), 1, $option);
					break;
				case 'saveorder':
					SurveyforceEditHelper::SF_saveOrderQuestion($cid, $sec);
					break;
				case 'move_quest_sel':
					SurveyforceEditHelper::SF_moveQuestionSelect($option, $cid, $sec);
					break;
				case 'move_quest_save':
					SurveyforceEditHelper::SF_moveQuestionSave($cid, $sec);
					break;
				case 'copy_quest_sel':
					SurveyforceEditHelper::SF_moveQuestionSelect($option, $cid, $sec);
					break;
				case 'copy_quest_save':
					SurveyforceEditHelper::SF_copyQuestionSave($cid, 0, 0, $sec);
					break;
				case 'add_iscale_from_quest':
					JFactory::getSession()->set('quest_redir', intval(mosGetParam($_REQUEST, 'quest_id', 0)));
					JFactory::getSession()->set('task_redir', strval(mosGetParam($_REQUEST, 'red_task', '')));
					SurveyforceEditHelper::SF_editIScale('0', $option);
					break;

				case 'save_iscale_A':
					SurveyforceEditHelper::SF_saveIScale($option);
					break;
				case 'cancel_iscale_A':
					SurveyforceEditHelper::SF_cancelIScale($option);
					break;
				### USERGROUPS ###
				case 'usergroups':
					SurveyforceEditHelper::SF_manageUsers($option);
					break;
				case 'add_list':
					SurveyforceEditHelper::SF_editUsergroup(0, $option);
					break;
				case 'edit_list':
					SurveyforceEditHelper::SF_editUsergroup(intval($cid[0]), $option);
					break;
				case 'save_list':
				case 'apply_list':
					SurveyforceEditHelper::SF_saveUsergroup($cid, $option);
					break;
				case 'del_list':
					SurveyforceEditHelper::SF_delUsergroup($cid, $option);
					break;
				case 'cancel_list':
					mosRedirect(SFRoute("index.php?option=com_surveyforce&task=usergroups"));
					break;
				case 'view_users':
					SurveyforceEditHelper::SF_viewUsers($option);
					break;
				case 'add_user':
					SurveyforceEditHelper::SF_addUser2Group($option);
					break;
				case 'save_user':
					SurveyforceEditHelper::SF_saveUsergroup($cid, $option);
					break;
				case 'del_user':
					SurveyforceEditHelper::SF_delUserFromGroup($cid, $option);
					break;
				case 'cancel_user':
					SurveyforceEditHelper::SF_cancelViewUsers($option);
					break;

				# ---  REPORTS  --- #
				case 'reports':
					SurveyforceEditHelper::SF_ViewReports($option);
					break;
				case 'rep_pdf':
					SurveyforceEditHelper::SF_ViewReportsPDF_full($option, $cid);
					break;
				case 'rep_csv':
					SurveyforceEditHelper::SF_ViewReportsCSV_full($option, $cid);
					break;
				case 'del_rep':
					SurveyforceEditHelper::SF_removeRep($cid, $option);
					break;
				case 'view_result':
					SurveyforceEditHelper::SF_ViewRepResult($id, $option);
					break;
				case 'view_result_c':
					SurveyforceEditHelper::SF_ViewRepResult(intval($cid[0]), $option);
					break;
				case 'rep_surv':
					SurveyforceEditHelper::SF_ListSurveys($option);
					break;
				case 'view_rep_surv':
					SurveyforceEditHelper::SF_ViewRepSurv(intval($cid[0]), $option);
					break;
				case 'view_rep_survA':
					SurveyforceEditHelper::SF_ViewRepSurv($id, $option);
					break;
				case 'rep_surv_print':
					SurveyforceEditHelper::SF_ViewRepSurv($id, $option, 1);
					break;
				case 'rep_print':
					SurveyforceEditHelper::SF_ViewRepResult($id, $option, 1);
					break;
				case 'rep_list':
					SurveyforceEditHelper::SF_manageUsers($option);
					break;
				case 'view_rep_list':
					SurveyforceEditHelper::SF_ViewRepList(intval($cid[0]), $option);
					break;
				case 'view_rep_listA':
					SurveyforceEditHelper::SF_ViewRepList($id, $option);
					break;
				case 'rep_list_print':
					SurveyforceEditHelper::SF_ViewRepList($id, $option, 1);
					break;

				case 'i_report':
					SurveyforceEditHelper::SF_ListSurveys($option, true);
					break;
				case 'view_irep_surv':
					SurveyforceEditHelper::SF_ViewIRepSurv(intval($cid[0]), $option);
					break;
				case 'cross_rep':
					SurveyforceEditHelper::SF_showCrossReport($option);
					break;
				case 'get_cross_rep':
					SurveyforceEditHelper::SF_getCrossReport($option);
					break;
				case 'get_options':
					SurveyforceEditHelper::SF_getOptions();
					break;

				# ---	EMAILS	 --- #
				case 'emails':
					SurveyforceEditHelper::SF_ListEmails($option);
					break;
				case 'add_email':
					SurveyforceEditHelper::SF_editEmail('0', $option);
					break;
				case 'edit_email':
					SurveyforceEditHelper::SF_editEmail(intval($cid[0]), $option);
					break;
				case 'editA_email':
					SurveyforceEditHelper::SF_editEmail($id, $option);
					break;
				case 'apply_email':
				case 'save_email':
					SurveyforceEditHelper::SF_saveEmail($option);
					break;
				case 'del_email':
					SurveyforceEditHelper::SF_removeEmail($cid, $option);
					break;
				case 'cancel_email':
					SurveyforceEditHelper::SF_cancelEmail($option);
					break;
				# --- INVITATIONS --- #
				case 'generate_invitations':
					SurveyforceEditHelper::SF_genInvitations($option);
					break;
				case 'make_inv_list':
					SurveyforceEditHelper::SF_makeInvList();
					break;
				case 'invite_users':
					SurveyforceEditHelper::SF_inviteUsers(intval($cid[0]), $option);
					break;
				case 'remind_users':
					SurveyforceEditHelper::SF_remindUsers(intval($cid[0]), $option);
					break;
				# --- TASKS from IFRAME	--- #
				case 'invitation_start':
					SurveyforceEditHelper::SF_startInvitation($option);
					break;
				case 'invitation_stop':
					@ob_end_clean();
					die();
					break;
				case 'remind_start':
					SurveyforceEditHelper::SF_startRemind($option);
					break;
				case 'remind_stop':
					@ob_end_clean();
					die();
					break;
			}
		}
	}

}
