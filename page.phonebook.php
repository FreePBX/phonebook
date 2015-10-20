<?php /* $Id */
if (!defined('FREEPBX_IS_AUTH')) { die('No direct script access allowed'); }
//	License for all code of this FreePBX module can be found in the license file inside the module directory
//	Copyright 2013 Schmooze Com Inc.
//  Copyright (C) 2006 WeBRainstorm S.r.l. (ask@webrainstorm.it)
//
$dataurl = "ajax.php?module=phonebook&command=getJSON&jdata=grid";
?>

<div class="container-fluid">
	<h1><?php echo _('Phonebook')?></h1>
	<div class="alert alert-info">
		<?php echo _('Use this module to create system wide speed dial numbers that can be dialed from any phone.')?>
	</div>
	<div class = "">
		<div class="row">
			<div class="col-sm-12">
				<div class="fpbx-container">
					<div class="display no-border">
						<div id="toolbar-all">
							<button type="button" class="btn btn-default" data-toggle="modal" data-target="#pbForm" data-action="add"><i class="fa fa-plus"></i> <?php echo _("Add Phonebook Entry")?></button>
							<a class="btn btn-default" href="?display=phonebook&amp;action=empty"><i class="fa fa-exclamation-triangle"></i> <?php echo _("Empty Phonebook")?></a>
							<a class="btn btn-default" href="?display=phonebook&amp;action=export"><i class="fa fa-upload"></i> <?php echo _("Export Phonebook")?></a>
							<button type="button" class="btn btn-default" data-toggle="modal" data-target="#importForm" data-action="add"><i class="fa fa-download"></i> <?php echo _("Import Phonebook")?></button>
						</div>
						 <table id="mygrid" data-url="<?php echo $dataurl?>" data-cache="false" data-toolbar="#toolbar-all" data-maintain-selected="true" data-show-columns="true" data-show-toggle="true" data-toggle="table" data-pagination="true" data-search="true" class="table table-striped">
						    <thead>
						            <tr>
						            <th data-field="number"><?php echo _("Number")?></th>
						            <th data-field="name"><?php echo _("Name")?></th>
						            <th data-field="dial"><?php echo _("Speeddial")?></th>
						            <th data-field="number" data-formatter="linkFormatter"><?php echo _("Actions")?></th>
						        </tr>
						    </thead>
						</table>
						<!-- Add/Edit Modal -->
						<div id="pbForm" class="modal fade" role="dialog">
						  <div class="modal-dialog">
						    <div class="modal-content">
						      <div class="modal-header">
						        <button type="button" class="close" data-dismiss="modal">&times;</button>
						        <h4 class="modal-title"><?php echo _("Add or replace entry")?></h4>
						      </div>
						      <div class="modal-body">
										<form autocomplete="off" name="edit" id="edit" action="" method="post" onsubmit="return edit_onsubmit();">
										<input type="hidden" name="display" value="phonebook">
										<input type="hidden" name="action" value="add">
										<input type="hidden" name="editnumber" id="editnumber" value="">
										<!--Name-->
										<div class="element-container">
											<div class="row">
												<div class="col-md-12">
													<div class="row">
														<div class="form-group">
															<div class="col-md-3">
																<label class="control-label" for="name"><?php echo _("Name") ?></label>
																<i class="fa fa-question-circle fpbx-help-icon" data-for="name"></i>
															</div>
															<div class="col-md-9">
																<input type="text" class="form-control" id="name" name="name" value="<?php echo isset($name)?$name:''?>" required>
															</div>
														</div>
													</div>
												</div>
											</div>
											<div class="row">
												<div class="col-md-12">
													<span id="name-help" class="help-block fpbx-help-block"><?php echo _("Enter the name")?></span>
												</div>
											</div>
										</div>
										<!--END Name-->
										<!--Number-->
										<div class="element-container">
											<div class="row">
												<div class="col-md-12">
													<div class="row">
														<div class="form-group">
															<div class="col-md-3">
																<label class="control-label" for="number"><?php echo _("Number") ?></label>
																<i class="fa fa-question-circle fpbx-help-icon" data-for="number"></i>
															</div>
															<div class="col-md-9">
																<input type="tel" class="form-control" id="number" name="number" value="<?php echo isset($number)?$number:''?>">
															</div>
														</div>
													</div>
												</div>
											</div>
											<div class="row">
												<div class="col-md-12">
													<span id="number-help" class="help-block fpbx-help-block"><?php echo _("Enter the number (For CallerID lookup to work it should match the CallerID received from network)")?></span>
												</div>
											</div>
										</div>
										<!--END Number-->
										<!--Speed Dial Code-->
										<div class="element-container">
											<div class="row">
												<div class="col-md-12">
													<div class="row">
														<div class="form-group">
															<div class="col-md-3">
																<label class="control-label" for="speeddial"><?php echo _("Speed Dial Code") ?></label>
																<i class="fa fa-question-circle fpbx-help-icon" data-for="speeddial"></i>
															</div>
															<div class="col-md-9">
																<input type="text" class="form-control" id="speeddial" name="speeddial" value="<?php echo isset($speeddial)?$speeddial:''?>">
															</div>
														</div>
													</div>
												</div>
											</div>
											<div class="row">
												<div class="col-md-12">
													<span id="speeddial-help" class="help-block fpbx-help-block"><?php echo _("Enter a speed dial code<br/>Speeddial module is required to use speeddial codes")?></span>
												</div>
											</div>
										</div>
										<!--END Speed Dial Code-->
										<!--Set Speed Dial-->
										<div class="element-container">
											<div class="row">
												<div class="col-md-12">
													<div class="row">
														<div class="form-group">
															<div class="col-md-3">
																<label class="control-label" for="gensd"><?php echo _("Set Speed Dial") ?></label>
																<i class="fa fa-question-circle fpbx-help-icon" data-for="gensd"></i>
															</div>
															<div class="col-md-9 radioset">
										            <input type="radio" name="gensd" id="gensdyes" value="yes" >
										            <label for="gensdyes"><?php echo _("Yes");?></label>
										            <input type="radio" name="gensd" id="gensdno" CHECKED>
										            <label for="gensdno"><?php echo _("No");?></label>
															</div>
														</div>
													</div>
												</div>
											</div>
											<div class="row">
												<div class="col-md-12">
													<span id="gensd-help" class="help-block fpbx-help-block"><?php echo _("Select Yes to have a speed dial created automatically for this number")?></span>
												</div>
											</div>
										</div>
										<!--END Set Speed Dial-->
										<input name="addsubmit" type="submit" value="<?php echo _("Submit Changes")?>">
										</form>
						      </div>
						    </div>
						  </div>
						</div>
						<!--END MODAL-->
						<!--import MODAL-->
						<div id="importForm" class="modal fade" role="dialog">
							<div class="modal-dialog">
								<div class="modal-content">
									<div class="modal-header">
										<button type="button" class="close" data-dismiss="modal">&times;</button>
										<h4 class="modal-title"><?php echo _("Import from CSV")?></h4>
									</div>
									<div class="modal-body">
										<form autocomplete="off" enctype="multipart/form-data" name="import" action="" method="post">
										<input type="hidden" name="MAX_FILE_SIZE" value="30000">
										<input type="hidden" name="display" value="phonebook">
										<input type="hidden" name="action" value="import">
										<!--File-->
										<div class="element-container">
											<div class="row">
												<div class="col-md-12">
													<div class="row">
														<div class="form-group">
															<div class="col-md-3">
																<label class="control-label" for="csv"><?php echo _("File") ?></label>
																<i class="fa fa-question-circle fpbx-help-icon" data-for="csv"></i>
															</div>
															<div class="col-md-9">
																<span class="btn btn-default btn-file">
    															<?php echo _("Browse")?> <input type="file" class="form-control" name="csv" id="csv">
																</span>
																<span class="filename"></span>
															</div>
														</div>
													</div>
												</div>
											</div>
											<div class="row">
												<div class="col-md-12">
													<span id="csv-help" class="help-block fpbx-help-block"><?php echo _("Import a CSV File formatted as follows:<br/>\"Name\";Number;Speeddial<br /> Names should be enclosed by '\"' and fields separated by ';' <br /><br /> Example:<br/>\"John Doe\";12345678;123")?></span>
												</div>
											</div>
										</div>
										<!--END File-->
										<br/>
										<input name="submit" type="submit" value="<?php echo _("Upload")?>" >
										</form>
									</div>
								</div>
							</div>
						</div>
						<!--END MODAL-->
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<script language="javascript">
<!--


