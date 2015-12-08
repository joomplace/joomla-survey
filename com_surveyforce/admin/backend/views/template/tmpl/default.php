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
		Joomla.submitform(task, document.getElementById('adminForm'));
		return true;
	}
</script>
<form action="<?php echo JRoute::_('index.php?option=com_surveyforce&view=template&id='.(int) $this->item->id); ?>" enctype="multipart/form-data" method="post" name="adminForm" id="adminForm" class="form-validate">
	<input type="hidden" name="jform[date_added]" value="<?php echo JFactory::getDate();?>" />
	<div class="row-fluid">
		<div id="j-main-container" class="span12 form-horizontal" style="padding-right: 15px ! important;">
			<fieldset class="adminform">
				<legend><?php echo JText::_('COM_SURVEYFORCE_TEMPLATE_CSS_EDITOR') ?></legend>
				<div class="control-group form-inline" style="margin: 0 0 10px">
					<b><?php echo $this->item->filepath; ?></b>
				</div>
				<div class="control-group form-inline">
					<p class="label"><?php echo JText::_('COM_SURVEYFORCE_TOGGLE_FULL_SCREEN'); ?></p>
					<div class="clr"></div>
					<div class="editor-border">
						<?php echo $this->form->getInput('sf_csscode'); ?>
					</div>
				</div>
				<br style="clear:both;"/>

			</fieldset>

		</div>
		<input type="hidden" name="task" value="" />
		<?php echo JHtml::_('form.token'); ?>
	</div>
</form>
