<?php
/**
 * Joomlaquiz component for Joomla 3.0
 * @package Joomlaquiz
 * @author JoomPlace Team
 * @Copyright Copyright (C) JoomPlace, www.joomplace.com
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */
defined('_JEXEC') or die;
$app = JFactory::getApplication();
$document = JFactory::getDocument();
$document->addStyleSheet(JURI::root() . 'administrator/components/com_surveyforce/assets/css/dashboard.css');
?>
<div id="tm-navbar" class="navbar navbar-static navbar-inverse">
    <div class="navbar-inner">
        <div class="container" style="width: auto;">
            <a class="brand" href="https://www.joomplace.com" target="_blank" rel="noopener noreferrer"><img class="tm-panel-logo" src="<?php echo JURI::root() ?>administrator/components/com_surveyforce/assets/images/joomplace-logo.png" /> <?php echo JText::_('COM_SURVEYFORCE_JOOMPLACE') ?></a>
            <ul class="nav" role="navigation">
                <li class="dropdown">
                    <a id="control-panel" href="index.php?option=com_surveyforce&view=dashboard" role="button" class="dropdown-toggle"><?php echo JText::_('COM_SURVEYFORCE_CONTROL_PANEL') ?></a>
                </li>
            </ul>
            <a class="btn btn-navbar" data-toggle="collapse" data-target=".nav-collapse-surveyforce">
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </a>
            <div class="nav-collapse-surveyforce nav-collapse collapse">
                <ul class="nav" role="navigation">
                    <li class="dropdown">
                        <a href="#" id="drop-categories-management" role="button" class="dropdown-toggle" data-toggle="dropdown"><?php echo JText::_('COM_SURVEYFORCE_MENU_CATEGORIES') ?><b class="caret"></b></a>
                        <ul class="dropdown-menu" role="menu" aria-labelledby="drop-categories-management">
                            <li role="presentation"><a role="menuitem" tabindex="-1" href="index.php?option=com_surveyforce&view=categories"><?php echo JText::_('COM_SURVEYFORCE_LIST_OF_CATEGORIES'); ?></a></li>
                            <li role="presentation"><a role="menuitem" tabindex="-1" href="index.php?option=com_surveyforce&task=category.add"><?php echo JText::_('COM_SURVEYFORCE_NEW_CATEGORY'); ?></a></li>
                        </ul>
                    </li>

                    <li class="dropdown">
                        <a href="#" id="drop-surveys-manage" role="button" class="dropdown-toggle" data-toggle="dropdown"><?php echo JText::_('COM_SURVEYFORCE_MENU_SURVEYS') ?><b class="caret"></b></a>
                        <ul class="dropdown-menu" role="menu" aria-labelledby="drop-surveys-manage">
                            <li role="presentation"><a role="menuitem" tabindex="-1" href="index.php?option=com_surveyforce&view=surveys"><?php echo JText::_('COM_SURVEYFORCE_LIST_OF_SURVEYS'); ?></a></li>
                            <li role="presentation"><a role="menuitem" tabindex="-1" href="index.php?option=com_surveyforce&view=iscales"><?php echo JText::_('COM_SURVEYFORCE_IMPORTANCE_SCALES2'); ?></a></li>
                            <li role="presentation"><a role="menuitem" tabindex="-1" href="index.php?option=com_surveyforce&task=survey.add"><?php echo JText::_('COM_SURVEYFORCE_NEW_SURVEY'); ?></a></li>
                        </ul>
                    </li>

                    <li class="dropdown">
                        <a href="#" id="drop-user-lists" role="button" class="dropdown-toggle" data-toggle="dropdown"><?php echo JText::_('COM_SURVEYFORCE_MENU_USERS') ?><b class="caret"></b></a>
                        <ul class="dropdown-menu" role="menu" aria-labelledby="drop-user-lists">
                            <li role="presentation" class="nav-header"><?php echo JText::_('COM_SURVEYFORCE_MENU_MANAGE_USERS');?></li>
                            <li role="presentation"><a role="menuitem" tabindex="-1" href="index.php?option=com_surveyforce&view=listusers"><?php echo JText::_('COM_SURVEYFORCE_MANAGE_USERS') ?></a></li>
                            <li role="presentation"><a role="menuitem" tabindex="-1" href="index.php?option=com_surveyforce&task=listuser.add"><?php echo JText::_('COM_SURVEYFORCE_NEW_LIST_OF_USERS') ?></a></li>
                            <li role="presentation"><a role="menuitem" tabindex="-1" href="index.php?option=com_surveyforce&view=invitations"><?php echo JText::_('COM_SURVEYFORCE_GENERATE_INVITATIONS') ?></a></li>
                            <li role="presentation" class="divider"></li>
                            <li role="presentation" class="nav-header"><?php echo JText::_('COM_SURVEYFORCE_MENU_MANAGE_AUTHORS');?></li>
                            <li role="presentation"><a role="menuitem" tabindex="-1" href="index.php?option=com_surveyforce&view=authors"><?php echo JText::_('COM_SURVEYFORCE_LIST_OF_AUTHORS') ?></a></li>
                            <li role="presentation"><a role="menuitem" tabindex="-1" href="index.php?option=com_surveyforce&task=authors.usersList"><?php echo JText::_('COM_SURVEYFORCE_ADD_AUTHOR') ?></a></li>
                        </ul>
                    </li>
					
                    <li class="dropdown">
                        <a href="#" id="drop-reports" role="button" class="dropdown-toggle" data-toggle="dropdown"><?php echo JText::_('COM_SURVEYFORCE_MENU_REPORTS') ?><b class="caret"></b></a>
                        <ul class="dropdown-menu" role="menu" aria-labelledby="drop-settings">
                            <li role="presentation"><a role="menuitem" tabindex="-1" href="index.php?option=com_surveyforce&view=reports"><?php echo JText::_('COM_SURVEYFORCE_REPORTS') ?></a></li>
                            <li role="presentation"><a role="menuitem" tabindex="-1" href="index.php?option=com_surveyforce&view=advreport"><?php echo JText::_('COM_SURVEYFORCE_ADVANCED_REPORTS') ?></a></li>

                        </ul>
                    </li>
					
					 <li class="dropdown">
                        <a href="#" id="drop-configuration" role="button" class="dropdown-toggle" data-toggle="dropdown"><?php echo JText::_('COM_SURVEYFORCE_MENU_CONFIGURATION') ?><b class="caret"></b></a>
                        <ul class="dropdown-menu" role="menu" aria-labelledby="drop-settings">
                        	<li role="presentation" class="nav-header"><?php echo JText::_('COM_SURVEYFORCE_MENU_MANAGE_EMAILS');?></li>
							<li role="presentation"><a role="menuitem" tabindex="-1" href="index.php?option=com_surveyforce&view=emails"><?php echo JText::_('COM_SURVEYFORCE_MANAGE_EMAILS') ?></a></li>
                            <li role="presentation"><a role="menuitem" tabindex="-1" href="index.php?option=com_surveyforce&task=email.add"><?php echo JText::_('COM_SURVEYFORCE_NEW_EMAIL') ?></a></li>
                            <li role="presentation" class="divider"></li>
                            <li role="presentation" class="nav-header"><?php echo JText::_('COM_SURVEYFORCE_MENU_OTHER_SETTINGS');?></li>
                            <li role="presentation"><a role="menuitem" tabindex="-1" href="index.php?option=com_surveyforce&view=configuration"><?php echo JText::_('COM_SURVEYFORCE_CONFIGURATION') ?></a></li>
                            <li role="presentation"><a role="menuitem" tabindex="-1" href="index.php?option=com_surveyforce&view=templates"><?php echo JText::_('COM_SURVEYFORCE_TEMPLATES') ?></a></li>

                        </ul>
                    </li>

                    <li class="dropdown">
                        <a href="#" id="drop-sample-surveys" role="button" class="dropdown-toggle" data-toggle="dropdown"><?php echo JText::_('COM_SURVEYFORCE_SAMPLE_SURVEYS') ?><b class="caret"></b></a>
                        <ul class="dropdown-menu" role="menu" aria-labelledby="drop-settings">
                            <li role="presentation"><a role="menuitem" tabindex="-1" href="index.php?option=com_surveyforce&view=samples"><?php echo JText::_('COM_SURVEYFORCE_SAMPLE_SURVEYS') ?></a></li>

                        </ul>
                    </li>

                </ul>
                <ul class="nav pull-right">
                    <li id="fat-menu" class="dropdown">
                        <a href="#" id="help" role="button" class="dropdown-toggle" data-toggle="dropdown"><?php echo JText::_('COM_SURVEYFORCE_MENU_HELP') ?><b class="caret"></b></a>
                        <ul class="dropdown-menu" role="menu" aria-labelledby="help">
                            <li role="presentation"><a role="menuitem" tabindex="-1" href="http://www.joomplace.com/video-tutorials-and-documentation/survey-force-deluxe/index.html?administrators_guide.htm" target="_blank"><?php echo JText::_('COM_SURVEYFORCE_MENU_HELP') ?></a></li>
                            <li role="presentation"><a role="menuitem" tabindex="-1" href="http://www.joomplace.com/support/helpdesk" target="_blank"><?php echo JText::_('COM_SURVEYFORCE_SUPPORT') ?></a></li>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>