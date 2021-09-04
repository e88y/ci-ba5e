if (typeof jQuery === 'undefined') { 
	throw new Error('This JavaScript requires jQuery'); 
}

$.users = {
	/* user options. Modify these options to suit your implementation */	
	options : {
		opt:false
	},
	table : null
};

$.users.edit = function(id)
{
	$.app.toConsole({"fkt":"$.users.edit", "id":id});
	
	var params = [
  		{"name":"user_id", "value":id},
  		{"name":"rendermode", "value":"ajax"}
  	];
	
	var target = baseUrl+'admin/users/edit/'+id;
	$.app.sendAjaxRequest(target, params, function success(result)
	{
		if (result.error != ""){
			$.dialog.error($.lang.item('error'), result.error);
		}
		else{
			$.app.replaceContent(result.data, $.users.init_form, undefined, target);
		}
	}, true, null, $.lang.item('msg_wait') );
}

/**
 * save user 
 */
$.users.save = function(e)
{
	$.app.toConsole({"fkt":"$.users.save"});
	
	var target 	= $('#form_user').attr( 'action' );
	var form 	= $('#form_user').get(0);
	
	var params 	= new FormData(form)
		params.append('rendermode','json');
	
	$.app.sendAjaxRequest(target, params, function success(result)
	{
		$.app.toConsole({"fkt":"callback save user ajax", "data":result});
		$.app.setFormValidationStates("form_user", result.error, result.extra, null);
		
		if (result.error && result.error != null){
			$.dialog.error($.lang.item('error'), result.error);
		}
		else{
			$.dialog.success($.lang.item('done'), $.lang.item('user_has_been_saved'), function callback(){
				$.app.redirect(baseUrl+"admin/users/");
			});
		}
	}, true, null, $.lang.item('user_save_progress'), false, false, false);
}

/**
 * remove user 
 */
$.users.remove = function(id)
{
	$.app.toConsole({"fkt":"$.users.remove", "id":id});
	if (id == undefined){
		throw new Error($.lang.item('msg_missing_parameter')); 
	}
	
	var params = [
  		{"name":"user_id", "value":id},
  		{"name":"confirmed", "value":1},
  		{"name":"rendermode", "value":"JSON"}
  	];

	$.dialog.confirm($.lang.item('msg_are_you_sure'), $.lang.item('user_sure_delete'), function callback_yes()
	{
		$.app.sendAjaxRequest(baseUrl+"admin/users/remove/", params, function success(result)
		{
			$.app.toConsole({"fkt":"callback_ajax", "result":result});
			if (result.error && result.error != ""){
				$.dialog.error($.lang.item('error'), result.error);
			}
			else{
				if (result.status == "SUCCESS")
				{
					$.dialog.success($.lang.item('done'), $.lang.item('user_deleted'), function callback_done(){
						$.users.table.ajax.reload(); // reload table 
					});
				}
			}
		}, true, null, $.lang.item('user_delete_progress'));
		
	}, null, $.lang.item('user_delete'), $.lang.item('cancel'))
}

/**
 * initialize table
 **/
$.users.init_table = function()
{
	if ($("#tbl_users").length > 0)
	{
		var selected_rows = [];
		var dt_fkt = function ( data ) { data.custom_field = 'tada';}
		$.app.toConsole({"fkt":"$.users.init_table", "tbl_columns_user":tbl_columns_user});
		
		$.users.table = $.app.datatable.initialize_ajax("tbl_users", baseUrl+"admin/users/datatable", tbl_columns_user, 
			$.app.datatable.callbacks.rowCallback, 
			$.app.datatable.callbacks.initComplete, 
			dt_fkt 
		);
	}
}

/**
 * initialize user form 
 **/
$.users.init_form = function()
{
	$.app.toConsole({"fkt":"$.users.init_form"});
	
	$.app.init_checked_list_box();
	$.app.init_toggle();
	$.app.init_select2();
	
	$("#i_password").val("");
	$("#i_password_repeat").val("");
	
	if ($("#input_upload").length > 0)
	{
		var target = baseUrl+"root/users/upload_avatar_ajax/";
		var show_buttons = true;
		
		if ($("#i_user_id").val() != "")
		{
			$.app.init_fileinput("#input_upload", target,  ["jpg", "jpeg", "gif", "png"], false, null, null, 1, 1, 10000, {startUpload: 1, user_id:$("#i_user_id").val()}, $("#img_avatar").get(0).outerHTML, true, true, show_buttons, show_buttons, show_buttons, false, false);
			$("#input_upload").on('filebatchuploadsuccess', function(event, files, extra) 
			{
				var response = files.response;
				$('#img_avatar').attr("src", response.img_src);
				
				$("#input_upload").fileinput('refresh', {initialPreview: [ '<img src="'+response.img_src+'" class="file-preview-image">'], showUpload:false});
		    });
		}
	}
	
	$('#form_user').submit(function(e) {
		e.preventDefault();
        $.users.save(e);
	});
	
}

$(document).ready(function()
{
	$.app.toConsole("users.js ready", "log");
	$.users.init_table();
	$.users.init_form();
});