function edit_onsubmit() {
	var msgInvalidNumber = "<?php echo _("Please enter a valid Number"); ?>";
	var msgInvalidName = "<?php echo _("Please enter a valid Name"); ?>";
	var msgInvalidCode = "<?php echo _("Please enter a valid Speeddial code or leave it empty when generatingß"); ?>";
	if($("#name").val().length == 0){
		warnInvalid($("#name"), msgInvalidName);
		return false;
	}
	if($("#number").val().length == 0){
		warnInvalid($("#number"),msgInvalidNumber);
		return false;
	}else{
		if(!isInteger($("#number").val())){
			warnInvalid($("#number"),msgInvalidNumber);
			return false;
		}
	}
	if($('gensdno:checked')){
		if($("#speeddial").val().length > 0){
			if(!isInteger($("#speeddial").val())){
				warnInvalid($("#speeddial"),msgInvalidCode);
				return false;
			}
		}
	}
	return true;
}


function linkFormatter(value, row, index){
    var html = '<a class="pbedit" href="#" data-toggle="modal" data-target="#pbForm" data-action="add" data-number="'+row['number']+'" data-name="'+row['name']+'" data-dial="'+row['dial']+'"><i class="fa fa-pencil"></i></a>';
    html += '&nbsp;<a href="?display=phonebook&action=delete&number='+value+'&speeddial='+row["dial"]+'" class="delAction"><i class="fa fa-trash"></i></a>';
    return html;
}
$(document).ready(function(){
	$(document).on('click','.pbedit',function(){
		if ($(this).data('action') == 'add') {
			$("#editnumber").val($(this).data('number'));
			$("#number").val($(this).data('number'));
			$("#name").val($(this).data('name'));
			$("#speeddial").val($(this).data('dial'));
		}
	});
	$('#pbForm').on('hidden.bs.modal', function () {
		$("#editnumber").val('');
		$("#number").val('');
		$("#name").val('');
		$("#speeddial").val('');
	});
});
-->
</script>
