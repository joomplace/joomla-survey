<?php
/**
* SurveyForce plugin for JomSocial Component
* @package SurveyForce Deluxe plugin
* @subpackage surveyforce.php
* @author JoomPlace Team
* @Copyright Copyright (C) JoomPlace, www.joomplace.com
* @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
*/

// no direct access
defined('_JEXEC') or die('Restricted access');

require_once( JPATH_BASE . DIRECTORY_SEPARATOR . 'components' . DIRECTORY_SEPARATOR . 'com_community' . DIRECTORY_SEPARATOR . 'libraries' . DIRECTORY_SEPARATOR . 'core.php');

if(!class_exists('plgCommunitySurveyforce'))
{
	class plgCommunitySurveyforce extends CApplications
	{
		public $name 		= "surveyforce";
		public $_name		= 'surveyforce';
		var $_path		= '';

	    function plgCommunitySurveyforce($subject, $config)
	    {
			parent::__construct($subject, $config);

			$this->_path	= JPATH_ROOT . DIRECTORY_SEPARATOR . 'administrator' . DIRECTORY_SEPARATOR . 'components' . DIRECTORY_SEPARATOR . 'com_surveyforce';
			JPlugin::loadLanguage('plg_surveyforce', JPATH_ADMINISTRATOR);
	    }

		function onProfileDisplay()
		{
			// Get the document object
			$document	=& JFactory::getDocument();
			$my			= CFactory::getUser();
			$user		= CFactory::getRequestUser();

			// Attach surveyforce.js to this page so that the editor can load up nicely.
			$document->addScript( JURI::base() . 'components'.DIRECTORY_SEPARATOR.'com_surveyforce'.DIRECTORY_SEPARATOR.'assets'.DIRECTORY_SEPARATOR.'js'.DIRECTORY_SEPARATOR.'surveyforce.js' );
			$document->addStyleSheet( JURI::base() . 'components'.DIRECTORY_SEPARATOR.'com_surveyforce'.DIRECTORY_SEPARATOR.'assets'.DIRECTORY_SEPARATOR.'css'.DIRECTORY_SEPARATOR.'surveywindow.css' );
			$document->addStyleSheet( JURI::base() . 'plugins'.DIRECTORY_SEPARATOR.'community'.DIRECTORY_SEPARATOR.'surveyforce'.DIRECTORY_SEPARATOR.'surveyforce'.DIRECTORY_SEPARATOR.'style.css' );

			// Test if surveyforce exists
			if( !file_exists( $this->_path ) )
			{
				$contents = "<table>
							<tr>
								<td style=\"vertical-align: top;padding:4px\">
					            <img src='".JURI::base()."components/com_community/assets/error.gif' alt='' />
					        	</td>
					        	<td style=\"vertical-align: top;padding:4px\">
								 " .JText::_('PLG_SURVEYFORCE_NOT_INSTALLED') . "
								</td>
							</tr>
							</table>";
			}
			else
			{
				$mainframe =& JFactory::getApplication();

				$userId = $user->id;
				$userName = $user->getDisplayName();

				$isOwner	= ($my->id == $userId ) ? true : false;

				$rows	= $this->_getEntries($isOwner);
				if($rows){
					$data_exist = 1;
				}else{
					$data_exist = 0;
				}

				$caching = $this->params->get('cache', 1);
				if($caching)
				{
					$caching = $mainframe->getCfg('caching');
				}

				$cache =& JFactory::getCache('plgCommunitySurveyforce');
				$cache->setCaching($caching);
				$callback = array('plgCommunitySurveyforce', '_getSurveyForceHTML');
				$contents = $cache->call($callback, $data_exist, $rows, $userId, $userName, $isOwner, $this->params);
			}

			return $contents;
		}

		function _getSurveyForceHTML($data_exist, $rows, $userId, $userName, $isOwner, $params){
			ob_start();
			$writeNewLink = '<div  class="surveymanage"><a class="sfbutton" href="javascript:void(0);" onclick="mySurveyShowWindow(\''.JURI::root().'index.php?option=com_surveyforce&view=authoring&tmpl=component&keepThis=true&TB_iframe=true\');">
								<span>'.JText::_("PLG_SURVEYFORCE_MANAGEMENT").'</span>
							</a></div><br/>';

			if($isOwner) {
				echo $writeNewLink;
			}
 			if ($data_exist) {
				foreach( $rows as $row ){
					$surveyLink = "mySurveyShowWindow('".JRoute::_("index.php?option=com_surveyforce&view=survey&id=".$row->id)."&tmpl=component&keepThis=true&TB_iframe=true');";
				?>
					<div style="margin: 10px 0 20px;">
						<div class="survey_title">
							<span>
								<a href="javascript:void(0);" onclick="<?php echo $surveyLink;?>" ><?php echo $row->sf_name;?></a>
							</span>
						</div>
						<div class="survey_sdescription">
							<?php echo $row->surv_short_descr;?>
						</div>
					</div>
				<?php
				}
			}

			$content	= ob_get_contents();
			ob_end_clean();
			return $content;
		}

		function _getEntries($isOwner){

			$my			= CFactory::getUser();
			$db		=& JFactory::getDBO();

			$order_by = $this->params->get('order_by', 'ordering');
			$order = $this->params->get('order', 'DESC');
			$limit = $this->params->get('count', 5);

			$date	= new JDate();
			$now	= $date->__toString();

			if (!$isOwner) {
				$user		= CFactory::getRequestUser();
				$isConnected	= CFriendsHelper::isConnected( $my->id , $user->id );
				if ($isConnected) {
					$query = "SELECT * FROM `#__survey_force_survs` WHERE `sf_author` = '".$user->id."' AND ( sf_public = 1 OR sf_reg = 1 OR sf_friend = 1)";
				} elseif ($my->id) {
					$query = "SELECT * FROM `#__survey_force_survs` WHERE `sf_author` = '".$user->id."' AND ( sf_public = 1 OR sf_reg = 1 )";
				} else {
					$query = "SELECT * FROM `#__survey_force_survs` WHERE `sf_author` = '".$user->id."' AND ( sf_public = 1 )";
				}
			} else {
				$query = "SELECT * FROM `#__survey_force_survs` WHERE `sf_author` = '".$my->id."'";
			}

			$db->SetQuery( $query );

			$rows = $db->loadObjectList();

			if($db->getErrorNum()) {
                JFactory::getApplication()->enqueueMessage($db->stderr(), 'error');
		    }

			return $rows;
		}
	}
}

