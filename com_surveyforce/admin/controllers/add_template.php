<?php
/**
 * Survey Force Deluxe component for Joomla 3
 * @package Survey Force Deluxe
 * @author JoomPlace Team
 * @Copyright Copyright (C) JoomPlace, www.joomplace.com
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */

defined('_JEXEC') or die('Restricted access');

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Filesystem\Folder;
use Joomla\CMS\Filesystem\File;
use Joomla\Archive\Archive;

class SurveyforceControllerAdd_template extends JControllerForm
{
    public function cancel($key = null)
    {
        $this->setRedirect('index.php?option=com_surveyforce&view=templates');
    }

    public function add()
    {
        $this->checkToken();
        $app  = Factory::getApplication();
        $file = $app->input->files->get('jform', array(), 'raw');

        if ($file['package_file']['type'] == 'application/zip' || $file['package_file']['type'] == 'application/x-zip-compressed') {

            if ((int) ini_get('memory_limit') < 128) {
                ini_set("memory_limit", "128M");
            }
            if ((int) ini_get('max_execution_time') < 600) {
                ini_set("max_execution_time", "0");
                set_time_limit(0);
            }

            $package_name = File::stripExt($file['package_file']['name']);
            $package_TempPath = Factory::getConfig()->get('tmp_path', JPATH_ROOT.'/tmp');

            if (File::move($file['package_file']['tmp_name'], $package_TempPath.'/'.$package_name.'.zip')) {

                @Folder::create($package_TempPath.'/'.$package_name.'/', 0755);
                $archive = new Archive(array('tmp_path' => $package_TempPath));
                $package = $archive->extract($package_TempPath.'/'.$package_name.'.zip', $package_TempPath.'/'.$package_name.'/');

                if ($package) {
                    File::delete($package_TempPath.'/'.$package_name.'.zip');
                    $folder_component = '';
                    if(!File::exists($package_TempPath.'/'.$package_name.'/template.php')) {
                        $folders = Folder::folders($package_TempPath.'/'.$package_name);
                        foreach($folders as $folder) {
                            if(File::exists($package_TempPath.'/'.$package_name.'/'.$folder.'/template.php')) {
                                $folder_component = $folder;
                                break;
                            }
                        }
                        if($folder_component == '') {
                            $app->enqueueMessage(Text::_('COM_SURVEYFORCE_ERROR_UPLOADING_TEMPLATE'), 'error');
                            Folder::delete($package_TempPath.'/'.$package_name);
                            $this->setRedirect('index.php?option=com_surveyforce&view=templates');
                            return true;
                        }
                    }

                    Folder::move($package_TempPath.'/'.$package_name.'/'.$folder_component, JPATH_COMPONENT_SITE.'/templates/'.$package_name);

                    $plugins_names = Folder::folders(JPATH_SITE.'/plugins/survey');
                    foreach($plugins_names as $plugin_name) {
                        if($plugin_name == 'pagebreak') {
                            continue;
                        }

                        if(Folder::exists($package_TempPath.'/'.$package_name.'/plugins/'.$plugin_name)) {
                            Folder::copy($package_TempPath.'/'.$package_name.'/plugins/'.$plugin_name,
                                JPATH_SITE.'/plugins/survey/'.$plugin_name.'/tmpl/'.$package_name);
                        } else {
                            Folder::copy(JPATH_SITE.'/plugins/survey/'.$plugin_name.'/tmpl/surveyforce_standart',
                                JPATH_SITE.'/plugins/survey/'.$plugin_name.'/tmpl/'.$package_name);
                        }
                    }

                    if(Folder::exists($package_TempPath.'/'.$package_name)) {
                        Folder::delete($package_TempPath.'/'.$package_name);
                    }

                    $db = Factory::getDbo();
                    $query = $db->getQuery(true);
                    $conditions = array(
                        $db->qn('sf_name').'='.$db->q($db->escape($package_name))
                    );
                    $query->delete($db->qn('#__survey_force_templates'))
                        ->where($conditions);
                    $db->setQuery($query)
                        ->execute();

                    $package_name_arr = explode('_', $package_name);
                    $sf_display_names = array();
                    foreach ($package_name_arr as $item) {
                        if($item == mb_strtolower('surveyforce', 'UTF-8')) {
                            continue;
                        }
                        $sf_display_names[] = $item;
                    }
                    $sf_display_names[0] = ucfirst($sf_display_names[0]);
                    $sf_display_names[] = 'template';
                    $sf_display_name = implode(' ', $sf_display_names);

                    $sf_templates = new stdClass();
                    $sf_templates->sf_name = $db->escape($package_name);
                    $sf_templates->sf_display_name = $db->escape($sf_display_name);
                    $db->insertObject('#__survey_force_templates', $sf_templates);

                    $app->enqueueMessage(Text::_('COM_SURVEYFORCE_SUCСESS_UPLOADING_TEMPLATE'));
                }
            }
        }

        $this->setRedirect('index.php?option=com_surveyforce&view=templates');
        return true;
    }
}