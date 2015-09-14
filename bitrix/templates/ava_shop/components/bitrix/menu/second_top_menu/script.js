$(document).ready(function(){
	$("#nav li.navli").click(function () { 
		if ($(this).find(".bigwindow").hasClass("odd")) {
			$(this).find(".bigwindow").removeClass("odd").hide();
		} else {
			$(".bigwindow").removeClass("odd").hide();
			$(this).find(".bigwindow").addClass("odd").show();
		}
		//alert($(this).find(".bigwindow").html());
	}, function () { 
		if ($(this).find(".bigwindow").hasClass("odd")) {
			$(this).find(".bigwindow").removeClass("odd").hide();
		} else {
			$(".bigwindow").removeClass("odd").hide();
			$(this).find(".bigwindow").addClass("odd").show();
		}
	});
});