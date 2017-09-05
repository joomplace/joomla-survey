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
?>
<?php echo $this->loadTemplate('menu'); ?>
<script type="text/javascript">
	jQuery(document).ready(function(){
		jQuery('label').each(function(){
			if ( jQuery(this).prop('for') == 'jform_sf_result_type0' )
			{
				jQuery(this).bind('click', function() {
					jQuery('#b_height_control').show();
					jQuery('#b_width_control').show();
					jQuery('#p_height_control').hide();
					jQuery('#p_width_control').hide();
				});
				if ( jQuery(this).hasClass('btn-success') )
				{
					jQuery('#b_height_control').show();
					jQuery('#b_width_control').show();
					jQuery('#p_height_control').hide();
					jQuery('#p_width_control').hide();
				}
			}

			if ( jQuery(this).prop('for') == 'jform_sf_result_type1' )
			{
				jQuery(this).bind('click', function() {
					jQuery('#p_height_control').show();
					jQuery('#p_width_control').show();
					jQuery('#b_height_control').hide();
					jQuery('#b_width_control').hide();
				});
				if ( jQuery(this).hasClass('btn-success') )
				{
					jQuery('#p_height_control').show();
					jQuery('#p_width_control').show();
					jQuery('#b_height_control').hide();
					jQuery('#b_width_control').hide();
				}
			}
		});
	});

