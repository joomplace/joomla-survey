<?php

/**
 * Survey Force Deluxe component for Joomla 3
 * @package Survey Force Deluxe
 * @author JoomPlace Team
 * @Copyright Copyright (C) JoomPlace, www.joomplace.com
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.controllerform');

/**
 * Question Controller
 */
class SurveyforceControllerQuestion extends JControllerForm {

    public function __construct($config = array()) {
		if ( JFactory::getApplication()->input->get('surv_id', 0) )
			JFactory::getApplication()->setUserState('question.sf_survey', JFactory::getApplication()->input->get('surv_id', 0));

        parent::__construct($config);
    }

    public function new_question_type() {

		if ( JFactory::getApplication()->input->get('surv_id', 0) )
			JFactory::getApplication()->setUserState('question.sf_survey', JFactory::getApplication()->input->get('surv_id', 0));

        require_once(JPATH_BASE . '/components/com_surveyforce/views/newquestion/view.html.php');
        $view = $this->getView("newquestion");
        $view->display();
    }

    protected function allowEdit($data = array(), $key = 'id') {
        // Check specific edit permission then general edit permission.
        return JFactory::getUser()->authorise('core.edit', 'com_surveyforce');
    }

    static public function SF_editorArea($name, $content, $hiddenField, $width, $height, $col, $row) {
        $editor = JFactory::getEditor();
        echo $editor->display($hiddenField, $content, $width, $height, $col, $row, array('pagebreak', 'readmore'));
    }

    public function edit_field() {

        require_once(JPATH_BASE . '/components/com_surveyforce/views/editor/view.html.php');
        $view = $this->getView("editor");
        $view->display();
        
    }
    
    public function orderup() {

        $db = JFactory::getDbo();
		$id = JFactory::getApplication()->input->get('cid', array(), 'ARRAY');
        $id = $id[0];

        $query = 'SELECT ordering FROM `#__survey_force_quests` WHERE id=' . $id;
        $db->setQuery($query);
        $order = $db->loadRow();
        
       
        $query = 'UPDATE `#__survey_force_quests` SET `ordering` =' . (intval($order[0]) - 1) . ' WHERE id=' . $id;
        $db->setQuery($query);
        $db->execute();
		JFactory::getApplication()->redirect('index.php?option=com_surveyforce&view=questions&surv_id='.JFactory::getApplication()->input->get('surv_id', 0));
    }

    public function orderdown() {

        $db = JFactory::getDbo();
        $id = JFactory::getApplication()->input->get('cid', array(), 'array');
        $id = $id[0];
        
        $query = 'SELECT ordering FROM `#__survey_force_quests` WHERE `id`=' . $id;
        $db->setQuery($query);
        $order = $db->loadRow();

        if (intval($order) > 0) {
            $query = 'UPDATE `#__survey_force_quests` SET `ordering`=' . (intval($order[0]) + 1) . ' WHERE id=' . $id;
            $db->setQuery($query);
            $db->execute();
        }
		JFactory::getApplication()->redirect('index.php?option=com_surveyforce&view=questions&surv_id='.JFactory::getApplication()->input->get('surv_id', 0));
    }

