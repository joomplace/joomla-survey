<?php
/**
 * SurveyForce Delux Component for Joomla 3
 * @package Survey Force Deluxe
 * @author JoomPlace Team
 * @Copyright Copyright (C) JoomPlace, www.joomplace.com
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */
defined('_JEXEC') or die('Restricted access');

/**
 * Surveyforce Component Controller
 */
class SurveyforceController extends JControllerLegacy {

	/**
	 * display task
	 *
	 * @return void
	 */
	public function __construct($config = array()) {

		parent::__construct($config);

	}
	public function display($cachable = false, $urlparams = array()) {

		$view = JFactory::getApplication()->input->getCmd('view', 'Dashboard');
		JFactory::getApplication()->input->set('view', $view);
		parent::display($cachable);

	}

	public function get_options() {

		if (!class_exists('SfAppPlugins')) {
			include_once JPATH_COMPONENT_ADMINISTRATOR . DIRECTORY_SEPARATOR . 'helpers' . DIRECTORY_SEPARATOR . 'app_plugin.php';
			$appsLib = SfAppPlugins::getInstance();
			$appsLib->loadApplications();
		}

		$type = SurveyforceHelper::getQuestionType(JFactory::getApplication()->input->getCmd('sf_qtype'));
		$data['quest_type'] = $type->sf_plg_name;
		$data['quest_id'] = JFactory::getApplication()->input->get('quest_id');
		$data['sf_qtype'] = JFactory::getApplication()->input->getCmd('sf_qtype');

		$appsLib->triggerEvent('onGetAdminQuestionOptions', $data);
	}

	public function install_plugins() {

		jimport('joomla.filesystem.folder');

		ignore_user_abort(false); // STOP script if User press 'STOP' button
		@set_time_limit(0);

		jimport('joomla.filesystem.file');
		jimport('joomla.filesystem.folder');


		$plugins = array();
		$plg_names = array();

		$source = JPATH_SITE . '/components/com_surveyforce/plugins/survey/';
		$source_content = JPATH_SITE . '/components/com_surveyforce/plugins/content/';

		if ( JFolder::exists($source) )
		{
			$plg_files = JFolder::files($source, '\.zip');
			if (!empty($plg_files)) {
				foreach ($plg_files as $plg_name) {
					$plugins[] = $source . $plg_name;
				}
			}
		}

		if ( JFolder::exists($source_content) )
		{
			$plg_files = JFolder::files($source_content, '\.zip');
			if (!empty($plg_files)) {
				foreach ($plg_files as $plg_name) {
					$plugins[] = $source_content . $plg_name;
				}
			}
		}

		jimport('joomla.installer.installer');
		jimport('joomla.installer.helper');

		$app = JFactory::getApplication();
		foreach ($plugins as $ii => $plugin) {
			$package = JInstallerHelper::unpack($plugin);
			$installer = JInstaller::getInstance();

			if (!$installer->install($package['dir'])) {
				// There was an error installing the package
				echo '<pre>';
				print_r($installer);

				echo $installer->message;
			}
			$plg_names[] = end($installer->manifest->name);

			// Cleanup the install files
			if (!is_file($package['packagefile'])) {
				$package['packagefile'] = $app->getCfg('tmp_path') . '/' . $package['packagefile'];
			}

			JInstallerHelper::cleanupInstall('', $package['extractdir']);
		}

		if (count($plg_names)) {
			foreach ($plg_names as $plg_name) {
				$this->_enablePlugin($plg_name);
			}
		}

		//remove temp folder
		if (JFolder::exists($source))		 	{ JFolder::delete($source); }
		if (JFolder::exists($source_content))	{ JFolder::delete($source_content); }

		$app->redirect('index.php?option=com_surveyforce');
	}

	function _enablePlugin($plugin) {
		$db = JFactory::getDBO();

		$query = 'UPDATE ' . $db->quoteName('#__extensions') . ' SET ' . $db->quoteName('enabled') . ' = ' . $db->quote(1)
			. ' WHERE ' . $db->quoteName('name') . ' = ' . $db->quote($plugin);

		$db->setQuery($query);

		if (!$db->execute()) {
			return $db->getErrorNum() . ':' . $db->getErrorMsg();
		} else {
			return null;
		}
	}
}