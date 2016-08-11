<?php
/**
 * Joomlaquiz component for Joomla 3.0
 * @package Joomlaquiz
 * @author JoomPlace Team
 * @Copyright Copyright (C) JoomPlace, www.joomplace.com
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */
defined('_JEXEC') or die('Restricted Access');

JHtml::_('behavior.tooltip');
JHtml::_('behavior.multiselect');
$extension  = 'com_surveyforce';

?>
<?php echo $this->loadTemplate('menu');?>
<form action="<?php echo JRoute::_('index.php?option=com_surveyforce&view=templates'); ?>" method="post" name="adminForm" id="adminForm" enctype="multipart/form-data">

	<div class="row-fluid">
		<div id="j-main-container" class="span12 form-horizontal">

			<legend><?php echo JText::_('COM_SURVEYFORCE_UPLOAD_TEMPLATE_PACKAGE'); ?></legend>

			<div class="control-group">
				<div class="control-label">
					<?php echo $this->form->getLabel('package_file'); ?>
				</div>
				<div class="controls">
					<?php echo $this->form->getInput('package_file'); ?>
				</div>
			</div>

		</div>
	</div>

	<input type="hidden" name="task" value="add_template.add" />
	<input type="hidden" name="boxchecked" value="0" />
	<?php echo JHtml::_('form.token'); ?>

</form>