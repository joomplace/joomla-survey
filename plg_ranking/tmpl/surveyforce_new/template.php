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

class SF_RankingTemplate {

        static $question;
        static $iscale;

        /*         * ******************************************************************************************************************* *
         * 
         * {QUESTION_TEXT} surveys name will be placed there
         * {ANSWERS} - text for background image will be placed there
         * {IMPORTANCE_SCALE} - progress bar will be placed there  
         *      
         * ********************************************************************************************************************* */
	public static function getQuestion() {
		$question = self::parserBodyQuestion();
		$document = JFactory::getDocument();
		foreach ( glob(dirname(__FILE__).'/css/*.css') as $CssFile )
			$document->addStyleSheet(JUri::root() . str_replace('\\', '/', str_replace( JPATH_BASE, '', $CssFile)) );
		return $question;
	}
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

        public static function parserBodyQuestion() {

            $body = SF_RankingTemplate::QuestionBody();
            $vars = array();

            preg_match_all("/\{[A-Z0-9]{1,}_{0,}[A-Z0-9]{1,}\}/i", $body, $vars);

            foreach ($vars[0] as $var) {

                $function_name = 'SF_RankingTemplate::Question' . ucfirst(strtolower(str_replace('}', '', str_replace('{', '', $var))));
                $replace = call_user_func($function_name);
                $body = str_replace($var, $replace, $body);
            }

            return $body;
        }

        /*         * ******************************************************************************************************************** *
         * 
         * This shall be defined functions for replace {VARIABLES}
         * 
         * ********************************************************************************************************************** */

        public function QuestionQuestion_text() {

            return SF_RankingTemplate::$question->sf_qtext;
        }

	public static function QuestionAnswers() {

		$acount = SF_RankingTemplate::$iscale['alt_fields_count'];
		$mcount = SF_RankingTemplate::$iscale['main_fields_count'];
		$ans_count = SF_RankingTemplate::$iscale['ans_count'];
		$make_select = array();

		for ($i = 0; $i < $mcount; $i++) {
			$make_select[$i] = '<option class="r_option" value="0">' . JText::_("COM_SURVEYFORCE_RANK_FIRST_ELEMENT") . '</option>';
		}
		$j = 0;
		$selected = '';
		$jj = -1;

		for ($i = 0; $i < $mcount; $i++) {
			$mfield_id = SF_RankingTemplate::$iscale['mfield'][$i]['mfield_id'];
			if ($ans_count > 0) {
				for ($ii = 0; $ii < $ans_count; $ii++) {
					if (SF_RankingTemplate::$iscale['answers'][$ii]['a_quest_id'] == $mfield_id)
						$jj = $ii;
				}
			}
			for ($j = 0; $j < $acount; $j++) {
				$selected = '';
				if ($ans_count > 0) {
					if ($jj >= 0 && SF_RankingTemplate::$iscale['answers'][$jj]['ans_id'] == SF_RankingTemplate::$iscale['afield'][$j]['afield_id'])
						$selected = " selected ";
				}

				$make_select[$i].= "\n" .
					'<option class="r_option" value ="' . SF_RankingTemplate::$iscale['afield'][$j]['afield_id'] . '" ' . $selected . '>'
					. SF_RankingTemplate::$iscale['afield'][$j]['afield_text'] .
					'</option>';
			}
			$jj = -1;
		}

		$return_str = '<div class="ranking_div" align="left" >' .
			'<form name="quest_form' . SF_RankingTemplate::$question->id . '">' .
			'<br/>' .
			'<table class="ranking_table" id="quest_table" >';

		$mfield_type = 0;
		$other_inp = '';
		$other_val = '';
		for ($i = 0; $i < $mcount; $i++) {
			$mfield_type = SF_RankingTemplate::$iscale['mfield'][$i]['mfield_is_true'];
			if ($mfield_type == 2) {
				$other_val = SF_RankingTemplate::$iscale['ans_txt'];
				if ($other_val == '!!!---!!!') {
					$other_val = '';
				}
				$other_inp = '<input class="r_other" type="text" id="other_op_' . SF_RankingTemplate::$question->id . '" name="other_op_' . SF_RankingTemplate::$question->id . '" value="' . $other_val . '"/>';
			} else {
				$other_inp = '';
				$other_val = '';
			}

			$return_str .='<tr>' .
				'<td class="r_left_cell">' .
				SF_RankingTemplate::$iscale['mfield'][$i]['mfield_text'] . " " . $other_inp .
				'</td>' .
				'<td class="r_right_cell">' .
				'<select class="r_select" onchange="javascript:removeSameRank(this, ' . SF_RankingTemplate::$question->id . ');" name="quest_select_' . SF_RankingTemplate::$question->id . '_' . SF_RankingTemplate::$iscale['mfield'][$i]['mfield_id'] . '" id="quest_select_' . SF_RankingTemplate::$question->id . '_' . SF_RankingTemplate::$iscale['mfield'][$i]['mfield_id'] . '">' .
				$make_select[$i] .
				'</select>' .
				'</td>' .
				'</tr>';
		}
		$return_str .='</table></form></div>';

		return $return_str;
	}

