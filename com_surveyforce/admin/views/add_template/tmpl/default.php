<?php
/**
 * Joomlaquiz component for Joomla 3.0
 * @package Survey Force Deluxe
 * @author JoomPlace Team
 * @Copyright Copyright (C) JoomPlace, www.joomplace.com
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */
defined('_JEXEC') or die('Restricted Access');
?>
<?php echo $this->loadTemplate('menu');?>
<form action="<?php echo JRoute::_('index.php?option=com_surveyforce&view=templates'); ?>"
      method="post" name="adminForm" id="adminForm" enctype="multipart/form-data">
	<div class="row-fluid">
		<div id="j-main-container" class="span12 form-horizontal">
			<h3><?php echo JText::_('COM_SURVEYFORCE_UPLOAD_TEMPLATE_PACKAGE'); ?></h3>
            <?php echo $this->form->renderField('package_file'); ?>
            <?php echo $this->form->renderField('archive_note'); ?>
		</div>
	</div>
	<input type="hidden" name="task" value="add_template.add" />
	<?php echo JHtml::_('form.token'); ?>
</form>