<?php

/**
 * Survey Force Deluxe component for Joomla 3 3
 * @package   Survey Force Deluxe
 * @author    JoomPlace Team
 * @Copyright Copyright (C) JoomPlace, www.joomplace.com
 * @license   GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */
defined('_JEXEC') or die;

/**
 * Surveyforce component helper.
 */
class SurveyforceHelper
{

	public static function getVersion()
	{
		$xml = JFactory::getXML(JPATH_COMPONENT_ADMINISTRATOR .'/surveyforce.xml');
		return (string)$xml->version;
	}

	public function SF_processGetField($field_text)
	{
		$field_text = (get_magic_quotes_gpc()) ? stripslashes($field_text) : $field_text;
		$field_text = str_replace('"', '&quot;', $field_text);
		$field_text = str_replace("'", '&#039;', $field_text);

		return $field_text;
	}

	public static function getNotifyUserEmails()
	{
		return array();
	}

	public static function showTitle($submenu, $addition = false)
	{
		$document = JFactory::getDocument();
		$title = JText::_('COM_SURVEYFORCE_' . strtoupper($submenu));
		$document->setTitle($title . ($addition ? ' ' . $addition : ''));
		JToolBarHelper::title($title . ($addition ? ' ' . $addition : ''), $submenu);

		return $title;
	}

	public static function getCSSJS()
	{
		$document = JFactory::getDocument();
		$document->addStyleSheet(JURI::root() . 'administrator/components/com_surveyforce/assets/css/surveyforce.css');
	}

	public static function getCodepress()
	{
		$document = JFactory::getDocument();
		$document->addScript(JURI::root() . 'administrator/components/com_surveyforce/assets/codepress/codepress.js');
	}

	public static function getQuestionType($new_qtype_id)
	{
		$db = JFactory::getDBO();
		$db->setQuery("SELECT `sf_plg_name` FROM `#__survey_force_qtypes` WHERE `id`=" . $new_qtype_id);
		$type = $db->loadResult();

		return $type;
	}

	public function listQuestionTypes()
	{
		$db = JFactory::getDbo();

		$query = $db->getQuery(true);
		$query->select('sf_qtype, sf_plg_name');
		$query->from('#__survey_force_qtypes');
		$query->group('sf_plg_name');
		$db->setQuery($query);
		$arrayTypes = $db->loadObjectList();

		return $arrayTypes;
	}

	public static function getLeftMenu()
	{
		jimport('joomla.html.html.bootstrap');
		JHtml::_('bootstrap.framework');
		$view = JFactory::getApplication()->input->getCmd('plugin', '');
		$task = JFactory::getApplication()->input->getCmd('view');
		$list_plugins = SurveyforceHelper::listQuestionTypes();
		$task = $view ? $view : $task;

		$menu = '<ul class="nav nav-list">
                        <li' . ($task == 'configuration' ? ' class="active"' : '') . '>
                            <a href="index.php?option=com_surveyforce&view=configuration">' . JText::_('COM_SURVEYFORCE_CONFIGURATION') . '</a>
                        </li><hr>';

		foreach ($list_plugins as $plugin)
		{
			$menu .= ' <li' . ($task == $plugin->sf_plg_name ? ' class="active"' : '') . '>
                            <a href="index.php?option=com_surveyforce&view=configuration&plugin=' . $plugin->sf_plg_name . '">' . JText::_($plugin->sf_qtype) . '</a>
                        </li>';
		}


		$menu .= '</ul>';

		return $menu;
	}

	public static function SF_refreshSection($section_id = 0)
	{
		$database = JFactory::getDBO();

		$query = "SELECT ordering FROM `#__survey_force_quests` "
			. " WHERE sf_section_id = " . $section_id
			. " ORDER BY ordering, id  LIMIT 1";
		$database->setQuery($query);
		$quest_ord = $database->loadResult();

		$row = JTable::getInstance('Section', 'SurveyforceTable', array());
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

		return true;
	}