</script>
<form action="<?php echo JRoute::_('index.php'); ?>" enctype="multipart/form-data" method="post" name="adminForm" id="adminForm">

    <div id="j-main-container" class="span12" style="float: right">
        <h2><?php echo JText::_('COM_SURVEYFORCE'); ?></h2>
        <hr>
        <ul class="nav nav-tabs" id="configTabs">
            <li class="active"><a href="#survey-basic"  data-toggle="tab"><?php echo JText::_('COM_SURVEYFORCE_BASIC'); ?></a></li>
            <li><a href="#survey-element-colors" data-toggle="tab"><?php echo JText::_('COM_SURVEYFORCE_ELEMENT_COLORS'); ?></a></li>
            <li><a href="#survey-result-review" data-toggle="tab"><?php echo JText::_('COM_SURVEYFORCE_RESULTS_SHOW'); ?></a></li>
            <li><a href="#survey-emails" data-toggle="tab"><?php echo JText::_('COM_SURVEYFORCE_EMAILS'); ?></a></li>
        </ul>
        <div class="tab-content">        
            <div class="tab-pane active" id="survey-basic">
                <fieldset>
                    <legend>Basic Settings</legend>
                    <div class="control-group form-inline">
                        <?php echo $this->form->getLabel('sf_enable_lms_integration') . ':' ?>
                        <div class="controls" >
                            <?php echo $this->form->getInput('sf_enable_lms_integration'); ?>
                        </div>
                    </div>
                    <div class="control-group form-inline">
                        <?php echo $this->form->getLabel('sf_enable_jomsocial_integration') . ':' ?>
                        <div class="controls" >
                            <?php echo $this->form->getInput('sf_enable_jomsocial_integration'); ?>
                        </div>
                    </div>
                    <div class="control-group form-inline">
                        <?php echo $this->form->getLabel('sf_mail_pause') . ':' ?> <?php echo $this->form->getInput('sf_mail_pause'); ?>
						<?php echo JText::_('COM_SURVEYFORCE_SECONDS_BETWEEN'); ?> <?php echo $this->form->getInput('sf_mail_count'); ?> <?php echo JText::_('COM_SURVEYFORCE_MAILS'); ?>
						<br /><small><?php echo JText::_('COM_SURVEYFORCE_IF_ONE_OF_VALUES_EQUAL_ZERO'); ?></small>
                    </div>

                    <div class="control-group form-inline">
						<?php echo $this->form->getLabel('sf_mail_maximum')?>
                        <div class="controls" >
                            <?php echo $this->form->getInput('sf_mail_maximum'); ?><br />
							<small><?php echo JText::_('COM_SURVEYFORCE_IF_EQUAL_ZERO'); ?></small>
                        </div>
                    </div>
                    <div class="control-group form-inline">
                        <?php echo $this->form->getLabel('sf_force_ssl')?>
                        <div class="controls" >
                            <?php echo $this->form->getInput('sf_force_ssl'); ?>
                        </div>
                    </div>
                    <div class="control-group form-inline">
                        <?php echo $this->form->getLabel('sf_show_dev_info')?>
                        <div class="controls" >
                            <?php echo $this->form->getInput('sf_show_dev_info'); ?>
                        </div>
                    </div>
                </fieldset>
            </div>

            <div class="tab-pane" id="survey-element-colors">
                <fieldset class="form-horizontal">
                    <legend>Colors for Drag'n'Drop panels</legend>
                    <div class="control-group">
	                    <div class="control-label">
                            <?php echo $this->form->getLabel('color_cont'); ?>
		                </div>
                        <div class="controls" >
                            <?php echo $this->form->getInput('color_cont'); ?>
                        </div>
                    </div>
                    <div class="control-group">
	                    <div class="control-label">
                            <?php echo $this->form->getLabel('color_drag'); ?>
		                </div>
                        <div class="controls" >
                            <?php echo $this->form->getInput('color_drag'); ?>
                        </div>
                    </div>
                    <div class="control-group">
	                    <div class="control-label">
                            <?php echo $this->form->getLabel('color_highlight'); ?>
	                    </div>
                        <div class="controls" >
                            <?php echo $this->form->getInput('color_highlight'); ?>
                        </div>
                    </div>
                </fieldset>
                <fieldset class="form-horizontal">
                    <legend><?php echo JText::_('COM_SURVEYFORCE_PROGRESS_BAR_STYLE_LEGEND'); ?></legend>
                    <div class="control-group">
                        <div class="control-label">
                            <?php echo $this->form->getLabel('progress_bar_style'); ?>
                        </div>
                        <div class="controls">
                            <?php echo $this->form->getInput('progress_bar_style'); ?>
                        </div>
                    </div>
                    <div class="control-group">
                        <div class="control-label">
                            <?php echo $this->form->getLabel('progress_bar_striped'); ?>
                        </div>
                        <div class="controls">
                            <?php echo $this->form->getInput('progress_bar_striped'); ?>
                        </div>
                    </div>
                    <div class="control-group">
                        <div class="control-label">
                            <?php echo $this->form->getLabel('progress_bar_animate'); ?>
                        </div>
                        <div class="controls">
                            <?php echo $this->form->getInput('progress_bar_animate'); ?>
                        </div>
                    </div>
                </fieldset>
            </div>
            <div class="tab-pane" id="survey-result-review">            
                <fieldset>
                    <legend>Type of results diagram</legend>
                    <div class="control-group form-inline">
                        <?php echo $this->form->getLabel('sf_result_type')  ?>
                        <div class="controls" >
                            <?php echo $this->form->getInput('sf_result_type'); ?>
                        </div>
                    </div>
                    <div class="control-group form-inline" id="b_height_control">
                        <?php echo $this->form->getLabel('b_height')  ?>
                        <div class="controls" >
                            <?php echo $this->form->getInput('b_height'); ?>
                        </div>
                    </div>
                    <div class="control-group form-inline" id="b_width_control">
                        <?php echo $this->form->getLabel('b_width') . ':' ?>
                        <div class="controls" >
                            <?php echo $this->form->getInput('b_width'); ?>
                        </div>
                    </div>
                    <div class="control-group form-inline" id="p_height_control">
                        <?php echo $this->form->getLabel('p_height')  ?>
                        <div class="controls" >
                            <?php echo $this->form->getInput('p_height'); ?>
                        </div>
                    </div>
                    <div class="control-group form-inline" id="p_width_control">
                        <?php echo $this->form->getLabel('p_width') . ':' ?>
                        <div class="controls" >
                            <?php echo $this->form->getInput('p_width'); ?>
                        </div>
                    </div>
                </fieldset>     
            </div>
            <div class="tab-pane" id="survey-emails">            
                <fieldset>
                    <legend>Email sending</legend>
                    <div class="control-group form-inline">
                        <?php echo $this->form->getLabel('sf_an_mail')  ?>
                        <div class="controls" >
                            <?php echo $this->form->getInput('sf_an_mail'); ?>
                        </div>
                    </div>
                    <div class="control-group form-inline">
                        <?php echo $this->form->getLabel('sf_an_mail_others') ?>
                        <div class="controls" >
                            <?php echo $this->form->getInput('sf_an_mail_others'); ?>
                            <?php echo $this->form->getInput('sf_an_mail_other_emails'); ?>
                        </div>
                    </div>
                </fieldset>
                <fieldset>
                    <legend>Email details</legend>
                    <div class="control-group form-inline">
                        <?php echo $this->form->getLabel('sf_an_mail_subject')  ?>
                        <div class="controls" >
                            <?php echo $this->form->getInput('sf_an_mail_subject'); ?>
                        </div>
                    </div>
                    <div class="control-group form-inline">
                        <?php echo $this->form->getLabel('sf_an_mail_text')  ?>
                        <div class="controls" >
                            <?php echo $this->form->getInput('sf_an_mail_text'); ?>
                        </div>
                    </div>
                    <div class="control-group form-inline">
                        <?php echo $this->form->getLabel('sf_an_mail_pattern')  ?>

                        <div class="controls" >
                            <?php echo $this->form->getInput('sf_an_mail_pattern') ?>
                        </div>
                    </div>
                </fieldset>
            </div>
        </div>
    <input type="hidden" name="task" value="configuration.save" />
    <input type="hidden" name="plugin" value="<?php echo JFactory::getApplication()->input->getCmd('plugin'); ?>" />
    <input type="hidden" name="option" value="com_surveyforce" />
    <?php echo JHtml::_('form.token'); ?>
    </div>
</form>