$(function() {
	$('#groupsTiles').imagesLoaded(function() {
		$('#groupsTiles').masonry({itemSelector:'.brwsGrpTile'});
	});
	
	$(document).on("click", 'button.itemDelBtn', function(e) {
		var item = $(e.currentTarget).siblings('input.itemId').val();
		
		$.when(deleteCartItem(item)).done(function(a) {
			$.when(procCartPreview()).done(function(b) {
				$('#cartPreview').html(b);
			});
		});
	});
	
	$.when(procCartPreview()).done(function(b) {
		$('#cartPreview').html(b);
	});
});

function deleteCartItem(item_id) {
	return $.post(globals.ajaxurl + 'cart.php', {'_mode':'item', '_task':'del', 'item':item_id}, null, 'json');
}

function procCartPreview() {
	return $.post(globals.ajaxurl + 'cart.php', {'_mode':'preview'}, null, 'html');
}