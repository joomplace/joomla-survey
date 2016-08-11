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

class SF_BoilerplateTemplate {

        static $question;
        static $iscale;

        /*         * ******************************************************************************************************************* *
         * 
         * {QUESTION_TEXT} surveys name will be placed there
         * {ANSWERS} - text for background image will be placed there
         * {IMPORTANCE_SCALE} - progress bar will be placed there  
         *      
         * ********************************************************************************************************************* */
	public function getQuestion() {
		$question = self::parserBodyQuestion();
		$document = JFactory::getDocument();
		foreach ( glob(dirname(__FILE__).'/css/*.css') as $CssFile )
			$document->addStyleSheet(JUri::root() . str_replace('\\', '/', str_replace( JPATH_BASE, '', $CssFile)) );
		return $question;
	}

        public function QuestionBody() {
            $return_str = <<<EOFTMPL
			<div align="left" style="padding-left:10px;text-align:left;">{QUESTION_TEXT}</div>
			{IMPORTANCE_SCALE}
EOFTMPL;
            //remove new line characters
            $return_str = str_replace("\n", '', $return_str);
            $return_str = str_replace("\r", '', $return_str);
            return $return_str;
        }

        public function parserBodyQuestion() {


            $body = SF_BoilerplateTemplate::QuestionBody();
            $vars = array();

            preg_match_all("/\{[A-Z0-9]{1,}_{0,}[A-Z0-9]{1,}\}/i", $body, $vars);

            foreach ($vars[0] as $var) {

                $function_name = 'SF_BoilerplateTemplate::Question' . ucfirst(strtolower(str_replace('}', '', str_replace('{', '', $var))));
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

            return SF_BoilerplateTemplate::$question->sf_qtext;
        }

	public function QuestionImportance_scale() {

		$return_str = '';
		$ans_imp_count = SF_BoilerplateTemplate::$iscale['ans_imp_count'];
		$iscale_name = SF_BoilerplateTemplate::$iscale['impscale_name'];
		$iscount = count(SF_BoilerplateTemplate::$iscale['isfield']);

		if ($iscount) {
			$return_str = '<div align="left" class="importance_div">' .
				'<form name="iscale_form'.SF_BoilerplateTemplate::$question->id.'">'.
				'<br/>' .
				'<br/>' .
				'<table class="importance_table" id="iscale_table" cellpadding="0" cellspacing="0">';
			$return_str = $return_str . '<tr class="sectiontableentry2"><td class="i_quest" colspan="' . $iscount . '" >&nbsp;&nbsp;' . $iscale_name . '</td></tr>';
			$return_str = $return_str . '<tr class="sectiontableentry1">';

			for ($j = 0; $j < $iscount; $j++) {
				$return_str = $return_str . '<td class="i_text_cell" onclick="javascript: sf_getObj(\'iscale_radio' . SF_BoilerplateTemplate::$question->id . '_' . $j . '\').checked=\'checked\';">' .
					'<label for="iscale_radio' . SF_BoilerplateTemplate::$question->id . '_' . $j . '" style="cursor: pointer;">' .
					SF_BoilerplateTemplate::$iscale['isfield'][$j]['isfield_text'] .
					'</label>' .
					'</td>';
			}

			$return_str = $return_str . '</tr>';
			$return_str = $return_str . '<tr class="sectiontableentry2">';
			$selected = '';

			for ($j = 0; $j < $iscount; $j++) {
				$selected = '';
				if ($ans_imp_count > 0) {
					if (SF_BoilerplateTemplate::$iscale['isfield'][$j]['isfield_id'] == SF_BoilerplateTemplate::$iscale['ans_imp_id']) {
						$selected = " checked='checked' ";
					}
				}
				$return_str = $return_str . '<td class="i_ans_cell" onclick="javascript: sf_getObj(\'iscale_radio' . SF_BoilerplateTemplate::$question->id . '_' . $j . '\').checked=\'checked\';">' .
					'<input class="i_radio" type="radio" name="iscale_radio' . SF_BoilerplateTemplate::$question->id . '" id="iscale_radio' . SF_BoilerplateTemplate::$question->id . '_' . $j . '" value="' . SF_BoilerplateTemplate::$iscale['isfield'][$j]['isfield_id'] . '" ' . $selected . '/>' .
					'</td>';
			}

			$return_str = $return_str . "</tr>";
			$return_str = $return_str . "</table></form></div>";
		}

		return $return_str;
	}

}