<?php

/**
 * Survey Force Deluxe component for Joomla 3 3.0
 * @package Survey Force Deluxe
 * @author JoomPlace Team
 * @Copyright Copyright (C) JoomPlace, www.joomplace.com
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.modeladmin');

class SurveyforceModelIscale extends JModelAdmin {

    protected $context = 'com_surveyforce';

    public function getTable($type = 'Iscale', $prefix = 'SurveyforceTable', $config = array()) {
        return JTable::getInstance($type, $prefix, $config);
    }

    public function getForm($data = array(), $loadData = true) {
        $form = $this->loadForm('com_surveyforce.iscale', 'iscale', array('control' => 'jform', 'load_data' => false));
        if (empty($form)) {
            return false;
        }

        $item = $this->getItem();
        $form->bind($item);

        return $form;
    }
    
    public function getFields(){
        $db = JFactory::getDbo();
        
        $item = $this->getItem();
        
        $query = $db -> getQuery(true);
        
        $query ->select('*')->from('#__survey_force_iscales_fields')->where('iscale_id='.$item->id);
        $db ->setQuery($query);
        
        $fields = $db->loadObjectList();
        
        return $fields;
        
    }

    public function save($data) {
        parent::save($data);
        
        $db = JFactory::getDbo();
        $app = JFactory::getApplication();
        $fields = JFactory::getApplication()->input->get('sf_hid_fields',array(), 'ARRAY');
        
        if($data['id'])
            $iscale_id = $data['id'];        
        else $iscale_id = $db->insertid();
        
        $db->setQuery('DELETE FROM #__survey_force_iscales_fields WHERE iscale_id='.$iscale_id);
        $db->execute();
        
        $i = 0;
        foreach($fields as $field)
        {
            $db->setQuery('INSERT INTO #__survey_force_iscales_fields (`iscale_id`, `isf_name`, `ordering`) 
                VALUE(\''.$iscale_id.'\', '.$db->quote($field, true).', \''.($i++).'\')');
            $db->execute();
        }
        
        if($db->getErrorMsg())
        {
            $app->enqueueMessage($db->getErrorMsg());
            return false;
        }
        else return true;
    }

}
