if (typeof jQuery === 'undefined') { 
	throw new Error('This JavaScript requires jQuery'); 
}

$(document).ready(function()
{
	$.app.toConsole("menu.js ready", "log");
	
	$(".navbar-right-expand-toggle").css('color', $('.navbar-brand').css("color") ); 
	
	// login button click handler
	$('#nform_login').submit(function(e) {
        e.preventDefault();
        
        // we cannot guarantee, that the login.js is loaded. so we get it by our self
        if ($.login == undefined)
        {
        	$.getScript(baseUrl+"resources/js/login.js" )
        		.done(function( script, textStatus ) {
        			$.login.authenticate("nform_login");
				})
				.fail(function( jqxhr, settings, exception ) {
					$.app.toConsole("could not load login.js", "error");
				});
        }
        else{
        	$.login.authenticate("nform_login");
        }
	});
	
	// nav expand toggle
	$(".navbar-right-expand-toggle").click(function() 
	{
		$(".navbar-right").toggleClass("expanded");
		return $(".navbar-right-expand-toggle").toggleClass("fa-rotate-90");
	});
});
/*
$(function() 
{
	
});

$(function() 
{
	
});
*/

