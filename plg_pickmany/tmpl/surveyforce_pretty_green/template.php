<?php

/**
 * Survey Force Deluxe Pickmany Plugin for Joomla 3
 * @package Joomla.Plugin
 * @subpackage Survey.pickmany
 * @author JoomPlace Team
 * @Copyright Copyright (C) JoomPlace, www.joomplace.com
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */
// no direct access
defined('_JEXEC') or die('Restricted access');

class SF_PickmanyTemplate {

	static $question;
	static $iscale;

	/*     * ******************************************************************************************************************* *
	 *
	 * {QUESTION_TEXT} - question text
	 * {ANSWERS} - There will be placed block of answers
	 * {IMPORTANCE_SCALE} - Importance scale
	 *
	 * ********************************************************************************************************************* */

	public static function QuestionBody() {
		$return_str = <<<EOFTMPL
			<div align="left" style="padding-left:10px;text-align:left;">{QUESTION_TEXT}</div>
			<div>{ANSWERS}</div>
			{IMPORTANCE_SCALE}
EOFTMPL;
		//remove new line characters
		$return_str = str_replace("\n", '', $return_str);
		$return_str = str_replace("\r", '', $return_str);
		return $return_str;
	}

	public static function getQuestion() {
		$question = SF_PickmanyTemplate::parserBodyQuestion();
		$document = JFactory::getDocument();

		return $question;
	}

	public static function parserBodyQuestion() {

		$body = SF_PickmanyTemplate::QuestionBody();
		$vars = array();

		preg_match_all("/\{[A-Z0-9]{1,}_{0,}[A-Z0-9]{1,}\}/i", $body, $vars);

		foreach ($vars[0] as $var) {

			$function_name = 'SF_PickmanyTemplate::Question' . ucfirst(strtolower(str_replace('}', '', str_replace('{', '', $var))));
			$replace = call_user_func($function_name);
			$body = str_replace($var, $replace, $body);
		}

		return $body;
	}

	/*     * ******************************************************************************************************************** *
	 *
	 * This shall be defined functions for replace {VARIABLES}
	 *
	 * ********************************************************************************************************************** */

	public static function QuestionQuestion_text() {

		return SF_PickmanyTemplate::$question->sf_qtext;
	}

	public static function QuestionAnswers() {

		$acount = SF_PickmanyTemplate::$iscale['alt_fields_count'];
		$mcount = SF_PickmanyTemplate::$iscale['main_fields_count'];
		$ans_count = SF_PickmanyTemplate::$iscale['ans_count'];
		$return_str = '';

		$return_str = '<div align="left" class="pick_many_div">' .
			'<form name="quest_form' . SF_PickmanyTemplate::$question->id . '">' .
			'<br/>' .
			'<table class="pick_many_table" id="quest_table">';

		$selected = '';
		for ($i = 0; $i < $mcount; $i++) {
			$selected = '';
			if ($ans_count > 0) {
				for ($ii = 0; $ii < $ans_count; $ii++) {
					if (SF_PickmanyTemplate::$iscale['answers'][$ii]['a_quest_id'] == SF_PickmanyTemplate::$iscale['mfield'][$i]['mfield_id'])
						$selected = " checked='checked' ";
				}
			}
			$return_str .= '<tr><td class="pm_answer_cell">' .
				'<input onclick="javascript: return check_num_opt(' . SF_PickmanyTemplate::$question->id . ', this)" onchange="javascript:  if (check_num_opt(' . SF_PickmanyTemplate::$question->id . ', this)) check_answer(' . SF_PickmanyTemplate::$question->id . ');" class="pm_checkbox" type="checkbox" name="quest_check' . SF_PickmanyTemplate::$question->id . '" id="quest_check' . SF_PickmanyTemplate::$question->id . $i . '" value="' . SF_PickmanyTemplate::$iscale['mfield'][$i]['mfield_id'] . '" ' . $selected . '>' .
				'<label onclick="javascript: return check_num_opt(' . SF_PickmanyTemplate::$question->id . ', this);" for="quest_check' . SF_PickmanyTemplate::$question->id . $i . '">' .
				SF_PickmanyTemplate::$iscale['mfield'][$i]['mfield_text'] .
				'</label>' .
				'<br/>' .
				'</td>' .
				'</tr>';
		}

		if ($acount > 0) {
			$selected = '';
			$other_val = '';
			if ($ans_count > 0) {
				for ($ii = 0; $ii < $ans_count; $ii++) {
					if (SF_PickmanyTemplate::$iscale['answers'][$ii]['a_quest_id'] == SF_PickmanyTemplate::$iscale['afield'][0]['afield_id'])
						$selected = " checked='checked' ";
				}
				$other_val = SF_PickmanyTemplate::$iscale['ans_txt'];
				if ($other_val == '!!!---!!!')
					$other_val = '';
			}
			$return_str .= '<tr>' .
				'<td class="pm_answer_cell">' .
				'<input onkeypress = "return preventKeyPress(event, 13);" onclick="javascript: return check_num_opt(' . SF_PickmanyTemplate::$question->id . ', this)" onchange="javascript: if (check_num_opt(' . SF_PickmanyTemplate::$question->id . ', this)) check_answer(' . SF_PickmanyTemplate::$question->id . ');" type="checkbox" class="pm_checkbox" name="quest_check' . SF_PickmanyTemplate::$question->id . '" id="quest_check' . SF_PickmanyTemplate::$question->id . 'e" value="' .SF_PickmanyTemplate::$iscale['afield'][0]['afield_id'] . '" ' . $selected . '>' .
				'<label onclick="javascript: return check_num_opt(' . SF_PickmanyTemplate::$question->id . ', this)" for="quest_check' . SF_PickmanyTemplate::$question->id . 'e">' .
				SF_PickmanyTemplate::$iscale['afield'][0]['afield_text'] .
				'</label>' .
				'&nbsp;<input onkeypress = "return preventKeyPress(event, 13);" class="pm_other" type="text" id="other_op_' . SF_PickmanyTemplate::$question->id . '" name="other_op_' . SF_PickmanyTemplate::$question->id . '" value="' . $other_val . '"/>' .
				'<br/>' .
				'</td>' .
				'</tr>';
		}
		$return_str .= '</table></form></div>';


		return $return_str;
	}

