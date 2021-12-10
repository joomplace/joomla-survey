<?php

/**
 * Survey Force Deluxe component for Joomla 3
 * @package Survey Force Deluxe
 * @author JoomPlace Team
 * @Copyright Copyright (C) JoomPlace, www.joomplace.com
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */
defined('_JEXEC') or die('Restricted access');

use \Joomla\CMS\Factory;
use \Joomla\CMS\Language\Text;
use \Joomla\CMS\Router\Route;
use \Joomla\Utilities\ArrayHelper;

class SurveyforceControllerCategories extends JControllerAdmin
{
    public function __construct($config = array())
    {
        parent::__construct($config);
    }

    public function getModel($name='Categories', $prefix='SurveyforceModel', $config=array('ignore_request' => true))
    {
        return parent::getModel($name, $prefix, $config);
    }

    public function add()
    {
        $this->setRedirect('index.php?option=com_surveyforce&task=category.add');
    }

    public function delete()
    {
        $this->checkToken();
        $cid = $this->input->get('cid', array(), 'array');
        $tmpl = $this->input->get('tmpl', '');

        if($tmpl == 'component') {
            $tmpl = '&tmpl=component';
        } else {
            $tmpl = '';
        }

        if (!is_array($cid) || count($cid) < 1) {
            Factory::getApplication()->enqueueMessage(Text::_($this->text_prefix . '_NO_ITEM_SELECTED'), 'error');
        } else {
            $model = $this->getModel();
            $cid = ArrayHelper::toInteger($cid);

            if ($model->delete($cid)) {
                $this->setMessage(Text::plural($this->text_prefix . '_N_ITEMS_DELETED', count($cid)));
            } else {
                $this->setMessage($model->getError());
            }
        }

        $this->setRedirect(Route::_('index.php?option=' . $this->option . '&view=' . $this->view_list . $tmpl, false));
    }

    public function edit()
    {
        $cid = $this->input->get('cid', array(), 'array');
        $item_id = !empty($cid['0']) ? (int)$cid['0'] : 0;
        $this->setRedirect(Route::_('index.php?option=' . $this->option . '&task=category.edit&id=' . $item_id, false));
    }

}
