<?php
/**
 * Survey Force Deluxe component for Joomla 3
 * @package Survey Force Deluxe
 * @author JoomPlace Team
 * @Copyright Copyright (C) JoomPlace, www.joomplace.com
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */

defined('_JEXEC') or die('Restricted access');

class SurveyforceControllerUser extends JControllerForm
{
	protected function getRedirectToItemAppend($cid = null){
		$tmpl   = $this->input->get('tmpl');
        $layout = $this->input->get('layout', 'edit', 'string');
        $append = '';

        // Setup redirect info.
        if ($tmpl)
        {
            $append .= '&tmpl=' . $tmpl;
        }

        if ($layout)
        {
            $append .= '&layout=' . $layout;
        }

        if ($recordId)
        {
            $append .= '&' . $urlVar . '=' . $recordId;
        }

        if (JFactory::getApplication()->input->get('id'))
        {
			$append .= '&list_id='.JFactory::getApplication()->input->get('id');
		}	
		
		if($cid) {
			$append .= '&id='.$cid;		
		}
		
		return $append;
	}
	
    public function add(){
        parent::add();
    }

    public function cancel(){
        $jform = JFactory::getApplication()->input->get('jform', array(),'array');
		unset($_SESSION['list_id']);
        $listid = $jform['list_id'];
        $this->setRedirect(JRoute::_('index.php?option=' . $this->option . '&view=users&id='.$listid, false));
    }

    public function save($pk){

    	parent::save($pk);
		
		$input = JFactory::getApplication()->input;
		
		$jform = $input->get('jform', array(),'array');
        unset($_SESSION['list_id']);
        $listid = $jform['list_id'];
		
		$task = $input->getCmd('task');
		
		if($task != 'save2new') {
			$this->setRedirect(JRoute::_('index.php?option=' . $this->option . '&view=users&id='.$listid, false));
		}
    	
    }
}
