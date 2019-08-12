<?php
namespace FreePBX\modules\Phonebook;
use FreePBX\modules\Backup as Base;
class Restore Extends Base\RestoreBase{
	public function runRestore($jobid){
		$configs = $this->getConfigs();
		phonebook_empty();
		$pb = $this->FreePBX->Phonebook;
		foreach ($configs as $number => $setting) {
			$pb->add($number, $setting['name'], $setting['speeddial']);
		}
	}
	public function processLegacy($pdo, $data, $tables, $unknownTables){
		phonebook_empty();
		$pb = $this->FreePBX->Phonebook;
		$astdb = $data['astdb'];
		if(!isset($astdb['sysspeeddials'])){
			return $this;
		}
		foreach($astdb['sysspeeddials'] as $speeddial => $number) {
			$pb->add($number, $astdb['cidname'][$number], $speeddial);
		}
	}
}
