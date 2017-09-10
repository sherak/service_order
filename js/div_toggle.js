$(function() {
	$('#login').on('submit', function() {
    	$(".loginfields").show();
	});

	$('#my_profile_nav li').click(function(e) {
        $('#my_profile_nav li').removeClass('active');
        var $this = $(this);
        if(!$this.hasClass('active')) {
            $this.addClass('active');
        }
    });

	$('#profile_nav li').click(function(e) {
        $('#profile_nav li').removeClass('active');
        var $this = $(this);
        if(!$this.hasClass('active')) {
            $this.addClass('active');
        }
    });

	$(".nav li a").click(function(){
	    $(".toggle").addClass('hidden');
	    var toShow = $(this).attr('href');
	    $(toShow).removeClass('hidden');
	});

	var elemShow = $(location.hash);
	if(elemShow.length > 0) {
		$(".toggle").addClass('hidden');
	    elemShow.removeClass('hidden');

	  	$('#my_profile_nav').find('>li a[href$="' + location.hash + '"]').click();
	  	$('#profile_nav').find('>li a[href$="' + location.hash + '"]').click();
	}
});