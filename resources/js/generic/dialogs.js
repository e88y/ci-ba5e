
// ..:::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::..
if (typeof jQuery === 'undefined') { 
	throw new Error('This JavaScript requires jQuery'); 
}

$(document).ready(function()
{
	$.app.toConsole("dialogs.js ready", "log");
});

/** 
 * the dialog super-object
 * @type Object
 * */
$.dialog = {
	options: {
		ajaxTargetGeneric: baseUrl+"modal/generic"
	},	
	error: function(title, content, callback, callback_no, txt_yes, txt_no){
		txt_yes = (txt_yes == undefined ? $.lang.item('ok'):txt_yes);
		return this.show(title, content, callback, callback_no, "danger", txt_yes, txt_no);
	},
	info: function(title, content, callback, callback_no, txt_yes, txt_no){
		txt_yes = (txt_yes == undefined ? $.lang.item('ok'):txt_yes);
		return this.show(title, content, callback, callback_no, "info", txt_yes, txt_no);
	},
	success: function(title, content, callback, callback_no, txt_yes, txt_no){
		txt_yes = (txt_yes == undefined ? $.lang.item('ok'):txt_yes);
		return this.show(title, content, callback, callback_no, "success", txt_yes, txt_no);
	},
	warning: function(title, content, callback, callback_no, txt_yes, txt_no){
		txt_yes = (txt_yes == undefined ? $.lang.item('ok'):txt_yes);
		return this.show(title, content, callback, callback_no, "warning", txt_yes, txt_no);
	},
	standard: function(title, content, callback){
		return this.show(title, content, callback, callback_no, "default", txt_yes, undefined);
	},
	confirm: function(title, content, callback, callback_no, txt_yes, txt_no)
	{
		txt_yes = (txt_yes == undefined ? $.lang.item('yes'):txt_yes);
		txt_no 	= (txt_no == undefined ? $.lang.item('no'):txt_no);
		
		return this.show(title, content, callback, callback_no, "confirm", txt_yes, txt_no, false );
	},
	confirm_delete: function(title, content, callback, callback_no, txt_yes, txt_no)
	{
		txt_yes = (txt_yes == undefined ? $.lang.item('yes'):txt_yes);
		txt_no 	= (txt_no == undefined ? $.lang.item('no'):txt_no);
		
		return this.show(title, content, callback, callback_no, "confirm_delete", txt_yes, txt_no, false );
	},
	confirm_3: function(title, content, callback, callback_no, txt_yes, txt_no)
	{
		return this.show(title, content, callback, callback_no, "confirm", txt_yes, txt_no, false );
	}
}

/**
 * main function for modal dialogs. 
 * this method does an ajax call to the modal-controller, which simply acts as a wrapper for the HTML_helper.
 * We retrieve the complete dialog markup and only need to say modal() on that result.
 * 
 * @param string title			>> title 
 * @param string content		>> dialog body content
 * @param function callback_yes	>> callback for the 'yes' button
 * @param function callback_no	>> callback for the 'no' button. Note that if 'txt_no' is undefined, no button will appear.
 * @param string type			>> succes, info, danger, warning, confirm
 * @param string txt_yes 		>> Text for the YES-Button		>> appears as ok button (addFooterButtonOK)
 * @param string txt_no 		>> Text for the NO-Button		>> appears as cancel button (addFooterButtonCancel)
 * @param bool dismissible		>> if true the dialog will cpontain a Close-Symbol in the upper right
 * @param string size 			>> not in use  
 * @param callback_open			>> JS function called after initialization
 * @param callback_close		>> JS function called when modal gets closed/hidden
 */
