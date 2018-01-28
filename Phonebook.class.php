<?php
namespace FreePBX\modules;

class Phonebook implements \BMO {
	public function __construct($freepbx = null) {
		if ($freepbx == null) {
			throw new Exception("Not given a FreePBX Object");
		}
		$this->FreePBX = $freepbx;
		$this->astman = $this->FreePBX->astman;
		$this->db = $freepbx->Database;
	}
	public function install() {
	}
	public function uninstall() {
	}
	public function backup() {
	}
	public function restore($backup) {
	}
	public function doConfigPageInit($page) {

		$action = isset($_REQUEST['action'])? trim($_REQUEST['action']) : '';
		$number = isset($_REQUEST['number'])? trim($_REQUEST['number']) : '';
		$name = isset($_REQUEST['name'])? trim($_REQUEST['name']) : '';
		$speeddial = isset($_REQUEST['speeddial'])? trim($_REQUEST['speeddial']) : '';
		$gensd = isset($_REQUEST['gensd'])? trim($_REQUEST['gensd']) : '';
		$editnumber = isset($_REQUEST['editnumber'])? trim($_REQUEST['editnumber']) : '';
		$editspeeddial = isset($_REQUEST['editspeeddial'])? trim($_REQUEST['editspeeddial']) : '';

		$dispnum = "phonebook"; //used for switch on config.php
		switch ($action) {
		case "add":
			\phonebook_add($number, $name, $speeddial, $gensd);
			break;
		case "delete":
			if(!empty($number)){
				\phonebook_del($number, $speeddial);
			}
			break;
		case "edit":
			$numbers = \phonebook_list();
			\phonebook_del($editnumber, $editspeeddial);
			\phonebook_add($number, $name, $speeddial, $gensd);
			break;
		case "empty":
			\phonebook_empty();
			break;
		case "import":
			$i = 0; // imported lines
			if ($_FILES['csv']['error']) {
				throw new \Exception("Upload error - Error no ".$_FILES['csv']['error']." - File too large? Max 1mb");
			}
			if(is_uploaded_file($_FILES['csv']['tmp_name'])) {
				$lines = file($_FILES['csv']['tmp_name']);
				if (is_array($lines))	{
					$n = count($lines); // total lines
					foreach($lines as $line) {
						$fields = phonebook_fgetcsvfromline($line, 3);
						if (is_array($fields)) {
							$fields = array_map('trim', $fields);
							if (is_array($fields) && count($fields) == 3
								&& is_numeric($fields[2])
								&&  ($fields[3] == '' || is_numeric($fields[3]))
							) {
								phonebook_del($fields[2], $numbers[$fields[2]]['speeddial']);
								// Make sure there's no tabs in the name, this can cause
								// problems
								$name = htmlentities(str_replace("\t", "", $fields[2]),ENT_QUOTES, 'UTF-8');
								phonebook_add($name, addslashes(htmlentities($fields[1],ENT_QUOTES, 'UTF-8')),
									htmlentities($fields[3],ENT_QUOTES, 'UTF-8'));
								$i++;
							}
						}
					}
				}
			} else {
				$n = 0; // total lines if no file
			}
			break;
		case "export":
			header('Content-Type: text/csv');
			header('Content-disposition: attachment; filename=phonebook.csv');
			$numbers = \phonebook_list();
			foreach ($numbers as $number => $values){
				// We can't use fputcsv, as that won't quote numbers, which we need to do
				// so that a number starting with zero isn't accidentally stripped by
				// spreadsheet programs.
				//
				// But we also want to make sure that we don't accidentally double quote ourselves
				// on exporting, which will ALSO confuse spreadsheet programs.
				$csv = '"'.trim(str_replace('"', '', $values['name']).'";"'.trim($number).'";');
				$csv .= (int)$values['speeddial']."\n";

				echo $csv;
			}
			exit;
		}
	}
	public function getActionBar($request) {
		$buttons = array();
		switch($request['display']) {
		case 'modulename':
			$buttons = array(
				'delete' => array(
					'name' => 'delete',
					'id' => 'delete',
					'value' => _('Delete')
				),
				'reset' => array(
					'name' => 'reset',
					'id' => 'reset',
					'value' => _('Reset')
				),
				'submit' => array(
					'name' => 'submit',
					'id' => 'submit',
					'value' => _('Submit')
				)
			);
			if (empty($request['extdisplay'])) {
				unset($buttons['delete']);
			}
			break;
		}
		return $buttons;
	}
	public function ajaxRequest($req, &$setting) {
		switch ($req) {
		case 'getJSON':
			return true;
			break;
		default:
			return false;
			break;
		}
	}
	public function ajaxHandler(){
		switch ($_REQUEST['command']) {
		case 'getJSON':
			switch ($_REQUEST['jdata']) {
			case 'grid':
				$ret = array();
				$numbers = $this->getAll();
				foreach ($numbers as $key => $value) {
					$ret[] = array(
						'number' => $key,
						'name' => isset($value['name'])?$value['name']:$key,
						'dial' => isset($value['speeddial']) ? $value['speeddial'] : ""
					);
				}
				return $ret;
				break;

			default:
				return false;
				break;
			}
			break;

			default:
				return false;
				break;
		}
	}
	public function getAll() {
		$astman = $this->astman;
		if ($astman) {
			$list = $astman->database_show();
			foreach ($list as $k => $v) {
				if (isset($v)) { // Somehow, a 'null' value is leaking into astdb.
					if (substr($k, 1, 7) == 'cidname')
						$numbers['foo'.substr($k, 9)]['name'] = $v ;
					if (substr($k, 1, 13) == 'sysspeeddials')
						$numbers['foo'.$v]['speeddial'] = substr($k, 15) ;
				}
			}
			if (isset($numbers) && is_array($numbers)) {
				foreach ($numbers as $key => $row) {
					$names[$key]  = isset($row['name'])?strtolower($row['name']):'';
				}
				array_multisort($names, SORT_ASC, SORT_STRING, $numbers);
				foreach ($numbers as $key => $value) {
					$retnumbers[substr($key, 3)] = $value;
				}
			}

			return isset($retnumbers)?$retnumbers:array();
		}
		return array();
	}
}
