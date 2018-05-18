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
class SurveyforceControllerConfiguration extends JControllerForm {

    protected function allowEdit($data = array(), $key = 'c_par_name') {
        // Check specific edit permission then general edit permission.
        return JFactory::getUser()->authorise('core.edit', 'com_surveyforce');
    }
    
    public function getModel($name = 'Configuration', $prefix = 'SurveyforceModel', $config = array('ignore_request' => true)) {
        return parent::getModel($name, $prefix, $config);
    }

    public function save($key = null, $urlVar = null) {
      
        $model = $this->getModel();
        $data = JFactory::getApplication()->input->get('jform', array(), 'ARRAY');
        $data['view'] = JFactory::getApplication()->input->getCmd('view','configuration');
        $model->save($data);
        $param = '';
        $plugin_var = JFactory::getApplication()->input->get('plugin','');
        if($plugin_var)
            $param = '&plugin='.$plugin_var;
        
        $this->setRedirect('index.php?option=com_surveyforce&view=configuration'.$param);
    }

}
