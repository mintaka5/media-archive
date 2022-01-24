$(function() {
	$('.delBtn').live("click", function() {
		var ele = this;
		var item = $(ele).siblings('.itemID').val();
		
		$.when(removeFromCart(item)).done(function(a) {
			$(ele).parent().parent().parent().parent('.cartItem').fadeOut(500, function() {
				$(this).remove();
				
				/**
				 * force redirect to cart page to reset download link
				 * and show that there are no more cart items
				 */
				if(a.data.num_items < 1) {
					window.location.href = globals.relurl + '?_page=cart';
				}
			});
		});
		
		/*$.post(globals.ajaxurl + 'cart.php', {'_mode':'item', '_task':'del', 'item':item}, function(d) {
			$(ele).parent().parent().parent().parent('.cartItem').fadeOut(500, function() {
				$(this).remove();
			});
		}, 'json');*/
	});
});

function removeFromCart(item_id) {
	return $.post(globals.ajaxurl + 'cart.php', {
		_mode:'item',
		_task:'del',
		'item':item_id
	}, null, 'json');
}