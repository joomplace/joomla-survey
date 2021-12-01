<?php
/**
 * Survey Force Deluxe component for Joomla 3 3.0
 * @package Survey Force Deluxe
 * @author JoomPlace Team
 * @Copyright Copyright (C) JoomPlace, www.joomplace.com
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */
defined('_JEXEC') or die;
JHtml::_('bootstrap.tooltip');
JHtml::_('behavior.formvalidation');
JHtml::_('behavior.multiselect');
JHtml::_('dropdown.init');
JHtml::_('formbehavior.chosen', 'select');

jimport('joomla.filesystem.file');
$user = JFactory::getUser();
$images = (!empty($this->item->images)) ? explode("|", $this->item->images) : '';
SurveyforceHelper::addFileUploadFull('index.php?option=com_surveyforce&task=images.addImage&id=' . (int) $this->item->id, 'topic-form', 0);
?>
<?php echo $this->loadTemplate('menu'); ?>
<script type="text/javascript">
	var count = <?php echo (is_array($images) && count($images) > 0) ? (count($images) + 1) : 1; ?>;
	function insertRow() {
		if (count < 4) {
			$('#filelist').append('<input type="file" name="jform[image][]" value="" /><br style="clear: both;"/>');
			count = count + 1;
		} else {
			$('[name="add_another"]').attr('disabled', 'disabled');
		}
	}
	Joomla.submitbutton = function(task)
	{
		if (task == 'survey.cancel' || document.formvalidator.isValid(document.id('topic-form'))) {
			<?php echo $this->form->getField('sf_descr')->save(); ?>
			Joomla.submitform(task, document.getElementById('topic-form'));
		}
		else {
			alert('<?php echo $this->escape(JText::_('JGLOBAL_VALIDATION_FORM_FAILED')); ?>');
		}
	}
	jQuery(document).ready(function() {
		jQuery('#topic-form').bind('fileuploaddone', function(e, data) {
			for (var key in data.result) {
				if (data.result[key].status == 'ok') {
					jQuery('#jform_exist_images').val(jQuery('#jform_exist_images').val() + '|' + data.result[key].image);
				}
			}
		});
		jQuery('#topic-form').bind('fileuploaddestroy', function(e, data) {
			var filename = data.url.mb_substring(data.url.indexOf("image=") + 6);
			jQuery('#remove_image').val(jQuery('#remove_image').val() + '|' + filename);
		});
		jQuery('#configTabs a:first').tab('show');
	});
</script>
<?php $params = JComponentHelper::getParams('com_surveyforce')->toObject(); ?>

<form action="<?php echo JRoute::_('index.php?option=com_surveyforce&layout=edit&id=' . (int) $this->item->id); ?>" enctype="multipart/form-data" method="post" name="adminForm" id="topic-form" class="form-validate">
<input type="hidden" name="jform[date_added]" value="<?php echo JFactory::getDate(); ?>" />
<legend><?php echo JText::_('COM_SURVEYFORCE_NEW_SURVEY'); ?></legend>
<div class="row-fluid">
<div id="j-main-container" class="span7 form-horizontal">
<ul class="nav nav-tabs" id="configTabs">
	<li><a href="#survey-details" data-toggle="tab"><?php echo JText::_('COM_SURVEYFORCE_SURVEY_DETAILS'); ?></a></li>
	<li><a href="#survey-settings" data-toggle="tab"><?php echo JText::_('COM_SURVEYFORCE_SURVEY_SETTINGS'); ?></a></li>
	<li><a href="#survey-final-page" data-toggle="tab"><?php echo JText::_('COM_SURVEYFORCE_FINAL_PAGE2'); ?></a></li>
</ul>
<div class="tab-content">
<div class="tab-pane" id="survey-details">
	<div class="control-group form-inline">
		<?php echo $this->form->getLabel('sf_name'); ?>
		<div class="controls">
			<?php echo $this->form->getInput('sf_name'); ?>
		</div>
	</div>
	<div class="control-group form-inline">
		<?php echo $this->form->getLabel('sf_descr'); ?>
		<div class="controls">
			<?php echo $this->form->getInput('sf_descr'); ?>
		</div>
	</div>
	<div class="control-group form-inline">
		<?php echo $this->form->getLabel('surv_short_descr'); ?>
		<div class="controls">
			<?php echo $this->form->getInput('surv_short_descr'); ?>
		</div>
	</div>
	<div class="control-group form-inline">
		<?php echo $this->form->getLabel('sf_enable_descr'); ?>
		<div class="controls">
			<?php echo $this->form->getInput('sf_enable_descr'); ?>
		</div>
	</div>
