<?php

/**
 * Survey Force Deluxe Pickone Plugin for Joomla 3
 * @package Joomla.Plugin
 * @subpackage Survey.pickone
 * @author JoomPlace Team
 * @Copyright Copyright (C) JoomPlace, www.joomplace.com
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */
// no direct access
defined('_JEXEC') or die('Restricted access');

class SF_PickoneTemplate {

	static $question;
	static $iscale;

	/*     * ******************************************************************************************************************* *
	 *
	 * {QUESTION_TEXT} - question text
	 * {ANSWERS} - There will be placed block of answers
	 * {IMPORTANCE_SCALE} - Importance scale
	 *
	 * ********************************************************************************************************************* */

	public function QuestionBody() {
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

	public function getQuestion() {
		$question = SF_PickoneTemplate::parserBodyQuestion();
		$document = JFactory::getDocument();

		return $question;
	}

	public function parserBodyQuestion() {


		$body = SF_PickoneTemplate::QuestionBody();
		$vars = array();

		preg_match_all("/\{[A-Z0-9]{1,}_{0,}[A-Z0-9]{1,}\}/i", $body, $vars);

		foreach ($vars[0] as $var) {

			$function_name = 'SF_PickoneTemplate::Question' . ucfirst(strtolower(str_replace('}', '', str_replace('{', '', $var))));
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

	public function QuestionQuestion_text() {

		return SF_PickoneTemplate::$question->sf_qtext;
	}

	public function QuestionAnswers() {

		$acount = SF_PickoneTemplate::$iscale['alt_fields_count'];
		$mcount = SF_PickoneTemplate::$iscale['main_fields_count'];
		$ans_count = SF_PickoneTemplate::$iscale['ans_count'];
		$quest_style = SF_PickoneTemplate::$question->sf_qstyle;
		$selected = '';

		//if dropdown list style
		if ($quest_style == 1) {
			if ($acount > 0) {
				$selected = " selected='selected' ";
			}
			$return_str = '<br/>'.
				'<div class="pick_one_div">'.
				'<form name="quest_form'.SF_PickoneTemplate::$question->id.'">'.
				'<select onchange="javascript: check_answer('.SF_PickoneTemplate::$question->id.');" class="po_select" name="quest_select_po_'.SF_PickoneTemplate::$question->id.'" id="quest_select_po_'.SF_PickoneTemplate::$question->id.'">'.
				'<option value="0" '.$selected.'>'.JText::_('COM_SURVEYFORCE_SELECT_ANS').'</option>';
		} else {
			//if radiobuttons style
			$return_str = '<div align="left" class="pick_one_div">' .
				'<form name="quest_form'.SF_PickoneTemplate::$question->id.'">'.
				'<br/>'.
				'<table id="quest_table" class="pick_one_table" >';
		}


		for ($i = 0; $i < $mcount; $i++) {
			$selected = '';

			if ($ans_count > 0) {
				if (SF_PickoneTemplate::$iscale['answers'][0]['a_quest_id'] == SF_PickoneTemplate::$iscale['mfield'][$i]['mfield_id']) {
					if ($quest_style == 1)
						$selected = " selected='selected' ";
					else
						$selected = " checked='checked' ";
				}
			}

			if ($quest_style == 1) {
				$return_str = $return_str . '<option value="'. SF_PickoneTemplate::$iscale['mfield'][$i]['mfield_id']. '" '. $selected. ' >'.
					SF_PickoneTemplate::$iscale['mfield'][$i]['mfield_text'].
					'</option>';
			} else {
				$return_str = $return_str.
					'<tr>'.
					'<td class="po_answer_cell">'.
					'<input onchange="javascript: check_answer('. SF_PickoneTemplate::$question->id. ');" class="po_radio" type="radio" name="quest_radio'. SF_PickoneTemplate::$question->id. '" id="quest_radio'. SF_PickoneTemplate::$question->id. $i. '" value="'. SF_PickoneTemplate::$iscale['mfield'][$i]['mfield_id']. '" '. $selected. '>'.
					'<label for="quest_radio'. SF_PickoneTemplate::$question->id. $i. '">'. SF_PickoneTemplate::$iscale['mfield'][$i]['mfield_text']. '</label>'.
					'<br/>'.
					'</td>'.
					'</tr>';
			}
		}

		if ($acount > 0) {
			$selected = '';
			$other_val = '';
			if ($ans_count > 0) {
				if (SF_PickoneTemplate::$iscale['answers'][0]['a_quest_id'] == SF_PickoneTemplate::$iscale['afield'][0]['afield_id']) {
					if ($quest_style == 1)
						$selected = " selected='selected' ";
					else
						$selected = " checked='checked' ";
				}
				$other_val = SF_PickoneTemplate::$iscale['ans_txt'];
				if ($other_val == '!!!---!!!') {
					$other_val = '';
				}
			}
			if ($quest_style == 1) {
				$return_str = $return_str.
					'<option value="'.SF_PickoneTemplate::$iscale['afield'][0]['afield_id'].'" '.$selected.' >'.SF_PickoneTemplate::$iscale['afield'][0]['afield_text'].'</option>'.
					'</select>'.
					'<br/>'.JText::_('COM_SURVEYFORCE_OTHER_ANSWER').
					'<br/>'.
					'<input class="po_other" type="text" id="other_op_'. SF_PickoneTemplate::$question->id. '" name="other_op_'. SF_PickoneTemplate::$question->id. '" value="'. $other_val. '"/>';
			} else {
				$return_str = $return_str. '<tr>'.
					'<td class="po_answer_cell">'.
					'<input onchange="javascript: check_answer('. SF_PickoneTemplate::$question->id. ');" class="po_radio" type="radio" name="quest_radio'. SF_PickoneTemplate::$question->id. '" id="quest_radio'. SF_PickoneTemplate::$question->id. 'e" value="'. SF_PickoneTemplate::$iscale['afield'][0]['afield_id']. '" '. $selected. '>'.
					'<label for="quest_radio'. SF_PickoneTemplate::$question->id. 'e">'. SF_PickoneTemplate::$iscale['afield'][0]['afield_text']. '</label>'.
					'&nbsp;<input class="po_other" type="text" id="other_op_'. SF_PickoneTemplate::$question->id. '" name="other_op_'. SF_PickoneTemplate::$question->id. '" value="'. $other_val. '"/>'.
					'<br/>'.
					'</td>'.
					'</tr>';
			}
		}

		if ($quest_style == 1) {
			if (acount > 0)
				$return_str = $return_str. '</form></div>';
			else
				$return_str = $return_str. '</select></form></div>';
		}
		else {
			$return_str = $return_str. '</table></form></div>';
		}



		return $return_str;
	}

	public function QuestionImportance_scale() {

		$return_str = '';
		$ans_imp_count = SF_PickoneTemplate::$iscale['ans_imp_count'];
		$iscale_name = SF_PickoneTemplate::$iscale['impscale_name'];
		$iscount = count(SF_PickoneTemplate::$iscale['isfield']);
		if ($iscount) {
			$return_str = '<div align="left" class="importance_div">' .
				'<form name="iscale_form'.SF_PickoneTemplate::$question->id.'">'.
				'<br/>' .
				'<br/>' .
				'<table class="importance_table" id="iscale_table" cellpadding="0" cellspacing="0">';
			$return_str = $return_str . '<tr class="sectiontableentry2"><td class="i_quest" colspan="' . $iscount . '" >&nbsp;&nbsp;' . $iscale_name . '</td></tr>';
			$return_str = $return_str . '<tr class="sectiontableentry1">';

			for ($j = 0; $j < $iscount; $j++) {
				$return_str = $return_str . '<td class="i_text_cell" onclick="javascript: sf_getObj(\'iscale_radio' . SF_PickoneTemplate::$question->id . '_' . $j . '\').checked=\'checked\';">' .
					'<label for="iscale_radio' . SF_PickoneTemplate::$question->id . '_' . $j . '" style="cursor: pointer;">' .
					SF_PickoneTemplate::$iscale['isfield'][$j]['isfield_text'] .
					'</label>' .
					'</td>';
			}

			$return_str = $return_str . '</tr>';
			$return_str = $return_str . '<tr class="sectiontableentry2">';
			$selected = '';

			for ($j = 0; $j < $iscount; $j++) {
				$selected = '';
				if ($ans_imp_count > 0) {
					if (SF_PickoneTemplate::$iscale['isfield'][$j]['isfield_id'] == SF_PickoneTemplate::$iscale['ans_imp_id']) {
						$selected = " checked='checked' ";
					}
				}
				$return_str = $return_str . '<td class="i_ans_cell" onclick="javascript: sf_getObj(\'iscale_radio' . SF_PickoneTemplate::$question->id . '_' . $j . '\').checked=\'checked\';">' .
					'<input class="i_radio" type="radio" name="iscale_radio' . SF_PickoneTemplate::$question->id . '" id="iscale_radio' . SF_PickoneTemplate::$question->id . '_' . $j . '" value="' . SF_PickoneTemplate::$iscale['isfield'][$j]['isfield_id'] . '" ' . $selected . '/>' .
					'<label for="iscale_radio' . SF_PickoneTemplate::$question->id . '_' . $j . '" style="cursor: pointer;">' .
					SF_PickoneTemplate::$iscale['isfield'][$j]['isfield_text'] .
					'</label>' .
					'</td>';
			}

			$return_str = $return_str . "</tr>";
			$return_str = $return_str . "</table></form></div>";
		}

		return $return_str;
	}

}