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
isset($_REQUEST['speeddial'])?$speeddial = $_REQUEST['speeddial']:$speeddial='';
isset($_REQUEST['gensd'])?$gensd = $_REQUEST['gensd']:$gensd='';

isset($_REQUEST['editnumber'])?$editnumber = $_REQUEST['editnumber']:$editnumber='';

$dispnum = "phonebook"; //used for switch on config.php

//if submitting form, update database

if(isset($_REQUEST['action'])) {
	switch ($action) {
		case "add":
			phonebook_add($number, $name, $speeddial, $gensd);
			redirect_standard();
		exit;
		break;
		case "delete":
			$numbers = phonebook_list();
			phonebook_del($number, $numbers[$number]['speeddial']);
			redirect_standard();
		break;
		case "edit":
			$numbers = phonebook_list();
			phonebook_del($editnumber, $numbers[$editnumber]['speeddial']);
			phonebook_add($number, $name, $speeddial, $gensd);
			redirect_standard();
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
						$fields = phonebook_fgetcsvfromline($line, 3);
						$fields = array_map('trim', $fields);
						if (is_array($fields) && count($fields) == 3 && is_numeric($fields[2]) &&  ($fields[3] == '' || is_numeric($fields[3]))) {
							phonebook_del($fields[2], $numbers[$fields[2]]['speeddial']);
							phonebook_add(htmlentities($fields[2],ENT_QUOTES), addslashes(htmlentities($fields[1],ENT_QUOTES)), htmlentities($fields[3],ENT_QUOTES));
							$i++;
						}
					}
					redirect_standard();
				}
			} else
				$n = 0; // total lines if no file
    break;
		case "export":
			header('Content-Type: text/csv');
			header('Content-disposition: attachment; filename=phonebook.csv');
			$numbers = phonebook_list();
			foreach ($numbers as $number => $values)
				printf("\"%s\";%s;%s\n", $values['name'], $number, $values['speeddial']);
			die_freepbx();
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

<form autocomplete="off" name="delete" action="<?php $_SERVER['PHP_SELF'] ?>" method="post" onsubmit="return confirm('<?php echo _("Are you sure you want to empty your phonebook ?")?>');">
<input type="hidden" name="action" value="empty">
<table cellpadding="5" width="100%">

<?php//onsubmit="return edit_onsubmit();"?>

	<tr>
		<td colspan="5"><h5><?php echo _("Phonebook entries") ?></h5><hr></td>
	</tr>

	<tr>
		<td><b><?php _("Number")?></b></td>
		<td><b><?php _("Name")?></b></td>
		<td><b><?php _("Speed dial")?></b></td>
		<td>&nbsp;</td>
		<td>&nbsp;</td>
	</tr>

<?php
// Why should I specify type=tool ???

	foreach ($numbers as $num => $values)	{
		print('<tr>');
		printf('<td>%s</td><td>%s</td><td>%s</td>', $num, $values['name'], $values['speeddial']);
		printf('<td><a href="%s?type=tool&display=%s&number=%s&action=delete" onclick="return confirm(\'%s\')">%s</a></td>', 
			$_SERVER['PHP_SELF'], urlencode($dispnum), urlencode($num), _("Are you sure you want to delete this entry ?"), _("Delete"));
		printf('<td><a href="#"  
    onClick="theForm.number.value = \'%s\'; theForm.name.value = \'%s\' ; theForm.speeddial.value = \'%s\' ; 
    if (theForm.name.value && theForm.number.value && !theForm.speeddial.value) { theForm.gensd.checked = false } else { theForm.gensd.checked = true };
    theForm.editnumber.value = \'%s\' ; theForm.action.value = \'edit\' ; ">%s</a></td>',
			$num,  addslashes($values['name']), $values['speeddial'], $num, _("Edit"));
		print('</tr>');
	}

?>

	<tr>
		<td colspan="3"><br><h6><a href="<?php echo $_SERVER['PHP_SELF'] ?>?type=tool&display=phonebook&action=export&quietmode=1"><?php echo _("Export in CSV") ?></a></h6></td><td colspan="2" align="center"><input name="submit" type="submit" value="<?php echo _("Empty Phonebook")?>"></td>		
	</tr>