$.dialog.show = function(title, content, callback_yes, callback_no, type, txt_yes, txt_no, dismissible, size, callback_open, callback_close)
{
	//$.app.toConsole({"fkt:":"dialog", "title":title, "content":content, "type":type, "txt_yes":txt_yes, "txt_no":txt_no, "dismissable":dismissible, "size":size, "callback_open":callback_open});
	
	dismissible = (dismissible == undefined ? true : dismissible);
	txt_yes 	= (txt_yes == undefined ? $.lang.item('ok') : txt_yes);
	
	var params = {
		id_modal:		$.app.generateUUID(),
		backdrop:		'static',
		color:			type,
		title:			title,
		content:		content,
		txt_yes: 		txt_yes,
		txt_no:			txt_no,
		dismissible:	dismissible
	}	

	$.app.sendAjaxRequest($.dialog.options.ajaxTargetGeneric, params, function(result)
	{
		// ..:::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::..
		// register event-handlers for the YES- and NO-Button if present (currently only theese 2 buttons ar supported.)
		if(callback_yes != undefined && typeof callback_yes == 'function')
		{
			$(document).on('click', "#"+params.id_modal+"-btn-ok",  function()
			{
				$.app.toConsole("callback-YES fired on #"+params.id_modal);
				setTimeout(callback_yes, 10); 
			});
		}
		
		if(callback_no != undefined && typeof callback_no == 'function')
		{
			$(document).on('click', "#"+params.id_modal+"-btn-cancel", function()
			{
				$.app.toConsole("callback-NO fired on #"+params.id_modal);
				setTimeout(callback_no, 10);
			});
		}
		
		// ..:::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::..
		// make sure the modal gets completly removed from the DOM on close
		$(document).on('hidden.bs.modal', '#'+params.id_modal, function() 
		{
			$(this).data('modal', null);
		  	$(this).remove();
		
		  	$.app.toConsole("modal ["+params.id_modal+"] removed from DOM");
		  	
			if(callback_close != undefined && typeof callback_close == 'function')
			{
				callback_close(params.id_modal);
			}
		});
		
		// ..:::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::..
		// Automatically set the focus to a button (cancel button, if possible), so we can hit space after the dialog appears.
		$(document).on('shown.bs.modal', '#'+params.id_modal, function() 
		{
			$.app.toConsole("modal ["+params.id_modal+"] show");
			
			if ($("#"+params.id_modal+"-btn-ok").length > 0) 	{
		  		$("#"+params.id_modal+"-btn-ok").focus();
		  	}
			if ( $("#"+params.id_modal+"-btn-cancel").length > 0){
		  		$("#"+params.id_modal+"-btn-cancel").focus();
		  	}
			if(callback_open != undefined && typeof callback_open == 'function')
			{
				callback_open(params.id_modal);
			}			
		});
		
		// ..:::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::..
		// we have retrieved the complete modal in result.data.
		// make sure you have registered all handlers, before populating the modal

		$(result.data).modal( { show:true } );
	});
	
	// ..:::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::..
	$.app.toConsole("modal ["+params.id_modal+"] generated");
	
	return params.id_modal;
};


/**
 * generate a custom dialog by controller/view
 * 
 * @param string ajax_target
 * @param array params
 * @param function callback
 */
$.dialog.from_controller = function(ajax_target, params, callback_shown, callback_close)
{
	params.rendermode = "AJAX"; // @see BASE_Controller >> checkAuthentification()
	$.app.toConsole(params);
	$.app.sendAjaxRequest(ajax_target, params, function(result)
	{
		// ..:::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::..
		// show-callback
		//$(document).off('shown.bs.modal', '#'+params.id_modal);
		//$(document).off('hidden.bs.modal', '#'+params.id_modal);

		$(document).on('shown.bs.modal', '#'+params.id_modal, function()  
		{
			$.app.toConsole("modal ["+params.id_modal+"] show");
			
			if(callback_shown != undefined && typeof callback_shown == 'function')
			{
				callback_shown(params.id_modal);
			}			
		});
		
		// ..:::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::..
		// make sure the modal gets completly removed from the DOM on close
		$(document).on('hidden.bs.modal', '#'+params.id_modal, function() 
		{
			$(this).data('modal', null);
		  	$(this).remove();
			
		  	$.app.toConsole("modal ["+params.id_modal+"] removed from DOM");
		  	if(callback_close != undefined && typeof callback_close == 'function')
			{
				callback_close(params.id_modal);
			}
		});
		
		$(result.data).modal( { show:true } );
	});
	
	$.app.toConsole("modal ["+params.id_modal+"] generated");
	
	return params.id_modal;
};




































/**
 * @deprecated 
 */
// OLD FUNCTIONS BELOW !!! DO NOT USE THEM


