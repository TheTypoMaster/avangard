$(function(){
	$("#footer_menu .menu_item a").click(function(){
		if(!$(this).parent().find('.bigwindow').is(':visible')){
			$('#footer_menu .menu_item .bigwindow').hide();
			$(this).parent().find('.bigwindow').show();
		}else{
			$('#footer_menu .menu_item .bigwindow').hide();
		}
	});
	$("#footer_menu .menu_item .bigwindow .closew").click(function(){
		$(this).parent().hide();
	});
});