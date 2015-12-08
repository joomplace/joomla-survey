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
   	
   	userArray = new Array;	
	<?php
		foreach($this->users as $user) {
			echo "userArray[".$user->value."] = ['".$user->name."','".$user->text."','".$user->email."']; ";
		}
	?>
	
	Joomla.submitbutton = function(task) {

		var form = document.adminForm;
		if (task == 'user.cancel') {
			Joomla.submitform( task, form );
			return;
		}
		
		// do field validation
		var reg_email = /[0-9a-z_]+@[0-9a-z_^.]+.[a-z]{2,3}/;
		if (document.getElementById('jform_name').value == ""){
			alert( "<?php echo JText::_('COM_SURVEYFORCE_USER_MUST_HAVE_NAME'); ?>" );
		} else if (document.getElementById('jform_lastname').value == ""){
			alert( "<?php echo JText::_('COM_SURVEYFORCE_USER_MUST_HAVE_LASTNAME'); ?>" );
		} else if (document.getElementById('jform_email').value == ""){
			alert( "<?php echo JText::_('COM_SURVEYFORCE_USER_MUST_HAVE_EMAIL'); ?>" );
		} else if (!reg_email.test(document.getElementById('jform_email').value)) {
			alert("<?php echo JText::_('COM_SURVEYFORCE_PLEASE_ENTER_VALID_EMAIL'); ?>");
		} else {
            Joomla.submitform(task, document.getElementById('user-form'));
		}
	}
	
	function changeUserSelect(c_e) {

		var form = document.adminForm;
		var sel_value = c_e.options[c_e.selectedIndex].value;			
		document.getElementById('jform_name').value = '';
		document.getElementById('jform_lastname').value = '';
		document.getElementById('jform_email').value = '';
		if (sel_value > 0) {
			document.getElementById('jform_name').value = userArray[sel_value][1];
			document.getElementById('jform_lastname').value = userArray[sel_value][0];
			document.getElementById('jform_email').value = userArray[sel_value][2];
		}	

	}
</script>
<form action="<?php echo JRoute::_('index.php?option=com_surveyforce&layout=edit&id=' . (int) $this->item->id); ?>" enctype="multipart/form-data" method="post" name="adminForm" id="user-form" class="form-validate">
     <div class="row-fluid">	   
        <div id="j-main-container" class="span7 form-horizontal">
        	<ul class="nav nav-tabs" id="configTabs">
                <li class="active"><a href="#user-details" data-toggle="tab"><?php echo JText::_('COM_SURVEYFORCE_NEW_EMAIL'); ?></a></li>	    
            </ul>
            <div class="tab-content">
                <div class="tab-pane active" id="user-details">
                    <fieldset class="adminform">
                        <legend><?php echo JText::_('COM_SURVEYFORCE_USER_DETAILS') ?></legend>
                        <div class="control-group form-inline">
                            <?php echo $this->form->getLabel('name'); ?>
                            <div class="controls">
                                <?php echo $this->form->getInput('name'); ?>
                            </div>
                        </div>
                        <div class="control-group form-inline">
                            <?php echo $this->form->getLabel('lastname'); ?>
                            <div class="controls">
                                <?php echo $this->form->getInput('lastname'); ?>
                            </div>
                        </div>
                        <div class="control-group form-inline">
                            <?php echo $this->form->getLabel('email'); ?>
                            <div class="controls">
                                <?php echo $this->form->getInput('email'); ?>
                            </div>
                        </div>
                        <div class="control-group form-inline">
                            <?php echo $this->form->getLabel('list_id'); ?>
                            <div class="controls">
                                <?php echo $this->form->getInput('list_id'); ?>
                            </div>
                        </div>
                        <div class="control-group form-inline">
                            <?php echo JText::_('COM_SURVEYFORCE_SELECT_REGISTERED').':'; ?>
                            <div class="controls">
                                <?php echo $this->reg_users; ?>
                            </div>
                        </div>
                        
                    </fieldset>
                </div>
            </div>
        </div>
    <input type="hidden" name="task" value="" />
    <input type="hidden" name="listid" value="<?php echo $this->listid; ?>" />
	<?php echo JHtml::_('form.token'); ?>
	</div>
</form>