// Shortcut-Functions for simple dialogs 
// ..:::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::..

function dialog_confirm(title, content, callback, callback_no, txt_yes, txt_no)
{
	dialog(title, content, callback, callback_no, "confirm", txt_yes, txt_no, false );
}


/**
 * General function for simple dialogs 
 * @deprecated 
 * @todo this dialog-function is working but also means double work.
 * 		 it does nearly the same in JS as the html_components_helper class in PHP (expect the footer-buttons which are not implemented yet in the helper) .
 * 		 we should make an ajax call to a modal-controller (which is a wrapper for the helper) and retrieve the modal this way 	
 * 			  
 * 
 * @param title		>> title 
 * @param content	>> dialog body content
 * @param callback	>> callback
 * @param type		>> succes, info, danger, warning, confirm
 */
function dialog(title, content, callback_yes, callback_no, type, txt_yes, txt_no, dismissable, size)
{
	// $.app.toConsole({"fkt:":"dialog", "title":title, "content":content, "type":type, "txt_yes":txt_yes, "txt_no":txt_no, "dismissable":dismissable, "size":size});
	
	var dataBackdrop 	= "static";
	var sizes			= new Array("sm", "lg");	// unsupported
	var id_modal 		= $.app.generateUUID();
	var id_buttonOK		= $.app.generateUUID();
	var id_buttonCancel	= $.app.generateUUID();
	
	
	if (type == undefined){
		type == "success"
	}
	if (txt_yes == undefined){
		txt_yes = "OK";
	}
	if (txt_no == undefined){
		txt_no = "Abbrechen";
	}
	if (size == undefined || sizes[size] == undefined){
		size == "lg"
	}
	
	// ..:::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::..
	var bt_cancel = "";
	if (type == "confirm"){
		
		// override some values. 
		// - confirm is not a valid bootstrap style - we use warning in this case.
		// - set the databackdrop to static and negate dismissable (We want the user to choose an option from the buttons)
		type 			= "warning";
		dataBackdrop 	= "static";
		dismissable 	= false;
		
		// create the cancel button
		bt_cancel 		= '<button id="'+id_buttonCancel+'" name="bt_cancel" type="button" class="btn btn-sm " data-dismiss="modal"><i class="fa fa-times"></i></i>&nbsp;'+txt_no+'</button>';
		
	}
	
	// ..:::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::..
	var dismissButton = "";
	if (dismissable === true){
		dismissButton = '<button id="'+id_modal+'-close" title="Fenster schließen" type="button" class="close modal-control" data-dismiss="modal" data-target="#'+id_modal+'" ><span class="text-'+type+'"><span class="glyphicon glyphicon-remove-sign"></span></span></button>';
	}
	
	// ..:::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::..
	var title_icons = new Array()
		title_icons["success"] 		= '<i class="fa fa-check"></i>';
		title_icons["info"] 		= '<i class="fa fa-info-circle"></i>';
		title_icons["warning"] 		= '<i class="fa fa-warning"></i>';
		title_icons["danger"] 		= '<i class="fa fa-fire"></i>';
		title_icons["confirm"] 		= '<i class="fa fa-question"></i>';
		title_icons["default"] 		= '<i class="fa fa-file"></i>';
	
	var title_icon	= title_icons[type];
	
	
	
	var tmpl = [
	'<div id="'+id_modal+'" name="'+id_modal+'" aria-hidden="true" data-backdrop="'+dataBackdrop+'" data-keyboard="false" class="modal modal-message modal-'+type+' fade in " role="dialog">',
		'<div class="modal-dialog">',
			'<div class="modal-content">',
				'<div class="modal-header"><strong class="text-'+type+'">'+title_icon+'&nbsp;&nbsp;'+title+'</strong>'+dismissButton+'</div>',
				'<div id="'+id_modal+'-body" class="modal-body">'+content+'</div>',
				'<div class="modal-footer">',
					bt_cancel,
					'<button id="'+id_buttonOK+'" name="'+id_buttonOK+'" type="button" class="btn btn-sm btn-'+type+'" data-dismiss="modal"><i class="fa fa-check"></i>&nbsp;'+txt_yes+'</button>',
				'</div>',
			'</div>',
		'</div>',
	'</div>'].join('');
	
	/*
	 * '<div class="modal-title" id="dialog_label_1447414654">',
			'<strong>'+title+'</strong>',
		'</div>',
	 */
	
	
	$(tmpl).modal({show:true});
	
	// ..:::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::..
	if(callback_yes != undefined && typeof callback_yes == 'function')
	{
		$(document).on('click', "#"+id_buttonOK, callback_yes );
	}
	if(callback_no != undefined && typeof callback_no == 'function')
	{
		$(document).on('click', "#"+id_buttonCancel, callback_no );
	}
	
	// ..:::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::..
	$(document).on('hidden.bs.modal', '#'+id_modal, function () 
	{
		$(this).data('modal', null);
	  	$(this).remove();
	  	
	  	// $.app.toConsole("remove modal from DOM");
	});

	
	/*
	$(document).on('shown.bs.modal', '#'+id_modal, function () 
	{
		setModalMaxHeight(this);
		$(this).show();
	});
	*/
	
	/*
	$(document).on('shown.bs.modal', '#'+id_modal, function () 
	{
		
		if ($("#"+id_modal+"-body").length > 0){
			
			$("#"+id_modal+"-body").slimscroll({ destroy: true })
			$("#"+id_modal+"-body").slimscroll({
				position: 'right',
				alwaysVisible: true,
				distance: '2px',
				railVisible: false,
				railColor: '#222',
				railOpacity: 0.3,
				wheelStep: 2,
			    size: "5px"
			}).css("width", "100%");
		}
		
	});
	*/
	// ..:::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::..
	$.app.toConsole("modal with id: "+id_modal+" generated");
}
/**
 * @deprecated 
 * @param dialogID
 * @param target
 * @param params
 * @param headerText
 */
