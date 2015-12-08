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

class SF_ShortanswerTemplate {

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
        $question = SF_ShortanswerTemplate::parserBodyQuestion();
        $document = JFactory::getDocument();

        return $question;
    }

    public function parserBodyQuestion() {


        $body = SF_ShortanswerTemplate::QuestionBody();
        $vars = array();

        preg_match_all("/\{[A-Z0-9]{1,}_{0,}[A-Z0-9]{1,}\}/i", $body, $vars);

        foreach ($vars[0] as $var) {

            $function_name = 'SF_ShortanswerTemplate::Question' . ucfirst(strtolower(str_replace('}', '', str_replace('{', '', $var))));
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

		$inp = 0;
		if (strpos(SF_ShortanswerTemplate::$question->sf_qtext,'{x}') > 0 || strpos(SF_ShortanswerTemplate::$question->sf_qtext,'{y}') > 0) {
			$inp = substr_count(SF_ShortanswerTemplate::$question->sf_qtext, '{x}')+substr_count(SF_ShortanswerTemplate::$question->sf_qtext, '{y}');
		}
		if ( !$inp )
        	return SF_ShortanswerTemplate::$question->sf_qtext;
		else
			return '';
    }

    public function QuestionAnswers() {

		$inp = 0;
		if (strpos(SF_ShortanswerTemplate::$question->sf_qtext,'{x}') > 0 || strpos(SF_ShortanswerTemplate::$question->sf_qtext,'{y}') > 0) {
			$inp = substr_count(SF_ShortanswerTemplate::$question->sf_qtext, '{x}')+substr_count(SF_ShortanswerTemplate::$question->sf_qtext, '{y}');
		}

		if ( $inp == 0 )
			$return_str = '<div align="left" class="short_ans_div">' .
					'<br/>' .
					'<textarea id="inp_short' . SF_ShortanswerTemplate::$question->id . '" class="short_ans_textarea" rows="5" >' .
					'</textarea>' .
					'</div>';
		else
		{
			$return_str = SF_ShortanswerTemplate::$question->sf_qtext;
			SF_ShortanswerTemplate::$question->sf_qtext = '';
			for ( $i = 0; $i < $inp; $i++)
			{
				$x_pos = strpos($return_str, '{x}');
				$y_pos = strpos($return_str, '{y}');

				if ( ($x_pos < $y_pos || $y_pos === false) && $x_pos !== false) {
					$tmp_str = '<input class="sa_input_text" type="text" id="short_ans_'.SF_ShortanswerTemplate::$question->id.'_'.$i.'" name="short_ans_'.SF_ShortanswerTemplate::$question->id.'_'.$i.'" value="" />';
					$return_str = preg_replace('/\{x\}/i', $tmp_str, $return_str, 1);
				}
				elseif ( $y_pos !== false )
				{
					$tmp_str = '<textarea id="short_ans_'.SF_ShortanswerTemplate::$question->id.'_'.$i.'" name="short_ans_'.SF_ShortanswerTemplate::$question->id.'_'.$i.'" class="short_ans_textarea" rows="5" ></textarea>';
					$return_str = preg_replace('/\{y\}/i', $tmp_str, $return_str, 1);
				}
			}
		}

        return $return_str;
    }

    public function QuestionImportance_scale() {

        $return_str = '';
        $ans_imp_count = SF_ShortanswerTemplate::$iscale['ans_imp_count'];
        $iscale_name = SF_ShortanswerTemplate::$iscale['impscale_name'];
        $iscount = count(SF_ShortanswerTemplate::$iscale['isfield']);
        if ($iscount) {
            $return_str = '<div align="left" class="importance_div">' .
                    '<form name="iscale_form'.SF_ShortanswerTemplate::$question->id.'">'.
                    '<br/>' .
                    '<br/>' .
                    '<table class="importance_table" id="iscale_table" cellpadding="0" cellspacing="0">';
            $return_str = $return_str . '<tr class="sectiontableentry2"><td class="i_quest" colspan="' . $iscount . '" >&nbsp;&nbsp;' . $iscale_name . '</td></tr>';
            $return_str = $return_str . '<tr class="sectiontableentry1">';

            for ($j = 0; $j < $iscount; $j++) {
                $return_str = $return_str . '<td class="i_text_cell" onclick="javascript: sf_getObj(\'iscale_radio' . SF_ShortanswerTemplate::$question->id . '_' . $j . '\').checked=\'checked\';">' .
                        '<label for="iscale_radio' . SF_ShortanswerTemplate::$question->id . '_' . $j . '" style="cursor: pointer;">' .
                        SF_ShortanswerTemplate::$iscale['isfield'][$j]['isfield_text'] .
                        '</label>' .
                        '</td>';
            }

            $return_str = $return_str . '</tr>';
            $return_str = $return_str . '<tr class="sectiontableentry2">';
            $selected = '';

            for ($j = 0; $j < $iscount; $j++) {
                $selected = '';
                if ($ans_imp_count > 0) {
                    if (SF_ShortanswerTemplate::$iscale['isfield'][$j]['isfield_id'] == SF_ShortanswerTemplate::$iscale['ans_imp_id']) {
                        $selected = " checked='checked' ";
                    }
                }
                $return_str = $return_str . '<td class="i_ans_cell" onclick="javascript: sf_getObj(\'iscale_radio' . SF_ShortanswerTemplate::$question->id . '_' . $j . '\').checked=\'checked\';">' .
                        '<input class="i_radio" type="radio" name="iscale_radio' . SF_ShortanswerTemplate::$question->id . '" id="iscale_radio' . SF_ShortanswerTemplate::$question->id . '_' . $j . '" value="' . SF_ShortanswerTemplate::$iscale['isfield'][$j]['isfield_id'] . '" ' . $selected . '/>' .
                        '</td>';
            }

            $return_str = $return_str . "</tr>";
            $return_str = $return_str . "</table></form></div>";
        }

        return $return_str;
    }

}