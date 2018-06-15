<?php
/**
 * Survey Force Deluxe component for Joomla 3
 * @package Survey Force Deluxe
 * @author JoomPlace Team
 * @copyright Copyright (C) JoomPlace, www.joomplace.com
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */
defined('_JEXEC') or die;

$app = JFactory::getApplication();
$doc = JFactory::getDocument();
JHtml::_('jquery.framework', false, null, false);
JHtml::_('formbehavior.chosen', 'select');
JHtml::_('behavior.calendar');
JHtml::_('behavior.formvalidator');

$userState = $app->getUserState('com_surveyforce.cleanup.data');
$surveys_state = isset($userState['surveys']) ? $userState['surveys'] : array();
$date_start = isset($userState['date_start']) ? $userState['date_start'] : '';
$date_end = isset($userState['date_end']) ? $userState['date_end'] : '';

echo $this->loadTemplate('menu');
?>

<form action="<?php echo JRoute::_('index.php?option=com_surveyforce&task=cleanup.display'); ?>"
      method="post" name="adminForm" id="adminForm" class="form-validate">

    <div class="control-group span12">
        <?php echo JText::_('COM_SURVEYFORCE_CLEANUP_INSTRUCTION'); ?>
    </div>
    <div class="control-group span12">
        <div class="control-label span3">
            <label id="jform_surveys-lbl" for="jform_surveys">
                <?php echo JText::_('COM_SURVEYFORCE_CLEANUP_SELECT_SURVEY_LABEL'); ?> <span class="required">*</span>
            </label>
        </div>
        <div class="controls span9">
            <select name="jform[surveys][]" id="jform_surveys" class="inputbox" multiple required >
                <?php
                foreach($this->survey_names as $survey){
                    $selected = in_array($survey->value, $surveys_state) ? ' selected' : '';
                    echo '<option value="'.$survey->value.'" '.$selected.'>'.$survey->text.'</option>';
                }
                ?>
            </select>
            <input type="button" class="btn" id="select_all_surveys" name="select_all_surveys"
                   value="<?php echo JText::_('COM_SURVEYFORCE_CLEANUP_SELECT_ALL_SURVEYS_BUTTON'); ?>"
                   data-select="<?php echo JText::_('COM_SURVEYFORCE_CLEANUP_SELECT_ALL_SURVEYS_BUTTON'); ?>"
                   data-deselect="<?php echo JText::_('COM_SURVEYFORCE_CLEANUP_DESELECT_ALL_SURVEYS_BUTTON'); ?>" >
        </div>
    </div>
    <div class="control-group span12">
        <div class="control-label span3">
            <label id="jform_date_start-lbl" for="jform_date_start">
                <?php echo JText::_('COM_SURVEYFORCE_CLEANUP_SELECT_DATE_START_LABEL'); ?>
            </label>
        </div>
        <div class="controls span9">
            <?php echo JHtml::_('calendar', $date_start, 'jform[date_start]', 'jform_date_start', '%Y-%m-%d', array('class'=>'inputbox', 'maxlength'=>'19')); ?>
        </div>
    </div>
    <div class="control-group span12">
        <div class="control-label span3">
            <label id="jform_date_end-lbl" for="jform_date_end">
                <?php echo JText::_('COM_SURVEYFORCE_CLEANUP_SELECT_DATE_END_LABEL'); ?>
            </label>
        </div>
        <div class="controls span9">
            <?php echo JHtml::_('calendar', $date_end, 'jform[date_end]', 'jform_date_end', '%Y-%m-%d', array('class'=>'inputbox', 'maxlength'=>'19')); ?>
        </div>
    </div>
    <div class="control-group span12">
        <input type="submit" name="cleanup-submit" id="cleanup-submit" class="btn validate" value="<?php echo JText::_('COM_SURVEYFORCE_CLEANUP_SUBMIT'); ?>" />
    </div>

    <input type="hidden" name="task" value="cleanup.results_cleanup" />
    <?php echo JHtml::_('form.token'); ?>
</form>
<script>
    jQuery(function($) {
        'use strict';
        var selectAll = false;

        $('#select_all_surveys').on('click', function() {
            if(!selectAll) {
                $('#jform_surveys option').prop('selected', true).trigger('liszt:updated');
                $(this).val($(this).attr('data-deselect'));
                selectAll = true;
            } else {
                $('#jform_surveys option').prop('selected', false).trigger('liszt:updated');
                $(this).val($(this).attr('data-select'));
                selectAll = false;
            }
        });

        $('#cleanup-submit').on('click', function(e) {
            if(!$('#adminForm .invalid').length) {
                e.preventDefault();
                if (confirm('<?php echo JText::_('COM_SURVEYFORCE_CLEANUP_CONFIRM'); ?>')) {
                    $('#adminForm').submit();
                }
            }
        });
    });
</script>