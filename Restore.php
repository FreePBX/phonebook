<?php
namespace FreePBX\modules\Phonebook;
use FreePBX\modules\Backup as Base;
class Restore Extends Base\RestoreBase{
  public function runRestore($jobid){
      $configs = $this->getConfigs();
      foreach ($configs as $number => $setting) {
        FreePBX::Phonebook()->add($number, $setting['name'], $setting['speeddial']);
      }
  }
}