</table>
</form>

<?php
}
?>

<form autocomplete="off" name="edit" action="<?php $_SERVER['PHP_SELF'] ?>" method="post" onsubmit="return edit_onsubmit();">
<input type="hidden" name="display" value="<?php echo $dispnum?>">
<input type="hidden" name="action" value="add">
<input type="hidden" name="editnumber" value="">
<table cellpadding="5" width="100%">

	<tr><td colspan="4"><h5><?php echo _("Add or replace entry") ?><hr></h5></td></tr>

	<tr>
		<td><a href="#" class="info"><?php echo _("Name:")?><span><?php echo _("Enter the name")?></span></a></td>
		<td><input type="text" name="name" tabindex="<?php echo ++$tabindex;?>"></td>
	</tr>
	
	<tr>
		<td><a href="#" class="info"><?php echo _("Number:")?>
		<span><?php echo _("Enter the number (For caller ID lookup to work it should match the caller ID received from network)")?></span></a></td>
		<td><input type="text" name="number" tabindex="<?php echo ++$tabindex;?>"></td>
	</tr>

	<tr>
		<td><a href="#" class="info"><?php echo _("Speed dial code:")?><span><?php echo _("Enter a speed dial code<br/>Speeddial module is required to use speeddial codes")?></span></a></td>
		<td><input type="text" name="speeddial" tabindex="<?php echo ++$tabindex;?>"></td>
	</tr>

  <tr>
		<td><a href="#" class="info"><?php echo _("Set Speed Dial?"); ?><span><?php echo _("Check to have a speed dial created automatically for this number"); ?></span></a></td>
		<td><input type="checkbox" name="gensd" value="yes" CHECKED tabindex="<?php echo ++$tabindex;?>"></td>

	<tr>
		<td colspan="2"><br><h6><input name="submit" type="submit" value="<?php echo _("Submit Changes")?>" tabindex="<?php echo ++$tabindex;?>"></h6></td>		

	</tr>
</table>
</form>

<form autocomplete="off" enctype="multipart/form-data" name="import" action="<?php $_SERVER['PHP_SELF'] ?>" method="post">
<input type="hidden" name="MAX_FILE_SIZE" value="30000">
<input type="hidden" name="display" value="<?php echo $dispnum?>">
<input type="hidden" name="action" value="import">

<table cellpadding="5" width="100%">

	<tr><td colspan="4"><h5><?php echo _("Import from CSV") ?><hr></h5></td></tr>

        <tr>
                <td><a href="#" class="info"><?php echo _("File:")?>
                <span><?php echo _("Import a CSV File formatted as follows:<br/>\"Name\";Number;Speeddial<br /> Names should be enclosed by '\"' and fields separated by ';' <br /><br /> Example:<br/>\"John Doe\";12345678;123")?></span></a></td>
                <td><input type="file" name="csv" tabindex="<?php echo ++$tabindex;?>"></td>
        </tr>

	<tr>
		<td colspan="2"><br><h6><input name="submit" type="submit" value="<?php echo _("Upload")?>" tabindex="<?php echo ++$tabindex;?>"></h6></td>		
	</tr>
</table>
</form>
<script language="javascript">
<!--

var theForm = document.edit;
theForm.name.focus();

function edit_onsubmit() {
	var msgInvalidNumber = "<?php echo _("Please enter a valid Number"); ?>";
	var msgInvalidName = "<?php echo _("Please enter a valid Name"); ?>";
	var msgInvalidCode = "<?php echo _("Please enter a valid Speeddial code or leave it empty"); ?>";
	defaultEmptyOK = false;
	if (!isInteger(theForm.number.value))
		return warnInvalid(theForm.number, msgInvalidNumber);
	if (!isAlphanumeric(theForm.name.value))
		return warnInvalid(theForm.name, msgInvalidName);
	
	defaultEmptyOK = true;
	if (!isInteger(theForm.speeddial.value))
		return warnInvalid(theForm.speeddial, msgInvalidCode);
		
	return true;
}


-->
</script>
