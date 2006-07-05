<?php /* $Id */
//Copyright (C) 2006 WeBRainstorm S.r.l. (ask@webrainstorm.it)
//
//This program is free software; you can redistribute it and/or
//modify it under the terms of the GNU General Public License
//as published by the Free Software Foundation; either version 2
//of the License, or (at your option) any later version.
//
//This program is distributed in the hope that it will be useful,
//but WITHOUT ANY WARRANTY; without even the implied warranty of
//MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
//GNU General Public License for more details.

isset($_REQUEST['action'])?$action = $_REQUEST['action']:$action='';
isset($_REQUEST['number'])?$number = $_REQUEST['number']:$number='';
isset($_REQUEST['name'])?$name = $_REQUEST['name']:$name='';

isset($_REQUEST['editnumber'])?$editnumber = $_REQUEST['editnumber']:$editnumber='';

$dispnum = "phonebook"; //used for switch on config.php

//if submitting form, update database

if(isset($_REQUEST['action'])) {
	switch ($action) {
		case "add":
			phonebook_add($number, $name);
		break;
		case "delete":
			phonebook_del($number);
		break;
    case "edit":
			phonebook_del($editnumber);
			phonebook_add($number, $name);
    break;
		case "empty":
			phonebook_empty();
		break;
		case "import":
			$i = 0; // imported lines
			if(is_uploaded_file($_FILES['csv']['tmp_name'])) {
				$lines = file($_FILES['csv']['tmp_name']);
				if (is_array($lines))	{
					$n = count($lines); // total lines
					foreach($lines as $line) {
						$fields = fgetcsvfromline($line, 2);
						if (is_array($fields) && count($fields) == 2 && is_numeric(trim($fields[2]))) {
							phonebook_add(trim($fields[2]), addslashes(trim($fields[1])));
							$i++;
						}
					}
				}
			} else
				$n = 0; // total lines if no file
    break;
	}
}

$numbers = phonebook_list();

?>

</div>

<!-- NO rnav in this module -->


<div class="content">
<?php
if ($action == 'delete') 
	echo '<h3>'._("Phonebook entry").' '.$itemid.' '._("deleted").' !</h3>';
elseif ($action == 'import')
	echo '<h3>'._("Imported").' '.$i.' '._("lines of").' '.$n.' '.'!</h3>';
elseif ($action == 'empty')
	echo '<h3>'._("Phonebook emptied").' !</h3>';
	
if (is_array($numbers)) {

?>

<table cellpadding="5" width="300">

<form autocomplete="off" name="delete" action="<?php $_SERVER['PHP_SELF'] ?>" method="post" onsubmit="return confirm('<? echo _("Are you sure you want to empty your phonebook ?")?>');">
<?#onsubmit="return edit_onsubmit();"?>
	<input type="hidden" name="action" value="empty">

	<tr>
		<td colspan="4"><h5><?php echo _("Phonebook entries") ?><hr></h5></td>
	</tr>

	<tr>
		<td><b><?=_("Number")?></b></td>
                <td><b><?=_("Name")?></b></td>
		<td>&nbsp;</td>
		<td>&nbsp;</td>
	</tr>

<?php
// Why should I specify type=tool ???

	foreach ($numbers as $num => $name)	{
		print('<tr>');
		printf('<td>%s</td><td>%s</td>', $num, $name);
		printf('<td><a href="%s?type=tool&display=%s&number=%s&action=delete" onclick="return confirm(\'%s\')">%s</a></td>', 
			$_SERVER['PHP_SELF'], urlencode($dispnum), urlencode($num), _("Are you sure you want to delete this entry ?"), _("Delete"));
		printf('<td><a href="#" onClick="theForm.number.value = \'%s\'; theForm.name.value = \'%s\' ; theForm.editnumber.value = \'%s\' ; theForm.action.value = \'edit\' ; ">%s</a></td>',
			$num,  addslashes($name), $num, _("Edit"));
		print('</tr>');
	}

?>

	<tr>
		<td colspan="5"><br><h6><input name="submit" type="submit" value="<?php echo _("Empty Phonebook")?>"></h6></td>		
	</tr>
</form>

</table>

<?
}
?>

<table cellpadding="5" width="300">
<form autocomplete="off" name="edit" action="<?php $_SERVER['PHP_SELF'] ?>" method="post" onsubmit="return edit_onsubmit();">
	<input type="hidden" name="display" value="<?php echo $dispnum?>">
	<input type="hidden" name="action" value="add">
	<input type="hidden" name="editnumber" value="">


	<tr><td colspan="4"><h5><?php echo _("Add or replace entry") ?><hr></h5></td></tr>

        <tr>
                <td><a href="#" class="info"><?php echo _("Number:")?>
                <span><?php echo _("Enter the number (For caller ID lookup to work it should match the caller ID received from network)")?></span></a></td>
                <td><input type="text" name="number"></td>
        </tr>

	<tr>
		<td><a href="#" class="info"><?php echo _("Name:")?><span><?php echo _("Enter the name")?></span></a></td>
		<td><input type="text" name="name"></td>
	</tr>

	<tr>
		<td colspan="2"><br><h6><input name="submit" type="submit" value="<?php echo _("Submit Changes")?>"></h6></td>		
	</tr>
</form>
</table>

<table cellpadding="5" width="300">
<form autocomplete="off" enctype="multipart/form-data" name="import" action="<?php $_SERVER['PHP_SELF'] ?>" method="post">
	<input type="hidden" name="MAX_FILE_SIZE" value="30000">
	<input type="hidden" name="display" value="<?php echo $dispnum?>">
	<input type="hidden" name="action" value="import">


	<tr><td colspan="4"><h5><?php echo _("Import From CSV") ?><hr></h5></td></tr>

        <tr>
                <td><a href="#" class="info"><?php echo _("File:")?>
                <span><?php echo _("Import a CSV File, the first column should contain the telephone number and the second should contain the name")?></span></a></td>
                <td><input type="file" name="csv"></td>
        </tr>

	<tr>
		<td colspan="2"><br><h6><input name="submit" type="submit" value="<?php echo _("Upload")?>"></h6></td>		
	</tr>
</form>
</table>
<script language="javascript">
<!--

var theForm = document.edit;
theForm.name.focus();

function edit_onsubmit() {
	defaultEmptyOK = false;
	if (!isAlphanumeric(theForm.name.value))
		return warnInvalid(theForm.name, "Please enter a valid Name");
        if (!isInteger(theForm.number.value))
                return warnInvalid(theForm.number, "Please enter a valid Number");
	
		
	return true;
}


-->
</script>