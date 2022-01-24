$(function() {
	$('.rmvGroup').on('click', function(e) {
		e.preventDefault();
		
		var groupId = $(e.currentTarget).parent().siblings('.groupId').val();
		
		var confirm = window.confirm("Are you sure you want to remove this group?");
		
		if(confirm == true) {
			$.when(removeGroup(groupId)).done(function(a) {
				$(e.currentTarget).parent().parent().parent().parent().parent().fadeOut('fast', function() {
					$(this).remove();
				});
			});
		}
	});
});

function removeGroup(group_id) {
	return $.post(globals.ajaxurl + 'groups.php', {'_mode':'del', '_id':group_id}, null, 'json');
}