	public static function QuestionImportance_scale() {

		$return_str = '';
		$ans_imp_count = SF_PickmanyTemplate::$iscale['ans_imp_count'];
		$iscale_name = SF_PickmanyTemplate::$iscale['impscale_name'];
		$iscount = count(SF_PickmanyTemplate::$iscale['isfield']);
		if ($iscount) {
			$return_str = '<div align="left" class="importance_div">' .
				'<form name="iscale_form'.SF_PickmanyTemplate::$question->id.'">'.
				'<br/>' .
				'<br/>' .
				'<table class="importance_table" id="iscale_table" cellpadding="0" cellspacing="0">';
			$return_str = $return_str . '<tr class="sectiontableentry2"><td class="i_quest" colspan="' . $iscount . '" >&nbsp;&nbsp;' . $iscale_name . '</td></tr>';
			$return_str = $return_str . '<tr class="sectiontableentry1">';

			for ($j = 0; $j < $iscount; $j++) {
				$return_str = $return_str . '<td class="i_text_cell" onclick="javascript: sf_getObj(\'iscale_radio' . SF_PickmanyTemplate::$question->id . '_' . $j . '\').checked=\'checked\';">' .
					'<label for="iscale_radio' . SF_PickmanyTemplate::$question->id . '_' . $j . '" style="cursor: pointer;">' .
					SF_PickmanyTemplate::$iscale['isfield'][$j]['isfield_text'] .
					'</label>' .
					'</td>';
			}

			$return_str = $return_str . '</tr>';
			$return_str = $return_str . '<tr class="sectiontableentry2">';
			$selected = '';

			for ($j = 0; $j < $iscount; $j++) {
				$selected = '';
				if ($ans_imp_count > 0) {
					if (SF_PickmanyTemplate::$iscale['isfield'][$j]['isfield_id'] == SF_PickmanyTemplate::$iscale['ans_imp_id']) {
						$selected = " checked='checked' ";
					}
				}
				$return_str = $return_str . '<td class="i_ans_cell" onclick="javascript: sf_getObj(\'iscale_radio' . SF_PickmanyTemplate::$question->id . '_' . $j . '\').checked=\'checked\';">' .
					'<input class="i_radio" type="radio" name="iscale_radio' . SF_PickmanyTemplate::$question->id . '" id="iscale_radio' . SF_PickmanyTemplate::$question->id . '_' . $j . '" value="' . SF_PickmanyTemplate::$iscale['isfield'][$j]['isfield_id'] . '" ' . $selected . '/>' .
					'<label for="iscale_radio' . SF_PickmanyTemplate::$question->id . '_' . $j . '" style="cursor: pointer;">' .
					SF_PickmanyTemplate::$iscale['isfield'][$j]['isfield_text'] .
					'</label>' .
					'</td>';
			}

			$return_str = $return_str . "</tr>";
			$return_str = $return_str . "</table></form></div>";
		}

		return $return_str;
	}

}