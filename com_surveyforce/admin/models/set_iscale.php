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

class SurveyforceModelSet_iscale extends JModelAdmin {

    protected $context = 'com_surveyforce';

    public function getLists(){

        $database = JFactory::getDBO();
            
        $id = 0;
        $row = JTable::getInstance('Iscale', 'SurveyforceTable', array());
        $row->load($id);
        
        $lists = array();
        $lists['sf_fields'] = array();
        $query = "SELECT * FROM `#__survey_force_iscales_fields` WHERE `iscale_id` = '".$id."' ORDER BY ordering";
        $database->SetQuery($query);
        $lists['sf_fields'] = $database->loadObjectList();
        $lists['row'] = $row;

        return $lists;
    }

    public function getForm($data = array(), $loadData = true) {
        
        return true;
    }

}