</div>
<div class="tab-pane" id="survey-settings">

	<div class="control-group form-inline">
		<?php echo $this->form->getLabel('sf_author'); ?>
		<div class="controls">
			<?php echo $this->form->getInput('sf_author'); ?>
		</div>
	</div>
	
	<div class="control-group form-inline">
		<?php echo $this->form->getLabel('sf_image'); ?>
		<div class="controls">
			<?php echo $this->form->getInput('sf_image'); ?>
		</div>
	</div>

	<div class="control-group form-inline">
		<?php echo $this->form->getLabel('sf_progressbar'); ?>
		<div class="controls">
			<?php echo $this->form->getInput('sf_progressbar'); ?>
		</div>
	</div>

	<div class="control-group form-inline">
		<?php echo $this->form->getLabel('sf_progressbar_type'); ?>
		<div class="controls">
			<?php echo $this->form->getInput('sf_progressbar_type'); ?>
		</div>
	</div>

	<div class="control-group form-inline">
		<?php echo $this->form->getLabel('sf_template'); ?>
		<div class="controls">
			<?php echo $this->form->getInput('sf_template'); ?>
		</div>
	</div>

	<div class="control-group form-inline">
		<?php echo $this->form->getLabel('sf_cat'); ?>
		<div class="controls">
			<?php echo $this->form->getInput('sf_cat'); ?>
		</div>
	</div>

	<div class="control-group form-inline">
		<?php echo $this->form->getLabel('sf_date_started'); ?>
		<div class="controls">
			<?php echo $this->form->getInput('sf_date_started'); ?>
		</div>
	</div>
		<div class="control-group form-inline">
		<?php echo $this->form->getLabel('sf_date_expired'); ?>
		<div class="controls">
			<?php echo $this->form->getInput('sf_date_expired'); ?>
		</div>
	</div>

	<div class="control-group form-inline">
		<?php echo $this->form->getLabel('published'); ?>
		<div class="controls">
			<?php echo $this->form->getInput('published'); ?>
		</div>
	</div>

	<div class="control-group form-inline">
		<?php echo $this->form->getLabel('sf_random'); ?>
		<div class="controls">
			<?php echo $this->form->getInput('sf_random'); ?>
		</div>
	</div>

	<div class="control-group form-inline">
		<?php echo $this->form->getLabel('sf_auto_pb'); ?>
		<div class="controls">
			<?php echo $this->form->getInput('sf_auto_pb'); ?>
		</div>
	</div>

	<div class="control-group form-inline">
		<?php echo $this->form->getLabel('sf_public'); ?>
		<div class="controls">
			<?php echo $this->form->getInput('sf_public'); ?>
		</div>
	</div>

	<div class="control-group form-inline">
		<?php echo $this->form->getLabel('sf_pub_voting'); ?>
		<div class="controls">
			<?php echo $this->form->getInput('sf_pub_voting'); ?>
		</div>
	</div>

	<div class="control-group form-inline">
		<?php echo $this->form->getLabel('sf_pub_control'); ?>
		<div class="controls">
			<?php echo $this->form->getInput('sf_pub_control'); ?>
		</div>
	</div>

	<div class="control-group form-inline">
		<?php echo $this->form->getLabel('sf_reg'); ?>
		<div class="controls">
			<?php echo $this->form->getInput('sf_reg'); ?>
		</div>
	</div>

	<div class="control-group form-inline">
		<?php echo $this->form->getLabel('sf_reg_voting'); ?>
		<div class="controls">
			<?php echo $this->form->getInput('sf_reg_voting'); ?>
		</div>
	</div>

	<div class="control-group form-inline">
		<?php echo $this->form->getLabel('sf_invite'); ?>
		<div class="controls">
			<?php echo $this->form->getInput('sf_invite'); ?>
		</div>
	</div>

	<div class="control-group form-inline">
		<?php echo $this->form->getLabel('sf_inv_voting'); ?>
		<div class="controls">
			<?php echo $this->form->getInput('sf_inv_voting'); ?>
		</div>
	</div>

	<?php if ( @$params->sf_enable_jomsocial_integration ) { ?>

		<div class="control-group form-inline">
			<?php echo $this->form->getLabel('sf_friend'); ?>
			<div class="controls">
				<?php echo $this->form->getInput('sf_friend'); ?>
			</div>
		</div>

		<div class="control-group form-inline">
			<?php echo $this->form->getLabel('sf_friend_voting'); ?>
			<div class="controls">
				<?php echo $this->form->getInput('sf_friend_voting'); ?>
			</div>
		</div>

	<?php } ?>

	<div class="control-group form-inline">
		<?php echo $this->form->getLabel('sf_special'); ?>
		<div class="controls">
			<?php echo $this->form->getInput('sf_special'); ?>
		</div>
	</div>

	<div class="control-group form-inline">
		<?php echo $this->form->getLabel('sf_prev_enable'); ?>
		<div class="controls">
			<?php echo $this->form->getInput('sf_prev_enable'); ?>
		</div>
	</div>

