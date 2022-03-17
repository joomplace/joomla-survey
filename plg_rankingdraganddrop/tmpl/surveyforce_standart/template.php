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

class SF_RankingdraganddropTemplate {

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
        $question = SF_RankingdraganddropTemplate::parserBodyQuestion();
        return $question;
    }

    public static function parserBodyQuestion() {

        $body = SF_RankingdraganddropTemplate::QuestionBody();
        $vars = array();

        preg_match_all("/\{[A-Z0-9]{1,}_{0,}[A-Z0-9]{1,}\}/i", $body, $vars);

        foreach ($vars[0] as $var) {
            $function_name = 'SF_RankingdraganddropTemplate::Question' . ucfirst(strtolower(str_replace('}', '', str_replace('{', '', $var))));
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

        return SF_RankingdraganddropTemplate::$question->sf_qtext;
    }

	public static function QuestionAnswers() {

		$color_cont = SF_RankingdraganddropTemplate::$iscale['config']['color_cont'];
		$color_drag = SF_RankingdraganddropTemplate::$iscale['config']['color_drag'];
		$color_highlight = SF_RankingdraganddropTemplate::$iscale['config']['color_highlight'];

		$acount = SF_RankingdraganddropTemplate::$iscale['alt_fields_count'];
		$mcount = SF_RankingdraganddropTemplate::$iscale['main_fields_count'];
		$ans_count = SF_RankingdraganddropTemplate::$iscale['ans_count'];

		$return_str = '<div class="drag_drop_div">'
			.'<table class="drag_drop_table" id="quest_table'.SF_RankingdraganddropTemplate::$question->id.'">';

		for ($i = 0; $i < $mcount; $i++) {
			$return_str.= '<tr>'
				.'<td width="2%">'
				.'<div class="jb_survey_dragdrop_left" id="cdiv'.SF_RankingdraganddropTemplate::$question->id.'_'.($i+1).'" style="position:relative; width:250px; height:30px; margin:10px; background: url(\''.JUri::root().'components/com_surveyforce/assets/images/cont_img.gif\') no-repeat; background-position:right; background-color: #'.$color_cont.'; border: 1px solid #000; cursor:default; text-align:center; vertical-align:middle;">'
				.SF_RankingdraganddropTemplate::$iscale['mfield'][$i]['mfield_text']
				.'</div>'
				.'</td>'
				.'<td width="auto">'
				.'<div class="jb_survey_dragdrop_right" id="ddiv'.SF_RankingdraganddropTemplate::$question->id.'_'.($i+1).'" onmousedown="startDrag(event, '.SF_RankingdraganddropTemplate::$question->id.');" onmouseup="stopDrag(event, '.SF_RankingdraganddropTemplate::$question->id.');" style="position:relative; width:250px; height:30px; margin:10px; background: url(\''.JUri::root().'components/com_surveyforce/assets/images/drag_img.gif\') no-repeat; background-position:left; background-color: #'.$color_drag.'; border: 1px solid #000; cursor:default; text-align:center; vertical-align:middle;">'		.SF_RankingdraganddropTemplate::$iscale['afield'][$i]['afield_text']
				.'</div>'
				.'</td>'
				.'</tr>';
		}
		$return_str.= '</table></div>';

		return $return_str;
	}

    public static function QuestionImportance_scale() {

        $return_str = '';
        $ans_imp_count = SF_RankingdraganddropTemplate::$iscale['ans_imp_count'];
        $iscale_name = SF_RankingdraganddropTemplate::$iscale['impscale_name'];
        $iscount = count(SF_RankingdraganddropTemplate::$iscale['isfield']);
        if ($iscount) {
            $return_str = '<div align="left" class="importance_div">' .
                    '<form name="iscale_form'.SF_RankingdraganddropTemplate::$question->id.'">'.
                    '<br/>' .
                    '<br/>' .
                    '<table class="importance_table" id="iscale_table" cellpadding="0" cellspacing="0">';
            $return_str = $return_str . '<tr class="sectiontableentry2"><td class="i_quest" colspan="' . $iscount . '" >&nbsp;&nbsp;' . $iscale_name . '</td></tr>';
            $return_str = $return_str . '<tr class="sectiontableentry1">';

            for ($j = 0; $j < $iscount; $j++) {
                $return_str = $return_str . '<td class="i_text_cell" onclick="javascript: sf_getObj(\'iscale_radio' . SF_RankingdraganddropTemplate::$question->id . '_' . $j . '\').checked=\'checked\';">' .
                        '<label for="iscale_radio' . SF_RankingdraganddropTemplate::$question->id . '_' . $j . '" style="cursor: pointer;">' .
                        SF_RankingdraganddropTemplate::$iscale['isfield'][$j]['isfield_text'] .
                        '</label>' .
                        '</td>';
            }

            $return_str = $return_str . '</tr>';
            $return_str = $return_str . '<tr class="sectiontableentry2">';
            $selected = '';

            for ($j = 0; $j < $iscount; $j++) {
                $selected = '';
                if ($ans_imp_count > 0) {
                    if (SF_RankingdraganddropTemplate::$iscale['isfield'][$j]['isfield_id'] == SF_RankingdraganddropTemplate::$iscale['ans_imp_id']) {
                        $selected = " checked='checked' ";
                    }
                }
                $return_str = $return_str . '<td class="i_ans_cell" onclick="javascript: sf_getObj(\'iscale_radio' . SF_RankingdraganddropTemplate::$question->id . '_' . $j . '\').checked=\'checked\';">' .
                        '<input class="i_radio" type="radio" name="iscale_radio' . SF_RankingdraganddropTemplate::$question->id . '" id="iscale_radio' . SF_RankingdraganddropTemplate::$question->id . '_' . $j . '" value="' . SF_RankingdraganddropTemplate::$iscale['isfield'][$j]['isfield_id'] . '" ' . $selected . '/>' .
                        '</td>';
            }

            $return_str = $return_str . "</tr>";
            $return_str = $return_str . "</table></form></div>";
        }

        return $return_str;
    }

}