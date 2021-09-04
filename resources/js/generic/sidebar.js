if (typeof jQuery === 'undefined') { 
	throw new Error('This JavaScript requires jQuery'); 
}

$.sidebar = {
		
};

$.sidebar.load_view = function(target)
{
	$.app.sendAjaxRequest(target, [{}], function success(result)
	{
		if (result.error){
			$.dialog.error($.lang.item('error'), result.error);
		}
		else{
			$.app.replaceContent(result.data );
		}
	}, true, null, $.lang.item('msg_wait') );
};

$.sidebar.set_sidebar_state = function()
{
	$.ajax({
		type: "post",
		url: baseUrl+"/home/set_sidebar",
		data: {
			"expanded":$.cookie.getCookie( $.cookie.sidebar_state ),
			"selected_item": $.cookie.getCookie( $.cookie.sidebar_selected_item )
		}
	});
}

$(document).ready(function()
{
	$.app.toConsole("sidebar.js ready", "log");
	
	//$('.side-menu').css('background-color', $('#topnav').css("background-color") );
	//$('.side-menu').css('background-image', $('#topnav').css("background-image") );
	
	$(".navbar-expand-toggle").css('color', $('.navbar-brand').css("color") ) 
	$(".navbar-expand-toggle").click(function() 
	{
		var expanded = ! $("#app-container").hasClass('expanded');		
		$(".app-container").toggleClass("expanded");

		if (expanded){
			$(this).find("i").removeClass("fa fa-ellipsis-v").addClass("fa fa-bars")
		}else{
			$(this).find("i").removeClass("fa fa-bars").addClass("fa fa-ellipsis-v")
		}
		
		$.cookie.setCookie( $.cookie.sidebar_state, expanded);
		
		
		$.sidebar.set_sidebar_state();
		
		return; //$(".navbar-expand-toggle").toggleClass("fa-rotate-90");
	});
	
	$(".side-menu .nav .dropdown").on('show.bs.collapse', function() 
	{
		$(".side-menu .nav .dropdown .collapse").collapse('hide');
	});
	
	//$.app.toConsole({"cookie -> sidebar_state":$.cookie.getCookie( $.cookie.sidebar_state )});

	// ..:::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::..
	// since js is running an collapsing sub-nodes should work, lets remove the href's from the root nodes
	$("a.sidebar-root-node").each(function( index ) 
	{
		$(this).removeAttr('href'); 
		
		$(this).on('click', function()
		{	// try to store sidebar selected root node in cookie
			$.cookie.setCookie( $.cookie.sidebar_selected_item, $(this).attr("data-target") );
			$.sidebar.set_sidebar_state();

			//$(".side-menu-container li").removeClass("active");
			//$(this).parent().addClass("active");
		});
		// $.app.toConsole({"fkt":"each a.sidebar-root-node", "index":index, "open-item":$.cookie.getCookie($.cookie.sidebar_collapsed_item), "data-target":$(this).attr("data-target")});
	});
	
	$("a.sidebar-solo-node").each(function( index )
	{
		/*
		$(this).on('click', function(){
			$(".side-menu-container li").removeClass("active");
			$(this).parent().addClass("active");
			
			$.cookie.setCookie( $.cookie.sidebar_selected_item, $(this).attr("data-target") );
		});
		*/
	});

	/**
	$("a.sidebar-child-node").each(function( index ) {
		
		$(this).on('click', function()
		{
			//$.sidebar.load_view( $(this).attr('href'));
			//return false;
		});
	});	
	*/
	
});