    public function get_options(){

        $database = JFactory::getDBO();
        @session_start();

            if(true||@$_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest'){ 
                $quest_id   = (int) JFactory::getApplication()->input->get('quest_id', '0' );
                if ($quest_id) {
                    
                    $database->setQuery("SELECT * FROM `#__survey_force_quests` WHERE `id` = '".$quest_id."'");
                    $quest = $database->loadObject();

                    $return = '';
                    if ($quest->sf_qtype == 1) {    
                        $query = "SELECT id AS value, stext AS text FROM `#__survey_force_scales` WHERE quest_id = '".$quest_id."' ORDER BY ordering";
                        $database->SetQuery($query);

                        $f_scale_data = $database->loadObjectList();
                        
                        $f_scale_data = JHtmlSelect::genericlist( $f_scale_data, 'f_scale_data', 'class="text_area" id="f_scale_data" size="1" ', 'value', 'text', null ); 
                        
                        $query = "SELECT id AS value, ftext AS text FROM `#__survey_force_fields` WHERE quest_id = '".$quest_id."' ORDER BY ordering";
                        $database->SetQuery($query);
                        $f_fields_data = $database->loadObjectList();
                        
                        $i =0;
                        while ($i < count($f_fields_data)) {
                            $f_fields_data[$i]->text = strip_tags($f_fields_data[$i]->text);
                            if (strlen($f_fields_data[$i]->text) > 55)
                                $f_fields_data[$i]->text = substr($f_fields_data[$i]->text, 0, 55).'...';
                            $f_fields_data[$i]->text = $f_fields_data[$i]->value . ' - ' . $f_fields_data[$i]->text;
                            $i ++;
                        }
                        
                        $f_fields_data = JHtmlSelect::genericlist( $f_fields_data, 'sf_field_data_m', 'class="text_area" id="sf_field_data_m" size="1" ', 'value', 'text', null );                       
                        
                        $return = ' and for option "'.$f_fields_data.'"  answer is "'.$f_scale_data.'" <input type="hidden" name="sf_qtype2" id="sf_qtype2" value="'.$quest->sf_qtype.'"/>';

                    } elseif ($quest->sf_qtype == 2 || $quest->sf_qtype == 3) {
                        
                        $query = "SELECT id AS value, ftext AS text FROM `#__survey_force_fields` WHERE quest_id = '".$quest_id."' ORDER BY ordering";
                        $database->SetQuery($query);
                        $f_fields_data = $database->loadObjectList();
                        
                        $i =0;
                        while ($i < count($f_fields_data)) {
                            $f_fields_data[$i]->text = strip_tags($f_fields_data[$i]->text);
                            if (strlen($f_fields_data[$i]->text) > 55)
                                $f_fields_data[$i]->text = substr($f_fields_data[$i]->text, 0, 55).'...';
                            $f_fields_data[$i]->text = $f_fields_data[$i]->value . ' - ' . $f_fields_data[$i]->text;
                            $i ++;
                        }
                        
                        $f_fields_data = JHtmlSelect::genericlist( $f_fields_data, 'sf_field_data_m', 'class="text_area" id="sf_field_data_m" size="1" ', 'value', 'text', null ); 
                        
                        $return = ' answer is "'.$f_fields_data.'" <input type="hidden" name="sf_qtype2" id="sf_qtype2" value="'.$quest->sf_qtype.'"/>';

                    } elseif ($quest->sf_qtype == 5 || $quest->sf_qtype == 6 ) {
                                            
                        $query = "SELECT id AS value, ftext AS text FROM `#__survey_force_fields` WHERE quest_id = '".$quest_id."' AND is_main = 1 ORDER BY ordering";
                        $database->SetQuery($query);
                        $f_fields_data = $database->loadObjectList();
                        
                        $i =0;
                        while ($i < count($f_fields_data)) {
                            $f_fields_data[$i]->text = strip_tags($f_fields_data[$i]->text);
                            if (strlen($f_fields_data[$i]->text) > 55)
                                $f_fields_data[$i]->text = substr($f_fields_data[$i]->text, 0, 55).'...';
                            $f_fields_data[$i]->text = $f_fields_data[$i]->value . ' - ' . $f_fields_data[$i]->text;
                            $i ++;
                        }
                        
                        $f_fields_data = JHtmlSelect::genericlist( $f_fields_data, 'sf_field_data_m', 'class="text_area" id="sf_field_data_m" size="1" ', 'value', 'text', null );  
                        
                        $query = "SELECT id AS value, ftext AS text FROM `#__survey_force_fields` WHERE quest_id = '".$quest_id."' AND is_main = 0 ORDER BY ordering";
                        $database->SetQuery($query);                        
                        $f_fields_data2 = $database->loadObjectList();
                        
                        $i =0;
                        while ($i < count($f_fields_data2)) {
                            $f_fields_data2[$i]->text = strip_tags($f_fields_data2[$i]->text);
                            if (strlen($f_fields_data2[$i]->text) > 55)
                                $f_fields_data2[$i]->text = substr($f_fields_data2[$i]->text, 0, 55).'...';
                            $f_fields_data2[$i]->text = $f_fields_data2[$i]->value . ' - ' . $f_fields_data2[$i]->text;
                            $i ++;
                        }
                        
                        $f_fields_data2 = JHtmlSelect::genericlist( $f_fields_data2, 'sf_field_data_a', 'class="text_area" id="sf_field_data_a" size="1" ', 'value', 'text', null );  
                        
                        $return = ' and for option "'.$f_fields_data.'"  answer is "'.$f_fields_data2.'" <input type="hidden" name="sf_qtype2" id="sf_qtype2" value="'.$quest->sf_qtype.'"/>';

                    } elseif ( $quest->sf_qtype == 9) {
                    
                        $query = "SELECT id AS value, ftext AS text FROM `#__survey_force_fields` WHERE quest_id = '".$quest_id."' AND is_main = 1 ORDER BY ordering";
                        $database->SetQuery($query);
                        $f_fields_data = $database->loadObjectList();
                        
                        $i =0;
                        while ($i < count($f_fields_data)) {
                            $f_fields_data[$i]->text = strip_tags($f_fields_data[$i]->text);
                            if (strlen($f_fields_data[$i]->text) > 55)
                                $f_fields_data[$i]->text = substr($f_fields_data[$i]->text, 0, 55).'...';
                            $f_fields_data[$i]->text = $f_fields_data[$i]->value . ' - ' . $f_fields_data[$i]->text;
                            $i ++;
                        }
                        
                        $f_fields_data = JHtmlSelect::genericlist( $f_fields_data, 'sf_field_data_m', 'class="text_area" id="sf_field_data_m" size="1" ', 'value', 'text', null );  
                        $query = "SELECT id AS value, ftext AS text FROM `#__survey_force_fields` WHERE quest_id = '".$quest_id."' AND is_main = 0 ORDER BY ordering";
                        $database->SetQuery($query);
                        $f_fields_data2 = $database->loadObjectList();
                        
                        $i =0;
                        while ($i < count($f_fields_data2)) {
                            $f_fields_data2[$i]->text = strip_tags($f_fields_data2[$i]->text);
                            if (strlen($f_fields_data2[$i]->text) > 55)
                                $f_fields_data2[$i]->text = substr($f_fields_data2[$i]->text, 0, 55).'...';
                            $f_fields_data2[$i]->text = $f_fields_data2[$i]->value . ' - ' . $f_fields_data2[$i]->text;
                            $i ++;
                        }
                        
                        $f_fields_data2 = JHtmlSelect::genericlist( $f_fields_data2, 'sf_field_data_a', 'class="text_area" id="sf_field_data_a" size="1" ', 'value', 'text', null );  
                        
                        $return = ' and for option "'.$f_fields_data.'" rank is "'.$f_fields_data2.'" <input type="hidden" name="sf_qtype2" id="sf_qtype2" value="'.$quest->sf_qtype.'"/>';
                        
                    }
                    @header ('Expires: Fri, 14 Mar 1980 20:53:00 GMT');
                    @header ('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT');
                    @header ('Cache-Control: no-cache, must-revalidate');
                    @header ('Pragma: no-cache');
                    @header ('Content-Type: text/xml charset='. _ISO);          
                    
                    echo '<?xml version="1.0" standalone="yes"?>';
                    echo '<response>' . "\n";
                    echo "\t" . '<data><![CDATA[';                  
                    echo $return."<script  type=\"text/javascript\" language=\"javascript\">jQuery('input#add_button').get(0).style.display = '';</script>";
                    echo ']]></data>' . "\n";
                    echo '</response>' . "\n";
                    exit();
                }
            }
    }