function dialog_fromController(dialogID, target, params, headerText)
{
	// $.app.toConsole({"fkt:":"dialog_fromController", "target":target, "params":params, "headerText":headerText});
	
	sendAjaxRequest(target, params, function(result)
	{
		var dataBackdrop 	= "true";
		var btn_dismiss		= '<button id="'+dialogID+'-close" title_left="Fenster schließen" class="modal-control" type="button" data-dismiss="modal" data-target="#'+dialogID+'" ><span class="glyphicon glyphicon-remove-sign"></span></button>';
		
		var tmpl = [
		'<div id="'+dialogID+'" name="'+dialogID+'" aria-hidden="true" data-backdrop="'+dataBackdrop+'" class="modal modal-message fade in" >',
			'<div class="modal-dialog">',
				'<div class="modal-content">',
					'<div class="modal-header"><i class="fa fa-file"></i>&nbsp<strong>'+headerText+'</strong>'+btn_dismiss+'</div>',
					'<div class="modal-body slimScroll">'+result.data+'</div>',
				'</div>',
			'</div>',
		'</div>'].join('');
		//'<div class="modal-title">'+headerText+'</div>',
		$(tmpl).modal("hide");
		
		// ..:::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::..
		$(document).on('hidden.bs.modal', '#'+dialogID, function () 
		{
			$(this).data('modal', null);
		  	$(this).remove();
		  	
		  	$.app.toConsole("remove modal from DOM");
		});
		
		
		// ..:::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::..
		// toConsole("modal with id: "+dialogID+" generated");
	});
}



// ..:::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::..
// ..:::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::..
// ..:::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::..
/*
function test()
{
	var dlg = new Dialog("title", "text");
		dlg.danger();

}
var dialog = 
{
	title 		: "",
	text		: "",
	type		: 0,
}

// Prototypes
var Dialog = function(title, text, type){
	
	this.title 	= title;
	this.text	= text;
	this.type	= type;
	
	console.log("Dialog created"+this);
}

Dialog.prototype.message = function(title, text, callback){
	dialog(title, text, callback, "success");
}

Dialog.prototype.info = function(title, text, callback){
	dialog(title, text, callback, "info");
}

Dialog.prototype.warning = function(title, text, callback){
	dialog(title, text, callback, "warning");
}

Dialog.prototype.error = function(title, text, callback){
	dialog(title, text, callback, "danger");
}
*/
