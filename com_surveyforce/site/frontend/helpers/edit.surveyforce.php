<?php
/**
 * Survey Force component for Joomla
 * @version    $Id: edit.surveyforce.php 2009-11-16 17:30:15
 * @package    Survey Force
 * @subpackage edit.surveyforce.php
 * @author     JoomPlace Team
 * @Copyright  Copyright (C) JoomPlace, www.joomplace.com
 * @license    GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */

defined('_JEXEC') or die;

// Component Helper
jimport('joomla.application.component.helper');

class SurveyforceEditHelper extends SurveyforceHelper
{

	public $database;
	protected $document;

	public function __construct()
	{
		$this->document = JFactory::getDocument();
	}

	public function SF_getOptions()
	{
		$quest_id = (int) mosGetParam($_REQUEST, 'quest_id', '0');

		if ($quest_id)
		{
			$quest = new mos_Survey_Force_Question($this->database);
			$quest->load($quest_id);
			$return = '';

			if ($quest->sf_qtype == 1)
			{
				$query = "SELECT id AS value, stext AS text FROM `#__survey_force_scales` WHERE quest_id = '" . $quest_id . "' ORDER BY ordering";
				$this->database->SetQuery($query);
				$f_scale_data = ($this->database->LoadObjectList() == null ? array() : $this->database->LoadObjectList());

				$f_scale_data = mosHTML::selectList($f_scale_data, 'f_scale_data', 'class="text_area" id="f_scale_data" size="1" ', 'value', 'text', null);

				$query = "SELECT id AS value, ftext AS text FROM `#__survey_force_fields` WHERE quest_id = '" . $quest_id . "' ORDER BY ordering";
				$this->database->SetQuery($query);
				$f_fields_data = ($this->database->LoadObjectList() == null ? array() : $this->database->LoadObjectList());

				$i = 0;
				while ($i < count($f_fields_data))
				{
					$f_fields_data[$i]->text = strip_tags($f_fields_data[$i]->text);
					if (strlen($f_fields_data[$i]->text) > 55)
						$f_fields_data[$i]->text = mb_substr($f_fields_data[$i]->text, 0, 55) . '...';
					$f_fields_data[$i]->text = $f_fields_data[$i]->value . ' - ' . $f_fields_data[$i]->text;
					$i++;
				}

				$f_fields_data = mosHTML::selectList($f_fields_data, 'sf_field_data_m', 'class="text_area" id="sf_field_data_m" size="1" ', 'value', 'text', null);
				$return = ' ' . JText::_('COM_SURVEYFORCE_SF_AND_OPTION') . ' "' . $f_fields_data . '"  ' . JText::_('COM_SURVEYFORCE_SF_ANSWER_IS') . ' "' . $f_scale_data . '" <input type="hidden" name="sf_qtype2" id="sf_qtype2" value="' . $quest->sf_qtype . '"/>';
			}
			elseif ($quest->sf_qtype == 2 || $quest->sf_qtype == 3)
			{

				$query = "SELECT id AS value, ftext AS text FROM `#__survey_force_fields` WHERE quest_id = '" . $quest_id . "' ORDER BY ordering";
				$this->database->SetQuery($query);
				$f_fields_data = ($this->database->LoadObjectList() == null ? array() : $this->database->LoadObjectList());

				$i = 0;
				while ($i < count($f_fields_data))
				{
					$f_fields_data[$i]->text = strip_tags($f_fields_data[$i]->text);
					if (strlen($f_fields_data[$i]->text) > 55)
						$f_fields_data[$i]->text = mb_substr($f_fields_data[$i]->text, 0, 55) . '...';
					$f_fields_data[$i]->text = $f_fields_data[$i]->value . ' - ' . $f_fields_data[$i]->text;
					$i++;
				}

				$f_fields_data = mosHTML::selectList($f_fields_data, 'sf_field_data_m', 'class="text_area" id="sf_field_data_m" size="1" ', 'value', 'text', null);
				$return = ' ' . JText::_('COM_SURVEYFORCE_SF_ANSWER_IS') . ' "' . $f_fields_data . '" <input type="hidden" name="sf_qtype2" id="sf_qtype2" value="' . $quest->sf_qtype . '"/>';
			}
			elseif ($quest->sf_qtype == 5 || $quest->sf_qtype == 6)
			{

				$query = "SELECT id AS value, ftext AS text FROM `#__survey_force_fields` WHERE quest_id = '" . $quest_id . "' AND is_main = 1 ORDER BY ordering";
				$this->database->SetQuery($query);
				$f_fields_data = ($this->database->LoadObjectList() == null ? array() : $this->database->LoadObjectList());

				$i = 0;
				while ($i < count($f_fields_data))
				{
					$f_fields_data[$i]->text = strip_tags($f_fields_data[$i]->text);
					if (strlen($f_fields_data[$i]->text) > 55)
						$f_fields_data[$i]->text = mb_substr($f_fields_data[$i]->text, 0, 55) . '...';
					$f_fields_data[$i]->text = $f_fields_data[$i]->value . ' - ' . $f_fields_data[$i]->text;
					$i++;
				}

				$f_fields_data = mosHTML::selectList($f_fields_data, 'sf_field_data_m', 'class="text_area" id="sf_field_data_m" size="1" ', 'value', 'text', null);

				$query = "SELECT id AS value, ftext AS text FROM `#__survey_force_fields` WHERE quest_id = '" . $quest_id . "' AND is_main = 0 ORDER BY ordering";
				$this->database->SetQuery($query);
				$f_fields_data2 = ($this->database->LoadObjectList() == null ? array() : $this->database->LoadObjectList());

				$i = 0;
				while ($i < count($f_fields_data2))
				{
					$f_fields_data2[$i]->text = strip_tags($f_fields_data2[$i]->text);
					if (strlen($f_fields_data2[$i]->text) > 55)
						$f_fields_data2[$i]->text = mb_substr($f_fields_data2[$i]->text, 0, 55) . '...';
					$f_fields_data2[$i]->text = $f_fields_data2[$i]->value . ' - ' . $f_fields_data2[$i]->text;
					$i++;
				}

				$f_fields_data2 = mosHTML::selectList($f_fields_data2, 'sf_field_data_a', 'class="text_area" id="sf_field_data_a" size="1" ', 'value', 'text', null);
				$return = ' ' . JText::_('COM_SURVEYFORCE_SF_AND_OPTION') . ' "' . $f_fields_data . '" ' . JText::_('COM_SURVEYFORCE_SF_ANSWER_IS') . ' "' . $f_fields_data2 . '" <input type="hidden" name="sf_qtype2" id="sf_qtype2" value="' . $quest->sf_qtype . '"/>';
			}
			elseif ($quest->sf_qtype == 9)
			{

				$query = "SELECT id AS value, ftext AS text FROM `#__survey_force_fields` WHERE quest_id = '" . $quest_id . "' AND is_main = 1 ORDER BY ordering";
				$this->database->SetQuery($query);
				$f_fields_data = ($this->database->LoadObjectList() == null ? array() : $this->database->LoadObjectList());

				$i = 0;
				while ($i < count($f_fields_data))
				{
					$f_fields_data[$i]->text = strip_tags($f_fields_data[$i]->text);
					if (strlen($f_fields_data[$i]->text) > 55)
						$f_fields_data[$i]->text = mb_substr($f_fields_data[$i]->text, 0, 55) . '...';
					$f_fields_data[$i]->text = $f_fields_data[$i]->value . ' - ' . $f_fields_data[$i]->text;
					$i++;
				}

				$f_fields_data = mosHTML::selectList($f_fields_data, 'sf_field_data_m', 'class="text_area" id="sf_field_data_m" size="1" ', 'value', 'text', null);
				$query = "SELECT id AS value, ftext AS text FROM `#__survey_force_fields` WHERE quest_id = '" . $quest_id . "' AND is_main = 0 ORDER BY ordering";
				$this->database->SetQuery($query);
				$f_fields_data2 = ($this->database->LoadObjectList() == null ? array() : $this->database->LoadObjectList());

				$i = 0;
				while ($i < count($f_fields_data2))
				{
					$f_fields_data2[$i]->text = strip_tags($f_fields_data2[$i]->text);
					if (strlen($f_fields_data2[$i]->text) > 55)
						$f_fields_data2[$i]->text = mb_substr($f_fields_data2[$i]->text, 0, 55) . '...';
					$f_fields_data2[$i]->text = $f_fields_data2[$i]->value . ' - ' . $f_fields_data2[$i]->text;
					$i++;
				}

				$f_fields_data2 = mosHTML::selectList($f_fields_data2, 'sf_field_data_a', 'class="text_area" id="sf_field_data_a" size="1" ', 'value', 'text', null);
				$return = ' ' . JText::_('COM_SURVEYFORCE_SF_AND_OPTION') . ' "' . $f_fields_data . '" ' . JText::_('COM_SURVEYFORCE_SF_RANK_IS') . ' "' . $f_fields_data2 . '" <input type="hidden" name="sf_qtype2" id="sf_qtype2" value="' . $quest->sf_qtype . '"/>';
			}
			@ob_end_clean();
			@ob_end_clean();

			@header('Expires: Fri, 14 Mar 1980 20:53:00 GMT');
			@header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT');
			@header('Cache-Control: no-cache, must-revalidate');
			@header('Pragma: no-cache');
			@header('Content-Type: text/xml charset=' . _ISO);
			@ob_end_clean();
			@ob_end_clean();

			echo '<?xml version="1.0" standalone="yes"?>';
			echo '<response>' . "\n";
			echo "\t" . '<data><![CDATA[';
			echo $return . "<script  type=\"text/javascript\" language=\"javascript\">jQuery('input#add_button').get(0).style.display = '';</script>";
			echo ']]></data>' . "\n";
			echo '</response>' . "\n";
			exit();
		}
	}

