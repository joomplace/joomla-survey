<?php
/**
 * Survey Force Deluxe component for Joomla 3
 * @package Survey Force Deluxe
 * @author JoomPlace Team
 * @copyright Copyright (C) JoomPlace, www.joomplace.com
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */
defined('_JEXEC') or die;
JHtml::_('behavior.tooltip');
JHtml::_('behavior.formvalidation');
JHtml::_('behavior.keepalive');
JHtml::_('formbehavior.chosen', 'select');
$app = JFactory::getApplication();
$input = $app->input;
$surveys_list = $this->surveys;
$ordering_list = $this->ordering_list;

?>
<?php echo $this->loadTemplate('menu'); ?>

<script type="text/javascript">
    
    var questions = new Array;
	<?php
	$i = 0;
	$c = 0;
        
	foreach ($surveys_list as $survey) {
	    foreach ($ordering_list as $question) {
	        $covert_question = str_replace("\r\n", "", addslashes(substr(strip_tags($question->text), 0, 50)));
	        if ($question->sf_survey == $survey->value) {
	            echo "questions.push(new Array( '{$survey->value}','" . addslashes(trim($question->id)) . "','" . trim( str_replace("\n", '', $covert_question) ) . "' ));\n\t\t";
	        }
	    }
	}
    
	?>
    Joomla.submitbutton = function(task)
    {
    	if(typeof window.parent.tinyMCE !== 'undefined'){
	        if (window.parent.tinyMCE.get('jform_sf_qtext').getContent() == '' && jQuery('#jform_sf_qtext').val() != '') {
	            window.parent.tinyMCE.get('jform_sf_qtext').setContent(jQuery('#jform_sf_qtext').val());
	        }

	        if (task != 'question.cancel' && window.parent.tinyMCE.get('jform_sf_qtext').getContent() == '') {
	            alert('<?php echo $this->escape(JText::_('JGLOBAL_VALIDATION_FORM_FAILED')); ?>');
	            return false;
	        }
	    }

        if( task != 'question.cancel' && document.getElementById('jform_sf_survey').options[document.getElementById('jform_sf_survey').selectedIndex].value == '0'){
            alert("Select survey, please!");
            return false;
        }

        if (task == 'question.cancel' || document.formvalidator.isValid(document.id('adminForm'))) {
            Joomla.submitform(task, document.getElementById('adminForm'));
        }
        else {
            alert('<?php echo $this->escape(JText::_('JGLOBAL_VALIDATION_FORM_FAILED')); ?>');
        }
    }

    
</script>
<form action="<?php echo JRoute::_('index.php?option=com_surveyforce&view=question&layout=edit&id=' . (int) $this->item->id); ?>" enctype="multipart/form-data" method="post" name="adminForm" id="adminForm" class="form-validate">
    <div id="j-main-container" class="span7 form-horizontal">
        <ul class="nav nav-tabs" id="questionTabs">
            <li class="active"><a href="#question-details" data-toggle="tab"><?php echo JText::_('COM_SURVEYFORCE_QUESTION'); ?></a></li>            
            <?php if ($this->options[0]): ?>
                <li><a href="#question-options" data-toggle="tab"><?php echo JText::_('COM_SURVEYFORCE_QUESTION_OPTIONS'); ?></a></li>                
            <?php endif; ?>            
        </ul>
        <div class="tab-content">
            <div class="tab-pane active" id="question-details">
                <fieldset class="adminform">
                    <legend><?php echo JText::_('COM_SURVEYFORCE_QUESTION') ?></legend>
                    <div class="control-group">
                        <div class="control-label">
                            <?php echo $this->form->getLabel('sf_qtext'); ?>
                        </div>
                        <div class="controls">
                            <?php echo $this->form->getInput('sf_qtext'); ?>
							<?php if ( $this->item->sf_qtype == 4 ) { ?>
								<b><small><?php echo JText::_('COM_SURVEYFORCE_EVERY_X_IN_QUESTION')?></small></b>
							<?php } ?>
                        </div>
                    </div>

                    <div class="control-group">
                        <div class="control-label">
                            <?php echo $this->form->getLabel('sf_survey'); ?>
                        </div>
                        <div class="controls">
                            <?php echo $this->form->getInput('sf_survey'); ?>
                        </div>
                    </div>
                     <div class="control-group">
                        <div class="control-label">
                            <?php echo $this->form->getLabel('sf_impscale'); ?>
                        </div>
                        <div class="controls">
                            <?php echo $this->form->getInput('sf_impscale'); ?>
                            <input type="button" class="btn" name="Define new" onClick="javascript: document.adminForm.task.value='iscale.add';document.adminForm.submit();" value="<?php echo JText::_('COM_SURVEYFORCE_DEFINE_NEW'); ?>" style="vertical-align:top;">
                        </div>
                    </div>
                    <div class="control-group">
                        <div class="control-label">
                            <?php echo $this->form->getLabel('published'); ?>
                        </div>
                        <div class="controls">
                            <?php echo $this->form->getInput('published'); ?>
                        </div>
                    </div>
                    <div class="control-group">
                        <div class="control-label">
                            <?php echo $this->form->getLabel('sf_compulsory'); ?>
                        </div>
                        <div class="controls">
                            <?php echo $this->form->getInput('sf_compulsory'); ?>
                        </div>
                    </div>
                     <div class="control-group">
                        <div class="control-label">
                            <?php echo $this->form->getLabel('sf_default_hided'); ?>
                        </div>
                        <div class="controls">
                            <?php echo $this->form->getInput('sf_default_hided'); ?>
                        </div>
                    </div>
                    <div class="control-group">
                        <div class="control-label">
                            <?php echo $this->form->getLabel('is_final_question'); ?>
                        </div>
                        <div class="controls">
                            <?php echo $this->form->getInput('is_final_question'); ?>
                        </div>
                    </div>
					<?php if(!$this->item->id){ ?>
						<div class="control-group">
							<div class="control-label">
								<?php echo $this->form->getLabel('insert_pb'); ?>
							</div>
							<div class="controls">
								<?php echo $this->form->getInput('insert_pb'); ?>
							</div>
						</div>
					<?php } ?>
                    <br/>
                    <br/>
                </fieldset>            
            </div>
            <?php if ($this->options != ''): ?>
                <div class="tab-pane" id="question-options">
                    <fieldset class="adminform">
                       <?php echo $this->options; ?>
                       <br/>
                    	<br/>
                    </fieldset>
                </div>
            <?php endif; ?>
        </div>
    </div>
    <input type="hidden" name="task" value = "" />
    <input type="hidden" name="option" value="com_surveyforce" />
    <input type="hidden" name="quest_id" value="<?php echo $this->item->id; ?>" />
    <input type="hidden" name="jform[id]" value="<?php echo $this->item->id; ?>" />
    <input type="hidden" name="jform[sf_qtype]" value="<?php echo $this->item->sf_qtype; ?>" />
    <input type="hidden" name="return" value="<?php echo $input->getCmd('return'); ?>" />
    <?php echo JHtml::_('form.token'); ?>
</form>

