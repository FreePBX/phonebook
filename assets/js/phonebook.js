function edit_onsubmit() {
	var msgInvalidNumber = _("Please enter a valid Number");
	var msgInvalidCode = _("Please enter a valid Speeddial code or leave it empty when Set Speed Dial=No ");
	if($("#name").val().length == 0){
		warnInvalid($("#name"), _("Please enter a valid Name"));
		return false;
	}
	if($("#number").val().length == 0){
		warnInvalid($("#number"),msgInvalidNumber);
		return false;
	}else{
		if(!isDialpattern($("#number").val())){
			warnInvalid($("#number"),msgInvalidNumber);
			return false;
		}
	}
	if($("input[name='gensd']:checked").val()=="on"){
		if($("#speeddial").val().length > 0){
			if(!isDialpattern($("#speeddial").val())){
				warnInvalid($("#speeddial"),msgInvalidCode);
				return false;
			}
		} else {
			warnInvalid($("#speeddial"),msgInvalidCode);
			return false;
		}
	}
	if($("#formaction").val() === 'add' && numbers.indexOf(parseInt($("#number").val())) >= 0) {
		warnInvalid($("#number"),_("Duplicate speeddial number"));
		return false;
	}
	return true;
}

var numbers = [];
function linkFormatter(value, row, index){
	numbers.push(row.number);
	var html = '<a class="pbedit" href="#" data-toggle="modal" data-target="#pbForm" data-action="edit" data-number="'+row.number+'" data-name="'+row.name+'" data-dial="'+row.dial+'"><i class="fa fa-pencil"></i></a>';
	html += '&nbsp;<a href="?display=phonebook&action=delete&number='+value+'&speeddial='+row.dial+'" class="delAction"><i class="fa fa-trash"></i></a>';
	return html;
}
$(document).ready(function(){
	$(document).on('click','.pbedit',function(){
		if ($(this).data('action') == 'edit') {
			$("#formaction").val("edit");
			$("#editnumber").val($(this).data('number'));
			$("#number").val($(this).data('number'));
			$("#name").val($(this).data('name'));
			$("#speeddial").val($(this).data('dial'));
			$("#editspeeddial").val($(this).data('dial'));
		}
	});
	$('#pbForm').on('hidden.bs.modal', function () {
		$("#formaction").val('add');
		$("#editnumber").val('');
		$("#number").val('');
		$("#name").val('');
		$("#speeddial").val('');
		$("#editspeeddial").val('');
	});
});
