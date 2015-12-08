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
<?php echo $this->loadTemplate('menu'); ?>
<script type="text/javascript">
 
    Joomla.submitbutton = function(task)
    {
        if (task == 'set_default.cancel' || document.formvalidator.isValid(document.id('setdefault-form'))) {
            Joomla.submitform(task, document.getElementById('setdefault-form'));
        }
        else {
            alert('<?php echo $this->escape(JText::_('JGLOBAL_VALIDATION_FORM_FAILED')); ?>');
        }
    }
    
</script>
<form action="<?php echo JRoute::_('index.php?option=com_surveyforce&view=set_default'); ?>" enctype="multipart/form-data" method="post" name="adminForm" id="setdefault-form">
    
    <div class="row-fluid">	   
        <div id="j-main-container" class="span7 form-horizontal">
            <ul class="nav nav-tabs" id="configTabs">
                <li class="active"><a href="#setdefault-details" data-toggle="tab"><?php echo JText::_('COM_SURVEYFORCE_SET_DEFAULT'); ?></a></li>	    
            </ul>
            <div class="tab-content">
                <div class="tab-pane active" id="setdefault-details">
                    <fieldset class="adminform">
                        <?php echo $this->form;?>
                        <br style="clear:both;"/>
                </div>


            </div>
        </div>
       
        <input type="hidden" name="task" value="" />
        <input type="hidden" value="<?php echo $this->sf_qtype; ?>" name="sf_qtype">
        <input type="hidden" value="<?php echo $this->id; ?>" name="id">
        <?php echo JHtml::_('form.token'); ?>	
    </div>
</form>
