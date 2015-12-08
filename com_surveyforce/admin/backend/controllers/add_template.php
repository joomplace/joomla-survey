<?php
/**
 * Survey Force Deluxe component for Joomla 3
 * @package Survey Force Deluxe
 * @author JoomPlace Team
 * @Copyright Copyright (C) JoomPlace, www.joomplace.com
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */

defined('_JEXEC') or die('Restricted access');

class SurveyforceControllerAdd_template extends JControllerForm
{

	public function cancel()
	{
		$this->setRedirect('index.php?option=com_surveyforce&view=templates');
	}

    public function add(){

        $file = $_FILES['jform'];
		if ( $file['type']['package_file'] == 'application/zip' )
		{
			jimport('joomla.filesystem.file');
			jimport('joomla.filesystem.folder');
			jimport('joomla.filesystem.archive');

			// Changing PHP settings.

			if ((int) ini_get('memory_limit') < 128)
				ini_set("memory_limit", "128M");

			if ((int) ini_get('max_execution_time') < 600)
			{
				ini_set("max_execution_time", "0");
				set_time_limit(0);
			}

			$package_name = JFile::stripExt($file['name']['package_file']);
			$package_TempPath = JFactory::getConfig()->get("tmp_path").'/';

			if ( JFile::move($file['tmp_name']['package_file'], $package_TempPath.$package_name.'.zip') )
			{
				@JFolder::create($package_TempPath.$package_name.'/', 0755);

				$package = JArchive::extract($package_TempPath.$package_name.'.zip', $package_TempPath.$package_name.'/');

				if ( $package )
				{
					JFile::delete($package_TempPath.$package_name.'.zip');
					JFolder::move($package_TempPath.$package_name, JPATH_COMPONENT_SITE.'/templates/'.$package_name);
					JFolder::delete($package_TempPath.$package_name);

					$db = JFactory::getDBO();
					$db->setQuery("DELETE FROM #__survey_force_templates WHERE sf_name = '".$db->escape($package_name)."'");
					$db->execute();

					$db->setQuery("INSERT INTO #__survey_force_templates (`sf_name`) VALUES ('".$db->escape($package_name)."')");
					$db->execute();
				}

			}
		}

		$this->setRedirect('index.php?option=com_surveyforce&view=templates');
    }
}
