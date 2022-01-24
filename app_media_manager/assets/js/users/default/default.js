$(function() {
	$('.userType').change(function() {
		var ele = this;
		
		var typeId = $(this).val();
		var userId = $(this).parent().parent().siblings('input.userId').val();
		
		$.post(globals.ajaxurl + 'users.php', {'_mode':'type', '_task':'edit', 'user':userId, 'type':typeId}, function(d) {
			$('<span />').html('Status updated').attr({'class':'notification'}).appendTo($(ele).parent().parent().parent().siblings('.userFullName'));
			setTimeout(function() {
				$($(ele).parent().parent().parent().siblings('.userFullName').children('.notification')).fadeOut(750, function() {
					$(this).remove();
				});
			}, 500);
		}, 'json');
	});
	
	$('.userDel').click(function() {
		var ele = this;
		
		var conf = confirm("Are you sure, you want to delete this user?");
		
		if(conf == true) {
			$.post(globals.ajaxurl + 'users.php', {'_mode':'del', 'user':$(ele).parent().parent().siblings('input.userId').val()}, function(d) {
				var pageNum = ($.url.param('pageNum')|1);
				window.location.href = globals.relurl + '?_page=users&pageNum=' + pageNum;
			}, 'json');
		}
	});
});