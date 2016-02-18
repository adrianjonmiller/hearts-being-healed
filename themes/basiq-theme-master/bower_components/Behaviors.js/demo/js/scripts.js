Behaviors.test = function(container) {
	container.html("Success");
	
	container.on('click', function(){
		$(this).append('<span>').html('Click Success')
	})
}