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
	var count = <?php echo (is_array($images) && count($images) > 0) ? (count($images)+1) : 1;?>;
	function insertRow() {
		
			$('[name="add_another"]').attr('disabled', 'disabled');
		
	}
	Joomla.submitbutton = function(task)
	{
		if (task == 'category.cancel' || document.formvalidator.isValid(document.id('category-form'))) {			
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
    <div id="j-main-container" class="span7 form-horizontal">
	<ul class="nav nav-tabs" id="configTabs">
	    <li><a href="#category-details" data-toggle="tab"><?php echo  JText::_('COM_SURVEYFORCE_NEW_CATEGORY');?></a></li>	    
	</ul>
	<div class="tab-content">
	    <div class="tab-pane" id="category-details">
		<fieldset class="adminform">
			<legend><?php echo JText::_('COM_SURVEYFORCE_CATEGORY_DETAILS') ?></legend>
			
			<br style="clear:both;"/>
			
			
	    </div>
	    
	
	</div>
    </div>
    <div id="j-right-sidebar-container" class="span3">
	<div class="accordion" id="accordion2">
	    
	    <div class="accordion-group">
		<div class="accordion-heading">
		    <a class="accordion-toggle" data-toggle="collapse" data-parent="#accordion2" href="#publishing-details">
		    <?php echo JText::_('COM_SURVEYFORCE_PUBLIC_DETAILS');?>
		    </a>
		</div>
		<div id="publishing-details" class="accordion-body collapse">
		    <div class="accordion-inner">
			    <fieldset class="panelform">
					<?php echo $this->form->getLabel('id'); ?>
					<?php echo $this->form->getInput('id'); ?>
				
					<?php echo $this->form->getLabel('published'); ?>
					<?php echo $this->form->getInput('published'); ?>
				
					<?php echo $this->form->getLabel('ordering'); ?>
					<?php echo $this->form->getInput('ordering'); ?>			
					
			    </fieldset>
		    </div>
		</div>
	    </div>	   
	</div>
    </div>
<input type="hidden" name="task" value="" />
<?php echo JHtml::_('form.token'); ?>	
</div>
</form>
