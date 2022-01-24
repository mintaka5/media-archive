$(function() {
	/**
	 * adds an asset to user's request cart.
	 */
    $('a.aAddToCart').live("click", function() {
            var ele = this;
            var asset = $(ele).parent().parent().siblings('input#assetId').val();

            $.post(globals.ajaxurl + 'lineitem.php', {'_mode':null, '_task':'add', 'asset':asset}, function(d) {
                    $(ele).attr({'class':'aDelFromCart assetAction'}).html('Remove from cart.');
                    procCartPreview();
            }, 'json');
    });
    
    /**
     * delete an asset
     */
    $('button.itemDelBtn').live("click", function() {
            var ele = this;
            var item = $(ele).siblings('input.itemId').val();

            $.post(globals.ajaxurl + 'cart.php', {'_mode':'item', '_task':'del', 'item':item}, function() {
                    procCartPreview();
            }, 'json');
    });
    
    /**
     * delete a user's request from their cart
     */
    $('a.aDelFromCart').live('click', function() {
        var ele = this;
        var asset = $(ele).parent().parent().siblings('#assetId').val();
        
        $.post(globals.ajaxurl+'lineitem.php', {'_mode':null, '_task':'del', 'asset':asset}, function(d) {
            $(ele).attr({'class':'aAddToCart assetAction'}).html('Add to request cart.');
            procCartPreview();
        }, 'json');
    });
    
    /**
     * initiate search for similar assets
     */
    $('.aLikeThis').live('click', function() {
    	//var ele = this;
		
		window.location.href = globals.relurl + '?_page=search&_mode=kwords&id='+$.url.param('id');
    });
    
    $.when(addAssetView($('#viewImg').attr('data-asset_id'), $('#viewImg').attr('data-hash'))).done(function(a) {});

    /**
     * updates the cart window, per page load, and cart interaction
     */
    procCartPreview();

    function procCartPreview() {
            $.post(globals.ajaxurl + 'cart.php', {'_mode':'preview'}, function(d) {
                    $('#cartPreview').html(d);
            }, 'html');
    }
});

function addAssetView(asset_id, hash) {
	return $.post(globals.ajaxurl + 'asset.php', {
		_mode:'meta',
		_task:'count',
		'asset_id':asset_id,
		'hash':hash
	}, null, 'json');
}