</div>



<div class="tab-pane" id="survey-final-page">

	<div class="control-group form-inline">
		<?php echo $this->form->getLabel('sf_after_start'); ?>
		<div class="controls">
			<?php echo $this->form->getInput('sf_after_start'); ?>
		</div>
	</div>

	<div class="control-group form-inline">
		<?php echo $this->form->getLabel('sf_fpage_type'); ?>
		<div class="controls">
			<?php echo $this->form->getInput('sf_fpage_type'); ?>
		</div>
	</div>

	<div class="control-group form-inline">
		<?php echo $this->form->getLabel('sf_fpage_text'); ?>
		<div class="controls">
			<?php echo $this->form->getInput('sf_fpage_text'); ?>
		</div>
	</div>

	<div class="control-group form-inline">
		<?php echo $this->form->getLabel('sf_redirect_enable'); ?>
		<div class="controls">
			<?php echo $this->form->getInput('sf_redirect_enable'); ?>
		</div>
	</div>

	<div class="control-group form-inline">
		<?php echo $this->form->getLabel('sf_redirect_url'); ?>
		<div class="controls">
			<?php echo $this->form->getInput('sf_redirect_url'); ?>
		</div>
	</div>

	<div class="control-group form-inline">
		<?php echo $this->form->getLabel('sf_redirect_delay'); ?>
		<div class="controls">
			<?php echo $this->form->getInput('sf_redirect_delay'); ?>
		</div>
	</div>
</div>
</div>
</div>
<input type="hidden" name="task" value="" />
<?php echo $this->form->getInput('id'); ?>
<?php echo $this->form->getInput('asset_id'); ?>
<?php echo JHtml::_('form.token'); ?>
</div>
</form>
<script>
    //Remember selected tabs
    jQuery(function($){
        'use strict';

        let params = (new URL(document.location)).searchParams,
            id = params.get('id');

        if(id === null) {
            id = 0;
        }

        let surveyTabName = getCookie('surveyTab'+id);
        if (surveyTabName) {
            $('.nav-tabs li').each(function () {
                $(this).removeClass('active');
                if ($('a', this).attr('href') == '#' + surveyTabName) {
                    $('a', this).trigger('click');
                }
            });
        }

        $('.nav-tabs a').on('click', function() {
            if ($(this).hasClass('active')) {
                return false;
            }
            tabSetCookie($(this).attr('href'));
        });

        function tabSetCookie(tabName) {
            document.cookie = 'surveyTab' + id + '=' + tabName.split('#')[1];
        }

        function getCookie(name) {
            let matches = document.cookie.match(new RegExp(
                "(?:^|; )" + name.replace(/([\.$?*|{}\(\)\[\]\\\/\+^])/g, '\\$1') + "=([^;]*)"
            ));
            return matches ? decodeURIComponent(matches[1]) : undefined;
        }
    });
</script>
