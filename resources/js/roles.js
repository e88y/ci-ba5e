if (typeof jQuery === 'undefined') { 
	throw new Error('This JavaScript requires jQuery'); 
}

$.roles = {
	/* user options. Modify these options to suit your implementation */	
	options : {
		opt:false
	},
	table : null
};

$.roles.edit = function(id)
{
	$.app.toConsole({"fkt":"$.roles.edit", "id":id});

	var params = [
  		{"name":"role_id", "value":id},
  		{"name":"rendermode", "value":"ajax"}
  	];

	var target = baseUrl+'admin/roles/edit/'+id;
	$.app.sendAjaxRequest(target, params, function success(result)
	{
		$.app.update_browser_url(target);
		
		if (result.error && result.error != ""){
			$.dialog.error($.lang.item('error'), result.error);
		}
		else{
			$.app.replaceContent(result.data, $.roles.init_form, undefined, target);
			$.app.replaceContent(result.extra.breadcrumb, undefined, "breadcrumb");
		}
	}, true, null, $.lang.item('msg_wait') );
}

$.roles.save = function(e)
{
	$.app.toConsole({"fkt":"$.roles.save"});
	
	var params	= $('#form_role').serializeArray();
		params.push({"name":"rendermode", "value":"json"})
	
	$.app.sendAjaxRequest(e.delegateTarget.action, params, function success(result)
	{
		$.app.toConsole({"fkt":"callback save role ajax", "data":result});
		
		$.app.setFormValidationStates("form_role", result.error, result.extra, null);
		
		if (result.error && result.error != ""){
			$.dialog.error($.lang.item('error'), result.error);
		}
		else{
			$.dialog.success($.lang.item('done'), $.lang.item('role_has_been_saved'), function callback()
			{
				$.app.redirect(baseUrl+"admin/roles/");
			});
		}
	}, true, null, $.lang.item('role_save_progress'));
}

$.roles.remove = function(id)
{
	$.app.toConsole({"fkt":"$.roles.remove", "id":id});
	if (id == undefined){
		throw new Error($.lang.item('msg_missing_parameter'));
	}
	
	var params = [
  		{"name":"role_id", "value":id},
  		{"name":"confirmed", "value":1},
  		{"name":"rendermode", "value":"JSON"}
  	];
	
	$.dialog.confirm($.lang.item('msg_are_you_sure'), $.lang.item('role_sure_delete'), function callback_yes()
	{
		$.app.sendAjaxRequest(baseUrl+"admin/roles/remove/", params, function success(result)
		{
			$.app.toConsole({"fkt":"callback_ajax", "result":result});
			if (result.error && result.error != ""){
				$.dialog.error($.lang.item('error'), result.error);
			}
			else{
				if (result.status == "SUCCESS")
				{
					$.dialog.success($.lang.item('done'), $.lang.item('role_deleted'), function callback_done(){
						$.roles.table.ajax.reload(); // reload the table 
					});
				}
			}
		}, true, null, $.lang.item('role_delete_progress'));
	}, null, $.lang.item('role_delete'), $.lang.item('cancel'))
		
}

/**
 * initialize form 
 **/
$.roles.init_form = function()
{
	if ($('#form_role').length > 0)
	{
		$.app.toConsole({"fkt":"$.roles.init_form"});
		
		$.app.init_checked_list_box();
		$.app.init_toggle();
		
		$('#form_role').submit(function(e) {
	        $.roles.save(e);
	        e.preventDefault();
		});
		
		if ($('#toggle_all_rights').length > 0 && $(".cb_right").length > 0)
		{
			$('#toggle_all_rights').change( function(event){
				$(".cb_right").prop('checked', $(this).prop('checked'));
				$(".cb_right").each(function(){$(this).triggerHandler('change')});
			});
		}
	}
}

/**
 * initialize table
 **/
$.roles.init_table = function()
{
	if ($("#tbl_roles").length > 0)
	{
		$.app.toConsole({"fkt":"$.roles.init_table"});
		
		var selected_rows = [];
		
		$.roles.table = $.app.datatable.initialize_ajax("tbl_roles", baseUrl+"admin/roles/datatable", tbl_columns_roles, 
			$.app.datatable.callbacks.rowCallback, 
			$.app.datatable.callbacks.initComplete
		);
	}
}

$(document).ready(function()
{
	$.app.toConsole("roles.js ready", "log");
	
	$.roles.init_table();
	$.roles.init_form();
	
});

