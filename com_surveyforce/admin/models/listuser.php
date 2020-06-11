<?php

/**
 * Survey Force Deluxe component for Joomla 3 3.0
 * @package Survey Force Deluxe
 * @author JoomPlace Team
 * @Copyright Copyright (C) JoomPlace, www.joomplace.com
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.modeladmin');

class SurveyforceModelListuser extends JModelAdmin {

	protected $context = 'com_surveyforce';

	public function getTable($type = 'Listuser', $prefix = 'SurveyforceTable', $config = array()) {
		return JTable::getInstance($type, $prefix, $config);
	}

	public function getForm($data = array(), $loadData = true) {
		$form = $this->loadForm('com_surveyforce.listuser', 'listuser', array('control' => 'jform', 'load_data' => false));
		if (empty($form)) {
			return false;
		}

		$item = $this->getItem();
		$form->bind($item);

		return $form;
	}

	public function getFields(){
		$db = JFactory::getDbo();

		$item = $this->getItem();

		$query = $db -> getQuery(true);

		$query ->select('*')->from('#__survey_force_users')->where('list_id='.$item->id);
		$db ->setQuery($query);

		$fields = $db->loadObjectList();

		return $fields;

	}

	public function save($data) {
		if($data['id']){
			if(parent::save($data)) return true;

		}else{
			include_once JPATH_COMPONENT_ADMINISTRATOR . DIRECTORY_SEPARATOR . 'helpers' . DIRECTORY_SEPARATOR . 'csv.php';
			$database = JFactory::getDbo();
			$user = JFactory::getUser();

			$is_add_reg = isset($data['is_add_reg']) ? $data['is_add_reg'] : 0;
			$is_add_csv = isset($data['is_import_csv']) ? $data['is_import_csv'] : 0;
			$is_add_man = isset($data['is_add_manually']) ? $data['is_add_manually'] : 0;
			$is_add_lms = isset($data['is_add_lms']) ? $data['is_add_lms'] : 0;
			$is_create = false;

			$data['date_created'] = date('Y-m-d H:i:s');
			$data['sf_author_id'] = $user->id;

			if (!$is_add_reg && !$is_add_csv && !$is_add_man && !$is_add_lms)
				return false;

			if ($is_add_csv) {
				// make arrays of valid fields for error checking
				$fieldDescriptors = new DeImportFieldDescriptors();
				$fieldDescriptors->addRequired('lastname');
				$fieldDescriptors->addRequired('name');
				$fieldDescriptors->addRequired('email');

				$userfile = JFactory::getApplication()->input->files->get('jform', null);
				$userfile = $userfile['csv_file'];
				$userfileTempName = $userfile['tmp_name'];
				$userfileName = $userfile['name'];

				$loader = new DeCsvLoader();
				$loader->setFileName($userfileTempName);
				if (!$loader->load()) {
					echo "<script> alert('" . JText::_('COM_SURVEYFORCE_IMPORT_FAILED') . ":" . $loader->getErrorMessage() . "'); window.history.go(-1); </script>\n";
					exit();
				}

				if (!SurveyforceHelper::SF_prepareImport($loader, $fieldDescriptors)) {
					echo "<script> alert('" . JText::_('COM_SURVEYFORCE_IMPORT_FAILED') . "'); window.history.go(-1); </script>\n";
					exit();
				}

				$requiredFieldNames = $fieldDescriptors->getRequiredFieldNames();
				$allFieldNames = $fieldDescriptors->getFieldNames();

				//check validate csv file
				$ii = 0;
				while (!$loader->isEof()) {
					$values = $loader->getNextValues();
					if (!SurveyforceHelper::SF_prepareImportRow($loader, $fieldDescriptors, $values, $requiredFieldNames, $allFieldNames)) {
						echo "<script> alert('" . $ii . JText::_('COM_SURVEYFORCE_ROW_IMPORT_FAILED') . "'); window.history.go(-1); </script>\n";
						exit();
					}

					$ii++;
				}

				if (!$is_create) {

					parent::save($data);
					$list_id = $database->insertid();
					$is_create = true;
				}

				// Prepare the data to be imported but first validate all entries before eventually importing
				// (a bit like a software transaction)
				$rows = array();
				$ii = 0;
				$loader->rowIndex = 1;

				while (!$loader->isEof()) {
					$values = $loader->getNextValues();
					if (!SurveyforceHelper::SF_prepareImportRow($loader, $fieldDescriptors, $values, $requiredFieldNames, $allFieldNames)) {
						echo "<script> alert('" . $ii . JText::_('COM_SURVEYFORCE_ROW_IMPORT_FAILED') . "'); window.history.go(-1); </script>\n";
						exit();
					}
					$row_user = $this->getTable('User');
					if (!$row_user->bind($values)) {
						echo "<script> alert('" . $row_user->getError() . "'); window.history.go(-1); </script>\n";
						exit();
					}
					$row_user->list_id = $list_id;
					if (!$row_user->check()) {
						continue;
					} else {
						if (function_exists('clone')) {
							$rows[] = clone($row_user);
						} elseif (version_compare(PHP_VERSION, '5.3.0') >= 0) {
							$rows[] = clone $row_user;
						} else {

							$rows[] = $row_user;
						}
					}
					$ii++;
				}

				// Finally import the data
				foreach (array_keys($rows) as $k) {
					$row_user = & $rows[$k];
					if (!$row_user->store()) {
						echo "<script> alert('" . $row_user->getError() . "'); window.history.go(-1); </script>\n";
						exit();
					}
				}
			}

			if (!$is_create) {
				parent::save($data);
				$list_id = $database->insertid();
			}

			if ($is_add_reg) {
				$query = "SELECT name, username, email FROM `#__users`"; // WHERE block = 0"; //FIX: add usertype(gid) checking
				$database->SetQuery($query);
				$mos_users = $database->loadObjectList();

				foreach ($mos_users as $mos_user) {
					$row_user = $this->getTable('User');
					$row_user->name = $mos_user->username;
					$row_user->lastname = $mos_user->name;
					$row_user->email = $mos_user->email;
					$row_user->list_id = $list_id;

					if (!$row_user->check()) {
						continue;
					} elseif (!$row_user->store()) {
						echo "<script> alert('" . $row_user->getError() . "'); window.history.go(-1); </script>\n";
						exit();
					}
				}
			}

			if ($is_add_man) {
				$ind = 0;
				$sf_hid_names = JFactory::getApplication()->input->get('sf_hid_names', array(), 'ARRAY');
				$sf_hid_lastnames = JFactory::getApplication()->input->get('sf_hid_lastnames', array(), 'ARRAY');
				$sf_hid_emails = JFactory::getApplication()->input->get('sf_hid_emails', array(), 'ARRAY');

				if (count($sf_hid_names) >= 1)
					foreach ($sf_hid_names as $man_user) {
						$row_user = $this->getTable('User');
						$row_user->name = $man_user;
						$row_user->lastname = $sf_hid_lastnames[$ind];
						$row_user->email = $sf_hid_emails[$ind];
						$row_user->list_id = $list_id;

						if (!$row_user->check()) {
							continue;
						} elseif (!$row_user->store()) {
							echo "<script> alert('" . $row_user->getError() . "'); window.history.go(-1); </script>\n";
							exit();
						}
						$ind++;
					}
			}
			if ($is_add_lms) {
				$lms_groups = JFactory::getApplication()->input->get('lms_groups', array(), 'ARRAY');
				if (count($lms_groups) > 0) {
					$lms_group_str = "'-1',";
					$teacher_in_courses_str2 = '';
					foreach ($lms_groups as $lms_group) {
						if (strpos($lms_group, '_') > 0) {
							$teacher_in_courses_str2 .= mb_substr($lms_group, 2) . ',';
						}
						else
							$lms_group_str .= $lms_group . ',';
					}
					$lms_group_str = mb_substr($lms_group_str, 0, -1);
					$teacher_in_courses_str2 = mb_substr($teacher_in_courses_str2, 0, -1);
					$query = "SELECT user_id FROM `#__lms_users_in_groups` WHERE (group_id IN ({$lms_group_str})) "
						. ($teacher_in_courses_str2 != '' ? " OR (group_id = 0 AND course_id IN ({$teacher_in_courses_str2}))" : '');
					$database->SetQuery($query);

					$lms_users = $database->loadColumn();
					$query = "SELECT `name`, `username`, `email` FROM `#__users` WHERE `id` IN (" . implode(',', $lms_users) . ")";
					$database->SetQuery($query);
					$mos_users = $database->loadObjectList();
					foreach ($mos_users as $mos_user) {

						$row_user = $this->getTable('User');
						$row_user->name = $mos_user->username;
						$row_user->lastname = $mos_user->name;
						$row_user->email = $mos_user->email;
						$row_user->list_id = $list_id;
						if (!$row_user->check()) {
							continue;
						} elseif (!$row_user->store()) {
							echo "<script> alert('" . $row_user->getError() . "'); window.history.go(-1); </script>\n";
							exit();
						}
					}
				}
			}

			$app = JFactory::getApplication();
			if ($database->getErrorMsg())
				$app->enqueueMessage($database->getErrorMsg(), 'error');
			else
				return true;
		}
	}

}
