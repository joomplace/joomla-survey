<?php
/**
 * Survey Force Deluxe component for Joomla 3
 * @package Survey Force Deluxe
 * @author JoomPlace Team
 * @Copyright Copyright (C) JoomPlace, www.joomplace.com
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

class DeImportFieldDescriptor {
	var $name			= '';
	var $required		= FALSE;
	var $defaultValue	= NULL;
	
	function __construct($name, $required = FALSE, $defaultValue = NULL) {
		$this->name				= $name;
		$this->required			= $required;
		$this->defaultValue		= $defaultValue;
	}
	
	function getName() {
		return $this->name;
	}
	
	function isRequired() {
		return $this->required;
	}
	
	function getDefaultValue() {
		return $this->defaultValue;
	}
}

class DeImportFieldDescriptors {
	var $fieldDescriptorsByName		= array();
	
	function addRequired($name) {
		$this->fieldDescriptorsByName[$name]	= new DeImportFieldDescriptor($name, TRUE);
	}
	
	function addOptional($name, $defaultValue = NULL) {
		$this->fieldDescriptorsByName[$name]	= new DeImportFieldDescriptor($name, FALSE, $defaultValue);
	}
	
	function get($name) {
		$result	= NULL;
		if (isset($this->fieldDescriptorsByName[$name])) {
			$result	= $this->fieldDescriptorsByName[$name];
		}
		return $result;
	}
	
	function getFieldNames() {
		$a		= array();
		foreach(array_keys($this->fieldDescriptorsByName) as $fieldName) {
			$a[]	= $fieldName;
		}
		return $a;
	}
	
	function getRequiredFieldNames() {
		$a		= array();
		foreach(array_keys($this->fieldDescriptorsByName) as $fieldName) {
			$fieldDescriptor	= $this->fieldDescriptorsByName[$fieldName];
			if ($fieldDescriptor->isRequired()) {
				$a[]	= $fieldName;
			}
		}
		return $a;
	}
	
	function contains($name) {
		return isset($this->fieldDescriptorsByName[$name]);
	}
	
	function isRequired($name) {
		$fieldDescriptor	= $this->get($name);
		return ($fieldDescriptor != NULL ? $fieldDescriptor->isRequired() : FALSE);
	}
	
	function getDefaultValue($name) {
		$fieldDescriptor	= $this->get($name);
		return ($fieldDescriptor != NULL ? $fieldDescriptor->getDefaultValue() : FALSE);
	}
	
}

class DeCsvLoader {
	
	var $fileName;
	var $delimiter		= ',';
	var $loaded			= FALSE;
	var $fieldNames		= array();
	var $rows			= array();
	var $rowIndex		= 0;
	var $errorMessage	= '';
	var $quote			= '"';
	
	function setFileName($fileName) {
		$this->fileName	= $fileName;
	}
	
	function resetError() {
		$this->setErrorMessage('');
	}
	
	function setDelimiter($delimiter) {
		$this->delimiter	= $delimiter;
	}
	
	function getDelimiter() {
		return $this->delimiter;
	}
	
	function setErrorMessage($errorMessage) {
		$this->errorMessage		= $errorMessage;
	}
	
	function getErrorMessage() {
		return $this->errorMessage;
	}
	
	function load() {
		$this->resetError();
		$this->rowIndex		= 0;
		$this->rows			= array();
		$this->fieldNames	= array();
		$this->loaded		= FALSE;
		if ($this->fileName == '') {
			$this->setErrorMessage('file name missing');
			return FALSE;
		}
		$this->rows		= file($this->fileName);
		if ($this->rows === FALSE) {
			$this->rows	= array();
			$this->setErrorMessage('unable to read file');
			return FALSE;
		}
		if (count($this->rows) < 1) {
			$this->setErrorMessage('header missing');
			return FALSE;
		}
		$this->fieldNames	= $this->getNextValues(FALSE);
		if ($this->fieldNames === FALSE) {
			$this->fieldNames	= array();
			return FALSE;
		}
		$this->loaded	= TRUE;
		return TRUE;
	}
	
	function isEof() {
		return ($this->rowIndex >= count($this->rows));
	}
	
	function getNextRow() {
		if ($this->isEof()) {
			$this->setErrorMessage('end of file reached');
			return FALSE;
		}
		return rtrim($this->rows[$this->rowIndex++]);
	}
	
	function clearQuotes($str){ 
		$str = trim($str);
		if ($str{0} == $this->quote && $str{strlen($str)-1} == $this->quote)
			$str = mb_substr($str, 1, strlen($str)-2);
		return $str;
	}
	
	function getNextValues($fieldNameKeys = TRUE) {

        $row	= $this->getNextRow();
		if ($row === FALSE) {
			return FALSE;
		}
		$a	= explode($this->delimiter, $row);
		//$a = array_map("$this->sf_clearCSVQuotes", $a);
        //analogue functionality

        foreach($a as $key => $val){
            $str = trim($val);
            if ($val{0} == '"' && $val{strlen($val)-1} == '"')
                $val = mb_substr($val, 1, strlen($val)-2);
            $a[$key] = $val;
        }
		
		if (($fieldNameKeys) && (count($this->fieldNames) > 0)) {
			$a2		= array();
			foreach($this->fieldNames as $k => $fieldName) {
				if (isset($a[$k])) {
					$a2[$fieldName]		= $a[$k];
				}
			}
			return $a2;
		} else {
			return $a;
		}
	}
	
	function getLastLineNumber() {
		return $this->rowIndex;
	}
	
	function getFieldNames() {
		return $this->fieldNames;
	}
	
	function setFieldNames($fieldNames) {
		$this->fieldNames	= $fieldNames;
	}

} 
