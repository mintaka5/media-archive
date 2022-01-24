var groupView = {
    addAssetToCart: function(assetId) {
        return $.post(globals.ajaxurl + 'lineitem.php', {
            _mode: null,
            _task: 'add',
            asset: assetId
        }, null, 'json');
    },
    removeItemFromCart: function(itemId) {
        return $.post(globals.ajaxurl + 'cart.php', {
            _mode: 'item',
            _task: 'del',
            item: itemId
        }, null, 'json');
    },
    getCartPreview: function() {
        return $.post(globals.ajaxurl + 'cart.php', {
            _mode: 'preview'
        }, null, 'html');
    }
};

$(function() {
    $(document).on('click', 'button.shoppingCart', function(e) {
        var ele = this;
        var asset = $(this).siblings('input.assetId').val();

        $.when(groupView.addAssetToCart(asset)).done(function(a) {
            $(ele).attr({'class':'orderedCart btn', 'disabled':'disabled'});
            procCartPreview();
        });
    });

    $(document).on('click', 'button.itemDelBtn', function(e) {
        var ele = this;
        var item = $(this).siblings('input.itemId').val();

        $.when(groupView.removeItemFromCart(item)).done(function(a) {
            procCartPreview();
        });
    });

    $(document).on('click', 'button.moreLikeThis', function(e) {
        window.location.href = globals.relurl + '?_page=search&_mode=kwords&id='+$(this).siblings('input.simId').val();
    });
	
	procCartPreview();
	
	function procCartPreview() {
        $.when(groupView.getCartPreview()).done(function(a) {
            $('#cartPreview').html(a);
        });
	}
});