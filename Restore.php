<?php
namespace FreePBX\modules\Phonebook;
use FreePBX\modules\Backup as Base;
class Restore Extends Base\RestoreBase{
	public function runRestore($jobid){
		$configs = $this->getConfigs();
		$pb = $this->FreePBX->Phonebook;
		foreach ($configs as $number => $setting) {
			$pb->add($number, $setting['name'], $setting['speeddial']);
		}
	}
	public function processLegacy($pdo, $data, $tables, $unknownTables, $tmpfiledir){
		$pb = $this->FreePBX->Phonebook;
		$astdb = $data['astdb'];
		if(!isset($astdb['sysspeeddials'])){
			return $this;
		}
		foreach($astdb['sysspeeddials'] as $number => $setting){
				$pb->add($number, $setting['name'], $setting['speeddial']);
		}
	}
}
