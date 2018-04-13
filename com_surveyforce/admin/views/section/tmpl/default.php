<?php
/**
 * Survey Force Deluxe component for Joomla 3 3.0
 * @package Survey Force Deluxe
 * @author JoomPlace Team
 * @Copyright Copyright (C) JoomPlace, www.joomplace.com
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */
defined('_JEXEC') or die('Restricted access');
JHtml::_('behavior.framework', true);
JHtml::_('bootstrap.tooltip');
JHtml::_('behavior.formvalidation');
JHtml::_('behavior.multiselect');
JHtml::_('dropdown.init');
JHtml::_('formbehavior.chosen', 'select');
?>
<?php echo $this->loadTemplate('menu'); ?>
<script type="text/javascript">
   
    Joomla.submitbutton = function(task)
    {
        if (task == 'section.cancel' || document.formvalidator.isValid(document.id('section-form'))) {
            Joomla.submitform(task, document.getElementById('section-form'));
        }
        else {
            alert('<?php echo $this->escape(JText::_('JGLOBAL_VALIDATION_FORM_FAILED')); ?>');
        }
    }

</script>
<?php

if($this->item->sf_survey_id) {
    $surv_id = $this->item->sf_survey_id;
} elseif($this->surv_id) {
    $surv_id = $this->surv_id;
} else {
    $surv_id = '';
}

?>
<form action="<?php echo JRoute::_('index.php?option=com_surveyforce&layout=edit&id='.(int) $this->item->id); ?>" enctype="multipart/form-data" method="post" name="adminForm" id="section-form" class="form-validate">
    
    <input type="hidden" name="id" value="<?php echo ($this->item->id) ? $this->item->id : ''; ?>" />
    <input type="hidden" name="surv_id" value="<?php echo $surv_id; ?>" />
    <div class="row-fluid">	   
        <div id="j-main-container" class="span7 form-horizontal">
            <fieldset class="adminform">

                <div class="control-group form-inline">
                    <?php echo $this->form->getLabel('sf_name'); ?>
                    <div class="controls">
                        <?php echo $this->form->getInput('sf_name'); ?>
                    </div>
                </div>
				<div class="control-group form-inline">
                    <?php echo $this->form->getLabel('addname'); ?>
                    <div class="controls">
                        <?php echo $this->form->getInput('addname'); ?>
                    </div>
                </div>
                <div class="control-group form-inline">
                    <?php echo $this->form->getLabel('sf_survey_id'); ?>
                    <div class="controls">
                        <?php echo $this->form->getInput('sf_survey_id', null, $surv_id); ?>
                    </div>
                </div>
				<div class="control-group form-inline">
                    <?php echo JText::_('COM_SURVEYFORCE_QUESTIONS2'); ?>
                    <div class="controls">
                        <?php echo $this->questions['questions']; ?>
                    </div>
                </div>
      		</fieldset>
        </div>  
        <input type="hidden" name="task" value="" />
        <?php echo JHtml::_('form.token'); ?>	
    </div>
</form>
