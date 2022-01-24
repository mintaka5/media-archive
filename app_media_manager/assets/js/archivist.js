$(function() {
	var GroupsList = Backbone.View.extend({
		el:'#groupsHolder',
		render: function() {
			console.log(this);
		}
	});
	
	var groupsList = new GroupsList();
});