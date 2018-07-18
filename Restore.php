<?php
namespace FreePBX\modules\Phonebook;
use FreePBX\modules\Backup as Base;
class Restore Extends Base\RestoreBase{
  public function runRestore($jobid){
      $configs = $this->getConfigs();
      $pb = \FreePBX::Phonebook();
      foreach ($configs as $number => $setting) {
        $pb->add($number, $setting['name'], $setting['speeddial']);
      }
  }
}