	public static function QuestionImportance_scale() {

		$return_str = '';
		$ans_imp_count = SF_RankingTemplate::$iscale['ans_imp_count'];
		$iscale_name = SF_RankingTemplate::$iscale['impscale_name'];
		$iscount = count(SF_RankingTemplate::$iscale['isfield']);
		if ($iscount) {
			$return_str = '<div align="left" class="importance_div">' .
				'<form name="iscale_form'.SF_RankingTemplate::$question->id.'">'.
				'<br/>' .
				'<br/>' .
				'<table class="importance_table" id="iscale_table" cellpadding="0" cellspacing="0">';
			$return_str = $return_str . '<tr class="sectiontableentry2"><td class="i_quest" colspan="' . $iscount . '" >&nbsp;&nbsp;' . $iscale_name . '</td></tr>';
			$return_str = $return_str . '<tr class="sectiontableentry1">';

			for ($j = 0; $j < $iscount; $j++) {
				$return_str = $return_str . '<td class="i_text_cell" onclick="javascript: sf_getObj(\'iscale_radio' . SF_RankingTemplate::$question->id . '_' . $j . '\').checked=\'checked\';">' .
					'<label for="iscale_radio' . SF_RankingTemplate::$question->id . '_' . $j . '" style="cursor: pointer;">' .
					SF_RankingTemplate::$iscale['isfield'][$j]['isfield_text'] .
					'</label>' .
					'</td>';
			}

			$return_str = $return_str . '</tr>';
			$return_str = $return_str . '<tr class="sectiontableentry2">';
			$selected = '';

			for ($j = 0; $j < $iscount; $j++) {
				$selected = '';
				if ($ans_imp_count > 0) {
					if (SF_RankingTemplate::$iscale['isfield'][$j]['isfield_id'] == SF_RankingTemplate::$iscale['ans_imp_id']) {
						$selected = " checked='checked' ";
					}
				}
				$return_str = $return_str . '<td class="i_ans_cell" onclick="javascript: sf_getObj(\'iscale_radio' . SF_RankingTemplate::$question->id . '_' . $j . '\').checked=\'checked\';">' .
					'<input class="i_radio" type="radio" name="iscale_radio' . SF_RankingTemplate::$question->id . '" id="iscale_radio' . SF_RankingTemplate::$question->id . '_' . $j . '" value="' . SF_RankingTemplate::$iscale['isfield'][$j]['isfield_id'] . '" ' . $selected . '/>' .
					'</td>';
			}

			$return_str = $return_str . "</tr>";
			$return_str = $return_str . "</table></form></div>";
		}

		return $return_str;
	}

}