	function clearPreviews()
	{
		$database = JFactory::getDbo();
		$query = "SELECT `start_id` FROM `#__survey_force_previews` WHERE `time` < '" . (strtotime(JFactory::getDate()) - 4000) . "'";
		$database->SetQuery($query);
		$start_ids = $database->loadColumn();

		if (is_array($start_ids) && count($start_ids) > 0)
		{
			$start_id_str = implode("','", $start_ids);

			$database->setQuery("DELETE FROM #__survey_force_previews WHERE start_id IN ( '{$start_id_str}' )");
			$database->execute();

			$database->setQuery("DELETE FROM #__survey_force_user_chain WHERE start_id IN ( '{$start_id_str}' )");
			$database->execute();

			$database->setQuery("DELETE FROM #__survey_force_user_answers WHERE start_id IN ( '{$start_id_str}' )");
			$database->execute();

			$database->setQuery("DELETE FROM #__survey_force_user_ans_txt WHERE start_id IN ( '{$start_id_str}' )");
			$database->execute();

			$database->setQuery("DELETE FROM #__survey_force_user_answers_imp WHERE start_id IN ( '{$start_id_str}' )");
			$database->execute();
		}
	}

	function SF_uploadImage($option)
	{

		$userfile_name = (isset($_FILES['userfile']['name']) ? $_FILES['userfile']['name'] : "");
		$directory = 'surveyforce';
		if (isset($_FILES['userfile']))
		{
			$base_Dir = JPATH_ROOT . "/media/com_surveyforce/";;

			if (empty($userfile_name))
			{
				echo "<script>alert('" . JText::_('COM_SF_PLEASE_SELECT_AN_IMAGE_TO_UPLOAD') . "'); document.location.href='index.php?no_html=1&amp;option=com_surveyforce&amp;task=uploadimage&amp;directory=" . $directory . "&t=" . $css . "';</script>";
			}

			$filename = explode(".", $userfile_name);

			if (preg_match('/[^0-9a-zA-Z_]/', $filename[0]))
			{
				mosErrorAlert(JText::_('COM_SF_FILE_MUST_CONTAIN_ONLY_ALPHANUMERIC'));
			}

			if (file_exists($base_Dir . $userfile_name))
			{
				mosErrorAlert(JText::_('COM_SF_S_IMAGE') . $userfile_name . JText::_('COM_SF_ALREADY_EXISTS'));
			}

			if ((strcasecmp(mb_substr($userfile_name, -4), ".gif")) && (strcasecmp(mb_substr($userfile_name, -4), ".jpg")) && (strcasecmp(mb_substr($userfile_name, -4), ".png")) && (strcasecmp(mb_substr($userfile_name, -4), ".bmp")))
			{
				mosErrorAlert(JText::_('COM_SF_THE_FILE_MUST_BE_GIF'));
			}

			if (!move_uploaded_file($_FILES['userfile']['tmp_name'], $base_Dir . $_FILES['userfile']['name']) || !mosChmod($base_Dir . $_FILES['userfile']['name']))
			{
				mosErrorAlert(JText::_('COM_SF_UPLOAD_OF') . ' ' . $userfile_name . ' ' . JText::_('COM_SF_FAILED'));
			}
			else
			{
				mosErrorAlert(JText::_('COM_SF_UPLOAD_OF') . ' ' . $userfile_name . ' ' . JText::_('COM_SF_SUCCESSFULL'), 'window.opener.location.reload(); window.close();');
			}
		}

		survey_force_front_html::SF_uploadImage($option);
	}
}