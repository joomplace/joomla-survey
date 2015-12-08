<?php
/**
* Survey Force Deluxe component for Joomla 3 3.0
* @package Survey Force Deluxe
* @author JoomPlace Team
* @Copyright Copyright (C) JoomPlace, www.joomplace.com
* @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
*/
defined('_JEXEC') or die('Restricted access');
JHtml::_('bootstrap.tooltip');
JHtml::_('behavior.formvalidation');
?>
<?php echo $this->loadTemplate('menu');?>
<script type="text/javascript">
	
	Joomla.submitbutton = function(task)
	{
		if (task == 'category.cancel' || document.formvalidator.isValid(document.id('category-form'))) {
			<?php echo $this->form->getField('sf_catdescr')->save(); ?>
			Joomla.submitform(task, document.getElementById('category-form'));
		}
		else {
			alert('<?php echo $this->escape(JText::_('JGLOBAL_VALIDATION_FORM_FAILED'));?>');
		}
	}
	jQuery(document).ready(function () {
	    
	    jQuery('#configTabs a:first').tab('show');
	});
</script>
<form action="<?php echo JRoute::_('index.php?option=com_surveyforce&layout=edit&id='.(int) $this->item->id); ?>" enctype="multipart/form-data" method="post" name="adminForm" id="category-form" class="form-validate">
    <input type="hidden" name="jform[date_added]" value="<?php echo JFactory::getDate();?>" />
<div class="row-fluid">	   
    <div id="j-main-container" class="span9 form-horizontal">
	<ul class="nav nav-tabs" id="configTabs">
	    <li><a href="#category-details" data-toggle="tab"><?php echo  JText::_('COM_SURVEYFORCE_NEW_CATEGORY');?></a></li>	    
	</ul>
	<div class="tab-content">
	    <div class="tab-pane" id="category-details">
		<fieldset class="adminform">
			<legend><?php echo JText::_('COM_SURVEYFORCE_CATEGORY_DETAILS') ?></legend>
			<div class="control-group form-inline">
				<?php echo JText::_('COM_SURVEYFORCE_NAME').':' ?>
				<div class="controls">
					<?php echo $this->form->getInput('sf_catname'); ?>
				</div>
			</div>
			<div class="control-group form-inline">
				<?php echo JText::_('COM_SURVEYFORCE_DESCRIPTION').':' ?>
				<div class="controls">
					<?php echo $this->form->getInput('sf_catdescr'); ?>
				</div>
			</div>
			<br style="clear:both;"/>
			
                </fieldset>	
	    </div>
	    
	
	</div>
    </div>
   
<input type="hidden" name="task" value="" />
<?php echo JHtml::_('form.token'); ?>	
</div>
</form>
