<?php

/**
 * Survey Force Deluxe Boilerplate Plugin for Joomla 3 
 * @package Joomla.Plugin
 * @subpackage Survey.boilerplate
 * @author JoomPlace Team
 * @Copyright Copyright (C) JoomPlace, www.joomplace.com
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

class plgSurveyBoilerplate {

    var $name = 'Boilerplate';
    var $_name = 'boilerplate';
    var $_type = 'survey';

    public function __construct() {
        return true;
    }

    public function onGetScriptJs() {

        $document = JFactory::getDocument();
        $document->addScript(JUri::root()."plugins/survey/boilerplate/js/boilerplate.js");
    }

    public function onGetQuestionData(&$data) {

        $database = JFactory::getDbo();

        $q_data = $data['q_data'];
       
        $ret_str = '';

        $ret_str .= "\t" . '<quest_type>' . $q_data->sf_qtype . '</quest_type>' . "\n";
        $inp = 0;
        $q_text = $q_data->sf_qtext;

        if ($q_data->sf_section_id > 0) {
            $query = "SELECT `addname`, `sf_name` FROM `#__survey_force_qsections` WHERE `id` = '" . $q_data->sf_section_id . "' ";
            $database->SetQuery($query);
            $qsection_t = ($database->loadObject());
            if (isset($qsection_t->addname) && intval($qsection_t->addname) > 0) {
                $q_text = '<div class="sf_section_name">' . $qsection_t->sf_name . "</div><br/>" . $q_text;
            }
        }
        $ret_str .= "\t\t" . '<quest_inp_count>' . $inp . '</quest_inp_count>' . "\n";
        $ret_str .= "\t" . '<quest_text><![CDATA[' . SurveyforceHelper::sfPrepareText($q_text) . '&nbsp;]]></quest_text>' . "\n";
        $ret_str .= "\t" . '<quest_id>' . $q_data->id . '</quest_id>' . "\n";
        $ret_str .= "\t" . '<default_hided>' . (int) $q_data->sf_default_hided . '</default_hided>' . "\n";
        $ret_str .= "\t" . '<main_fields_count>0</main_fields_count>' . "\n";
        $ret_str .= "\t" . '<compulsory>' . $q_data->sf_compulsory . '</compulsory>' . "\n";
        $ret_str .= "\t" . '<sf_qstyle>' . (int) $q_data->sf_qstyle . '</sf_qstyle>' . "\n";
        $ret_str .= "\t" . '<factor_name><![CDATA[' . $q_data->sf_fieldtype . '&nbsp;]]></factor_name>' . "\n";
        $ret_str .= "\t" . '<sf_num_options>' . (int) $q_data->sf_num_options . '</sf_num_options>' . "\n";

        $f_iscale_data = array();
        if ($q_data->sf_impscale) { //important scale is SET
            $query = "SELECT a.iscale_name, b.* FROM #__survey_force_iscales as a, #__survey_force_iscales_fields as b WHERE a.id = '" . $q_data->sf_impscale . "' AND a.id = b.iscale_id ORDER BY b.ordering";
            $database->SetQuery($query);
            $result = $database->LoadObjectList();
            $f_iscale_data = ($result == null ? array() : $result);
        }

        

        // Copy this code for new plugin
        $tmpl_name = SurveyforceHelper::getTemplate($data);
        $class_name = 'SF_' . ucfirst($data['quest_type']) . 'Template';

       
         if (!class_exists($class_name))
            if (file_exists(JPATH_SITE . '/plugins/survey/'.$data['quest_type'].'/tmpl/' . $tmpl_name . '/template.php'))
                include_once JPATH_SITE . '/plugins/survey/'.$data['quest_type'].'/tmpl/' . $tmpl_name . '/template.php';

        $iscale = array();
        $iscale['impscale_name'] = (isset($f_iscale_data) && count($f_iscale_data)) ? $f_iscale_data[0]->iscale_name : '';
        $iscale['ans_imp_id'] = (isset($f_answ_imp_data) && count($f_answ_imp_data)) ? $f_answ_imp_data[0]->iscalefield_id : ''; 
        $iscale['isfield'] = array();
        
        if (isset($f_iscale_data) && count($f_iscale_data))
            foreach ($f_iscale_data as $is_row) {
                array_push($iscale['isfield'], array(
                    'isfield_text' => $is_row->isf_name,
                    'isfield_id' => $is_row->id,
                ));
            }



        $class_name::$question = $q_data;
        $class_name::$iscale = $iscale;
        $html = $class_name::getQuestion();

        $ret_str .= '<html><![CDATA[' . $html . ']]></html>';

        //End

        return $ret_str;
    }

    public function onSaveQuestion(&$data) {

        return true;
    }

    public function onGetAdminJavaScript(&$data) {

        return true;
    }

    public static function onGetAdminOptions(&$data)
    {
        $id = JFactory::getApplication()->input->getInt('id', 0);
        JToolBarHelper::title( ($id ? JText::_('COM_SURVEYFORCE_EDIT_QUESTION') : JText::_('COM_SURVEYFORCE_NEW_QUESTION')).' ('.JText::_('COM_SURVEYFORCE_BOILERPLATE').')', 'static.png' );

        return false;
    }

}