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

class SF_RankingdropdownTemplate {

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
        $question = SF_RankingdropdownTemplate::parserBodyQuestion();
        $document = JFactory::getDocument();

        return $question;
    }

    public function parserBodyQuestion() {


        $body = SF_RankingdropdownTemplate::QuestionBody();
        $vars = array();

        preg_match_all("/\{[A-Z0-9]{1,}_{0,}[A-Z0-9]{1,}\}/i", $body, $vars);

        foreach ($vars[0] as $var) {

            $function_name = 'SF_RankingdropdownTemplate::Question' . ucfirst(strtolower(str_replace('}', '', str_replace('{', '', $var))));
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

        return SF_RankingdropdownTemplate::$question->sf_qtext;
    }

    public function QuestionAnswers() {

        $acount = SF_RankingdropdownTemplate::$iscale['alt_fields_count'];
        $mcount = SF_RankingdropdownTemplate::$iscale['main_fields_count'];
        $ans_count = SF_RankingdropdownTemplate::$iscale['ans_count'];
        $make_select = array();

        for ($i = 0; $i < $mcount; $i++) {
            $make_select[$i] = '<option class="dd_option" value="0">' . $sf_dd_first_menu . '</option>';
        }

        $selected = '';

        for ($i = 0; $i < $mcount; $i++) {
            if ($ans_count > 0) {
                for ($ii = 0; $ii < $ans_count; $ii++) {
                    if (SF_RankingdropdownTemplate::$iscale['answers'][$ii]['a_quest_id']== SF_RankingdropdownTemplate::$iscale['mfield'][$i]['mfield_id'])
                        $jj = $ii;
                }
            }
            for ($j = 0; $j < $acount; $j++) {
                $selected = '';
                if ($ans_count > 0) {
                    if ($jj >= 0 && SF_RankingdropdownTemplate::$iscale['answers'][$jj]['ans_id'] == SF_RankingdropdownTemplate::$iscale['afield'][$j]['afield_id'])
                        $selected = " $selected ";
                }
                $make_select[$i] = $make_select[$i] . '<option class="dd_option" value ="' . SF_RankingdropdownTemplate::$iscale['afield'][$j]['afield_id'] . '" ' . $selected . '>' .
                        SF_RankingdropdownTemplate::$iscale['afield'][$j]['afield_text'] .
                        '</option>';
            }
            $jj = -1;
        }
        $return_str = '<div align="left" class="dp_n_dn_div">' .
                '<form name="quest_form' . SF_RankingdropdownTemplate::$question->id . '">' .
                '<br/>' .
                '<table id="quest_table" class="drop_down_table">';
        for ($i = 0; $i < $mcount; $i++) {
            $return_str = $return_str . '<tr>' .
                    '<td class="dd_left_cell">' . SF_RankingdropdownTemplate::$iscale['mfield'][$i]['mfield_text'] . '</td>' .
                    '<td class="dd_right_cell">' .
                    '<select onchange="javascript: check_answer(' . SF_RankingdropdownTemplate::$question->id . ');" class="dd_select" name="quest_select_' . SF_RankingdropdownTemplate::$question->id . '_' . SF_RankingdropdownTemplate::$iscale['mfield'][$i]['mfield_id'] . '">' . $make_select[$i] .
                    '</select>' .
                    '</td>' .
                    '</tr>';
        }
        $return_str = $return_str . '</table></form></div>';

        return $return_str;
    }

    public function QuestionImportance_scale() {

        $return_str = '';
        $ans_imp_count = SF_RankingdropdownTemplate::$iscale['ans_imp_count'];
        $iscale_name = SF_RankingdropdownTemplate::$iscale['impscale_name'];
        $iscount = count(SF_RankingdropdownTemplate::$iscale['isfield']);
        if ($iscount) {
            $return_str = '<div align="left" class="importance_div">' .
                    '<form name="iscale_form'.SF_RankingdropdownTemplate::$question->id.'">'.
                    '<br/>' .
                    '<br/>' .
                    '<table class="importance_table" id="iscale_table" cellpadding="0" cellspacing="0">';
            $return_str = $return_str . '<tr class="sectiontableentry2"><td class="i_quest" colspan="' . $iscount . '" >&nbsp;&nbsp;' . $iscale_name . '</td></tr>';
            $return_str = $return_str . '<tr class="sectiontableentry1">';

            for ($j = 0; $j < $iscount; $j++) {
                $return_str = $return_str . '<td class="i_text_cell" onclick="javascript: sf_getObj(\'iscale_radio' . SF_RankingdropdownTemplate::$question->id . '_' . $j . '\').checked=\'checked\';">' .
                        '<label for="iscale_radio' . SF_RankingdropdownTemplate::$question->id . '_' . $j . '" style="cursor: pointer;">' .
                        SF_RankingdropdownTemplate::$iscale['isfield'][$j]['isfield_text'] .
                        '</label>' .
                        '</td>';
            }

            $return_str = $return_str . '</tr>';
            $return_str = $return_str . '<tr class="sectiontableentry2">';
            $selected = '';

            for ($j = 0; $j < $iscount; $j++) {
                $selected = '';
                if ($ans_imp_count > 0) {
                    if (SF_RankingdropdownTemplate::$iscale['isfield'][$j]['isfield_id'] == SF_RankingdropdownTemplate::$iscale['ans_imp_id']) {
                        $selected = " checked='checked' ";
                    }
                }
                $return_str = $return_str . '<td class="i_ans_cell" onclick="javascript: sf_getObj(\'iscale_radio' . SF_RankingdropdownTemplate::$question->id . '_' . $j . '\').checked=\'checked\';">' .
                        '<input class="i_radio" type="radio" name="iscale_radio' . SF_RankingdropdownTemplate::$question->id . '" id="iscale_radio' . SF_RankingdropdownTemplate::$question->id . '_' . $j . '" value="' . SF_RankingdropdownTemplate::$iscale['isfield'][$j]['isfield_id'] . '" ' . $selected . '/>' .
                        '</td>';
            }

            $return_str = $return_str . "</tr>";
            $return_str = $return_str . "</table></form></div>";
        }

        return $return_str;
    }

}