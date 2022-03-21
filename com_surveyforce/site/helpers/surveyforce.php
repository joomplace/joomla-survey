<?php
/**
 * SurveyForce Delux Component for Joomla 3
 * @package   Surveyforce
 * @author    JoomPlace Team
 * @copyright Copyright (C) JoomPlace, www.joomplace.com
 * @license   GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */
defined('_JEXEC') or die;

if (!defined('_SEL_CATEGORY')) define('_SEL_CATEGORY', '- ' . JText::_('COM_SURVEYFORCE_SELECT_CATEGORY') . ' -');
if (!defined('_CMN_NEW_ITEM_FIRST')) define('_CMN_NEW_ITEM_FIRST', JText::_('COM_SURVEYFORCE_NEW_ITEMS_DEFAULT_TO_THE_FIRST_PLACE'));
if (!defined('_PDF_GENERATED')) define('_PDF_GENERATED', JText::_('COM_SURVEYFORCE_GENERATED'));
if (!defined('_CURRENT_SERVER_TIME_FORMAT')) define('_CURRENT_SERVER_TIME_FORMAT', 'Y-m-d H:i:s');
if (!defined('_CURRENT_SERVER_TIME')) define('_CURRENT_SERVER_TIME', JFactory::getDate());
if (!defined('_PN_DISPLAY_NR')) define('_PN_DISPLAY_NR', JText::_('COM_SURVEYFORCE_DISPLAY'));
if (!defined('_SURVEY_FORCE_ADMIN_HOME')) define('_SURVEY_FORCE_ADMIN_HOME', JPATH_COMPONENT_ADMINISTRATOR);
if (!defined('_SURVEY_FORCE_COMP_NAME')) define('_SURVEY_FORCE_COMP_NAME', JText::_('COM_SURVEYFORCE_SURVEYFORCE_DELUXE_VER') . survey_version);

// Component Helper
jimport('joomla.application.component.helper');

if (!function_exists('mosGetParam'))
{
	function mosGetParam($arrayVal, $varName, $defaultVal = '')
	{
		return JFactory::getApplication()->input->getArray($arrayVal, $varName, $defaultVal);
	}
}

function SFRoute($url, $xhtml = null)
{
	return JRoute::_($url, false);
}

class SurveyforceHelper
{
	protected $document;

	public function __construct()
	{
		$this->document = JFactory::getDocument();
	}

	public static function SF_processGetField($field_text)
	{
		$field_text = str_replace('"', '&quot;', $field_text);
		$field_text = str_replace("'", '&#039;', $field_text);
		return $field_text;
	}

	public static function getQuestionType($new_qtype_id)
	{
		$db = JFactory::getDBO();

		$query = $db->getQuery(true)
			->select('*')
			->from('`#__survey_force_qtypes`')
			->where('`id` = ' . $new_qtype_id);
		$db->setQuery($query);
		return $db->loadObject();
	}
	
	public static function isDisabledPlugin($types)
	{
		foreach($types as $type){
			if (!JPluginHelper::isEnabled('survey',$type->sf_plg_name)){
				return $type->sf_plg_name;
			}
		}
		return false;
	}

	/**
	 * Get an authorization user tyype
	 *
	 * Returns int value:
	 * 1 - owner/admin
	 * 2 - lms teacher/author
	 * 3 - other
	 * 0 - not logged
	 *
	 * @return  INT
	 */
	public static function SF_GetUserType($survey_id = 0, $question_id = 0)
	{
		$database = JFactory::getDbo();

		if (JFactory::getUser()->guest)
			return 0;

		$user_id = JFactory::getUser()->id;

		if (!($user_id > 0))
			return 0;

		if (JFactory::getUser()->get('usertype') == 'Super Administrator')
			return 1;

		if (!$survey_id && $question_id)
		{
			if (!is_array($question_id))
			{
				$query = $database->getQuery(true)
					->select('`sf_survey`')
					->from('`#__survey_force_quests`')
					->where('`id` = ' . (int) $question_id);
				$database->setQuery($query);
				$survey_id = $database->loadResult();
			}
			else
			{
				$query = $database->getQuery(true)
					->select('`sf_survey`')
					->from('`#__survey_force_quests`')
					->where('`id` IN (' . implode(',', $question_id) . ')');
				$database->setQuery($query);
				$survey_id = $database->loadResult();
			}
		}

		$sf_config = JComponentHelper::getParams('com_surveyforce');
		$enable_lms_integration = $sf_config->get('sf_enable_lms_integration');
		$sf_enable_jomsocial_integration = $sf_config->get('sf_enable_jomsocial_integration');

		$is_lms = (file_exists(JPATH_SITE . '/components/com_joomla_lms/joomla_lms.php') && $enable_lms_integration ? true : false);
		$is_jomsocial = (file_exists(JPATH_SITE . '/components/com_community/community.php') && $sf_enable_jomsocial_integration ? true : false);

		if ($is_lms)
		{
			$query = $database->getQuery(true)
				->select('`lms_usertype_id`')
				->from('`#__lms_users`')
				->where('`user_id` = "' . $user_id . '"');
			$database->setQuery($query);
			$lms_usertype = $database->loadResult();

			if ($survey_id < 1)
			{
				if ($lms_usertype == 1 || $lms_usertype == 5)
					return 2;
			}
			else
			{
				$query = $database->getQuery(true)
					->select("`sf_author`")
					->from("`#__survey_force_survs`")
					->where("`id` = '" . $survey_id . "'");
				$database->setQuery($query);
				$author_id = $database->loadResult();

				if ($author_id == $user_id && ($lms_usertype == 1 || $lms_usertype == 5))
					return 1;
				elseif ($author_id != $user_id && ($lms_usertype == 1 || $lms_usertype == 5))
					return 2;
			}
		}

		$query = $database->getQuery(true)
			->select("`id`")
			->from("`#__survey_force_authors`")
			->where("`user_id` = '" . $user_id . "'");
		$database->setQuery($query);
		$is_author = (int) $database->loadResult();

		if ($survey_id < 1)
		{
			if ($is_author || ($is_jomsocial && $user_id))
				return 2;
		}
		else
		{
			$query = $database->getQuery(true)
				->select("`sf_author`")
				->from("`#__survey_force_survs`")
				->where("`id` = '" . $survey_id . "'");
			$database->setQuery($query);
			$author_id = $database->loadResult();

			if ($author_id == $user_id && ($is_author || ($is_jomsocial && $user_id)))
				return 1;
			elseif ($author_id != $user_id && ($is_author || ($is_jomsocial && $user_id)))
				return 2;
		}
		return 3;
	}

	public static function getTemplate($data)
	{
		$db = JFactory::getDbo();

		$template_id = (empty($data['survey']->sf_template) ? 1 : $data['survey']->sf_template);

		$query = $db->getQuery(true)
			->select("`sf_name`")
			->from("`#__survey_force_templates`")
			->where("`id` = " . (isset($template_id) ? $template_id : 0));
		$db->setQuery($query);

		return $db->loadResult();
	}

	public static function getNotifyUserEmails()
	{
		$access = JAccess::getAssetRules('com_surveyforce')->getData();

		if (!sizeof($access))
			return array();
		
		$tacc = $access['core.notify']->getData();
		$gids = $users = $userlist = $emails = array();
		if (is_array($tacc) && sizeof($tacc) > 0)
		{
			foreach ($tacc as $key => $acc)
			{
				if ($acc == 1)
				{
					$gids[] = $key;
				}
			}
		}

		if (sizeof($gids) > 0)
		{
			foreach ($gids as $gid)
			{
				$userlist = JAccess::getUsersByGroup($gid);
				if (sizeof($userlist) > 0)
				{
					foreach ($userlist as $usl)
					{
						$usl ? $users[] = $usl : '';
					}
				}
			}
		}

		if (sizeof($users) > 0)
		{
			$users = array_unique($users);
			foreach ($users as $usid)
			{
				$user = JFactory::getUser($usid);
				$emails[] = $user->email;
			}
		}

		return $emails;
	}

	public static function SF_load_template($template_name, $type = '')
	{
		require_once(JPATH_SITE . '/components/com_surveyforce/helpers/templates.php');
		new SurveyforceTemplates($template_name, $type);
	}

	public static function getSettings()
	{
		$settings = JFactory::getApplication()->getParams();

		if ($settings->get('use_cb') || $settings->get('use_jsoc'))
		{
			if (!file_exists(JPATH_SITE . '/administrator/components/com_comprofiler/admin.comprofiler.php'))
			{
				$settings->set('use_cb', 0);
			}
			if (!file_exists(JPATH_SITE . '/administrator/components/com_community/admin.community.php'))
			{
				$settings->set('use_jsoc', 0);
			}
		}

		return $settings;
	}

	public static function sfPrepareText($text, $force_compatibility = false)
	{
		// Black list of mambots:
		$banned_bots = array();
		$row = new stdclass();

		$row->id = null;
		$row->text = $text;
		$row->introtext = '';
		$params = JComponentHelper::getParams('com_surveyforce');
		$new_text = $text;

		$dispatcher = JDispatcher::getInstance();

		JPluginHelper::importPlugin('content');
		$results = $dispatcher->trigger('onContentPrepare', array('com_surveyforce', &$row, &$params, 0));
		$results = $dispatcher->trigger('onPrepareContent', array(& $row, & $params, 0));

		$new_text = $row->text;

		return $new_text;
	}

	public static function create_chain($survey_id)
	{
		$database = JFactory::getDbo();

		$query = $database->getQuery(true)
			->select("*")
			->from("`#__survey_force_survs`")
			->where("`id` = '" . $survey_id . "'");
		$database->setQuery($query);
		$res = $database->loadObjectList();
		$survey = ($res == null) ? array() : $res;	
		$survey = $survey[0];

		$chain = '';

		if ($survey)
		{
			$auto_pb = $survey->sf_auto_pb;
			$chaintype = $survey->sf_random;
			
			$query = $database->getQuery(true)
				->select("`q`.*")
				->select("IFNULL(`qs`.`ordering`,999999) AS `section_ordering`")
				->from("`#__survey_force_quests` AS `q`")
				->join("LEFT", "`#__survey_force_qsections` AS `qs` ON `qs`.`id` = `q`.`sf_section_id`")
				->where("`q`.`published` = 1 AND `q`.`sf_survey` = '" . $survey_id . "' " . ($auto_pb ? " AND `q`.`sf_qtype` <> 8 " : "") . "")
				->order("`section_ordering`,`q`.`ordering`, `q`.`id`");
			$database->setQuery($query);
			$q_data = $database->loadObjectList();

			for ($i = 0, $n = count($q_data); $i < $n; $i++)
			{
				if ($q_data[$i]->sf_qtype == 8)
				{
					$chain .= '#';
				}
				elseif ($q_data[$i]->sf_qtype != 8)
				{
					$chain .= $q_data[$i]->id;
				}

				if ($auto_pb && ($i + 1) < $n)
					$chain .= '*#';

				if (($i + 1) < $n)
					$chain .= '*';
			}

			if (mb_substr($chain, -2) == '*#')
			{
				$chain = mb_substr($chain, 0, -2);
			}

			if (mb_substr($chain, -3) == '*#*')
			{
				$chain = mb_substr($chain, 0, -3);
			}

			if (mb_substr($chain, -1) == '*')
			{
				$chain = mb_substr($chain, 0, -1);
			}

			if (mb_substr($chain, -1) == '#')
			{
				$chain = mb_substr($chain, 0, -1);
			}

			if ($chaintype == 1)
			{ // random pages
				$pages = explode('*#*', $chain);
				srand((float) microtime() * 1000000);
				shuffle($pages);
				$chain = implode("*#*", $pages);
			}
			elseif ($chaintype == 2)
			{ //randon questions in pages
				$pages = explode('*#*', $chain);
				for ($j = 0, $m = count($pages); $j < $m; $j++)
				{
					$page = explode('*', $pages[$j]);
					srand((float) microtime() * 1000000);
					shuffle($page);
					$pages[$j] = implode("*", $page);
				}
				$chain = implode("*#*", $pages);
			}
			elseif ($chaintype == 3)
			{ //randon questions in page and pages
				$pages = explode('*#*', $chain);
				for ($j = 0, $m = count($pages); $j < $m; $j++)
				{
					$page = explode('*', $pages[$j]);
					srand((float) microtime() * 1000000);
					shuffle($page);
					$pages[$j] = implode("*", $page);
				}
				srand((float) microtime() * 1000000);
				shuffle($pages);
				$chain = implode("*#*", $pages);
			}
		}

		return $chain;
	}

	public static function clear_chain($chain, $not_shown)
	{
		$new_chain = array();
		$pages = explode('*#*', $chain);

		for ($j = 0, $m = count($pages); $j < $m; $j++)
		{
			$page = explode('*', $pages[$j]);
			$page = array_diff($page, $not_shown);
			if (count($page))
				$new_chain[] = implode("*", $page);
		}

		return implode("*#*", $new_chain);
	}

	public static function SF_analizeAjaxRequest()
	{
		// Get the application.
		$app = JFactory::getApplication('site');

		$sf_task = mosGetParam($_REQUEST, 'action', '');

		require_once(dirname(__FILE__) . '/survey.php');

		$limit = mosGetParam($_REQUEST, 'limit', 0);
		$page = mosGetParam($_REQUEST, 'count', 0);
		$survey_id = mosGetParam($_REQUEST, 'survey', 0);
		$pagination = mosGetParam($_REQUEST, 'pagination', 0);

		SF_process_ajax($sf_task, $limit, $page, $survey_id, $pagination);
		
		$app->close();
	}

	public function SF_ShowSurvey($survey_id = null)
	{
		$database = JFactory::getDbo();
		$app = JFactory::getApplication();
		$template = $app->input->getInt('survey_template', 0);

		$database->setQuery('SELECT * FROM #__survey_force_survs WHERE id=' . $survey_id);

		$survey = $database->loadObject();
		$preview = $app->input->get('preview', 0);

		$sf_config = JComponentHelper::getParams('com_surveyforce');
		if(!$survey){
			$survey = new stdClass();
		}
		$survey->is_complete = 1;

		$survey->sf_descr = @SurveyforceHelper::sfPrepareText($survey->sf_descr);
		$survey->surv_short_descr = @SurveyforceHelper::sfPrepareText($survey->surv_short_descr);

		if ($template > 0)
			$survey->sf_template = $template;

		$query = "SELECT `sf_name` FROM `#__survey_force_templates` WHERE `id` = '{$survey->sf_template}' ";
		$database->setQuery($query);
		$survey->template = $database->loadResult();

		if (JFactory::getUser()->id)
		{
			$query = "SELECT 1 FROM `#__survey_force_user_starts` WHERE survey_id = {$survey_id} AND user_id = '" . JFactory::getUser()->id . "' AND is_complete = 1 ORDER BY id DESC";
			$database->setQuery($query);
			$survey->is_complete = (int) $database->loadResult();
		}
		elseif ($survey->sf_pub_control > 0)
		{
			$ip = $_SERVER["REMOTE_ADDR"];
            $cookie = \JFactory::getApplication()->input->cookie->get(md5('survey' . $survey->id), '');

			if ($survey->sf_pub_control == 1)
			{
				$query = "SELECT 1 FROM `#__survey_force_user_starts` WHERE survey_id = {$survey_id} AND user_id = '0' AND `sf_ip_address` = '{$ip}' AND is_complete = 1  ORDER BY id DESC";
			}
			elseif ($survey->sf_pub_control == 2)
			{
				$query = "SELECT 1 FROM `#__survey_force_user_starts` WHERE survey_id = {$survey_id} AND user_id = '0' AND `unique_id` = '{$cookie}' AND is_complete = 1  ORDER BY id DESC";
			}
			elseif ($survey->sf_pub_control == 3)
			{
				$query = "SELECT 1 FROM `#__survey_force_user_starts` WHERE survey_id = {$survey_id} AND user_id = '0' AND `unique_id` = '{$cookie}' AND `sf_ip_address` = '{$ip}' AND is_complete = 1  ORDER BY id DESC";
			}
			$database->setQuery($query);
			$survey->is_complete = (int) $database->loadResult();
		}

		$query = " SELECT * FROM `#__survey_force_quest_show` WHERE `survey_id` = '" . $survey->id . "' ";
		$database->setQuery($query);
		$rules = $database->loadObjectList();

		if ($preview)
		{
			$query = "SELECT `id` FROM `#__survey_force_previews` WHERE `preview_id` = '" . $preview . "'";
			$database->setQuery($query);

			if ($database->loadResult())
			{
				$query = "DELETE FROM `#__survey_force_previews` WHERE `preview_id` = '" . $preview . "'";
				$database->setQuery($query);
				$database->execute();

				$survey->is_complete = 0;
				$query = " SELECT sf_qtype FROM #__survey_force_quests WHERE published = 1 AND sf_survey = {$survey->id} ORDER BY ordering, id ";
				$database->setQuery($query);
				$q_data = $database->loadColumn();
				for ($i = 0, $n = count($q_data); $i < $n; $i++)
				{
					if ($survey->sf_auto_pb == 0 && $q_data[$i] != 8 && isset($q_data[$i + 1]) && $q_data[$i + 1] != 8)
						$survey->sf_image = '';
				}

				return array('survey' => $survey, 'sf_config' => $sf_config, 'is_invited' => 0, 'invite_num' => '', 'rules' => $rules, 'preview' => $preview);
			}
			else
			{
				$survey->error = 'bloked';
				$survey->message = SurveyforceTemplates::Survey_blocked($sf_config,'_preview_id_not_found');
				return array('survey' => $survey, 'sf_config' => $sf_config, 'is_invited' => 0, 'invite_num' => '', 'rules' => $rules, 'preview' => $preview);
			}
		}

		$now = strtotime(JFactory::getDate());

		if ($survey->published
            && ((strtotime($survey->sf_date_expired) >= $now || $survey->sf_date_expired == '0000-00-00 00:00:00')
                    && (strtotime($survey->sf_date_started) <= $now || $survey->sf_date_started == '0000-00-00 00:00:00'))
        ) {

			$query = " SELECT sf_qtype FROM #__survey_force_quests WHERE published = 1 AND sf_survey = {$survey->id} ORDER BY ordering, id ";
			$database->setQuery($query);
			$q_data = $database->loadColumn();
			for ($i = 0, $n = count($q_data); $i < $n; $i++)
			{
				if ($survey->sf_auto_pb == 0 && $q_data[$i] != 8 && isset($q_data[$i + 1]) && $q_data[$i + 1] != 8)
					$survey->sf_image = '';
			}

			$sf_special = false;
			if ((JFactory::getUser()->id) && ($survey->sf_special))
			{
				$query = "SELECT COUNT(*) FROM #__survey_force_users AS a "
					. "\n WHERE a.list_id IN ({$survey->sf_special}) "
					. "\n AND a.lastname = ".$database->quote(JFactory::getUser()->name)." AND a.email = ".$database->quote(JFactory::getUser()->email);
				$database->setQuery($query);
				if ($database->loadResult() > 0)
					$sf_special = true;
				elseif (SurveyforceHelper::SF_GetUserType($survey->id) == 1)
					$sf_special = true;
			}

		$query = "SELECT * FROM #__extensions WHERE name = 'com_community' AND type = 'component'";
		$database->setQuery($query);
		$isInstolled = $database->loadObject();
		
		$friends = array();
		if ($sf_config->get('sf_enable_jomsocial_integration') && !empty($isInstolled))
			{
				$query = "SELECT j.connect_to FROM #__community_connection AS j WHERE j.status = 1 AND j.connect_from = '{$survey->sf_author}'";
				$database->setQuery($query);
				$friends = $database->loadColumn();
			}

			if ($survey->sf_invite)
			{
				$session = JFactory::getSession();
				$invite_num = JFactory::getApplication()->input->getString('invite', '');
                $session->set('invite_num', $invite_num);

                $no_invited_error_access = false;
                if(JFactory::getUser()->id) {
                    if($survey->sf_reg || $survey->sf_public) {
                        $no_invited_error_access = false;
                    } else {
                        $no_invited_error_access = true;
                    }
                } else {
                    if($survey->sf_public) {
                        $no_invited_error_access = false;
                    } else {
                        $no_invited_error_access = true;
                    }
                }

                if($invite_num || $no_invited_error_access === false) {
                    return array('survey' => $survey, 'sf_config' => $sf_config, 'is_invited' => 1, 'invite_num' => $invite_num, 'rules' => $rules, 'preview' => $preview);
                } else {
                    $survey->error = 'blocked';
                    $survey->message = SurveyforceTemplates::Survey_blocked($sf_config,'_user_has_no_right_or_invitation');
                    return array('survey' => $survey, 'sf_config' => $sf_config, 'is_invited' => 0, 'invite_num' => '', 'rules' => $rules, 'preview' => $preview);
                }
			}
			elseif ($sf_special)
			{

				$survey->is_complete = 0;
				return array('survey' => $survey, 'sf_config' => $sf_config, 'is_invited' => 0, 'invite_num' => '', 'rules' => $rules, 'preview' => $preview);

			}
			elseif (((JFactory::getUser()->id) && ($survey->sf_reg)) ||
				((JFactory::getUser()->id) && ($survey->sf_friend) && $sf_config->get('sf_enable_jomsocial_integration') && in_array(JFactory::getUser()->id, $friends)) ||
				($survey->sf_public) ||
				(JFactory::getUser()->id && SurveyforceHelper::SF_GetUserType($survey->id) == 1) ||
				($survey->id && JFactory::getUser()->id && SurveyforceHelper::SF_GetUserType($survey->id) == 1)
			)
			{

				return array('survey' => $survey, 'sf_config' => $sf_config, 'is_invited' => 0, 'invite_num' => '', 'rules' => $rules, 'preview' => $preview);

			}
			elseif (!$survey->id && JFactory::getUser()->id && (SurveyforceHelper::SF_GetUserType() == 1 || SurveyforceHelper::SF_GetUserType() == 2))
			{

				mosRedirect(JRoute::_('index.php?option=com_surveyforce&view=authoring'));
			}
			else
			{
				$survey->error = 'blocked';
				$survey->message = SurveyforceTemplates::Survey_blocked($sf_config,'_user_has_no_right_or_invitation');
				return array('survey' => $survey, 'sf_config' => $sf_config, 'is_invited' => 0, 'invite_num' => '', 'rules' => $rules, 'preview' => $preview);
			}
		}
		else
		{
			$survey->error = 'blocked';
			if(!$survey->published) {
                $survey->message = SurveyforceTemplates::Survey_blocked($sf_config, '_not_published');
            }
            elseif($survey->sf_date_started != '0000-00-00 00:00:00' && strtotime($survey->sf_date_started) <= $now) {
				$survey->message = SurveyforceTemplates::Survey_blocked($sf_config,'_started');
			}
            elseif($survey->published
                && $survey->sf_date_started != '0000-00-00 00:00:00' && strtotime($survey->sf_date_started) > $now) {
                $survey->message = SurveyforceTemplates::Survey_blocked($sf_config,'_yet');
            }
            else {
                $survey->message = SurveyforceTemplates::Survey_blocked($sf_config,'_expired');
            }

			return array('survey' => $survey, 'sf_config' => $sf_config, 'is_invited' => 0, 'invite_num' => '', 'rules' => $rules, 'preview' => $preview);
		}
	}

	public function SF_ShowSurvey_Invited()
	{

		require_once(JPATH_BASE . '/components/com_surveyforce/helpers/surveyforce.class.php');
		require_once(JPATH_BASE . '/components/com_surveyforce/helpers/html.php');

		$database = JFactory::getDbo();
		$survey_id = JFactory::getApplication()->input->get('survey', 0);
		$invite_num = JFactory::getApplication()->input->get('invite', '', 'CMD');
		$template = JFactory::getApplication()->input->get('survey_template');
		$survey = new mos_Survey_Force_Survey($database);
		$survey->load($survey_id);

		$survey->sf_descr = SurveyforceHelper::sfPrepareText($survey->sf_descr);
		$survey->surv_short_descr = SurveyforceHelper::sfPrepareText($survey->surv_short_descr);

		$sf_config = JComponentHelper::getParams('com_surveyforce');

		if ($template > 0)
			$survey->sf_template = $template;

		$query = "SELECT `sf_name` FROM `#__survey_force_templates` WHERE `id` = '{$survey->sf_template}' ";
		$database->setQuery($query);
		$survey->template = $database->loadResult();

		//if no template
		if (strlen($survey->template) < 1)
		{
			require_once(realpath(dirname(__FILE__) . '/templates.php'));
		}
		else
		{
			if (file_exists(JPATH_SITE . '/media/surveyforce/' . $survey->template . '/templates.php'))
			{
				require_once(JPATH_SITE . '/media/surveyforce/' . $survey->template . '/templates.php');
			}
			else
			{
				require_once(realpath(dirname(__FILE__) . '/templates.php'));
			}
		}

		$survey->is_complete = 0;
		if ($invite_num != '')
		{
			$query = "SELECT 1 FROM `#__survey_force_invitations` AS a, `#__survey_force_user_starts` AS b WHERE a.invite_num = ".JFactory::getDbo()->q($invite_num)." AND b.invite_id = a.id AND b.is_complete = 1";
			$database->setQuery($query);
			$survey->is_complete = (int) $database->loadResult();
			$db = JFactory::getDbo();
			$query = $db->getQuery(true);
			$query->select('1')
                ->from('#__survey_force_invitations')
                ->where('invite_num = '.$db->q($invite_num));
			if(!$db->setQuery($query)->loadResult()){
                JFactory::getApplication()->input->set('invite', 'NOINVITE');
            }
		}


		$query = " SELECT * FROM `#__survey_force_quest_show` WHERE `survey_id` = '" . $survey->id . "' ";
		$database->setQuery($query);
		$rules = $database->loadObjectList();

		$now = strtotime(JFactory::getDate());
        if ($survey->published
            && ((strtotime($survey->sf_date_expired) >= $now || $survey->sf_date_expired == '0000-00-00 00:00:00')
                && (strtotime($survey->sf_date_started) <= $now || $survey->sf_date_started == '0000-00-00 00:00:00'))
        ) {

			$query = " SELECT sf_qtype FROM #__survey_force_quests WHERE published = 1 AND sf_survey = {$survey->id} ORDER BY ordering, id ";
			$database->setQuery($query);
			$q_data = $database->loadColumn();
			for ($i = 0, $n = count($q_data); $i < $n; $i++)
			{
				if ($survey->sf_auto_pb == 0 && $q_data[$i] != 8 && isset($q_data[$i + 1]) && $q_data[$i + 1] != 8)
					$survey->sf_image = '';
			}
			$sf_special = false;
			if ((JFactory::getUser()->id) && ($survey->sf_special))
			{
				$query = "SELECT DISTINCT b.id FROM #__survey_force_users AS a, #__users AS b "
					. "\n WHERE a.list_id IN ({$survey->sf_special}) AND b.id = " . JFactory::getUser()->id
					. "\n AND a.name = b.username AND a.email = b.email AND a.lastname = b.name ";
				$database->setQuery($query);
				if ($database->loadResult() > 0)
					$sf_special = true;
			}

		$query = "SELECT * FROM #__extensions WHERE name = 'com_community' AND type = 'component'";
		$database->setQuery($query);
		$isInstolled = $database->loadObject();
		
		$friends = array();
		if ($sf_config->get('sf_enable_jomsocial_integration') && !empty($isInstolled))
			{
				$query = "SELECT j.connect_to FROM #__community_connection AS j WHERE j.status = 1 AND j.connect_from = '{$survey->sf_author}'";
				$database->setQuery($query);
				$friends = $database->loadColumn();
			}

			if ($survey->sf_invite)
			{
				return self::SF_ShowSurvey($survey->id);
			}
			elseif ((JFactory::getUser()->id) && ($survey->sf_reg))
			{
				return self::SF_ShowSurvey($survey->id);
			}
			elseif ((JFactory::getUser()->id) && ($survey->sf_friend) && $sf_config->get('sf_enable_jomsocial_integration') && in_array(JFactory::getUser()->id, $friends))
			{
				return self::SF_ShowSurvey($survey->id);
			}
			elseif ($sf_special)
			{
				return self::SF_ShowSurvey($survey->id);
			}
			elseif ($survey->sf_public)
			{
				return self::SF_ShowSurvey($survey->id);
			}
			else
			{
				$survey->error = 'bloked';
				$survey->message = SurveyforceTemplates::Survey_blocked($sf_config,'_user_has_no_right_or_invitation');
				return array('survey' => $survey, 'sf_config' => $sf_config, 'is_invited' => 0, 'invite_num' => '', 'rules' => $rules, 'preview' => $preview);
			}
		}
		else
		{
			$survey->error = 'bloked';
            if(!$survey->published) {
                $survey->message = SurveyforceTemplates::Survey_blocked($sf_config, '_not_published');
            }
            elseif($survey->sf_date_started != '0000-00-00 00:00:00' && strtotime($survey->sf_date_started) <= $now) {
                $survey->message = SurveyforceTemplates::Survey_blocked($sf_config,'_started');
            } else {
                $survey->message = SurveyforceTemplates::Survey_blocked($sf_config,'_expired');
            }

			return array('survey' => $survey, 'sf_config' => $sf_config, 'is_invited' => 0, 'invite_num' => '', 'rules' => $rules, 'preview' => $preview);
		}
	}

	public function showSurveyCat($cat_id = 0)
	{

		$database = JFactory::getDbo();
		if (!$cat_id)
		{
			return;
		}
		$sf_config = JComponentHelper::getParams('com_surveyforce');

		require_once(realpath(dirname(__FILE__) . '/template.php'));

		$query = "SELECT * FROM `#__survey_force_cats` WHERE `id` = '$cat_id'";
		$database->setQuery($query);
		$cat = $database->loadObjectList();
		$cat = $cat[0];

		$query = "SELECT * FROM `#__survey_force_survs` WHERE `sf_cat` = '$cat_id' AND `published` = 1";
		$database->setQuery($query);
		$rows = $database->loadObjectList();

		if (is_array($rows) && count($rows))
			foreach ($rows as $i => $row)
			{
				$rows[$i]->sf_descr = SurveyforceHelper::sfPrepareText($rows[$i]->sf_descr);
				$rows[$i]->surv_short_descr = SurveyforceHelper::sfPrepareText($rows[$i]->surv_short_descr);
			}

		SurveyforceTemplates::showCategory($cat, $rows, $sf_config);
	}

	public static function SF_GetQuestData($q_data, $survey, $start_id=0)
	{
		$type = SurveyforceHelper::getQuestionType($q_data->sf_qtype);
		$data['quest_type'] = $type->sf_plg_name;

		JPluginHelper::importPlugin('survey', $type->sf_plg_name);
		$className = 'plgSurvey' . ucfirst($type->sf_plg_name);
						
		$data['q_data'] = $q_data;
		$data['start_id'] = $start_id;
		$data['survey'] = $survey;
		
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
		$query->select('sf_name, id')
                ->from('#__survey_force_qsections')
                ->where('sf_survey_id = "'.$data['survey']->id.'" AND addname = 1');
        $db->setQuery($query);
		$section = $db->loadObject();

		if($section){
			$sectionName = $section->sf_name;
			if($data['q_data']->sf_section_id == $section->id){
				$data['q_data']->sf_qtext = $sectionName.$data['q_data']->sf_qtext;
			}
		}
		
		if (method_exists($className, 'onGetQuestionData'))
			$return = $className::onGetQuestionData($data);
			
		return $return;
	}

	public static function listQuestionTypes($id_survey)
	{
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
		$query->select('sf_qtype');
		$query->from('#__survey_force_quests');
		$query->where('sf_survey=' . intval($id_survey));
		$query->group('sf_qtype');
		$db->setQuery($query);
		$result = $db->loadObjectList();
		$aIds = array();

		foreach ($result as $value)
			array_push($aIds, $value->sf_qtype);
		array_push($aIds, 0);


		$query = $db->getQuery(true);
		$query->select('sf_plg_name');
		$query->from('#__survey_force_qtypes');
		$query->where('id IN (' . implode(',', $aIds) . ')');
		$query->group('sf_plg_name');
		$db->setQuery($query);
		$arrayTypes = $db->loadObjectList();

		return $arrayTypes;
	}

	public static function getJsCss($template, $listQuestionTypes)
	{

		foreach ($listQuestionTypes as $plugin)
		{
			JPluginHelper::importPlugin('survey', $plugin->sf_plg_name);
			$className = 'plgSurvey' . ucfirst($plugin->sf_plg_name);
			$classObject = new $className();
			
			//JS
			if (method_exists($classObject, 'onGetScriptJs'))
				$classObject->onGetScriptJs();

			if (method_exists($classObject, 'onGetScriptJsTmpl'))
				$classObject->onGetScriptJsTmpl($template);

			//CSS
			if (method_exists($classObject, 'onGetScriptCssTmpl'))
				$classObject->onGetScriptCssTmpl($template);
		}
	}

	public static function SF_ListSurveys($option, $is_i = false)
	{
		$database = JFactory::getDbo();
		$catid = intval(JFactory::getApplication()->getUserStateFromRequest("catid", 'catid', 0));
		$limit = intval(JFactory::getApplication()->getUserStateFromRequest("viewlistlimit", 'limit', 20));
		$limitstart = intval(JFactory::getApplication()->getUserStateFromRequest("viewlimitstart", 'limitstart', 0));
		if ($limit == 0) $limit = 999999;
		$limit = intval(JFactory::getApplication()->input->get('limit', JFactory::getSession()->get('list_limit', JFactory::getApplication()->getCfg('list_limit'))));
		if ($limit == 0) $limit = 999999;
		JFactory::getSession()->set('list_limit', $limit);
		$limitstart = intval(JFactory::getApplication()->input->get('limitstart', 0));
		$catid = intval(JFactory::getApplication()->input->get('catid', JFactory::getSession()->get('list_catid', 0)));
		JFactory::getSession()->set('list_catid', $catid);

		$search = JFactory::getApplication()->input->get('sf_search', '');
		
		$where_search = '';
		if($search){
			$where_search = " AND LOWER(a.sf_name) LIKE '%".strtolower($search)."%'";
		}

		// get the total number of records
		$query = "SELECT COUNT(*)"
			. "\n FROM #__survey_force_survs as a WHERE 1=1 "
			. ($catid ? "\n AND sf_cat = $catid" : '')
			. (JFactory::getUser()->authorise( 'core.manage', 'com_surveyforce' )?'':" AND sf_author = '" . JFactory::getUser()->id . "' ")
			. $where_search;

		$database->setQuery($query);
		$total = $database->loadResult();

		jimport('joomla.html.pagination');
		$pageNav = new SFPageNav($total, $limitstart, $limit);

		// get the subset (based on limits) of required records
		$query = "SELECT a.*, b.sf_catname, us.username "
			. "\n FROM #__survey_force_survs a LEFT JOIN #__survey_force_cats b ON a.sf_cat = b.id LEFT JOIN #__users as us ON a.sf_author = us.id WHERE 1=1 "
			. ($catid ? "\n AND a.sf_cat = $catid " : '')
			. (JFactory::getUser()->authorise( 'core.manage', 'com_surveyforce' )?'':" AND sf_author = '" . JFactory::getUser()->id . "' ")
			. $where_search
			. "\n ORDER BY a.sf_name, b.sf_catname "
			. "\n LIMIT $pageNav->limitstart, $pageNav->limit";

		$database->setQuery($query);
		$rows = $database->loadObjectList();

		$query = " SELECT COUNT(*) FROM #__survey_force_listusers "
			. (JFactory::getUser()->authorise( 'core.manage', 'com_surveyforce' )?'':"WHERE sf_author_id = '" . JFactory::getUser()->id . "'")
			. " ORDER BY listname ";
		$database->setQuery($query);
		$lists['userlists'] = $database->loadResult();

		$javascript = 'onchange="document.adminForm.submit();"';
		$query = "SELECT id AS value, sf_catname AS text"
			. "\n FROM #__survey_force_cats"
			. "\n ORDER BY sf_catname";
		$database->setQuery($query);
		$categories[] = JHtmlSelect::option('0', JText::_("COM_SURVEYFORCE_SF_SELECT_CATEGORY"));
		$categories = @array_merge($categories, $database->loadObjectList());
		$category = JHtmlSelect::genericlist($categories, 'catid', 'class="text_area" size="1" ' . $javascript, 'value', 'text', $catid);
		$lists['category'] = $category;
		if (JFactory::getApplication()->input->get('task') == 'i_report')
			survey_force_front_html::SF_showIReport($rows, $lists, $pageNav, $option, $is_i);
		else
			survey_force_front_html::SF_showSurvsList($rows, $lists, $pageNav, $option, $is_i);
	}

	public static function SF_editUsergroup($id, $option)
	{
		$database = JFactory::getDbo();
		$sf_config = JComponentHelper::getParams('com_surveyforce');
		if (isset($_REQUEST['limit']) && $_REQUEST['limit'] == 0)
		{
			$limit = 999999999;
		}
		else
		{
			$limit = intval(mosGetParam($_REQUEST, 'limit', JFactory::getSession()->get('list_limit', JFactory::getApplication()->getCfg('list_limit'))));
		}
		JFactory::getSession()->set('list_limit', $limit);
		$limitstart = intval(mosGetParam($_REQUEST, 'limitstart', 0));
		$listname = mosGetParam($_REQUEST, 'listname', '');

		// get the total number of records
		$query = "SELECT COUNT(*) FROM #__users ORDER BY username ";
		$database->setQuery($query);
		$total = $database->loadResult();

		$pageNav = new SFPageNav($total, $limitstart, $limit);

		$lists = array();
		$lists['listname'] = $listname;
		$lists['listid'] = 0;
		if ($id)
		{
			$query = "SELECT * FROM #__survey_force_listusers WHERE id = $id ";
			$database->setQuery($query);
			$list = null;
			$list = $database->loadObject();
			if ($listname == '')
				$lists['listname'] = $list->listname;
			$lists['listid'] = $list->id;
		}

		$query = "SELECT id AS value, sf_name AS text"
			. "\n FROM #__survey_force_survs WHERE published = 1"
			. (JFactory::getUser()->get('usertype') != 'Super Administrator' ? " AND sf_author = '" . JFactory::getUser()->id . "' " : '')
			. "\n ORDER BY sf_name";
		$database->setQuery($query);
		$surveys = $database->loadObjectList();

		$database->setQuery("SELECT `survey_id` FROM #__survey_force_listusers WHERE `id` = '" . $id . "'");
		$surv_id = $database->loadResult();

		$survey = mosHTML::selectList($surveys, 'survey_id', 'class="text_area" size="1" ', 'value', 'text', (isset($surv_id) ? $surv_id : null));
		$lists['survey'] = $survey;
		$lists['date_created'] = '';

		if ($sf_config->get('sf_enable_lms_integration'))
		{
			$query = "SELECT lms_usertype_id FROM #__lms_users WHERE user_id = '" . JFactory::getUser()->id . "'";
			$database->setQuery($query);
			$is_super = $database->loadResult();

			$query = "SELECT id FROM `#__lms_courses` ";
			$database->setQuery($query);
			$courses = @array_merge(array(0 => 0), ($database->loadColumn() == null ? array() : $database->loadColumn()));
			$usergroups = array();
			foreach ($courses as $course_id)
			{
				if ($is_super == 5)
				{
					$query = "SELECT DISTINCT a.id AS value, concat(c.course_name, ' (', a.ug_name, ')') AS text "
						. "FROM #__lms_usergroups AS a, #__lms_user_courses AS b, #__lms_courses AS c "
						. "WHERE a.course_id = '{$course_id}' AND b.course_id = a.course_id AND c.id = a.course_id ORDER BY c.course_name";
				}
				else
				{
					$query = "SELECT DISTINCT a.id AS value, concat(c.course_name, ' (', a.ug_name, ')') AS text "
						. "FROM #__lms_usergroups AS a, #__lms_user_courses AS b, #__lms_courses AS c "
						. "WHERE a.course_id = '{$course_id}' AND b.course_id = a.course_id AND b.user_id = '" . JFactory::getUser()->id . "' AND b.role_id IN (1,4) AND c.id = a.course_id ORDER BY c.course_name";
				}

				$query2 = "SELECT DISTINCT concat('0_', c.id) AS value, concat(c.course_name, ' (Users without group)') AS text "
					. "FROM #__lms_user_courses AS b, #__lms_courses AS c "
					. "WHERE b.course_id = '{$course_id}' AND  c.id = b.course_id ORDER BY c.course_name";
				$database->setQuery($query2);
				$usergroups = @array_merge($usergroups, $database->loadObjectList());

				$database->setQuery($query);

				$usergroups = @array_merge($usergroups, $database->loadObjectList());
			}
			$usergroups = mosHTML::selectList($usergroups, 'lms_groups[]', 'class="text_area" size="4" multiple="multiple" ', 'value', 'text', 0);
			$lists['lms_groups'] = $usergroups;
		}

		$query = "SELECT `a`.* " . ($id ? ", `b`.`id` AS `luid`" : "") . " FROM `#__users` AS `a` ";
		if ($id)
		{
			$query .= "LEFT JOIN `#__survey_force_users` AS `b` ON `b`.`email` = `a`.`email` AND `b`.`list_id` = $id ";
		}
		$query .= "ORDER BY `a`.`username`"
			. "\n LIMIT $pageNav->limitstart, $pageNav->limit";
	
		$query_check = "SELECT * FROM #__extensions WHERE name = 'com_community' AND type = 'component'";
		$database->setQuery($query_check);
		$isInstolled = $database->loadObject();
		
		
		if ($sf_config->get('sf_enable_jomsocial_integration') && !empty($isInstolled))
		{
			$query = "SELECT u.* FROM `#__users` AS u, `#__community_connection` AS j WHERE u.`id` = j.`connect_to` AND j.`status` = 1 AND j.`connect_from` = '{$my->id}' ORDER BY u.`username`"
				. "\n LIMIT $pageNav->limitstart, $pageNav->limit";
		}
		$database->setQuery($query);
		$row = $database->loadObjectList();

		survey_force_front_html::SF_editListUsers($row, $lists, $sf_config, $pageNav, $option);
	}

	public static function SF_saveUsergroup(&$cid, $option)
	{
		$database = JFactory::getDbo();
		$row = new mos_Survey_Force_ListUsers($database);

        $jinput = JFactory::getApplication()->input;

		$post = $jinput->post->getArray();
        $post = $jinput->getArray(array(
            'listname' => 'STRING',
            'survey_id' => 'INT',
            'is_add_manually' => 'INT',
            'limit' => 'INT',
            'cid' => 'ARRAY',
            'option' => 'STRING',
            'task' => 'STRING',
            'boxchecked' => 'INT',
            'hidemainmenu' => 'INT',
            'id' => 'INT',
            'Itemid' => 'INT',
            'sf_author_id' => 'INT'
        ), $post);

		if (!$row->bind($post))
		{
			echo "<script> alert('" . $row->getError() . "'); window.history.go(-1); </script>\n";
			exit();
		}
		// pre-save checks
		if (!$row->check())
		{
			echo "<script> alert('" . $row->getError() . "'); window.history.go(-1); </script>\n";
			exit();
		}
		if (!$row->id)
		{
			$row->date_created = date('Y-m-d H:i:s');
		}
		
		// save the changes
		if (!$row->store())
		{
			echo "<script> alert('" . $row->getError() . "'); window.history.go(-1); </script>\n";
			exit();
		}
		
		$list_id = $row->id;
		$is_add_man = intval(mosGetParam($post, 'is_add_manually', 0));
		$is_add_lms = intval(mosGetParam($post, 'is_add_lms', 0));
		if ($is_add_man && count($cid) > 0)
		{
			$query = "SELECT `name`, `username`, `email` FROM `#__users` WHERE `id` IN (" . implode(',', $cid) . ") ";
			$database->setQuery($query);
			$mos_users = $database->loadObjectList();
			
			foreach ($mos_users as $mos_user)
			{
				$row_user = new mos_Survey_Force_UserInfo($database);
				
				$row_user->name = $mos_user->username;
				$row_user->lastname = $mos_user->name;
				$row_user->email = $mos_user->email;
				$row_user->list_id = $list_id;
				if (!$row_user->check())
				{
					continue;
				}
				elseif (!$row_user->store())
				{
					echo "<script> alert('" . $row_user->getError() . "'); window.history.go(-1); </script>\n";
					exit();
				}
			}
		}
		if ($is_add_lms)
		{
			$lms_groups = mosGetParam($post, 'lms_groups', array());
			if (count($lms_groups) > 0)
			{
				$query = "SELECT lms_usertype_id FROM #__lms_users WHERE user_id = '" . JFactory::getUser()->id . "'";
				$database->setQuery($query);
				$is_super = $database->loadResult();
				if ($is_super == 5)
				{
					$query = "SELECT course_id FROM #__lms_user_courses";
				}
				else
				{
					$query = "SELECT distinct course_id FROM #__lms_user_courses WHERE user_id = '" . JFactory::getUser()->id . "' AND role_id IN (1,4)";
				}
				$database->setQuery($query);
				$teacher_in_courses = $database->loadColumn();
				$teacher_in_courses_str = implode(',', $teacher_in_courses);
				$lms_group_str = "'-1',";
				$teacher_in_courses_str2 = '';
				foreach ($lms_groups as $lms_group)
				{
					if (strpos($lms_group, '_') > 0)
					{
						$teacher_in_courses_str2 .= mb_substr($lms_group, 2) . ',';
					}
					else
						$lms_group_str .= $lms_group . ',';
				}
				$lms_group_str = mb_substr($lms_group_str, 0, -1);
				$teacher_in_courses_str2 = mb_substr($teacher_in_courses_str2, 0, -1);
				$query = "SELECT user_id FROM #__lms_users_in_groups WHERE (group_id IN ({$lms_group_str}) AND course_id IN ({$teacher_in_courses_str})) "
					. ($teacher_in_courses_str2 != '' ? " OR (group_id = 0 AND course_id IN ({$teacher_in_courses_str2}))" : '');
				$database->setQuery($query);

				$lms_users = $database->loadColumn();
				$query = "SELECT name, username, email FROM #__users WHERE id IN (" . implode(',', $lms_users) . ")";
				$database->setQuery($query);
				$mos_users = $database->loadObjectList();
				foreach ($mos_users as $mos_user)
				{
					$row_user = new mos_Survey_Force_UserInfo($database);
					$row_user->name = '';
					$row_user->lastname = $mos_user->name;
					$row_user->email = $mos_user->email;
					$row_user->list_id = $list_id;
					if (!$row_user->check())
					{
						continue;
					}
					elseif (!$row_user->store())
					{
						echo "<script> alert('" . $row_user->getError() . "'); window.history.go(-1); </script>\n";
						exit();
					}
				}
			}
		}
		if ($jinput->get('task') == 'save_list') {
            mosRedirect(SFRoute("index.php?option=com_surveyforce&task=usergroups"));
        } elseif ($jinput->get('task') == 'apply_list') {
            mosRedirect(SFRoute("index.php?option=com_surveyforce&task=edit_list&id=" . $list_id));
        } elseif ($jinput->get('task') == 'save_user') {
            mosRedirect(SFRoute("index.php?option=com_surveyforce&task=view_users&list_id=" . $list_id));
        }
	}

	public static function SF_addUser2Group($option)
	{

		$listid = intval(JFactory::getApplication()->input->get('list_id'));
		if ($listid)
			self::SF_editUsergroup($listid, $option);
		else
			mosRedirect(SFRoute("index.php?option=$option&task=usergroups"));
	}

	public static function SF_delUserFromGroup($cid, $option)
	{
		$database = JFactory::getDbo();
		if (count($cid))
		{
			$cids = implode(',', $cid);
			$query = "DELETE FROM #__survey_force_users WHERE id IN ($cids) ";
			$database->setQuery($query);
			if (!$database->execute())
			{
				echo "<script> alert('" . $database->getErrorMsg() . "'); window.history.go(-1); </script>\n";
			}
		}
		mosRedirect(SFRoute("index.php?option=com_surveyforce&task=view_users"));
	}

	public static function SF_delUsergroup($cid, $option)
	{
		$database = JFactory::getDbo();
		if (count($cid))
		{
			$cids = implode(',', $cid);
			$query = "DELETE FROM #__survey_force_users WHERE list_id IN ($cids) ";
			$database->setQuery($query);
			if (!$database->execute())
			{
				echo "<script> alert('" . $database->getErrorMsg() . "'); window.history.go(-1); </script>\n";
			}
			$query = "DELETE FROM #__survey_force_listusers WHERE id IN ($cids) ";
			$database->setQuery($query);
			if (!$database->execute())
			{
				echo "<script> alert('" . $database->getErrorMsg() . "'); window.history.go(-1); </script>\n";
			}
		}
		mosRedirect(SFRoute("index.php?option=com_surveyforce&task=usergroups"));

	}

	public static function SF_cancelViewUsers($option)
	{
		mosRedirect(SFRoute("index.php?option=com_surveyforce&task=view_users"));
	}

	public static function SF_ListCategories($option)
	{
		$database = JFactory::getDbo();
		$limit = intval(JFactory::getApplication()->getUserStateFromRequest("viewlistlimit", 'limit', 20));
		$limitstart = intval(JFactory::getApplication()->getUserStateFromRequest("viewlimitstart", 'limitstart', 0));
		if ($limit == 0) $limit = 999999;
		$limit = intval(mosGetParam($_REQUEST, 'limit', JFactory::getSession()->get('list_limit', JFactory::getApplication()->getCfg('list_limit'))));
		if ($limit == 0) $limit = 999999;
		JFactory::getSession()->set('list_limit', $limit);
		$limitstart = intval(mosGetParam($_REQUEST, 'limitstart', 0));

		// get the total number of records
		$query = "SELECT COUNT(*)"
			. "\n FROM #__survey_force_cats";
		$database->setQuery($query);
		$total = $database->loadResult();

		jimport('joomla.html.pagination');
		$pageNav = new SFPageNav($total, $limitstart, $limit);

		// get the subset (based on limits) of required records
		$query = "SELECT a.*, b.name "
			. "\n FROM #__survey_force_cats AS a"
			. "\n LEFT JOIN #__users AS b ON a.user_id = b.id"
			. "\n ORDER BY a.user_id, a.sf_catname"
			. "\n LIMIT $pageNav->limitstart, $pageNav->limit";
		$database->setQuery($query);
		$rows = $database->loadObjectList();
		survey_force_front_html::SF_showCatsList($rows, $pageNav, $option);
	}

	public static function SF_editCategory($id, $option)
	{
		$database = JFactory::getDbo();
		$row = new mos_Survey_Force_Cat($database);
		// load the row from the db table
		$row->load($id);

		if ($id > 0 && $row->user_id != JFactory::getUser()->id)
		{
			echo "<script> alert('" . JText::_('COM_SF_YOU_CAN_NOT_EDIT_THIS_CATEGORY') . "'); window.history.go(-1); </script>\n";
			exit();
		}

		$row->user_id = JFactory::getUser()->id;

		if ($id)
		{
			// do stuff for existing records
			$row->checkout(JFactory::getUser()->id);
		}
		else
		{
			// do stuff for new records
			$row->published = 1;
		}
		$lists = array();

		survey_force_front_html::SF_editCategory($row, $lists, $option);
	}

	public static function SF_saveCategory($option)
	{
        $post = JFactory::getApplication()->input->post;
        $database = JFactory::getDbo();
		$row = new mos_Survey_Force_Cat($database);

        $cat = array();
        $cat['id'] = $post->getInt('id', 0);
        $cat['sf_catname'] = self::SF_processGetField($post->get('sf_catname'));
        $cat['sf_catdescr'] = self::SF_processGetField($post->get('sf_catdescr'));
        $cat['published'] = $post->getInt('published', 0);
        $cat['user_id'] = $post->getInt('user_id', 0);
        $cat['ordering'] = $post->getInt('ordering', 0);

		if (!$row->bind($cat)) {
			echo "<script> alert('" . $row->getError() . "'); window.history.go(-1); </script>\n";
			exit();
		}

		if (!$row->check()) {
			echo "<script> alert('" . $row->getError() . "'); window.history.go(-1); </script>\n";
			exit();
		}

		if (!$row->store()) {
			echo "<script> alert('" . $row->getError() . "'); window.history.go(-1); </script>\n";
			exit();
		}

		$row->checkin();
		#$row->updateOrder();

		if (JFactory::getApplication()->input->get('task') == 'apply_cat') {
			mosRedirect(SFRoute("index.php?option=com_surveyforce&task=edit_cat&cid[]=" . $row->id));
		} else {
			mosRedirect(SFRoute("index.php?option=com_surveyforce&task=categories"));
		}
	}

	public static function SF_removeCategory(&$cid, $option)
	{
		$database = JFactory::getDbo();
		if (count($cid))
		{
			$cids = implode(',', $cid);
			$query = "DELETE FROM #__survey_force_cats"
				. "\n WHERE id IN ( $cids )";
			$database->setQuery($query);
			if (!$database->execute())
			{
				echo "<script> alert('" . $database->getErrorMsg() . "'); window.history.go(-1); </script>\n";
			}
		}
		mosRedirect(SFRoute("index.php?option=com_surveyforce&task=categories"));
	}

	public static function SF_cancelCategory($option)
	{
        $post = JFactory::getApplication()->input->post;
        $cat = array();
        $cat['id'] = $post->getInt('id', 0);
        $cat['sf_catname'] = self::SF_processGetField($post->get('sf_catname'));
        $cat['sf_catdescr'] = self::SF_processGetField($post->get('sf_catdescr'));
        $cat['published'] = $post->getInt('published', 0);
        $cat['user_id'] = $post->getInt('user_id', 0);
        $cat['ordering'] = $post->getInt('ordering', 0);

		$database = JFactory::getDbo();
		$row = new mos_Survey_Force_Cat($database);

		$row->bind($cat);
		$row->checkin();

		mosRedirect(SFRoute("index.php?option=com_surveyforce&task=categories"));
	}

	public static function SF_manageUsers($option)
	{
		$database = JFactory::getDbo();
		$limit = intval(JFactory::getApplication()->getUserStateFromRequest("viewlistlimit", 'limit', 20));
		$limitstart = intval(JFactory::getApplication()->getUserStateFromRequest("viewlimitstart", 'limitstart', 0));
		if ($limit == 0) $limit = 999999;
		$limit = intval(mosGetParam($_REQUEST, 'limit', JFactory::getSession()->get('list_limit', JFactory::getApplication()->getCfg('list_limit'))));
		if ($limit == 0) $limit = 999999;
		JFactory::getSession()->set('list_limit', $limit);
		$limitstart = intval(mosGetParam($_REQUEST, 'limitstart', 0));
		// get the total number of records
		$query = "SELECT COUNT(*)"
			. "\n FROM #__survey_force_listusers "
			. (" WHERE  sf_author_id = '" . JFactory::getUser()->id . "' ");
		$database->setQuery($query);
		$total = $database->loadResult();

		jimport('joomla.html.pagination');
		$pageNav = new SFPageNav($total, $limitstart, $limit);

		// get the subset (based on limits) of required records
		$query = "SELECT a.id, a.listname, a.survey_id, a.date_created, a.date_invited, a.date_remind,"
			. "\n a.is_invited, b.sf_name as survey_name, count(c.id) as users_count, d.name AS author "
			. "\n FROM #__survey_force_listusers a LEFT JOIN #__survey_force_survs b ON b.id = a.survey_id"
			. "\n LEFT JOIN #__survey_force_users c ON c.list_id = a.id"
			. "\n LEFT JOIN #__users AS d ON d.id = a.sf_author_id "
			//. (" WHERE  a.sf_author_id = '" . JFactory::getUser()->id . "' ")
			. "\n GROUP BY a.id, a.listname, a.survey_id, a.date_created, a.date_invited, a.date_remind,"
			. "\n a.is_invited, b.sf_name "
			. "\n ORDER BY a.listname"
			. "\n LIMIT $pageNav->limitstart, $pageNav->limit";
		$database->setQuery($query);
		$rows = $database->loadObjectList();
		$i = 0;
		while ($i < count($rows))
		{
			$list_id = $rows[$i]->id;
			$query = "SELECT count(a.id) FROM #__survey_force_invitations as a, #__survey_force_users as b"
				. "\n WHERE b.id = a.user_id AND (a.inv_status=1 OR a.inv_status=3) AND b.list_id = '" . $list_id . "'";
			$database->setQuery($query);
			$rows[$i]->total_starts = $database->loadResult();
			$i++;
		}

		survey_force_front_html::SF_showListUsers($rows, $lists, $pageNav, $option);
	}

	#######################################
	###	--- ---		REPORTS 	--- --- ###

	public static function SF_ViewReports($option, $is_pdf = 0)
	{
		$database = JFactory::getDbo();
		$sf_config = JComponentHelper::getParams('com_surveyforce');

		$limit = intval(mosGetParam($_REQUEST, 'limit', JFactory::getSession()->get('list_limit', JFactory::getApplication()->getCfg('list_limit'))));
		if ($limit == 0) $limit = 999999;
		JFactory::getSession()->set('list_limit', $limit);
		$limitstart = intval(mosGetParam($_REQUEST, 'limitstart', JFactory::getSession()->get('list_limitstart', 0)));
		JFactory::getSession()->set('list_limitstart', $limitstart);
		$surv_id = intval(mosGetParam($_REQUEST, 'surv_id', JFactory::getSession()->get('list_surv_id', 0)));
		JFactory::getSession()->set('list_surv_id', $surv_id);

		$filt_status = intval(mosGetParam($_REQUEST, 'filt_status', JFactory::getSession()->get('list_filt_status', 2)));
		JFactory::getSession()->set('list_filt_status', $filt_status);
		$filt_utype = intval(mosGetParam($_REQUEST, 'filt_utype', JFactory::getSession()->get('list_filt_utype', 0)));
		JFactory::getSession()->set('list_filt_utype', $filt_utype);
		$filt_ulist = intval(mosGetParam($_REQUEST, 'filt_ulist', JFactory::getSession()->get('list_filt_ulist', 0)));
		JFactory::getSession()->set('list_filt_ulist', $filt_ulist);

		$javascript = 'onchange="submitbutton(\'reports\');"';
		$filter_quest = array();
		$filter_ans = array(0);
		$i = 0;
		$j = 0;
		$lists['filter_quest'] = array();
		$lists['filter_quest_ans'] = array();
		if ($surv_id)
		{
			$query = "SELECT count(*) FROM #__survey_force_quests WHERE  published = 1 AND sf_survey = '" . $surv_id . "' and id = '" . $filter_quest . "' and sf_qtype IN (2,3)";
			$database->setQuery($query);
			if (!$database->loadResult())
			{
				if (isset($_REQUEST['filter_quest']))
				{
					$k = 0;
					foreach ($_REQUEST['filter_quest'] as $filt_row)
					{
						if ($filt_row)
						{
							$qlists = array();
							$query = "SELECT id AS value, sf_qtext AS text"
								. "\n FROM #__survey_force_quests WHERE  published = 1 AND sf_survey = '" . $surv_id . "' and sf_qtype IN (2,3)"
								. "\n ORDER BY ordering";
							$database->setQuery($query);
							$quests33 = $database->loadObjectList();
							$ji = 0;

							while ($ji < count($quests33))
							{
								$quests33[$ji]->text = strip_tags($quests33[$ji]->text);
								if (strlen($quests33[$ji]->text) > 55)
									$quests33[$ji]->text = mb_substr($quests33[$ji]->text, 0, 55) . '...';
								$quests33[$ji]->text = $quests33[$ji]->value . ' - ' . $quests33[$ji]->text;

								$ji++;
							}

							$qlists[] = mosHTML::makeOption('0', JText::_('COM_SF_SELECT_QUESTION'));
							$qlists = @array_merge($qlists, $quests33);
							$qlist = mosHTML::selectList($qlists, 'filter_quest[]', 'class="text_area" size="1" ' . $javascript, 'value', 'text', $filt_row);
							$filter_quest[$i] = $filt_row;
							$lists['filter_quest'][$i] = $qlist;
							$sel_ans = array(0);
							if (isset($_REQUEST['filter_ans'][$filt_row]) && $_REQUEST['filter_ans'][$filt_row])
							{
								$sel_ans = $_REQUEST['filter_ans'][$filt_row];
							}
							$sel_ans2 = null;
							if (is_array($sel_ans) && count($sel_ans))
								foreach ($sel_ans as $sel_an)
								{
									$tmp = new stdClass;
									$tmp->value = $sel_an;
									$sel_ans2[] = $tmp;
								}

							$query = "SELECT distinct a.answer AS value, b.ftext AS text"
								. "\n FROM #__survey_force_user_answers as a, #__survey_force_fields as b, #__survey_force_quests as c WHERE c.published = 1 AND a.quest_id = '" . $filt_row . "' and a.survey_id = '" . $surv_id . "' and a.quest_id = c.id and c.sf_qtype IN (2,3) and a.answer <> 0 and a.answer = b.id";
							$database->setQuery($query);
							$alists = array();
							$alists[] = mosHTML::makeOption('0', '- Select Answer -');
							$alists = @array_merge($alists, $database->loadObjectList());
							$alist = mosHTML::selectList($alists, "filter_ans[$filt_row][]", 'class="text_area" size="3" multiple="multiple" ' . $javascript, 'value', 'text', $sel_ans2);
							$filter_ans[$i] = implode(',', $sel_ans);
							$lists['filter_quest_ans'][$i] = $alist;
							$i++;
							$k++;
						}
					}
				}
				$qlists = array();
				$query = "SELECT id AS value, sf_qtext AS text"
					. "\n FROM #__survey_force_quests WHERE  published = 1 AND sf_survey = '" . $surv_id . "' and sf_qtype IN (2,3)"
					. "\n ORDER BY ordering, id ";
				$database->setQuery($query);

				$quests34 = $database->loadObjectList();

				$ji = 0;
				while ($ji < count($quests34))
				{
					$quests34[$ji]->text = strip_tags($quests34[$ji]->text);
					if (strlen($quests34[$ji]->text) > 55)
						$quests34[$ji]->text = mb_substr($quests34[$ji]->text, 0, 55) . '...';
					$quests34[$ji]->text = $quests34[$ji]->value . ' - ' . $quests34[$ji]->text;
					$ji++;
				}

				$qlists[] = mosHTML::makeOption('0', JText::_('COM_SF_SELECT_QUESTION'));
				$qlists = @array_merge($qlists, $quests34);
				$qlist = mosHTML::selectList($qlists, 'filter_quest[]', 'class="text_area" size="1" ' . $javascript, 'value', 'text', '0');
				$lists['filter_quest'][$i] = $qlist;
				$lists['filter_quest_ans'][$i] = '';
			}
		}

		if (($filt_utype - 1) != 2)
		{
			$filt_ulist = 0;
		}
		$query = "SELECT count(sf_ust.id) FROM #__survey_force_user_starts as sf_ust, #__survey_force_survs as sf_s"
			. "\n WHERE sf_ust.survey_id = sf_s.id"
			. ($surv_id ? "\n and sf_s.id = $surv_id" : '')
			. ($filt_status ? "\n and sf_ust.is_complete = '" . ($filt_status - 1) . "'" : '')
			. ($filt_utype ? "\n and sf_ust.usertype = '" . ($filt_utype - 1) . "'" : '');
		$database->setQuery($query);

		$total = $database->loadResult();


		// get the subset (based on limits) of required records
		$query = "SELECT sf_ust.*, sf_s.sf_name as survey_name, u.username reg_username, u.name reg_name, u.email reg_email,"
			. "\n sf_u.name as inv_name, sf_u.lastname as inv_lastname, sf_u.email as inv_email"
			. "\n FROM (#__survey_force_user_starts as sf_ust, #__survey_force_survs as sf_s";
		$r = 0;
		foreach ($filter_ans as $filt_ans)
		{
			$query .= ($filt_ans != '0' ? ", #__survey_force_user_answers as sf_ans" . $r : '');
			$r++;
		}

		$query .= ")"
			. "\n LEFT JOIN #__users as u ON u.id = sf_ust.user_id and sf_ust.usertype=1"
			. "\n LEFT JOIN #__survey_force_users as sf_u ON sf_u.id = sf_ust.user_id and sf_ust.usertype=2"
			. "\n WHERE sf_ust.survey_id = sf_s.id"
			. ($surv_id ? "\n and sf_s.id = $surv_id" : '')
			. (" AND sf_s.sf_author = '" . JFactory::getUser()->id . "' ")
			. ($filt_status ? "\n and sf_ust.is_complete = '" . ($filt_status - 1) . "'" : '')
			. ($filt_utype ? "\n and sf_ust.usertype = '" . ($filt_utype - 1) . "'" : '')
			. ($filt_ulist ? "\n and sf_u.list_id = '" . ($filt_ulist) . "'" : '');
		$r = 0;
		foreach ($filter_ans as $filt_ans)
		{
			$query .= ($filt_ans != '0' ? "\n and sf_ans" . $r . ".start_id = sf_ust.id and sf_ans" . $r . ".answer IN (" . ($filt_ans) . ")" : '');
			$r++;

		}
		$query .= "\n ORDER BY sf_ust.sf_time DESC";
		$database->setQuery($query);

		$rows = $database->loadObjectList();
		$total = count($rows);
		$rows = @array_slice($rows, $limitstart, $limit);

		jimport('joomla.html.pagination');
		$pageNav = new SFPageNav($total, $limitstart, $limit);

		$query = "SELECT id AS value, sf_name AS text"
			. "\n FROM #__survey_force_survs"
			. (" WHERE sf_author = '" . JFactory::getUser()->id . "' ")
			. "\n ORDER BY sf_name";
		$database->setQuery($query);

		$surveys[] = mosHTML::makeOption('0', JText::_('COM_SF_S_SELECT_SURVEY'));
		$surveys = @array_merge($surveys, $database->loadObjectList());
		$survey = mosHTML::selectList($surveys, 'surv_id', 'class="text_area" size="1" ' . $javascript, 'value', 'text', $surv_id);
		$lists['survey'] = $survey;

		$statuses1 = array();
		$statuses1[0] = new stdClass;
		$statuses1[1] = new stdClass;
		$statuses1[0]->value = 2;
		$statuses1[0]->text = JText::_('COM_SF_COMPLETED');
		$statuses1[1]->value = 1;
		$statuses1[1]->text = JText::_('COM_SF_NOT_COMPLETED');
		$statuses[] = mosHTML::makeOption('0', JText::_('COM_SF_SELECT_STATUS'));
		$statuses = @array_merge($statuses, $statuses1);
		$f_status = mosHTML::selectList($statuses, 'filt_status', 'class="text_area" size="1" style="width:190px;"' . $javascript, 'value', 'text', $filt_status);
		$lists['filt_status'] = $f_status;

		$u_types1 = array();
		$u_types1[0] = new stdClass;
		$u_types1[1] = new stdClass;
		$u_types1[2] = new stdClass;
		if (!$sf_config->get('sf_enable_jomsocial_integration'))
		{
			$u_types1[0]->value = 3;
			$u_types1[0]->text = JText::_('COM_SF_INVITED_USERS');
		}
		$u_types1[1]->value = 2;
		$u_types1[1]->text = JText::_('COM_SF_REGISTERED_USERS');
		$u_types1[2]->value = 1;
		$u_types1[2]->text = JText::_('COM_SF_GUESTS');
		$u_types[] = mosHTML::makeOption('0', JText::_('COM_SF_SELECT_USERTYPE'));
		$u_types = @array_merge($u_types, $u_types1);
		$f_utypes = mosHTML::selectList($u_types, 'filt_utype', 'class="text_area" size="1" style="width:190px;" ' . $javascript, 'value', 'text', $filt_utype);
		$lists['filt_utype'] = $f_utypes;

		$lists['filt_ulist'] = '';
		if (($filt_utype - 1) == 2)
		{
			$query = "SELECT id AS value, listname AS text"
				. "\n FROM #__survey_force_listusers"
				. "\n ORDER BY listname";
			$database->setQuery($query);
			$ulists[] = mosHTML::makeOption('0', '- Select UserList -');
			$ulists = @array_merge($ulists, $database->loadObjectList());
			$ulist = mosHTML::selectList($ulists, 'filt_ulist', 'class="text_area" size="1" style="width:190px;" ' . $javascript, 'value', 'text', $filt_ulist);
			$lists['filt_ulist'] = $ulist;
		}

		if ($is_pdf)
		{
			self::SF_PrintReports($rows);
		}
		else
		{
			survey_force_front_html::SF_ViewReports($rows, $lists, $pageNav, $option);
		}
	}

	public static function SF_ViewReportsPDF_full($option, $cid)
	{
		$database = JFactory::getDbo();
		$limit = intval(JFactory::getApplication()->getUserStateFromRequest("viewlistlimit", 'limit', 20));
		$limitstart = intval(JFactory::getApplication()->getUserStateFromRequest("viewlimitstart", 'limitstart', 0));
		$surv_id = intval(JFactory::getApplication()->getUserStateFromRequest("surv_id", 'surv_id', 0));
		$filt_status = intval(JFactory::getApplication()->getUserStateFromRequest("filt_status", 'filt_status', 2));
		$filt_utype = intval(JFactory::getApplication()->getUserStateFromRequest("filt_utype", 'filt_utype', 0));
		$filt_ulist = intval(JFactory::getApplication()->getUserStateFromRequest("filt_ulist", 'filt_ulist', 0));
		$filter_quest = intval(JFactory::getApplication()->getUserStateFromRequest("filter_quest", 'filter_quest', 0));
		$filter_ans = intval(JFactory::getApplication()->getUserStateFromRequest("filter_ans", 'filter_ans', 0));
		if ($limit == 0) $limit = 999999;
		$limit = intval(mosGetParam($_REQUEST, 'limit', JFactory::getSession()->get('list_limit', JFactory::getApplication()->getCfg('list_limit'))));
		if ($limit == 0) $limit = 999999;
		JFactory::getSession()->set('list_limit', $limit);
		$limitstart = intval(mosGetParam($_REQUEST, 'limitstart', JFactory::getSession()->get('list_limitstart', 0)));
		JFactory::getSession()->set('list_limitstart', $limitstart);
		$surv_id = intval(mosGetParam($_REQUEST, 'surv_id', JFactory::getSession()->get('list_surv_id', 0)));
		JFactory::getSession()->set('list_surv_id', $surv_id);

		$filt_status = intval(mosGetParam($_REQUEST, 'filt_status', JFactory::getSession()->get('list_filt_status', 2)));
		JFactory::getSession()->set('list_filt_status', $filt_status);
		$filt_utype = intval(mosGetParam($_REQUEST, 'filt_utype', JFactory::getSession()->get('list_filt_utype', 0)));
		JFactory::getSession()->set('list_filt_utype', $filt_utype);
		$filt_ulist = intval(mosGetParam($_REQUEST, 'filt_ulist', JFactory::getSession()->get('list_filt_ulist', 0)));
		JFactory::getSession()->set('list_filt_ulist', $filt_ulist);
		$filter_quest = intval(mosGetParam($_REQUEST, 'filter_quest', JFactory::getSession()->get('list_filter_quest', 0)));
		JFactory::getSession()->set('list_filter_quest', $filter_quest);
		$filter_ans = intval(mosGetParam($_REQUEST, 'filter_ans', JFactory::getSession()->get('list_filter_ans', 0)));
		JFactory::getSession()->set('list_filter_ans', $filter_ans);


		$javascript = 'onchange="submitbutton(\'reports\');"';
		$filter_quest = array();
		$filter_ans = array();
		$i = 0;
		$j = 0;
		$lists['filter_quest'] = array();
		$lists['filter_quest_ans'] = array();
		if ($surv_id)
		{
			$query = "SELECT count(*) FROM #__survey_force_quests WHERE published = 1 AND sf_survey = '" . $surv_id . "' and id = '" . $filter_quest . "' and sf_qtype IN (2,3)";
			$database->setQuery($query);
			if (!$database->loadResult())
			{
				if (isset($_REQUEST['filter_quest']))
				{
					$k = 0;
					foreach ($_REQUEST['filter_quest'] as $filt_row)
					{
						if ($filt_row)
						{
							$qlists = array();
							$query = "SELECT id AS value, sf_qtext AS text"
								. "\n FROM #__survey_force_quests WHERE published = 1 AND sf_survey = '" . $surv_id . "' and sf_qtype IN (2,3)"
								. "\n ORDER BY ordering, id ";
							$database->setQuery($query);
							$qlists[] = mosHTML::makeOption('0', JText::_('COM_SF_SELECT_QUESTION'));
							$qlists = @array_merge($qlists, $database->loadObjectList());
							$qlist = mosHTML::selectList($qlists, 'filter_quest[]', 'class="text_area" size="1" ' . $javascript, 'value', 'text', $filt_row);
							$filter_quest[$i] = $filt_row;
							$lists['filter_quest'][$i] = $qlist;
							$sel_ans = 0;
							if (isset($_REQUEST['filter_ans'][$k]) && $_REQUEST['filter_ans'][$k])
							{
								$sel_ans = $_REQUEST['filter_ans'][$k];
							}
							$query = "SELECT distinct a.answer AS value, b.ftext AS text"
								. "\n FROM #__survey_force_user_answers as a, #__survey_force_fields as b, #__survey_force_quests as c WHERE c.published = 1 AND a.quest_id = '" . $filt_row . "' and a.survey_id = '" . $surv_id . "' and a.quest_id = c.id and c.sf_qtype IN (2,3) and a.answer <> 0 and a.answer = b.id";
							$database->setQuery($query);
							$alists = array();
							$alists[] = mosHTML::makeOption('0', JText::_('COM_SF_SELECT_ANSWER'));
							$alists = @array_merge($alists, $database->loadObjectList());
							$alist = mosHTML::selectList($alists, 'filter_ans[]', 'class="text_area" size="1" ' . $javascript, 'value', 'text', $sel_ans);
							$filter_ans[$i] = $sel_ans;
							$lists['filter_quest_ans'][$i] = $alist;
							$i++;
							$k++;
						}
					}
				}
				$qlists = array();
				$query = "SELECT id AS value, sf_qtext AS text"
					. "\n FROM #__survey_force_quests WHERE published = 1 AND sf_survey = '" . $surv_id . "' and sf_qtype IN (2,3)"
					. "\n ORDER BY ordering, id ";
				$database->setQuery($query);
				$qlists[] = mosHTML::makeOption('0', JText::_('COM_SF_SELECT_QUESTION'));
				$qlists = @array_merge($qlists, $database->loadObjectList());
				$qlist = mosHTML::selectList($qlists, 'filter_quest[]', 'class="text_area" size="1" ' . $javascript, 'value', 'text', '0');
				$lists['filter_quest'][$i] = $qlist;
				$lists['filter_quest_ans'][$i] = '';
			}
		}

		if (($filt_utype - 1) != 2)
		{
			$filt_ulist = 0;
		}
		$query = "SELECT count(sf_ust.id) FROM #__survey_force_user_starts as sf_ust, #__survey_force_survs as sf_s"
			. "\n WHERE sf_ust.survey_id = sf_s.id"
			. ($surv_id ? "\n and sf_s.id = $surv_id" : '')
			. ($filt_status ? "\n and sf_ust.is_complete = '" . ($filt_status - 1) . "'" : '')
			. ($filt_utype ? "\n and sf_ust.usertype = '" . ($filt_utype - 1) . "'" : '');
		if ((count($cid) > 0) && ($cid[0] != 0))
		{
			$cids = implode(',', $cid);
			$query .= "\n and sf_ust.id in (" . $cids . ")";
		}

		$database->setQuery($query);
		$total = $database->loadResult();

		jimport('joomla.html.pagination');
		$pageNav = new SFPageNav($total, $limitstart, $limit);

		// get the subset (based on limits) of required records
		$query = "SELECT sf_ust.*, sf_s.sf_name as survey_name, u.username reg_username, u.name reg_name, u.email reg_email,"
			. "\n sf_u.name as inv_name, sf_u.lastname as inv_lastname, sf_u.email as inv_email"
			. "\n FROM (#__survey_force_user_starts as sf_ust, #__survey_force_survs as sf_s";
		$r = 0;
		foreach ($filter_ans as $filt_ans)
		{
			$query .= ($filt_ans ? ", #__survey_force_user_answers as sf_ans" . $r : '');
			$r++;
		}
		$query .= ")"
			. "\n LEFT JOIN #__users as u ON u.id = sf_ust.user_id and sf_ust.usertype=1"
			. "\n LEFT JOIN #__survey_force_users as sf_u ON sf_u.id = sf_ust.user_id and sf_ust.usertype=2"
			. "\n WHERE sf_ust.survey_id = sf_s.id"
			. ($surv_id ? "\n and sf_s.id = $surv_id" : '')
			. (" AND sf_s.sf_author = '" . JFactory::getUser()->id . "' ")
			. ($filt_status ? "\n and sf_ust.is_complete = '" . ($filt_status - 1) . "'" : '')
			. ($filt_utype ? "\n and sf_ust.usertype = '" . ($filt_utype - 1) . "'" : '')
			. ($filt_ulist ? "\n and sf_u.list_id = '" . ($filt_ulist) . "'" : '');
		if ((count($cid) > 0) && ($cid[0] != 0))
		{
			$cids = implode(',', $cid);
			$query .= "\n and sf_ust.id in (" . $cids . ")";
		}

		$r = 0;
		foreach ($filter_ans as $filt_ans)
		{
			$query .= ($filt_ans ? "\n and sf_ans" . $r . ".start_id = sf_ust.id and sf_ans" . $r . ".answer = '" . ($filt_ans) . "'" : '');
			$r++;

		}
		$query .= "\n ORDER BY sf_ust.sf_time DESC";
		$database->setQuery($query);
		$rows = $database->loadObjectList();
		$ri = 0;
		while ($ri < count($rows))
		{


			$query = "SELECT s.*, u.username reg_username, u.name reg_name, u.email reg_email,"
				. "\n sf_u.name as inv_name, sf_u.lastname as inv_lastname, sf_u.email as inv_email"
				. "\n FROM #__survey_force_user_starts as s"
				. "\n LEFT JOIN #__users as u ON u.id = s.user_id and s.usertype=1"
				. "\n LEFT JOIN #__survey_force_users as sf_u ON sf_u.id = s.user_id and s.usertype=2"
				. "\n WHERE s.id = '" . $rows[$ri]->id . "'";
			$database->setQuery($query);
			$rows[$ri]->start_data = $database->loadObjectList();
			$rows[$ri]->count_start_data = count($rows[$ri]->start_data);

			if ($rows[$ri]->count_start_data)
			{

				$query = "SELECT * FROM #__survey_force_survs WHERE id = '" . $rows[$ri]->start_data[0]->survey_id . "'";
				$database->setQuery($query);
				$rows[$ri]->survey_data = $database->loadObjectList();

				$query = "SELECT q.*"
					. "\n FROM #__survey_force_quests as q"
					. "\n WHERE q.published = 1 AND q.sf_survey = '" . $rows[$ri]->start_data[0]->survey_id . "' AND sf_qtype NOT IN (7, 8)"
					. "\n ORDER BY q.ordering, q.id";
				$database->setQuery($query);
				$rows[$ri]->questions_data = $database->loadObjectList();

				$qi = 0;
				$rows[$ri]->questions_data[$qi]->answer = '';
				while ($qi < count($rows[$ri]->questions_data))
				{
					if ($rows[$ri]->questions_data[$qi]->sf_impscale)
					{
						$query = "SELECT iscale_name FROM #__survey_force_iscales WHERE id = '" . $rows[$ri]->questions_data[$qi]->sf_impscale . "'";
						$database->setQuery($query);
						$rows[$ri]->questions_data[$qi]->iscale_name = $database->loadResult();

						$query = "SELECT iscalefield_id FROM #__survey_force_user_answers_imp"
							. "\n WHERE quest_id = '" . $rows[$ri]->questions_data[$qi]->id . "' and survey_id = '" . $rows[$ri]->questions_data[$qi]->sf_survey . "'"
							. "\n and iscale_id = '" . $rows[$ri]->questions_data[$qi]->sf_impscale . "'"
							. "\n AND start_id = '" . $rows[$ri]->id . "'";
						$database->setQuery($query);
						$ans_inf = $database->loadResult();

						$rows[$ri]->questions_data[$qi]->answer_imp = array();
						$query = "SELECT * FROM #__survey_force_iscales_fields WHERE iscale_id = '" . $rows[$ri]->questions_data[$qi]->sf_impscale . "'"
							. "\n ORDER BY ordering";
						$database->setQuery($query);
						$tmp_data = $database->loadObjectList();
						$j = 0;
						while ($j < count($tmp_data))
						{
							$rows[$ri]->questions_data[$qi]->answer_imp[$j]->num = $j;
							$rows[$ri]->questions_data[$qi]->answer_imp[$j]->f_id = $tmp_data[$j]->id;
							$rows[$ri]->questions_data[$qi]->answer_imp[$j]->f_text = $tmp_data[$j]->isf_name;
							$rows[$ri]->questions_data[$qi]->answer_imp[$j]->alt_text = '';
							if ($ans_inf == $tmp_data[$j]->id)
							{
								$rows[$ri]->questions_data[$qi]->answer_imp[$j]->alt_text = '1';
								$rows[$ri]->questions_data[$qi]->answer_imp[$j]->alt_id = $ans_inf;
							}
							$j++;
						}
					}
					$rows[$ri]->questions_data[$qi]->sf_qtext = trim(strip_tags($rows[$ri]->questions_data[$qi]->sf_qtext, '<a><b><i><u>'));
					switch ($rows[$ri]->questions_data[$qi]->sf_qtype)
					{
						case 1:
							$rows[$ri]->questions_data[$qi]->answer = array();
							$rows[$ri]->questions_data[$qi]->scale = '';
							$query = "SELECT stext FROM #__survey_force_scales WHERE quest_id = '" . $rows[$ri]->questions_data[$qi]->id . "'"
								. "\n and quest_id = '" . $rows[$ri]->questions_data[$qi]->id . "'"
								. "\n ORDER BY ordering";
							$database->setQuery($query);
							$tmp_data = $database->loadColumn();
							$rows[$ri]->questions_data[$qi]->scale = implode(', ', $tmp_data);

							$query = "SELECT * FROM #__survey_force_fields WHERE quest_id = '" . $rows[$ri]->questions_data[$qi]->id . "'"
								. "\n and is_main = 1 ORDER BY ordering";
							$database->setQuery($query);
							$tmp_data = $database->loadObjectList();

							$query = "SELECT * FROM #__survey_force_user_answers WHERE quest_id = '" . $rows[$ri]->questions_data[$qi]->id . "' and survey_id = '" . $rows[$ri]->questions_data[$qi]->sf_survey . "' and start_id = '" . $rows[$ri]->id . "'";
							$database->setQuery($query);
							$ans_inf_data = $database->loadObjectList();

							$j = 0;
							while ($j < count($tmp_data))
							{
								$rows[$ri]->questions_data[$qi]->answer[$j]->num = $j;
								$rows[$ri]->questions_data[$qi]->answer[$j]->f_id = $tmp_data[$j]->id;
								$rows[$ri]->questions_data[$qi]->answer[$j]->f_text = $tmp_data[$j]->ftext;
								$rows[$ri]->questions_data[$qi]->answer[$j]->alt_text = JText::_('COM_SURVEYFORCE_NO_ANSWER');
								foreach ($ans_inf_data as $ans_data)
								{
									if ($ans_data->answer == $tmp_data[$j]->id)
									{
										$query = "SELECT * FROM #__survey_force_scales WHERE id = '" . $ans_data->ans_field . "'"
											. "\n and quest_id = '" . $rows[$ri]->questions_data[$qi]->id . "'"
											. "\n ORDER BY ordering";
										$database->setQuery($query);
										$alt_data = $database->loadObjectList();
										$rows[$ri]->questions_data[$qi]->answer[$j]->alt_text = ($ans_data->ans_field == 0 ? JText::_('COM_SURVEYFORCE_NO_ANSWER') : $alt_data[0]->stext);
										$rows[$ri]->questions_data[$qi]->answer[$j]->alt_id = $ans_data->ans_field;
									}
								}
								$j++;
							}
							break;
						case 2:
							$query = "SELECT a.answer, b.ans_txt FROM ( #__survey_force_user_answers AS a, #__survey_force_quests AS c ) LEFT JOIN #__survey_force_user_ans_txt AS b ON ( a.ans_field = b.id AND c.sf_qtype = 2 ) WHERE c.published = 1 AND a.quest_id = '" . $rows[$ri]->questions_data[$qi]->id . "' and a.survey_id = '" . $rows[$ri]->questions_data[$qi]->sf_survey . "' and a.start_id = '" . $rows[$ri]->id . "' AND c.id = a.quest_id ";
							$database->setQuery($query);
							$ans_inf = $database->loadObjectList();

							$rows[$ri]->questions_data[$qi]->answer = array();
							$query = "SELECT * FROM #__survey_force_fields WHERE quest_id = '" . $rows[$ri]->questions_data[$qi]->id . "'"
								. "\n ORDER BY ordering";
							$database->setQuery($query);
							$tmp_data = $database->loadObjectList();
							$j = 0;
							while ($j < count($tmp_data))
							{
								$rows[$ri]->questions_data[$qi]->answer[$j]->num = $j;
								$rows[$ri]->questions_data[$qi]->answer[$j]->f_id = $tmp_data[$j]->id;
								$rows[$ri]->questions_data[$qi]->answer[$j]->f_text = $tmp_data[$j]->ftext;
								$rows[$ri]->questions_data[$qi]->answer[$j]->alt_text = '';
								if (count($ans_inf) > 0 && $ans_inf[0]->answer == $tmp_data[$j]->id)
								{
									$rows[$ri]->questions_data[$qi]->answer[$j]->f_text = $tmp_data[$j]->ftext . ($ans_inf[0]->ans_txt != '' ? ' (' . $ans_inf[0]->ans_txt . ')' : '');
									$rows[$ri]->questions_data[$qi]->answer[$j]->alt_text = '1';
									$rows[$ri]->questions_data[$qi]->answer[$j]->alt_id = $ans_inf;
								}
								$j++;
							}
							break;
						case 3:
							$query = "SELECT a.answer, b.ans_txt FROM ( #__survey_force_user_answers AS a, #__survey_force_quests AS c ) LEFT JOIN #__survey_force_user_ans_txt AS b ON ( a.ans_field = b.id AND c.sf_qtype = 3 ) WHERE c.published = 1 AND a.quest_id = '" . $rows[$ri]->questions_data[$qi]->id . "' and a.survey_id = '" . $rows[$ri]->questions_data[$qi]->sf_survey . "' and a.start_id = '" . $rows[$ri]->id . "'  AND c.id = a.quest_id ";
							$database->setQuery($query);
							$ans_inf_data = $database->loadObjectList();

							$questions_data = array();
							$questions_data[$i]->answer = array();
							$query = "SELECT * FROM #__survey_force_fields WHERE quest_id = '" . $rows[$ri]->questions_data[$qi]->id . "'"
								. "\n ORDER BY ordering";
							$database->setQuery($query);
							$tmp_data = $database->loadObjectList();
							$j = 0;
							while ($j < count($tmp_data))
							{
								$rows[$ri]->questions_data[$qi]->answer[$j]->num = $j;
								$rows[$ri]->questions_data[$qi]->answer[$j]->f_id = $tmp_data[$j]->id;
								$rows[$ri]->questions_data[$qi]->answer[$j]->f_text = $tmp_data[$j]->ftext;
								$rows[$ri]->questions_data[$qi]->answer[$j]->alt_text = '';
								foreach ($ans_inf_data as $ans_data)
								{
									if ($ans_data->answer == $tmp_data[$j]->id)
									{
										$rows[$ri]->questions_data[$qi]->answer[$j]->f_text = $tmp_data[$j]->ftext . ($ans_data->ans_txt != '' ? ' (' . $ans_data->ans_txt . ')' : '');
										$rows[$ri]->questions_data[$qi]->answer[$j]->alt_text = '1';
										$rows[$ri]->questions_data[$qi]->answer[$j]->alt_id = $ans_data->answer;
									}
								}
								$j++;
							}
							break;
						case 4:
							$n = mb_substr_count($rows[$ri]->questions_data[$qi]->sf_qtext, "{x}") + mb_substr_count($rows[$ri]->questions_data[$qi]->sf_qtext, "{y}");
							if ($n > 0)
							{
								$query = "SELECT b.ans_txt, a.ans_field FROM #__survey_force_user_answers as a LEFT JOIN #__survey_force_user_ans_txt as b ON a.answer = b.id	WHERE a.quest_id = '" . $rows[$ri]->questions_data[$qi]->id . "' AND a.survey_id = '" . $rows[$ri]->questions_data[$qi]->sf_survey . "' AND a.start_id = '" . $rows[$ri]->id . "' ORDER BY a.ans_field ";
								$database->setQuery($query);
								$ans_inf_data = $database->loadObjectList();
								$rows[$ri]->questions_data[$qi]->answer = $ans_inf_data;
								$rows[$ri]->questions_data[$qi]->answer_count = $n;
							}
							else
							{
								$query = "SELECT b.ans_txt FROM #__survey_force_user_answers as a, #__survey_force_user_ans_txt as b WHERE a.quest_id = '" . $rows[$ri]->questions_data[$qi]->id . "' and a.survey_id = '" . $rows[$ri]->questions_data[$qi]->sf_survey . "' and a.start_id = '" . $rows[$ri]->id . "' and a.answer = b.id";
								$database->setQuery($query);
								$ans_inf_data = $database->loadResult();
								$rows[$ri]->questions_data[$qi]->answer = ($ans_inf_data == '') ? JText::_('COM_SURVEYFORCE_NO_ANSWER') : $ans_inf_data;
							}
							break;
						case 5:
						case 6:
						case 9:
							$query = "SELECT a.*, b.ans_txt FROM ( #__survey_force_user_answers AS a, #__survey_force_quests AS c ) LEFT JOIN #__survey_force_user_ans_txt AS b ON ( a.next_quest_id = b.id AND c.sf_qtype = 9 ) WHERE c.published = 1 AND a.quest_id = '" . $rows[$ri]->questions_data[$qi]->id . "' and a.survey_id = '" . $rows[$ri]->questions_data[$qi]->sf_survey . "' and a.start_id = '" . $rows[$ri]->id . "' AND c.id=a.quest_id ";
							$database->setQuery($query);
							$ans_inf_data = $database->loadObjectList();

							$rows[$ri]->questions_data[$qi]->answer = array();
							$query = "SELECT * FROM #__survey_force_fields WHERE quest_id = '" . $rows[$ri]->questions_data[$qi]->id . "'"
								. "\n and is_main = 1 ORDER BY ordering";
							$database->setQuery($query);
							$tmp_data = $database->loadObjectList();
							$j = 0;
							while ($j < count($tmp_data))
							{
								$rows[$ri]->questions_data[$qi]->answer[$j]->num = $j;
								$rows[$ri]->questions_data[$qi]->answer[$j]->f_id = $tmp_data[$j]->id;
								$rows[$ri]->questions_data[$qi]->answer[$j]->f_text = $tmp_data[$j]->ftext;
								$rows[$ri]->questions_data[$qi]->answer[$j]->alt_text = ($rows[$ri]->questions_data[$qi]->sf_qtype == 9 ? JText::_('COM_SURVEYFORCE_NO_ANSWER') : JText::_('COM_SURVEYFORCE_NO_ANSWER'));
								foreach ($ans_inf_data as $ans_data)
								{
									if ($ans_data->answer == $tmp_data[$j]->id)
									{
										$rows[$ri]->questions_data[$qi]->answer[$j]->f_text = $tmp_data[$j]->ftext . ($ans_data->ans_txt != '' ? ' (' . $ans_data->ans_txt . ')' : '');

										$query = "SELECT * FROM #__survey_force_fields WHERE id = '" . $ans_data->ans_field . "'"
											. "\n and quest_id = '" . $rows[$ri]->questions_data[$qi]->id . "'"
											. "\n and is_main = 0 ORDER BY ordering";
										$database->setQuery($query);
										$alt_data = $database->loadObjectList();
										$rows[$ri]->questions_data[$qi]->answer[$j]->alt_text = ($ans_data->ans_field == 0 ? ($rows[$ri]->questions_data[$qi]->sf_qtype == 9 ? JText::_('COM_SURVEYFORCE_NO_ANSWER') : JText::_('COM_SURVEYFORCE_NO_ANSWER')) : $alt_data[0]->ftext);
										$rows[$ri]->questions_data[$qi]->answer[$j]->alt_id = $ans_data->ans_field;
									}
								}
								$j++;
							}
							break;

						default:
							if (!$rows[$ri]->questions_data[$qi]->answer) $rows[$ri]->questions_data[$qi]->answer = JText::_('COM_SURVEYFORCE_NO_ANSWER');
							break;
					}
					$qi++;
				}
			} // end if (count(start_data);//


			$ri++;
		}

		self::SF_PrintReportsPDF_full($rows);
	}

	public static function SF_ViewReportsCSV_full($option, $cid)
	{
		$database = JFactory::getDbo();
		$limit = intval(JFactory::getApplication()->getUserStateFromRequest("viewlistlimit", 'limit', 20));
		$limitstart = intval(JFactory::getApplication()->getUserStateFromRequest("viewlimitstart", 'limitstart', 0));
		$surv_id = intval(JFactory::getApplication()->getUserStateFromRequest("surv_id", 'surv_id', 0));
		$filt_status = intval(JFactory::getApplication()->getUserStateFromRequest("filt_status", 'filt_status', 2));
		$filt_utype = intval(JFactory::getApplication()->getUserStateFromRequest("filt_utype", 'filt_utype', 0));
		$filt_ulist = intval(JFactory::getApplication()->getUserStateFromRequest("filt_ulist", 'filt_ulist', 0));
		$filter_quest = intval(JFactory::getApplication()->getUserStateFromRequest("filter_quest", 'filter_quest', 0));
		$filter_ans = intval(JFactory::getApplication()->getUserStateFromRequest("filter_ans", 'filter_ans', 0));
		if ($limit == 0) $limit = 999999;
		$limit = intval(mosGetParam($_REQUEST, 'limit', JFactory::getSession()->get('list_limit', JFactory::getApplication()->getCfg('list_limit'))));
		if ($limit == 0) $limit = 999999;
		JFactory::getSession()->set('list_limit', $limit);
		$limitstart = intval(mosGetParam($_REQUEST, 'limitstart', JFactory::getSession()->get('list_limitstart', 0)));
		JFactory::getSession()->set('list_limitstart', $limitstart);
		$surv_id = intval(mosGetParam($_REQUEST, 'surv_id', JFactory::getSession()->get('list_surv_id', 0)));
		JFactory::getSession()->set('list_surv_id', $surv_id);

		$filt_status = intval(mosGetParam($_REQUEST, 'filt_status', JFactory::getSession()->get('list_filt_status', 2)));
		JFactory::getSession()->set('list_filt_status', $filt_status);
		$filt_utype = intval(mosGetParam($_REQUEST, 'filt_utype', JFactory::getSession()->get('list_filt_utype', 0)));
		JFactory::getSession()->set('list_filt_utype', $filt_utype);
		$filt_ulist = intval(mosGetParam($_REQUEST, 'filt_ulist', JFactory::getSession()->get('list_filt_ulist', 0)));
		JFactory::getSession()->set('list_filt_ulist', $filt_ulist);
		$filter_quest = intval(mosGetParam($_REQUEST, 'filter_quest', JFactory::getSession()->get('list_filter_quest', 0)));
		JFactory::getSession()->set('list_filter_quest', $filter_quest);
		$filter_ans = intval(mosGetParam($_REQUEST, 'filter_ans', JFactory::getSession()->get('list_filter_ans', 0)));
		JFactory::getSession()->set('list_filter_ans', $filter_ans);
		$javascript = 'onchange="submitbutton(\'reports\');"';
		$filter_quest = array();
		$filter_ans = array();
		$i = 0;
		$j = 0;
		if ($surv_id)
		{
			$query = "SELECT count(*) FROM #__survey_force_quests WHERE published = 1 AND sf_survey = '" . $surv_id . "' and id = '" . $filter_quest . "' and sf_qtype IN (2,3)";
			$database->setQuery($query);
			if (!$database->loadResult())
			{
				if (isset($_REQUEST['filter_quest']))
				{
					$k = 0;
					foreach ($_REQUEST['filter_quest'] as $filt_row)
					{
						if ($filt_row)
						{
							$filter_quest[$i] = $filt_row;
							$sel_ans = 0;
							if (isset($_REQUEST['filter_ans'][$k]) && $_REQUEST['filter_ans'][$k])
							{
								$sel_ans = $_REQUEST['filter_ans'][$k];
							}
							$filter_ans[$i] = $sel_ans;
							$i++;
							$k++;
						}
					}
				}
			}
		}

		if (($filt_utype - 1) != 2)
		{
			$filt_ulist = 0;
		}

		// get the subset (based on limits) of required records
		$query = "SELECT distinct sf_s.sf_name as survey_name, sf_s.id as survey_id "
			. "\n FROM #__survey_force_user_starts as sf_ust, #__survey_force_survs as sf_s";
		$r = 0;
		foreach ($filter_ans as $filt_ans)
		{
			$query .= ($filt_ans ? ", #__survey_force_user_answers as sf_ans" . $r : '');
			$r++;
		}
		$query .= ""
			. "\n WHERE sf_ust.survey_id = sf_s.id"
			. ($surv_id ? "\n and sf_s.id = $surv_id" : '')
			. (" AND sf_s.sf_author = '" . JFactory::getUser()->id . "' ")
			. ($filt_status ? "\n and sf_ust.is_complete = '" . ($filt_status - 1) . "'" : '')
			. ($filt_utype ? "\n and sf_ust.usertype = '" . ($filt_utype - 1) . "'" : '')
			. ($filt_ulist ? "\n and sf_u.list_id = '" . ($filt_ulist) . "'" : '');

		if ((count($cid) > 0) && ($cid[0] != 0))
		{
			$cids = implode(',', $cid);
			$query .= "\n and sf_ust.id in (" . $cids . ")";
		}

		$r = 0;
		foreach ($filter_ans as $filt_ans)
		{
			$query .= ($filt_ans ? "\n and sf_ans" . $r . ".start_id = sf_ust.id and sf_ans" . $r . ".answer = '" . ($filt_ans) . "'" : '');
			$r++;

		}
		$query .= "\n ORDER BY sf_s.sf_name";
		$database->setQuery($query);
		$rows = $database->loadObjectList();

		$query = "SELECT distinct sf_ust.id "
			. "\n FROM #__survey_force_user_starts as sf_ust, #__survey_force_survs as sf_s";
		$r = 0;
		foreach ($filter_ans as $filt_ans)
		{
			$query .= ($filt_ans ? ", #__survey_force_user_answers as sf_ans" . $r : '');
			$r++;
		}
		$query .= ""
			. "\n WHERE sf_ust.survey_id = sf_s.id"
			. ($surv_id ? "\n and sf_s.id = $surv_id" : '')
			. ($filt_status ? "\n and sf_ust.is_complete = '" . ($filt_status - 1) . "'" : '')
			. ($filt_utype ? "\n and sf_ust.usertype = '" . ($filt_utype - 1) . "'" : '')
			. ($filt_ulist ? "\n and sf_u.list_id = '" . ($filt_ulist) . "'" : '');

		if ((count($cid) > 0) && ($cid[0] != 0))
		{
			$cids = implode(',', $cid);
			$query .= "\n and sf_ust.id in (" . $cids . ")";
		}

		$r = 0;
		foreach ($filter_ans as $filt_ans)
		{
			$query .= ($filt_ans ? "\n and sf_ans" . $r . ".start_id = sf_ust.id and sf_ans" . $r . ".answer = '" . ($filt_ans) . "'" : '');
			$r++;

		}
		$query .= "\n ORDER BY sf_ust.id";
		$database->setQuery($query);
		$start_ids = array();
		$start_ids = $database->loadColumn();
		$start_ids[] = 0;
		$starts_str = implode(',', $start_ids);

		$ri = 0;
		while ($ri < count($rows))
		{

			$query = "SELECT * FROM #__survey_force_survs WHERE id = '" . $rows[$ri]->survey_id . "'";
			$database->setQuery($query);
			$rows[$ri]->survey_data = $database->loadObjectList();

			$query = "SELECT q.*"
				. "\n FROM #__survey_force_quests as q"
				. "\n WHERE q.published = 1 AND q.sf_survey = '" . $rows[$ri]->survey_id . "' AND sf_qtype NOT IN (7, 8)"
				. "\n ORDER BY q.ordering, q.id ";
			$database->setQuery($query);
			$rows[$ri]->questions_data = $database->loadObjectList();
			$qi = 0;
			$rows[$ri]->questions_data[$qi]->answer = '';
			while ($qi < count($rows[$ri]->questions_data))
			{
				if ($rows[$ri]->questions_data[$qi]->sf_impscale)
				{
					$query = "SELECT iscale_name FROM #__survey_force_iscales WHERE id = '" . $rows[$ri]->questions_data[$qi]->sf_impscale . "'";
					$database->setQuery($query);
					$rows[$ri]->questions_data[$qi]->iscale_name = $database->loadResult();

					$query = "SELECT count(id) FROM #__survey_force_user_answers_imp"
						. "\n WHERE quest_id = '" . $rows[$ri]->questions_data[$qi]->id . "' and survey_id = '" . $rows[$ri]->questions_data[$qi]->sf_survey . "'"
						. "\n AND iscale_id = '" . $rows[$ri]->questions_data[$qi]->sf_impscale . "' and start_id IN (" . $starts_str . ")";
					$database->setQuery($query);
					$rows[$ri]->questions_data[$qi]->total_iscale_answers = $database->loadResult();

					$query = "SELECT b.isf_name, count(a.id) as ans_count FROM #__survey_force_iscales_fields as b"
						. "\n LEFT JOIN #__survey_force_user_answers_imp as a ON"
						. "\n a.quest_id = '" . $rows[$ri]->questions_data[$qi]->id . "'"
						. "\n and a.survey_id = '" . $rows[$ri]->questions_data[$qi]->sf_survey . "'"
						. "\n and a.iscale_id = '" . $rows[$ri]->questions_data[$qi]->sf_impscale . "'"
						. "\n and a.start_id IN (" . $starts_str . ") and a.iscalefield_id = b.id "
						. "\n WHERE b.iscale_id = '" . $rows[$ri]->questions_data[$qi]->sf_impscale . "'"
						. "\n GROUP BY b.isf_name ORDER BY  b.ordering";//ans_count DESC,
					$database->setQuery($query);
					$ans_data = $database->loadObjectList();

					$rows[$ri]->questions_data[$qi]->answer_imp = array();
					$j = 0;
					while ($j < count($ans_data))
					{
						$rows[$ri]->questions_data[$qi]->answer_imp[$j]->num = $j;
						$rows[$ri]->questions_data[$qi]->answer_imp[$j]->ftext = $ans_data[$j]->isf_name;
						$rows[$ri]->questions_data[$qi]->answer_imp[$j]->ans_count = $ans_data[$j]->ans_count;
						$j++;
					}
				}
				$rows[$ri]->questions_data[$qi]->sf_qtext = trim(strip_tags($rows[$ri]->questions_data[$qi]->sf_qtext, '<a><b><i><u>'));
				switch ($rows[$ri]->questions_data[$qi]->sf_qtype)
				{
					case 2:
						$query = "SELECT count(id) FROM #__survey_force_user_answers"
							. "\n WHERE quest_id = '" . $rows[$ri]->questions_data[$qi]->id . "'"
							. "\n and survey_id = '" . $rows[$ri]->questions_data[$qi]->sf_survey . "'"
							. "\n and start_id IN (" . $starts_str . ") ";
						$database->setQuery($query);
						$rows[$ri]->questions_data[$qi]->total_answers = $database->loadResult();

						$query = "SELECT b.ftext, count(a.answer) as ans_count FROM #__survey_force_fields as b"
							. "\n LEFT JOIN #__survey_force_user_answers as a ON ( a.answer = b.id and a.start_id IN (" . $starts_str . ") AND a.quest_id = '" . $rows[$ri]->questions_data[$qi]->id . "' )"
							. "\n WHERE b.quest_id = '" . $rows[$ri]->questions_data[$qi]->id . "'"
							. "\n GROUP BY b.ftext ORDER BY b.ordering";//ans_count DESC
						$database->setQuery($query);
						$ans_data = $database->loadObjectList();
						$rows[$ri]->questions_data[$qi]->answer = array();
						$j = 0;
						while ($j < count($ans_data))
						{
							$rows[$ri]->questions_data[$qi]->answer[$j]->num = $j;
							$rows[$ri]->questions_data[$qi]->answer[$j]->ftext = $ans_data[$j]->ftext;
							$rows[$ri]->questions_data[$qi]->answer[$j]->ans_count = $ans_data[$j]->ans_count;
							$j++;
						}
						break;
					case 3:
						$query = "SELECT count(distinct start_id) FROM #__survey_force_user_answers"
							. "\n WHERE quest_id = '" . $rows[$ri]->questions_data[$qi]->id . "'"
							. "\n and survey_id = '" . $rows[$ri]->questions_data[$qi]->sf_survey . "' "
							. "\n and start_id IN (" . $starts_str . ") ";
						$database->setQuery($query);
						$rows[$ri]->questions_data[$qi]->total_answers = $database->loadResult();

						$query = "SELECT b.ftext, count(a.answer) as ans_count FROM #__survey_force_fields as b"
							. "\n LEFT JOIN #__survey_force_user_answers as a ON ( a.answer = b.id and a.start_id IN (" . $starts_str . ") AND a.quest_id = '" . $rows[$ri]->questions_data[$qi]->id . "' )"
							. "\n WHERE b.quest_id = '" . $rows[$ri]->questions_data[$qi]->id . "'"
							. "\n GROUP BY b.ftext ORDER BY b.ordering";//ans_count DESC
						$database->setQuery($query);
						$ans_data = $database->loadObjectList();
						$rows[$ri]->questions_data[$qi]->answer = array();
						$j = 0;
						while ($j < count($ans_data))
						{
							$rows[$ri]->questions_data[$qi]->answer[$j]->num = $j;
							$rows[$ri]->questions_data[$qi]->answer[$j]->ftext = $ans_data[$j]->ftext;
							$rows[$ri]->questions_data[$qi]->answer[$j]->ans_count = $ans_data[$j]->ans_count;
							$j++;
						}
						break;
					case 4:
						$n = mb_substr_count($rows[$ri]->questions_data[$qi]->sf_qtext, '{x}') + mb_substr_count($rows[$ri]->questions_data[$qi]->sf_qtext, '{y}');
						if ($n > 0)
						{
							$query = "SELECT id FROM #__survey_force_user_answers"
								. "\n WHERE quest_id = '" . $rows[$ri]->questions_data[$qi]->id . "'"
								. "\n and survey_id = '" . $rows[$ri]->questions_data[$qi]->sf_survey . "'"
								. "\n and start_id IN (" . $starts_str . ") GROUP BY start_id, quest_id";
							$database->setQuery($query);
							$rows[$ri]->questions_data[$qi]->total_answers = count($database->loadColumn());
							$rows[$ri]->questions_data[$qi]->answer = array();
							$rows[$ri]->questions_data[$qi]->answers_top100 = array();
							$rows[$ri]->questions_data[$qi]->answer_count = $n;
							for ($j = 0; $j < $n; $j++)
							{
								$query = "SELECT answer FROM #__survey_force_user_answers WHERE ans_field = " . ($j + 1)
									. " AND quest_id = '" . $rows[$ri]->questions_data[$qi]->id . "'"
									. " AND survey_id = '" . $rows[$ri]->questions_data[$qi]->sf_survey . "'"
									. " AND start_id IN (" . $starts_str . ") ";
								$database->setQuery($query);
								$ans_txt_data = @array_merge(array(0 => 0), $database->loadColumn());

								$query = "SELECT b.ans_txt, count(a.answer) as ans_count FROM #__survey_force_user_ans_txt as b,"
									. "\n #__survey_force_user_answers as a"
									. "\n WHERE a.quest_id = '" . $rows[$ri]->questions_data[$qi]->id . "'"
									. "\n and a.answer = b.id and a.start_id IN (" . $starts_str . ")"
									. "\n AND a.answer IN (" . implode(',', $ans_txt_data) . ") "
									. "\n GROUP BY b.ans_txt ORDER BY ans_count DESC LIMIT 0,5";
								$database->setQuery($query);
								$ans_data = $database->loadObjectList();
								$jj = 0;
								$tmp = array();
								while ($jj < count($ans_data))
								{
									$tmp[$jj]->num = $jj;
									$tmp[$jj]->ftext = $ans_data[$jj]->ans_txt;
									$tmp[$jj]->ans_count = $ans_data[$jj]->ans_count;
									$jj++;
								}
								$rows[$ri]->questions_data[$qi]->answer[$j] = $tmp;

								$query = "SELECT b.ans_txt FROM #__survey_force_user_ans_txt as b, #__survey_force_user_answers as a"
									. "\n WHERE a.quest_id = '" . $rows[$ri]->questions_data[$qi]->id . "' and a.answer = b.id"
									. "\n and a.start_id IN (" . $starts_str . ")"
									. "\n AND a.answer IN (" . implode(',', $ans_txt_data) . ") "
									. "\n ORDER BY a.sf_time DESC LIMIT 0,100";
								$database->setQuery($query);
								$ans_data = $database->loadColumn();
								$rows[$ri]->questions_data[$qi]->answers_top100[$j] = implode(', ', $ans_data);
							}
						}
						else
						{
							$query = "SELECT id FROM #__survey_force_user_answers"
								. "\n WHERE quest_id = '" . $rows[$ri]->questions_data[$qi]->id . "'"
								. "\n and survey_id = '" . $rows[$ri]->questions_data[$qi]->sf_survey . "'"
								. "\n and start_id IN (" . $starts_str . ") GROUP BY start_id, quest_id";
							$database->setQuery($query);
							$rows[$ri]->questions_data[$qi]->total_answers = count($database->loadColumn());

							$query = "SELECT b.ans_txt, count(a.answer) as ans_count FROM #__survey_force_user_ans_txt as b,"
								. "\n #__survey_force_user_answers as a"
								. "\n WHERE a.quest_id = '" . $rows[$ri]->questions_data[$qi]->id . "'"
								. "\n and a.answer = b.id and a.start_id IN (" . $starts_str . ")"
								. "\n GROUP BY b.ans_txt ORDER BY ans_count DESC LIMIT 0,5";
							$database->setQuery($query);
							$ans_data = $database->loadObjectList();
							$rows[$ri]->questions_data[$qi]->answer = array();
							$j = 0;
							while ($j < count($ans_data))
							{
								$rows[$ri]->questions_data[$qi]->answer[$j]->num = $j;
								$rows[$ri]->questions_data[$qi]->answer[$j]->ftext = $ans_data[$j]->ans_txt;
								$rows[$ri]->questions_data[$qi]->answer[$j]->ans_count = $ans_data[$j]->ans_count;
								$j++;
							}
							$query = "SELECT b.ans_txt FROM #__survey_force_user_ans_txt as b, #__survey_force_user_answers as a"
								. "\n WHERE a.quest_id = '" . $rows[$ri]->questions_data[$qi]->id . "' and a.answer = b.id"
								. "\n and a.start_id IN (" . $starts_str . ")"
								. "\n ORDER BY a.sf_time DESC LIMIT 0,100";
							$database->setQuery($query);
							$ans_data = $database->loadColumn();
							$rows[$ri]->questions_data[$qi]->answers_top100 = implode(', ', $ans_data);
						}
						break;
					case 1:
						$query = "SELECT count(distinct start_id) FROM #__survey_force_user_answers"
							. "\n WHERE quest_id = '" . $rows[$ri]->questions_data[$qi]->id . "'"
							. "\n and survey_id = '" . $rows[$ri]->questions_data[$qi]->sf_survey . "'"
							. "\n and start_id IN (" . $starts_str . ")";
						$database->setQuery($query);
						$rows[$ri]->questions_data[$qi]->total_answers = $database->loadResult();

						$query = "SELECT * FROM #__survey_force_fields"
							. "\n WHERE quest_id = '" . $rows[$ri]->questions_data[$qi]->id . "' ORDER by ordering";
						$database->setQuery($query);
						$f_data = $database->loadObjectList();
						$j = 0;
						$rows[$ri]->questions_data[$qi]->answer = array();
						while ($j < count($f_data))
						{
							$query = "SELECT b.stext, count(a.answer) as ans_count FROM #__survey_force_scales as b"
								. "\n LEFT JOIN #__survey_force_user_answers as a"
								. "\n ON ( a.ans_field = b.id and a.answer = '" . $f_data[$j]->id . "' "
								. "\n and a.start_id IN (" . $starts_str . ") AND a.quest_id = '" . $rows[$ri]->questions_data[$qi]->id . "' )"
								. "\n WHERE b.quest_id = '" . $rows[$ri]->questions_data[$qi]->id . "'"
								. "\n GROUP BY b.stext ORDER BY b.ordering";
							$database->setQuery($query);
							$ans_data = $database->loadObjectList();
							$rows[$ri]->questions_data[$qi]->answer[$j]->full_ans = array();
							$jj = 0;
							$rows[$ri]->questions_data[$qi]->answer[$j]->ftext = $f_data[$j]->ftext;
							while ($jj < count($ans_data))
							{
								$rows[$ri]->questions_data[$qi]->answer[$j]->full_ans[$jj]->ftext = $ans_data[$jj]->stext;
								$rows[$ri]->questions_data[$qi]->answer[$j]->full_ans[$jj]->ans_count = $ans_data[$jj]->ans_count;
								$jj++;
							}
							$j++;
						}
						break;
					case 5:
					case 6:
					case 9:
						$query = "SELECT count(distinct start_id) FROM #__survey_force_user_answers"
							. "\n WHERE quest_id = '" . $rows[$ri]->questions_data[$qi]->id . "'"
							. "\n and survey_id = '" . $rows[$ri]->questions_data[$qi]->sf_survey . "'"
							. "\n and start_id IN (" . $starts_str . ")";
						$database->setQuery($query);
						$rows[$ri]->questions_data[$qi]->total_answers = $database->loadResult();

						$query = "SELECT * FROM #__survey_force_fields"
							. "\n WHERE quest_id = '" . $rows[$ri]->questions_data[$qi]->id . "' and is_main = '1' ORDER by ordering";
						$database->setQuery($query);
						$f_data = $database->loadObjectList();
						$j = 0;
						$rows[$ri]->questions_data[$qi]->answer = array();
						while ($j < count($f_data))
						{
							$query = "SELECT b.ftext, count(a.answer) as ans_count FROM #__survey_force_fields as b"
								. "\n LEFT JOIN #__survey_force_user_answers as a ON a.ans_field = b.id"
								. "\n and a.answer = '" . $f_data[$j]->id . "'"
								. "\n and a.quest_id = '" . $rows[$ri]->questions_data[$qi]->id . "'"
								. "\n and a.survey_id = '" . $rows[$ri]->questions_data[$qi]->sf_survey . "'"
								. "\n and a.start_id IN (" . $starts_str . ")"
								. "\n WHERE b.quest_id = '" . $rows[$ri]->questions_data[$qi]->id . "' and b.is_main = '0'"
								. "\n GROUP BY b.ftext ORDER BY b.ordering ";//ans_count DESC

							$database->setQuery($query);
							$ans_data = $database->loadObjectList();
							$rows[$ri]->questions_data[$qi]->answer[$j]->full_ans = array();
							$jj = 0;
							$rows[$ri]->questions_data[$qi]->answer[$j]->ftext = $f_data[$j]->ftext;
							while ($jj < count($ans_data))
							{
								$rows[$ri]->questions_data[$qi]->answer[$j]->full_ans[$jj]->ftext = $ans_data[$jj]->ftext;
								$rows[$ri]->questions_data[$qi]->answer[$j]->full_ans[$jj]->ans_count = $ans_data[$jj]->ans_count;
								$jj++;
							}
							$j++;
						}
						break;
				}
				$qi++;
			}

			$ri++;
		}
		self::SF_PrintReportsCSV_sum($rows);
	}

	public static function SF_removeRep(&$cid, $option)
	{
		$database = JFactory::getDbo();
		if (count($cid))
		{
			$cids = implode(',', $cid);
			$query = "DELETE FROM #__survey_force_user_starts"
				. "\n WHERE id IN ( $cids )";
			$database->setQuery($query);
			if (!$database->execute())
			{
				echo "<script> alert('" . $database->getErrorMsg() . "'); window.history.go(-1); </script>\n";
			}

			$query = "DELETE FROM #__survey_force_user_chain "
				. "\n WHERE start_id IN ( $cids )";
			$database->setQuery($query);
			if (!$database->execute())
			{
				echo "<script> alert('" . $database->getErrorMsg() . "'); window.history.go(-1); </script>\n";
			}

			$query = "DELETE FROM #__survey_force_user_answers"
				. "\n WHERE start_id IN ( $cids )";
			$database->setQuery($query);
			if (!$database->execute())
			{
				echo "<script> alert('" . $database->getErrorMsg() . "'); window.history.go(-1); </script>\n";
			}
			$query = "DELETE FROM #__survey_force_user_ans_txt"
				. "\n WHERE start_id IN ( $cids )";
			$database->setQuery($query);
			if (!$database->execute())
			{
				echo "<script> alert('" . $database->getErrorMsg() . "'); window.history.go(-1); </script>\n";
			}
			$query = "DELETE FROM #__survey_force_user_answers_imp"
				. "\n WHERE start_id IN ( $cids )";
			$database->setQuery($query);
			if (!$database->execute())
			{
				echo "<script> alert('" . $database->getErrorMsg() . "'); window.history.go(-1); </script>\n";
			}
		}
		mosRedirect(SFRoute("index.php?option=$option&task=reports"));
	}

	public static function SF_removeRepAll($option)
	{
		$database = JFactory::getDbo();
		$limit = intval(JFactory::getApplication()->getUserStateFromRequest("viewlistlimit", 'limit', 20));
		$limitstart = intval(JFactory::getApplication()->getUserStateFromRequest("viewlimitstart", 'limitstart', 0));
		$surv_id = intval(JFactory::getApplication()->getUserStateFromRequest("surv_id", 'surv_id', 0));
		$filt_status = intval(JFactory::getApplication()->getUserStateFromRequest("filt_status", 'filt_status', 2));
		$filt_utype = intval(JFactory::getApplication()->getUserStateFromRequest("filt_utype", 'filt_utype', 0));
		$filt_ulist = intval(JFactory::getApplication()->getUserStateFromRequest("filt_ulist", 'filt_ulist', 0));
		if ($limit == 0) $limit = 999999;
		$limit = intval(mosGetParam($_REQUEST, 'limit', JFactory::getSession()->get('list_limit', JFactory::getApplication()->getCfg('list_limit'))));
		if ($limit == 0) $limit = 999999;
		JFactory::getSession()->set('list_limit', $limit);
		$limitstart = intval(mosGetParam($_REQUEST, 'limitstart', JFactory::getSession()->get('list_limitstart', 0)));
		JFactory::getSession()->set('list_limitstart', $limitstart);
		$surv_id = intval(mosGetParam($_REQUEST, 'surv_id', JFactory::getSession()->get('list_surv_id', 0)));
		JFactory::getSession()->set('list_surv_id', $surv_id);

		$filt_status = intval(mosGetParam($_REQUEST, 'filt_status', JFactory::getSession()->get('list_filt_status', 2)));
		JFactory::getSession()->set('list_filt_status', $filt_status);
		$filt_utype = intval(mosGetParam($_REQUEST, 'filt_utype', JFactory::getSession()->get('list_filt_utype', 0)));
		JFactory::getSession()->set('list_filt_utype', $filt_utype);
		$filt_ulist = intval(mosGetParam($_REQUEST, 'filt_ulist', JFactory::getSession()->get('list_filt_ulist', 0)));
		JFactory::getSession()->set('list_filt_ulist', $filt_ulist);

		$filter_ans = array();
		$i = 0;
		$j = 0;
		if ($surv_id)
		{
			if (isset($_REQUEST['filter_quest']))
			{
				$k = 0;
				foreach ($_REQUEST['filter_quest'] as $filt_row)
				{
					if ($filt_row)
					{
						$sel_ans = 0;
						if (isset($_REQUEST['filter_ans'][$k]) && $_REQUEST['filter_ans'][$k])
						{
							$sel_ans = $_REQUEST['filter_ans'][$k];
						}
						$filter_ans[$i] = $sel_ans;
						$i++;
						$k++;
					}
				}
			}
		}

		if (($filt_utype - 1) != 2)
		{
			$filt_ulist = 0;
		}

		// get the subset (based on limits) of required records
		$query = "SELECT sf_ust.id "
			. "\n FROM (#__survey_force_user_starts as sf_ust, #__survey_force_survs as sf_s";
		$r = 0;
		foreach ($filter_ans as $filt_ans)
		{
			$query .= ($filt_ans ? ", #__survey_force_user_answers as sf_ans" . $r : '');
			$r++;
		}
		$query .= ")"
			. "\n LEFT JOIN #__users as u ON u.id = sf_ust.user_id and sf_ust.usertype=1"
			. "\n LEFT JOIN #__survey_force_users as sf_u ON sf_u.id = sf_ust.user_id and sf_ust.usertype=2"
			. "\n WHERE sf_ust.survey_id = sf_s.id"
			. ($surv_id ? "\n and sf_s.id = $surv_id" : '')
			. (" AND sf_s.sf_author = " . JFactory::getUser()->id . " ")
			. ($filt_status ? "\n and sf_ust.is_complete = '" . ($filt_status - 1) . "'" : '')
			. ($filt_utype ? "\n and sf_ust.usertype = '" . ($filt_utype - 1) . "'" : '')
			. ($filt_ulist ? "\n and sf_u.list_id = '" . ($filt_ulist) . "'" : '');
		$r = 0;
		foreach ($filter_ans as $filt_ans)
		{
			$query .= ($filt_ans ? "\n and sf_ans" . $r . ".start_id = sf_ust.id and sf_ans" . $r . ".answer = '" . ($filt_ans) . "'" : '');
			$r++;

		}
		$query .= "\n ORDER BY sf_ust.sf_time DESC ";
		$database->setQuery($query);
		$cid = $database->loadColumn();

		if (count($cid))
		{
			$cids = implode(',', $cid);
			$query = "DELETE FROM #__survey_force_user_starts"
				. "\n WHERE id IN ( $cids )";
			$database->setQuery($query);
			if (!$database->execute())
			{
				echo "<script> alert('" . $database->getErrorMsg() . "'); window.history.go(-1); </script>\n";
			}

			$query = "DELETE FROM #__survey_force_user_chain "
				. "\n WHERE start_id IN ( $cids )";
			$database->setQuery($query);
			if (!$database->execute())
			{
				echo "<script> alert('" . $database->getErrorMsg() . "'); window.history.go(-1); </script>\n";
			}

			$query = "DELETE FROM #__survey_force_user_answers"
				. "\n WHERE start_id IN ( $cids )";
			$database->setQuery($query);
			if (!$database->execute())
			{
				echo "<script> alert('" . $database->getErrorMsg() . "'); window.history.go(-1); </script>\n";
			}
			$query = "DELETE FROM #__survey_force_user_ans_txt"
				. "\n WHERE start_id IN ( $cids )";
			$database->setQuery($query);
			if (!$database->execute())
			{
				echo "<script> alert('" . $database->getErrorMsg() . "'); window.history.go(-1); </script>\n";
			}
			$query = "DELETE FROM #__survey_force_user_answers_imp"
				. "\n WHERE start_id IN ( $cids )";
			$database->setQuery($query);
			if (!$database->execute())
			{
				echo "<script> alert('" . $database->getErrorMsg() . "'); window.history.go(-1); </script>\n";
			}
		}
		mosRedirect(SFRoute("index.php?option=$option&task=reports"));
	}

	public static function SF_ViewRepResult($id, $option, $is_pdf = 0)
	{
		$database = JFactory::getDbo();
		$query = "SELECT s.*, u.username reg_username, u.name reg_name, u.email reg_email,"
			. "\n sf_u.name as inv_name, sf_u.lastname as inv_lastname, sf_u.email as inv_email"
			. "\n FROM #__survey_force_user_starts as s"
			. "\n LEFT JOIN #__users as u ON u.id = s.user_id and s.usertype=1"
			. "\n LEFT JOIN #__survey_force_users as sf_u ON sf_u.id = s.user_id and s.usertype=2"
			. "\n WHERE s.id = '" . $id . "'";
		$database->setQuery($query);
		$start_data = $database->loadObjectList();
		if (!count($start_data))
		{
			echo "<script> alert('" . JText::_('COM_SF_NO_RESULTS_FOUND') . "'); window.history.go(-1);</script>\n";
			exit;
		}


		$query = "SELECT * FROM #__survey_force_survs WHERE id = '" . $start_data[0]->survey_id . "' "
			. (" AND sf_author = '" . JFactory::getUser()->id . "' ");
		$database->setQuery($query);
		$survey_data = $database->loadObjectList();

		$query = "SELECT q.*"
			. "\n FROM #__survey_force_quests as q"
			. "\n WHERE q.published = 1 AND q.sf_survey = '" . $start_data[0]->survey_id . "' AND sf_qtype NOT IN (7, 8) "
			. "\n ORDER BY q.ordering, q.id ";
		$database->setQuery($query);
		$questions_data = $database->loadObjectList();

		$i = 0;
		$questions_data[$i]->answer = '';
		if (is_array($questions_data) && count($questions_data) > 0)
			while ($i < count($questions_data))
			{
				$questions_data[$i]->sf_qtext = trim(strip_tags(@$questions_data[$i]->sf_qtext, '<a><b><i><u>'));
				if (@$questions_data[$i]->sf_impscale)
				{
					$query = "SELECT iscale_name FROM #__survey_force_iscales WHERE id = '" . $questions_data[$i]->sf_impscale . "'";
					$database->setQuery($query);
					$questions_data[$i]->iscale_name = $database->loadResult();

					$query = "SELECT iscalefield_id FROM #__survey_force_user_answers_imp"
						. "\n WHERE quest_id = '" . $questions_data[$i]->id . "' and survey_id = '" . $questions_data[$i]->sf_survey . "'"
						. "\n AND iscale_id = '" . $questions_data[$i]->sf_impscale . "'"
						. "\n and start_id = '" . $id . "'";
					$database->setQuery($query);
					$ans_inf = $database->loadResult();

					$questions_data[$i]->answer_imp = array();
					$query = "SELECT * FROM #__survey_force_iscales_fields WHERE iscale_id = '" . $questions_data[$i]->sf_impscale . "'"
						. "\n ORDER BY ordering";
					$database->setQuery($query);
					$tmp_data = $database->loadObjectList();
					$j = 0;
					while ($j < count($tmp_data))
					{
						$questions_data[$i]->answer_imp[$j]->num = $j;
						$questions_data[$i]->answer_imp[$j]->f_id = $tmp_data[$j]->id;
						$questions_data[$i]->answer_imp[$j]->f_text = $tmp_data[$j]->isf_name;
						$questions_data[$i]->answer_imp[$j]->alt_text = '';
						if ($ans_inf == $tmp_data[$j]->id)
						{
							$questions_data[$i]->answer_imp[$j]->alt_text = '1';
							$questions_data[$i]->answer_imp[$j]->alt_id = $ans_inf;
						}
						$j++;
					}
				}

				switch (@$questions_data[$i]->sf_qtype)
				{
					case 1:
						$questions_data[$i]->answer = array();

						$questions_data[$i]->scale = '';
						$query = "SELECT stext FROM #__survey_force_scales WHERE quest_id = '" . $questions_data[$i]->id . "'"
							. "\n and quest_id = '" . $questions_data[$i]->id . "'"
							. "\n ORDER BY ordering";
						$database->setQuery($query);
						$tmp_data = $database->loadColumn();
						$questions_data[$i]->scale = implode(', ', $tmp_data);

						$query = "SELECT * FROM #__survey_force_fields WHERE quest_id = '" . $questions_data[$i]->id . "'"
							. "\n and is_main = 1 ORDER BY ordering";
						$database->setQuery($query);
						$tmp_data = $database->loadObjectList();

						$query = "SELECT * FROM #__survey_force_user_answers WHERE quest_id = '" . $questions_data[$i]->id . "' and survey_id = '" . $questions_data[$i]->sf_survey . "' and start_id = '" . $id . "'";
						$database->setQuery($query);
						$ans_inf_data = $database->loadObjectList();

						$j = 0;
						while ($j < count($tmp_data))
						{
							$questions_data[$i]->answer[$j] = new stdClass;
							$questions_data[$i]->answer[$j]->num = $j;
							$questions_data[$i]->answer[$j]->f_id = $tmp_data[$j]->id;
							$questions_data[$i]->answer[$j]->f_text = $tmp_data[$j]->ftext;
							$questions_data[$i]->answer[$j]->alt_text = JText::_('COM_SURVEYFORCE_NO_ANSWER');
							foreach ($ans_inf_data as $ans_data)
							{
								if ($ans_data->answer == $tmp_data[$j]->id)
								{
									$query = "SELECT * FROM #__survey_force_scales WHERE id = '" . $ans_data->ans_field . "'"
										. "\n and quest_id = '" . $questions_data[$i]->id . "'"
										. "\n ORDER BY ordering";
									$database->setQuery($query);
									$alt_data = $database->loadObjectList();
									$questions_data[$i]->answer[$j]->alt_text = ($ans_data->ans_field == 0 ? JText::_('COM_SURVEYFORCE_NO_ANSWER') : $alt_data[0]->stext);
									$questions_data[$i]->answer[$j]->alt_id = $ans_data->ans_field;
								}
							}
							$j++;
						}
						break;
					case 2:
						$query = "SELECT a.answer, b.ans_txt FROM ( #__survey_force_user_answers AS a, #__survey_force_quests AS c ) LEFT JOIN #__survey_force_user_ans_txt AS b ON ( a.ans_field = b.id AND c.sf_qtype = 2 ) WHERE c.published = 1 AND a.quest_id = '" . $questions_data[$i]->id . "' AND a.survey_id = '" . $questions_data[$i]->sf_survey . "' AND a.start_id = '" . $id . "' AND c.id = a.quest_id ";
						$database->setQuery($query);
						$ans_inf = $database->loadObjectList();

						$questions_data[$i]->answer = array();
						$query = "SELECT * FROM #__survey_force_fields WHERE quest_id = '" . $questions_data[$i]->id . "'"
							. "\n ORDER BY ordering";
						$database->setQuery($query);
						$tmp_data = $database->loadObjectList();
						$j = 0;
						while ($j < count($tmp_data))
						{
							$questions_data[$i]->answer[$j] = new stdClass;
							$questions_data[$i]->answer[$j]->num = $j;
							$questions_data[$i]->answer[$j]->f_id = $tmp_data[$j]->id;
							$questions_data[$i]->answer[$j]->f_text = $tmp_data[$j]->ftext;
							$questions_data[$i]->answer[$j]->alt_text = '';
							if (count($ans_inf) > 0 && $ans_inf[0]->answer == $tmp_data[$j]->id)
							{
								$questions_data[$i]->answer[$j]->f_text = $tmp_data[$j]->ftext . ($ans_inf[0]->ans_txt != '' ? ' (' . $ans_inf[0]->ans_txt . ')' : '');
								$questions_data[$i]->answer[$j]->alt_text = '1';
								$questions_data[$i]->answer[$j]->alt_id = $ans_inf;
							}
							$j++;
						}
						break;
					case 3:
						$query = "SELECT a.answer, b.ans_txt FROM ( #__survey_force_user_answers AS a, #__survey_force_quests AS c ) LEFT JOIN #__survey_force_user_ans_txt AS b ON ( a.ans_field = b.id AND c.sf_qtype = 3 )	WHERE c.published = 1 AND a.quest_id = '" . $questions_data[$i]->id . "' AND a.survey_id = '" . $questions_data[$i]->sf_survey . "' AND a.start_id = '" . $id . "' AND c.id = a.quest_id ";
						$database->setQuery($query);
						$ans_inf_data = $database->loadObjectList();

						$questions_data[$i]->answer = array();
						$query = "SELECT * FROM #__survey_force_fields WHERE quest_id = '" . $questions_data[$i]->id . "'"
							. "\n ORDER BY ordering";
						$database->setQuery($query);
						$tmp_data = $database->loadObjectList();
						$j = 0;
						while ($j < count($tmp_data))
						{
							$questions_data[$i]->answer[$j] = new stdClass;
							$questions_data[$i]->answer[$j]->num = $j;
							$questions_data[$i]->answer[$j]->f_id = $tmp_data[$j]->id;
							$questions_data[$i]->answer[$j]->f_text = $tmp_data[$j]->ftext;
							$questions_data[$i]->answer[$j]->alt_text = '';
							foreach ($ans_inf_data as $ans_data)
							{
								if ($ans_data->answer == $tmp_data[$j]->id)
								{
									$questions_data[$i]->answer[$j]->f_text = $tmp_data[$j]->ftext . ($ans_data->ans_txt != '' ? ' (' . $ans_data->ans_txt . ')' : '');
									$questions_data[$i]->answer[$j]->alt_text = '1';
									$questions_data[$i]->answer[$j]->alt_id = $ans_data->answer;
								}
							}
							$j++;
						}
						break;
					case 4:
						$n = mb_substr_count($questions_data[$i]->sf_qtext, "{x}") + mb_substr_count($questions_data[$i]->sf_qtext, "{y}");
						if ($n > 0)
						{
							$query = "SELECT b.ans_txt, a.ans_field FROM #__survey_force_user_answers as a LEFT JOIN #__survey_force_user_ans_txt as b ON a.answer = b.id	WHERE a.quest_id = '" . $questions_data[$i]->id . "' AND a.survey_id = '" . $questions_data[$i]->sf_survey . "' AND a.start_id = '" . $id . "' ORDER BY a.ans_field ";
							$database->setQuery($query);
							$ans_inf_data = $database->loadObjectList();
							$questions_data[$i]->answer = $ans_inf_data;
							$questions_data[$i]->answer_count = $n;
						}
						else
						{
							$query = "SELECT b.ans_txt FROM #__survey_force_user_answers as a, #__survey_force_user_ans_txt as b WHERE a.quest_id = '" . $questions_data[$i]->id . "' and a.survey_id = '" . $questions_data[$i]->sf_survey . "' and a.start_id = '" . $id . "' and a.answer = b.id";
							$database->setQuery($query);
							$ans_inf_data = $database->loadResult();
							$questions_data[$i]->answer = ($ans_inf_data == '') ? JText::_('COM_SURVEYFORCE_NO_ANSWER') : $ans_inf_data;
						}
						break;
					case 5:
					case 6:
					case 9:
						$query = "SELECT a.* , b.ans_txt FROM ( #__survey_force_user_answers AS a, #__survey_force_quests AS c )
LEFT JOIN #__survey_force_user_ans_txt AS b ON ( a.next_quest_id = b.id AND c.sf_qtype = 9 ) WHERE c.published = 1 AND a.quest_id = '" . $questions_data[$i]->id . "' AND a.survey_id = '" . $questions_data[$i]->sf_survey . "' AND a.start_id = '" . $id . "' AND c.id = a.quest_id";
						$database->setQuery($query);
						$ans_inf_data = $database->loadObjectList();

						$questions_data[$i]->answer = array();
						$query = "SELECT * FROM #__survey_force_fields WHERE quest_id = '" . $questions_data[$i]->id . "'"
							. "\n and is_main = 1 ORDER BY ordering";
						$database->setQuery($query);
						$tmp_data = $database->loadObjectList();
						$j = 0;
						while ($j < count($tmp_data))
						{
							$questions_data[$i]->answer[$j] = new stdClass;
							$questions_data[$i]->answer[$j]->num = $j;
							$questions_data[$i]->answer[$j]->f_id = $tmp_data[$j]->id;
							$questions_data[$i]->answer[$j]->f_text = $tmp_data[$j]->ftext;
							$questions_data[$i]->answer[$j]->alt_text = ($questions_data[$i]->sf_qtype == 9 ? '' : JText::_('COM_SURVEYFORCE_NO_ANSWER'));
							foreach ($ans_inf_data as $ans_data)
							{
								if ($ans_data->answer == $tmp_data[$j]->id)
								{
									$questions_data[$i]->answer[$j]->f_text = $tmp_data[$j]->ftext . ($ans_data->ans_txt != '' ? ' (' . $ans_data->ans_txt . ')' : '');
									$query = "SELECT * FROM #__survey_force_fields WHERE id = '" . $ans_data->ans_field . "'"
										. "\n and quest_id = '" . $questions_data[$i]->id . "'"
										. "\n and is_main = 0 ORDER BY ordering";
									$database->setQuery($query);
									$alt_data = $database->loadObjectList();
									if (count($alt_data) > 0)
									{
										$questions_data[$i]->answer[$j]->alt_text = ($ans_data->ans_field == 0 ? ($questions_data[$i]->sf_qtype == 9 ? '' : JText::_('COM_SURVEYFORCE_NO_ANSWER')) : $alt_data[0]->ftext);
										$questions_data[$i]->answer[$j]->alt_id = $ans_data->ans_field;
									}
								}
							}
							$j++;
						}
						break;
					case 7:
					case 8:
						break;
					default:
						if (!$questions_data[$i]->answer) $questions_data[$i]->answer = JText::_('COM_SURVEYFORCE_NO_ANSWER');
						break;
				}
				$i++;
			}

		if ($is_pdf)
		{
			self::SF_PrintRepResult($start_data, $survey_data, $questions_data);
		}
		else
		{
			survey_force_front_html::SF_ViewRepResult($option, $start_data, $survey_data, $questions_data);
		}
	}

	public static function SF_ViewRepSurv($id, $option, $is_pdf = 0)
	{	
		$database = JFactory::getDbo();
		$query = "SELECT * FROM `#__survey_force_survs` WHERE id = '" . $id . "'";
		$database->setQuery($query);
		$survey_data = $database->loadObjectList();
		if (!count($survey_data))
		{
			echo "<script> alert('" . JText::_('COM_SF_NO_RESULTS_FOUND') . "'); window.history.go(-1);</script>\n";
			exit;
		}
		$survey_data = $survey_data[0];

		$query = "SELECT `id` FROM `#__survey_force_user_starts` WHERE `survey_id` = '{$id}'";
		$database->setQuery($query);
		$start_ids = $database->loadColumn();

		$query = "SELECT count(*) FROM #__survey_force_user_starts WHERE survey_id = '" . $id . "'";
		$database->setQuery($query);
		$survey_data->total_starts = $database->loadResult();

		$query = "SELECT count(*) FROM #__survey_force_user_starts WHERE survey_id = '" . $id . "' and usertype = 0";
		$database->setQuery($query);
		$survey_data->total_gstarts = $database->loadResult();
		$query = "SELECT count(*) FROM #__survey_force_user_starts WHERE survey_id = '" . $id . "' and usertype = 1";
		$database->setQuery($query);
		$survey_data->total_rstarts = $database->loadResult();
		$query = "SELECT count(*) FROM #__survey_force_user_starts WHERE survey_id = '" . $id . "' and usertype = 2";
		$database->setQuery($query);
		$survey_data->total_istarts = $database->loadResult();

		$query = "SELECT count(*) FROM #__survey_force_user_starts WHERE survey_id = '" . $id . "' and is_complete = 1";
		$database->setQuery($query);
		$survey_data->total_completes = $database->loadResult();
		$query = "SELECT count(*) FROM #__survey_force_user_starts WHERE survey_id = '" . $id . "' and is_complete = 1 and usertype = 0";
		$database->setQuery($query);
		$survey_data->total_gcompletes = $database->loadResult();
		$query = "SELECT count(*) FROM #__survey_force_user_starts WHERE survey_id = '" . $id . "' and is_complete = 1 and usertype = 1";
		$database->setQuery($query);
		$survey_data->total_rcompletes = $database->loadResult();
		$query = "SELECT count(*) FROM #__survey_force_user_starts WHERE survey_id = '" . $id . "' and is_complete = 1 and usertype = 2";
		$database->setQuery($query);
		$survey_data->total_icompletes = $database->loadResult();

		$query = "SELECT q.*"
			. "\n FROM #__survey_force_quests as q"
			. "\n WHERE q.published = 1 AND q.sf_survey = '" . $id . "'"
			. "\n ORDER BY q.ordering, q.id ";
		$database->setQuery($query);
		$questions_data = $database->loadObjectList();
		$i = 0;
		while ($i < count($questions_data))
		{
			if ($questions_data[$i]->sf_impscale)
			{
				$query = "SELECT iscale_name FROM #__survey_force_iscales WHERE id = '" . $questions_data[$i]->sf_impscale . "'";
				$database->setQuery($query);
				$questions_data[$i]->iscale_name = $database->loadResult();

				$query = "SELECT count(id) FROM #__survey_force_user_answers_imp"
					. "\n WHERE quest_id = '" . $questions_data[$i]->id . "' and survey_id = '" . $questions_data[$i]->sf_survey . "'"
					. "\n AND iscale_id = '" . $questions_data[$i]->sf_impscale . "' AND `start_id` IN ('" . implode("','", $start_ids) . "')";
				$database->setQuery($query);
				$questions_data[$i]->total_iscale_answers = $database->loadResult();

				$query = "SELECT b.isf_name, count(a.id) as ans_count FROM #__survey_force_iscales_fields as b LEFT JOIN #__survey_force_user_answers_imp as a ON ( a.quest_id = '" . $questions_data[$i]->id . "' and a.iscalefield_id = b.id AND `a`.`start_id` IN ('" . implode("','", $start_ids) . "'))"
					. "\n WHERE b.iscale_id = '" . $questions_data[$i]->sf_impscale . "'"
					. "\n GROUP BY b.isf_name ORDER BY  b.ordering";//ans_count DESC,
				$database->setQuery($query);
				$ans_data = $database->loadObjectList();

				$questions_data[$i]->answer_imp = array();
				$j = 0;
				while ($j < count($ans_data))
				{
					$questions_data[$i]->answer_imp[$j]->num = $j;
					$questions_data[$i]->answer_imp[$j]->ftext = $ans_data[$j]->isf_name;
					$questions_data[$i]->answer_imp[$j]->ans_count = $ans_data[$j]->ans_count;
					$j++;
				}
			}
			$questions_data[$i]->sf_qtext = trim(strip_tags($questions_data[$i]->sf_qtext, '<a><b><i><u>'));
			switch ($questions_data[$i]->sf_qtype)
			{
				case 2:
					$query = "SELECT count(id) FROM #__survey_force_user_answers"
						. "\n WHERE quest_id = '" . $questions_data[$i]->id . "' and survey_id = '" . $questions_data[$i]->sf_survey . "' AND `start_id` IN ('" . implode("','", $start_ids) . "') ";
					$database->setQuery($query);
					$questions_data[$i]->total_answers = $database->loadResult();

					$query = "SELECT b.ftext, count(a.answer) as ans_count FROM #__survey_force_fields as b LEFT JOIN #__survey_force_user_answers as a ON (a.answer = b.id AND a.quest_id = '" . $questions_data[$i]->id . "' AND `a`.`start_id` IN ('" . implode("','", $start_ids) . "')) "
						. "\n WHERE b.quest_id = '" . $questions_data[$i]->id . "'"
						. "\n GROUP BY b.ftext ORDER BY b.ordering";//ans_count DESC
					$database->setQuery($query);

					$ans_data = $database->loadObjectList();
					$questions_data[$i]->answer = array();
					$j = 0;
					while ($j < count($ans_data))
					{
						$questions_data[$i]->answer[$j]->num = $j;
						$questions_data[$i]->answer[$j]->ftext = $ans_data[$j]->ftext;
						$questions_data[$i]->answer[$j]->ans_count = $ans_data[$j]->ans_count;
						$j++;
					}
					break;
				case 3:
					$query = "SELECT count(distinct start_id) FROM #__survey_force_user_answers"
						. "\n WHERE quest_id = '" . $questions_data[$i]->id . "' and survey_id = '" . $questions_data[$i]->sf_survey . "' AND `start_id` IN ('" . implode("','", $start_ids) . "') ";
					$database->setQuery($query);
					$questions_data[$i]->total_answers = $database->loadResult();

					$query = "SELECT b.ftext, count(a.answer) as ans_count FROM #__survey_force_fields as b LEFT JOIN #__survey_force_user_answers as a ON ( a.answer = b.id AND a.quest_id = '" . $questions_data[$i]->id . "' AND `a`.`start_id` IN ('" . implode("','", $start_ids) . "'))"
						. "\n WHERE b.quest_id = '" . $questions_data[$i]->id . "'"
						. "\n GROUP BY b.ftext ORDER BY b.ordering";//ans_count DESC
					$database->setQuery($query);
					$ans_data = $database->loadObjectList();
					$questions_data[$i]->answer = array();
					$j = 0;
					while ($j < count($ans_data))
					{
						$questions_data[$i]->answer[$j]->num = $j;
						$questions_data[$i]->answer[$j]->ftext = $ans_data[$j]->ftext;
						$questions_data[$i]->answer[$j]->ans_count = $ans_data[$j]->ans_count;
						$j++;
					}
					break;
				case 4:
					$n = mb_substr_count($questions_data[$i]->sf_qtext, '{x}') + mb_substr_count($questions_data[$i]->sf_qtext, '{y}');
					if ($n > 0)
					{
						$query = "SELECT id FROM #__survey_force_user_answers"
							. "\n WHERE quest_id = '" . $questions_data[$i]->id . "' and survey_id = '" . $questions_data[$i]->sf_survey . "' AND `start_id` IN ('" . implode("','", $start_ids) . "') GROUP BY start_id, quest_id ";
						$database->setQuery($query);
						$questions_data[$i]->total_answers = count($database->loadColumn());

						$questions_data[$i]->answer = array();
						$questions_data[$i]->answers_top100 = array();
						$questions_data[$i]->answer_count = $n;
						for ($j = 0; $j < $n; $j++)
						{
							$query = "SELECT answer FROM #__survey_force_user_answers WHERE ans_field = " . ($j + 1)
								. " AND quest_id = '" . $questions_data[$i]->id . "'"
								. " AND survey_id = '" . $questions_data[$i]->sf_survey . "' AND `start_id` IN ('" . implode("','", $start_ids) . "') ";
							$database->setQuery($query);
							$ans_txt_data = @array_merge(array(0 => 0), $database->loadColumn());

							$query = "SELECT b.ans_txt, count(a.answer) as ans_count FROM #__survey_force_user_ans_txt as b,"
								. "\n #__survey_force_user_answers as a"
								. "\n WHERE a.quest_id = '" . $questions_data[$i]->id . "' AND `a`.`start_id` IN ('" . implode("','", $start_ids) . "')"
								. "\n AND a.answer = b.id "
								. "\n AND a.answer IN (" . implode(',', $ans_txt_data) . ") "
								. "\n GROUP BY b.ans_txt ORDER BY ans_count DESC LIMIT 0,5";
							$database->setQuery($query);
							$ans_data = $database->loadObjectList();
							$jj = 0;
							$tmp = array();
							while ($jj < count($ans_data))
							{
								$tmp[$jj]->num = $jj;
								$tmp[$jj]->ftext = $ans_data[$jj]->ans_txt;
								$tmp[$jj]->ans_count = $ans_data[$jj]->ans_count;
								$jj++;
							}
							$questions_data[$i]->answer[$j] = $tmp;

							$query = "SELECT b.ans_txt FROM #__survey_force_user_ans_txt as b, #__survey_force_user_answers as a"
								. "\n WHERE a.quest_id = '" . $questions_data[$i]->id . "' AND `a`.`start_id` IN ('" . implode("','", $start_ids) . "') AND a.answer = b.id"
								. "\n AND a.answer IN (" . implode(',', $ans_txt_data) . ") "
								. "\n ORDER BY a.sf_time DESC LIMIT 0,100";
							$database->setQuery($query);
							$ans_data = $database->loadColumn();
							$questions_data[$i]->answers_top100[$j] = implode(', ', $ans_data);
						}
					}
					else
					{
						$query = "SELECT id FROM #__survey_force_user_answers"
							. "\n WHERE quest_id = '" . $questions_data[$i]->id . "' AND `start_id` IN ('" . implode("','", $start_ids) . "') and survey_id = '" . $questions_data[$i]->sf_survey . "' GROUP BY start_id, quest_id ";
						$database->setQuery($query);
						$questions_data[$i]->total_answers = count($database->loadColumn());

						$query = "SELECT b.ans_txt, count(a.answer) as ans_count FROM #__survey_force_user_ans_txt as b, #__survey_force_user_answers as a"
							. "\n WHERE a.quest_id = '" . $questions_data[$i]->id . "' AND `a`.`start_id` IN ('" . implode("','", $start_ids) . "') and a.answer = b.id"
							. "\n GROUP BY b.ans_txt ORDER BY ans_count DESC LIMIT 0,5";
						$database->setQuery($query);
						$ans_data = $database->loadObjectList();
						$questions_data[$i]->answer = array();
						$j = 0;
						while ($j < count($ans_data))
						{
							$questions_data[$i]->answer[$j]->num = $j;
							$questions_data[$i]->answer[$j]->ftext = $ans_data[$j]->ans_txt;
							$questions_data[$i]->answer[$j]->ans_count = $ans_data[$j]->ans_count;
							$j++;
						}
						$query = "SELECT b.ans_txt FROM #__survey_force_user_ans_txt as b, #__survey_force_user_answers as a"
							. "\n WHERE a.quest_id = '" . $questions_data[$i]->id . "' AND `a`.`start_id` IN ('" . implode("','", $start_ids) . "') and a.answer = b.id"
							. "\n ORDER BY a.sf_time DESC LIMIT 0,100";
						$database->setQuery($query);
						$ans_data = $database->loadColumn();
						$questions_data[$i]->answers_top100 = implode(', ', $ans_data);
					}
					break;
				case 1:
					$query = "SELECT count(distinct start_id) FROM #__survey_force_user_answers"
						. "\n WHERE quest_id = '" . $questions_data[$i]->id . "' AND `start_id` IN ('" . implode("','", $start_ids) . "') and survey_id = '" . $questions_data[$i]->sf_survey . "' ";
					$database->setQuery($query);
					$questions_data[$i]->total_answers = $database->loadResult();

					$query = "SELECT * FROM #__survey_force_fields WHERE quest_id = '" . $questions_data[$i]->id . "' ORDER by ordering";
					$database->setQuery($query);
					$f_data = $database->loadObjectList();
					$j = 0;
					$questions_data[$i]->answer = array();
					while ($j < count($f_data))
					{
						$query = "SELECT b.stext, count(a.answer) as ans_count FROM #__survey_force_scales as b LEFT JOIN #__survey_force_user_answers as a ON ( a.ans_field = b.id AND a.answer = '" . $f_data[$j]->id . "' AND a.quest_id = '" . $questions_data[$i]->id . "' AND `a`.`start_id` IN ('" . implode("','", $start_ids) . "'))"
							. "\n WHERE b.quest_id = '" . $questions_data[$i]->id . "' "
							. "\n GROUP BY b.stext ORDER BY b.ordering";
						$database->setQuery($query);
						$ans_data = $database->loadObjectList();
						$questions_data[$i]->answer[$j]->full_ans = array();
						$jj = 0;
						$questions_data[$i]->answer[$j]->ftext = $f_data[$j]->ftext;
						while ($jj < count($ans_data))
						{
							$questions_data[$i]->answer[$j]->full_ans[$jj]->ftext = $ans_data[$jj]->stext;
							$questions_data[$i]->answer[$j]->full_ans[$jj]->ans_count = $ans_data[$jj]->ans_count;
							$jj++;
						}
						$j++;
					}
					break;
				case 5:
				case 6:
				case 9:
					$query = "SELECT count(distinct start_id) FROM #__survey_force_user_answers"
						. "\n WHERE quest_id = '" . $questions_data[$i]->id . "' AND `start_id` IN ('" . implode("','", $start_ids) . "') and survey_id = '" . $questions_data[$i]->sf_survey . "' ";
					$database->setQuery($query);
					$questions_data[$i]->total_answers = $database->loadResult();

					$query = "SELECT * FROM #__survey_force_fields WHERE quest_id = '" . $questions_data[$i]->id . "' and is_main = '1' ORDER by ordering";
					$database->setQuery($query);
					$f_data = $database->loadObjectList();
					$j = 0;
					$questions_data[$i]->answer = array();
					while ($j < count($f_data))
					{
						$query = "SELECT b.ftext, count(a.answer) as ans_count FROM #__survey_force_fields as b LEFT JOIN #__survey_force_user_answers as a ON ( a.ans_field = b.id AND a.answer = '" . $f_data[$j]->id . "' AND a.quest_id = '" . $questions_data[$i]->id . "' AND `a`.`start_id` IN ('" . implode("','", $start_ids) . "'))"
							. "\n WHERE b.quest_id = '" . $questions_data[$i]->id . "' and b.is_main = '0'"
							. "\n GROUP BY b.ftext ORDER BY b.ordering ";//ans_count DESC
						$database->setQuery($query);
						$ans_data = $database->loadObjectList();
						$questions_data[$i]->answer[$j]->full_ans = array();
						$jj = 0;
						$questions_data[$i]->answer[$j]->ftext = $f_data[$j]->ftext;
						while ($jj < count($ans_data))
						{
							$questions_data[$i]->answer[$j]->full_ans[$jj]->ftext = $ans_data[$jj]->ftext;
							$questions_data[$i]->answer[$j]->full_ans[$jj]->ans_count = $ans_data[$jj]->ans_count;
							$jj++;
						}
						$j++;
					}
					break;
			}
			$i++;
		}

		if ($is_pdf)
		{
			SF_PrintRepSurv_List($survey_data, $questions_data);
		}
		else
		{
			survey_force_front_html::SF_ViewRepSurv_List($option, $survey_data, $questions_data, 0, 0);
		}
	}

	public static function SF_ViewRepList($id, $option, $is_pdf = 0)
	{
		$database = JFactory::getDbo();
		$query = "SELECT * FROM #__survey_force_listusers WHERE id = '" . $id . "'";
		$database->setQuery($query);
		$list_data = $database->loadObjectList();
		if (!count($list_data))
		{
			echo "<script> alert('" . JText::_('COM_SF_NO_RESULTS_FOUND') . "'); window.history.go(-1);</script>\n";
			exit;
		}
		$list_data = $list_data[0];

		$query = "SELECT * FROM #__survey_force_survs WHERE id = '" . $list_data->survey_id . "'";
		$database->setQuery($query);
		$survey_data = $database->loadObjectList();
		if (!count($survey_data))
		{
			echo "<script> alert('" . JText::_('COM_SF_NO_RESULTS_FOUND') . "'); window.history.go(-1);</script>\n";
			exit;
		}

		$survey_data = $survey_data[0];

		$query = "SELECT count(a.id) FROM #__survey_force_user_starts as a, #__survey_force_users as b"
			. "\n  WHERE a.survey_id = '" . $survey_data->id . "'"
			. "\n and a.usertype = 2 and a.user_id = b.id and b.list_id = '" . $list_data->id . "' and b.is_invited = 1 and a.is_complete = 1";
		$database->setQuery($query);
		$survey_data->total_completes = $database->loadResult();

		$query = "SELECT count(a.id) FROM #__survey_force_user_starts as a, #__survey_force_users as b"
			. "\n  WHERE a.survey_id = '" . $survey_data->id . "'"
			. "\n and a.usertype = 2 and a.user_id = b.id and b.list_id = '" . $list_data->id . "' and b.is_invited = 1";
		$database->setQuery($query);
		$survey_data->total_starts = $database->loadResult();

		$query = "SELECT count(b.id) FROM #__survey_force_users as b"
			. "\n  WHERE b.list_id = '" . $list_data->id . "' and b.is_invited = 1";
		$database->setQuery($query);
		$survey_data->total_inv_users = $database->loadResult();

		$query = "SELECT q.*"
			. "\n FROM #__survey_force_quests as q"
			. "\n WHERE q.published = 1 AND q.sf_survey = '" . $survey_data->id . "'"
			. "\n ORDER BY q.ordering, q.id ";
		$database->setQuery($query);
		$questions_data = $database->loadObjectList();
		$i = 0;
		$query = "SELECT b.id FROM #__survey_force_user_starts as b, #__survey_force_users as c"
			. "\n WHERE b.usertype = 2 and b.user_id = c.id and c.list_id = '" . $list_data->id . "'";
		$database->setQuery($query);
		$start_id_array = $database->loadColumn();
		$start_id_array[] = 0;
		$start_ids = @implode(',', $start_id_array);

		while ($i < count($questions_data))
		{
			if ($questions_data[$i]->sf_impscale)
			{
				$query = "SELECT iscale_name FROM #__survey_force_iscales WHERE id = '" . $questions_data[$i]->sf_impscale . "'";
				$database->setQuery($query);
				$questions_data[$i]->iscale_name = $database->loadResult();

				$query = "SELECT count(a.id) FROM #__survey_force_user_answers_imp as a"
					. "\n WHERE a.quest_id = '" . $questions_data[$i]->id . "' and a.survey_id = '" . $questions_data[$i]->sf_survey . "' and a.iscale_id = '" . $questions_data[$i]->sf_impscale . "'"
					. "\n and a.start_id IN (" . $start_ids . ")";
				$database->setQuery($query);
				$questions_data[$i]->total_iscale_answers = $database->loadResult();

				$query = "SELECT b.isf_name, count(a.iscalefield_id) as ans_count FROM #__survey_force_iscales_fields as b LEFT JOIN #__survey_force_user_answers_imp as a ON a.quest_id = '" . $questions_data[$i]->id . "' and a.survey_id = '" . $questions_data[$i]->sf_survey . "' and a.iscale_id = '" . $questions_data[$i]->sf_impscale . "' and a.start_id IN (" . $start_ids . ") and a.iscalefield_id = b.id"
					. "\n WHERE b.iscale_id = '" . $questions_data[$i]->sf_impscale . "'"
					. "\n GROUP BY b.isf_name ORDER BY  b.ordering";//ans_count DESC,
				$database->setQuery($query);
				$ans_data = $database->loadObjectList();
				$questions_data[$i]->answer_imp = array();
				$j = 0;
				while ($j < count($ans_data))
				{
					$questions_data[$i]->answer_imp[$j]->num = $j;
					$questions_data[$i]->answer_imp[$j]->ftext = $ans_data[$j]->isf_name;
					$questions_data[$i]->answer_imp[$j]->ans_count = $ans_data[$j]->ans_count;
					$j++;
				}
			}
			$questions_data[$i]->sf_qtext = trim(strip_tags($questions_data[$i]->sf_qtext, '<a><b><i><u>'));
			switch ($questions_data[$i]->sf_qtype)
			{
				case 2:
					$query = "SELECT count(a.id) FROM #__survey_force_user_answers as a"
						. "\n WHERE a.quest_id = '" . $questions_data[$i]->id . "' and a.survey_id = '" . $questions_data[$i]->sf_survey . "' "
						. "\n and a.start_id IN (" . $start_ids . ")";
					$database->setQuery($query);
					$questions_data[$i]->total_answers = $database->loadResult();

					$query = "SELECT b.ftext, count(a.answer) as ans_count FROM #__survey_force_fields as b LEFT JOIN #__survey_force_user_answers as a ON ( a.start_id IN (" . $start_ids . ") AND a.answer = b.id AND a.quest_id = '" . $questions_data[$i]->id . "' )"
						. "\n WHERE b.quest_id = '" . $questions_data[$i]->id . "'"
						. "\n GROUP BY b.ftext ORDER BY b.ordering"; //ans_count DESC
					$database->setQuery($query);
					$ans_data = $database->loadObjectList();
					$questions_data[$i]->answer = array();
					$j = 0;
					while ($j < count($ans_data))
					{
						$questions_data[$i]->answer[$j]->num = $j;
						$questions_data[$i]->answer[$j]->ftext = $ans_data[$j]->ftext;
						$questions_data[$i]->answer[$j]->ans_count = $ans_data[$j]->ans_count;
						$j++;
					}
					break;
				case 3:
					$query = "SELECT count(distinct start_id) FROM #__survey_force_user_answers"
						. "\n WHERE quest_id = '" . $questions_data[$i]->id . "' and survey_id = '" . $questions_data[$i]->sf_survey . "' and start_id IN (" . $start_ids . ")";
					$database->setQuery($query);
					$questions_data[$i]->total_answers = $database->loadResult();

					$query = "SELECT b.ftext, count(a.answer) as ans_count FROM #__survey_force_fields as b LEFT JOIN #__survey_force_user_answers as a ON ( a.answer = b.id AND a.start_id IN (" . $start_ids . ") AND a.quest_id = '" . $questions_data[$i]->id . "' )"
						. "\n WHERE b.quest_id = '" . $questions_data[$i]->id . "'"
						. "\n GROUP BY b.ftext ORDER BY b.ordering";//ans_count DESC
					$database->setQuery($query);
					$ans_data = $database->loadObjectList();
					$questions_data[$i]->answer = array();
					$j = 0;
					while ($j < count($ans_data))
					{
						$questions_data[$i]->answer[$j]->num = $j;
						$questions_data[$i]->answer[$j]->ftext = $ans_data[$j]->ftext;
						$questions_data[$i]->answer[$j]->ans_count = $ans_data[$j]->ans_count;
						$j++;
					}
					break;
				case 4:
					$n = mb_substr_count($questions_data[$i]->sf_qtext, '{x}') + mb_substr_count($questions_data[$i]->sf_qtext, '{y}');
					if ($n > 0)
					{
						$query = "SELECT id FROM #__survey_force_user_answers"
							. "\n WHERE quest_id = '" . $questions_data[$i]->id . "' AND survey_id = '" . $questions_data[$i]->sf_survey . "' AND start_id IN (" . $start_ids . ")  GROUP BY start_id, quest_id ";
						$database->setQuery($query);
						$questions_data[$i]->total_answers = count($database->loadColumn());

						$questions_data[$i]->answer = array();
						$questions_data[$i]->answers_top100 = array();
						$questions_data[$i]->answer_count = $n;
						for ($j = 0; $j < $n; $j++)
						{
							$query = "SELECT answer FROM #__survey_force_user_answers WHERE ans_field = " . ($j + 1)
								. " AND quest_id = '" . $questions_data[$i]->id . "' AND a.start_id IN (" . $start_ids . ") "
								. " AND survey_id = '" . $questions_data[$i]->sf_survey . "' ";
							$database->setQuery($query);
							$ans_txt_data = @array_merge(array(0 => 0), $database->loadColumn());

							$query = "SELECT b.ans_txt, count(a.answer) as ans_count FROM #__survey_force_user_ans_txt as b,"
								. "\n #__survey_force_user_answers as a"
								. "\n WHERE a.quest_id = '" . $questions_data[$i]->id . "'"
								. "\n AND a.answer = b.id AND a.start_id IN (" . $start_ids . ") "
								. "\n AND a.answer IN (" . implode(',', (array) $ans_txt_data) . ") "
								. "\n GROUP BY b.ans_txt ORDER BY ans_count DESC LIMIT 0,5";
							$database->setQuery($query);
							$ans_data = $database->loadObjectList();
							$jj = 0;
							$tmp = array();
							while ($jj < count($ans_data))
							{
								$tmp[$jj]->num = $jj;
								$tmp[$jj]->ftext = $ans_data[$jj]->ans_txt;
								$tmp[$jj]->ans_count = $ans_data[$jj]->ans_count;
								$jj++;
							}
							$questions_data[$i]->answer[$j] = $tmp;

							$query = "SELECT b.ans_txt FROM #__survey_force_user_ans_txt as b, #__survey_force_user_answers as a"
								. "\n WHERE a.quest_id = '" . $questions_data[$i]->id . "' AND a.answer = b.id"
								. "\n AND a.answer IN (" . implode(',', (array) $ans_txt_data) . ") AND a.start_id IN (" . $start_ids . ") "
								. "\n ORDER BY a.sf_time DESC LIMIT 0,100";
							$database->setQuery($query);
							$ans_data = $database->loadColumn();
							$ans_data = (is_array($ans_data) ? $ans_data : array());
							$questions_data[$i]->answers_top100[$j] = implode(', ', $ans_data);
						}
					}
					else
					{
						$query = "SELECT id FROM #__survey_force_user_answers"
							. "\n WHERE quest_id = '" . $questions_data[$i]->id . "' AND survey_id = '" . $questions_data[$i]->sf_survey . "' AND start_id IN (" . $start_ids . ")  GROUP BY start_id, quest_id ";
						$database->setQuery($query);
						$questions_data[$i]->total_answers = count($database->loadColumn());

						$query = "SELECT b.ans_txt, count(a.answer) as ans_count FROM #__survey_force_user_ans_txt as b, #__survey_force_user_answers as a"
							. "\n WHERE a.quest_id = '" . $questions_data[$i]->id . "' and a.survey_id = '" . $questions_data[$i]->sf_survey . "' and a.answer = b.id and a.start_id IN (" . $start_ids . ")"
							. "\n GROUP BY b.ans_txt ORDER BY ans_count DESC LIMIT 0,5";
						$database->setQuery($query);
						$ans_data = $database->loadObjectList();
						$questions_data[$i]->answer = array();
						$j = 0;
						while ($j < count($ans_data))
						{
							$questions_data[$i]->answer[$j]->num = $j;
							$questions_data[$i]->answer[$j]->ftext = $ans_data[$j]->ans_txt;
							$questions_data[$i]->answer[$j]->ans_count = $ans_data[$j]->ans_count;
							$j++;
						}
						$ans_data = array();
						$query = "SELECT b.ans_txt FROM #__survey_force_user_ans_txt as b, #__survey_force_user_answers as a"
							. "\n WHERE a.quest_id = '" . $questions_data[$i]->id . "' and a.survey_id = '" . $questions_data[$i]->sf_survey . "' and a.start_id IN (" . $start_ids . ") and a.answer = b.id "
							. "\n ORDER BY a.sf_time DESC LIMIT 0,100";
						$database->setQuery($query);
						$ans_data = $database->loadColumn();
						if (count($ans_data) > 0)
						{
							$questions_data[$i]->answers_top100 = implode(', ', $ans_data);
						}
						else
						{
							$questions_data[$i]->answers_top100 = '';
						}
					}
					break;
				case 1:
					$query = "SELECT count(distinct start_id) FROM #__survey_force_user_answers"
						. "\n WHERE quest_id = '" . $questions_data[$i]->id . "' and survey_id = '" . $questions_data[$i]->sf_survey . "' and start_id IN (" . $start_ids . ")";
					$database->setQuery($query);
					$questions_data[$i]->total_answers = $database->loadResult();

					$query = "SELECT * FROM #__survey_force_fields WHERE quest_id = '" . $questions_data[$i]->id . "' ORDER by ordering";
					$database->setQuery($query);
					$f_data = $database->loadObjectList();
					$j = 0;
					$questions_data[$i]->answer = array();
					while ($j < count($f_data))
					{
						$query = "SELECT b.stext, count(a.answer) as ans_count FROM #__survey_force_scales as b LEFT JOIN #__survey_force_user_answers as a ON ( a.ans_field = b.id AND a.answer = '" . $f_data[$j]->id . "' AND a.start_id IN (" . $start_ids . ") AND a.quest_id = '" . $questions_data[$i]->id . "' )"
							. "\n WHERE b.quest_id = '" . $questions_data[$i]->id . "'"
							. "\n GROUP BY b.stext ORDER BY b.ordering";
						$database->setQuery($query);
						$ans_data = $database->loadObjectList();
						$questions_data[$i]->answer[$j]->full_ans = array();
						$jj = 0;
						$questions_data[$i]->answer[$j]->ftext = $f_data[$j]->ftext;
						while ($jj < count($ans_data))
						{
							$questions_data[$i]->answer[$j]->full_ans[$jj]->ftext = $ans_data[$jj]->stext;
							$questions_data[$i]->answer[$j]->full_ans[$jj]->ans_count = $ans_data[$jj]->ans_count;
							$jj++;
						}
						$j++;
					}
					break;
				case 5:
				case 6:
				case 9:
					$query = $database->getQuery(true)
						->select('COUNT(DISTINCT `start_id`)')
						->from('`#__survey_force_user_answers`')
						->where('`quest_id` = "' . $questions_data[$i]->id . '" AND `survey_id` = "' . $questions_data[$i]->sf_survey . '" AND `start_id` IN (' . $start_ids . ')');
					$database->setQuery($query);
					$questions_data[$i]->total_answers = $database->loadResult();

					$query = "SELECT * FROM #__survey_force_fields WHERE quest_id = '" . $questions_data[$i]->id . "' and is_main = '1' ORDER by ordering";
					$database->setQuery($query);
					$f_data = $database->loadObjectList();
					$j = 0;
					$questions_data[$i]->answer = array();
					while ($j < count($f_data))
					{
						$query = "SELECT b.ftext, count(a.answer) as ans_count FROM #__survey_force_fields as b LEFT JOIN #__survey_force_user_answers as a ON ( a.ans_field = b.id AND a.answer = '" . $f_data[$j]->id . "' AND a.start_id IN (" . $start_ids . ") AND a.quest_id = '" . $questions_data[$i]->id . "' )"
							. "\n WHERE b.quest_id = '" . $questions_data[$i]->id . "' and b.is_main = '0'"
							. "\n GROUP BY b.ftext ORDER BY b.ordering";//ans_count DESC
						$database->setQuery($query);
						$ans_data = $database->loadObjectList();
						$questions_data[$i]->answer[$j]->full_ans = array();
						$jj = 0;
						$questions_data[$i]->answer[$j]->ftext = $f_data[$j]->ftext;
						while ($jj < count($ans_data))
						{
							$questions_data[$i]->answer[$j]->full_ans[$jj]->ftext = $ans_data[$jj]->ftext;
							$questions_data[$i]->answer[$j]->full_ans[$jj]->ans_count = $ans_data[$jj]->ans_count;
							$jj++;
						}
						$j++;
					}
					break;
			}
			$i++;
		}
		if ($is_pdf)
		{
			SF_PrintRepSurv_List($survey_data, $questions_data, 1);
		}
		else
		{
			survey_force_adm_html::SF_ViewRepSurv_List($option, $survey_data, $questions_data, 1, $list_data->id);
		}
	}

	public static function SF_ViewRepUsers($cid, $option, $is_pdf = 0, $is_pc = 0)
	{
		$database = JFactory::getDbo();
		$surv_id = intval(JFactory::getApplication()->getUserStateFromRequest("surv_id", 'surv_id', 0));
		$filt_status = intval(JFactory::getApplication()->getUserStateFromRequest("filt_status", 'filt_status', 2));
		$filt_utype = intval(JFactory::getApplication()->getUserStateFromRequest("filt_utype", 'filt_utype', 0));
		$filt_ulist = intval(JFactory::getApplication()->getUserStateFromRequest("filt_ulist", 'filt_ulist', 0));

		$query = "SELECT * FROM #__survey_force_survs WHERE id = '" . $surv_id . "'";
		$database->setQuery($query);
		$survey_data = $database->loadObjectList();
		if (!count($survey_data))
		{
			echo "<script> alert('" . JText::_('COM_SF_NO_RESULTS_FOUND') . "'); window.history.go(-1);</script>\n";
			exit;
		}

		$survey_data = $survey_data[0];

		$query = "SELECT count(*) FROM #__survey_force_user_starts WHERE survey_id = '" . $surv_id . "'";
		$database->setQuery($query);
		$survey_data->total_starts = $database->loadResult();
		$query = "SELECT count(*) FROM #__survey_force_user_starts WHERE survey_id = '" . $surv_id . "' and usertype = 0";
		$database->setQuery($query);
		$survey_data->total_gstarts = $database->loadResult();
		$query = "SELECT count(*) FROM #__survey_force_user_starts WHERE survey_id = '" . $surv_id . "' and usertype = 1";
		$database->setQuery($query);
		$survey_data->total_rstarts = $database->loadResult();
		$query = "SELECT count(*) FROM #__survey_force_user_starts WHERE survey_id = '" . $surv_id . "' and usertype = 2";
		$database->setQuery($query);
		$survey_data->total_istarts = $database->loadResult();

		$query = "SELECT count(*) FROM #__survey_force_user_starts WHERE survey_id = '" . $surv_id . "' and is_complete = 1";
		$database->setQuery($query);
		$survey_data->total_completes = $database->loadResult();
		$query = "SELECT count(*) FROM #__survey_force_user_starts WHERE survey_id = '" . $surv_id . "' and is_complete = 1 and usertype = 0";
		$database->setQuery($query);
		$survey_data->total_gcompletes = $database->loadResult();
		$query = "SELECT count(*) FROM #__survey_force_user_starts WHERE survey_id = '" . $surv_id . "' and is_complete = 1 and usertype = 1";
		$database->setQuery($query);
		$survey_data->total_rcompletes = $database->loadResult();
		$query = "SELECT count(*) FROM #__survey_force_user_starts WHERE survey_id = '" . $surv_id . "' and is_complete = 1 and usertype = 2";
		$database->setQuery($query);
		$survey_data->total_icompletes = $database->loadResult();

		$query = "SELECT q.*"
			. "\n FROM #__survey_force_quests as q"
			. "\n WHERE q.published = 1 AND q.sf_survey = '" . $survey_data->id . "'"
			. "\n ORDER BY q.ordering, q.id ";
		$database->setQuery($query);
		$questions_data = $database->loadObjectList();
		$i = 0;
		$query = "SELECT b.id FROM #__survey_force_user_starts as b "
			. "\n WHERE 1=1 "
			. ($filt_status ? "\n and b.is_complete = '" . ($filt_status - 1) . "'" : '')
			. ($filt_utype ? "\n and b.usertype = '" . ($filt_utype - 1) . "'" : '');
		if ((count($cid) > 0) && ($cid[0] != 0))
		{
			$cids = implode(',', $cid);
			$query .= "\n AND b.id in (" . $cids . ")";
		}
		$database->setQuery($query);

		$start_id_array = $database->loadColumn();
		$start_id_array[] = 0;
		$start_ids = @implode(',', $start_id_array);

		while ($i < count($questions_data))
		{
			if ($questions_data[$i]->sf_impscale)
			{
				$query = "SELECT iscale_name FROM #__survey_force_iscales WHERE id = '" . $questions_data[$i]->sf_impscale . "'";
				$database->setQuery($query);
				$questions_data[$i]->iscale_name = $database->loadResult();

				$query = "SELECT count(a.id) FROM #__survey_force_user_answers_imp as a"
					. "\n WHERE a.quest_id = '" . $questions_data[$i]->id . "' and a.survey_id = '" . $questions_data[$i]->sf_survey . "' and a.iscale_id = '" . $questions_data[$i]->sf_impscale . "'"
					. "\n and a.start_id IN (" . $start_ids . ")";
				$database->setQuery($query);
				$questions_data[$i]->total_iscale_answers = $database->loadResult();

				$query = "SELECT b.isf_name, count(a.iscalefield_id) as ans_count FROM #__survey_force_iscales_fields as b LEFT JOIN #__survey_force_user_answers_imp as a ON a.quest_id = '" . $questions_data[$i]->id . "' and a.survey_id = '" . $questions_data[$i]->sf_survey . "' and a.iscale_id = '" . $questions_data[$i]->sf_impscale . "' and a.start_id IN (" . $start_ids . ") and a.iscalefield_id = b.id"
					. "\n WHERE b.iscale_id = '" . $questions_data[$i]->sf_impscale . "'"
					. "\n GROUP BY b.isf_name ORDER BY  b.ordering";//ans_count DESC,
				$database->setQuery($query);
				$ans_data = $database->loadObjectList();
				$questions_data[$i]->answer_imp = array();
				$j = 0;
				while ($j < count($ans_data))
				{
					$questions_data[$i]->answer_imp[$j]->num = $j;
					$questions_data[$i]->answer_imp[$j]->ftext = $ans_data[$j]->isf_name;
					$questions_data[$i]->answer_imp[$j]->ans_count = $ans_data[$j]->ans_count;
					$j++;
				}
			}
			$questions_data[$i]->sf_qtext = trim(strip_tags($questions_data[$i]->sf_qtext, '<a><b><i><u>'));
			switch ($questions_data[$i]->sf_qtype)
			{
				case 2:
					$query = "SELECT count(a.id) FROM #__survey_force_user_answers as a"
						. "\n WHERE a.quest_id = '" . $questions_data[$i]->id . "' and a.survey_id = '" . $questions_data[$i]->sf_survey . "' "
						. "\n and a.start_id IN (" . $start_ids . ")";
					$database->setQuery($query);
					$questions_data[$i]->total_answers = $database->loadResult();

					$query = "SELECT b.ftext, count(a.answer) as ans_count FROM #__survey_force_fields as b LEFT JOIN #__survey_force_user_answers as a ON ( a.start_id IN (" . $start_ids . ") AND a.answer = b.id AND a.quest_id = '" . $questions_data[$i]->id . "') "
						. "\n WHERE b.quest_id = '" . $questions_data[$i]->id . "'"
						. "\n GROUP BY b.ftext ORDER BY b.ordering"; //ans_count DESC
					$database->setQuery($query);
					$ans_data = $database->loadObjectList();
					$questions_data[$i]->answer = array();
					$j = 0;
					while ($j < count($ans_data))
					{
						$questions_data[$i]->answer[$j]->num = $j;
						$questions_data[$i]->answer[$j]->ftext = $ans_data[$j]->ftext;
						$questions_data[$i]->answer[$j]->ans_count = ($is_pc ? intval($ans_data[$j]->ans_count / $questions_data[$i]->total_answers * 100) : $ans_data[$j]->ans_count);
						$j++;
					}
					break;
				case 3:
					$query = "SELECT count(distinct start_id) FROM #__survey_force_user_answers"
						. "\n WHERE quest_id = '" . $questions_data[$i]->id . "' and survey_id = '" . $questions_data[$i]->sf_survey . "' and start_id IN (" . $start_ids . ")";
					$database->setQuery($query);
					$questions_data[$i]->total_answers = $database->loadResult();

					$query = "SELECT b.ftext, count(a.answer) as ans_count FROM #__survey_force_fields as b LEFT JOIN #__survey_force_user_answers as a ON ( a.answer = b.id AND a.start_id IN (" . $start_ids . ") AND a.quest_id = '" . $questions_data[$i]->id . "' )"
						. "\n WHERE b.quest_id = '" . $questions_data[$i]->id . "'"
						. "\n GROUP BY b.ftext ORDER BY b.ordering";//ans_count DESC
					$database->setQuery($query);
					$ans_data = $database->loadObjectList();
					$questions_data[$i]->answer = array();
					$j = 0;
					while ($j < count($ans_data))
					{
						$questions_data[$i]->answer[$j]->num = $j;
						$questions_data[$i]->answer[$j]->ftext = $ans_data[$j]->ftext;
						$questions_data[$i]->answer[$j]->ans_count = ($is_pc ? intval($ans_data[$j]->ans_count / $questions_data[$i]->total_answers * 100) : $ans_data[$j]->ans_count);
						$j++;
					}
					break;
				case 4:
					$n = mb_substr_count($questions_data[$i]->sf_qtext, '{x}') + mb_substr_count($questions_data[$i]->sf_qtext, '{y}');
					if ($n > 0)
					{
						$query = "SELECT id FROM #__survey_force_user_answers"
							. "\n WHERE quest_id = '" . $questions_data[$i]->id . "' AND survey_id = '" . $questions_data[$i]->sf_survey . "' AND start_id IN (" . $start_ids . ")  GROUP BY start_id, quest_id ";
						$database->setQuery($query);
						$questions_data[$i]->total_answers = count($database->loadColumn());

						$questions_data[$i]->answer = array();
						$questions_data[$i]->answers_top100 = array();
						$questions_data[$i]->answer_count = $n;
						for ($j = 0; $j < $n; $j++)
						{
							$query = "SELECT answer FROM #__survey_force_user_answers WHERE ans_field = " . ($j + 1)
								. " AND quest_id = '" . $questions_data[$i]->id . "' AND a.start_id IN (" . $start_ids . ") "
								. " AND survey_id = '" . $questions_data[$i]->sf_survey . "' ";
							$database->setQuery($query);
							$ans_txt_data = @array_merge(array(0 => 0), $database->loadColumn());

							$query = "SELECT b.ans_txt, count(a.answer) as ans_count FROM #__survey_force_user_ans_txt as b,"
								. "\n #__survey_force_user_answers as a"
								. "\n WHERE a.quest_id = '" . $questions_data[$i]->id . "'"
								. "\n AND a.answer = b.id AND a.start_id IN (" . $start_ids . ") "
								. "\n AND a.answer IN (" . implode(',', $ans_txt_data) . ") "
								. "\n GROUP BY b.ans_txt ORDER BY ans_count DESC LIMIT 0,5";
							$database->setQuery($query);
							$ans_data = $database->loadObjectList();
							$jj = 0;
							$tmp = array();
							while ($jj < count($ans_data))
							{
								$tmp[$jj]->num = $jj;
								$tmp[$jj]->ftext = $ans_data[$jj]->ans_txt;
								$tmp[$jj]->ans_count = ($is_pc ? intval($ans_data[$jj]->ans_count / $questions_data[$i]->total_answers * 100) : $ans_data[$jj]->ans_count);
								$jj++;
							}
							$questions_data[$i]->answer[$j] = $tmp;

							$query = "SELECT b.ans_txt FROM #__survey_force_user_ans_txt as b, #__survey_force_user_answers as a"
								. "\n WHERE a.quest_id = '" . $questions_data[$i]->id . "' AND a.answer = b.id"
								. "\n AND a.answer IN (" . implode(',', $ans_txt_data) . ") AND a.start_id IN (" . $start_ids . ") "
								. "\n ORDER BY a.sf_time DESC LIMIT 0,100";
							$database->setQuery($query);
							$ans_data = $database->loadColumn();
							$ans_data = (is_array($ans_data) ? $ans_data : array());
							$questions_data[$i]->answers_top100[$j] = implode(', ', $ans_data);
						}
					}
					else
					{
						$query = "SELECT id FROM #__survey_force_user_answers"
							. "\n WHERE quest_id = '" . $questions_data[$i]->id . "' AND survey_id = '" . $questions_data[$i]->sf_survey . "' AND start_id IN (" . $start_ids . ")  GROUP BY start_id, quest_id ";
						$database->setQuery($query);
						$questions_data[$i]->total_answers = count($database->loadColumn());

						$query = "SELECT b.ans_txt, count(a.answer) as ans_count FROM #__survey_force_user_ans_txt as b, #__survey_force_user_answers as a"
							. "\n WHERE a.quest_id = '" . $questions_data[$i]->id . "' and a.survey_id = '" . $questions_data[$i]->sf_survey . "' and a.answer = b.id and a.start_id IN (" . $start_ids . ")"
							. "\n GROUP BY b.ans_txt ORDER BY ans_count DESC LIMIT 0,5";
						$database->setQuery($query);
						$ans_data = $database->loadObjectList();
						$questions_data[$i]->answer = array();
						$j = 0;
						while ($j < count($ans_data))
						{
							$questions_data[$i]->answer[$j]->num = $j;
							$questions_data[$i]->answer[$j]->ftext = $ans_data[$j]->ans_txt;
							$questions_data[$i]->answer[$j]->ans_count = ($is_pc ? intval($ans_data[$j]->ans_count / $questions_data[$i]->total_answers * 100) : $ans_data[$j]->ans_count);
							$j++;
						}
						$ans_data = array();
						$query = "SELECT b.ans_txt FROM #__survey_force_user_ans_txt as b, #__survey_force_user_answers as a"
							. "\n WHERE a.quest_id = '" . $questions_data[$i]->id . "' and a.survey_id = '" . $questions_data[$i]->sf_survey . "' and a.start_id IN (" . $start_ids . ") and a.answer = b.id "
							. "\n ORDER BY a.sf_time DESC LIMIT 0,100";
						$database->setQuery($query);
						$ans_data = $database->loadColumn();
						if (count($ans_data) > 0)
						{
							$questions_data[$i]->answers_top100 = implode(', ', $ans_data);
						}
						else
						{
							$questions_data[$i]->answers_top100 = '';
						}
					}
					break;
				case 1:
					$query = "SELECT count(distinct start_id) FROM #__survey_force_user_answers"
						. "\n WHERE quest_id = '" . $questions_data[$i]->id . "' and survey_id = '" . $questions_data[$i]->sf_survey . "' and start_id IN (" . $start_ids . ")";
					$database->setQuery($query);
					$questions_data[$i]->total_answers = $database->loadResult();

					$query = "SELECT * FROM #__survey_force_fields WHERE quest_id = '" . $questions_data[$i]->id . "' ORDER by ordering";
					$database->setQuery($query);
					$f_data = $database->loadObjectList();
					$j = 0;
					$questions_data[$i]->answer = array();
					while ($j < count($f_data))
					{
						$query = "SELECT b.stext, count(a.answer) as ans_count FROM #__survey_force_scales as b LEFT JOIN #__survey_force_user_answers as a ON ( a.ans_field = b.id AND a.answer = '" . $f_data[$j]->id . "' AND a.start_id IN (" . $start_ids . ") AND a.quest_id = '" . $questions_data[$i]->id . "' )"
							. "\n WHERE b.quest_id = '" . $questions_data[$i]->id . "'"
							. "\n GROUP BY b.stext ORDER BY b.ordering";
						$database->setQuery($query);
						$ans_data = $database->loadObjectList();
						$questions_data[$i]->answer[$j]->full_ans = array();
						$jj = 0;
						$questions_data[$i]->answer[$j]->ftext = $f_data[$j]->ftext;
						while ($jj < count($ans_data))
						{
							$questions_data[$i]->answer[$j]->full_ans[$jj]->ftext = $ans_data[$jj]->stext;
							$questions_data[$i]->answer[$j]->full_ans[$jj]->ans_count = ($is_pc ? intval($ans_data[$jj]->ans_count / $questions_data[$i]->total_answers * 100) : $ans_data[$jj]->ans_count);
							$jj++;
						}
						$j++;
					}
					break;
				case 5:
				case 6:
				case 9:
					$query = "SELECT count(distinct start_id) FROM #__survey_force_user_answers"
						. "\n WHERE quest_id = '" . $questions_data[$i]->id . "' and survey_id = '" . $questions_data[$i]->sf_survey . "' and start_id IN (" . $start_ids . ")";
					$database->setQuery($query);
					$questions_data[$i]->total_answers = $database->loadResult();

					$query = "SELECT * FROM #__survey_force_fields WHERE quest_id = '" . $questions_data[$i]->id . "' and is_main = '1' ORDER by ordering";
					$database->setQuery($query);
					$f_data = $database->loadObjectList();
					$j = 0;
					$questions_data[$i]->answer = array();
					while ($j < count($f_data))
					{
						$query = "SELECT b.ftext, count(a.answer) as ans_count FROM #__survey_force_fields as b LEFT JOIN #__survey_force_user_answers as a ON ( a.ans_field = b.id AND a.answer = '" . $f_data[$j]->id . "' AND a.start_id IN (" . $start_ids . ") AND a.quest_id = '" . $questions_data[$i]->id . "' )"
							. "\n WHERE b.quest_id = '" . $questions_data[$i]->id . "' and b.is_main = '0'"
							. "\n GROUP BY b.ftext ORDER BY b.ordering";//ans_count DESC
						$database->setQuery($query);
						$ans_data = $database->loadObjectList();
						$questions_data[$i]->answer[$j]->full_ans = array();
						$jj = 0;
						$questions_data[$i]->answer[$j]->ftext = $f_data[$j]->ftext;
						while ($jj < count($ans_data))
						{
							$questions_data[$i]->answer[$j]->full_ans[$jj]->ftext = $ans_data[$jj]->ftext;
							$questions_data[$i]->answer[$j]->full_ans[$jj]->ans_count = ($is_pc ? intval($ans_data[$jj]->ans_count / $questions_data[$i]->total_answers * 100) : $ans_data[$jj]->ans_count);
							$jj++;
						}
						$j++;
					}
					break;
			}
			$i++;
		}

		if ($is_pdf)
		{
			SF_PrintRepSurv_List($survey_data, $questions_data, 0, $is_pc);
		}
		else
		{
			return;
			survey_force_adm_html::SF_ViewRepSurv_List($option, $survey_data, $questions_data, 1, $list_data->id);
		}
	}

	public static function SF_PrintReports($rows)
	{
		$database = JFactory::getDbo();
		chdir(JPATH_SITE);
		include(_SURVEY_FORCE_ADMIN_HOME . '/includes/class.ezpdf.php');
		//
		$pdf = new Cezpdf('a4', 'P');  //A4 Portrait
		$pdf->ezSetCmMargins(2, 1.5, 1, 1);
		$pdf->selectFont(JPATH_SITE . '/media/Helvetica.afm'); //choose font
		//
		$all = $pdf->openObject();
		$pdf->saveState();
		$pdf->setStrokeColor(0, 0, 0, 1);
		// footer and header
		//
		$pdf->restoreState();
		$pdf->closeObject();
		$pdf->addObject($all, 'all');
		$pdf->ezSetDy(30);

		//get PDF content

		for ($i = 0, $n = count($rows); $i < $n; $i++)
		{
			$row = $rows[$i];
			$text_to_pdf = mosFormatDate($row->sf_time, _CURRENT_SERVER_TIME_FORMAT) . "  - " . (($row->is_complete) ? 'completed' : 'not completed') . "\n";
			$text_to_pdf .= $row->survey_name . "\n";
			switch ($row->usertype)
			{
				case '0':
					$text_to_pdf .= JText::_('COM_SF_GUEST') . " - ";
					break;
				case '1':
					$text_to_pdf .= JText::_('COM_SF_REGISTERED_USER') . " - ";
					break;
				case '2':
					$text_to_pdf .= JText::_('COM_SF_INVITED_USER') . " - ";
					break;
			}
			switch ($row->usertype)
			{
				case '0':
					$text_to_pdf .= JText::_('COM_SF_ANONYMOUS');
					break;
				case '1':
					$text_to_pdf .= $row->reg_username . ", " . $row->reg_name . " (" . $row->reg_email . ")";
					break;
				case '2':
					$text_to_pdf .= $row->inv_name . " " . $row->inv_lastname . " (" . $row->inv_email . ")";
					break;
			}
			$pdf->ezText($text_to_pdf, 12);
			$pdf->line(10, $pdf->y - 10, 578, $pdf->y - 10);
			$text_to_pdf = "\n";
			$pdf->ezText($text_to_pdf, 6);
		}

		$filedata = $pdf->ezOutput();
		@ob_end_clean();
		header("Content-type: application/pdf");
		header("Content-Length: " . strlen(ltrim($filedata)));
		header("Content-Disposition: attachment; filename=report.pdf");
		echo $filedata;
		die;
	}

	public static function SF_PrintReportsPDF_full($rows)
	{
		$database = JFactory::getDbo();
		/*
	 * Create the pdf document
	 */

		require_once(_SURVEY_FORCE_ADMIN_HOME . '/assets/tcpdf/sf_pdf.php');

		$pdf_doc = new sf_pdf();

		$pdf = &$pdf_doc->_engine;

		$pdf->getAliasNbPages();
		$pdf->AddPage();

		$pdf->SetFont('dejavusans');
		$fontFamily = $pdf->getFontFamily();

		$cur_survey = -1;
		$is_first = 1;
		for ($i = 0, $n = count($rows); $i < $n; $i++)
		{
			$row = $rows[$i];
			if ($cur_survey != $row->survey_id)
			{
				if (!$is_first)
				{
					$pdf->AddPage();
				}
				$is_first = 0;
				$pdf->SetFontSize(10);
				$pdf->setFont($fontFamily, 'B');
				$pdf->Write(5, JText::_('COM_SF_SURVEY_INFORMATION'), '', 0);
				$pdf->Ln();
				$pdf->Ln();

				$pdf->SetFontSize(8);
				$pdf->Write(5, JText::_('COM_SF_NAME') . ": ", '', 0);

				$pdf->setFont($fontFamily, 'B');
				$pdf->Write(5, $pdf_doc->cleanText($row->survey_data[0]->sf_name), '', 0);
				$pdf->Ln();

				$pdf->setFont($fontFamily, 'B');
				$pdf->Write(5, JText::_('COM_SF_DESCRIPTION'), '', 0);

				$pdf->setFont($fontFamily, 'B');
				$pdf->Write(5, $pdf_doc->cleanText($row->survey_data[0]->sf_descr), '', 0);
				$pdf->Ln();

				$pdf->line(15, $pdf->GetY(), 200, $pdf->GetY());
				$pdf->line(15, $pdf->GetY() + 2, 200, $pdf->GetY() + 2);
				$pdf->Ln();
			}
			$cur_survey = $row->survey_id;

			$pdf->SetFontSize(10);
			$pdf->setFont($fontFamily, 'B');
			$pdf->Write(5, JText::_('COM_SF_USER_INFORMATION'), '', 0);
			$pdf->Ln();

			$pdf->SetFontSize(8);
			$pdf->Write(5, JText::_('COM_SF_START_AT') . ": ", '', 0);
			$pdf->Ln();

			$text_to_pdf = mosFormatDate($row->start_data[0]->sf_time, _CURRENT_SERVER_TIME_FORMAT) . (($row->is_complete) ? ' (completed)' : ' (not completed)');
			$text_to_pdf = $pdf_doc->cleanText($text_to_pdf);
			$pdf->setFont($fontFamily, 'B');
			$pdf->Write(5, $text_to_pdf, '', 0);
			$pdf->Ln();

			$pdf->setFont($fontFamily, 'B');
			$pdf->Write(5, JText::_('COM_SF_USER') . ": ", '', 0);

			$pdf->setFont($fontFamily, 'B');
			$text_to_pdf = '';
			switch ($row->usertype)
			{
				case '0':
					$text_to_pdf .= JText::_('COM_SF_GUEST') . " - ";
					break;
				case '1':
					$text_to_pdf .= JText::_('COM_SF_REGISTERED_USER') . " - ";
					break;
				case '2':
					$text_to_pdf .= JText::_('COM_SF_INVITED_USER') . " - ";
					break;
			}
			switch ($row->usertype)
			{
				case '0':
					$text_to_pdf .= JText::_('COM_SF_ANONYMOUS');
					break;
				case '1':
					$text_to_pdf .= $row->reg_username . ", " . $row->reg_name . " (" . $row->reg_email . ")";
					break;
				case '2':
					$text_to_pdf .= $row->inv_name . " " . $row->inv_lastname . " (" . $row->inv_email . ")";
					break;
			}
			$text_to_pdf = $pdf_doc->cleanText($text_to_pdf);
			$pdf->Write(5, $pdf_doc->cleanText($text_to_pdf), '', 0);
			$pdf->Ln();

			$pdf->line(15, $pdf->GetY(), 200, $pdf->GetY());
			$pdf->line(15, $pdf->GetY() + 2, 200, $pdf->GetY() + 2);
			$pdf->Ln();

			$pdf->setFont($fontFamily, 'B');
			$pdf->Write(5, JText::_('COM_SF_USER_ANSWERS'), '', 0);
			$pdf->Ln();
			$pdf->line(15, $pdf->GetY(), 200, $pdf->GetY());
			$pdf->Ln();
			$pdf->setFont($fontFamily, 'B');

			foreach ($row->questions_data as $qrow)
			{
				$text_to_pdf = $pdf_doc->cleanText($qrow->sf_qtext);
				$pdf->SetFontSize(10);
				$pdf->Write(5, $text_to_pdf, '', 0);
				$pdf->Ln();

				switch ($qrow->sf_qtype)
				{
					case 2:
					case 3:
						$text_to_pdf = '';
						foreach ($qrow->answer as $arow)
						{
							$img_ans = $arow->alt_text ? " - " . JText::_('COM_SF_USER_CHOICE') : '';
							$text_to_pdf .= $arow->f_text . $img_ans . "\n";
						}
						$text_to_pdf = $pdf_doc->cleanText($text_to_pdf);
						$pdf->SetFontSize(8);

						$pdf->Write(5, $text_to_pdf, '', 0);
						$pdf->Ln();
						break;
					case 1:
						$text_to_pdf = JText::_('COM_SF_SCALE') . ": " . $qrow->scale;
						$text_to_pdf = $pdf_doc->cleanText($text_to_pdf);
						$pdf->SetFontSize(8);
						$pdf->Write(5, $text_to_pdf, '', 0);
						$pdf->Ln();
					case 5:
					case 6:
					case 9:
						$text_to_pdf = '';
						foreach ($qrow->answer as $arow)
						{
							$text_to_pdf .= $arow->f_text . " - " . $arow->alt_text . "\n";
						}
						$text_to_pdf = $pdf_doc->cleanText($text_to_pdf);
						$pdf->SetFontSize(8);
						$pdf->Write(5, $text_to_pdf, '', 0);
						$pdf->Ln();
						break;
					case 4:
						if (isset($qrow->answer_count))
						{
							$tmp = JText::_('COM_SF_1ST_ANSWER');
							for ($ii = 1; $ii <= $qrow->answer_count; $ii++)
							{
								if ($ii == 2) $tmp = JText::_('COM_SF_SECOND_ANSWER');
								elseif ($ii == 3) $tmp = JText::_('COM_SF_THIRD_ANSWER');
								elseif ($ii > 3) $tmp = $ii . JText::_('COM_SF_TH_ANSWER');
								foreach ($qrow->answer as $answer)
								{
									if ($answer->ans_field == $ii)
									{
										$text_to_pdf = $tmp . ($answer->ans_txt == '' ? ' ' . JText::_('COM_SURVEYFORCE_NO_ANSWER') : $answer->ans_txt) . "\n";
										$text_to_pdf = $pdf_doc->cleanText($text_to_pdf);
										$pdf->SetFontSize(8);
										$pdf->Write(5, $text_to_pdf, '', 0);
										$pdf->Ln();
										$tmp = -1;
									}
								}
								if ($tmp != -1)
								{
									$text_to_pdf = $tmp . " " . JText::_('COM_SURVEYFORCE_NO_ANSWER') . "\n";
									$text_to_pdf = $pdf_doc->cleanText($text_to_pdf);
									$pdf->SetFontSize(8);
									$pdf->Write(5, $text_to_pdf, '', 0);
									$pdf->Ln();
								}
							}
						}
						else
						{
							$text_to_pdf = $qrow->answer . "\n";
							$text_to_pdf = $pdf_doc->cleanText($text_to_pdf);
							$pdf->SetFontSize(8);
							$pdf->Write(5, $text_to_pdf, '', 0);
							$pdf->Ln();

						}
						break;
					default:
						$text_to_pdf = $qrow->answer . "\n";
						$text_to_pdf = $pdf_doc->cleanText($text_to_pdf);
						$pdf->SetFontSize(8);
						$pdf->Write(5, $text_to_pdf, '', 0);
						$pdf->Ln();
						break;
				}
				if ($qrow->sf_impscale)
				{
					$text_to_pdf = $qrow->iscale_name;
					$text_to_pdf = $pdf_doc->cleanText($text_to_pdf);
					$pdf->SetFontSize(10);
					$pdf->Write(5, $text_to_pdf, '', 0);
					$pdf->Ln();
					$text_to_pdf = '';
					foreach ($qrow->answer_imp as $arow)
					{
						$img_ans = $arow->alt_text ? " - " . JText::_('COM_SF_USER_CHOICE') : '';
						$text_to_pdf .= $arow->f_text . $img_ans . "\n";
					}
					$text_to_pdf = $pdf_doc->cleanText($text_to_pdf);
					$pdf->SetFontSize(8);

					$pdf->Write(5, $text_to_pdf, '', 0);
					$pdf->Ln();
				}
				$pdf->line(15, $pdf->GetY(), 200, $pdf->GetY());
			}
			$pdf->line(15, $pdf->GetY(), 200, $pdf->GetY());
		}

		$data = $pdf->Output('', 'S');

		@ob_end_clean();
		header("Content-type: application/pdf");
		header("Content-Length: " . strlen(ltrim($data)));
		header("Content-Disposition: attachment; filename=report.pdf");
		echo $data;
		die;
	}

	public static function SF_PrintReportsCSV_sum($rows)
	{
		$database = JFactory::getDbo();
		$text_to_csv = "";
		$cur_survey = -1;
		for ($ij = 0, $n = count($rows); $ij < $n; $ij++)
		{
			$row = $rows[$ij];
			if ($cur_survey != $row->survey_id)
			{
				$text_to_csv .= JText::_('COM_SF_SURVEY_INFORMATION') . ':,' . "\n";
				$text_to_csv .= JText::_('COM_SF_NAME') . ':,';
				$text_to_csv .= self::SF_processCSVField($row->survey_data[0]->sf_name) . "," . "\n";
				$text_to_csv .= JText::_('COM_SF_DESCRIPTION') . ',';
				$text_to_csv .= self::SF_processCSVField($row->survey_data[0]->sf_descr) . "," . "\n";
			}
			$cur_survey = $row->survey_id;
			$text_to_csv .= "\n" . JText::_('COM_SF_ANSWERS') . ':,' . "\n";
			foreach ($row->questions_data as $qrow)
			{
				$text_to_csv .= "\n" . self::SF_processCSVField($qrow->sf_qtext) . "," . "\n";
				switch ($qrow->sf_qtype)
				{
					case 2:
					case 3:
					case 4:
						if (isset($qrow->answer_count))
						{
							$tmp = JText::_('COM_SF_1ST_ANSWER');
							for ($ii = 1; $ii <= $qrow->answer_count; $ii++)
							{
								if ($ii == 2) $tmp = JText::_('COM_SF_SECOND_ANSWER');
								elseif ($ii == 3) $tmp = JText::_('COM_SF_THIRD_ANSWER');
								elseif ($ii > 3) $tmp = $ii . JText::_('COM_SF_TH_ANSWER');
								$text_to_csv .= $tmp . "\n";
								$total = $qrow->total_answers;
								$i = 0;
								$tmp_data = array();
								if (count($qrow->answer[$ii - 1]) > 0)
								{
									foreach ($qrow->answer[$ii - 1] as $arow)
									{
										$tmp_data[$i] = $arow->ans_count;
										$i++;
									}
									foreach ($qrow->answer[$ii - 1] as $arow)
									{
										$text_to_csv .= self::SF_processCSVField($arow->ftext) . ",," . $arow->ans_count . "\n";
									}
									if ($qrow->sf_qtype == 4)
									{
										$text_to_csv .= JText::_('COM_SF_OTHER_ANSWERS') . ':,,' . self::SF_processCSVField($qrow->answers_top100[$ii - 1]) . "\n";
									}

								}
							}
						}
						else
						{
							$total = $qrow->total_answers;
							$i = 0;
							$tmp_data = array();
							foreach ($qrow->answer as $arow)
							{
								$tmp_data[$i] = $arow->ans_count;
								$i++;
							}
							foreach ($qrow->answer as $arow)
							{
								$text_to_csv .= self::SF_processCSVField($arow->ftext) . ",," . $arow->ans_count . "\n";
							}
							if ($qrow->sf_qtype == 4)
							{
								$text_to_csv .= JText::_('COM_SF_OTHER_ANSWERS') . ':,,' . self::SF_processCSVField($qrow->answers_top100) . "\n";
							}
						}
						break;

					case 1:
					case 5:
					case 6:
					case 9:
						$total = $qrow->total_answers;
						foreach ($qrow->answer as $arows)
						{
							$i = 0;
							$tmp_data = array();
							foreach ($arows->full_ans as $arow)
							{
								$tmp_data[$i] = $arow->ans_count;
								$i++;
							}
							if (isset($arows->ftext))
							{
								$text_to_csv .= JText::_('COM_SF_OPTION') . ':,' . self::SF_processCSVField($arows->ftext) . "\n";
							}

							foreach ($arows->full_ans as $arow)
							{
								$text_to_csv .= self::SF_processCSVField($arow->ftext) . ",," . $arow->ans_count . "\n";
							}
						}
						break;
				}
				if ($qrow->sf_impscale)
				{
					$total = $qrow->total_iscale_answers;
					$i = 0;
					$tmp_data = array();
					foreach ($qrow->answer_imp as $arow)
					{
						$tmp_data[$i] = $arow->ans_count;
						$i++;
					}

					$text_to_csv .= self::SF_processCSVField($qrow->iscale_name) . "\n";
					foreach ($qrow->answer_imp as $arow)
					{
						$text_to_csv .= self::SF_processCSVField($arow->ftext) . ",," . $arow->ans_count . "\n";
					}
				}
			}
			$text_to_csv .= "\n";
		}
		@ob_end_clean();
		header("Content-type: application/csv");
		header("Content-Length: " . strlen(ltrim($text_to_csv)));
		header("Content-Disposition: inline; filename=report.csv");
		echo $text_to_csv;
		die;
	}

	public static function SF_PrintRepResult($start_data, $survey_data, $questions_data)
	{
        /*
         * Create the pdf document
         */

		require_once(_SURVEY_FORCE_ADMIN_HOME . '/assets/tcpdf/sf_pdf.php');

		$pdf_doc = new sf_pdf();

		$pdf = &$pdf_doc->_engine;

		$pdf->getAliasNbPages();
		$pdf->AddPage();

		$pdf->SetFont('dejavusans');
		$fontFamily = $pdf->getFontFamily();

		$s_user = '';
		switch ($start_data[0]->usertype)
		{
			case '0':
				$s_user = JText::_('COM_SF_ANONYMOUS');
				break;
			case '1':
				$s_user = JText::_('COM_SF_REGISTERED_USER') . ": " . $start_data[0]->reg_username . ", " . $start_data[0]->reg_name . " (" . $start_data[0]->reg_email . ")";
				break;
			case '2':
				$s_user = JText::_('COM_SF_INVITED_USER') . ": " . $start_data[0]->inv_name . " " . $start_data[0]->inv_lastname . " (" . $start_data[0]->inv_email . ")";
				break;
		}
		$s_user = $pdf_doc->cleanText($s_user);

		$pdf->SetFontSize(10);
		$pdf->setFont($fontFamily, 'B');
		$pdf->Write(5, JText::_('COM_SF_SURVEY_INFORMATION'), '', 0);
		$pdf->Ln();
		$pdf->Ln();

		$pdf->SetFontSize(8);
		$pdf->Write(5, JText::_('COM_SF_NAME') . ": ", '', 0);

		$pdf->setFont($fontFamily, 'B');
		$pdf->Write(5, $pdf_doc->cleanText($survey_data[0]->sf_name), '', 0);
		$pdf->Ln();

		$pdf->setFont($fontFamily, 'B');
		$pdf->Write(5, JText::_('COM_SF_DESCRIPTION'), '', 0);

		$pdf->setFont($fontFamily, 'B');
		$pdf->Write(5, $pdf_doc->cleanText($survey_data[0]->sf_descr), '', 0);
		$pdf->Ln();

		$pdf->setFont($fontFamily, 'B');
		$pdf->Write(5, JText::_('COM_SF_START_AT') . ": ", '', 0);

		$pdf->setFont($fontFamily, 'B');
		$pdf->Write(5, $pdf_doc->cleanText(mosFormatDate($start_data[0]->sf_time, _CURRENT_SERVER_TIME_FORMAT)), '', 0);
		$pdf->Ln();

		$pdf->setFont($fontFamily, 'B');
		$pdf->Write(5, JText::_('COM_SF_USER') . ": ", '', 0);

		$pdf->setFont($fontFamily, 'B');
		$pdf->Write(5, $s_user, '', 0);
		$pdf->Ln();
		$pdf->Ln();

		$pdf->setFont($fontFamily, 'B');
		$pdf->Write(5, JText::_('COM_SF_USER_ANSWERS'), '', 0);
		$pdf->Ln();
		$pdf->line(15, $pdf->GetY(), 200, $pdf->GetY());
		$pdf->Ln();
		$pdf->setFont($fontFamily, 'B');


		foreach ($questions_data as $qrow)
		{
			$text_to_pdf = $pdf_doc->cleanText($qrow->sf_qtext);
			$pdf->SetFontSize(10);
			$pdf->Write(5, $text_to_pdf, '', 0);
			$pdf->Ln();
			switch ($qrow->sf_qtype)
			{
				case 2:
				case 3:
					$text_to_pdf = '';
					foreach ($qrow->answer as $arow)
					{
						$img_ans = $arow->alt_text ? " - " . JText::_('COM_SF_USER_CHOICE') : '';
						$text_to_pdf .= $arow->f_text . $img_ans . "\n";
					}
					$text_to_pdf = $pdf_doc->cleanText($text_to_pdf);
					$pdf->SetFontSize(8);

					$pdf->Write(5, $text_to_pdf, '', 0);
					$pdf->Ln();
					break;
				case 1:
					$text_to_pdf = JText::_('COM_SF_SCALE') . ": " . $qrow->scale;
					$text_to_pdf = $pdf_doc->cleanText($text_to_pdf);
					$pdf->SetFontSize(8);
					$pdf->Write(5, $text_to_pdf, '', 0);
					$pdf->Ln();
				case 5:
				case 6:
				case 9:
					$text_to_pdf = '';
					foreach ($qrow->answer as $arow)
					{
						$text_to_pdf .= $arow->f_text . " - " . $arow->alt_text . "\n";
					}
					$text_to_pdf = $pdf_doc->cleanText($text_to_pdf);
					$pdf->SetFontSize(8);
					$pdf->Write(5, $text_to_pdf, '', 0);
					$pdf->Ln();
					break;
				case 4:
					if (isset($qrow->answer_count))
					{
						$tmp = JText::_('COM_SF_1ST_ANSWER');
						for ($i = 1; $i <= $qrow->answer_count; $i++)
						{
							if ($i == 2) $tmp = JText::_('COM_SF_SECOND_ANSWER');
							elseif ($i == 3) $tmp = JText::_('COM_SF_THIRD_ANSWER');
							else $tmp = $i . JText::_('COM_SF_TH_ANSWER');
							foreach ($qrow->answer as $answer)
							{
								if ($answer->ans_field == $i)
								{
									$text_to_pdf = $tmp . ($answer->ans_txt == '' ? ' ' . JText::_('COM_SURVEYFORCE_NO_ANSWER') : $answer->ans_txt) . "\n";
									$text_to_pdf = $pdf_doc->cleanText($text_to_pdf);
									$pdf->SetFontSize(8);
									$pdf->Write(5, $text_to_pdf, '', 0);
									$pdf->Ln();
									$tmp = -1;
								}
							}
							if ($tmp != -1)
							{
								$text_to_pdf = $tmp . " " . JText::_('COM_SURVEYFORCE_NO_ANSWER') . "\n";
								$text_to_pdf = $pdf_doc->cleanText($text_to_pdf);
								$pdf->SetFontSize(8);
								$pdf->Write(5, $text_to_pdf, '', 0);
								$pdf->Ln();
							}
						}
					}
					else
					{
						$text_to_pdf = $qrow->answer . "\n";
						$text_to_pdf = $pdf_doc->cleanText($text_to_pdf);
						$pdf->SetFontSize(8);
						$pdf->Write(5, $text_to_pdf, '', 0);
						$pdf->Ln();
					}
					break;
				default:
					$text_to_pdf = $qrow->answer . "\n";
					$text_to_pdf = $pdf_doc->cleanText($text_to_pdf);
					$pdf->SetFontSize(8);
					$pdf->Write(5, $text_to_pdf, '', 0);
					$pdf->Ln();
					break;
			}
			if ($qrow->sf_impscale)
			{
				$text_to_pdf = $qrow->iscale_name;
				$text_to_pdf = $pdf_doc->cleanText($text_to_pdf);
				$pdf->SetFontSize(10);
				$pdf->Write(5, $text_to_pdf, '', 0);
				$pdf->Ln();
				$text_to_pdf = '';
				foreach ($qrow->answer_imp as $arow)
				{
					$img_ans = $arow->alt_text ? " - " . JText::_('COM_SF_USER_CHOICE') : '';
					$text_to_pdf .= $arow->f_text . $img_ans . "\n";
				}
				$text_to_pdf = $pdf_doc->cleanText($text_to_pdf);
				$pdf->SetFontSize(8);

				$pdf->Write(5, $text_to_pdf, '', 0);
				$pdf->Ln();
			}
			$pdf->line(15, $pdf->GetY(), 200, $pdf->GetY());
			$pdf->Ln();
		}

		$data = $pdf->Output('', 'S');

		@ob_end_clean();
		header("Content-type: application/pdf");
		header("Content-Length: " . strlen(ltrim($data)));
		header("Content-Disposition: attachment; filename=report.pdf");
		echo $data;
		die;
	}

	public static function SF_PrintRepSurv_List($survey_data, $questions_data, $is_list = 0, $is_pc = 0)
	{
		clearOldImages();
		/*
	 * Create the pdf document
	 */

		require_once(_SURVEY_FORCE_ADMIN_HOME . '/tcpdf/sf_pdf.php');

		$pdf_doc = new sf_pdf();

		$pdf = &$pdf_doc->_engine;

		$pdf->getAliasNbPages();
		$pdf->AddPage();

		$pdf->SetFont('dejavusans');
		$fontFamily = $pdf->getFontFamily();

		//get PDF content
		$pdf->SetFontSize(10);
		$pdf->setFont($fontFamily, 'B');
		$pdf->Write(5, JText::_('COM_SF_SURVEY_INFORMATION'), '', 0);
		$pdf->Ln();
		$pdf->Ln();

		$pdf->SetFontSize(8);
		$pdf->Write(5, JText::_('COM_SF_NAME') . ": ", '', 0);

		$pdf->setFont($fontFamily, 'B');
		$pdf->Write(5, $pdf_doc->cleanText($survey_data->sf_name), '', 0);
		$pdf->Ln();

		$pdf->setFont($fontFamily, 'B');
		$pdf->Write(5, JText::_('COM_SF_DESCRIPTION'), '', 0);

		$pdf->setFont($fontFamily, 'B');
		$pdf->Write(5, $pdf_doc->cleanText($survey_data->sf_descr), '', 0);
		$pdf->Ln();
		$pdf->Ln();

		if ($is_list == 1)
		{
			$pdf->SetLeftMargin($pdf_doc->_margin_left);
			$options = array('total' => (($survey_data->total_starts > $survey_data->total_inv_users) ? $survey_data->total_starts : $survey_data->total_inv_users),
				'grids' => $survey_data->total_inv_users . ',' . $survey_data->total_starts . ',' . $survey_data->total_completes,
				'fileName' => JPATH_ROOT . "/media/com_surveyforce/gen_images/" . (strlen(date('d')) < 2 ? '0' . date('d') : date('d')) . '_' . md5(uniqid(time())) . '.png');
			SF_draw_grid($options);
			$pdf->Image($options['fileName'], $pdf->GetX(), $pdf->GetY(), 0, 0, '', '', '', false, 50);

			$text_to_pdf = $survey_data->total_inv_users . " - " . JText::_('COM_SF_TOTAL_INVITED_USERS');
			$pdf->SetLeftMargin(60);
			$pdf->setFont($fontFamily, 'B');
			$pdf->Write(4.5, $pdf_doc->cleanText($text_to_pdf), '', 0);
			$pdf->Ln();
			$pdf->Write(4.5, $pdf_doc->cleanText($survey_data->total_starts . " - " . JText::_('COM_SF_TOTAL_STARTS_OF_SURVEY')), '', 0);
			$pdf->Ln();
			$pdf->Write(4.5, $pdf_doc->cleanText($survey_data->total_completes . " - " . JText::_('COM_SF_TOTAL_COMPLETES_OF_SURVEY')), '', 0);
			$pdf->Ln();
			$pdf->SetLeftMargin($pdf_doc->_margin_left);
		}
		else
		{
			$pdf->SetLeftMargin($pdf_doc->_margin_left);
			$options = array('total' => $survey_data->total_starts,
				'grids' => $survey_data->total_starts . ',' . $survey_data->total_gstarts . ','
					. $survey_data->total_rstarts . ',' . $survey_data->total_istarts . ',' . $survey_data->total_completes . ','
					. $survey_data->total_gcompletes . ',' . $survey_data->total_rcompletes . ',' . $survey_data->total_icompletes,
				'fileName' => JPATH_ROOT . "/media/com_surveyforce/gen_images/" . (strlen(date('d')) < 2 ? '0' . date('d') : date('d')) . '_' . md5(uniqid(time())) . '.png');
			SF_draw_grid($options);
			$pdf->Image($options['fileName'], $pdf->GetX(), $pdf->GetY(), 0, 0, '', '', '', false, 50);

			$text_to_pdf = $survey_data->total_starts . " - " . JText::_('COM_SF_TOTAL_STARTS_OF_SURVEY');
			$pdf->SetLeftMargin(60);
			$pdf->setFont($fontFamily, 'B');
			$pdf->Write(4.5, $pdf_doc->cleanText($text_to_pdf), '', 0);
			$pdf->Ln();
			$pdf->Write(4.5, $pdf_doc->cleanText($survey_data->total_gstarts . " - " . JText::_('COM_SF_TOTAL_STARTS_OF_SURVEY_GUEST')), '', 0);
			$pdf->Ln();
			$pdf->Write(4.5, $pdf_doc->cleanText($survey_data->total_rstarts . " - " . JText::_('COM_SF_TOTAL_STARTS_OF_SURVEY_REGISTERED')), '', 0);
			$pdf->Ln();
			$pdf->Write(4.5, $pdf_doc->cleanText($survey_data->total_istarts . " - " . JText::_('COM_SF_TOTAL_STARTS_OF_SURVEY_INVITED')), '', 0);
			$pdf->Ln();
			$pdf->Write(4.5, $pdf_doc->cleanText($survey_data->total_completes . " - " . JText::_('COM_SF_TOTAL_COMPLETES_OF_SURVEY')), '', 0);
			$pdf->Ln();
			$pdf->Write(4.5, $pdf_doc->cleanText($survey_data->total_gcompletes . " - " . JText::_('COM_SF_TOTAL_COMPLETES_OF_SURVEY_GUEST')), '', 0);
			$pdf->Ln();
			$pdf->Write(4.5, $pdf_doc->cleanText($survey_data->total_rcompletes . " - " . JText::_('COM_SF_TOTAL_COMPLETES_OF_SURVEY_REGISTERED')), '', 0);
			$pdf->Ln();
			$pdf->Write(4.5, $pdf_doc->cleanText($survey_data->total_icompletes . " - " . JText::_('COM_SF_TOTAL_COMPLETES_OF_SURVEY_INVITED')), '', 0);
			$pdf->Ln();
			$pdf->SetLeftMargin($pdf_doc->_margin_left);

		}
		$pdf->Ln();
		$pdf->line(15, $pdf->GetY(), 200, $pdf->GetY());
		$pdf->Ln();
		$pdf->Ln();

		$tmp_data = array();
		$total = 0;
		$i = 0;
		foreach ($questions_data as $qrow)
		{
			switch ($qrow->sf_qtype)
			{
				case 2:
				case 3:
				case 4:
					if (isset($qrow->answer_count))
					{
						$tmp = JText::_('COM_SF_1ST_ANSWER');

						$text_to_pdf = $pdf_doc->cleanText($qrow->sf_qtext);
						$pdf->SetFontSize(10);
						$pdf->Write(5, $text_to_pdf, '', 0);
						$pdf->Ln();

						for ($ii = 1; $ii <= $qrow->answer_count; $ii++)
						{
							if ($ii == 2) $tmp = JText::_('COM_SF_SECOND_ANSWER');
							elseif ($ii == 3) $tmp = JText::_('COM_SF_THIRD_ANSWER');
							elseif ($ii > 3) $tmp = $ii . JText::_('COM_SF_TH_ANSWER');

							$total = $qrow->total_answers;
							$i = 0;
							$tmp_data = array();
							foreach ($qrow->answer[$ii - 1] as $arow)
							{
								$tmp_data[$i] = ($is_pc ? round($arow->ans_count * $total / 100) : $arow->ans_count);
								$i++;
							}
							$rrr = count($tmp_data);

							$text_to_pdf = $pdf_doc->cleanText($tmp);
							$pdf->SetFontSize(8);

							$pdf->Write(5, $text_to_pdf, '', 0);
							$pdf->Ln();


							$pdf->SetLeftMargin($pdf_doc->_margin_left);
							$options = array('total' => $total,
								'grids' => implode(',', $tmp_data),
								'fileName' => JPATH_ROOT . "/media/com_surveyforce/gen_images/" . (strlen(date('d')) < 2 ? '0' . date('d') : date('d')) . '_' . md5(uniqid(time())) . '.png');
							SF_draw_grid($options);
							$pdf->Image($options['fileName'], $pdf->GetX(), $pdf->GetY(), 0, 0, '', '', '', false, 50);

							$pdf->SetLeftMargin(60);
							$pdf->setFont($fontFamily, 'B');
							$pdf->SetFontSize(8);
							foreach ($qrow->answer[$ii - 1] as $arow)
							{
								$pdf->Write(4.5, $pdf_doc->cleanText($arow->ans_count . ($is_pc ? '% ' : '') . " - " . $arow->ftext), '', 0);
								$pdf->Ln();
							}
							$pdf->SetLeftMargin($pdf_doc->_margin_left);
							if ($qrow->sf_qtype == 4)
							{
								$pdf->Write(4.5, $pdf_doc->cleanText(JText::_('COM_SF_OTHER_ANSWERS') . ": " . $qrow->answers_top100[$ii - 1]), '', 0);
								$pdf->Ln();
							}
						}
					}
					else
					{
						$total = $qrow->total_answers;
						$i = 0;
						$tmp_data = array();
						foreach ($qrow->answer as $arow)
						{
							$tmp_data[$i] = ($is_pc ? round($arow->ans_count * $total / 100) : $arow->ans_count);
							$i++;
						}
						$rrr = count($tmp_data);


						$text_to_pdf = $pdf_doc->cleanText($qrow->sf_qtext);
						$pdf->SetFontSize(10);

						$pdf->Write(5, $text_to_pdf, '', 0);
						$pdf->Ln();

						$pdf->SetLeftMargin($pdf_doc->_margin_left);
						$options = array('total' => $total,
							'grids' => implode(',', $tmp_data),
							'fileName' => JPATH_ROOT . "/media/com_surveyforce/gen_images/" . (strlen(date('d')) < 2 ? '0' . date('d') : date('d')) . '_' . md5(uniqid(time())) . '.png');
						SF_draw_grid($options);
						$pdf->Image($options['fileName'], $pdf->GetX(), $pdf->GetY(), 0, 0, '', '', '', false, 50);

						$pdf->SetLeftMargin(60);
						$pdf->SetFontSize(8);
						foreach ($qrow->answer as $arow)
						{
							$pdf->Write(4.5, $pdf_doc->cleanText($arow->ans_count . ($is_pc ? '% ' : '') . " - " . $arow->ftext), '', 0);
							$pdf->Ln();
						}
						$pdf->SetLeftMargin($pdf_doc->_margin_left);
						if ($qrow->sf_qtype == 4)
						{
							$pdf->Write(4.5, $pdf_doc->cleanText(JText::_('COM_SF_OTHER_ANSWERS') . ": " . $qrow->answers_top100), '', 0);
							$pdf->Ln();
						}
					}
					break;
				case 1:
				case 5:
				case 6:
				case 9:
					$total = $qrow->total_answers;
					if (count($qrow->answer) > 0)
					{
						$rrr = count($qrow->answer[0]->full_ans);
					}
					$text_to_pdf = $pdf_doc->cleanText($qrow->sf_qtext);
					$pdf->SetFontSize(10);

					$pdf->Write(5, $text_to_pdf, '', 0);
					$pdf->Ln();

					foreach ($qrow->answer as $arows)
					{
						$i = 0;
						$tmp_data = array();
						foreach ($arows->full_ans as $arow)
						{
							$tmp_data[$i] = ($is_pc ? round($arow->ans_count * $total / 100) : $arow->ans_count);
							$i++;
						}
						$rrr = count($tmp_data);

						$text_to_pdf = $pdf_doc->cleanText($arows->ftext);
						$pdf->SetFontSize(10);

						$pdf->Write(5, $text_to_pdf, '', 0);
						$pdf->Ln();

						$pdf->SetLeftMargin($pdf_doc->_margin_left);
						$options = array('total' => $total,
							'grids' => implode(',', $tmp_data),
							'fileName' => JPATH_ROOT . "/media/com_surveyforce/gen_images/" . (strlen(date('d')) < 2 ? '0' . date('d') : date('d')) . '_' . md5(uniqid(time())) . '.png');
						SF_draw_grid($options);
						$pdf->Image($options['fileName'], $pdf->GetX(), $pdf->GetY(), 0, 0, '', '', '', false, 50);

						$pdf->SetLeftMargin(60);
						$pdf->SetFontSize(8);
						foreach ($arows->full_ans as $arow)
						{
							$pdf->Write(4.5, $pdf_doc->cleanText($arow->ans_count . ($is_pc ? '% ' : '') . " - " . $arow->ftext), '', 0);
							$pdf->Ln();
						}
						$pdf->SetLeftMargin($pdf_doc->_margin_left);
						$pdf->Ln();
					}
					break;
			}
			if ($qrow->sf_impscale)
			{
				$total = $qrow->total_iscale_answers;
				$i = 0;
				$tmp_data = array();
				foreach ($qrow->answer_imp as $arow)
				{
					$tmp_data[$i] = ($is_pc ? round($arow->ans_count * $total / 100) : $arow->ans_count);
					$i++;
				}
				$rrr = count($tmp_data);

				$text_to_pdf = $pdf_doc->cleanText($qrow->iscale_name);
				$pdf->SetFontSize(10);

				$pdf->Write(5, $text_to_pdf, '', 0);
				$pdf->Ln();

				$pdf->SetLeftMargin($pdf_doc->_margin_left);
				$options = array('total' => $total,
					'grids' => implode(',', $tmp_data),
					'fileName' => JPATH_ROOT . "/media/com_surveyforce/gen_images/" . (strlen(date('d')) < 2 ? '0' . date('d') : date('d')) . '_' . md5(uniqid(time())) . '.png');
				SF_draw_grid($options);
				$pdf->Image($options['fileName'], $pdf->GetX(), $pdf->GetY(), 0, 0, '', '', '', false, 50);

				$pdf->SetLeftMargin(60);
				$pdf->SetFontSize(8);
				foreach ($qrow->answer_imp as $arow)
				{
					$pdf->Write(4.5, $pdf_doc->cleanText($arow->ans_count . " - " . $arow->ftext), '', 0);
					$pdf->Ln();
				}
				$pdf->SetLeftMargin($pdf_doc->_margin_left);
			}
			if ($qrow->sf_qtype != 7 && $qrow->sf_qtype != 8)
			{
				$pdf->Ln();
				$pdf->line(15, $pdf->GetY(), 200, $pdf->GetY());
				$pdf->Ln();
			}
		}

		$data = $pdf->Output('', 'S');
		@ob_end_clean();
		header("Content-type: application/pdf");
		header("Content-Length: " . strlen(ltrim($data)));
		header("Content-Disposition: attachment; filename=report.pdf");
		echo $data;
		die;
	}

	public static function get_html_translation_table_my()
	{
		$trans = get_html_translation_table(HTML_ENTITIES);
		$trans[chr(130)] = '&sbquo;';    // Single Low-9 Quotation Mark
		$trans[chr(131)] = '&fnof;';    // Latin Small Letter F With Hook
		$trans[chr(132)] = '&bdquo;';    // Double Low-9 Quotation Mark
		$trans[chr(133)] = '&hellip;';    // Horizontal Ellipsis
		$trans[chr(134)] = '&dagger;';    // Dagger
		$trans[chr(135)] = '&Dagger;';    // Double Dagger
		$trans[chr(136)] = '&circ;';    // Modifier Letter Circumflex Accent
		$trans[chr(137)] = '&permil;';    // Per Mille Sign
		$trans[chr(138)] = '&Scaron;';    // Latin Capital Letter S With Caron
		$trans[chr(139)] = '&lsaquo;';    // Single Left-Pointing Angle Quotation Mark
		$trans[chr(140)] = '&OElig;    ';    // Latin Capital Ligature OE
		$trans[chr(145)] = '&lsquo;';    // Left Single Quotation Mark
		$trans[chr(146)] = '&rsquo;';    // Right Single Quotation Mark
		$trans[chr(147)] = '&ldquo;';    // Left Double Quotation Mark
		$trans[chr(148)] = '&rdquo;';    // Right Double Quotation Mark
		$trans[chr(149)] = '&bull;';    // Bullet
		$trans[chr(150)] = '&ndash;';    // En Dash
		$trans[chr(151)] = '&mdash;';    // Em Dash
		$trans[chr(152)] = '&tilde;';    // Small Tilde
		$trans[chr(153)] = '&trade;';    // Trade Mark Sign
		$trans[chr(154)] = '&scaron;';    // Latin Small Letter S With Caron
		$trans[chr(155)] = '&rsaquo;';    // Single Right-Pointing Angle Quotation Mark
		$trans[chr(156)] = '&oelig;';    // Latin Small Ligature OE
		$trans[chr(159)] = '&Yuml;';    // Latin Capital Letter Y With Diaeresis
		ksort($trans);
		return $trans;
	}

	public static function rel_decodeHTML($string)
	{
		$string = strtr($string, array_flip(self::get_html_translation_table_my()));
		$string = preg_replace("/&#([0-9]+);/me", "chr('\\1')", $string);
		return $string;
	}

	public static function rel_pdfCleaner($text)
	{
		// Ugly but needed to get rid of all the stuff the PDF class cant handle
		$text = str_replace('<p>', "\n\n", $text);
		$text = str_replace('<P>', "\n\n", $text);
		$text = str_replace('<br />', "\n", $text);
		$text = str_replace('<br>', "\n", $text);
		$text = str_replace('<BR />', "\n", $text);
		$text = str_replace('<BR>', "\n", $text);
		$text = str_replace('<li>', "\n - ", $text);
		$text = str_replace('<LI>', "\n - ", $text);
		$text = str_replace('{mosimage}', '', $text);
		$text = str_replace('{mospagebreak}', '', $text);

		$text = strip_tags($text);
		$text = self::rel_decodeHTML($text);

		return $text;
	}


//for CSV import
	public static function SF_prepareImport(&$loader, &$fieldDescriptors)
	{
		$unknownFieldNames = array();
		$missingFieldNames = array();
		$requiredFieldNames = $fieldDescriptors->getRequiredFieldNames();
		$fieldNames = $loader->getFieldNames();
		foreach ($fieldNames as $k => $fieldName)
		{
			$fieldName = strtolower(trim($fieldName));
			$fieldNames[$k] = $fieldName;
			if (!$fieldDescriptors->contains($fieldName))
			{
				$unknownFieldNames[] = $fieldName;
			}
		}
		$loader->setFieldNames($fieldNames);    // set the "normalized" field names
		foreach ($requiredFieldNames as $fieldName)
		{
			if (!in_array($fieldName, $fieldNames))
			{
				$missingFieldNames[] = $fieldName;
			}
		}
		if ((count($unknownFieldNames) > 0) || (count($missingFieldNames) > 0))
		{
			return FALSE;
		}
		return TRUE;
	}

	public static function SF_prepareImportRow(&$loader, &$fieldDescriptors, $values, $requiredFieldNames, $allFieldNames)
	{
		$unknownFieldNames = array();
		$missingFieldNames = array();
		foreach ($requiredFieldNames as $fieldName)
		{
			if ((!isset($values[$fieldName])) || (trim($values[$fieldName]) == ''))
			{
				$missingFieldNames[] = $fieldName;
			}
		}
		if ((count($unknownFieldNames) > 0) || (count($missingFieldNames) > 0))
		{
			return FALSE;
		}
		foreach ($allFieldNames as $fieldName)
		{
			if (!isset($values[$fieldName]))
			{
				$defaultValue = $fieldDescriptors->getDefaultValue($fieldName);
				if ($defaultValue != '')
				{
					$values[$fieldName] = $defaultValue;
				}
			}
		}
		return TRUE;
	}

	public static function SF_showPreview($option)
	{
		require(JPATH_BASE . '/components/com_surveyforce/language/default.php');
		require_once(JPATH_BASE . '/components/com_surveyforce/helpers/generate.surveyforce.php');
		$type = mosGetParam($_REQUEST, 'type', '');

		$gg = new sf_ImageGenerator(array($type));

		$gg->width = intval(mosGetParam($_REQUEST, 'width', 600));
		$gg->height = intval(mosGetParam($_REQUEST, 'height', 250));
		$rows = array();
		$sections = array();
		$usr_answers = array();
		$tmp = null;
		$tmp->label = JText::_('COM_SF_NOT_AT_ALL');
		$tmp->percent = 0;
		$tmp->number = 10;
		$rows[] = $tmp;
		$tmp = null;
		$tmp->label = JText::_('COM_SF_PARENTS');
		$tmp->percent = 0;
		$tmp->number = 40;
		$rows[] = $tmp;
		$tmp = null;
		$tmp->label = JText::_('COM_SF_GRANDMA_GRANDPA');
		$tmp->percent = 0;
		$tmp->number = 25;
		$rows[] = $tmp;
		$tmp = null;
		$tmp->label = JText::_('COM_SF_SISTER');
		$tmp->percent = 0;
		$tmp->number = 29;
		$rows[] = $tmp;
		$tmp = null;
		$tmp->label = JText::_('COM_SF_BROTHER');
		$tmp->percent = 0;
		$tmp->number = 19;
		$rows[] = $tmp;
		$tmp = null;
		$tmp->label = JText::_('COM_SF_AUNT_UNCLE');
		$tmp->percent = 0;
		$tmp->number = 13;
		$rows[] = $tmp;

		$sections[1] = $rows;
		$titles[1] = '';
		$maintitle = JText::_('COM_SF_ARE_YOU_CLOSE_TO_ANY');
		$usr_answers[1][] = JText::_('COM_SF_PARENTS');

		$gg->clearOldImages();
		echo $gg->createImage($sections, $titles, $usr_answers, $maintitle, 50);
	}
	#######################################
	###	--- ---    IMP SCALES   --- --- ###

	public static function SF_viewIScales($option)
	{
		$database = JFactory::getDbo();
		$limit = intval(JFactory::getApplication()->getUserStateFromRequest("viewlistlimit", 'limit', 20));
		$limitstart = intval(JFactory::getApplication()->getUserStateFromRequest("viewlimitstart", 'limitstart', 0));
		if ($limit == 0) $limit = 999999;
		// get the total number of records
		$query = "SELECT COUNT(*)"
			. "\n FROM #__survey_force_iscales";
		$database->setQuery($query);
		$total = $database->loadResult();

		jimport('joomla.html.pagination');
		$pageNav = new mosPageNav($total, $limitstart, ($limit == 999999 ? 0 : $limit));

		// get the subset (based on limits) of required records
		$query = "SELECT * "
			. "\n FROM #__survey_force_iscales"
			. "\n ORDER BY iscale_name"
			. "\n LIMIT $pageNav->limitstart, $pageNav->limit";
		$database->setQuery($query);
		$rows = $database->loadObjectList();
		$i = 0;
		while ($i < count($rows))
		{
			$query = "SELECT isf_name FROM #__survey_force_iscales_fields WHERE iscale_id = '" . $rows[$i]->id . "' ORDER BY ordering";
			$database->setQuery($query);
			$isf_ar = $database->loadColumn();
			$rows[$i]->iscale_descr = implode(', ' . "\n", $isf_ar);
			$i++;
		}
		survey_force_adm_html::SF_viewIScales($rows, $pageNav, $option);
	}

	public static function SF_editIScale($id, $option)
	{
		$database = JFactory::getDbo();
		if (JFactory::getApplication()->input->get('task') == 'add_iscale_from_quest')
		{
			JFactory::getSession()->set('is_return_sf', 1);
			JFactory::getSession()->set('sf_qtext_sf', $_REQUEST['sf_qtext']);
			JFactory::getSession()->set('sf_survey_sf', mosGetParam($_REQUEST, 'sf_survey', ''));
			JFactory::getSession()->set('sf_impscale_sf', mosGetParam($_REQUEST, 'sf_impscale', ''));
			JFactory::getSession()->set('ordering_sf', mosGetParam($_REQUEST, 'ordering', ''));
			JFactory::getSession()->set('sf_compulsory_sf', mosGetParam($_REQUEST, 'sf_compulsory', ''));
			JFactory::getSession()->set('insert_pb_sf', mosGetParam($_REQUEST, 'insert_pb', ''));
			JFactory::getSession()->set('published', mosGetParam($_REQUEST, 'published', ''));
			JFactory::getSession()->set('is_likert_predefined_sf', mosGetParam($_REQUEST, 'is_likert_predefined', ''));

			JFactory::getSession()->set('sf_hid_scale_sf', mosGetParam($_REQUEST, 'sf_hid_scale', array()));
			JFactory::getSession()->set('sf_hid_scale_id_sf', mosGetParam($_REQUEST, 'sf_hid_scale_id', array()));

			JFactory::getSession()->set('sf_hid_rule_sf', mosGetParam($_REQUEST, 'sf_hid_rule', array()));
			JFactory::getSession()->set('sf_hid_rule_quest_sf', mosGetParam($_REQUEST, 'sf_hid_rule_quest', array()));
			JFactory::getSession()->set('sf_hid_rule_alt_sf', mosGetParam($_REQUEST, 'sf_hid_rule_alt', array()));
			JFactory::getSession()->set('priority_sf', mosGetParam($_REQUEST, 'priority', array()));

			JFactory::getSession()->set('sf_hid_fields_sf', mosGetParam($_REQUEST, 'sf_hid_fields', array()));
			JFactory::getSession()->set('sf_hid_field_ids_sf', mosGetParam($_REQUEST, 'sf_hid_field_ids', array()));

			JFactory::getSession()->set('sf_fields_sf', mosGetParam($_REQUEST, 'sf_fields', array()));
			JFactory::getSession()->set('sf_field_ids_sf', mosGetParam($_REQUEST, 'sf_field_ids', array()));
			JFactory::getSession()->set('sf_alt_fields_sf', mosGetParam($_REQUEST, 'sf_alt_fields', array()));
			JFactory::getSession()->set('sf_alt_field_ids_sf', mosGetParam($_REQUEST, 'sf_alt_field_ids', array()));

			JFactory::getSession()->set('other_option_cb_sf', mosGetParam($_REQUEST, 'other_option_cb', 0));
			JFactory::getSession()->set('other_option_sf', (isset($_REQUEST['other_option']) ? $_REQUEST['other_option'] : ''));
			JFactory::getSession()->set('other_op_id_sf', mosGetParam($_REQUEST, 'other_op_id', 0));

			JFactory::getSession()->set('sf_hid_rank_sf', mosGetParam($_REQUEST, 'sf_hid_rank', array()));
			JFactory::getSession()->set('sf_hid_rank_id_sf', mosGetParam($_REQUEST, 'sf_hid_rank_id', array()));

		}
		$row = new mos_Survey_Force_IScale($database);
		// load the row from the db table
		$row->load($id);

		$lists = array();

		$lists['sf_fields'] = array();
		$query = "SELECT * FROM #__survey_force_iscales_fields WHERE iscale_id = '" . $id . "' ORDER BY ordering";
		$database->setQuery($query);
		$lists['sf_fields'] = $database->loadObjectList();

		survey_force_front_html::SF_editIScale($row, $lists, $option);
	}

	public static function SF_saveIScale($option)
	{
		$post = JFactory::getApplication()->input->post;
	    $database = JFactory::getDbo();
		$row = new mos_Survey_Force_IScale($database);

        $iscale = array();
        $iscale['id'] = $post->getInt('id', 0);
        $iscale['iscale_name'] = $post->get('iscale_name', '', 'STRING');

		if (!$row->bind($iscale)) {
			echo "<script> alert('" . $row->getError() . "'); window.history.go(-1); </script>\n";
			exit();
		}

		if (!$row->check()) {
			echo "<script> alert('" . $row->getError() . "'); window.history.go(-1); </script>\n";
			exit();
		}

		if (!$row->store()) {
			echo "<script> alert('" . $row->getError() . "'); window.history.go(-1); </script>\n";
			exit();
		}

		$row->checkin();
		$is_id = $row->id;

		$query = "DELETE FROM #__survey_force_iscales_fields WHERE iscale_id = '" . $is_id . "'";
		$database->setQuery($query);
		$database->execute();
		$f_order = 0;

		foreach ($post->get('sf_hid_fields') as $f_row) {
			$new_field = new mos_Survey_Force_IScaleField($database);
			$new_field->iscale_id = $is_id;
			$new_field->isf_name = self::SF_processGetField($f_row);
			$new_field->ordering = $f_order;
			if (!$new_field->check()) {
				echo "<script> alert('" . $new_field->getError() . "'); window.history.go(-1); </script>\n";
				exit();
			}
			if (!$new_field->store()) {
				echo "<script> alert('" . $new_field->getError() . "'); window.history.go(-1); </script>\n";
				exit();
			}
			$f_order++;
		}

		if (JFactory::getApplication()->input->get('task') == 'save_iscale_A') {
			$quest_redir = intval(JFactory::getSession()->get('quest_redir'));
			$task_redir = strval(JFactory::getSession()->get('task_redir'));
			mosRedirect(SFRoute("index.php?option=com_surveyforce&task=" . $task_redir . "&id=" . $quest_redir));
		} else {
			mosRedirect(SFRoute("index.php?option=com_surveyforce&task=questions"));
		}
	}

	public static function SF_removeIscale(&$cid, $option)
	{
		$database = JFactory::getDbo();
		if (count($cid))
		{
			$cids = implode(',', $cid);
			$query = "DELETE FROM #__survey_force_iscales"
				. "\n WHERE id IN ( $cids )";
			$database->setQuery($query);
			if (!$database->execute())
			{
				echo "<script> alert('" . $database->getErrorMsg() . "'); window.history.go(-1); </script>\n";
			}
			else
			{
				$query = "DELETE FROM #__survey_force_iscales_fields WHERE iscale_id IN ( $cids )";
				$database->setQuery($query);
				$database->execute();
			}
		}
		mosRedirect("index.php?option=$option&task=iscales");
	}

	public static function SF_cancelIScale($option)
	{
        $post = JFactory::getApplication()->input->post;
        $database = JFactory::getDbo();
		$row = new mos_Survey_Force_IScale($database);

        $iscale = array();
        $iscale['id'] = $post->getInt('id', 0);
        $iscale['iscale_name'] = $post->get('iscale_name', '', 'STRING');

		$row->bind($iscale);
		$row->checkin();

		if (JFactory::getApplication()->input->get('task') == 'cancel_iscale_A') {
			$quest_redir = intval(JFactory::getSession()->get('quest_redir'));
			$task_redir = strval(JFactory::getSession()->get('task_redir'));
			mosRedirect(SFRoute("index.php?option=com_surveyforce&task=" . $task_redir . "&id=" . $quest_redir));
		} else {
			mosRedirect(SFRoute("index.php?option=com_surveyforce&task=questions"));
		}
	}

	public static function SF_ViewIRepSurv($id, $option)
	{
		$database = JFactory::getDbo();
		@set_time_limit(3600);

		$max_quest_length = 150;

		$show_iscale = intval(mosGetParam($_REQUEST, 'inc_imp', 0));
		$add_info = intval(mosGetParam($_REQUEST, 'add_info', 0));
		$query = "SELECT * FROM #__survey_force_survs WHERE id = '" . $id . "'";
		$database->setQuery($query);
		$survey_data = $database->LoadObject();
		if (isset($survey_data->id) && $survey_data->id)
		{
			$query = "SELECT sf_ust.*, sf_s.sf_name as survey_name, u.username reg_username, u.name reg_name, u.email reg_email,"
				. "\n sf_u.name as inv_name, sf_u.lastname as inv_lastname, sf_u.email as inv_email"
				. "\n FROM (#__survey_force_user_starts as sf_ust, #__survey_force_survs as sf_s)"
				. "\n LEFT JOIN #__users as u ON u.id = sf_ust.user_id and sf_ust.usertype=1"
				. "\n LEFT JOIN #__survey_force_users as sf_u ON sf_u.id = sf_ust.user_id and sf_ust.usertype=2"
				. "\n WHERE sf_ust.survey_id = sf_s.id"
				. "\n and sf_s.id = $id"
				. "\n ORDER BY sf_ust.sf_time DESC, sf_ust.id DESC";
			$database->setQuery($query);
			$rows = $database->loadObjectList();


			$query = "SELECT a.*, b.iscale_name FROM #__survey_force_quests as a LEFT JOIN #__survey_force_iscales as b ON b.id=a.sf_impscale WHERE a.published = 1 AND a.sf_survey = $id AND a.sf_qtype IN (1,2,3,4,5,6,9) ORDER BY a.ordering, a.sf_qtext";
			$database->setQuery($query);
			$sf_quests = $database->loadObjectList();

			$iii = 0;

			$t_fields = array();
			foreach ($sf_quests as $key => $sfq)
			{
				switch ($sfq->sf_qtype)
				{
					case 1:
						$query = "SELECT id, ftext FROM #__survey_force_fields WHERE quest_id = {$sfq->id} AND is_main = 1 ORDER BY ordering";
						$database->setQuery($query);
						$t_fields[$sfq->id . '1'] = $database->loadObjectList();

						$tmp_str = '';
						if (count($t_fields[$sfq->id . '1']))
							foreach ($t_fields[$sfq->id . '1'] as $field)
							{
								$tmp_str .= ',' . str_replace(',', '', self::SF_processCSVField(str_replace("\r\n", "", $sfq->sf_qtext . ' - ' . $field->ftext)));
							}
						$sf_quests[$key]->sf_qtext2 = $tmp_str;
						break;
					case 5:
					case 6:
					case 9:
						$query = "SELECT id, ftext FROM #__survey_force_fields AS a WHERE quest_id = {$sfq->id} AND is_main = 1 ORDER BY ordering";
						$database->setQuery($query);
						$t_fields[$sfq->id . '569'] = $database->loadObjectList();
						break;
				}
			}

			@ob_end_clean();
			header("Content-type: application/csv");
			header("Content-Disposition: inline; filename=report.csv");

			if ($add_info)
			{
				echo '"","","","",';
			}

			echo '"",""';


			$nnn = count($rows);

			while ($iii < $nnn)
			{

				$rows[$iii]->questions = array();

				foreach ($sf_quests as $key => $sfq)
				{
					$sf_quests[$key]->sf_qtext = trim(strip_tags($sf_quests[$key]->sf_qtext, '<a><b><i><u>'));
					$one_answer = new stdClass();
					$one_answer->quest_id = $sfq->id;
					$user_answer = '';
					if ($sfq->sf_impscale)
					{
						$query = "SELECT b.isf_name FROM #__survey_force_iscales_fields as b, #__survey_force_user_answers_imp as a"
							. "\n WHERE a.quest_id = '" . $sfq->id . "' AND a.survey_id = '" . $sfq->sf_survey . "' AND a.start_id = '" . $rows[$iii]->id . "' AND a.iscalefield_id = b.id "
							. "\n AND b.iscale_id = '" . $sfq->sf_impscale . "'";
						$database->setQuery($query);
						$user_answer = $database->loadResult();
					}
					$one_answer->sf_impscale = $sfq->sf_impscale;
					$one_answer->iscale_answer = $user_answer;
					$user_answer = '';
					switch ($sfq->sf_qtype)
					{
						case 1:
							$fields = $t_fields[$sfq->id . '1'];


							$tmp_str = '';
							foreach ($fields as $field)
							{
								$query = "SELECT b.stext as user_answer FROM #__survey_force_user_answers as a, #__survey_force_scales as b"
									. "\n WHERE a.quest_id = '" . $sfq->id . "' and b.quest_id = a.quest_id and a.answer = {$field->id} and b.id = a.ans_field  and a.survey_id = '" . $sfq->sf_survey . "' and a.start_id = '" . $rows[$iii]->id . "' ORDER BY b.ordering";
								$database->setQuery($query);
								$user_answer .= self::SF_processCSVField($database->loadResult()) . ',';

								$tmp_str .= ',' . str_replace(',', '', self::SF_processCSVField(str_replace("\r\n", "", $sfq->sf_qtext . ' - ' . $field->ftext)));
							}
							$sf_quests[$key]->sf_qtext2 = $tmp_str;
							break;
						case 5:
						case 6:
						case 9:
							$fields = $t_fields[$sfq->id . '569'];
							$user_answer = '"';
							$tmp_str = '';
							foreach ($fields as $field)
							{
								$query = "SELECT b.ftext as user_answer, c.ans_txt AS user_text  FROM (#__survey_force_user_answers as a, #__survey_force_fields as b) LEFT JOIN `#__survey_force_user_ans_txt` AS c ON a.next_quest_id = c.id "
									. "\n WHERE a.quest_id = '" . $sfq->id . "' and b.quest_id = a.quest_id and a.answer = {$field->id} and b.id = a.ans_field  and a.survey_id = '" . $sfq->sf_survey . "' and a.start_id = '" . $rows[$iii]->id . "'";
								$database->setQuery($query);

								$user_answer_ = $database->loadObjectList();
								if (isset($user_answer_[0]))
								{

									$user_answer .= $user_answer_[0]->user_answer . ($user_answer_[0]->user_text ? ' (' . str_replace(',', '', self::SF_processCSVField_noquot(str_replace("\r\n", "", $user_answer_[0]->user_text))) . ')' : '') . '","';

								}


								$tmp_str .= ',' . str_replace(',', '', self::SF_processCSVField(str_replace("\r\n", "", $sfq->sf_qtext . ' - ' . $field->ftext)));
							}
							$user_answer .= '",';
							$sf_quests[$key]->sf_qtext2 = $tmp_str;
							break;
						case 2:
							$query = "SELECT b.ftext as user_answer, c.ans_txt AS user_text  FROM (#__survey_force_user_answers as a, #__survey_force_fields as b ) LEFT JOIN `#__survey_force_user_ans_txt` AS c ON a.ans_field = c.id "
								. "\n WHERE a.quest_id = '" . $sfq->id . "' and b.quest_id = a.quest_id and b.id = a.answer and a.survey_id = '" . $sfq->sf_survey . "' and a.start_id = '" . $rows[$iii]->id . "'";
							$database->setQuery($query);
							$user_answer_ = $database->loadObjectList();
							$user_answer = '';
							if (isset($user_answer_[0]))
							{
								$user_answer = $user_answer_[0]->user_answer . ($user_answer_[0]->user_text ? ' (' . str_replace(',', '', self::SF_processCSVField_noquot(str_replace("\r\n", "", $user_answer_[0]->user_text))) . ')' : '');
							}
							break;

						case 3:
							$query = "SELECT b.ftext AS user_answer, c.ans_txt AS user_text FROM (#__survey_force_user_answers as a, #__survey_force_fields as b) LEFT JOIN `#__survey_force_user_ans_txt` AS c ON a.ans_field = c.id "
								. "\n WHERE a.quest_id = '" . $sfq->id . "' and b.quest_id = a.quest_id and b.id = a.answer and a.survey_id = '" . $sfq->sf_survey . "' and a.start_id = '" . $rows[$iii]->id . "'"
								. "\n ORDER BY b.ordering";
							$database->setQuery($query);
							$ans_inf_data = $database->loadObjectList();
							$user_answer = '';
							if (count($ans_inf_data))
							{
								foreach ($ans_inf_data as $ans_inf_data_)
								{
									$user_answer .= $ans_inf_data_->user_answer . ($ans_inf_data_->user_text ? ' (' . str_replace(',', '', self::SF_processCSVField_noquot(str_replace("\r\n", "", $ans_inf_data_->user_text))) . ')' : '') . ';';
								}
							}
							break;
						case 4:
							$n = mb_substr_count($sfq->sf_qtext, '{x}') + mb_substr_count($sfq->sf_qtext, '{y}');
							if ($n > 0)
							{
								$tmp = JText::_('COM_SF_1ST_ANSWER');
								$tmp_str = '';
								for ($i = 0; $i < $n; $i++)
								{
									if ($i == 1) $tmp = JText::_('COM_SF_SECOND_ANSWER');
									elseif ($i == 2) $tmp = JText::_('COM_SF_THIRD_ANSWER');
									elseif ($i > 2) $tmp = ($i + 1) . JText::_('COM_SF_TH_ANSWER');
									$query = "SELECT b.ans_txt as user_answer FROM #__survey_force_user_answers as a, #__survey_force_user_ans_txt as b "
										. " WHERE a.ans_field = '" . ($i + 1) . "' AND a.quest_id = '" . $sfq->id . "' and a.survey_id = '" . $sfq->sf_survey . "' and a.start_id = '" . $rows[$iii]->id . "' and a.answer = b.id";
									$database->setQuery($query);
									$user_answer .= '"' . self::SF_processCSVField_noquot($database->loadResult()) . '",';

									$tmp_str .= ',"' . mb_substr(str_replace(',', '', self::SF_processCSVField_noquot(str_replace("\r\n", "", $sfq->sf_qtext))), 0, $max_quest_length) . ' - ' . $tmp . '"';

								}
								$sf_quests[$key]->sf_qtext2 = $tmp_str;
							}
							else
							{
								$query = "SELECT b.ans_txt as user_answer FROM #__survey_force_user_answers as a, #__survey_force_user_ans_txt as b WHERE a.quest_id = '" . $sfq->id . "' and a.survey_id = '" . $sfq->sf_survey . "' and a.start_id = '" . $rows[$iii]->id . "' and a.answer = b.id";
								$database->setQuery($query);
								$user_answer = self::SF_processCSVField_noquot($database->loadResult());
								if (!$user_answer) $user_answer = '';
							}
							break;
					}
					$one_answer->answer = $user_answer;
					$one_answer->sf_qtype = (isset($sf_quests[$key]->sf_qtext2) && $sfq->sf_qtype == 4 ? 41 : $sfq->sf_qtype);

					$rows[$iii]->questions[] = $one_answer;
					unset($one_answer);

				}
				$row = $rows[$iii];

				if ($iii == 0)
				{
					foreach ($sf_quests as $i => $sfq)
					{
						if (!isset($sfq->sf_qtext2))
							echo ',' . self::SF_processCSVField(mb_substr(str_replace("\r\n", "", str_replace(',', '', $sfq->sf_qtext)), 0, $max_quest_length));
						else
							echo $sfq->sf_qtext2;
						if ($show_iscale && $sfq->sf_impscale)
						{
							echo ',' . str_replace(',', '', self::SF_processCSVField(mb_substr(str_replace("\r\n", "", $sfq->iscale_name), 0, $max_quest_length)));
						}
					}
					echo "\n";
				}

				if ($add_info)
				{
					echo '"' . $row->id . '","' . $row->sf_time . '","' . ($row->is_complete == 0 ? 'Incomplete' : 'Complete') . '",' . self::SF_processCSVField(str_replace("\r\n", "", $row->survey_name)) . ',"';
					switch ($row->usertype)
					{
						case '0':
							echo JText::_('COM_SF_GUEST') . '",';
							break;
						case '1':
							echo JText::_('COM_SF_REGISTERED_USER') . '",';
							break;
						case '2':
							echo JText::_('COM_SF_INVITED_USER') . '",';
							break;
						default:
							echo '",';
							break;
					}
				}
				else
				{
					switch ($row->usertype)
					{
						case '0':
							echo '"' . JText::_('COM_SF_GUEST') . '",';
							break;
						case '1':
							echo '"' . JText::_('COM_SF_REGISTERED_USER') . '",';
							break;
						case '2':
							echo '"' . JText::_('COM_SF_INVITED_USER') . '",';
							break;
						default:
							echo '"",';
							break;
					}
				}
				switch ($row->usertype)
				{
					case '0':
						echo '"' . JText::_('COM_SF_ANONYMOUS') . '",';
						break;
					case '1':
						echo '"' . $row->reg_username . "; " . $row->reg_name . " (" . $row->reg_email . ')",';
						break;
					case '2':
						echo '"' . $row->inv_name . " " . $row->inv_lastname . " (" . $row->inv_email . ')",';
						break;
					default:
						echo '"",';
						break;
				}

				foreach ($row->questions as $rq)
				{
					if ($rq->sf_qtype != 1 && $rq->sf_qtype != 5 && $rq->sf_qtype != 6 && $rq->sf_qtype != 9 && $rq->sf_qtype != 41)
						echo self::SF_processCSVField($rq->answer) . ",";
					else
						echo $rq->answer;
					if ($show_iscale && $rq->sf_impscale)
					{
						echo self::SF_processCSVField($rq->iscale_answer) . ",";
					}
				}
				echo "\n";

				unset($row);
				unset($rows[$iii]);
				$iii++;
			}

			unset($t_fields);

			die;
		}
		mosRedirect(SFRoute("index.php?option=com_surveyforce&task=i_report"));
	}

	public static function SF_showCrossReport($option)
	{
		$database = JFactory::getDbo();
		$survid = intval(mosGetParam($_REQUEST, 'survid', 0));

		$lists = array();

		$query = "SELECT id AS value, sf_name AS text FROM #__survey_force_survs "
			. (" WHERE sf_author = '" . JFactory::getUser()->id . "' ");
		$database->setQuery($query);
		$surveys = $database->loadObjectList();
		if (count($surveys) < 1)
		{
			$lists['surveys'] = JText::_('COM_SF_NO_SURVEYS');
			$lists['mquest_id'] = '';
			survey_force_front_html::SF_showCrossReport($lists, $option);
			return;
		}
		$survid = ($survid > 0 ? $survid : $surveys[0]->value);
		$surveys = mosHTML::selectList($surveys, 'survid', 'class="text_area" size="1" onchange="document.adminForm.submit();"', 'value', 'text', $survid);
		$lists['surveys'] = $surveys;

		$query = "SELECT id AS value, SUBSTRING(sf_qtext,1,100) AS text, sf_qtype FROM #__survey_force_quests WHERE published = 1 AND sf_qtype NOT IN (4, 7, 8) AND sf_survey = $survid ORDER BY ordering";
		$database->setQuery($query);
		$questions_tmp = $database->loadObjectList();
		$questions = array();
		if (count($questions_tmp) > 0)
		{
			foreach ($questions_tmp as $question)
			{
				if ($question->sf_qtype != 2 && $question->sf_qtype != 3)
				{
					$query = "SELECT id, ftext FROM #__survey_force_fields WHERE quest_id = {$question->value} AND is_main = 1 ORDER BY ordering";
					$database->setQuery($query);
					$fields_tmp = $database->loadObjectList();
					foreach ($fields_tmp as $field)
					{
						$tmp = new stdClass;
						$tmp->value = $question->value . '_' . $field->id;
						$tmp->text = $question->text . '  - ' . $field->ftext;
						$questions[] = $tmp;
					}
				}
				else
				{
					$tmp = new stdClass;
					$tmp->value = $question->value;
					$tmp->text = $question->text;
					$questions[] = $tmp;
				}
			}
			$lists['mquest_id'] = mosHTML::selectList($questions, 'mquest_id', 'class="text_area" size="4" ', 'value', 'text', $questions[0]->value);
		}
		else
			$lists['mquest_id'] = '';

		$query = "SELECT id AS value, SUBSTRING(sf_qtext,1,100) AS text, sf_qtype FROM #__survey_force_quests WHERE published = 1 AND sf_qtype NOT IN (7, 8) AND sf_survey = $survid ORDER BY ordering";
		$database->setQuery($query);
		$questions_tmp = $database->loadObjectList();
		$questions = array();
		if (count($questions_tmp) > 0)
		{
			foreach ($questions_tmp as $question)
			{
				if ($question->sf_qtype != 2 && $question->sf_qtype != 3 && $question->sf_qtype != 4)
				{
					$query = "SELECT id, ftext FROM #__survey_force_fields WHERE quest_id = {$question->value} AND is_main = 1 ORDER BY ordering";
					$database->setQuery($query);
					$fields_tmp = $database->loadObjectList();
					foreach ($fields_tmp as $field)
					{
						$tmp = new stdClass;
						$tmp->value = $question->value . '_' . $field->id;
						$tmp->text = $question->text . '  - ' . $field->ftext;
						$questions[] = $tmp;
					}
				}
				else
				{
					$tmp = new stdClass;
					$tmp->value = $question->value;
					$tmp->text = $question->text;
					$questions[] = $tmp;
				}
			}
		}

		$questions2 = array();
		$questions2[] = mosHTML::makeOption('0', JText::_('COM_SF_ALL_QUESTIONS'));
		$questions = @array_merge($questions2, $questions);
		$lists['cquest_id'] = mosHTML::selectList($questions, 'cquest_id[]', 'class="text_area" size="4" multiple="multiple" ', 'value', 'text', 0);
		survey_force_front_html::SF_showCrossReport($lists, $option);
	}

	public static function SF_getCrossReport($option)
	{
		$database = JFactory::getDbo();
		$survid = intval(mosGetParam($_REQUEST, 'survid', 0));
		$mquest_id = mosGetParam($_REQUEST, 'mquest_id', '');
		$cquest_id = mosGetParam($_REQUEST, 'cquest_id', array());
		$start_date = mosGetParam($_REQUEST, 'start_date', '');
		$end_date = mosGetParam($_REQUEST, 'end_date', '');

		$is_complete = intval(mosGetParam($_REQUEST, 'is_complete', 0));
		$is_notcomplete = intval(mosGetParam($_REQUEST, 'is_notcomplete', 0));
		$type = strval(mosGetParam($_REQUEST, 'rep_type', 'csv'));
		if ($survid && $mquest_id != '' && is_array($cquest_id) && (count($cquest_id) > 0) && ($is_complete || $is_notcomplete))
		{
			$date_where = '';
			if ($start_date != '' && $end_date != '')
			{
				$date_where = " AND sf_time BETWEEN '$start_date' AND '$end_date' ";
			}
			elseif ($start_date != '' && $end_date == '')
			{
				$date_where = " AND sf_time > '$start_date' ";
			}
			elseif ($start_date == '' && $end_date != '')
			{
				$date_where = " AND sf_time < '$end_date' ";
			}
			$query = "SELECT id FROM #__survey_force_user_starts "
				. "WHERE survey_id = $survid "
				. ($is_complete ? ($is_notcomplete ? '' : " AND is_complete = 1 ") : ($is_notcomplete ? " AND is_complete = 0 " : ''))
				. $date_where;
			$database->setQuery($query);
			$start_ids = $database->loadColumn();
			$m_id = intval($mquest_id);
			$f_id = 0;
			if (strpos($mquest_id, '_') > 0)
			{
				$f_id = intval(mb_substr($mquest_id, strpos($mquest_id, '_') + 1));
			}
			$query = "SELECT sf_qtype FROM #__survey_force_quests  WHERE published = 1 AND id = $m_id";
			$database->setQuery($query);
			$qtype = $database->loadResult();

			if ($qtype == 1)
			{

				if ($f_id > 0)
				{
					$query = "SELECT stext FROM #__survey_force_scales  WHERE id = $f_id ORDER BY ordering";
					$database->setQuery($query);
					$f_text = $database->loadResult();
				}
				$query = "SELECT id FROM #__survey_force_scales WHERE quest_id = $m_id ORDER BY ordering";

			}
			elseif ($qtype == 2 || $qtype == 3)
			{

				if ($f_id > 0)
				{
					$query = "SELECT ftext FROM #__survey_force_fields  WHERE id = $f_id";
					$database->setQuery($query);
					$f_text = $database->loadResult();
				}
				$query = "SELECT id FROM #__survey_force_fields WHERE quest_id = $m_id  ORDER BY ordering";
			}
			elseif ($qtype == 5 || $qtype == 6 || $qtype == 9)
			{

				if ($f_id > 0)
				{
					$query = "SELECT ftext FROM #__survey_force_fields  WHERE id = $f_id";
					$database->setQuery($query);
					$f_text = $database->loadResult();
				}
				$query = "SELECT id FROM #__survey_force_fields WHERE quest_id = $m_id AND is_main = 0 ORDER BY ordering";
			}
			$database->setQuery($query);
			$fields_ids = @array_merge($database->loadColumn(), array(0 => 0));
			$starts_by_fields = array();
			foreach ($fields_ids as $fields_id)
			{
				$query = "SELECT start_id FROM #__survey_force_user_answers WHERE start_id IN (" . implode(',', $start_ids) . ") "
					. " AND quest_id = $m_id "
					. ($qtype == 2 || $qtype == 3 ? " AND answer = $fields_id " : " AND answer = $f_id AND ans_field = $fields_id ");
				$database->setQuery($query);
				$starts_by_fields[$fields_id] = $database->loadColumn();
				if (count($starts_by_fields[$fields_id]) < 1)
					$starts_by_fields[$fields_id] = array(0);
			}

			$all_quests = false;
			if (in_array('0', $cquest_id))
			{
				$all_quests = true;
				$query = "SELECT id, sf_qtype, sf_qtext FROM #__survey_force_quests  WHERE published = 1 AND sf_survey = $survid AND sf_qtype NOT IN (7,8) ORDER BY ordering, id";
				$database->setQuery($query);
				$questions2 = $database->loadObjectList();
				$questions = array();
				foreach ($questions2 as $key => $quest)
				{
					$questions2[$key]->answer_count = 0;
					if ($quest->sf_qtype != 2 && $quest->sf_qtype != 3)
						$query = "SELECT id FROM #__survey_force_fields WHERE quest_id = {$quest->id} AND is_main = 1 ORDER BY ordering";
					else
						$query = "SELECT id FROM #__survey_force_fields WHERE quest_id = {$quest->id} ORDER BY ordering";
					$database->setQuery($query);
					$questions2[$key]->fields = @array_merge($database->loadColumn(), array(0 => 0));

					if ($quest->sf_qtype != 1 && $quest->sf_qtype != 4)
					{
						$query = "SELECT id FROM #__survey_force_fields WHERE quest_id = {$quest->id} AND is_main = 0 ORDER BY ordering";
					}
					elseif ($quest->sf_qtype == 4)
					{
						$questions2[$key]->answer_count = mb_substr_count($quest->sf_qtext, '{x}') + mb_substr_count($quest->sf_qtext, '{y}');
						$questions[$quest->id]->answer_count = $questions2[$key]->answer_count;
						if ($questions2[$key]->answer_count > 0)
						{
							$n = $questions2[$key]->answer_count;
							$questions2[$key]->fields = array();
							$a_fields = array();
							for ($i = 1; $i <= $n; $i++)
							{
								$query = "SELECT `answer` FROM `#__survey_force_user_answers` WHERE survey_id = {$survid} AND quest_id = {$quest->id} AND ans_field = {$i} AND start_id IN (" . implode(',', $start_ids) . ")";
								$database->setQuery($query);
								$ans_ids = $database->loadColumn();
								if (!is_array($ans_ids))
									$ans_ids = array();

								$query = "SELECT `ans_txt`, count( * ) num "
									. " FROM `#__survey_force_user_ans_txt` "
									. " WHERE id IN (" . implode(',', $ans_ids) . ") GROUP BY `ans_txt` ORDER BY num DESC LIMIT 0 , 10";
								$database->setQuery($query);
								$ans_txts = $database->loadObjectList();
								foreach ($ans_txts as $ans_txt)
								{
									$questions2[$key]->fields = @array_merge($questions2[$key]->fields, array(0 => $ans_txt->ans_txt));
								}

								$a_fields[] = $i;
							}
							$questions2[$key]->a_fields = $a_fields;
						}
						else
						{
							$query = "SELECT `answer` FROM `#__survey_force_user_answers` WHERE start_id IN (" . implode(',', $start_ids) . ") AND survey_id = {$survid} AND quest_id = {$quest->id} ";
							$database->setQuery($query);
							$ans_ids = $database->loadColumn();
							if (!is_array($ans_ids))
								$ans_ids = array();
							$query = "SELECT `ans_txt`, count( * ) num FROM `#__survey_force_user_ans_txt` WHERE id IN (" . implode(',', $ans_ids) . ") GROUP BY `ans_txt` ORDER BY num DESC LIMIT 0 , 10";
						}
					}
					else
					{
						$query = "SELECT id FROM #__survey_force_scales WHERE quest_id = {$quest->id} ORDER BY ordering";
					}
					$database->setQuery($query);
					if ($quest->sf_qtype == 4 && $questions2[$key]->answer_count < 1)
					{
						$ans_txts = $database->loadObjectList();
						$questions2[$key]->fields = array();
						foreach ($ans_txts as $ans_txt)
						{
							$questions2[$key]->fields = @array_merge($questions2[$key]->fields, array(0 => $ans_txt->ans_txt));
						}

						$questions2[$key]->a_fields = null;
						$questions[$quest->id]->answer_count = $questions2[$key]->answer_count;
					}
					elseif ($quest->sf_qtype != 4)
						$questions2[$key]->a_fields = @array_merge($database->loadColumn(), array(0 => 0));

					$questions[$quest->id] = $quest;
					$questions[$quest->id]->a_fields = $questions2[$key]->a_fields;
					$questions[$quest->id]->fields = $questions2[$key]->fields;
					$questions[$quest->id]->answer_count = $questions2[$key]->answer_count;
				}
			}
			else
			{
				$questions = array();
				foreach ($cquest_id as $quest)
				{
					$tmp = new stdClass;
					$tmp->answer_count = 0;
					$tmp->id = intval($quest);
					$query = "SELECT sf_qtype, sf_qtext FROM #__survey_force_quests  WHERE published = 1 AND id = {$tmp->id}";
					$database->setQuery($query);
					$n = null;
					$n = $database->loadObject();
					$tmp->sf_qtype = $n->sf_qtype;
					$tmp->sf_qtext = $n->sf_qtext;
					if ($tmp->sf_qtype != 1 && $tmp->sf_qtype != 4)
					{
						$query = "SELECT id FROM #__survey_force_fields WHERE quest_id = {$tmp->id} AND is_main = 0 ORDER BY ordering";
					}
					elseif ($tmp->sf_qtype == 4)
					{
						$tmp->answer_count = mb_substr_count($tmp->sf_qtext, '{x}') + mb_substr_count($tmp->sf_qtext, '{y}');
						if ($tmp->answer_count > 0)
						{
							$n = $tmp->answer_count;
							$tmp->fields = array();
							$a_fields = array();
							for ($i = 1; $i <= $n; $i++)
							{
								$query = "SELECT `answer` FROM `#__survey_force_user_answers` WHERE survey_id = {$survid} AND quest_id = {$tmp->id} AND ans_field = {$i} AND start_id IN (" . implode(',', $start_ids) . ")";
								$database->setQuery($query);
								$ans_ids = $database->loadColumn();
								if (!is_array($ans_ids))
									$ans_ids = array();

								$query = "SELECT `ans_txt`, count( * ) num "
									. " FROM `#__survey_force_user_ans_txt` "
									. " WHERE id IN (" . implode(',', $ans_ids) . ") GROUP BY `ans_txt` ORDER BY num DESC LIMIT 0 , 10";
								$database->setQuery($query);
								$ans_txts = $database->loadObjectList();
								foreach ($ans_txts as $ans_txt)
								{
									$tmp->fields = @array_merge($tmp->fields, array(0 => $ans_txt->ans_txt));
								}

								$a_fields[] = $i;
							}
							$tmp->a_fields = $a_fields;
						}
						else
						{
							$query = "SELECT `answer` FROM `#__survey_force_user_answers` WHERE quest_id = {$tmp->id} AND start_id IN (" . implode(',', $start_ids) . ")";
							$database->setQuery($query);
							$ans_ids = $database->loadColumn();
							if (!is_array($ans_ids))
								$ans_ids = array();
							$query = "SELECT `ans_txt`, count( * ) num FROM `#__survey_force_user_ans_txt` WHERE id IN (" . implode(',', $ans_ids) . ") GROUP BY `ans_txt` ORDER BY num DESC LIMIT 0 , 10";
						}
					}
					else
					{
						$query = "SELECT id FROM #__survey_force_scales WHERE quest_id = {$tmp->id} ORDER BY ordering";
					}
					$database->setQuery($query);
					if ($tmp->sf_qtype == 4 && $tmp->answer_count < 1)
					{
						$ans_txts = $database->loadObjectList();
						$tmp->fields = array();
						foreach ($ans_txts as $ans_txt)
						{
							$tmp->fields = @array_merge($tmp->fields, array(0 => $ans_txt->ans_txt));
						}

						$tmp->a_fields = null;
					}
					elseif ($tmp->sf_qtype != 4)
					{
						$tmp->a_fields = @array_merge($database->loadColumn(), array(0 => 0));
						if (strpos($quest, '_') > 0)
						{
							$tmp->fields = array(0 => 0, 1 => intval(mb_substr($quest, strpos($quest, '_') + 1)));
						}
						else
						{
							$query = "SELECT id FROM #__survey_force_fields WHERE quest_id = {$tmp->id} AND is_main = 1";
							$database->setQuery($query);
							$tmp->fields = @array_merge($database->loadColumn(), array(0 => 0));
						}
						foreach ($questions as $key => $question)
						{
							if ($question->id == $tmp->id)
							{
								$questions[$key]->fields = @array_merge($tmp->fields, $questions[$key]->fields);
								$tmp = null;
								break;
							}
						}
					}

					if ($tmp != null)
						$questions[$tmp->id] = $tmp;
				}
			}
			$result_data = array();
			foreach ($questions as $question)
			{
				$tmp = array();
				foreach ($fields_ids as $fields_id)
				{
					if ($question->sf_qtype == 2 || $question->sf_qtype == 3)
					{
						$query = "SELECT answer FROM #__survey_force_user_answers "
							. " WHERE start_id IN (" . implode(',', $starts_by_fields[$fields_id]) . ") "
							. " AND quest_id = {$question->id} "
							. " AND answer IN (" . implode(',', $question->fields) . ") ";
					}
					elseif ($question->sf_qtype == 4)
					{
						if ($question->answer_count > 0)
						{
							$query = "SELECT a.ans_txt, b.ans_field FROM #__survey_force_user_ans_txt AS a LEFT JOIN #__survey_force_user_answers AS b ON b.answer = a.id AND b.quest_id = {$question->id}"
								. " WHERE a.start_id IN (" . implode(',', $starts_by_fields[$fields_id]) . ") "
								. " AND a.ans_txt IN ('" . implode("', '", $question->fields) . "') ORDER BY b.ans_field";
						}
						else
						{
							$query = "SELECT ans_txt FROM #__survey_force_user_ans_txt "
								. " WHERE start_id IN (" . implode(',', $starts_by_fields[$fields_id]) . ") "
								. " AND ans_txt IN ('" . implode("', '", $question->fields) . "') ";
						}
					}
					else
					{
						$query = "SELECT answer, ans_field FROM #__survey_force_user_answers "
							. " WHERE start_id IN (" . implode(',', $starts_by_fields[$fields_id]) . ") "
							. " AND quest_id = {$question->id} "
							. ($question->sf_qtype == 1 ? " AND ans_field IN (" . implode(',', $question->a_fields) . ") " : " AND answer IN (" . implode(',', $question->fields) . ") ");
					}
					$database->setQuery($query);
					if ($question->sf_qtype == 2 || $question->sf_qtype == 3)
					{
						$t = array_count_values($database->loadColumn());
						$tmp[$fields_id] = array();
						foreach ($question->fields as $f_id)
						{
							$tmp[$fields_id][$f_id] = isset($t[$f_id]) ? $t[$f_id] : 0;
						}
					}
					elseif ($question->sf_qtype == 4)
					{
						if ($question->answer_count > 0)
						{
							$tmp_data = $database->loadObjectList();
							$t_fields = array();
							foreach ($tmp_data as $data)
							{
								if (!isset($t_fields[$data->ans_field]))
									$t_fields[$data->ans_field] = array();
								$t_fields[$data->ans_field][] = $data->ans_txt;
							}

							foreach ($t_fields as $key => $data)
							{
								$t_fields[$key] = array_count_values($data);
							}

							$tmp[$fields_id] = $t_fields;
						}
						else
						{
							$t = array_count_values($database->loadColumn());
							$tmp[$fields_id] = array();
							foreach ($question->fields as $f_id)
							{
								$tmp[$fields_id][$f_id] = isset($t[$f_id]) ? $t[$f_id] : 0;
							}
						}
					}
					else
					{
						$tmp_data = $database->loadObjectList();
						$t_fields = array();
						foreach ($tmp_data as $data)
						{
							if (!isset($t_fields[$data->answer]))
								$t_fields[$data->answer] = array();
							$t_fields[$data->answer][] = $data->ans_field;
						}

						foreach ($t_fields as $key => $data)
						{
							$t_fields[$key] = array_count_values($data);
						}

						foreach ($t_fields as $key => $data)
						{
							foreach ($question->a_fields as $af_id)
							{
								$t_fields[$key][$af_id] = isset($t_fields[$key][$af_id]) ? $t_fields[$key][$af_id] : 0;
							}
						}

						$tmp[$fields_id] = $t_fields;
					}
				}
				if ($question->sf_qtype == 2 || $question->sf_qtype == 3 || $question->sf_qtype == 4)
				{
					if ($question->sf_qtype == 4 && $question->answer_count > 0)
					{
						$t = array();
						foreach ($fields_ids as $fields_id2)
						{
							foreach ($tmp[$fields_id2] as $f_id => $fields)
							{
								foreach ($fields as $af_id => $count)
								{
									foreach ($fields_ids as $fields_id)
									{
										$t[$f_id][$af_id][$fields_id] = isset($tmp[$fields_id][$f_id][$af_id]) ? $tmp[$fields_id][$f_id][$af_id] : '0';
									}
								}
							}
						}
					}
					else
					{
						$t = array();
						foreach ($question->fields as $f_id)
						{
							$t[$f_id] = array();
							foreach ($fields_ids as $fields_id)
							{
								$t[$f_id][$fields_id] = $tmp[$fields_id][$f_id];
							}
						}
					}
				}
				else
				{
					$t = array();
					foreach ($question->fields as $f_id)
					{
						foreach ($question->a_fields as $af_id)
						{
							foreach ($fields_ids as $fields_id)
							{
								$t[$f_id][$af_id][$fields_id] = isset($tmp[$fields_id][$f_id][$af_id]) ? $tmp[$fields_id][$f_id][$af_id] : '0';
							}
						}
					}
				}

				$result_data[$question->id] = $t;
			}
			if ($type == 'pdf')
			{
				chdir(JPATH_BASE);
				/*
			 * Create the pdf document
			 */

				require_once(_SURVEY_FORCE_ADMIN_HOME . '/assets/tcpdf/sf_pdf.php');

				$pdf_doc = new sf_pdf();

				$pdf = &$pdf_doc->_engine;

				$pdf->getAliasNbPages();
				$pdf->AddPage();

				$pdf->SetFont('dejavusans');
				$fontFamily = $pdf->getFontFamily();

				$query = "SELECT  sf_qtext   FROM #__survey_force_quests  WHERE published = 1 AND id = {$m_id}";
				$database->setQuery($query);
				$main_quest = $pdf_doc->cleanText($database->loadResult() . (isset($f_text) ? " - $f_text\n" : "\n"));
				$start_key = 'dummy';
				reset($result_data);

				for ($ij = 0, $nm = count($result_data); $ij < $nm; $ij++)
				{
					if ($start_key == 'dummy')
						list($key, $data) = each($result_data);
					$cur_y = $pdf->GetY();


					if ($cur_y > 240)
						$pdf->AddPage();

					$pdf->SetX(60);
					$pdf->SetFontSize(8);
					$pdf->setFont($fontFamily, 'B');
					$pdf->setFont($fontFamily, 'I');
					$pdf->MultiCell(0, 0, $main_quest, 0, 'J', 0, 1, 0, 0, true, 0);
					$pdf->Ln(0.5);

					$query = "SELECT  sf_qtext   FROM #__survey_force_quests  WHERE published = 1 AND id = {$key}";
					$database->setQuery($query);

					$quest = $pdf_doc->cleanText($database->loadResult()) . "\n";
					$pdf->setFont($fontFamily, 'I');
					$pdf->MultiCell(60, 0, $quest, 0, 'J', 0, 1, 0, 0, true, 0);
					$pdf->Ln(0.5);

					$cur_y = $pdf->GetY();
					$col_width = 130 / (count($fields_ids) + 1);
					$pdf->SetFontSize(6);

					$pdf->SetX(60);
					$pdf->MultiCell($col_width, 0, "Total", 0, 'C', 0, 1, 0, 0, true, 0);

					$i = 1;
					$line_y = 10000;
					foreach ($fields_ids as $fields_id)
					{
						$query = "SELECT ftext FROM #__survey_force_fields WHERE id = {$fields_id}";
						if ($qtype == 1)
						{
							$query = "SELECT stext FROM #__survey_force_scales WHERE id = {$fields_id} ORDER BY ordering";
						}
						$database->setQuery($query);
						$tt = $pdf_doc->cleanText($database->loadResult());
						if ($fields_id == 0)
							$tt = JText::_('COM_SURVEYFORCE_NO_ANSWER');
						$pdf->SetY($cur_y);
						$pdf->SetX(60 + $col_width * $i);
						$pdf->MultiCell($col_width, 0, $tt, 0, 'C', 0, 1, 0, 0, true, 0);
						$i++;
					}

					$pdf->line(60, $pdf->GetY() + 2, 200, $pdf->GetY() + 2);
					$pdf->Ln();
					$pdf->setFont($fontFamily, 'B');
					if ($questions[$key]->sf_qtype == 2 || $questions[$key]->sf_qtype == 3)
					{
						$total_row = array('total' => 0);
						$cur_y2 = $pdf->GetY();
						foreach ($data as $k => $item)
						{
							$pdf->setFontSize(4);
							$query = "SELECT ftext FROM #__survey_force_fields WHERE id = {$k}";
							$database->setQuery($query);
							$tt = $pdf_doc->cleanText($database->loadResult());
							if ($k == 0)
								$tt = JText::_('COM_SURVEYFORCE_NO_ANSWER');
							$total_col = 0;

							$pdf->SetY($cur_y2);
							$cur_y = $pdf->GetY();
							$pdf->SetY($cur_y);
							$pdf->SetX(17);
							$pdf->MultiCell(40, 0, $tt . "\n", 0, 'J', 0, 1, 0, 0, true, 0);
							$pdf->Ln(0.5);
							$cur_y2 = $pdf->GetY();

							$pdf->SetY($cur_y);
							$i = 1;
							foreach ($fields_ids as $fields_id)
							{
								if ($cur_y > $pdf->getPageHeight() - 20)
								{
									$cur_y = 15;
								}
								$pdf->SetY($cur_y);
								$pdf->SetX(60 + $col_width * $i);
								$pdf->MultiCell($col_width, 0, "{$item[$fields_id]}", 0, 'C', 0, 1, 0, 0, true, 0);

								$total_col = $total_col + $item[$fields_id];
								if (!isset($total_row[$fields_id]))
									$total_row[$fields_id] = 0;
								$total_row[$fields_id] = $total_row[$fields_id] + $item[$fields_id];
								$i++;
							}
							$total_row['total'] = $total_row['total'] + $total_col;
							$pdf->SetY($cur_y);
							$pdf->SetX(60);
							$pdf->MultiCell($col_width, 0, "{$total_col}", 0, 'C', 0, 1, 0, 0, true, 0);
						}

						$cur_y = $pdf->GetY() + 10;
						$pdf->SetY($cur_y);
						$pdf->setFontSize(6);
						$pdf->line(60, $pdf->GetY() + 2, 200, $pdf->GetY() + 2);
						$pdf->Ln();
						$cur_y = $pdf->GetY();
						$pdf->SetX(30);
						$pdf->MultiCell(20, 0, "Totals", 0, 'R', 0, 1, 0, 0, true, 0);

						$pdf->SetY($cur_y);
						$pdf->SetX(60);
						$pdf->MultiCell($col_width, 0, "{$total_row['total']}", 0, 'C', 0, 1, 0, 0, true, 0);
						$i = 1;
						foreach ($fields_ids as $fields_id)
						{
							$pdf->SetY($cur_y);
							$pdf->SetX(60 + $col_width * $i);
							$pdf->MultiCell($col_width, 0, "{$total_row[$fields_id]}", 0, 'C', 0, 1, 0, 0, true, 0);
							$i++;
						}
					}
					elseif ($questions[$key]->sf_qtype == 4)
					{
						if ($questions[$key]->answer_count > 0)
						{
							foreach ($data as $nn => $itemz)
							{
								$tmp = '';
								if ($nn == 1) $tmp = JText::_('COM_SURVEYFORCE_COM_SF_FIRST_ANSWER');
								if ($nn == 2) $tmp = JText::_('COM_SURVEYFORCE_COM_SF_SECOND_ANSWER');
								if ($nn == 3) $tmp = JText::_('COM_SURVEYFORCE_COM_SF_THIRD_ANSWER');
								if ($nn > 3) $tmp = $nn . JText::_('COM_SURVEYFORCE_COM_SF_X_ANSWER');

								$pdf_doc->cleanText($tmp);
								$pdf->SetX(18);
								$pdf->MultiCell(42, 0, $tmp . "\n", 0, 'J', 0, 1, 0, 0, true, 0);

								$total_row = array('total' => 0);
								$cur_y2 = $pdf->GetY();
								foreach ($itemz as $k => $item)
								{
									$tt = $pdf_doc->cleanText($k);

									if ($k === 0)
										$tt = JText::_('COM_SURVEYFORCE_NO_ANSWER');
									$total_col = 0;

									if ($cur_y2 > 240)
									{
										$pdf->AddPage();
										$cur_y2 = $pdf->GetY();
									}

									$pdf->SetY($cur_y2);
									$cur_y = $pdf->GetY();
									$pdf->SetY($cur_y);
									$pdf->SetX(17);
									$pdf->MultiCell(40, 0, $tt . "\n", 0, 'J', 0, 1, 0, 0, true, 0);
									$pdf->Ln(0.5);
									$cur_y2 = $pdf->GetY();

									$i = 1;
									foreach ($fields_ids as $fields_id)
									{
										$pdf->SetY($cur_y);
										$pdf->SetX(60 + $col_width * $i);
										$pdf->MultiCell($col_width, 0, "{$item[$fields_id]}", 0, 'C', 0, 1, 0, 0, true, 0);
										$total_col = $total_col + $item[$fields_id];
										if (!isset($total_row[$fields_id]))
											$total_row[$fields_id] = 0;
										$total_row[$fields_id] = $total_row[$fields_id] + $item[$fields_id];
										$i++;
									}
									$total_row['total'] = $total_row['total'] + $total_col;
									$pdf->SetY($cur_y);
									$pdf->SetX(60);
									$pdf->MultiCell($col_width, 0, "{$total_col}", 0, 'C', 0, 1, 0, 0, true, 0);
								}
								$pdf->line(60, $pdf->GetY() + 2, 200, $pdf->GetY() + 2);
								$pdf->Ln();
								$cur_y = $pdf->GetY();
								$pdf->SetX(30);
								$pdf->MultiCell(20, 0, "Totals", 0, 'R', 0, 1, 0, 0, true, 0);

								$pdf->SetY($cur_y);
								$pdf->SetX(60);
								$pdf->MultiCell($col_width, 0, "{$total_row['total']}", 0, 'C', 0, 1, 0, 0, true, 0);

								$i = 1;
								foreach ($fields_ids as $fields_id)
								{
									$pdf->SetY($cur_y);
									$pdf->SetX(60 + $col_width * $i);
									$pdf->MultiCell($col_width, 0, "{$total_row[$fields_id]}", 0, 'C', 0, 1, 0, 0, true, 0);
									$i++;
								}
							}
						}
						else
						{
							$total_row = array('total' => 0);
							$cur_y2 = $pdf->GetY();
							foreach ($data as $k => $item)
							{
								$tt = $pdf_doc->cleanText($k);

								if ($k === 0)
									$tt = JText::_('COM_SURVEYFORCE_NO_ANSWER');
								$total_col = 0;

								if ($cur_y2 > 240)
								{
									$pdf->AddPage();
									$cur_y2 = $pdf->GetY();
								}

								$pdf->SetY($cur_y2);
								$cur_y = $pdf->GetY();
								$pdf->SetX(17);
								$pdf->MultiCell(40, 0, $tt . "\n", 0, 'J', 0, 1, 0, 0, true, 0);
								$pdf->Ln(0.5);
								$cur_y2 = $pdf->GetY();

								$i = 1;
								foreach ($fields_ids as $fields_id)
								{
									$pdf->SetY($cur_y);
									$pdf->SetX(60 + $col_width * $i);
									$pdf->MultiCell($col_width, 0, "{$item[$fields_id]}", 0, 'C', 0, 1, 0, 0, true, 0);

									$total_col = $total_col + $item[$fields_id];
									if (!isset($total_row[$fields_id]))
										$total_row[$fields_id] = 0;
									$total_row[$fields_id] = $total_row[$fields_id] + $item[$fields_id];
									$i++;
								}
								$total_row['total'] = $total_row['total'] + $total_col;
								$pdf->SetY($cur_y);
								$pdf->SetX(60);
								$pdf->MultiCell($col_width, 0, "{$total_col}", 0, 'C', 0, 1, 0, 0, true, 0);

							}
							$pdf->SetY($cur_y2);
							$pdf->line(60, $pdf->GetY() + 2, 200, $pdf->GetY() + 2);
							$pdf->Ln();
							$cur_y = $pdf->GetY();

							$pdf->SetX(30);
							$pdf->MultiCell(20, 0, "Totals", 0, 'R', 0, 1, 0, 0, true, 0);

							$pdf->SetY($cur_y);
							$pdf->SetX(60);
							$pdf->MultiCell($col_width, 0, "{$total_row['total']}", 0, 'C', 0, 1, 0, 0, true, 0);

							$i = 1;
							foreach ($fields_ids as $fields_id)
							{
								$pdf->SetY($cur_y);
								$pdf->SetX(60 + $col_width * $i);
								$pdf->MultiCell($col_width, 0, "{$total_row[$fields_id]}", 0, 'C', 0, 1, 0, 0, true, 0);
								$i++;
							}
						}
					}
					else
					{
						foreach ($data as $k => $item)
						{
							$total_row = array('total' => 0);
							$query = "SELECT ftext FROM #__survey_force_fields WHERE id = {$k}";
							$database->setQuery($query);
							$tt = $database->loadResult();
							if ($k == 0)
								continue;

							if ($pdf->GetY() > 240)
							{
								$pdf->AddPage();
							}

							$tt = $pdf_doc->cleanText($tt);
							$pdf->SetX(17);
							$pdf->MultiCell(42, 0, $tt . "\n", 0, 'J', 0, 1, 0, 0, true, 0);
							$cur_y2 = $pdf->GetY();

							foreach ($item as $kk => $it)
							{
								$query = "SELECT ftext FROM #__survey_force_fields WHERE id = {$kk}";
								if ($questions[$key]->sf_qtype == 1)
								{
									$query = "SELECT stext  FROM #__survey_force_scales WHERE id = {$kk} ORDER BY ordering";
								}
								$database->setQuery($query);
								$tt = $pdf_doc->cleanText($database->loadResult());
								if ($kk == 0)
									$tt = ($questions[$key]->sf_qtype == 9 ? JText::_('COM_SF_NO_RANK') : JText::_('COM_SURVEYFORCE_NO_ANSWER'));

								if ($cur_y2 > 240)
								{
									$pdf->AddPage();
									$cur_y2 = $pdf->GetY();
								}

								$pdf->SetY($cur_y2);
								$cur_y = $pdf->GetY();
								$pdf->SetY($cur_y);
								$pdf->SetX(20);
								$pdf->MultiCell(40, 0, $tt . "\n", 0, 'J', 0, 1, 0, 0, true, 0);
								$pdf->Ln(0.5);
								$cur_y2 = $pdf->GetY();

								$total_col = 0;
								$i = 1;
								foreach ($fields_ids as $fields_id)
								{
									$pdf->SetY($cur_y);
									$pdf->SetX(60 + $col_width * $i);
									$pdf->MultiCell($col_width, 0, "{$it[$fields_id]}", 0, 'C', 0, 1, 0, 0, true, 0);

									$total_col = $total_col + $it[$fields_id];
									if (!isset($total_row[$fields_id]))
										$total_row[$fields_id] = 0;
									$total_row[$fields_id] = $total_row[$fields_id] + $it[$fields_id];
									$i++;
								}
								$total_row['total'] = $total_row['total'] + $total_col;
								$pdf->SetY($cur_y);
								$pdf->SetX(60);
								$pdf->MultiCell($col_width, 0, "{$total_col}", 0, 'C', 0, 1, 0, 0, true, 0);
							}
							$pdf->line(60, $pdf->GetY() + 2, 200, $pdf->GetY() + 2);
							$pdf->Ln();
							$cur_y = $pdf->GetY();

							$pdf->SetX(30);
							$pdf->MultiCell(20, 0, "Totals", 0, 'R', 0, 1, 0, 0, true, 0);

							$pdf->SetY($cur_y);
							$pdf->SetX(60);
							$pdf->MultiCell($col_width, 0, "{$total_row['total']}", 0, 'C', 0, 1, 0, 0, true, 0);

							$i = 1;
							foreach ($fields_ids as $fields_id)
							{
								$pdf->SetY($cur_y);
								$pdf->SetX(60 + $col_width * $i);
								$pdf->MultiCell($col_width, 0, "{$total_row[$fields_id]}", 0, 'C', 0, 1, 0, 0, true, 0);
								$i++;
							}
						}
					}
					$pdf->line(15, $pdf->GetY() + 2, 200, $pdf->GetY() + 2);
					$pdf->Ln();
					$pdf->Ln();
				}

				$data = $pdf->Output('', 'S');
				@ob_end_clean();
				header("Content-type: application/pdf");
				header("Content-Length: " . strlen(ltrim($data)));
				header("Content-Disposition: attachment; filename=report.pdf");
				echo $data;
			}
			else
			{
				$csv_data = "";
				$z = ',';
				$query = "SELECT  sf_qtext   FROM #__survey_force_quests  WHERE published = 1 AND id = {$m_id}";
				$database->setQuery($query);
				$main_quest = SF_processPDFField($database->loadResult()) . (isset($f_text) ? " - $f_text" : '');
				foreach ($result_data as $key => $data)
				{
					$csv_data .= $z . $main_quest . "\n";
					$query = "SELECT  sf_qtext   FROM #__survey_force_quests  WHERE published = 1 AND id = {$key}";
					$database->setQuery($query);
					$csv_data .= SF_processPDFField($database->loadResult()) . "\n";
					$csv_data .= "{$z}Total";
					foreach ($fields_ids as $fields_id)
					{
						$query = "SELECT ftext FROM #__survey_force_fields WHERE id = {$fields_id}";
						if ($qtype == 1)
						{
							$query = "SELECT stext FROM #__survey_force_scales WHERE id = {$fields_id} ORDER BY ordering";
						}
						$database->setQuery($query);
						$tt = SF_processPDFField($database->loadResult());
						if ($fields_id == 0)
							$tt = JText::_('COM_SURVEYFORCE_NO_ANSWER');
						$csv_data .= "{$z}{$tt}";
					}
					$csv_data .= "\n";
					if ($questions[$key]->sf_qtype == 2 || $questions[$key]->sf_qtype == 3)
					{
						$total_row = array('s' => 0);
						foreach ($data as $k => $item)
						{
							$query = "SELECT ftext FROM #__survey_force_fields WHERE id = {$k}";
							$database->setQuery($query);
							$tt = SF_processPDFField($database->loadResult());
							if ($k == 0)
								$tt = JText::_('COM_SURVEYFORCE_NO_ANSWER');
							$ech = '';
							$total_col = 0;

							foreach ($fields_ids as $fields_id)
							{
								$ech .= "{$z}" . $item[$fields_id];
								$total_col = $total_col + $item[$fields_id];
								if (!isset($total_row[$fields_id]))
									$total_row[$fields_id] = 0;
								$total_row[$fields_id] = $total_row[$fields_id] + $item[$fields_id];
							}
							$total_row['s'] = $total_row['s'] + $total_col;
							$csv_data .= "$tt{$z}$total_col" . $ech . "\n";
						}
						$ech = '';
						foreach ($fields_ids as $fields_id)
						{
							$ech .= "{$z}" . $total_row[$fields_id];
						}
						$csv_data .= "Total{$z}{$total_row['s']}" . $ech . "\n";
					}
					elseif ($questions[$key]->sf_qtype == 4)
					{
						if ($questions[$key]->answer_count > 0)
						{
							foreach ($data as $nn => $itemz)
							{
								$tmp = '';
								if ($nn == 1) $tmp = JText::_('COM_SF_1ST_ANSWER');
								if ($nn == 2) $tmp = JText::_('COM_SF_SECOND_ANSWER');
								if ($nn == 3) $tmp = JText::_('COM_SF_THIRD_ANSWER');
								if ($nn > 3) $tmp = $nn . JText::_('COM_SF_TH_ANSWER');
								$csv_data .= "$tmp\n";
								$total_row = array('s' => 0);
								foreach ($itemz as $k => $item)
								{
									$tt = SF_processPDFField($k);
									if ($k === 0)
										$tt = JText::_('COM_SURVEYFORCE_NO_ANSWER');
									$ech = '';
									$total_col = 0;

									foreach ($fields_ids as $fields_id)
									{
										$ech .= "{$z}" . $item[$fields_id];
										$total_col = $total_col + $item[$fields_id];
										if (!isset($total_row[$fields_id]))
											$total_row[$fields_id] = 0;
										$total_row[$fields_id] = $total_row[$fields_id] + $item[$fields_id];
									}
									$total_row['s'] = $total_row['s'] + $total_col;
									$csv_data .= "$tt{$z}$total_col" . $ech . "\n";
								}
								$ech = '';
								foreach ($fields_ids as $fields_id)
								{
									$ech .= "{$z}" . $total_row[$fields_id];
								}
								$csv_data .= "Total{$z}{$total_row['s']}" . $ech . "\n";
							}
						}
						else
						{
							$total_row = array('s' => 0);
							foreach ($data as $k => $item)
							{
								$tt = SF_processPDFField($k);
								if ($k === 0)
									$tt = JText::_('COM_SURVEYFORCE_NO_ANSWER');
								$ech = '';
								$total_col = 0;

								foreach ($fields_ids as $fields_id)
								{
									$ech .= "{$z}" . $item[$fields_id];
									$total_col = $total_col + $item[$fields_id];
									if (!isset($total_row[$fields_id]))
										$total_row[$fields_id] = 0;
									$total_row[$fields_id] = $total_row[$fields_id] + $item[$fields_id];
								}
								$total_row['s'] = $total_row['s'] + $total_col;
								$csv_data .= "$tt{$z}$total_col" . $ech . "\n";
							}
							$ech = '';
							foreach ($fields_ids as $fields_id)
							{
								$ech .= "{$z}" . $total_row[$fields_id];
							}
							$csv_data .= "Total{$z}{$total_row['s']}" . $ech . "\n";
						}
					}
					else
					{
						foreach ($data as $k => $item)
						{
							$total_row = array('s' => 0);
							$query = "SELECT ftext FROM #__survey_force_fields WHERE id = {$k}";
							$database->setQuery($query);
							$tt = SF_processPDFField($database->loadResult());
							if ($k == 0)
								continue;

							$csv_data .= "$tt\n";
							foreach ($item as $kk => $it)
							{
								$query = "SELECT ftext FROM #__survey_force_fields WHERE id = {$kk}";
								if ($questions[$key]->sf_qtype == 1)
								{
									$query = "SELECT stext  FROM #__survey_force_scales WHERE id = {$kk} ORDER BY ordering";
								}
								$database->setQuery($query);
								$tt = SF_processPDFField($database->loadResult());
								if ($kk == 0)
									$tt = JText::_('COM_SURVEYFORCE_NO_ANSWER');
								$ech = '';
								$total_col = 0;

								foreach ($fields_ids as $fields_id)
								{
									$ech .= "{$z}" . $it[$fields_id];
									$total_col = $total_col + $it[$fields_id];
									if (!isset($total_row[$fields_id]))
										$total_row[$fields_id] = 0;
									$total_row[$fields_id] = $total_row[$fields_id] + $it[$fields_id];
								}
								$total_row['s'] = $total_row['s'] + $total_col;
								$csv_data .= "$tt{$z}$total_col" . $ech . "\n";
							}
							$ech = '';
							foreach ($fields_ids as $fields_id)
							{
								$ech .= "{$z}" . $total_row[$fields_id];
							}
							$csv_data .= "Total{$z}{$total_row['s']}" . $ech . "\n";
						}
					}
					$csv_data .= "\n\n";
				}
				$filedata = SF_processField($csv_data);
				@ob_end_clean();
				header("Content-type: application/csv");
				header("Content-Length: " . strlen(ltrim($filedata)));
				header("Content-Disposition: attachment; filename=report.csv");
				echo $filedata;
			}
			exit;
		}
		else
		{
			echo "<script> alert('" . JText::_('COM_SF_YOU_NOT_SPECIFY_ENOUGH_DATA') . "'); window.history.go(-1); </script>\n";
			exit();
		}
	}

	public function SF_processField($field_text)
	{
		$field_text = ampReplace($field_text);
		$field_text = str_replace('&quot;', '"', $field_text);
		$field_text = str_replace('&#039;', "'", $field_text);
		$field_text = str_replace('&#39;', "'", $field_text);
		return trim($field_text);
	}

	public function SF_processPDFField($field_text, $allowed_tags = '')
	{
		$field_text = strip_tags($field_text, $allowed_tags);
		$field_text = self::rel_pdfCleaner($field_text);
		$field_text = str_replace('&quot;', '"', $field_text);
		$field_text = str_replace('&#039;', "'", $field_text);
		$field_text = str_replace('&#39;', "'", $field_text);
		return trim($field_text);
	}

	public static function SF_viewUsers($option)
	{
		$database = JFactory::getDbo();
		$listid = intval(JFactory::getApplication()->getUserStateFromRequest("list_id", 'list_id', 0));
		$limit = intval(JFactory::getApplication()->getUserStateFromRequest("viewlistlimit", 'limit', 20));
		$limitstart = intval(JFactory::getApplication()->getUserStateFromRequest("viewlimitstart", 'limitstart', 0));
		if ($limit == 0) $limit = 999999;
		$listid = intval(mosGetParam($_REQUEST, 'list_id', JFactory::getSession()->get('list_list_id', 0)));
		JFactory::getSession()->set('list_list_id', $listid);
		$limit = intval(mosGetParam($_REQUEST, 'limit', JFactory::getSession()->get('list_limit', JFactory::getApplication()->getCfg('list_limit'))));
		if ($limit == 0) $limit = 999999;
		JFactory::getSession()->set('list_limit', $limit);
		$limitstart = intval(mosGetParam($_REQUEST, 'limitstart', 0));

		// get the total number of records
		$query = "SELECT COUNT(*)"
			. "\n FROM #__survey_force_users WHERE list_id = '" . $listid . "'";
		$database->setQuery($query);
		$total = $database->loadResult();

		jimport('joomla.html.pagination');
		$pageNav = new SFPageNav($total, $limitstart, $limit);

		// get the subset (based on limits) of required records
		$query = "SELECT * "
			. "\n FROM #__survey_force_users WHERE list_id = '" . $listid . "'"
			. "\n ORDER BY name, lastname, email "
			. "\n LIMIT $pageNav->limitstart, $pageNav->limit";
		$database->setQuery($query);
		$rows = $database->loadObjectList();
		$query = "SELECT listname FROM #__survey_force_listusers WHERE id = '" . $listid . "'";
		$database->setQuery($query);
		$lists['listname'] = $database->loadResult();
		$lists['listid'] = $listid;
		$javascript = 'onchange="document.adminForm.submit();"';
		$query = "SELECT id AS value, listname AS text"
			. "\n FROM #__survey_force_listusers"
			. (JFactory::getUser()->get('usertype') != 'Super Administrator' ? " WHERE  sf_author_id = '" . JFactory::getUser()->id . "' " : '')
			. "\n ORDER BY listname";
		$database->setQuery($query);
		$userlists = array();
		$userlists = @array_merge($userlists, $database->loadObjectList());
		$userlist = mosHTML::selectList($userlists, 'list_id', 'class="text_area" size="1" ' . $javascript, 'value', 'text', $listid);
		$lists['userlists'] = $userlist;

		survey_force_front_html::SF_show_Users($rows, $lists, $pageNav, $option);

	}

	public static function SF_ListEmails($option)
	{
		$database = JFactory::getDbo();
		$limit = intval(JFactory::getApplication()->getUserStateFromRequest("viewlistlimit", 'limit', 20));
		$limitstart = intval(JFactory::getApplication()->getUserStateFromRequest("viewlimitstart", 'limitstart', 0));
		$limit = intval(mosGetParam($_REQUEST, 'limit', JFactory::getSession()->get('list_limit', JFactory::getApplication()->getCfg('list_limit'))));
		$limit = (!$limit ? 999999 : $limit);

		JFactory::getSession()->set('list_limit', $limit);
		$limitstart = intval(mosGetParam($_REQUEST, 'limitstart', 0));

		// get the total number of records
		$query = "SELECT COUNT(*)"
			. "\n FROM #__survey_force_emails"
			. (" WHERE user_id ='" . JFactory::getUser()->id . "'");
		$database->setQuery($query);
		$total = $database->loadResult();

		jimport('joomla.html.pagination');
		$pageNav = new SFPageNav($total, $limitstart, $limit);

		// get the subset (based on limits) of required records
		$query = "SELECT * "
			. "\n FROM #__survey_force_emails"
			. (" WHERE user_id ='" . JFactory::getUser()->id . "' ")
			. "\n ORDER BY email_subject"
			. "\n LIMIT $pageNav->limitstart, $pageNav->limit";
		$database->setQuery($query);
		$rows = $database->loadObjectList();

		survey_force_front_html::SF_showEmailsList($rows, $pageNav, $option);
	}

	public static function SF_editEmail($id, $option)
	{
		$database = JFactory::getDbo();
		$row = new mos_Survey_Force_Email($database);
		$row->load($id);

		if ($id)
		{
			// do stuff for existing records
			$row->checkout(JFactory::getUser()->id);
		}
		else
		{
			// do stuff for new records
			$row->email_reply = JFactory::getUser()->email;
			$row->published = 1;
		}
		$lists = array();

		survey_force_front_html::SF_editEmail($row, $lists, $option);

	}

	public static function SF_saveEmail($option)
	{
        $post = JFactory::getApplication()->input->post;
        $database = JFactory::getDbo();
		$row = new mos_Survey_Force_Email($database);

        $email = array();
        $email['id'] = $post->getInt('id', 0);
        $email['email_subject'] = $post->get('email_subject', '', 'STRING');
        $email['email_body'] = $post->get('email_body', '', 'STRING');
        $email['email_reply'] = $post->get('email_reply', '', 'STRING');
        $email['user_id'] = $post->getInt('user_id', 0);

		if (!$row->bind($email)) {
			echo "<script> alert('" . $row->getError() . "'); window.history.go(-1); </script>\n";
			exit();
		}

		if (!$row->check()) {
			echo "<script> alert('" . $row->getError() . "'); window.history.go(-1); </script>\n";
			exit();
		}

		if (!$row->store()) {
			echo "<script> alert('" . $row->getError() . "'); window.history.go(-1); </script>\n";
			exit();
		}

		$row->checkin();

		if (JFactory::getSession()->get('task') == 'apply_email') {
			mosRedirect(SFRoute("index.php?option=com_surveyforce&task=editA_email&id=" . $row->id));
		} else {
			mosRedirect(SFRoute("index.php?option=com_surveyforce&task=emails"));
		}
	}

	public static function SF_removeEmail(&$cid, $option)
	{
		$database = JFactory::getDbo();
		if (count($cid))
		{
			$cids = implode(',', $cid);
			$query = "DELETE FROM #__survey_force_emails"
				. "\n WHERE id IN ( $cids )";
			$database->setQuery($query);
			if (!$database->execute())
			{
				echo "<script> alert('" . $database->getErrorMsg() . "'); window.history.go(-1); </script>\n";
			}
		}
		mosRedirect(SFRoute("index.php?option=com_surveyforce&task=emails"));
	}

	public static function SF_cancelEmail($option)
	{
        $post = JFactory::getApplication()->input->post;
        $database = JFactory::getDbo();
		$row = new mos_Survey_Force_Email($database);

        $email = array();
        $email['id'] = $post->getInt('id', 0);
        $email['email_subject'] = $post->get('email_subject', '', 'STRING');
        $email['email_body'] = $post->get('email_body', '', 'STRING');
        $email['email_reply'] = $post->get('email_reply', '', 'STRING');
        $email['user_id'] = $post->getInt('user_id', 0);

		$row->bind($email);
		$row->checkin();
		mosRedirect(SFRoute("index.php?option=com_surveyforce&task=emails"));
	}

	public static function SF_inviteUsers($id, $option)
	{
		$database = JFactory::getDbo();
		$row = new mos_Survey_Force_ListUsers($database);
		// load the row from the db table
		$row->load($id);

		$lists = array();

		$query = "SELECT id AS value, email_subject AS text"
			. "\n FROM #__survey_force_emails "
			. ("\n WHERE user_id = '" . JFactory::getUser()->id . "' ")
			. "\n ORDER BY email_subject";
		$database->setQuery($query);
		$email_lists = $database->loadObjectList();
		$email_list = mosHTML::selectList($email_lists, 'email_id', 'class="text_area" size="1" ', 'value', 'text', 0);
		$lists['email_list'] = $email_list;
		survey_force_front_html::SF_inviteUsers($row, $lists, $option);
	}

	public static function SF_remindUsers($id, $option)
	{
		$database = JFactory::getDbo();
		$row = new mos_Survey_Force_ListUsers($database);
		// load the row from the db table
		$row->load($id);

		$lists = array();
		$query = "SELECT id AS value, email_subject AS text"
			. "\n FROM #__survey_force_emails "
			. ("\n WHERE user_id = '" . JFactory::getUser()->id . "' ")
			. "\n ORDER BY email_subject";
		$database->setQuery($query);
		$email_lists = $database->loadObjectList();
		$email_list = mosHTML::selectList($email_lists, 'email_id', 'class="text_area" size="1" ', 'value', 'text', 0);
		$lists['email_list'] = $email_list;
		survey_force_front_html::SF_remindUsers($row, $lists, $option);
	}

	public static function SF_startInvitation($option)
	{
		$database = JFactory::getDbo();
		$sf_config = JComponentHelper::getParams('com_surveyforce');
		$mail_pause = intval($sf_config->get('sf_mail_pause'));
		$mail_count = intval($sf_config->get('sf_mail_count'));
		$mail_max = intval($sf_config->get('sf_mail_maximum'));
		ignore_user_abort(false); // STOP script if User press 'STOP' button
		@set_time_limit(0);
		@ob_end_clean();
		@ob_start();
		echo "<script>function getObj_frame(name) {"
			. " if (parent.document.getElementById) { return parent.document.getElementById(name); }"
			. "	else if (parent.document.all) { return parent.document.all[name]; }"
			. "	else if (parent.document.layers) { return parent.document.layers[name]; }}</script>";

        $list_id = \JFactory::getApplication()->input->getInt('list', 0);
        $email_id = \JFactory::getApplication()->input->getInt('email', 0);

		$query = "SELECT * FROM #__survey_force_emails WHERE id ='" . $email_id . "'";
		$database->setQuery($query);
		$Send_email = $database->loadObjectList();

		$query = "SELECT count(*) FROM `#__survey_force_users` WHERE `list_id`= '" . $list_id . "' AND `is_invited` = 0 ";
		$database->setQuery($query);
		$is_invited = intval($database->loadResult());

		$query = "SELECT survey_id FROM #__survey_force_listusers WHERE id = '" . $list_id . "'";
		$database->setQuery($query);
		$survey_id = intval($database->loadResult());

		if ($is_invited < 1)
		{
			echo "<script>var div_log = getObj_frame('div_invite_log_txt'); if (div_log) {"
				. "div_log.innerHTML = '" . JText::_('COM_SF_ALL_USERS_FROM_THE_FOLLOWING_LIST') . "';"
				. "}</script>";
			@flush();
			@ob_end_flush();
			die();
		}

		$query = "SELECT count(*) FROM #__survey_force_users WHERE list_id ='" . $list_id . "'";
		$database->setQuery($query);
		$Users_count = $database->loadResult();
		$query = "SELECT * FROM #__survey_force_users WHERE list_id ='" . $list_id . "' and is_invited = '0'";
		$database->setQuery($query);
		$UsersList = $database->loadObjectList();
		$Users_to_invite = count($UsersList);
		#$message_header 	= sprintf( 'Invitation for Survey on site ', JFactory::getApplication()->getCfg('sitename') );
		$message = $Send_email[0]->email_body;
		$subject = JFactory::getApplication()->getCfg('sitename') . ' / ' . stripslashes($Send_email[0]->email_subject);
		$email_reply = $Send_email[0]->email_reply;
		$ii = 1;

		$query = "UPDATE #__survey_force_listusers SET is_invited = '2', date_invited = '" . date('Y-m-d H:i:s') . "' WHERE id ='" . $list_id . "'";
		$database->setQuery($query);
		$database->execute();
		$send_count = 0;
		$counter = 0;
		foreach ($UsersList as $user_row)
		{
			if ($mail_max && $send_count == $mail_max)
			{
				echo "<script>var st_but = getObj_frame('Start_button');"
					. "var div_log_txt = getObj_frame('div_invite_log_txt');"
					. "st_but.value = 'Resume';"
					. " if (div_log_txt) {"
					. "div_log_txt.innerHTML = '" . JText::_('COM_SF_MAXIMUM_NUMBER_MAILS_EXCEED') . "';"
					. "}"
					. "</script>";
				@flush();
				@ob_flush();
				die;
			}
			$url = JFactory::getURi()->root();
			$user_invite_num = md5(uniqid(rand(), true));
			$link = ' <a href="' . $url . "/index.php?option=com_surveyforce&task=start_invited&survey=" . $survey_id . "&invite=" . $user_invite_num . '">' . $url . "/index.php?option=com_surveyforce&task=start_invited&survey=" . $survey_id . "&invite=" . $user_invite_num . '</a>';
			$user_name = ' ' . $user_row->name . ' ' . $user_row->lastname . ' ';
			$message_user = str_replace('#link#', $link, $message);
			$message_user = str_replace('#name#', $user_name, $message_user);

			$query = "INSERT INTO #__survey_force_invitations (invite_num, user_id, inv_status) VALUES ('" . $user_invite_num . "', '" . $user_row->id . "', 0)";
			$database->setQuery($query);
			$database->execute();
			$user_invite_id = $database->insertid();
			
			mosMail($email_reply, JFactory::getApplication()->getCfg('fromname'), $user_row->email, $subject, nl2br($message_user), 1); //1 - in HTML mode

			$query = "UPDATE #__survey_force_users SET is_invited = '1', invite_id = '" . $user_invite_id . "' WHERE id ='" . $user_row->id . "'";
			$database->setQuery($query);
			$database->execute();
			if (($mail_pause && $mail_count) && $counter == ($mail_count - 1))
			{
				$counter = -1;
				for ($jj = $mail_pause; $jj > 1; $jj--)
				{
					echo "<script>var div_log = getObj_frame('div_invite_log');"
						. "var div_log_txt = getObj_frame('div_invite_log_txt');"
						. " if (div_log) {"
						. "div_log.innerHTML = '" . intval(($ii - $Users_to_invite + $Users_count) * 100 / $Users_count) . "%';"
						. "div_log.style.width = '" . intval(($ii - $Users_to_invite + $Users_count) * 600 / $Users_count) . "px';"
						. "}"
						. " if (div_log_txt) {"
						. "div_log_txt.innerHTML =  '" . ($ii - $Users_to_invite + $Users_count) . JText::_('COM_SF_USERS_INVITED_PAUSE') . ' '. "$jj" . ' '. JText::_('COM_SF_SECONDS') . "';"
						. "}"
						. "</script>";
					@flush();
					@ob_flush();
					sleep(1);
				}
			}
			else
			{
				echo "<script>var div_log = getObj_frame('div_invite_log');"
					. "var div_log_txt = getObj_frame('div_invite_log_txt');"
					. " if (div_log) {"
					. "div_log.innerHTML = '" . intval(($ii - $Users_to_invite + $Users_count) * 100 / $Users_count) . "%';"
					. "div_log.style.width = '" . intval(($ii - $Users_to_invite + $Users_count) * 600 / $Users_count) . "px';"
					. "}"
					. " if (div_log_txt) {"
					. "div_log_txt.innerHTML = '" . ($ii - $Users_to_invite + $Users_count) . JText::_('COM_SF_USERS_INVITED') . "';"
					. "}"
					. "</script>";
				@flush();
				@ob_flush();
			}
			$ii++;
			$send_count++;
			$counter++;
			sleep(1);
		}
		$query = "UPDATE #__survey_force_listusers SET is_invited = '1' WHERE id ='" . $list_id . "'";
		$database->setQuery($query);
		$database->execute();
		echo "<script>var div_log = getObj_frame('div_invite_log'); if (div_log) {"
			. "div_log.innerHTML = '100%';"
			. "div_log.style.width = '600px';"
			. "}</script>";
		@flush();
		@ob_end_flush();

		die();
	}

	public static function SF_startRemind($option)
	{
		$jinput = \JFactory::getApplication()->input;
	    $database = JFactory::getDbo();
		$sf_config = JComponentHelper::getParams('com_surveyforce');
		$mail_pause = intval($sf_config->get('sf_mail_pause'));
		$mail_count = intval($sf_config->get('sf_mail_count'));
		$mail_max = intval($sf_config->get('sf_mail_maximum'));
		ignore_user_abort(false); // STOP script if User press 'STOP' button
		@set_time_limit(0);
		@ob_end_clean();
		@ob_start();
		echo "<script>function getObj_frame(name) {"
			. " if (parent.document.getElementById) { return parent.document.getElementById(name); }"
			. "	else if (parent.document.all) { return parent.document.all[name]; }"
			. "	else if (parent.document.layers) { return parent.document.layers[name]; }}</script>";

        $list_id = $jinput->getInt('list', 0);
        $email_id = $jinput->getInt('email', 0);

		$query = "SELECT * FROM #__survey_force_emails WHERE id ='" . $email_id . "'";
		$database->setQuery($query);
		$Send_email = $database->loadObjectList();

		$query = "SELECT is_invited, survey_id FROM #__survey_force_listusers WHERE id = '" . $list_id . "'";
		$database->setQuery($query);
		$list_data = $database->loadObjectList();

		$is_invited = $list_data[0]->is_invited;
		$survey_id = $list_data[0]->survey_id;

		$query = "SELECT count(a.id) FROM #__survey_force_users as a, #__survey_force_invitations as b WHERE a.list_id ='" . $list_id . "' and a.is_reminded = 0 and a.invite_id = b.id and b.inv_status = 0";
		$database->setQuery($query);
		$Users_count = $database->loadResult();
		$query = "SELECT a.* FROM #__survey_force_users as a, #__survey_force_invitations as b WHERE a.list_id ='" . $list_id . "' and a.is_reminded = 0 and a.invite_id = b.id and b.inv_status = 0";
		$database->setQuery($query);
		$UsersList = $database->loadObjectList();
		$Users_to_remind = count($UsersList);

		#$message_header 	= sprintf( 'Invitation for Survey on site ', JFactory::getApplication()->getCfg('sitename') );
		$message = $Send_email[0]->email_body;
		$subject = JFactory::getApplication()->getCfg('sitename') . ' / ' . stripslashes($Send_email[0]->email_subject);
		$email_reply = $Send_email[0]->email_reply;
		$ii = 1;

		$query = "UPDATE #__survey_force_listusers SET date_remind = '" . date('Y-m-d H:i:s') . "' WHERE id ='" . $list_id . "'";
		$database->setQuery($query);
		$database->execute();
		$send_rem = 0;
		$counter = 0;
		foreach ($UsersList as $user_row)
		{
			if ($mail_max && $send_rem == $mail_max)
			{
				echo "<script>var st_but = getObj_frame('Start_button');"
					. "var div_log_txt = getObj_frame('div_invite_log_txt');"
					. "st_but.value = 'Resume';"
					. " if (div_log_txt) {"
					. "div_log_txt.innerHTML = '" . JText::_('COM_SF_MAXIMUM_NUMBER_MAILS_EXCEED') . "';"
					. "}"
					. "</script>";
				@flush();
				@ob_flush();
				die;
			}
			$query = "SELECT invite_num FROM #__survey_force_invitations WHERE id = '" . $user_row->invite_id . "'";
			$database->setQuery($query);
			$user_invite_num = $database->loadResult();
			$link = '<a href="' . JFactory::getApplication()->getCfg('sitename') . "/index.php?option=com_surveyforce&task=start_invited&survey=" . $survey_id . "&invite=" . $user_invite_num . '">' . JFactory::getApplication()->getCfg('sitename') . "/index.php?option=com_surveyforce&task=start_invited&survey=" . $survey_id . "&invite=" . $user_invite_num . '</a>';
			$user_name = ' ' . $user_row->name . ' ' . $user_row->lastname . ' ';
			$message_user = str_replace('#link#', $link, $message);
			$message_user = str_replace('#name#', $user_name, $message_user);

			mosMail($email_reply, JFactory::getApplication()->getCfg('fromname'), $user_row->email, $subject, nl2br($message_user), 1); //1 - in HTML mode
			$query = "UPDATE #__survey_force_users SET is_reminded = '1' WHERE id ='" . $user_row->id . "'";
			$database->setQuery($query);
			$database->execute();
			if (($mail_pause && $mail_count) && $counter == ($mail_count - 1))
			{
				$counter = -1;
				for ($jj = $mail_pause; $jj > 1; $jj--)
				{
					echo "<script>var div_log = getObj_frame('div_invite_log');"
						. "var div_log_txt = getObj_frame('div_invite_log_txt');"
						. " if (div_log) {"
						. "div_log.innerHTML = '" . intval(($ii) * 100 / $Users_count) . "%';"
						. "div_log.style.width = '" . intval(($ii) * 600 / $Users_count) . "px';"
						. "}"
						. " if (div_log_txt) {"
						. "div_log_txt.innerHTML =  '" . ($ii) . ' '. JText::_('COM_SF_USERS_REMINDED_PAUSE') . ' '. "$jj" . ' '. JText::_('COM_SF_SECONDS') . "';"
						. "}"
						. "</script>";
					@flush();
					@ob_flush();
					sleep(1);
				}
			}
			else
			{
				echo "<script>var div_log = getObj_frame('div_invite_log');"
					. "var div_log_txt = getObj_frame('div_invite_log_txt');"
					. " if (div_log) {"
					. "div_log.innerHTML = '" . intval(($ii) * 100 / $Users_count) . "%';"
					. "div_log.style.width = '" . intval(($ii) * 600 / $Users_count) . "px';"
					. "}"
					. " if (div_log_txt) {"
					. "div_log_txt.innerHTML = '" . ' '. ($ii) . ' ' . JText::_('COM_SF_USERS_REMINDED') . "';"
					. "}"
					. "</script>";
				@flush();
				@ob_flush();
				sleep(1);
			}
			$ii++;
			$send_rem++;
			$counter++;
		}
		$query = "UPDATE #__survey_force_users SET is_reminded = '0' WHERE list_id ='" . $list_id . "'";
		$database->setQuery($query);
		$database->execute();

		echo "<script>var div_log = getObj_frame('div_invite_log'); if (div_log) {"
			. "div_log.innerHTML = '100%';"
			. "div_log.style.width = '600px';"
			. "}</script>";
		@flush();
		@ob_end_flush();

		die();
	}

	public static function SF_cancelSurvey($option)
	{
        $post = JFactory::getApplication()->input->post;
	    $database = JFactory::getDbo();
		$row = new mos_Survey_Force_Survey($database);

        $query = "SHOW COLUMNS FROM `#__survey_force_survs`";
        $database->setQuery($query);
        $fields = $database->loadColumn();

        $survey = array();
        if(!empty($fields)) {
            foreach($fields as $field) {
                $survey[$field] = $post->get($field, null, 'STRING');
            }
        }

		$row->bind($survey);
		$row->checkin();

		mosRedirect(SFRoute("index.php?option=com_surveyforce") . '?task=surveys');
	}

	public static function SF_changeSurvey($option, $cid = null, $state = 0)
	{
		$database = JFactory::getDbo();
		if ((is_array($cid) && count($cid) > 0))
		{
			for ($i = 0, $n = count($cid); $i < $n; $i++)
			{
				if (self::SF_GetUserType($cid[$i]) != 1)
					unset($cid[$i]);
			}
			if (!is_array($cid) || count($cid) < 1)
			{
				mosRedirect(SFRoute("index.php?option=com_surveyforce&task=surveys"));
			}
		}

		$cids = implode(',', $cid);

		$query = "UPDATE #__survey_force_survs"
			. "\n SET published = " . intval($state)
			. "\n WHERE id IN ( $cids )";
		$database->setQuery($query);
		if (!$database->execute())
		{
			echo "<script> alert('" . $database->getErrorMsg() . "'); window.history.go(-1); </script>\n";
			exit();
		}

		mosRedirect(SFRoute("index.php?option=com_surveyforce&task=surveys"));
	}

	public static function SF_saveSurvey($option)
	{
        $post = JFactory::getApplication()->input->post;
	    $database = JFactory::getDbo();
		$row = new mos_Survey_Force_Survey($database);

        $query = "SHOW COLUMNS FROM `#__survey_force_survs`";
        $database->setQuery($query);
        $fields = $database->loadColumn();

        $survey = array();
        if(!empty($fields)) {
            foreach($fields as $field) {
                $survey[$field] = $post->get($field, null, 'STRING');
            }
        }
        $survey['sf_name'] = self::SF_processGetField(@$post->get('sf_name'));

		if (!$row->bind($survey)) {
			echo "<script> alert('" . $row->getError() . "'); window.history.go(-1); </script>\n";
			exit();
		}

		if ($row->sf_special) {
			$userlists = mosGetParam($_REQUEST, 'userlists', array());
			if (count($userlists) > 0) {
				$row->sf_special = implode(',', $userlists);
			}
		}

		if (!$row->check()) {
			echo "<script> alert('" . $row->getError() . "'); window.history.go(-1); </script>\n";
			exit();
		}

		if (!$row->store()) {
			echo "<script> alert('" . $row->getError() . "'); window.history.go(-1); </script>\n";
			exit();
		}

		$row->checkin();
		self::updateJSRules($row->id);
		#$row->updateOrder();

		if (JFactory::getApplication()->input->get('task') == 'apply_surv') {
			mosRedirect(SFRoute("index.php?option=com_surveyforce") . '?task=editA_surv&id=' . $row->id);
		} else {
			mosRedirect(SFRoute("index.php?option=com_surveyforce&task=surveys&catid={$row->sf_cat}"));
		}
	}

	public static function SF_removeSurvey(&$cid, $option)
	{
		$database = JFactory::getDbo();
		if ((is_array($cid) && count($cid) > 0))
		{
			for ($i = 0, $n = count($cid); $i < $n; $i++)
			{
				if (self::SF_GetUserType($cid[$i]) != 1)
					unset($cid[$i]);
			}
			if (!is_array($cid) || count($cid) < 1)
			{
				mosRedirect(SFRoute("index.php?option=com_surveyforce&task=surveys"));
			}
		}
		if (count($cid))
		{
			$cids = implode(',', $cid);
			$query = "DELETE FROM #__survey_force_survs"
				. "\n WHERE id IN ( $cids )";
			$database->setQuery($query);
			if (!$database->execute())
			{
				echo "<script> alert('" . $database->getErrorMsg() . "'); window.history.go(-1); </script>\n";
			}

			$query = "SELECT id FROM #__survey_force_quests WHERE sf_survey IN ( $cids ) ";
			$database->setQuery($query);
			$qids = $database->loadColumn();
			if (count($qids))
			{
				$sec = array();
				self::SF_removeQuestion($qids, $sec, $option, true);
			}
		}
		mosRedirect(SFRoute("index.php?option=com_surveyforce&task=surveys"));
	}

	public static function SF_removeQuestion(&$cid, &$sec, $option, $no_redirect = false)
	{
		$database = JFactory::getDbo();
		if (count($cid))
		{
			$cids = implode(',', $cid);
			$query = "DELETE FROM #__survey_force_quests"
				. "\n WHERE id IN ( $cids )";
			$database->setQuery($query);
			if (!$database->execute())
			{
				echo "<script> alert('" . $database->getErrorMsg() . "'); window.history.go(-1); </script>\n";
			}
			else
			{
				$query = "DELETE FROM #__survey_force_fields WHERE quest_id IN ( $cids )";
				$database->setQuery($query);
				$database->execute();

				$query = "DELETE FROM #__survey_force_scales WHERE quest_id IN ( $cids )";
				$database->setQuery($query);
				$database->execute();

				$query = "DELETE FROM #__survey_force_quest_show WHERE quest_id IN ( $cids )";
				$database->setQuery($query);
				$database->execute();
			}
		}
		if (count($sec))
		{
			$secs = implode(',', $sec);
			$query = "DELETE FROM #__survey_force_qsections"
				. "\n WHERE id IN ( $secs )";
			$database->setQuery($query);
			if (!$database->execute())
			{
				echo "<script> alert('" . $database->getErrorMsg() . "'); window.history.go(-1); </script>\n";
			}
		}
		if ($no_redirect) return;

		mosRedirect(SFRoute("index.php?option=com_surveyforce&task=questions"));
	}

	public static function SF_moveSurveySelect($option, $cid)
	{
		$database = JFactory::getDbo();
		if (!is_array($cid) || count($cid) < 1)
		{
			echo "<script> alert('Select an item to move'); window.history.go(-1);</script>\n";
			exit;
		}

		## query to list selected surveys
		$cids = implode(',', $cid);
		$query = "SELECT a.sf_name, b.sf_catname"
			. "\n FROM #__survey_force_survs AS a LEFT JOIN #__survey_force_cats AS b ON b.id = a.sf_cat"
			. "\n WHERE a.id IN ( $cids )";
		$database->setQuery($query);
		$items = $database->loadObjectList();

		## query to choose category to move to
		$query = "SELECT a.sf_catname AS text, a.id AS value"
			. "\n FROM #__survey_force_cats AS a"
			. "\n ORDER BY a.sf_catname";
		$database->setQuery($query);
		$categories = $database->loadObjectList();

		// build the html select list
		$CategoryList = mosHTML::selectList($categories, 'categorymove', 'class="text_area" size="10"', 'value', 'text', null);
		survey_force_front_html::SF_moveSurvey_Select($option, $cid, $CategoryList, $items);
	}

	public static function SF_copySurveySave($cid)
	{
		$database = JFactory::getDbo();
		$categoryMove = strval(mosGetParam($_REQUEST, 'categorymove', ''));

		$cids = implode(',', $cid);
		$total = count($cid);

		$query = "SELECT * FROM #__survey_force_survs WHERE id IN ( $cids )";
		$database->setQuery($query);
		$survs_to_copy = $database->loadAssocList();
		foreach ($survs_to_copy as $surv2copy)
		{
			$new_surv = new mos_Survey_Force_Survey($database);
			if (!$new_surv->bind($surv2copy))
			{
				echo "<script> alert('" . $new_surv->getError() . "'); window.history.go(-1); </script>\n";
				exit();
			}
			$new_surv->id = 0;
			$new_surv->sf_cat = $categoryMove;
			$new_surv->sf_name = 'Copy of ' . $new_surv->sf_name;
			if (!$new_surv->check())
			{
				echo "<script> alert('" . $new_surv->getError() . "'); window.history.go(-1); </script>\n";
				exit();
			}
			if (!$new_surv->store())
			{
				echo "<script> alert('" . $new_surv->getError() . "'); window.history.go(-1); </script>\n";
				exit();
			}
			$new_surv_id = $new_surv->id;
			$query = "SELECT id FROM #__survey_force_quests WHERE sf_survey = '" . $surv2copy['id'] . "' ORDER BY ordering, id";
			$database->setQuery($query);
			$cid = $database->loadColumn();
			if (!is_array($cid))
			{
				$cid = array(0);
			}
			$query = "SELECT id FROM #__survey_force_qsections WHERE sf_survey_id = '" . $surv2copy['id'] . "' ORDER BY ordering, id";
			$database->setQuery($query);
			$sec = $database->loadColumn();
			if (!is_array($sec))
			{
				$sec = array();
			}

			self::SF_copyQuestionSave($cid, 1, $new_surv_id, $sec);
		}
		$categoryNew = new mos_Survey_Force_Cat ($database);
		$categoryNew->load($categoryMove);

		$msg = $total . JText::_('COM_SF_SURVEYS_INCLUDING_ALL_QUESIONS') . $categoryNew->sf_catname;
		mosRedirect(SFRoute("index.php?option=com_surveyforce&task=surveys"));
	}

	public static function SF_copyQuestionSave($cid, $run_from_surv_copy = 0, $surveyMove = 0, $sec = array())
	{
		$database = JFactory::getDbo();
		$total = 0;
		$rules_data = array();
		$rules_count = 0;
		$copy_rules = 0;//only in 'copy quest' mode (not for 'copy survey' mode)
		if (!$run_from_surv_copy)
		{
			$surveyMove = intval(mosGetParam($_REQUEST, 'surveymove', 0));
		}

		if (count($sec))
		{
			$new_sec_id = array();
			foreach ($sec as $s_id)
			{
				$row = new mos_Survey_Force_Sections($database);
				$row->load($s_id);
				$row->id = 0;
				$row->sf_survey_id = $surveyMove;
				if (!$row->store())
				{
					echo "<script> alert('" . $row->getError() . "'); window.history.go(-1); </script>\n";
					exit();
				}
				$new_sec_id[$s_id] = $row->id;
				$row->checkin();
			}
		}

		$cids = implode(',', $cid);
		$total = count($cid);
		$query = "SELECT * FROM #__survey_force_quests WHERE id IN ( $cids ) ORDER BY ordering, id";
		$database->setQuery($query);
		$quests_to_copy = $database->loadAssocList();
		$query = "SELECT MAX(ordering) FROM #__survey_force_quests WHERE sf_survey = {$surveyMove}";
		$database->setQuery($query);
		$new_order = (int) $database->loadResult() + 1;
		$quests_ids_map = array();
		$scales_ids_map = array();
		$fields_ids_map = array();
		$fields2_ids_map = array();
		$altfields_ids_map = array();
		if ($total > 0)
		{
			foreach ($quests_to_copy as $quest2copy)
			{
				$old_quest_id = $quest2copy['id'];

				if (!$run_from_surv_copy)
				{
					$rules_data = array();
				}
				$new_quest = new mos_Survey_Force_Question($database);
				if (!$new_quest->bind($quest2copy))
				{
					echo "<script> alert('" . $new_quest->getError() . "'); window.history.go(-1); </script>\n";
					exit();
				}
				if ($new_quest->sf_survey == $surveyMove)
				{
					$copy_rules = 1;
				}
				else
				{
					$copy_rules = 0;
				}
				$new_quest->id = 0;
				$new_quest->ordering = $new_order;
				$new_quest->sf_survey = $surveyMove;
				$new_quest->sf_rule = 0;
				$new_quest->sf_section_id = (@$new_sec_id[$new_quest->sf_section_id] ? $new_sec_id[$new_quest->sf_section_id] : 0);
				if ($run_from_surv_copy)
				{
					$new_order++;
				}
				if (!$new_quest->check())
				{
					echo "<script> alert('" . $new_quest->getError() . "'); window.history.go(-1); </script>\n";
					exit();
				}
				if (!$new_quest->store())
				{
					echo "<script> alert('" . $new_quest->getError() . "'); window.history.go(-1); </script>\n";
					exit();
				}

				$new_quest_id = $new_quest->id;
				$quests_ids_map[$old_quest_id] = $new_quest_id;


				if (($quest2copy['sf_qtype'] == 1) || ($quest2copy['sf_qtype'] == 2) || ($quest2copy['sf_qtype'] == 3))
				{
					$doing_rule = 0;
					if ($run_from_surv_copy && ($quest2copy['sf_qtype'] == 2))
					{
						$query = "SELECT count(*) FROM #__survey_force_rules WHERE quest_id = '" . $old_quest_id . "'";
						$database->setQuery($query);
						$c_rules = $database->loadResult();
						if ($c_rules)
						{
							$query = "SELECT * FROM #__survey_force_rules WHERE quest_id = '" . $old_quest_id . "'";
							$database->setQuery($query);
							$q_rules = $database->loadObjectList();
							foreach ($q_rules as $q_rule)
							{
								$new_rule = new stdClass();
								$new_rule = $q_rule;
								$new_rule->id = 0;
								$new_rule->quest_id = $new_quest_id;
								$new_rule->is_ready = 0;
								$rules_data[$rules_count] = $new_rule;
								$rules_count++;
								$doing_rule = 1;
							}
						}
					}
					elseif (($copy_rules) && (!$run_from_surv_copy) && ($quest2copy['sf_qtype'] == 2))
					{
						$rules_data = array();
						$query = "SELECT * FROM #__survey_force_rules WHERE quest_id = '" . $old_quest_id . "'";
						$database->setQuery($query);
						$q_rules = $database->loadObjectList();
						foreach ($q_rules as $q_rule)
						{
							$new_rule = new stdClass();
							$new_rule->id = 0;
							$new_rule->quest_id = $new_quest_id;
							$new_rule->next_quest_id = $q_rule->next_quest_id;
							$new_rule->answer_id = $q_rule->answer_id;
							$new_rule->is_ready = 0;
							$rules_data[] = $new_rule;
							$doing_rule = 1;
						}
					}
					$query = "SELECT * FROM #__survey_force_fields WHERE quest_id = '" . $old_quest_id . "'";
					$database->setQuery($query);
					$fields_to_copy = $database->loadAssocList();
					foreach ($fields_to_copy as $field2copy)
					{
						$new_field = new mos_Survey_Force_Field($database);
						if (!$new_field->bind($field2copy))
						{
							echo "<script> alert('" . $new_field->getError() . "'); window.history.go(-1); </script>\n";
							exit();
						}
						$old_field_id = $new_field->id;
						$new_field->id = 0;
						//$new_quest->ordering = 0;
						$new_field->quest_id = $new_quest_id;
						if (!$new_field->check())
						{
							echo "<script> alert('" . $new_field->getError() . "'); window.history.go(-1); </script>\n";
							exit();
						}
						if (!$new_field->store())
						{
							echo "<script> alert('" . $new_field->getError() . "'); window.history.go(-1); </script>\n";
							exit();
						}
						$fields_ids_map[$old_field_id] = $new_field->id;

						if ($run_from_surv_copy && $doing_rule)
						{
							$i = 0;
							while ($i < count($rules_data))
							{
								if ((!$rules_data[$i]->is_ready) && ($rules_data[$i]->answer_id == $old_field_id))
								{
									$rules_data[$i]->answer_id = $new_field->id;
									$rules_data[$i]->is_ready = 1;
								}
								$i++;
							}
						}
						elseif ((!$run_from_surv_copy) && $doing_rule && $copy_rules && (count($rules_data)))
						{
							$i = 0;
							while ($i < count($rules_data))
							{
								if ((!$rules_data[$i]->is_ready) && ($rules_data[$i]->answer_id == $old_field_id))
								{
									$rules_data[$i]->answer_id = $new_field->id;
									$rules_data[$i]->is_ready = 1;
								}
								$i++;
							}
						}
					}
					if ((!$run_from_surv_copy) && $doing_rule && $copy_rules && (count($rules_data)) && ($quest2copy['sf_qtype'] == 2))
					{
						$i = 0;
						while ($i < count($rules_data))
						{
							if ($rules_data[$i]->is_ready)
							{
								$new_rule = new mos_Survey_Force_Rule_Field($database);
								$new_rule->id = 0;
								$new_rule->answer_id = $rules_data[$i]->answer_id;
								$new_rule->quest_id = $new_quest_id;
								$new_rule->next_quest_id = $rules_data[$i]->next_quest_id;
								if (!$new_rule->check())
								{
									echo "<script> alert('" . $new_field->getError() . "'); window.history.go(-1); </script>\n";
									exit();
								}
								if (!$new_rule->store())
								{
									echo "<script> alert('" . $new_field->getError() . "'); window.history.go(-1); </script>\n";
									exit();
								}
							}
							$i++;
						}
					}

				}
				if ($quest2copy['sf_qtype'] == 1)
				{
					$query = "SELECT * FROM #__survey_force_scales WHERE quest_id = '" . $old_quest_id . "' ORDER BY ordering";
					$database->setQuery($query);
					$scales_to_copy = $database->loadAssocList();
					foreach ($scales_to_copy as $scale2copy)
					{
						$new_scale = new mos_Survey_Force_Scale_Field($database);
						if (!$new_scale->bind($scale2copy))
						{
							echo "<script> alert('" . $new_scale->getError() . "'); window.history.go(-1); </script>\n";
							exit();
						}
						$new_scale->id = 0;
						//$new_scale->ordering = 0;
						$new_scale->quest_id = $new_quest_id;
						if (!$new_scale->check())
						{
							echo "<script> alert('" . $new_scale->getError() . "'); window.history.go(-1); </script>\n";
							exit();
						}
						if (!$new_scale->store())
						{
							echo "<script> alert('" . $new_scale->getError() . "'); window.history.go(-1); </script>\n";
							exit();
						}

						$scales_ids_map[$scale2copy['id']] = $new_scale->id;
					}
				}
				if (($quest2copy['sf_qtype'] == 5) || ($quest2copy['sf_qtype'] == 6))
				{
					$query = "SELECT * FROM #__survey_force_fields WHERE quest_id = '" . $old_quest_id . "' and is_main = 1";
					$database->setQuery($query);
					$fields_to_copy = $database->loadAssocList();
					foreach ($fields_to_copy as $field2copy)
					{
						$new_field = new mos_Survey_Force_Field($database);
						if (!$new_field->bind($field2copy))
						{
							echo "<script> alert('" . $new_field->getError() . "'); window.history.go(-1); </script>\n";
							exit();
						}
						$new_field->id = 0;
						//$new_field->ordering = 0;
						$new_field->quest_id = $new_quest_id;

						$alt_field_id = $new_field->alt_field_id;
						$query = "SELECT * FROM #__survey_force_fields WHERE id='" . $alt_field_id . "' and quest_id = '" . $old_quest_id . "' and is_main = 0";
						$database->setQuery($query);
						$alt_field_to_copy = $database->loadAssocList();
						$new_alt_field = new mos_Survey_Force_Field($database);
						if (!$new_alt_field->bind($alt_field_to_copy[0]))
						{
							echo "<script> alert('" . $new_alt_field->getError() . "'); window.history.go(-1); </script>\n";
							exit();
						}
						$new_alt_field->id = 0;
						//$new_alt_field->ordering = 0;
						$new_alt_field->quest_id = $new_quest_id;

						if (!$new_alt_field->check())
						{
							echo "<script> alert('" . $new_alt_field->getError() . "'); window.history.go(-1); </script>\n";
							exit();
						}
						if (!$new_alt_field->store())
						{
							echo "<script> alert('" . $new_alt_field->getError() . "'); window.history.go(-1); </script>\n";
							exit();
						}
						$new_alt_field_id = $new_alt_field->id;
						$altfields_ids_map[$alt_field_to_copy[0]['id']] = $new_alt_field->id;

						$new_field->alt_field_id = $new_alt_field_id;
						if (!$new_field->check())
						{
							echo "<script> alert('" . $new_field->getError() . "'); window.history.go(-1); </script>\n";
							exit();
						}
						if (!$new_field->store())
						{
							echo "<script> alert('" . $new_field->getError() . "'); window.history.go(-1); </script>\n";
							exit();
						}
						$fields2_ids_map[$field2copy['id']] = $new_field->id;
					}
				}
				if ($quest2copy['sf_qtype'] == 9)
				{
					$query = "SELECT * FROM `#__survey_force_fields` WHERE quest_id = '" . $old_quest_id . "'";
					$database->setQuery($query);
					$fields_to_copy = $database->loadAssocList();

					foreach ($fields_to_copy as $field2copy)
					{
						$new_field = new mos_Survey_Force_Field($database);
						if (!$new_field->bind($field2copy))
						{
							echo "<script> alert('" . $new_field->getError() . "'); window.history.go(-1); </script>\n";
							exit();
						}
						$new_field->id = 0;
						$new_field->quest_id = $new_quest_id;
						if (!$new_field->check())
						{
							echo "<script> alert('" . $new_field->getError() . "'); window.history.go(-1); </script>\n";
							exit();
						}
						if (!$new_field->store())
						{
							echo "<script> alert('" . $new_field->getError() . "'); window.history.go(-1); </script>\n";
							exit();
						}
						$fields_ids_map[$field2copy['id']] = $new_field->id;
					}
				}

			}

			if ($run_from_surv_copy)
			{
				$query = "SELECT * FROM #__survey_force_quest_show WHERE quest_id IN ('" . implode("','", array_keys($quests_ids_map)) . "')";
				$database->setQuery($query);
				$ds_rules = $database->loadObjectList();
				if (is_array($ds_rules))
					foreach ($ds_rules as $ds_rule)
					{
						$ds_rule->id = null;
						if (!isset($quests_ids_map[$ds_rule->quest_id])) continue;
						foreach ($quests_to_copy as $quest2copy)
						{
							if ($quest2copy['id'] == $ds_rule->quest_id_a)
							{
								if ($quest2copy['sf_qtype'] == 1)
								{
									$ds_rule->ans_field = $scales_ids_map[$ds_rule->ans_field];
									$ds_rule->answer = $fields_ids_map[$ds_rule->answer];
								}
								elseif ($quest2copy['sf_qtype'] == 2 || $quest2copy['sf_qtype'] == 3)
								{
									$ds_rule->answer = $fields_ids_map[$ds_rule->answer];
								}
								elseif ($quest2copy['sf_qtype'] == 5 || $quest2copy['sf_qtype'] == 6)
								{
									$ds_rule->answer = $fields2_ids_map[$ds_rule->answer];
									$ds_rule->ans_field = $altfields_ids_map[$ds_rule->ans_field];
								}
								elseif ($quest2copy['sf_qtype'] == 9)
								{
									$ds_rule->answer = $fields_ids_map[$ds_rule->answer];
									$ds_rule->ans_field = $fields_ids_map[$ds_rule->ans_field];
								}
							}
						}
						$ds_rule->survey_id = $surveyMove;
						$ds_rule->quest_id = $quests_ids_map[$ds_rule->quest_id];
						$ds_rule->quest_id_a = $quests_ids_map[$ds_rule->quest_id_a];

						$database->insertObject('#__survey_force_quest_show', $ds_rule, 'id');
					}


				$query = "SELECT * FROM #__survey_force_rules WHERE quest_id IN ('" . implode("','", array_keys($quests_ids_map)) . "')";
				$database->setQuery($query);
				$rules_data = $database->loadObjectList();

				foreach ($rules_data as $rule_data)
				{
					foreach ($quests_to_copy as $quest2copy)
					{
						if ($quest2copy['id'] == $rule_data->quest_id)
						{

							$new_rule = new mos_Survey_Force_Rule_Field($database);
							$new_rule->id = 0;

							if ($quest2copy['sf_qtype'] == 1)
							{
								$new_rule->answer_id = $fields_ids_map[$rule_data->answer_id];
								$new_rule->alt_field_id = $scales_ids_map[$rule_data->alt_field_id];
							}
							elseif ($quest2copy['sf_qtype'] == 2 || $quest2copy['sf_qtype'] == 3)
							{
								$new_rule->answer_id = $fields_ids_map[$rule_data->answer_id];
							}
							elseif ($quest2copy['sf_qtype'] == 5 || $quest2copy['sf_qtype'] == 6)
							{
								$new_rule->answer_id = $fields2_ids_map[$rule_data->answer_id];
								$new_rule->alt_field_id = $altfields_ids_map[$rule_data->alt_field_id];
							}
							elseif ($quest2copy['sf_qtype'] == 9)
							{
								$new_rule->answer_id = $fields_ids_map[$rule_data->answer_id];
								$new_rule->alt_field_id = $fields_ids_map[$rule_data->alt_field_id];
							}

							$new_rule->quest_id = $quests_ids_map[$rule_data->quest_id];
							$new_rule->next_quest_id = $quests_ids_map[$rule_data->next_quest_id];

							if (!$new_rule->check())
							{
								echo "<script> alert('" . $new_field->getError() . "'); window.history.go(-1); </script>\n";
								exit();
							}
							if (!$new_rule->store())
							{
								echo "<script> alert('" . $new_field->getError() . "'); window.history.go(-1); </script>\n";
								exit();
							}
						}
					}
				}

			}
		}
		if (!$run_from_surv_copy)
		{
			$surveyNew = new mos_Survey_Force_Survey ($database);
			$surveyNew->load($surveyMove);
			self::SF_refreshOrder($surveyMove);
			$msg = $total . JText::_('COM_SF_QUESTIONS_COPIED_TO') . $surveyNew->sf_name;
			mosRedirect(SFRoute("index.php?option=com_surveyforce&task=questions"));
		}
	}

	public static function SF_show_results($surv_id, $option)
	{
		$database = JFactory::getDbo();
		@set_time_limit(0);

		require_once(JPATH_BASE . '/components/com_surveyforce/helpers/generate.surveyforce.php');
		$rows = array();
		$query = "SELECT id FROM #__survey_force_quests WHERE published = 1 AND sf_survey = '" . $surv_id . "' ORDER BY ordering, id";
		$database->setQuery($query);
		$questions = $database->loadColumn();

		$sf_config = JComponentHelper::getParams('com_surveyforce');
		$prefix = $sf_config->get('sf_result_type') == 'Bar' ? 'b' : 'p';
		$gg = new sf_ImageGenerator(array($sf_config->get('sf_result_type')));
		$gg->colors['axisColor1'] = $sf_config->get($prefix . '_axis_color1');
		$gg->colors['axisColor2'] = $sf_config->get($prefix . '_axis_color2');
		$gg->colors['aquaColor1'] = $sf_config->get($prefix . '_aqua_color1');
		$gg->colors['aquaColor2'] = $sf_config->get($prefix . '_aqua_color2');
		$gg->colors['aquaColor3'] = $sf_config->get($prefix . '_aqua_color3');
		$gg->colors['aquaColor4'] = $sf_config->get($prefix . '_aqua_color4');
		$gg->colors['barColor1'] = $sf_config->get($prefix . '_bar_color1');
		$gg->colors['barColor2'] = $sf_config->get($prefix . '_bar_color2');
		$gg->colors['barColor3'] = $sf_config->get($prefix . '_bar_color3');
		$gg->colors['barColor4'] = $sf_config->get($prefix . '_bar_color4');
		$gg->width = $sf_config->get($prefix . '_width');
		$gg->height = $sf_config->get($prefix . '_height');
		$gg->clearOldImages();//delete yesterday images
		foreach ($questions as $question)
		{
			$img_src = $gg->getImage($surv_id, $question, 1);
			
			if (is_array($img_src))
			{
				foreach ($img_src as $imgsrc)
				{
					$rows[] = $imgsrc;
				}
			}
			elseif ($img_src)
			{
				$rows[] = $img_src;
			}
		}
		$lists = array();
		$query = "SELECT sf_name  FROM #__survey_force_survs WHERE id = " . $surv_id;
		$database->setQuery($query);
		$lists['sname'] = $database->loadResult();

		$javascript = 'onchange="document.adminForm.submit();"';
		$query = "SELECT id AS value, sf_name AS text"
			. "\n FROM #__survey_force_survs"
			. (JFactory::getUser()->get('usertype') != 'Super Administrator' ? " WHERE sf_author = '" . JFactory::getUser()->id . "' " : ' ')
			. "\n ORDER BY sf_name";
		$database->setQuery($query);

		$survey = $database->loadObjectList();
		$survey = mosHTML::selectList($survey, 'cid[]', 'class="text_area" size="1" ' . $javascript, 'value', 'text', $surv_id);
		$lists['survey'] = $survey;
		survey_force_front_html::show_results($rows, $lists, $option);

	}

	public static function SF_preview_survey($id, $option)
	{
		$database = JFactory::getDbo();
		$unique_id = md5(uniqid(rand(), true));
		$query = "INSERT INTO `#__survey_force_previews` SET `preview_id` = '" . $unique_id . "', `time` = '" . strtotime(JFactory::getDate()) . "'";
		$database->setQuery($query);
		$database->execute();

		mosRedirect("index.php?option=com_surveyforce&view=survey&id={$id}&preview=" . $unique_id);
	}

	public static function SF_editSurvey($id, $option)
	{
		$database = JFactory::getDbo();
		$row = new mos_Survey_Force_Survey($database);
		// load the row from the db table
		$row->load($id);

		if ($id)
		{
			// do stuff for existing records
			$row->checkout(JFactory::getUser()->id);
			$row->sf_author = JFactory::getUser()->id;
		}
		else
		{
			// do stuff for new records
			#$row->published = 1;
			$row->sf_author = JFactory::getUser()->id;
			$row->sf_special = 0;
			$row->sf_auto_pb = 1;
		}
		if (!$row->sf_author)
			$row->sf_author = JFactory::getUser()->id;
		$lists = array();
		$query2 = "SELECT * FROM #__survey_force_cats order by sf_catname";
		$database->setQuery($query2);
		$sf_cats = $database->loadObjectList();
		$lists['sf_categories'] = mosHTML::selectList($sf_cats, 'sf_cat', 'class="text_area" size="1"', 'id', 'sf_catname', $row->sf_cat);

		$row->sf_template = ($row->sf_template ? $row->sf_template : 1);
		$query2 = "SELECT `id` AS `value`, `sf_name` AS `text` FROM `#__survey_force_templates` ORDER BY `sf_name`";
		$database->setQuery($query2);
		$templates = $database->loadObjectList();
		$lists['sf_templates'] = mosHTML::selectList($templates, 'sf_template', 'class="text_area" size="1"', 'value', 'text', $row->sf_template);

		// build the html radio buttons for published
		$lists['published'] = mosHTML::yesnoradioList('published', '', $row->published);
		$directory = '/media/com_surveyforce/';
		$javascript = "onchange=\"javascript:if (document.adminForm.sf_image.options[selectedIndex].value!='') {"
			. " document.imagelib.src='" . JURI::root() . "media/com_surveyforce/' + document.adminForm.sf_image.options[selectedIndex].value; } else {"
			. " document.imagelib.src='" . JURI::root() . "components/com_surveyforce/images/blank.png'}\"";
		$lists['images'] = self::sfm_Images('sf_image', $row->sf_image, $javascript, $directory);

		$query = " SELECT id AS value, listname AS text FROM #__survey_force_listusers "
			. (JFactory::getUser()->get('usertype') != 'Super Administrator' ? "WHERE sf_author_id = '{JFactory::getUser()->id}' " : '')
			. " ORDER BY listname ";
		$database->setQuery($query);
		$userlists = $database->loadObjectList();
		$selected = array();
		if ($row->sf_special)
		{
			$tmp = explode(',', $row->sf_special);
			foreach ($tmp as $k => $list_id)
			{
				$selected[$k]->value = $list_id;
			}
		}


		$yes_no[] = mosHTML::makeOption('1', JText::_("COM_SURVEYFORCE_SF_YES"));
		$yes_no[] = mosHTML::makeOption('0', JText::_("COM_SURVEYFORCE_SF_NO"));
		$lists['sf_auto_pb'] = mosHTML::selectList($yes_no, 'sf_auto_pb', 'class="text_area" size="1" ', 'value', 'text', intval($row->sf_auto_pb));

		$lists['published'] = mosHTML::selectList($yes_no, 'published', 'class="text_area" size="1" ', 'value', 'text', intval($row->published));

		$lists['sf_prev_enable'] = mosHTML::selectList($yes_no, 'sf_prev_enable', 'class="text_area" size="1" ', 'value', 'text', intval($row->sf_prev_enable));

		$lists['sf_enable_descr'] = mosHTML::selectList($yes_no, 'sf_enable_descr', 'class="text_area" size="1" ', 'value', 'text', intval($row->sf_enable_descr));

		$lists['sf_progressbar'] = mosHTML::selectList($yes_no, 'sf_progressbar', 'class="text_area" size="1" ', 'value', 'text', intval($row->sf_progressbar));

		$bartype[] = mosHTML::makeOption('0', JText::_('COM_SF_COUNT_BY_QUESTIONS'));
		$bartype[] = mosHTML::makeOption('1', JText::_('COM_SF_COUNT_BY_PAGES'));

		$lists['sf_progressbar_type'] = mosHTML::selectList($bartype, 'sf_progressbar_type', 'class="text_area" size="1" ', 'value', 'text', intval($row->sf_progressbar_type));
		$yes_no_anonimous = array();
		$yes_no_anonimous[] = mosHTML::makeOption('1', JText::_('COM_SF_NO'));
		$yes_no_anonimous[] = mosHTML::makeOption('0', JText::_('COM_SF_YES'));

		$lists['sf_anonymous'] = mosHTML::selectList($yes_no_anonimous, 'sf_anonymous', 'class="text_area" size="1" ', 'value', 'text', intval($row->sf_anonymous));

		$random = array();

		$random[] = mosHTML::makeOption('0', JText::_("COM_SURVEYFORCE_SF_NO"));
		$random[] = mosHTML::makeOption('1', JText::_("COM_SURVEYFORCE_SF_RANDOM_ORDER1"));
		$random[] = mosHTML::makeOption('2', JText::_("COM_SURVEYFORCE_SF_RANDOM_ORDER2"));
		$random[] = mosHTML::makeOption('3', JText::_("COM_SURVEYFORCE_SF_RANDOM_ORDER3"));
		$lists['sf_random'] = mosHTML::selectList($random, 'sf_random', 'class="text_area" size="1" ', 'value', 'text', intval($row->sf_random));

		$voting = array();
		$voting[] = mosHTML::makeOption('0', JText::_('COM_SURVEYFORCE_SF_MULTIPLE_VOTING'));
		$voting[] = mosHTML::makeOption('1', JText::_('COM_SURVEYFORCE_SF_ONCE_VOTING'));
		$voting[] = mosHTML::makeOption('2', JText::_('COM_SURVEYFORCE_SF_ONCE_VOTING_REPLACE'));
		$voting[] = mosHTML::makeOption('3', JText::_('COM_SURVEYFORCE_SF_ALLOW_EDIT_ANSWERS'));

		$lists['sf_reg_voting'] = mosHTML::selectList($voting, 'sf_reg_voting', 'class="text_area" size="1" ', 'value', 'text', intval($row->sf_reg_voting));
		$lists['sf_friend_voting'] = mosHTML::selectList($voting, 'sf_friend_voting', 'class="text_area" size="1" ', 'value', 'text', intval($row->sf_friend_voting));
		$lists['sf_inv_voting'] = mosHTML::selectList($voting, 'sf_inv_voting', 'class="text_area" size="1" ', 'value', 'text', intval($row->sf_inv_voting));

		$voting = array();
		$voting[] = mosHTML::makeOption('0', JText::_('COM_SURVEYFORCE_SF_MULTIPLE_VOTING'));
		$voting[] = mosHTML::makeOption('1', JText::_('COM_SURVEYFORCE_SF_ONCE_VOTING'));
		$voting[] = mosHTML::makeOption('2', JText::_('COM_SURVEYFORCE_SF_ONCE_VOTING_REPLACE'));

		$disabled = (intval($row->sf_pub_control) ? '' : ' disabled="disabled" ');
		$lists['sf_pub_voting'] = mosHTML::selectList($voting, 'sf_pub_voting', 'class="text_area" size="1" id="sf_pub_voting" style="width: 160px;" ' . $disabled, 'value', 'text', intval($row->sf_pub_voting));


		$control = array();
		$control[] = mosHTML::makeOption('0', JText::_('COM_SURVEYFORCE_SF_NONE'));
		$control[] = mosHTML::makeOption('1', JText::_('COM_SURVEYFORCE_SF_IP_ADDR'));
		$control[] = mosHTML::makeOption('2', JText::_('COM_SURVEYFORCE_SF_COOKIE'));
		$control[] = mosHTML::makeOption('3', JText::_('COM_SURVEYFORCE_SF_BOTH'));

		$jscript = ' onchange="javascript: if (this.selectedIndex == 0) {document.getElementById(\'sf_pub_voting\').disabled=\'disabled\';}else{document.getElementById(\'sf_pub_voting\').disabled=\'\';}" ';
		$lists['sf_pub_control'] = mosHTML::selectList($control, 'sf_pub_control', 'class="text_area" size="1" style="width: 160px;" ' . $jscript, 'value', 'text', intval($row->sf_pub_control));

		$lists['sf_use_css'] = mosHTML::selectList($yes_no, 'sf_use_css', 'class="text_area" size="1" ', 'value', 'text', intval($row->sf_use_css));

		if (count($userlists) > 0)
		{
			$lists['userlists'] = mosHTML::selectList($userlists, 'userlists[]', 'class="text_area" size="4"  multiple="multiple" ', 'value', 'text', $selected);
		}
		else
		{
			$lists['userlists'] = null;
		}
		survey_force_front_html::SF_editSurvey($row, $lists, $option);
	}

	public static function sfm_Images($name, &$active, $javascript = NULL, $directory = NULL)
	{

		if (!$directory)
		{
			$directory = JURI::root() . 'media/com_surveyforce';
		}

		if (!$javascript)
		{
			$javascript = "onchange=\"javascript:if (document.forms[0].image.options[selectedIndex].value!='') {document.imagelib.src='$directory/' + document.forms[0].image.options[selectedIndex].value} else {document.imagelib.src='" . JURI::root() . "components/com_surveyforce/images/blank.png'}\"";
		}

		$imageFiles = mosReadDirectory(JPATH_BASE . $directory);
		$images = array(mosHTML::makeOption('', JText::_('COM_SURVEYFORCE_SELECT_IMAGE')));
		foreach ($imageFiles as $file)
		{
			if (preg_match('/bmp|gif|jpg|png/', $file))
			{
				$images[] = mosHTML::makeOption($file);
			}
		}
		$images = mosHTML::selectList($images, $name, 'class="inputbox" size="1" ' . $javascript, 'value', 'text', $active);

		return $images;
	}

	public static function SF_processCSVField($field_text)
	{
		$field_text = strip_tags($field_text);
		$field_text = str_replace('&#039;', "'", $field_text);
		$field_text = str_replace('&#39;', "'", $field_text);
		$field_text = str_replace('&quot;', '"', $field_text);
		$field_text = str_replace('"', '""', $field_text);
		$field_text = str_replace("\n", ' ', $field_text);
		$field_text = str_replace("\r", ' ', $field_text);
		$field_text = strtr($field_text, array_flip(self::get_html_translation_table_my()));
		$field_text = preg_replace("/&#([0-9]+);/me", "chr('\\1')", $field_text);
		$field_text = '"' . $field_text . '"';
		return $field_text;
	}

	public static function SF_processCSVField_noquot($field_text)
	{
		$field_text = strip_tags($field_text);
		$field_text = str_replace('&#039;', "'", $field_text);
		$field_text = str_replace('&#39;', "'", $field_text);
		$field_text = str_replace('&quot;', '"', $field_text);
		$field_text = str_replace('"', '""', $field_text);
		$field_text = str_replace("\n", ' ', $field_text);
		$field_text = str_replace("\r", ' ', $field_text);
		$field_text = strtr($field_text, array_flip(self::get_html_translation_table_my()));
		$field_text = preg_replace("/&#([0-9]+);/me", "chr('\\1')", $field_text);
		return $field_text;
	}

	public static function updateJSRules($survey_id)
	{
		jimport('joomla.filesystem.file');
		jimport('joomla.filesystem.folder');

		if (JFile::exists(JPATH_SITE . '/components/com_surveyforce/jomsocial_rule.xml'))
			$rules = JFile::read(JPATH_SITE . '/components/com_surveyforce/jomsocial_rule.xml');
		else
			$rules = '';

		if (strpos($rules, 'completed.survey' . $survey_id) === false)
		{
			$rules = str_replace('</rules></jomsocial>', '', $rules);
			$new_rule = "\r\n
\t<rule>\r\n
\t\t<name>" . JText::_('COM_SF_COMPLETED_SURVEY') . $survey_id . "</name>\r\n
\t\t<description>" . JText::_('COM_SF_GIVE_POINTS_WHEN_REGISTERED_USER_COMPLETE_SURVEY') . "</description>\r\n
\t\t<action_string>completed.survey" . $survey_id . "</action_string>\r\n
\t\t<publish>" . JText::_('COM_SF_FALSE') . "</publish>\r\n
\t\t<points>0</points>\r\n
\t\t<access_level>1</access_level>\r\n
\t</rule>\r\n
\r\n";
			$rules .= $new_rule . '</rules></jomsocial>';

			JFile::delete(JPATH_SITE . '/components/com_surveyforce/jomsocial_rule.xml');
			JFile::write(JPATH_SITE . '/components/com_surveyforce/jomsocial_rule.xml', $rules);
		}
	}

	public static function SF_ListQuestions($option)
	{
		$database = JFactory::getDbo();
		$survid = intval(JFactory::getApplication()->getUserStateFromRequest("surv_id", 'surv_id', 0));
		$limit = intval(JFactory::getApplication()->getUserStateFromRequest("viewlistlimit", 'limit', 20));
		$limitstart = intval(JFactory::getApplication()->getUserStateFromRequest("viewlimitstart", 'limitstart', 0));
		if ($limit == 0) $limit = 999999;

		$limit = intval(mosGetParam($_REQUEST, 'limit', JFactory::getSession()->get('list_limit', JFactory::getApplication()->getCfg('list_limit'))));
		if ($limit == 0) $limit = 999999;
		JFactory::getSession()->set('list_limit', $limit);
		$limitstart = intval(mosGetParam($_REQUEST, 'limitstart', JFactory::getSession()->get('list_limitstart', 0)));
		JFactory::getSession()->set('list_limitstart', $limitstart);
		$survid = intval(mosGetParam($_REQUEST, 'surv_id', JFactory::getSession()->get('list_surv_id', 0)));
		JFactory::getSession()->set('list_surv_id', $survid);
		$lists = array();
		$lists['sf_auto_pb_on'] = '';
		if ($survid)
		{
			$query = "SELECT sf_auto_pb "
				. "\n FROM #__survey_force_survs"
				. "\n WHERE id = $survid";
			$database->setQuery($query);
			if ($database->loadResult() > 0)
				$lists['sf_auto_pb_on'] = '<small>' . JText::_('COM_SURVEYFORCE_SF_AUTO_PB_IS_ON') . '</small>';
		}

		// get the total number of records
		$query = "SELECT COUNT(*)"
			. "\n FROM #__survey_force_quests"
			. ($survid ? "\n WHERE sf_survey = $survid" : '');
		$database->setQuery($query);
		$total = $database->loadResult();

		jimport('joomla.html.pagination');
		$pageNav = new SFPageNav($total, $limitstart, $limit);

		// get the subset (based on limits) of required records
		$query = "SELECT a.*, b.sf_qtype as qtype_full, c.sf_name as survey_name"
			. "\n FROM #__survey_force_quests a LEFT JOIN #__survey_force_qtypes b ON b.id = a.sf_qtype LEFT JOIN #__survey_force_survs c ON a.sf_survey = c.id"
			. ($survid ? "\n WHERE a.sf_survey = $survid " : '')
			. "\n ORDER BY a.ordering, a.id "
			. "\n LIMIT $pageNav->limitstart, $pageNav->limit";
		$database->setQuery($query);
		$quests = $database->loadObjectList();
		$lists['survid'] = $survid ? $survid : 0;
		if ($survid)
		{
			$query = " SELECT a.*, c.sf_name AS survey_name, b.id AS quest_id "
				. " FROM #__survey_force_qsections AS a "
				. " LEFT JOIN #__survey_force_survs AS c ON a.sf_survey_id = c.id "
				. " LEFT JOIN #__survey_force_quests AS b ON b.sf_section_id = a.id "
				. " WHERE 1=1 "
				. ($survid ? "\n AND a.sf_survey_id = $survid" : '')
				. " ORDER BY a.ordering DESC, a.id DESC";
			$database->setQuery($query);

			$sections = $database->loadAssocList('id');
			$sections = (is_array($sections) ? $sections : array());
			$first_sec = end($sections);
			$end_sec = reset($sections);
			$rows = array();
			$last_sid = 0;
			foreach ($quests as $n => $quest)
			{
				if (!isset($first_quest_sec) && $pageNav->limitstart == 0)
				{
					$first_quest_sec = $quest->sf_section_id;
				}

				if ($quest->sf_section_id == 0)
				{
					$rows[] = $quest;
					continue;
				}
				if ($quest->sf_section_id != $last_sid)
				{
					foreach ($sections as $section)
					{
						if ($section['id'] == $quest->sf_section_id)
						{
							if (isset($first_quest_sec) && $first_quest_sec == $section['id'])
								$section['first'] = true;

							$rows[] = $section;
							unset($sections[$section['id']]);
						}
					}
				}
				$last_sid = $quest->sf_section_id;
				$rows[] = $quest;
			}
			if ($pageNav->limitstart + $pageNav->limit >= $total)
			{
				$sections = array_reverse($sections);
				foreach ($sections as $section)
				{
					if ($section['quest_id'] == '')
					{
						if ($first_sec['id'] == $section['id'])
							$section['first'] = true;
						if ($end_sec['id'] == $section['id'])
							$section['end'] = true;
						$rows[] = $section;
					}
				}
			}
		}
		else
			$rows = $quests;
		$i = 0;
		while ($i < count($rows))
		{
			if (isset($rows[$i]->sf_impscale) && $rows[$i]->sf_impscale)
			{

				$query = "SELECT `id` FROM `#__survey_force_user_starts` WHERE `survey_id` = '{$rows[$i]->sf_survey}'";
				$database->setQuery($query);
				$all_start_ids = $database->loadColumn();

				$query = "SELECT iscale_name FROM #__survey_force_iscales WHERE id = '" . $rows[$i]->sf_impscale . "'";
				$database->setQuery($query);
				$rows[$i]->iscale_name = $database->loadResult();

				$query = "SELECT count(id) FROM #__survey_force_user_answers_imp"
					. "\n WHERE quest_id = '" . $rows[$i]->id . "' and survey_id = '" . $rows[$i]->sf_survey . "'"
					. "\n AND iscale_id = '" . $rows[$i]->sf_impscale . "' AND `start_id` IN ('" . implode("','", $all_start_ids) . "')";
				$database->setQuery($query);
				$rows[$i]->total_iscale_answers = $database->loadResult();

				$query = "SELECT b.isf_name, count(a.id) as ans_count FROM #__survey_force_iscales_fields as b LEFT JOIN #__survey_force_user_answers_imp as a ON ( a.quest_id = '" . $rows[$i]->id . "' and a.survey_id = '" . $rows[$i]->sf_survey . "' and a.iscale_id = '" . $rows[$i]->sf_impscale . "' and a.iscalefield_id = b.id AND `a`.`start_id` IN ('" . implode("','", $all_start_ids) . "'))"
					. "\n WHERE b.iscale_id = '" . $rows[$i]->sf_impscale . "'"
					. "\n GROUP BY b.isf_name ORDER BY b.ordering";
				$database->setQuery($query);
				$ans_data = $database->loadObjectList();

				$rows[$i]->answer_imp = array();
				$j = 0;
				while ($j < count($ans_data))
				{
					$rows[$i]->answer_imp[$j]->num = $j;
					$rows[$i]->answer_imp[$j]->ftext = $ans_data[$j]->isf_name;
					$rows[$i]->answer_imp[$j]->ans_count = $ans_data[$j]->ans_count;
					$j++;
				}
			}
			$i++;
		}

		$javascript = 'onchange="document.adminForm.submit();"';

		$query = "SELECT id AS value, sf_name AS text"
			. "\n FROM #__survey_force_survs"
			. (JFactory::getUser()->get('usertype') != 'Super Administrator' ? " WHERE sf_author = '" . JFactory::getUser()->id . "' " : ' ')
			. "\n ORDER BY sf_name";
		$database->setQuery($query);
		$surveys[] = mosHTML::makeOption('0', JText::_("COM_SF_SELECT_SURVEY"));
		$surveys = @array_merge($surveys, $database->loadObjectList());
		$survey = mosHTML::selectList($surveys, 'surv_id', 'class="text_area" size="1" ' . $javascript, 'value', 'text', $survid);
		$lists['survey'] = $survey;

		$query = "SELECT id AS value, sf_qtype AS text"
			. "\n FROM #__survey_force_qtypes"
			. "\n ORDER BY id";
		$database->setQuery($query);
		$qtypes = array();
		$qtypes = $database->loadObjectList();
		$qtypes = mosHTML::selectList($qtypes, 'qtypes_id', 'class="text_area" size="1" ', 'value', 'text', 1);
		$lists['qtypes'] = $qtypes;
		survey_force_front_html::SF_showQuestsList($rows, $lists, $pageNav, $option);
	}

	public static function SF_editQuestion($id, $option, $qtype = 0)
	{
		$database = JFactory::getDbo();
		$new_qtype_id = intval(JFactory::getApplication()->getUserStateFromRequest("new_qtype_id", 'new_qtype_id', 0));

		if ($qtype == 8)
		{
			$sf_survey = intval(JFactory::getApplication()->getUserStateFromRequest("surv_id", 'surv_id', 0));
			$sf_survey = intval(mosGetParam($_REQUEST, 'surv_id', JFactory::getSession()->get('list_surv_id', 0)));
			$query = "SELECT MAX(ordering) FROM #__survey_force_quests WHERE sf_survey = {$sf_survey}";
			$database->setQuery($query);
			$max_ord = $database->loadResult();

			$query = "INSERT INTO #__survey_force_quests (sf_survey, sf_qtype, sf_compulsory, sf_qtext, ordering, published, is_final_question ) VALUES ($sf_survey, 8, 0, 'Page Break', " . ($max_ord + 1) . ", 1, 0) ";
			$database->setQuery($query);
			$database->execute();
			mosRedirect(SFRoute("index.php?option=com_surveyforce&task=questions"));
		}

		$is_return = intval(JFactory::getSession()->get('is_return_sf')) > 0 ? true : false;
		JFactory::getSession()->set('is_return_sf', -1);

		$row = new mos_Survey_Force_Question($database);
		// load the row from the db table
		$row->load($id);

		if ($id)
		{
			// do stuff for existing records
			if ($row->sf_qtype == 8)
			{
				mosRedirect(SFRoute("index.php?option=com_surveyforce&task=questions"));
			}
			$row->checkout(JFactory::getUser()->id);
		}
		else
		{
			// do stuff for new records
			$row->ordering = 0;
			$row->sf_survey = intval(JFactory::getApplication()->getUserStateFromRequest("surv_id", 'surv_id', 0));
			$row->sf_qtype = intval($new_qtype_id);
			if (empty($row->sf_survey))
				$row->sf_survey = intval(mosGetParam($_REQUEST, 'surv_id', JFactory::getSession()->get('list_surv_id', 0)));
		}

		$lists = array();
		$lists['survid'] = ($row->sf_survey ? $row->sf_survey : 0);
		$row->sf_qtext = $is_return ? JFactory::getSession()->get('sf_qtext_sf') : $row->sf_qtext;
		// build the html select list for ordering
		if ($id)
		{
			$query = "SELECT a.ordering AS value, a.sf_qtext AS text"
				. "\n FROM #__survey_force_quests AS a"
				. ($row->sf_survey ? "\n WHERE a.sf_survey = '" . $row->sf_survey . "' " : '')
				. " AND sf_section_id = '" . $row->sf_section_id . "' "
				. "\n ORDER BY a.ordering, a.id ";
		}
		else
		{
			$query = "SELECT a.ordering AS value, a.sf_qtext AS text"
				. "\n FROM #__survey_force_quests AS a"
				. ($row->sf_survey ? "\n WHERE a.sf_survey = '" . $row->sf_survey . "' " : '')
				. "\n ORDER BY a.ordering, a.id ";
		}

		$text_new_order = JText::_("COM_SURVEYFORCE_SF_NEW_ITEM");
		if ($id)
		{
			$order = self::sfGetOrderingList($query);
			$order = array_slice($order, 1, -1);
			$sel_value = $is_return ? JFactory::getSession()->get('ordering_sf') : $row->ordering;
			$ordering = mosHTML::selectList($order, 'ordering', 'class="text_area" size="1"', 'value', 'text', intval($sel_value));
		}
		else
		{
			$ordering = '<input type="hidden" name="ordering" value="' . $row->ordering . '" />' . $text_new_order;
		}
		$lists['ordering'] = $ordering;

		//build list of surveys
		$query = "SELECT id AS value, sf_name AS text"
			. "\n FROM #__survey_force_survs"
			. (JFactory::getUser()->get('usertype') != 'Super Administrator' ? " WHERE sf_author = '" . JFactory::getUser()->id . "' " : " ")
			. "\n ORDER BY sf_name";
		$database->setQuery($query);
		$surveys = $database->loadObjectList();
		$disable = '';
		$sel_value = $is_return ? JFactory::getSession()->get('sf_survey_sf') : $row->sf_survey;
		$survey = mosHTML::selectList($surveys, 'sf_survey', $disable . ' class="text_area" size="1" ', 'value', 'text', intval($sel_value));
		$lists['survey'] = $survey;

		//build list of imp.scales
		$query = "SELECT id AS value, iscale_name AS text"
			. "\n FROM #__survey_force_iscales"
			. "\n ORDER BY iscale_name";
		$database->setQuery($query);

		$impscales[] = mosHTML::makeOption('0', JText::_("COM_SF_SELECT_IMP_SCALE"));
		$impscales = @array_merge($impscales, $database->loadObjectList());
		$sel_value = $is_return ? JFactory::getSession()->get('sf_impscale_sf') : $row->sf_impscale;
		if ($is_return)
		{
			$query = "SELECT id FROM #__survey_force_iscales ORDER BY id DESC";
			$database->setQuery($query);
			$sel_value = $database->loadResult();
		}
		$impscale = mosHTML::selectList($impscales, 'sf_impscale', 'class="text_area" size="1" ', 'value', 'text', intval($sel_value));
		$lists['impscale'] = $impscale;
		$yes_no[] = mosHTML::makeOption('1', JText::_("COM_SF_YES"));
		$yes_no[] = mosHTML::makeOption('0', JText::_("COM_SF_NO"));
		$sel_value = $is_return ? JFactory::getSession()->get('sf_compulsory_sf') : $row->sf_compulsory;
		$lists['compulsory'] = mosHTML::selectList($yes_no, 'sf_compulsory', 'class="text_area" size="1" ', 'value', 'text', intval($sel_value));
		$sel_value = $is_return ? JFactory::getSession()->get('insert_pb_sf') : 1;
		$lists['insert_pb'] = mosHTML::selectList($yes_no, 'insert_pb', 'class="text_area" size="1" ', 'value', 'text', intval($sel_value));

		$lists['use_drop_down'] = mosHTML::selectList($yes_no, 'sf_qstyle', 'class="text_area" size="1" ', 'value', 'text', intval($row->sf_qstyle));

		$sel_value = $is_return ? JFactory::getSession()->get('published') : 1;
		$lists['published'] = mosHTML::selectList($yes_no, 'published', 'class="text_area" size="1" ', 'value', 'text', intval($sel_value));

		$lists['sf_default_hided'] = mosHTML::selectList($yes_no, 'sf_default_hided', 'class="text_area" size="1" ', 'value', 'text', intval($row->sf_default_hided));

		//build list of sections
		$query = "SELECT id AS value, sf_name AS text"
			. "\n FROM #__survey_force_qsections"
			. "\n WHERE sf_survey_id = {$lists['survid']} "
			. "\n ORDER BY sf_name ";
		$database->setQuery($query);
		$sf_sections[] = mosHTML::makeOption('0', JText::_("COM_SF_SELECT_SECTION"));
		$sf_sections = @array_merge($sf_sections, $database->loadObjectList());
		$sel_value = $is_return ? JFactory::getSession()->get('sf_section_id_sf') : $row->sf_section_id;
		if (count($sf_sections) > 2)
		{
			$sf_sections = mosHTML::selectList($sf_sections, 'sf_section_id', 'class="text_area" size="1" ', 'value', 'text', intval($sel_value));
			$lists['sf_section_id'] = $sf_sections;
		}
		else
		{
			$lists['sf_section_id'] = null;
		}

		if (!$qtype)
		{
			$qtype = $row->sf_qtype;
		}

		$query = "SELECT id AS value, sf_qtext AS text"
			. "\n FROM #__survey_force_quests WHERE id <> '" . $id . "' AND sf_qtype <> 8 "
			. ($row->sf_survey ? "\n and sf_survey = '" . $row->sf_survey . "'" : '')
			. "\n ORDER BY ordering, id ";
		$database->setQuery($query);
		$quests = $database->loadObjectList();
		$i = 0;
		while ($i < count($quests))
		{
			$quests[$i]->text = strip_tags($quests[$i]->text);
			if (strlen($quests[$i]->text) > 55)
				$quests[$i]->text = mb_substr($quests[$i]->text, 0, 55) . '...';
			$quests[$i]->text = $quests[$i]->value . ' - ' . $quests[$i]->text;
			$i++;
		}
		$quest = mosHTML::selectList($quests, 'sf_quest_list', 'class="text_area" id="sf_quest_list" size="1" ', 'value', 'text', 0);
		$lists['quests'] = $quest;

		$query = "SELECT id AS value, sf_qtext AS text"
			. "\n FROM #__survey_force_quests WHERE id <> '" . $id . "' AND sf_qtype NOT IN (4, 7, 8) "
			. ($row->sf_survey ? "\n and sf_survey = '" . $row->sf_survey . "'" : '')
			. "\n ORDER BY ordering, id ";
		$database->setQuery($query);
		$quests3 = $database->loadObjectList();
		$i = 0;
		while ($i < count($quests3))
		{
			$quests3[$i]->text = strip_tags($quests3[$i]->text);
			if (strlen($quests3[$i]->text) > 55)
				$quests3[$i]->text = mb_substr($quests3[$i]->text, 0, 55) . '...';
			$quests3[$i]->text = $quests3[$i]->value . ' - ' . $quests3[$i]->text;
			$i++;
		}

		$quest = mosHTML::selectList($quests3, 'sf_quest_list3', 'class="text_area" id="sf_quest_list3" size="1" onchange="javascript: showOptions(this.value);" ', 'value', 'text', 0);
		$lists['quests3'] = $quest;


		$query = "SELECT a.*, c.sf_qtext, c.sf_qtype, c.id AS qid,  d.ftext AS aftext, e.stext AS astext, b.ftext AS qoption, b.id AS bid, d.id AS fdid, e.id AS sdid FROM  #__survey_force_fields AS b, #__survey_force_quests AS c, #__survey_force_quest_show AS a LEFT JOIN #__survey_force_fields AS d ON a.ans_field = d.id LEFT JOIN #__survey_force_scales AS e ON a.ans_field = e.id WHERE a.quest_id = '" . $id . "' AND a.answer = b.id AND a.quest_id_a = c.id ";
		$database->setQuery($query);

		$lists['quest_show'] = $database->loadObjectList();

		$i = 0;
		while ($i < count($lists['quest_show']))
		{
			$lists['quest_show'][$i]->sf_qtext = strip_tags($lists['quest_show'][$i]->sf_qtext);
			if (strlen($lists['quest_show'][$i]->sf_qtext) > 55)
				$lists['quest_show'][$i]->sf_qtext = mb_substr($lists['quest_show'][$i]->sf_qtext, 0, 55) . '...';
			$lists['quest_show'][$i]->sf_qtext = $lists['quest_show'][$i]->qid . ' - ' . $lists['quest_show'][$i]->sf_qtext;
			$i++;
		}

		$query = "SELECT next_quest_id "
			. "\n FROM #__survey_force_rules WHERE quest_id = '" . $row->id . "' and answer_id = 9999997 ";
		$database->setQuery($query);
		$squest = (int) $database->loadResult();

		$quest = mosHTML::selectList($quests, 'sf_quest_list2', 'class="text_area" id="sf_quest_list2" size="1" ', 'value', 'text', $squest);
		$lists['quests2'] = $quest;
		$lists['checked'] = '';
		if ($squest) $lists['checked'] = ' checked = "checked" ';

		$lists['sf_fields_rule'] = array();
		$query = "SELECT b.ftext, c.sf_qtext, c.id as next_quest_id, a.priority, d." . ($qtype == 1 ? 's' : 'f') . "text as alt_ftext "
			. "\n FROM  #__survey_force_fields as b, #__survey_force_quests as c, #__survey_force_rules as a LEFT JOIN " . ($qtype == 1 ? "#__survey_force_scales as d " : "#__survey_force_fields as d ") . " ON a.alt_field_id = d.id "
			. "\n WHERE a.quest_id = '" . $row->id . "' and a.answer_id <> 9999997 and a.answer_id = b.id and a.next_quest_id = c.id ";
		$database->setQuery($query);
		$lists['sf_fields_rule'] = $database->loadObjectList();
		if ($is_return)
		{
			$lists['sf_fields_rule'] = array();
			$sf_hid_rule = JFactory::getSession()->get('sf_hid_rule_sf');
			$sf_hid_rule_quest = JFactory::getSession()->get('sf_hid_rule_quest_sf');
			$sf_hid_rule_alt = JFactory::getSession()->get('sf_hid_rule_alt_sf');
			$priority = JFactory::getSession()->get('priority_sf');
			for ($i = 0, $n = count($sf_hid_rule); $i < $n; $i++)
			{
				$tmp = new stdClass();
				$tmp->next_quest_id = $sf_hid_rule_quest[$i];
				$tmp->ftext = $sf_hid_rule[$i];
				$tmp->alt_ftext = $sf_hid_rule_alt[$i];
				$tmp->priority = $priority[$i];
				$query = "SELECT c.sf_qtext FROM #__survey_force_quests as c WHERE c.id = " . $sf_hid_rule_quest[$i];
				$database->setQuery($query);
				$tmp->sf_qtext = $database->loadResult();
				$lists['sf_fields_rule'][] = $tmp;
			}
		}

		if (!is_array($lists['sf_fields_rule']) || count($lists['sf_fields_rule']) < 1)
			$lists['sf_fields_rule'] = array();

		if ($qtype == 1)
		{
			$row->is_likert_predefined = ($id) ? 0 : 1;
			$row->is_likert_predefined = $is_return ? JFactory::getSession()->get('is_likert_predefined_sf') : $row->is_likert_predefined;
		}

		$type = SurveyforceHelper::getQuestionType($qtype);
		JPluginHelper::importPlugin('survey', $type->sf_plg_name);
		$className = 'plgSurvey' . ucfirst($type->sf_plg_name);

		$data = array();
		$data['id'] = $row->id;
		$data['quest_type'] = $type->sf_plg_name;
		$data['item'] = $row;

		if ($data['quest_type'] == 'pagebreak')
		{
			mosRedirect(JRoute::_(JURI::base() . 'index.php?option=com_surveyforce&view=questions&surv_id=' . $row->sf_survey));
		}

		if (method_exists($className, 'onGetAdminOptions'))
			$questionHTML = $className::onGetAdminOptions($data, $lists, true);


		survey_force_front_html::SF_editQ_PluginShow($row, $lists, $questionHTML, $type);

	}

	public static function SF_saveQuestion($option)
	{
        $database = JFactory::getDbo();
		$row = new mos_Survey_Force_Question($database);
		$jinput = JFactory::getApplication()->input;
		$post = $jinput->post;

        $query = "SHOW COLUMNS FROM `#__survey_force_quests`";
        $database->setQuery($query);
        $fields = $database->loadColumn();

        $question = array();
        if(!empty($fields)) {
            foreach($fields as $field) {
                $question[$field] = $post->get($field, null, 'STRING');
            }
        }

		if (!$row->bind($question)) {
			echo "<script> alert('" . $row->getError() . "'); window.history.go(-1); </script>\n";
			exit();
		}

		if ($row->id < 1) {
			$query = "SELECT MAX(ordering) FROM #__survey_force_quests WHERE sf_survey = {$row->sf_survey}";
			$database->setQuery($query);
			$max_ord = $database->loadResult();
			$row->ordering = $max_ord + 1;
		} else {
			if (SurveyforceHelper::SF_GetUserType(0, $row->id) != 1) {
                mosRedirect(SFRoute("index.php?option=com_surveyforce&view=authoring"));
            }
		}

		$query = "SELECT count(*) FROM #__survey_force_user_answers WHERE quest_id = '" . $row->id . "'";
		$database->setQuery($query);
		$ans_count = $database->loadResult();
		$is_update = false;

		if ($ans_count > 0) {
			$is_update = true;
		}

		if (!$row->check()) {
			echo "<script> alert('" . $row->getError() . "'); window.history.go(-1); </script>\n";
			exit();
		}

		if (!$row->store()) {
			echo "<script> alert('" . $row->getError() . "'); window.history.go(-1); </script>\n";
			exit();
		}

		$row->checkin();
		#$row->updateOrder();
		$qid = $row->id;

		$query = "DELETE FROM #__survey_force_rules WHERE quest_id = '" . $qid . "'";
		$database->setQuery($query);
		$database->execute();

		$rules_ar = array();
		$rules_count = 0;

		$sf_hid_rule = $jinput->get('sf_hid_rule', array(), 'array');
        $sf_hid_rule_alt = $jinput->get('sf_hid_rule_alt', array(), 'array');
        $sf_hid_rule_quest = $jinput->get('sf_hid_rule_quest', array(), 'array');

		$query = "DELETE FROM #__survey_force_quest_show WHERE quest_id = '" . $qid . "'";
		$database->setQuery($query);
		$database->execute();

		$sf_hid_rule2_id = mosGetParam($_REQUEST, 'sf_hid_rule2_id', array());
		$sf_hid_rule2_alt_id = mosGetParam($_REQUEST, 'sf_hid_rule2_alt_id', array());
		$sf_hid_rule2_quest_ids = mosGetParam($_REQUEST, 'sf_hid_rule2_quest_id', array());

		if (is_array($sf_hid_rule2_quest_ids) && count($sf_hid_rule2_quest_ids))
		{
			foreach ($sf_hid_rule2_quest_ids as $ij => $sf_hid_rule2_quest_id)
			{
				$query = "INSERT INTO `#__survey_force_quest_show` (quest_id, survey_id, quest_id_a, answer, ans_field)
				VALUES('" . $qid . "','" . $row->sf_survey . "', '" . $sf_hid_rule2_quest_id . "', '" . (isset($sf_hid_rule2_id[$ij]) ? $sf_hid_rule2_id[$ij] : 0) . "', '" . (isset($sf_hid_rule2_alt_id[$ij]) ? $sf_hid_rule2_alt_id[$ij] : 0) . "')";
				$database->setQuery($query);
				$database->execute();
			}
		}

		$priority = mosGetParam($_REQUEST, 'priority', array());
		if (is_array($sf_hid_rule) && count($sf_hid_rule))
		{
			foreach ($sf_hid_rule as $f_rule)
			{
				$rules_ar[$rules_count]->rul_txt = self::SF_processGetField($f_rule);
				$rules_ar[$rules_count]->answer_id = 0;
				$rules_ar[$rules_count]->rul_txt_alt = self::SF_processGetField((isset($sf_hid_rule_alt[$rules_count]) ? $sf_hid_rule_alt[$rules_count] : 0));
				$rules_ar[$rules_count]->answer_id_alt = 0;
				$rules_ar[$rules_count]->quest_id = isset($sf_hid_rule_quest[$rules_count]) ? $sf_hid_rule_quest[$rules_count] : 0;
				$rules_ar[$rules_count]->priority = isset($priority[$rules_count]) ? $priority[$rules_count] : 0;
				$rules_count++;
			}
		}

		if ($row->sf_qtype == 1)
		{
			$new_scale = array();
            $is_likert_predef = $jinput->getInt('is_likert_predefined', 0);
            $likert_id = $jinput->getInt('sf_likert_scale', 0);
			if ($is_likert_predef && $likert_id)
			{
				$query = "DELETE FROM #__survey_force_scales WHERE quest_id = '" . $qid . "'";
				$database->setQuery($query);
				$database->execute();

				$query = "SELECT * FROM #__survey_force_scales WHERE quest_id = '" . $likert_id . "' ORDER BY ordering";
				$database->setQuery($query);
				$new_scale = $database->loadObjectList();
				$field_order = 0;
				foreach ($new_scale as $f_row)
				{
					$new_field = new mos_Survey_Force_Scale_Field($database);
					$new_field->quest_id = $qid;
					$new_field->stext = $f_row->stext;
					$new_field->ordering = $field_order;
					if (!$new_field->check())
					{
						echo "<script> alert('" . $new_field->getError() . "'); window.history.go(-1); </script>\n";
						exit();
					}
					if (!$new_field->store())
					{
						echo "<script> alert('" . $new_field->getError() . "'); window.history.go(-1); </script>\n";
						exit();
					}
					$j = 0;
					while ($j < $rules_count)
					{
						if ($rules_ar[$j]->rul_txt_alt == $new_field->stext)
						{
							$rules_ar[$j]->answer_id_alt = $new_field->id;
						}
						$j++;
					}

					$field_order++;
				}
			}
			else
			{
				$field_order = 0;
				$scale = $jinput->get('sf_hid_fields_scale', array(), 'array');
                $scale_id = $jinput->get('sf_hid_fields_scale_ids', array(0), 'array');
                $old_scale_id = $jinput->get('old_sf_hid_field_scale_ids', array(0), 'array');
				$old_scale_id = @array_merge(array(0 => 0), $old_scale_id);
				for ($i = 0, $n = count($old_scale_id); $i < $n; $i++)
				{
					if (in_array($old_scale_id[$i], $scale_id))
						unset($old_scale_id[$i]);
				}

				if (count($old_scale_id))
				{
					$query = "DELETE FROM #__survey_force_scales WHERE quest_id = '" . $qid . "' AND id IN ( " . implode(', ', $old_scale_id) . " )";
					$database->setQuery($query);
					$database->execute();
				}


				for ($i = 0, $n = count($scale); $i < $n; $i++)
				{
					$f_row = $scale[$i];
					$new_field = new mos_Survey_Force_Scale_Field($database);
					if (@$scale_id[$i] > 0)
					{
						$new_field->id = $scale_id[$i];
					}
					$new_field->quest_id = $qid;
					$new_field->stext = self::SF_processGetField($f_row);
					$new_field->ordering = $field_order;
					if (!$new_field->check())
					{
						echo "<script> alert('" . $new_field->getError() . "'); window.history.go(-1); </script>\n";
						exit();
					}
					if (!$new_field->store())
					{
						echo "<script> alert('" . $new_field->getError() . "'); window.history.go(-1); </script>\n";
						exit();
					}
					$j = 0;
					while ($j < $rules_count)
					{
						if ($rules_ar[$j]->rul_txt_alt == $new_field->stext)
						{
							$rules_ar[$j]->answer_id_alt = $new_field->id;
						}
						$j++;
					}
					$field_order++;
				}
			}

			$field_order = 0;
            $sf_hid_fields = $jinput->get('sf_hid_fields', array(), 'array');
            $sf_hid_field_ids = $jinput->get('sf_hid_field_ids', array(0), 'array');
            $old_sf_hid_field_ids = $jinput->get('old_sf_hid_field_ids', array(0), 'array');
            $old_sf_hid_field_ids = @array_merge(array(0 => 0), $old_sf_hid_field_ids);

			for ($i = 0, $n = count($old_sf_hid_field_ids); $i < $n; $i++)
			{
				if (in_array($old_sf_hid_field_ids[$i], $sf_hid_field_ids))
					unset($old_sf_hid_field_ids[$i]);
			}


			if (count($old_sf_hid_field_ids))
			{
				$query = "DELETE FROM `#__survey_force_fields` WHERE `quest_id` = '" . $qid . "' AND id IN ( " . implode(', ', $old_sf_hid_field_ids) . " )";
				$database->setQuery($query);
				$database->execute();
			}

			for ($i = 0, $n = count($sf_hid_fields); $i < $n; $i++)
			{
				$f_row = $sf_hid_fields[$i];
				$new_field = new mos_Survey_Force_Field($database);
				if ($sf_hid_field_ids[$i] > 0)
				{
					$new_field->id = (int) $sf_hid_field_ids[$i];
				}
				$new_field->quest_id = $qid;
				$new_field->ftext = self::SF_processGetField($f_row);
				$new_field->alt_field_id = 0;
				$new_field->is_main = 1;
				$new_field->ordering = $field_order;
				$new_field->is_true = 1;//(only for pickone)($f_row == $jinput->get('sf_fields'))?1:0;#(only for pickone)
				if (!$new_field->check())
				{
					echo "<script> alert('" . $new_field->getError() . "'); window.history.go(-1); </script>\n";
					exit();
				}
				if (!$new_field->store(false))
				{
					echo "<script> alert('" . $new_field->getError() . "'); window.history.go(-1); </script>\n";
					exit();
				}

				$j = 0;
				while ($j < $rules_count)
				{
					if ($rules_ar[$j]->rul_txt == $new_field->ftext)
					{
						$rules_ar[$j]->answer_id = $new_field->id;
					}
					$j++;
				}
				$field_order++;
			}

		}
		elseif ($row->sf_qtype == 2)
		{
			$field_order = 0;
            $other_option_cb = \JFactory::getApplication()->input->getInt('other_option_cb', 0);

            $sf_hid_fields = $jinput->get('sf_hid_fields', array(), 'array');
            $sf_hid_field_ids = $jinput->get('sf_hid_field_ids', array(0), 'array');
            $old_sf_hid_field_ids = $jinput->get('old_sf_hid_field_ids', array(0), 'array');
			$old_sf_hid_field_ids = @array_merge(array(0 => 0), $old_sf_hid_field_ids);
			for ($i = 0, $n = count($old_sf_hid_field_ids); $i < $n; $i++)
			{
				if (in_array($old_sf_hid_field_ids[$i], $sf_hid_field_ids))
					unset($old_sf_hid_field_ids[$i]);
			}
			if (count($old_sf_hid_field_ids))
			{
				$query = "DELETE FROM `#__survey_force_fields` WHERE `quest_id` = '" . $qid . "' AND id IN ( " . implode(', ', $old_sf_hid_field_ids) . " )";
				$database->setQuery($query);
				$database->execute();
			}

			for ($i = 0, $n = count($sf_hid_fields); $i < $n; $i++)
			{
				$f_row = $sf_hid_fields[$i];
				$new_field = new mos_Survey_Force_Field($database);
				if ($sf_hid_field_ids[$i] > 0)
				{
					$new_field->id = $sf_hid_field_ids[$i];
				}
				$new_field->quest_id = $qid;
				$new_field->ftext = self::SF_processGetField($f_row);
				$new_field->alt_field_id = 0;
				$new_field->is_main = 1;
				$new_field->ordering = $field_order;
				$new_field->is_true = 1;//(only for pickone)($f_row == $jinput->get('sf_fields'))?1:0;#(only for pickone)
				if (!$new_field->check())
				{
					echo "<script> alert('" . $new_field->getError() . "'); window.history.go(-1); </script>\n";
					exit();
				}
				if (!$new_field->store())
				{
					echo "<script> alert('" . $new_field->getError() . "'); window.history.go(-1); </script>\n";
					exit();
				}
				$j = 0;
				while ($j < $rules_count)
				{
					if ($rules_ar[$j]->rul_txt == $new_field->ftext)
					{
						$rules_ar[$j]->answer_id = $new_field->id;
					}
					$j++;
				}
				$field_order++;
			}

			if ($other_option_cb == 2)
			{
				$other_text = $jinput->get('other_option');
				$other_id = $jinput->getInt('other_op_id', 0);
				$new_field = new mos_Survey_Force_Field($database);
				if ($other_id > 0)
				{
					$new_field->id = $other_id;
				}
				$new_field->quest_id = $qid;
				$new_field->ftext = self::SF_processGetField($other_text);
				$new_field->alt_field_id = 0;
				$new_field->is_main = 0;
				$new_field->ordering = $field_order;
				$new_field->is_true = 1;
				if (!$new_field->check())
				{
					echo "<script> alert('" . $new_field->getError() . "'); window.history.go(-1); </script>\n";
					exit();
				}
				if (!$new_field->store())
				{
					echo "<script> alert('" . $new_field->getError() . "'); window.history.go(-1); </script>\n";
					exit();
				}
				$j = 0;
				while ($j < $rules_count)
				{
					if ($rules_ar[$j]->rul_txt == $new_field->ftext)
					{
						$rules_ar[$j]->answer_id = $new_field->id;
					}
					$j++;
				}
			}
		}
		elseif ($row->sf_qtype == 3)
		{
			$field_order = 0;
			$other_option_cb = $jinput->getInt('other_option_cb', 0);
            $sf_hid_fields = $jinput->get('sf_hid_fields', array(), 'array');
            $sf_hid_field_ids = $jinput->get('sf_hid_field_ids', array(0), 'array');
            $old_sf_hid_field_ids = $jinput->get('old_sf_hid_field_ids', array(0), 'array');
			$old_sf_hid_field_ids = @array_merge(array(0 => 0), $old_sf_hid_field_ids);
			for ($i = 0, $n = count($old_sf_hid_field_ids); $i < $n; $i++)
			{
				if (in_array($old_sf_hid_field_ids[$i], $sf_hid_field_ids))
				{
					unset($old_sf_hid_field_ids[$i]);
				}
			}

			if (count($old_sf_hid_field_ids))
			{
				$query = "DELETE FROM `#__survey_force_fields` WHERE `quest_id` = '" . $qid . "' AND id IN ( " . implode(', ', $old_sf_hid_field_ids) . " )";
				$database->setQuery($query);
				$database->execute();
			}

			for ($i = 0, $n = count($sf_hid_fields); $i < $n; $i++)
			{
				$f_row = $sf_hid_fields[$i];
				$new_field = new mos_Survey_Force_Field($database);
				if ($sf_hid_field_ids[$i] > 0)
				{
					$new_field->id = $sf_hid_field_ids[$i];
				}
				$new_field->quest_id = $qid;
				$new_field->ftext = self::SF_processGetField($f_row);
				$new_field->alt_field_id = 0;
				$new_field->is_main = 1;
				$new_field->ordering = $field_order;
				$new_field->is_true = 1;//(only for pickone)($f_row == $jinput->get('sf_fields'))?1:0;#(only for pickone)
				if (!$new_field->check())
				{
					echo "<script> alert('" . $new_field->getError() . "'); window.history.go(-1); </script>\n";
					exit();
				}
				if (!$new_field->store())
				{
					echo "<script> alert('" . $new_field->getError() . "'); window.history.go(-1); </script>\n";
					exit();
				}
				$j = 0;
				while ($j < $rules_count)
				{
					if ($rules_ar[$j]->rul_txt == $new_field->ftext)
					{
						$rules_ar[$j]->answer_id = $new_field->id;
					}
					$j++;
				}
				$field_order++;
			}

			if ($other_option_cb == 2)
			{
				$other_text = $jinput->get('other_option');
				$other_id = $jinput->getInt('other_op_id', 0);
				$new_field = new mos_Survey_Force_Field($database);
				if ($other_id > 0)
				{
					$new_field->id = $other_id;
				}
				$new_field->quest_id = $qid;
				$new_field->ftext = self::SF_processGetField($other_text);
				$new_field->alt_field_id = 0;
				$new_field->is_main = 0;
				$new_field->ordering = $field_order;
				$new_field->is_true = 1;
				if (!$new_field->check())
				{
					echo "<script> alert('" . $new_field->getError() . "'); window.history.go(-1); </script>\n";
					exit();
				}
				if (!$new_field->store())
				{
					echo "<script> alert('" . $new_field->getError() . "'); window.history.go(-1); </script>\n";
					exit();
				}
				$j = 0;
				while ($j < $rules_count)
				{
					if ($rules_ar[$j]->rul_txt == $new_field->ftext)
					{
						$rules_ar[$j]->answer_id = $new_field->id;
					}
					$j++;
				}
			}
		}
		elseif (($row->sf_qtype == 5) or ($row->sf_qtype == 6))
		{
			$ii = 0;

            $sf_fields = $jinput->get('sf_fields', array(), 'array');
            $sf_field_ids = $jinput->get('sf_field_ids', array(0), 'array');
            $old_sf_field_ids = $jinput->get('old_sf_field_ids', array(0), 'array');
            $sf_alt_fields = $jinput->get('sf_alt_fields', array(), 'array');
            $sf_alt_field_ids = $jinput->get('sf_alt_field_ids', array(0), 'array');
            $old_sf_alt_field_ids = $jinput->get('old_sf_alt_field_ids', array(0), 'array');

			for ($i = 0, $n = count($old_sf_field_ids); $i < $n; $i++)
			{
				if (in_array($old_sf_field_ids[$i], $sf_field_ids))
					unset($old_sf_field_ids[$i]);
			}
			for ($i = 0, $n = count($old_sf_alt_field_ids); $i < $n; $i++)
			{
				if (in_array($old_sf_alt_field_ids[$i], $sf_alt_field_ids))
					unset($old_sf_alt_field_ids[$i]);
			}

			$old_id = @array_merge(array(0 => 0), $old_sf_field_ids, $old_sf_alt_field_ids);

			if (count($old_id))
			{
				$query = "DELETE FROM `#__survey_force_fields` WHERE `quest_id` = '" . $qid . "' AND id IN ( " . implode(', ', $old_id) . " )";
				$database->setQuery($query);
				$database->execute();
			}

			$new_alt_field_nums = array();

			for ($i = 0, $n = count($sf_alt_fields); $i < $n; $i++)
			{
				$f_row = $sf_alt_fields[$i];
				$new_field = new mos_Survey_Force_Field($database);
				if ($sf_alt_field_ids[$i] > 0)
				{
					$new_field->id = $sf_alt_field_ids[$i];
				}
				$new_field->quest_id = $qid;
				$new_field->ftext = self::SF_processGetField($f_row);
				$new_field->alt_field_id = 0;
				$new_field->is_main = 0;
				$new_field->is_true = 1;
				$new_alt_field[$ii]->f_nom = $ii;
				if (!$new_field->check())
				{
					echo "<script> alert('" . $new_field->getError() . "'); window.history.go(-1); </script>\n";
					exit();
				}
				if (!$new_field->store())
				{
					echo "<script> alert('" . $new_field->getError() . "'); window.history.go(-1); </script>\n";
					exit();
				}

				if ($sf_alt_field_ids[$i] > 0)
				{
					$new_alt_field[$ii]->alt_field_id = $sf_alt_field_ids[$i];
				}
				else
				{
					$new_alt_field[$ii]->alt_field_id = $database->insertid();
				}

				$j = 0;
				while ($j < $rules_count)
				{
					if ($rules_ar[$j]->rul_txt_alt == $new_field->ftext)
					{
						$rules_ar[$j]->answer_id_alt = $new_field->id;
					}
					$j++;
				}
				$ii++;
			}
			shuffle($new_alt_field);
			$field_order = 0;

			for ($i = 0, $n = count($sf_fields); $i < $n; $i++)
			{
				$f_row = $sf_fields[$i];
				$jj = 0;
				$alt_f_index = 0;
				foreach ($new_alt_field as $fa_row)
				{
					if ($fa_row->f_nom == $field_order)
					{
						$alt_f_index = $jj;
					}
					$jj++;
				}
				$new_field = new mos_Survey_Force_Field($database);
				if ($sf_field_ids[$i] > 0)
				{
					$new_field->id = $sf_field_ids[$i];
				}
				$new_field->quest_id = $qid;
				$new_field->ftext = self::SF_processGetField($f_row);
				$new_field->alt_field_id = $new_alt_field[$alt_f_index]->alt_field_id;
				$new_field->is_main = 1;
				$new_field->is_true = 1;
				$new_field->ordering = $field_order;
				if (!$new_field->check())
				{
					echo "<script> alert('" . $new_field->getError() . "'); window.history.go(-1); </script>\n";
					exit();
				}
				if (!$new_field->store())
				{
					echo "<script> alert('" . $new_field->getError() . "'); window.history.go(-1); </script>\n";
					exit();
				}
				$field_order++;
				$j = 0;
				while ($j < $rules_count)
				{
					if ($rules_ar[$j]->rul_txt == $new_field->ftext)
					{
						$rules_ar[$j]->answer_id = $new_field->id;
					}
					$j++;
				}
			}
		}
		elseif ($row->sf_qtype == 9)
		{
			$other_option_cb = $jinput->getInt('other_option_cb', 0);
			$other_text = $jinput->get('other_option');
			$other_id = $jinput->getInt('other_op_id', 0);

			$field_order = 0;
            $rank = $jinput->get('sf_hid_rank', array(), 'array');
            $rank_id = $jinput->get('sf_hid_rank_id', array(0), 'array');
            $old_rank_id = $jinput->get('old_sf_hid_rank_id', array(0), 'array');
			for ($i = 0, $n = count($old_rank_id); $i < $n; $i++)
			{
				if (in_array($old_rank_id[$i], $rank_id))
					unset($old_rank_id[$i]);
			}

            $sf_hid_fields = $jinput->get('sf_hid_fields', array(), 'array');
            $sf_hid_field_ids = $jinput->get('sf_hid_field_ids', array(0), 'array');
            $old_sf_hid_field_ids = $jinput->get('old_sf_hid_field_ids', array(0), 'array');
			for ($i = 0, $n = count($old_sf_hid_field_ids); $i < $n; $i++)
			{
				if (in_array($old_sf_hid_field_ids[$i], $sf_hid_field_ids))
					unset($old_sf_hid_field_ids[$i]);
			}
			if ($other_option_cb != 2)
				$old_ids = @array_merge(array(0 => 0), array(0 => $other_id), $old_rank_id, $old_sf_hid_field_ids);
			else
				$old_ids = @array_merge(array(0 => 0), $old_rank_id, $old_sf_hid_field_ids);

			if (count($old_ids))
			{
				$query = "DELETE FROM `#__survey_force_fields` WHERE `quest_id` = '" . $qid . "' AND id IN ( " . implode(', ', $old_ids) . " )";
				$database->setQuery($query);
				$database->execute();
			}


			for ($i = 0, $n = count($rank); $i < $n; $i++)
			{
				$f_row = $rank[$i];
				$new_field = new mos_Survey_Force_Field($database);
				if ($rank_id[$i] > 0)
				{
					$new_field->id = $rank_id[$i];
				}
				$new_field->quest_id = $qid;
				$new_field->ftext = self::SF_processGetField($f_row);
				$new_field->alt_field_id = 0;
				$new_field->is_main = 0;
				$new_field->ordering = $field_order;
				$new_field->is_true = 1;
				if (!$new_field->check())
				{
					echo "<script> alert('" . $new_field->getError() . "'); window.history.go(-1); </script>\n";
					exit();
				}
				if (!$new_field->store())
				{
					echo "<script> alert('" . $new_field->getError() . "'); window.history.go(-1); </script>\n";
					exit();
				}
				$j = 0;
				while ($j < $rules_count)
				{
					if ($rules_ar[$j]->rul_txt_alt == $new_field->ftext)
					{
						$rules_ar[$j]->answer_id_alt = $new_field->id;
					}
					$j++;
				}
				$field_order++;
			}

			$field_order = 0;
			for ($i = 0, $n = count($sf_hid_fields); $i < $n; $i++)
			{
				$f_row = $sf_hid_fields[$i];
				$new_field = new mos_Survey_Force_Field($database);
				if ($sf_hid_field_ids[$i] > 0)
				{
					$new_field->id = $sf_hid_field_ids[$i];
				}
				$new_field->quest_id = $qid;
				$new_field->ftext = self::SF_processGetField($f_row);
				$new_field->alt_field_id = 0;
				$new_field->is_main = 1;
				$new_field->ordering = $field_order;
				$new_field->is_true = 1;//(only for pickone)($f_row == $jinput->get('sf_fields'))?1:0;#(only for pickone)
				if (!$new_field->check())
				{
					echo "<script> alert('" . $new_field->getError() . "'); window.history.go(-1); </script>\n";
					exit();
				}
				if (!$new_field->store())
				{
					echo "<script> alert('" . $new_field->getError() . "'); window.history.go(-1); </script>\n";
					exit();
				}
				$j = 0;
				while ($j < $rules_count)
				{
					if ($rules_ar[$j]->rul_txt == $new_field->ftext)
					{
						$rules_ar[$j]->answer_id = $new_field->id;
					}
					$j++;
				}
				$field_order++;
			}

			if ($other_option_cb == 2)
			{
				$new_field = new mos_Survey_Force_Field($database);
				if ($other_id > 0)
				{
					$new_field->id = $other_id;
				}
				$new_field->quest_id = $qid;
				$new_field->ftext = self::SF_processGetField($other_text);
				$new_field->alt_field_id = 0;
				$new_field->is_main = 1;
				$new_field->ordering = $field_order;
				$new_field->is_true = 2;
				if (!$new_field->check())
				{
					echo "<script> alert('" . $new_field->getError() . "'); window.history.go(-1); </script>\n";
					exit();
				}
				if (!$new_field->store())
				{
					echo "<script> alert('" . $new_field->getError() . "'); window.history.go(-1); </script>\n";
					exit();
				}
				$j = 0;
				while ($j < $rules_count)
				{
					if ($rules_ar[$j]->rul_txt == $new_field->ftext)
					{
						$rules_ar[$j]->answer_id = $new_field->id;
					}
					$j++;
				}
			}
		}

		if (is_array($rules_ar) && count($rules_ar) > 0)
		{
			foreach ($rules_ar as $rule_one)
			{
				if ($rule_one->answer_id)
				{
					$new_rule = new mos_Survey_Force_Rule_Field($database);
					$new_rule->quest_id = $qid;
					$new_rule->next_quest_id = $rule_one->quest_id;
					$new_rule->answer_id = $rule_one->answer_id;

					$new_rule->alt_field_id = $rule_one->answer_id_alt;
					$new_rule->priority = $rule_one->priority;
					if (!$new_rule->check())
					{
						echo "<script> alert('" . $new_rule->getError() . "'); window.history.go(-1); </script>\n";
						exit();
					}
					if (!$new_rule->store())
					{
						echo "<script> alert('" . $new_rule->getError() . "'); window.history.go(-1); </script>\n";
						exit();
					}
				}
			}
		}
		$super_rule = $jinput->getInt('super_rule', 0);
		$sf_quest_list2 = $jinput->getInt('sf_quest_list2', 0);

		if ($super_rule && $sf_quest_list2)
		{
			$new_rule = new mos_Survey_Force_Rule_Field($database);
			$new_rule->quest_id = $qid;
			$new_rule->next_quest_id = $sf_quest_list2;
			$new_rule->answer_id = 9999997;

			$new_rule->alt_field_id = 9999997;
			$new_rule->priority = 1000;
			if (!$new_rule->check())
			{
				echo "<script> alert('" . $new_rule->getError() . "'); window.history.go(-1); </script>\n";
				exit();
			}
			if (!$new_rule->store())
			{
				echo "<script> alert('" . $new_rule->getError() . "'); window.history.go(-1); </script>\n";
				exit();
			}
		}

		$insert_pb = $jinput->getInt('insert_pb', 1);
		$q_id = $jinput->getInt('id', 0);
		if ($q_id == 0 && $insert_pb == 1)
		{
			$sf_survey = intval(JFactory::getApplication()->getUserStateFromRequest("surv_id", 'surv_id', $row->sf_survey));
			$sf_survey = $jinput->getInt('surv_id', JFactory::getSession()->get('list_surv_id', $row->sf_survey));
			$query = "INSERT INTO #__survey_force_quests (sf_survey, sf_qtype, sf_compulsory, sf_qtext, ordering, published, is_final_question) VALUES ({$sf_survey}, 8, 0, 'Page Break', " . ($max_ord + 2) . ", " . $row->published . ", " . $jinput->getInt('is_final_question') . ")";
			$database->setQuery($query);
			$database->execute();
		}

		self::SF_refreshSection($row->sf_section_id);
		self::SF_refreshOrder($row->sf_survey);

		if (JFactory::getApplication()->input->get('task') == 'apply_quest')
		{
			mosRedirect(SFRoute("index.php?option=com_surveyforce&task=edit_quest&cid[]=" . $row->id));
		}
		else
		{
			mosRedirect(SFRoute("index.php?option=com_surveyforce&task=questions&surv_id=" . $row->sf_survey));
		}
	}

	public static function SF_cancelQuestion($option)
	{
        $post = JFactory::getApplication()->input->post;
        $database = JFactory::getDbo();
		$row = new mos_Survey_Force_Question($database);

        $query = "SHOW COLUMNS FROM `#__survey_force_quests`";
        $database->setQuery($query);
        $fields = $database->loadColumn();

        $question = array();
        if(!empty($fields)) {
            foreach($fields as $field) {
                $question[$field] = $post->get($field, null, 'STRING');
            }
        }

		$row->bind($question);
		$row->checkin();
		mosRedirect(SFRoute("index.php?option=com_surveyforce&task=questions"));
	}

	public static function SF_orderQuestion($id, $inc, $option)
	{
		$jinput = JFactory::getApplication()->input;
	    $database = JFactory::getDbo();
		$limit = $jinput->getInt('limit', 0);
		$limitstart = $jinput->getInt('limitstart', 0);
		$survid = $jinput->getInt('surv_id', 0);
		$msg = '';
		$row = new mos_Survey_Force_Question($database);
		$row->load($id);
		if ($limit == 0) $limit = 999999;

		if ($inc < 0)
		{ #orderup
			$query = "SELECT id, ordering, sf_section_id FROM #__survey_force_quests "
				. " WHERE id <> $id AND ordering <= {$row->ordering} " . ($survid ? " AND sf_survey = $survid " : '')
				. " ORDER BY ordering DESC, id DESC LIMIT 1 ";

		}
		elseif ($inc > 0)
		{ #orderdown
			$query = "SELECT id, ordering, sf_section_id FROM #__survey_force_quests "
				. " WHERE id <> $id AND ordering >= {$row->ordering} " . ($survid ? " AND sf_survey = $survid " : '')
				. " ORDER BY ordering, id LIMIT 1 ";

		}
		$database->setQuery($query);
		$r_row = null;
		$r_row = $database->loadObject();
		if ($r_row != null)
		{
			if ($row->sf_section_id == $r_row->sf_section_id)
				$row->move($inc, ($survid ? " sf_survey = $survid " : ''));
			elseif ($row->sf_section_id != $r_row->sf_section_id && $row->sf_section_id == 0)
				$row->moves($inc, " sf_section_id = {$r_row->sf_section_id} " . ($survid ? " AND sf_survey = $survid " : ''));
			elseif ($row->sf_section_id != $r_row->sf_section_id && $row->sf_section_id != 0)
			{
				SF_orderSection($row->sf_section_id, $inc, $option);
				return;
			}
			self::SF_refreshSection($row->sf_section_id);
			self::SF_refreshSection($r_row->sf_section_id);
			self::SF_refreshOrder($row->sf_survey);
			$msg = JText::_('COM_SF_NEW_QUESTION_ORDER_WAS_SAVED');
		}

		mosRedirect(SFRoute("index.php?option=com_surveyforce&task=questions"));
	}

	public static function SF_editSection($id, $option)
	{
		$database = JFactory::getDbo();
		$row = new mos_Survey_Force_Sections($database);
		// load the row from the db table
		$row->load($id);

		if ($id)
		{
			// do stuff for existing records
			$row->checkout(JFactory::getUser()->id);
		}
		else
		{
			// do stuff for new records
			$row->ordering = 0;
			$row->sf_survey_id = intval(JFactory::getApplication()->getUserStateFromRequest("surv_id", 'surv_id', 0));
			$row->sf_survey_id = intval(mosGetParam($_REQUEST, 'surv_id', JFactory::getSession()->get('list_surv_id', 0)));
		}

		$lists = array();
		$query2 = "SELECT * FROM #__survey_force_survs order by sf_name";
		$database->setQuery($query2);
		$sf_survs = $database->loadObjectList();
		$lists['sf_surveys'] = mosHTML::selectList($sf_survs, 'sf_survey_id', 'class="text_area" size="1"', 'id', 'sf_name', $row->sf_survey_id);

		$query2 = "SELECT id AS value, sf_qtext AS text FROM #__survey_force_quests WHERE sf_survey = {$row->sf_survey_id} ORDER BY ordering, id";
		$database->setQuery($query2);
		$questions = $database->loadObjectList();
		foreach ($questions as &$item)
		{
			$item->text = strip_tags($item->text);
			if (strlen($item->text) > 255)
				$item->text = mb_substr($item->text, 0, 255) . '...';
		}
		$selected_q = 0;
		if ($id)
		{
			$query2 = "SELECT id AS value FROM #__survey_force_quests WHERE sf_survey = {$row->sf_survey_id} AND sf_section_id = {$id} ORDER BY ordering, id";
			$database->setQuery($query2);
			$selected_q = $database->loadObjectList();
			if (count($selected_q) < 1)
				$selected_q = 0;
		}
		$no_quest = array();
		$no_quest[] = mosHTML::makeOption('0', ' - ' . JText::_('COM_SF_NO_QUESTIONS') . ' - ');
		$questions = @array_merge($no_quest, $questions);
		$lists['sf_questions'] = mosHTML::selectList($questions, 'sf_quest[]', 'class="text_area" size="5" style="width:300px" multiple="multiple"', 'value', 'text', $selected_q);

		$query = "SELECT a.ordering AS value, a.sf_name AS text"
			. "\n FROM #__survey_force_qsections AS a"
			. ($row->sf_survey_id ? "\n WHERE a.sf_survey_id = '" . $row->sf_survey_id . "' " : '')
			. "\n ORDER BY a.ordering";
		$text_new_order = JText::_("COM_SURVEYFORCE_SF_NEW_ITEM");

		if ($id)
		{
			$order = mosGetOrderingList($query);
			$order = array_slice($order, 1, -1);
			$ordering = mosHTML::selectList($order, 'ordering', 'class="text_area" size="1"', 'value', 'text', intval($row->ordering));
		}
		else
		{
			$ordering = '<input type="hidden" name="ordering" value="' . $row->ordering . '" />' . $text_new_order;
		}
		$lists['ordering'] = $ordering;
		$yes_no[] = mosHTML::makeOption('1', JText::_('COM_SF_YES'));
		$yes_no[] = mosHTML::makeOption('0', JText::_('COM_SF_NO'));
		$lists['addname'] = mosHTML::selectList($yes_no, 'addname', 'class="text_area" size="1" ', 'value', 'text', intval($row->addname));

		survey_force_front_html::SF_editSection($row, $lists, $option);
	}

	public static function SF_saveSection($option)
	{
		$database = JFactory::getDbo();
		$row = new mos_Survey_Force_Sections($database);
        $post = JFactory::getApplication()->input->post;

        $section = array();
        $section['id'] = $post->getInt('id', 0);
        $section['sf_name'] = self::SF_processGetField($post->get('sf_name', ''));
        $section['addname'] = $post->getInt('addname', 0);
        $section['ordering'] = $post->getInt('ordering', 0);
        $section['sf_survey_id'] = $post->getInt('sf_survey_id', 0);

		if (!$row->bind($section)) {
			echo "<script> alert('" . $row->getError() . "'); window.history.go(-1); </script>\n";
			exit();
		}

		if (!$row->check()) {
			echo "<script> alert('" . $row->getError() . "'); window.history.go(-1); </script>\n";
			exit();
		}

		if (!$row->store()) {
			echo "<script> alert('" . $row->getError() . "'); window.history.go(-1); </script>\n";
			exit();
		}

		$row->checkin();

        $questions = JFactory::getApplication()->input->get('sf_quest', array(), 'ARRAY');

		$query = "UPDATE #__survey_force_quests SET sf_section_id = 0 WHERE sf_section_id = {$row->id}";
		$database->setQuery($query);
		$database->execute();

		$query = "UPDATE #__survey_force_quests SET sf_section_id = {$row->id} WHERE id IN ( " . implode(',', $questions) . " )";
		$database->setQuery($query);
		$database->execute();

		self::SF_refreshSection($row->id);
		self::SF_refreshOrder($row->sf_survey_id);

		if (JFactory::getApplication()->input->get('task') == 'apply_section') {
			mosRedirect(SFRoute("index.php?option=com_surveyforce&task=editA_sec&id=" . $row->id));
		} else {
			mosRedirect(SFRoute("index.php?option=com_surveyforce&task=questions&surv_id=" . JFactory::getApplication()->input->getInt('sf_survey_id', 0)));
		}
	}

	public static function SF_orderSection($id, $inc, $option)
	{
		$database = JFactory::getDbo();
		$limit = intval(mosGetParam($_REQUEST, 'limit', 0));
		$limitstart = intval(mosGetParam($_REQUEST, 'limitstart', 0));
		$survid = intval(mosGetParam($_REQUEST, 'surv_id', 0));
		$msg = '';
		$row = new mos_Survey_Force_Sections($database);
		$row->load($id);

		if ($limit == 0) $limit = 999999;
		if ($inc < 0)
		{ #orderup 
			$query = "SELECT id, ordering, sf_section_id FROM #__survey_force_quests "
				. " WHERE sf_section_id <> $id AND ordering <= {$row->ordering} " . ($survid ? " AND sf_survey = $survid " : '')
				. " ORDER BY ordering DESC, id DESC LIMIT 1 ";

		}
		elseif ($inc > 0)
		{ #orderdown 
			$query = "SELECT id, ordering, sf_section_id FROM #__survey_force_quests "
				. " WHERE sf_section_id <> $id AND ordering >= {$row->ordering} " . ($survid ? " AND sf_survey = $survid " : '')
				. " ORDER BY ordering, id LIMIT 1 ";

		}
		$database->setQuery($query);

		$r_row = null;
		$r_row = $database->loadObject();
		if ($r_row != null)
		{
			if ($r_row->sf_section_id == 0)
			{
				$row_quest = new mos_Survey_Force_Question($database);
				$row_quest->load($r_row->id);
				$row_quest->moves(-$inc, " sf_section_id = {$id} " . ($survid ? " AND sf_survey = $survid " : ''));
			}
			elseif ($r_row->sf_section_id != 0)
			{
				$query = "SELECT id FROM #__survey_force_quests WHERE sf_section_id = '$id' ORDER BY ordering, id ";
				$database->setQuery($query);
				$quests = $database->loadObjectList();
				foreach ($quests as $quest)
				{
					$row_quest = new mos_Survey_Force_Question($database);
					$row_quest->load($quest->id);
					$row_quest->moves($inc, " sf_section_id = {$r_row->sf_section_id} " . ($survid ? " AND sf_survey = $survid " : ''));
				}
				$row->move($inc, ($survid ? " sf_survey_id  = $survid " : ''));
			}
			$msg = JText::_('COM_SF_NEW_SECTION_ORDER_WAS_SAVED');
			self::SF_refreshSection($id);
		}

		self::SF_refreshOrder($row->sf_survey_id);


		mosRedirect(SFRoute("index.php?option=com_surveyforce&task=questions&surv_id=" . (int) $survid));
	}

	public static function SF_refreshSection($section_id = 0)
	{
		$database = JFactory::getDbo();
		$query = "SELECT ordering FROM #__survey_force_quests "
			. " WHERE sf_section_id = " . (int) $section_id
			. " ORDER BY ordering, id  LIMIT 1";
		$database->setQuery($query);
		$quest_ord = $database->loadResult();

		$row = new mos_Survey_Force_Sections($database);
		$row->load($section_id);

		if ($quest_ord != $row->ordering)
		{
			$row->ordering = $quest_ord;
			if (!$row->store())
			{
				echo "<script> alert('" . $database->getErrorMsg() . "'); window.history.go(-1); </script>\n";
				exit();
			}
		}

	}

	public static function SF_refreshOrder($sf_survey_id = 0)
	{
		$database = JFactory::getDbo();
		$query = "SELECT id, ordering, sf_section_id FROM #__survey_force_quests "
			. " WHERE sf_survey = " . $sf_survey_id
			. " ORDER BY ordering, id ";
		$database->setQuery($query);
		$questions = $database->loadObjectList();
		if (count($questions) > 0)
		{
			$last_sec = $questions[0]->sf_section_id;
			$sections = array();
			if ($last_sec != 0)
				$sections[$last_sec] = 1;
			$s = 0;
			foreach ($questions as $question)
			{
				if ($question->sf_section_id == $last_sec)
				{
					continue;
				}
				else
				{
					$last_sec = $question->sf_section_id;
					if (!isset($sections[$question->sf_section_id]))
						$sections[$question->sf_section_id] = 0;
					$sections[$question->sf_section_id]++;
				}
			}
			foreach ($sections as $id => $count)
			{
				if ($count > 1 && $id > 0)
				{
					$t = 0;
					foreach ($questions as $question)
					{
						if ($t == 0 && $question->sf_section_id == $id)
						{
							$first_order = $question->ordering;
							$t = 1;
							continue;
						}
						if ($t == 1 && $question->sf_section_id == $id)
						{
							$row = new mos_Survey_Force_Question($database);
							$row->load($question->id);
							$row->moves(-1, " ordering > $first_order AND sf_survey = $sf_survey_id ");
							$first_order = $row->ordering;
						}
					}
				}
			}
			$query = "SELECT id, ordering, sf_section_id FROM #__survey_force_quests "
				. " WHERE sf_survey = " . $sf_survey_id
				. " ORDER BY ordering, id ";
			$database->setQuery($query);
			$questions = $database->loadObjectList();
			$s = 1;
			foreach ($questions as $question)
			{
				$row = new mos_Survey_Force_Question($database);
				$row->load($question->id);
				$row->ordering = $s++;
				if (!$row->store())
				{
					echo "<script> alert('" . $database->getErrorMsg() . "'); window.history.go(-1); </script>\n";
					exit();
				}
			}
		}
	}

	public static function SF_saveOrderQuestion(&$cidz, &$secz)
	{
		$database = JFactory::getDbo();
		$cid = mosGetParam($_REQUEST, 'cid', array());
		if (!is_array($cid))
		{
			$cid = array();
		}

		$sec = mosGetParam($_REQUEST, 'sec', array());
		if (!is_array($sec))
		{
			$sec = array();
		}
		$survid = intval(mosGetParam($_REQUEST, 'surv_id', 0));

		$order = mosGetParam($_REQUEST, 'order', array(0));
		$orderS = mosGetParam($_REQUEST, 'orderS', array(0));

		$query = "SELECT id, ordering FROM #__survey_force_qsections WHERE id NOT IN ('" . @implode("','", $sec) . "') AND sf_survey_id = '{$survid}'";
		$database->setQuery($query);
		$other_sections = $database->loadObjectList();
		if (is_array($other_sections) && count($other_sections))
			foreach ($other_sections as $other_section)
			{
				$sec[] = $other_section->id;
				$orderS[] = $other_section->ordering;
			}

		$query = "SELECT id, ordering FROM #__survey_force_quests WHERE id NOT IN ('" . @implode("','", $cid) . "') AND sf_survey = '{$survid}'";
		$database->setQuery($query);
		$other_quests = $database->loadObjectList();
		if (is_array($other_quests) && count($other_quests))
			foreach ($other_quests as $other_quest)
			{
				$cid[] = $other_quest->id;
				$order[] = $other_quest->ordering;
			}

		$total = count($cid);
		$totalS = count($sec);

		$row = new mos_Survey_Force_Question($database);
		$rowS = new mos_Survey_Force_Sections($database);
		$conditions = array();
		$sf_survey_id = 0;
		//sort order and cid
		$tmp = array($order, $cid);
		array_multisort($tmp[0], SORT_ASC, SORT_NUMERIC,
			$tmp[1], SORT_ASC, SORT_NUMERIC);
		$order = $tmp[0];
		$cid = $tmp[1];

		$order_t = array();
		$cid_t = array();
		$type = array();
		foreach ($cid as $i => $id)
		{
			$row->load($id);
			if ($row->sf_section_id == 0)
			{
				$order_t[] = $order[$i];
				$cid_t[] = $cid[$i];
				$type[] = 0;
			}
		}

		// update ordering values	
		for ($i = 0; $i < $totalS; $i++)
		{
			$rowS->load($sec[$i]);
			$order_t[] = $orderS[$i];
			$cid_t[] = $sec[$i];
			$type[] = $sec[$i];
			if ($rowS->ordering != $orderS[$i])
			{
				$rowS->ordering = $orderS[$i];
				if (!$rowS->store())
				{
					echo "<script> alert('" . $database->getErrorMsg() . "'); window.history.go(-1); </script>\n";
					exit();
				}
			}
		}

		$tmp = array($order_t, $cid_t, $type);
		array_multisort($tmp[0], SORT_ASC, SORT_NUMERIC,
			$tmp[1], SORT_ASC, SORT_NUMERIC,
			$tmp[2], SORT_ASC, SORT_NUMERIC);
		$order_t = $tmp[0];
		$cid_t = $tmp[1];
		$type = $tmp[2];

		$order_max = $order_t[0];

		for ($i = 0, $n = count($cid_t); $i < $n; $i++)
		{
			if ($type[$i] == 0)
			{
				$row->load($cid_t[$i]);
				$sf_survey_id = $row->sf_survey;
				$row->ordering = $order_max++;
				if (!$row->store())
				{
					echo "<script> alert('" . $database->getErrorMsg() . "'); window.history.go(-1); </script>\n";
					exit();
				}
			}
			else
			{
				for ($j = 0, $m = count($cid); $j < $m; $j++)
				{
					$row->load($cid[$j]);
					$sf_survey_id = $row->sf_survey;
					if ($row->sf_section_id == $type[$i])
					{
						$row->ordering = $order_max++;
						if (!$row->store())
						{
							echo "<script> alert('" . $database->getErrorMsg() . "'); window.history.go(-1); </script>\n";
							exit();
						}
					}
				}
			}
		}
		self::SF_refreshOrder($sf_survey_id);
		$msg = JText::_('COM_SF_NEW_QUESTION_ORDER_WAS_SAVED');

		mosRedirect(SFRoute("index.php?option=com_surveyforce&task=questions"));
	}

	public static function SF_changeQuestion($option, $cid = null, $state = 0)
	{
		$database = JFactory::getDbo();
		$surveyid = strval(mosGetParam($_REQUEST, 'surv_id', 0));
		if ((is_array($cid) && count($cid) > 0))
		{
			if (!is_array($cid) || count($cid) < 1)
			{
				mosRedirect(SFRoute("index.php?option=com_surveyforce&task=questions&surv_id=$surveyid"));
			}
		}
		if (!is_array($cid) || count($cid) < 1)
		{
			echo "<script> alert('" . JText::_('COM_SF_SELECT_AN_ITEM_TO') . JFactory::getApplication()->input->get('task') . "'); window.history.go(-1);</script>\n";
			exit();
		}

		$cids = implode(',', $cid);

		$query = "UPDATE #__survey_force_quests"
			. "\n SET published = " . intval($state)
			. "\n WHERE id IN ( $cids )";
		$database->setQuery($query);
		if (!$database->execute())
		{
			echo "<script> alert('" . $database->getErrorMsg() . "'); window.history.go(-1); </script>\n";
			exit();
		}
		mosRedirect(SFRoute("index.php?option=com_surveyforce&task=questions&surv_id=$surveyid"));
	}

	public static function SF_moveQuestionSelect($option, $cid, $sec)
	{
		$database = JFactory::getDbo();
		if (!is_array($cid) || count($cid) < 1)
		{
			echo "<script> alert('" . JText::_('COM_SF_SELECT_AN_ITEM_TO_MOVE') . "'); window.history.go(-1);</script>\n";
			exit;
		}

		## query to list selected questions
		$cids = implode(',', $cid);
		$secs = implode(',', $sec);
		$query = "SELECT CONCAT('Section: ', a.sf_name) AS sf_qtext, b.sf_name AS survey_name"
			. "\n FROM #__survey_force_qsections AS a LEFT JOIN #__survey_force_survs AS b ON b.id = a.sf_survey_id "
			. "\n WHERE a.id IN ( $secs )";
		if (!empty($secs))
		{
			$database->setQuery($query);
			$items = $database->loadObjectList();
		}
		else
			$items = array();

		$query = "SELECT a.sf_qtext, b.sf_name as survey_name"
			. "\n FROM #__survey_force_quests AS a LEFT JOIN #__survey_force_survs AS b ON b.id = a.sf_survey"
			. "\n WHERE a.id IN ( $cids )";
		$database->setQuery($query);
		$items = @array_merge($items, $database->loadObjectList());

		## query to choose survey to move to
		$query = "SELECT a.sf_name AS text, a.id AS value"
			. "\n FROM #__survey_force_survs AS a"
			. (JFactory::getUser()->get('usertype') != 'Super Administrator' ? " WHERE sf_author = '" . JFactory::getUser()->id . "' " : " ")
			. "\n ORDER BY a.sf_name";
		$database->setQuery($query);
		$surveys = $database->loadObjectList();

		// build the html select list
		$SurveyList = mosHTML::selectList($surveys, 'surveymove', 'class="text_area" size="10"', 'value', 'text', null);
		survey_force_front_html::SF_moveQ_Select($option, $cid, $sec, $SurveyList, $items);
	}

	public static function SF_moveQuestionSave($cid, $sec)
	{
		$database = JFactory::getDbo();
		$surveyMove = strval(mosGetParam($_REQUEST, 'surveymove', ''));

		$cids = implode(',', $cid);
		$total = count($cid);

		$query = "UPDATE #__survey_force_quests"
			. "\n SET sf_survey = '$surveyMove'"
			. "WHERE id IN ( $cids )";
		$database->setQuery($query);
		if (!$database->execute())
		{
			echo "<script> alert('" . $database->getErrorMsg() . "'); window.history.go(-1); </script>\n";
			exit();
		}

		$query = "DELETE FROM #__survey_force_quest_show WHERE quest_id IN ( $cids )";
		$database->setQuery($query);
		$database->execute();

		$surveyNew = new mos_Survey_Force_Survey ($database);
		$surveyNew->load($surveyMove);

		if (count($sec))
		{
			$secs = implode(',', $sec);
			$query = "UPDATE #__survey_force_qsections SET sf_survey_id = " . $surveyMove
				. "\n WHERE id IN ( $secs )";
			$database->setQuery($query);
			if (!$database->execute())
			{
				echo "<script> alert('" . $database->getErrorMsg() . "'); window.history.go(-1); </script>\n";
			}
		}
		self::SF_refreshOrder($surveyMove);
		$msg = $total . JText::_('COM_SF_QUESTIONS_MOVED_TO') . $surveyNew->sf_name;
		mosRedirect(SFRoute("index.php?option=com_surveyforce&task=questions"));
	}

	public static function sfGetOrderingList($sql, $chop = '55')
	{
		$database = JFactory::getDbo();
		$order = array();
		$database->setQuery($sql);
		if (!($orders = $database->loadObjectList()))
		{
			if ($database->getErrorNum())
			{
				echo $database->stderr();
				return false;
			}
			else
			{
				$order[] = mosHTML::makeOption(1, JText::_('COM_SF_FIRST'));
				return $order;
			}
		}
		$order[] = mosHTML::makeOption(0, '0 ' . JText::_('COM_SF_FIRST'));
		for ($i = 0, $n = count($orders); $i < $n; $i++)
		{
			$orders[$i]->text = strip_tags($orders[$i]->text);
			if (strlen($orders[$i]->text) > $chop)
			{
				$text = mb_substr($orders[$i]->text, 0, $chop) . "...";
			}
			else
			{
				$text = $orders[$i]->text;
			}

			$order[] = mosHTML::makeOption($orders[$i]->value, $orders[$i]->value . ' (' . $text . ')');
		}
		$order[] = mosHTML::makeOption($orders[$i - 1]->value + 1, ($orders[$i - 1]->value + 1) . JText::_('COM_SF_LAST'));

		return $order;
	}

	public static function SF_new_question_type()
	{
		$new_qtype_id = intval(JFactory::getApplication()->getUserStateFromRequest("new_qtype_id", 'new_qtype_id', 0));

		$lang = JFactory::getLanguage();
		$lang->load('com_surveyforce', JPATH_BASE . '/administrator');
		?>
		<link href="<?php echo JURI::root(); ?>administrator/templates/isis/css/template.css" rel="stylesheet"
		      type="text/css"/>
		<style type="text/css">
			label {
				cursor: pointer;
			}
		</style>
		<script>
			Joomla.submitbutton = function (pressbutton) {
				var form = document.getElementById('questionTypesForm');
				if (pressbutton == 'cancel') {
					return;
				}

				if (pressbutton == 'add_new') {
					if (form.new_qtype_id.value == '') {
						alert('Please, select question type!');
						return false;
					}
					switch (form.new_qtype_id.value) {
						case '1':
							pressbutton = 'add_likert';
							break;
						case '2':
							pressbutton = 'add_pickone';
							break;
						case '3':
							pressbutton = 'add_pickmany';
							break;
						case '4':
							pressbutton = 'add_short';
							break;
						case '5':
							pressbutton = 'add_drp_dwn';
							break;
						case '6':
							pressbutton = 'add_drg_drp';
							break;
						case '7':
							pressbutton = 'add_boilerplate';
							break;
						case '8':
							pressbutton = 'add_pagebreak';
							break;
						case '9':
							pressbutton = 'add_ranking';
							break;
					}
				}

				form.task.value = pressbutton;
				form.submit();
			}
		</script>
		<form action="<?php echo JRoute::_("index.php?option=com_surveyforce") ?>" method="post" name="adminForm" id="questionTypesForm" target="_parent">

			<fieldset class="adminform">
				<legend><?php echo JText::_('COM_SF_SELECT_NEW_QUESTION_TYPE'); ?></legend>

				<button type="button" class="btn btn-primary" onclick="Joomla.submitbutton('add_new');"><?php echo JText::_('COM_SF_NEXT');?></button>

				<button type="button" class="btn btn-danger" onclick="window.parent.SqueezeBox.close();"><?php echo JText::_('COM_SF_CANCEL');?></button>

				<div class="clearfix"></div><br/>

				<table width="100%" cellpadding="2" cellspacing="2" class="admintable">
					<tr>
						<td width="50%">
							<label for="new_qtype_id_1">
								<input type="radio" name="new_qtype_id" id="new_qtype_id_1" value="1" <?php echo($new_qtype_id == 1 ? ' checked="checked" ' : '') ?> />
								<?php echo JText::_('COM_SF_LIKERTSCALE'); ?>
							</label>
						</td>
						<td>
							<label for="new_qtype_id_2">
								<input type="radio" name="new_qtype_id" id="new_qtype_id_2" value="2" <?php echo($new_qtype_id == 2 ? ' checked="checked" ' : '') ?> />
								<?php echo JText::_('COM_SF_PICKONE'); ?>
							</label>
						</td>
					</tr>
					<tr>
						<td width="50%">
							<label for="new_qtype_id_3">
								<input type="radio" name="new_qtype_id" id="new_qtype_id_3" value="3" <?php echo($new_qtype_id == 3 ? ' checked="checked" ' : '') ?> />
								<?php echo JText::_('COM_SF_PICKMANY'); ?>
							</label>
						</td>
						<td>
							<label for="new_qtype_id_4">
								<input type="radio" name="new_qtype_id" id="new_qtype_id_4" value="4" <?php echo($new_qtype_id == 4 ? ' checked="checked" ' : '') ?> />
								<?php echo JText::_('COM_SF_SHORTANSWER'); ?>
							</label>
						</td>
					</tr>
					<tr>
						<td width="50%">
							<label for="new_qtype_id_5">
								<input type="radio" name="new_qtype_id" id="new_qtype_id_5" value="5" <?php echo($new_qtype_id == 5 ? ' checked="checked" ' : '') ?> />
								<?php echo JText::_('COM_SF_RANKING_DROPDOWN'); ?>
							</label>
						</td>
						<td>
							<label for="new_qtype_id_6">
								<input type="radio" name="new_qtype_id" id="new_qtype_id_6" value="6" <?php echo($new_qtype_id == 6 ? ' checked="checked" ' : '') ?> />
								<?php echo JText::_('COM_SF_RANKING_DRAGNDROP'); ?>
							</label>
						</td>
					</tr>
					<tr>
						<td width="50%">
							<label for="new_qtype_id_7">
								<input type="radio" name="new_qtype_id" id="new_qtype_id_7" value="7" <?php echo($new_qtype_id == 7 ? ' checked="checked" ' : '') ?> />
								<?php echo JText::_('COM_SF_BOILERPLATE'); ?>
							</label>
						</td>
						<td>
							<label for="new_qtype_id_8">
								<input type="radio" name="new_qtype_id" id="new_qtype_id_8" value="8" <?php echo($new_qtype_id == 8 ? ' checked="checked" ' : '') ?> />
								<?php echo JText::_('COM_SF_PAGE_BREAK'); ?>
							</label>
						</td>
					</tr>
					<tr>
						<td width="50%">
							<label for="new_qtype_id_9">
								<input type="radio" name="new_qtype_id" id="new_qtype_id_9" value="9" <?php echo($new_qtype_id == 9 ? ' checked="checked" ' : '') ?> />
								<?php echo JText::_('COM_SF_S_RANKING'); ?>
							</label>
						</td>
						<td>&nbsp;</td>
					</tr>

				</table>
			</fieldset>

			<input type="hidden" name="boxchecked" value="0"/>
			<input type="hidden" name="c_id" value="0"/>
			<input type="hidden" name="task" value=""/>
		</form>
	<?php
	}

	public static function SF_setDefault($id, $option)
	{
		$database = JFactory::getDbo();
		$row = new mos_Survey_Force_Question($database);
		$row->load($id);

		$lists = array();
		$lists['answer_data'] = array();

		$query = "SELECT * FROM `#__survey_force_fields` WHERE `quest_id` = '" . $row->id . "' AND is_main = '1' ORDER BY ordering";
		$database->setQuery($query);
		$lists['main_data'] = $database->loadObjectList();

		$query = "SELECT * FROM `#__survey_force_fields` WHERE `quest_id` = '" . $row->id . "' AND is_main = '0' ORDER BY ordering";
		$database->setQuery($query);
		$lists['second_data'] = $database->loadObjectList();

		$query = "SELECT * FROM `#__survey_force_scales` WHERE `quest_id` = '" . $row->id . "' ORDER BY ordering";
		$database->setQuery($query);
		$lists['scale_data'] = $database->loadObjectList();

		$byKey = false;
		if ($row->sf_qtype <= 3)
			$byKey = 'answer';

		$query = "SELECT * FROM `#__survey_force_def_answers` WHERE `quest_id` = '" . $row->id . "' ";
		$database->setQuery($query);
		$lists['answer_data'] = $database->loadAssocList('answer');

		$return = array();
		$data = array();

		$lists['sf_qtype'] = $row->sf_qtype;
		$lists['row'] = $row;

		$data['id'] = $row->id;

		$type = SurveyforceHelper::getQuestionType($row->sf_qtype);

		$lists['sf_qtype_plugin'] = $type->sf_plg_name;

		JPluginHelper::importPlugin('survey', $type->sf_plg_name);
		$className = 'plgSurvey' . ucfirst($lists['sf_qtype_plugin']);

		if (method_exists($className, 'onGetSetDefault'))
			$return = $className::onGetSetDefault($data);

		if (count($return))
		{
			$lists = array_merge($lists, $return);
		}

		survey_force_front_html::SF_showSetDefault($row, $lists, $option);
	}

	public static function SF_saveDefault($option, $quest_id = 0)
	{
        if(isset($_SESSION['qid'])) {
            unset($_SESSION['qid']);
        }

        $jinput = JFactory::getApplication()->input;
        $data = unserialize($jinput->serialize())[1];

        $sf_qtype = $jinput->get('sf_qtype');

        $database = JFactory::getDbo();

		$query = "SELECT sf_survey FROM `#__survey_force_quests` WHERE `id` = $quest_id ";
		$database->setQuery($query);
		$survey_id = $database->loadResult();

		$data['survey_id'] = $survey_id;

		if ($quest_id > 0 && $survey_id > 0) {
			$query = "DELETE FROM `#__survey_force_def_answers` WHERE `survey_id` = $survey_id AND quest_id = $quest_id ";
			$database->setQuery($query);
			$database->execute();

			$type = SurveyforceHelper::getQuestionType($sf_qtype);
			JPluginHelper::importPlugin('survey', $type->sf_plg_name);
			$className = 'plgSurvey' . ucfirst($type->sf_plg_name);

			if (method_exists($className, 'onSaveDefault')) {
                $className::onSaveDefault($data);
            }
		}

		mosRedirect(SFRoute("index.php?option=com_surveyforce") . '?task=edit_quest&cid[0]=' . $quest_id);
	}

	public static function SF_cancelDefault($id, $option)
	{
		mosRedirect(SFRoute("index.php?option=com_surveyforce&task=edit_quest&cid[0]=$id"));
	}
}
