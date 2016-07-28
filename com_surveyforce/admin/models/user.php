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

class SurveyforceModelUser extends JModelAdmin {

    protected $context = 'com_surveyforce';

    public function getTable($type = 'User', $prefix = 'SurveyforceTable', $config = array()) {
        return JTable::getInstance($type, $prefix, $config);
    }

    public function getForm($data = array(), $loadData = true) {
        $form = $this->loadForm('com_surveyforce.user', 'user', array('control' => 'jform', 'load_data' => false));
        if (empty($form)) {
            return false;
        }

        $item = $this->getItem();
		
		$item->list_id = JFactory::getApplication()->input->get('list_id');		
		
		$item->reg_users = $item->id;
		
        $form->bind($item);

        return $form;
    }

    public function getUsers(){

        $database = JFactory::getDBO();

        $query = "SELECT id as value, username as text, name, email FROM `#__users` ORDER BY `username`";
        $database->SetQuery($query);

        $list_users = array();
        $list_users[] = JHTML::_('select.option', '0', JText::_('COM_SURVEYFORCE_SELECT_USER'));
        $pr = $database->loadObjectList();
        $lists['users'] = $pr;

        $i = 0;
        while ($i < count($pr)) {
            $pr[$i]->text = $pr[$i]->text . " (".$pr[$i]->name.", ".$pr[$i]->email.")";
            $i ++;
        }

        $list_users = @array_merge( $list_users, $pr );
        $lists['reg_users'] = JHTML::_('select.genericlist', $list_users, 'reg_users', 'class="text_area" style="width:300px" size="1" onChange="changeUserSelect(this);" ', 'value', 'text', null );

        return $lists;
    }

}
