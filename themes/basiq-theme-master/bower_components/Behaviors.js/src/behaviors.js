var Behaviors = window.Behaviors || {};

var initializeBehaviors = function(context) {

	if(!context)
		context = $(document);
	
	var containers = context.find('[data-behavior]'),
	success = true;

	$.each(containers, function(){
	 	var container = $(this);
	 	$.each(container.attr('data-behavior').split(" "), function(index, behaviorName) {
		 try {
		 	container[behaviorName] = new Behaviors[behaviorName](container);
			} catch (e) {
		 		success = e.stack;
			}
	 	});
 	});
 return success;
};

$(document).ready(function(){
	var start = +new Date();
	var success = initializeBehaviors();
	var end =  +new Date();

	if($('body').attr('data-debug')) {
		if(success === true ) {
			console.log("Scripts Executed in " + (end - start)/1000 + " seconds.");
		} else {
			console.log(success);
		}
	}
});