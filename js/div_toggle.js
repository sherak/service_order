$(function() {
	$("#nav a").click(function(){
	    $(".toggle").hide();
	    var toShow = $(this).attr('href');
	    $(toShow).show();
	});

	var toShow = $(location.hash);
	if(toShow.length > 0) {
		$(".toggle").hide();
	    $(location.hash).show();
	}
});