	public function cancel($key = NULL)
	{
		if (JFactory::getApplication()->getUserState( "question.sf_survey"))
			JFactory::getApplication()->redirect('index.php?option=com_surveyforce&view=questions&surv_id='.JFactory::getApplication()->getUserState( "question.sf_survey"));
		else
			parent::cancel();
	}

	public function save($key = NULL, $urlVar = NULL)
	{
		$res = parent::save();

		if ( JFactory::getApplication()->input->get('task') == 'save')
			if ( $res && JFactory::getApplication()->getUserState( "question.sf_survey") )
				JFactory::getApplication()->redirect('index.php?option=com_surveyforce&view=questions&surv_id='.JFactory::getApplication()->getUserState( "question.sf_survey"));

		return $res;
	}

	public function saveandnew()
	{
		$this->save();

		$jform = JFactory::getApplication()->input->get('jform', array(), 'array');

		JFactory::getApplication()->setUserState('question.sf_survey', $jform['sf_survey']);
		JFactory::getApplication()->setUserState('question.surv_id', $jform['sf_survey']);

		JFactory::getApplication()->setUserState('question.new_qtype_id', $jform['sf_qtype']);

		JFactory::getApplication()->redirect('index.php?option=com_surveyforce&view=question&layout=edit&surv_id='.$jform['sf_survey'].'&new_qtype_id='.$jform['sf_qtype']);
	}

}
