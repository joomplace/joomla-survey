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

class SurveyforceModelInvite_users extends JModelAdmin {

    protected $context = 'com_surveyforce';

    public function getTable($type = 'Listuser', $prefix = 'SurveyforceTable', $config = array()) {
        return JTable::getInstance($type, $prefix, $config);
    }

    public function getForm($data = array(), $loadData = true) {
        $form = $this->loadForm('com_surveyforce.invite_users', 'invite_users', array('control' => 'jform', 'load_data' => false));
        if (empty($form)) {
            return false;
        }

        $item = $this->getItem();
        $form->bind($item);

        return $form;
    }

    public function getItem($pk = null) {

        $pk = JFactory::getApplication()->input->get('cid');
        $result = parent::getItem($pk);
        return $result;
    }   
}