	public static function SF_refreshOrder($sf_survey_id)
	{
		$database = JFactory::getDBO();
		$query = "SELECT id, ordering, sf_section_id FROM `#__survey_force_quests` "
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

			if (count($sections))
			{
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

								$quest_field = JTable::getInstance('Question', 'SurveyforceTable', array());
								$quest_field->load($question->id);

								$quest_field->move(-1, " ordering > $first_order AND sf_survey = $sf_survey_id ");
								$first_order = $quest_field->ordering;
							}
						}
					}
				}
			}

			$query = "SELECT id, ordering, sf_section_id FROM `#__survey_force_quests` "
				. " WHERE sf_survey = " . $sf_survey_id
				. " ORDER BY ordering, id ";
			$database->setQuery($query);
			$questions = $database->loadObjectList();

			$s = 1;
			foreach ($questions as $question)
			{

				$database->setQuery("SELECT * FROM `#__survey_force_quests` WHERE `id` = '" . $question->id . "'");
				$object = $database->loadObject();
				$object->ordering = $s++;

				if (!$database->updateObject('#__survey_force_quests', $object, 'id'))
				{
					echo "<script> alert('" . $database->getErrorMsg() . "'); window.history.go(-1); </script>\n";
					exit();
				}

			}
		}

		return true;

	}

	public static function sfGetOrderingList($sql, $chop = '55')
	{
		$database = JFactory::getDBO();

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
				$order[] = JHTML::_('select.option', 1, JText::_('COM_SURVEYFORCE_FIRST'));
				return $order;
			}
		}
		$order[] = JHTML::_('select.option', 0, '0 ' . JText::_('COM_SURVEYFORCE_FIRST'));
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

			$order[] = JHTML::_('select.option', $orders[$i]->value, $orders[$i]->value . ' (' . $text . ')');
		}
		$order[] = JHTML::_('select.option', $orders[$i - 1]->value + 1, ($orders[$i - 1]->value + 1) . JText::_('COM_SURVEYFORCE_LAST'));

		return $order;
	}

	public static function addFileUploadFull($uploadHandler = 'server/php/', $fileInputId = 'fileupload', $preloadImages = array())
	{
		$document = JFactory::getDocument();
		JHTML::stylesheet(JURI::root() . 'components/com_surveyforce/assets/bootstrap/css/font-awesome.css');
		JHTML::script(JURI::root() . 'components/com_surveyforce/assets/jplace.jquery.js');
		JHTML::script(JURI::root() . 'components/com_surveyforce/assets/bootstrap/js/bootstrap.min.js');
		JHTML::stylesheet(JURI::root() . 'components/com_surveyforce/assets/file-upload/css/jquery.fileupload-ui.css');
		JHTML::script(JURI::root() . 'components/com_surveyforce/assets/file-upload/js/vendor/jquery.ui.widget.js');
		JHTML::script(JURI::root() . 'administrator/components/com_surveyforce/assets/js/tmpl.min.js');
		JHTML::script(JURI::root() . 'administrator/components/com_surveyforce/assets/js/load-image.min.js');
		JHTML::script(JURI::root() . 'administrator/components/com_surveyforce/assets/js/canvas-to-blob.min.js');
		JHTML::script(JURI::root() . 'components/com_surveyforce/assets/file-upload/js/jquery.iframe-transport.js');
		JHTML::script(JURI::root() . 'components/com_surveyforce/assets/file-upload/js/jquery.fileupload.js');
		JHTML::script(JURI::root() . 'components/com_surveyforce/assets/file-upload/js/jquery.fileupload-fp.js');
		JHTML::script(JURI::root() . 'components/com_surveyforce/assets/file-upload/js/jquery.fileupload-ui.js');
		$document->addCustomTag('<!--[if gte IE 8]><script src="' . JURI::root() . 'components/com_surveyforce/assets/file-upload/js/cors/jquery.xdr-transport.js"></script><![endif]-->');
		$uploadInit = "
                                <script type='text/javascript'>
                                (function($) {
                                    $(document).ready(function () {
                                  $('#$fileInputId').fileupload({
                                      // Uncomment the following to send cross-domain cookies:
                                      //xhrFields: {withCredentials: true},
                                      url: '$uploadHandler',
                                      formData: {task: 'images.addImage'}
                                  });
                                  var result = " . json_encode($preloadImages) . ";
                                  if (result && result.length) {
                                      $('#$fileInputId').fileupload('option', 'done').call($('#$fileInputId'), null, {result: result});
                                  }
                                    });
                                })(jplace.jQuery);
                                </script>
                                ";
		$document->addCustomTag($uploadInit);
	}

	//for CSV import
	function SF_prepareImport(&$loader, &$fieldDescriptors)
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
		$loader->setFieldNames($fieldNames); // set the "normalized" field names
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

	function SF_prepareImportRow(&$loader, &$fieldDescriptors, $values, $requiredFieldNames, $allFieldNames)
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


	function SF_processCSVField($field_text)
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


	function get_html_translation_table_my()
	{
		$trans = get_html_translation_table(HTML_ENTITIES);
		$trans[chr(130)] = '&sbquo;'; // Single Low-9 Quotation Mark
		$trans[chr(131)] = '&fnof;'; // Latin Small Letter F With Hook
		$trans[chr(132)] = '&bdquo;'; // Double Low-9 Quotation Mark
		$trans[chr(133)] = '&hellip;'; // Horizontal Ellipsis
		$trans[chr(134)] = '&dagger;'; // Dagger
		$trans[chr(135)] = '&Dagger;'; // Double Dagger
		$trans[chr(136)] = '&circ;'; // Modifier Letter Circumflex Accent
		$trans[chr(137)] = '&permil;'; // Per Mille Sign
		$trans[chr(138)] = '&Scaron;'; // Latin Capital Letter S With Caron
		$trans[chr(139)] = '&lsaquo;'; // Single Left-Pointing Angle Quotation Mark
		$trans[chr(140)] = '&OElig;    '; // Latin Capital Ligature OE
		$trans[chr(145)] = '&lsquo;'; // Left Single Quotation Mark
		$trans[chr(146)] = '&rsquo;'; // Right Single Quotation Mark
		$trans[chr(147)] = '&ldquo;'; // Left Double Quotation Mark
		$trans[chr(148)] = '&rdquo;'; // Right Double Quotation Mark
		$trans[chr(149)] = '&bull;'; // Bullet
		$trans[chr(150)] = '&ndash;'; // En Dash
		$trans[chr(151)] = '&mdash;'; // Em Dash
		$trans[chr(152)] = '&tilde;'; // Small Tilde
		$trans[chr(153)] = '&trade;'; // Trade Mark Sign
		$trans[chr(154)] = '&scaron;'; // Latin Small Letter S With Caron
		$trans[chr(155)] = '&rsaquo;'; // Single Right-Pointing Angle Quotation Mark
		$trans[chr(156)] = '&oelig;'; // Latin Small Ligature OE
		$trans[chr(159)] = '&Yuml;'; // Latin Capital Letter Y With Diaeresis
		ksort($trans);
		return $trans;
	}

	function clearOldImages($day = null)
	{

		$image_path = JPATH_ROOT . "/media/com_surveyforce/gen_images/";

		if ($day == null)
			$day = (strlen(date('d')) < 2 ? '0' . date('d') : date('d'));
		elseif (strlen($day) < 2)
			$day = '0' . $day;

		$current_dir = opendir($image_path);
		$old_umask = umask(0);
		while (false !== ($entryname = readdir($current_dir)))
		{
			if ($entryname != '.' and $entryname != '..')
			{
				if (!is_dir($image_path . $entryname) && mb_substr($entryname, 0, 2) != $day)
				{
					@chmod($image_path . $entryname, 0757);
					unlink($image_path . $entryname);
				}
			}
		}
		umask($old_umask);
		closedir($current_dir);
	}

	function SF_draw_grid($options = array())
	{
		// Add values to the graph
		$graphValues = explode(',', $options['grids']);
		$kol_items = count($graphValues);

		$imgWidth = 250;
		$imgHeight = 25 * $kol_items;
		$imgLineWdth = 25;
		$imgLineWdth_Offset = 3;
		$max_value = intval($options['total']);

		for ($i = 0; $i < $kol_items; $i++)
		{
			if ($max_value != 0)
			{
				$graphValues[$i] = intval($graphValues[$i] * $imgWidth / $max_value);
			}
			if ($graphValues[$i] >= $imgWidth) $graphValues[$i] = $imgWidth - 1;
		}
		// Create image and define colors
		$image = imagecreate($imgWidth, $imgHeight);
		$colorWhite = imagecolorallocate($image, 255, 255, 255);
		$colorGrey = imagecolorallocate($image, 192, 192, 192);
		$colorDarkBlue = imagecolorallocate($image, 104, 157, 228);
		$colorLightBlue = imagecolorallocate($image, 184, 212, 250);
		// Create border around image
		imageline($image, 0, 0, 0, $imgHeight, $colorGrey);
		imageline($image, 0, 0, $imgWidth, 0, $colorGrey);
		imageline($image, $imgWidth - 1, 0, $imgWidth - 1, $imgHeight - 1, $colorGrey);
		imageline($image, 0, $imgHeight - 1, $imgWidth - 1, $imgHeight - 1, $colorGrey);
		// Create grid
		for ($i = 1; $i < 11; $i++)
		{
			$stw = ($i * 25 > $imgWidth) ? $imgWidth : $i * 25;
			$sth = ($i * 25 > $imgHeight) ? $imgHeight : $i * 25;
			imageline($image, $stw, 0, $stw, $imgHeight, $colorGrey);
			imageline($image, 0, $sth, $imgWidth, $sth, $colorGrey);
		}
		// Create bar charts
		for ($i = 0; $i < $kol_items; $i++)
		{
			if ($graphValues[$i] > 0)
			{
				imagefilledrectangle($image, 0, (($i) * $imgLineWdth) + $imgLineWdth_Offset, $graphValues[$i], (($i + 1) * $imgLineWdth) - $imgLineWdth_Offset, $colorDarkBlue);
				imagefilledrectangle($image, 1, (($i) * $imgLineWdth) + $imgLineWdth_Offset + 1, $graphValues[$i] - 1, (($i + 1) * $imgLineWdth) - $imgLineWdth_Offset - 1, $colorLightBlue);
			}
		}
		// Output graph and clear image from memory

		imagepng($image, $options['fileName']);
		imagedestroy($image);
	}

	function SF_PrintRepSurv_List($survey_data, $questions_data, $is_pc = 0)
	{

		self::clearOldImages();
		/*
		 * Create the pdf document
		 */
		require_once(JPATH_COMPONENT_ADMINISTRATOR . '/assets/tcpdf/sf_pdf.php');

		$pdf_doc = new sf_pdf();

		$pdf = & $pdf_doc->_engine;

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

		$pdf->SetLeftMargin($pdf_doc->_margin_left);
		$options = array('total' => $survey_data->total_starts,
			'grids' => $survey_data->total_starts . ',' . $survey_data->total_gstarts . ','
				. $survey_data->total_rstarts . ',' . $survey_data->total_istarts . ',' . $survey_data->total_completes . ','
				. $survey_data->total_gcompletes . ',' . $survey_data->total_rcompletes . ',' . $survey_data->total_icompletes,
			'fileName' => JPATH_ROOT . "/media/com_surveyforce/gen_images/" . (strlen(date('d')) < 2 ? '0' . date('d') : date('d')) . '_' . md5(uniqid(mktime())) . '.png');
		self::SF_draw_grid($options);
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
								'fileName' => JPATH_ROOT . "/media/com_surveyforce/gen_images/" . (strlen(date('d')) < 2 ? '0' . date('d') : date('d')) . '_' . md5(uniqid(mktime())) . '.png');
							self::SF_draw_grid($options);
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
							'fileName' => JPATH_ROOT . "/media/com_surveyforce/gen_images/" . (strlen(date('d')) < 2 ? '0' . date('d') : date('d')) . '_' . md5(uniqid(mktime())) . '.png');
						self::SF_draw_grid($options);
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
							'fileName' => JPATH_ROOT . "/media/com_surveyforce/gen_images/" . (strlen(date('d')) < 2 ? '0' . date('d') : date('d')) . '_' . md5(uniqid(mktime())) . '.png');
						self::SF_draw_grid($options);
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
				$total = ($is_pc ? 1 : $total);
				$rrr = count($tmp_data);

				$text_to_pdf = $pdf_doc->cleanText($qrow->iscale_name);
				$pdf->SetFontSize(10);

				$pdf->Write(5, $text_to_pdf, '', 0);
				$pdf->Ln();

				$pdf->SetLeftMargin($pdf_doc->_margin_left);
				$options = array('total' => $total,
					'grids' => implode(',', $tmp_data),
					'fileName' => JPATH_ROOT . "/media/com_surveyforce/gen_images/" . (strlen(date('d')) < 2 ? '0' . date('d') : date('d')) . '_' . md5(uniqid(mktime())) . '.png');
				self::SF_draw_grid($options);
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

	public static function addCategoriesSubmenu($vName)
	{
		JHtmlSidebar::addEntry(
			JText::_('COM_SURVEYFORCE_LIST_OF_CATEGORIES'),
			'index.php?option=com_surveyforce&view=categories',
			$vName == 'categories'
		);
		JHtmlSidebar::addEntry(
			JText::_('COM_SURVEYFORCE_NEW_CATEGORY'),
			'index.php?option=com_surveyforce&task=category.add',
			$vName == 'new_category');
	}

	public static function addSurveysSubmenu($vName)
	{
		JHtmlSidebar::addEntry(
			JText::_('COM_SURVEYFORCE_LIST_OF_SURVEYS'),
			'index.php?option=com_surveyforce&view=surveys',
			$vName == 'surveys'
		);
		JHtmlSidebar::addEntry(
			JText::_('COM_SURVEYFORCE_IMPORTANCE_SCALES2'),
			'index.php?option=com_surveyforce&view=iscales',
			$vName == 'iscales');
		JHtmlSidebar::addEntry(
			JText::_('COM_SURVEYFORCE_NEW_SURVEY'),
			'index.php?option=com_surveyforce&task=survey.add',
			$vName == 'survey_add');
	}

	public static function addUserlistSubmenu($vName)
	{
		JHtmlSidebar::addEntry(
			JText::_('COM_SURVEYFORCE_MANAGE_USERS'),
			'index.php?option=com_surveyforce&view=listusers',
			$vName == 'listusers'
		);
		JHtmlSidebar::addEntry(
			JText::_('COM_SURVEYFORCE_NEW_LIST_OF_USERS'),
			'index.php?option=com_surveyforce&task=listuser.add',
			$vName == 'listuser_add');
		JHtmlSidebar::addEntry(
			JText::_('COM_SURVEYFORCE_GENERATE_INVITATIONS'),
			'index.php?option=com_surveyforce&view=invitations',
			$vName == 'invitations');
	}

	public static function addAuthorsSubmenu($vName)
	{
		JHtmlSidebar::addEntry(
			JText::_('COM_SURVEYFORCE_LIST_OF_AUTHORS'),
			'index.php?option=com_surveyforce&view=authors',
			$vName == 'authors'
		);
		JHtmlSidebar::addEntry(
			JText::_('COM_SURVEYFORCE_ADD_AUTHOR'),
			'index.php?option=com_surveyforce&task=authors.usersList',
			$vName == 'authors_add');
	}

	public static function addReportsSubmenu($vName)
	{
		JHtmlSidebar::addEntry(
			JText::_('COM_SURVEYFORCE_REPORTS'),
			'index.php?option=com_surveyforce&view=reports',
			$vName == 'reports'
		);
		JHtmlSidebar::addEntry(
			JText::_('COM_SURVEYFORCE_ADVANCED_REPORTS'),
			'index.php?option=com_surveyforce&view=advreport',
			$vName == 'advreport');
	}

	public static function addConfigurationSubmenu($vName)
	{
		JHtmlSidebar::addEntry(
			JText::_('COM_SURVEYFORCE_MANAGE_EMAILS'),
			'index.php?option=com_surveyforce&view=emails',
			$vName == 'emails'
		);
		JHtmlSidebar::addEntry(
			JText::_('COM_SURVEYFORCE_NEW_EMAIL'),
			'index.php?option=com_surveyforce&task=email.add',
			$vName == 'email_add');

		JHtmlSidebar::addEntry(
			JText::_('COM_SURVEYFORCE_TEMPLATES'),
			'index.php?option=com_surveyforce&view=templates',
			$vName == 'templates');
	}
}