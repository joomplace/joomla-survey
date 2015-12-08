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
 * Configuration Controller
 */
class SurveyforceModelConfiguration extends JModelAdmin {

    protected $context = 'com_surveyforce';

    public function getTable($type = 'Configuration', $prefix = 'SurveyforceTable', $config = array()) {
        return JTable::getInstance($type, $prefix, $config);
    }

    public function getForm($data = array(), $loadData = true) {
        
        $db = JFactory::getDbo();
        
        $form = $this->loadForm('com_surveyforce.configuration', 'configuration', array('control' => 'jform', 'load_data' => false));
        if (empty($form)) {
            return false;
        }

        $plugin = 'com_surveyforce';

        $db->setQuery("SELECT `params` FROM `#__extensions` WHERE `element` = 'com_surveyforce'");
        $params = $db->loadResult();

        $params = json_decode($params);

        $form->bind($params);

        return $form;
    }

    public function save($data) {

        $db = JFactory::getDbo();
        $plugin = 'com_surveyforce';

        $str_config = json_encode($data);   
        $query = "UPDATE `#__extensions` SET `params`='" . $str_config . "' WHERE `element`='" . $plugin."'";

        $db->setQuery($query);
        $db->execute();

        if ($db->getErrorMsg())
            return $db->getErrorMsg();
        else
            return true;
    }

}