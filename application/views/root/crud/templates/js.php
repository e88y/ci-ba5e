<?php 

	$str = 'if (typeof jQuery === "undefined") { 
	throw new Error("This JavaScript requires jQuery"); 
}

/**
 * '.$classname.' object
 */
$.'.$classname.' = {
	/* '.$classname.' options. Modify these options to suit your implementation */	
	options : {
		opt:false
	},
	table : null
};

/**
 * edit '.$classname.' 
 */
$.'.$classname.'.edit = function(id)
{
	$.app.toConsole({"fkt":"$.'.$classname.'.edit", "id":id});

	var params = $("#form_'.$classname.'").serializeArray();
		params.push({"name":"'.$classname.'_id", "value":id});
		params.push({"name":"rendermode", "value":"ajax"});

	var target = baseUrl+"admin/'.$classname.'/edit/"+id;
			
	$.app.sendAjaxRequest(target, params, function success(result)
	{
		if (result.error && result.error != ""){
			$.dialog.error($.lang.item("error"), result.error);
		}
		else{
			$.app.replaceContent(result.data, $.'.$classname.'.init_form, undefined, target);
			$.app.replaceContent(result.extra.breadcrumb, undefined, "breadcrumb");
		}
	}, true, null, $.lang.item("msg_wait") );
}

/**
 * save '.$classname.' 
 */
$.'.$classname.'.save = function(e)
{
	$.app.toConsole({"fkt":"$.'.$classname.'.save"});
	
	var params	= $("#form_'.$classname.'").serializeArray();
		params.push({"name":"rendermode", "value":"json"})
	
	$.app.sendAjaxRequest(e.delegateTarget.action, params, function success(result)
	{
		$.app.toConsole({"fkt":"callback save '.$classname.' ajax", "data":result});
		
		$.app.setFormValidationStates("form_'.$classname.'", result.error, result.extra, null);
		
		if (result.error && result.error != ""){
			$.dialog.error($.lang.item("error"), result.error);
		}
		else{
			$.dialog.success($.lang.item("done"), $.lang.item("'.$classname.'_has_been_saved"), function callback()
			{
				$.app.redirect(baseUrl+"admin/'.$classname.'/");
			});
		}
	}, true, null, $.lang.item("'.$classname.'_save_progress"));
}

/**
 * remove '.$classname.' 
 */
$.'.$classname.'.remove = function(id)
{
	$.app.toConsole({"fkt":"$.'.$classname.'.remove", "id":id});
	if (id == undefined){
		throw new Error($.lang.item("msg_missing_parameter"));
	}
	
	var params = [
  		{"name":"'.$classname.'_id", "value":id},
  		{"name":"confirmed", "value":1},
  		{"name":"rendermode", "value":"JSON"}
  	];
	
	$.dialog.confirm($.lang.item("msg_are_you_sure"), $.lang.item("'.$classname.'_sure_delete"), function callback_yes()
	{
		$.app.sendAjaxRequest(baseUrl+"admin/'.$classname.'/remove/", params, function success(result)
		{
			$.app.toConsole({"fkt":"callback_ajax", "result":result});
			if (result.error && result.error != ""){
				$.dialog.error($.lang.item("error"), result.error);
			}
			else{
				if (result.status == "SUCCESS")
				{
					$.dialog.success($.lang.item("done"), $.lang.item("'.$classname.'_deleted"), function callback_done(){
						$.'.$classname.'.table.ajax.reload(); // reload the table 
					});
				}
			}
		}, true, null, $.lang.item("'.$classname.'_delete_progress"));
	}, null, $.lang.item("'.$classname.'_delete"), $.lang.item("cancel"))
		
}

/**
 * initialize form 
 **/
$.'.$classname.'.init_form = function()
{
	if ($("#form_'.$classname.'").length > 0)
	{
		$.app.toConsole({"fkt":"$.'.$classname.'.init_form"});
		
		$.app.init_checked_list_box();
		$.app.init_toggle();
		
		$("#form_'.$classname.'").submit(function(e) {
	        $.'.$classname.'.save(e);
	        e.preventDefault();
		});
	}
}

/**
 * initialize table
 **/
$.'.$classname.'.init_table = function()
{
	if ($("#tbl_'.$classname.'").length > 0)
	{
		$.app.toConsole({"fkt":"$.'.$classname.'.init_table"});
		
		var selected_rows = [];
		
		$.'.$classname.'.table = $.app.datatable.initialize_ajax("tbl_'.$classname.'", baseUrl+"admin/'.$classname.'/datatable", tbl_columns_'.$classname.', 
			$.app.datatable.callbacks.rowCallback, 
			$.app.datatable.callbacks.initComplete
		);
	}
}

$(document).ready(function()
{
	$.app.toConsole("'.$classname.'.js ready", "log");
	
	$.'.$classname.'.init_table();
	$.'.$classname.'.init_form();
	
});';
echo $str;
?>