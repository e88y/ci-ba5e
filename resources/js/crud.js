if (typeof jQuery === 'undefined') { 
	throw new Error('This JavaScript requires jQuery'); 
}

$.crud = {
	/* options. Modify these options to suit your implementation */	
	table : null
};


$.crud.generate = function()
{
	$.app.toConsole({"fkt":"$.crud.generate"});
	/*
	var settings = [];
	$('#tbl_columns > tbody > tr').each(function() {
		settings.push({
			"column":	$(':nth-child(3)', this).val(),
			"required":	$("input[name*='required']").val(),
			"hidden":	$("input[name*='hidden']").val(),
			"type":		$("select[name*='type']").val()
		});
	});
	*/
	var params		= $('#form_crud').serializeArray();
		//params.push( {"name":"settings", "value":JSON.stringify(settings)});
		params.push( {"name":"rendermode", "value":"JSON"});
		params.push( {"name":"generate", "value":"1"});
	
	$.app.sendAjaxRequest(baseUrl+"root/crud/", params, function success(result)
	{
		$.app.toConsole({"fkt":"callback generate crud", "data":result});
		
		$.app.setFormValidationStates("form_crud", result.error, result.extra, null);
		
		if (result.error && result.error != ""){
			$.dialog.error($.lang.item('error'), result.error);
		}
		else{
			$.dialog.success($.lang.item('crud_has_been_generated'), result.success +"<br>"+ result.info, function callback()
			{
			});
		}
	}, true, null, $.lang.item('crud_generate_progress'));
	
}


/**
 * initialize localization table
 **/
$.crud.init_table = function(selected_table)
{
	if (selected_table != undefined)
	{
		$.app.toConsole({"fkt":"$.crud.init_table"});
		if ($("#tbl_columns").length > 0)
		{
			$('#frm_crud_tbl_view').find('legend').html($.lang.item('wait_for_it'));
			
			var block = $('#frm_crud_tbl_view').parent();
			
			if ($.crud.table != undefined)
			{
				$.app.datatable.reload_ajax($.crud.table, baseUrl+"root/crud/datatable/"+selected_table, 
					function(json){ 
						$('#frm_crud_tbl_view').find('legend').html( $.lang.item('table')+": <b>"+selected_table+"</b> | "+ $.lang.item('columns')+": <b>"+json.recordsTotal+"</b>");
						//$.app.init_chosen();
						//$.app.init_toggle();
					});
			}
			else
			{
				$.app.datatable.options.pageLength 		= 10;
				$.app.datatable.options.paging 			= false;
				$.app.datatable.options.lenghtChange 	= false;
				$.app.datatable.bFancy 					= true;
				
				$.crud.table = $.app.datatable.initialize_ajax("tbl_columns", baseUrl+"root/crud/datatable/"+selected_table+"/", tbl_columns_crud, 
					function(row, data){ }, 
					function(settings, json){
						$('#frm_crud_tbl_view').find('legend').html( $.lang.item('table')+": <b>"+selected_table+"</b> | "+ $.lang.item('columns')+": <b>"+json.recordsTotal+"</b>");	
						$('#tbl_columns_base_wrapper').show();
						//$.app.init_chosen();
						//$.app.init_toggle();
					} 
				);
				
				$('#frm_crud_tbl_view').fadeIn("slow");
			}
		}
	}
}
$.crud.init_form = function()
{
	$.app.toConsole({"fkt":"$.support.init_form"});
	
	$('#bt_analize').addClass("animated hinge").on('webkitAnimationEnd mozAnimationEnd MSAnimationEnd oanimationend animationend', function(){$('#fi_analize').hide()});
	//$('#bt_analize').hide("slow", "hinge", null);
	
	$('#i_table').focus();
	
	$("#i_table").on('change', function() {
		$.crud.init_table($(this).val());
		$('#i_classname').val( $(this).val() )
		
	});
	
	$('#form_crud').submit(function(e) {
		$.crud.generate();
		e.preventDefault();
	});
}


$(document).ready(function()
{
	$.app.toConsole("crud.js ready", "log");

	$.crud.init_table();
	$.crud.init_form();
});