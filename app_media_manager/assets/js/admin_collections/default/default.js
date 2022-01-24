var containers = {
	ajaxurl:globals.ajaxurl + 'containers.php',
	remove: function(container_id) {
		return $.post(this.ajaxurl, {
			_mode:'remove',
			'container_id':container_id
		}, null, 'json');
	}
};

$(function() {
	$(document).on('click', '.rmvContainer', function(e) {
		e.preventDefault();
		
		var container_id = $(e.currentTarget).attr('data-id');
		
		$.when(containers.remove(container_id)).done(function(a) {
			$(e.currentTarget).parent().parent().parent().parent().parent('.containerListItem').fadeOut('fast', function() {
				$(this).remove();
			})
		});
	});
});