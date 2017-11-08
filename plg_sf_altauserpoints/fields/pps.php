<?php
/**
* Goals component for Joomla 3.0
* @package Goals
* @author JoomPlace Team
* @Copyright Copyright (C) JoomPlace, www.joomplace.com
* @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
*/
defined('JPATH_BASE') or die;

jimport('joomla.html.html');
jimport('joomla.form.formfield');
jimport('joomla.form.helper');
JFormHelper::loadFieldClass('list');

/* points per survey */

class JFormFieldPPS extends JFormFieldList
{
	public $type = 'pps';

	protected function getInput()
	{
		// Initialize variables.
		$html = array();
		$attr = '';
		
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
		$query->select($db->qn('id'))
			->select($db->qn('sf_name'))
			->from($db->qn('#__survey_force_survs'))
			->order($db->qn('id'));
		$surveys = $db->setQuery($query)->loadObjectList();

        if($surveys && is_array($surveys) && !empty($surveys)) {
            foreach ($surveys as $surv) {
                ob_start();
                ?>
                <div class="control-group">
                    <div class="control-label">
                        <label id="jform_params_comment-lbl" for="jform_params_comment" class="hasTooltip"
                               title=""><?php echo '#' . $surv->id . '. ' . mb_substr($surv->sf_name, 0,
                                    20) . (mb_strlen($surv->sf_name) > 20 ? '...' : ''); ?></label>
                    </div>
                    <div class="controls">
                        <input type="text" name="jform[params][pps][<?php echo $surv->id; ?>]"
                               value="<?php echo $this->value[$surv->id]; ?>" size="10">
                    </div>
                </div>
                <?php
                $entry = ob_get_contents();
                ob_end_clean();
                $html[] = $entry;
            }
        }
		return implode($html);
	}
}