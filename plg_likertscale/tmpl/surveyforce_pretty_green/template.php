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

class SF_LikertscaleTemplate {

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
			<div class="likert_scale_div">
			{LIKERSCALE}
			</div>
			{IMPORTANCE_SCALE}				
EOFTMPL;
		//remove new line characters
		$return_str = str_replace("\n", '', $return_str);
		$return_str = str_replace("\r", '', $return_str);
		return $return_str;
	}

	public function getQuestion() {
		$question = SF_LikertscaleTemplate::parserBodyQuestion();
		$document = JFactory::getDocument();
		return $question;
	}

	public function parserBodyQuestion() {


		$body = SF_LikertscaleTemplate::QuestionBody();
		$vars = array();

		preg_match_all("/\{[A-Z0-9]{1,}_{0,}[A-Z0-9]{1,}\}/i", $body, $vars);

		foreach ($vars[0] as $var) {

			$function_name = 'SF_LikertscaleTemplate::Question' . ucfirst(strtolower(str_replace('}', '', str_replace('{', '', $var))));
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

		return SF_LikertscaleTemplate::$question->sf_qtext;
	}


	public function QuestionLikerscale() {

		$mcount = SF_LikertscaleTemplate::$iscale['main_fields_count'];
		$scount = SF_LikertscaleTemplate::$iscale['scale_fields_count'];
		$ans_count = SF_LikertscaleTemplate::$iscale['ans_count'];
		$sdata = SF_LikertscaleTemplate::$iscale['sdata'];
		$mfield = SF_LikertscaleTemplate::$iscale['mfield'];
		$ans_count = SF_LikertscaleTemplate::$iscale['ans_count'];
		$answers = SF_LikertscaleTemplate::$iscale['answers'];
		$factor_name = SF_LikertscaleTemplate::$iscale['factor_name'];

		$return_str = '';
        if($scount){
			$return_str = '<form name="quest_form' . SF_LikertscaleTemplate::$question->id . '">';
			$return_str.= '<table id="quest_table" class="likert_scale_table" cellpadding="3" cellspacing="0">';
			$return_str.= '<tr><td class="ls_factor_name">'.$factor_name.'</td>';

			for ($j = 0; $j < $scount; $j++) {
				$return_str.= '<td class="ls_scale_field">'.$sdata[$j]['sfield_text'].'</td>';
			}

			$return_str.='</tr>';

			$k = 1;
			$i = 0;
			$ii = 0;
			$jj = 0;
			$checked = '';
			//question option rows
			for ($i = 0; $i < $mcount; $i++) {
				//question option text
				$return_str.= '<tr class="sectiontableentry'.$k.'"><td class="ls_quest_field"><div id="qoption_'.SF_LikertscaleTemplate::$question->id.'_'.$mfield[$i]['mfield_id'].'">'.$mfield[$i]['mfield_text'].'</div></td>';
				//get row number for answers for this otpion
				if ($ans_count > 0) {
					for($ii = 0; $ii < $ans_count; $ii++) {
						if ( $answers[$ii]['a_quest_id'] == $mfield[$i]['mfield_id'] )
							$jj = $ii;
					}
				}
				for ($j = 0; $j < $scount; $j++) {
					$checked = '';
					if ($ans_count > 0) {
						//if selected current scale
						if ($answers[$jj]['ans_id'] == $sdata[$j]['sfield_id'])
							$checked = " checked='checked' ";
					}
					$return_str.=
						'<td class="ls_answer_cell" onclick="javascript: sf_getObj(\'quest_radio_' . SF_LikertscaleTemplate::$question->id . '_' . $mfield[$i]['mfield_id'] .'_'.$j . '\').checked=\'checked\'; check_answer(' . SF_LikertscaleTemplate::$question->id . '); ">
						<input onchange="javascript: check_answer(' . SF_LikertscaleTemplate::$question->id . ');" class="ls_radio" type="radio" name="quest_radio_' . SF_LikertscaleTemplate::$question->id . '_' . $mfield[$i]['mfield_id'] . '" value="' . $sdata[$j]['sfield_id'] . '" id="quest_radio_' . SF_LikertscaleTemplate::$question->id . '_' . $mfield[$i]['mfield_id'] .'_'.$j. '" ' . $checked . '>
						<label class="jb_survey_label" for="quest_radio_' . SF_LikertscaleTemplate::$question->id . '_' . $mfield[$i]['mfield_id'] .'_'.$j. '">' . $sdata[$j]['sfield_text'] . '</label>
						</td>';
				}
				$return_str.='</tr>';
				$k = 3 - $k;
			}
			$return_str.='</table></form></div>';
		}
		
		return $return_str;
	}

	public function QuestionImportance_scale() {

		$return_str = '';
		$ans_imp_count = SF_LikertscaleTemplate::$iscale['ans_imp_count'];
		$iscale_name = SF_LikertscaleTemplate::$iscale['impscale_name'];
		$iscount = count(SF_LikertscaleTemplate::$iscale['isfield']);
		if ($iscount) {
			$return_str = '<div align="left" class="importance_div">' .
				'<form name="iscale_form'.SF_LikertscaleTemplate::$question->id.'">'.
				'<br/>' .
				'<br/>' .
				'<table class="importance_table" id="iscale_table" cellpadding="0" cellspacing="0">';
			$return_str = $return_str . '<tr class="sectiontableentry2"><td class="i_quest" colspan="' . $iscount . '" >&nbsp;&nbsp;' . $iscale_name . '</td></tr>';
			$return_str = $return_str . '<tr class="sectiontableentry1">';

			for ($j = 0; $j < $iscount; $j++) {
				$return_str = $return_str . '<td class="i_text_cell" onclick="javascript: sf_getObj(\'iscale_radio' . SF_LikertscaleTemplate::$question->id . '_' . $j . '\').checked=\'checked\';">' .
					'<label for="iscale_radio' . SF_LikertscaleTemplate::$question->id . '_' . $j . '" style="cursor: pointer;">' .
					SF_LikertscaleTemplate::$iscale['isfield'][$j]['isfield_text'] .
					'</label>' .
					'</td>';
			}

			$return_str = $return_str . '</tr>';
			$return_str = $return_str . '<tr class="sectiontableentry2">';
			$selected = '';

			for ($j = 0; $j < $iscount; $j++) {
				$selected = '';
				if ($ans_imp_count > 0) {
					if (SF_LikertscaleTemplate::$iscale['isfield'][$j]['isfield_id'] == SF_LikertscaleTemplate::$iscale['ans_imp_id']) {
						$selected = " checked='checked' ";
					}
				}
				$return_str = $return_str . '<td class="i_ans_cell" onclick="javascript: sf_getObj(\'iscale_radio' . SF_LikertscaleTemplate::$question->id . '_' . $j . '\').checked=\'checked\';">' .
					'<input class="i_radio" type="radio" name="iscale_radio' . SF_LikertscaleTemplate::$question->id . '" id="iscale_radio' . SF_LikertscaleTemplate::$question->id . '_' . $j . '" value="' . SF_LikertscaleTemplate::$iscale['isfield'][$j]['isfield_id'] . '" ' . $selected . '/>' .
					'<label for="iscale_radio' . SF_LikertscaleTemplate::$question->id . '_' . $j . '" style="cursor: pointer;">' .
					SF_LikertscaleTemplate::$iscale['isfield'][$j]['isfield_text'] .
					'</label>' .
					'</td>';
			}

			$return_str = $return_str . "</tr>";
			$return_str = $return_str . "</table></form></div>";
		}

		return $return_str;
	}

}