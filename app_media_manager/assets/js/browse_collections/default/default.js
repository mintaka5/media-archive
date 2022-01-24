$(function() {
	$(document).on('click', '.containerGroups', function(e) {
		window.location.href = $(e.currentTarget).attr('data-href');
	});
});