<?php
/**
 * Survey Force Deluxe component for Joomla 3 3.0
 * @package Survey Force Deluxe
 * @author JoomPlace Team
 * @Copyright Copyright (C) JoomPlace, www.joomplace.com
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */
defined('_JEXEC') or die('Restricted Access');

JHtml::_('behavior.framework', true);
JHtml::_('bootstrap.tooltip');
JHtml::_('behavior.formvalidation');
?>
<?php echo $this->loadTemplate('menu'); ?>
<script language="javascript" type="text/javascript">

Joomla.submitbutton = function (task) {
		if (document.formvalidator.isValid(document.id('adminForm')) || task == 'invitations.cancel')
		{
			if (task == 'invitations.cancel') {
				Joomla.submitform(task, document.getElementById('adminForm'));
			}
			else{
				if ((document.id("jform_count").value == '') || (document.id("jform_count").value == 0)) {
					alert("Please enter number of invitations");
					document.id("jform_count").focus();
				}
				else if (document.id("jform_survey").value == 0) {
					alert("Please, select a survey");
				}
				else{
					Joomla.submitform(task, document.getElementById('adminForm'));
				}
			}
		}
		else
		{
			alert('<?php echo $this->escape(JText::_('JGLOBAL_VALIDATION_FORM_FAILED')); ?>');
		}
};

	jQuery(document).ready(function () {
		jQuery('#configTabs a:first').tab('show');
	});

</script>
<form action="<?php echo JRoute::_('index.php?option=com_surveyforce'); ?>" enctype="multipart/form-data" method="post" name="adminForm" id="adminForm" class="form-validate">
    <input type="hidden" name="jform[date_added]" value="<?php echo JFactory::getDate(); ?>" />
    <div class="row-fluid">	   
        <div id="j-main-container" class="span9 form-horizontal">
            <ul class="nav nav-tabs" id="configTabs">
                <li> <a href="#invitations" data-toggle="tab"><?php echo JText::_('COM_SURVEYFORCE_GENERATE_INVITATIONS'); ?></a></li>
            </ul>
            <div class="tab-content">
				<div class="tab-pane" id="invitations">

                <fieldset class="adminform">
					<div class="control-group">
						<div class="control-label">
							<?php echo $this->form->getLabel('count'); ?>
						</div>
						<div class="controls">
							<?php echo $this->form->getInput('count'); ?>
						</div>
					</div>
					<div class="control-group">
						<div class="control-label">
							<?php echo $this->form->getLabel('survey'); ?>
						</div>
						<div class="controls">
							<?php echo $this->form->getInput('survey'); ?>
						</div>
					</div>

                </fieldset>
				</div>
            </div>
        </div>

        <input type="hidden" name="task" value="" />
	    <input type="hidden" name="option" value="com_surveyforce" />
	    <input type="hidden" name="id" value="<?php echo (int) $this->item->id; ?>" />
        <?php echo JHtml::_('form.token'); ?>	
</form>