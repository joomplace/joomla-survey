<?php
/**
 * Survey Force Deluxe component for Joomla 3 3.0
 * @package Survey Force Deluxe
 * @author JoomPlace Team
 * @Copyright Copyright (C) JoomPlace, www.joomplace.com
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */
defined('_JEXEC') or die('Restricted Access');

JHtml::_('behavior.tooltip');
$listOrder  = $this->state->get('list.ordering');
$listDirn   = $this->state->get('list.direction');
$saveOrder  = $ordering = $listOrder == 'ordering';
$user       = JFactory::getUser();
$userId     = $user->get('id');

?>
<script type="text/javascript">
    var survey_id = 0;

    Joomla.submitform = function(){
        document.adminForm.submit();
    }

    function insert() {         
        if (!survey_id ) {
            alert("<?php echo JText::_('COM_SF_PLEASE_SELECT_SURVEY_TO_INSERT'); ?>");
            return;
        }
        
        var tag = 'id='+survey_id;
        
        tag = '{surveyforce '+tag+'}';

        window.parent.jInsertEditorText(tag, '<?php echo $this->eName; ?>');
        timeoutId = setTimeout(parent.SqueezeBox.close(), 2000);
        return false;
    }
    
    function getObj(name) {
      if (document.getElementById)  {  return document.getElementById(name);  }
      else if (document.all)  {  return document.all[name];  }
      else if (document.layers)  {  return document.layers[name];  }
    }
    
    function refresh_info(){
        return;     
    }
</script>

<form name="adminForm" method="post" action="<?php echo JRoute::_('index.php?option=com_surveyforce&view=insert_survey&tmpl=component'); ?>"> 
<h2><?php echo JText::_('COM_SF_SELECT_SURVEY_TO_INSERT');?>:</h2>
<div id="j-main-container" class="span12">
    <div id="filter-bar" class="btn-toolbar">
            <div class="filter-search btn-group pull-left">
                    <label for="filter_search" class="element-invisible"><?php echo JText::_('COM_SURVEYFORCE_FILETERBYTAG');?></label>
                    <input type="text" name="filter_search" id="filter_search" placeholder="<?php echo JText::_('COM_SURVEYFORCE_FILETERBYTAG'); ?>" value="<?php echo $this->escape($this->state->get('filter.search')); ?>" title="<?php echo JText::_('COM_CONTENT_FILTER_SEARCH_DESC'); ?>" />
            </div>
            <div class="btn-group pull-left">
                    <button type="submit" class="btn hasTooltip" title="<?php echo JText::_('JSEARCH_FILTER_SUBMIT'); ?>"><i class="icon-search"></i></button>
                    <button type="button" class="btn hasTooltip" title="<?php echo JText::_('JSEARCH_FILTER_CLEAR'); ?>" onclick="document.id('filter_search').value='';this.form.submit();"><i class="icon-remove"></i></button>
            </div>
        </div>
        <div class="clearfix"> </div>
<table width="100%" align="left" class="table table-striped" height="100%">
<tfoot>
    <tr>
        <td colspan="2">
            <?php echo $this->pagination->getListFooter(); ?>
        </td>
    </tr>
</tfoot>
<tbody>
<?php
foreach ($this->items as $survey) {?>
    <tr> 
        <td width="20" valign="top"><input type="radio" name="survey" id="survey<?php echo $survey->id;?>" onchange="if(this.checked){survey_id = <?php echo $survey->id;?>; refresh_info();}else{refresh_info();}" /></td>
        <td><label for="survey<?php echo $survey->id;?>" style="cursor:pointer;"><?php echo $survey->sf_name;   ?></label></td>
    </tr>
<?php }
?>
</tbody>
</table>

<input type="hidden" name="task" value="" />
<input type="hidden" name="boxchecked" value="0" />
<input type="hidden" name="tmpl" value="component" />
<input type="hidden" name="filter_order" value="<?php echo $listOrder; ?>" />
<input type="hidden" name="filter_order_Dir" value="<?php echo $listDirn; ?>" />

<?php echo JHtml::_('form.token'); ?>
</div>
</form>

<br />
<br />
<div style="float:right; clear:both;">
    <br />
    <button onclick="insert();" class="btn btn-primary"><?php echo JText::_('COM_SF_INSERT_TAG');?></button>
</div>