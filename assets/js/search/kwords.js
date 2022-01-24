$(function() {
    $('button.shoppingCart').live("click", function() {
            var ele = this;
            var asset = $(ele).siblings('input.assetId').val();

            $.post(globals.ajaxurl + 'lineitem.php', {'_mode':null, '_task':'add', 'asset':asset}, function(d) {
                    $(ele).attr({'class':'orderedCart btn', 'disabled':'disabled'});
                    procCartPreview();
            }, 'json');
    });

    $('button.itemDelBtn').live("click", function() {
            var ele = this;
            var item = $(ele).siblings('input.itemId').val();

            $.post(globals.ajaxurl + 'cart.php', {'_mode':'item', '_task':'del', 'item':item}, function() {
                    procCartPreview();
            }, 'json');
    });
    
    $('button.moreLikeThis').live("click", function() {
		var ele = this;
		
		window.location.href = globals.relurl + '?_page=search&_mode=kwords&id='+$(ele).siblings('input.simId').val();
	});

    procCartPreview();

    function procCartPreview() {
            $.post(globals.ajaxurl + 'cart.php', {'_mode':'preview'}, function(d) {
                    $('#cartPreview').html(d);
            }, 'html');
    }
});