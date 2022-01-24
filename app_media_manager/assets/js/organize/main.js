$(function() {
	var debug = function(msg) {
		console.log(msg);
	};
	
	/**
	 * entire layout
	 */
	var layout = $('#groups-layout').layout({
		applyDefaultStyles: true,
		spacing_open: 2,
		spacing_closed: 2,
		north__closable: false,
		north__resizable: false,
		north__size: 85,
		east__initClosed: true,
		east__size: 250,
		east__maxSize: 250,
		west__size: 250,
		south__size: 135,
		south__resizable: false,
		south__closable: false
	});
	
	/**
	 * for some reason CSS doesn't want to do this, so using JQuery to 
	 * force hiding of scrollbar
	 */
	$('.ui-layout-south').css({'overflow':'hidden'});
	
	/**
	 * Number of asset items for south section's slider
	 */
	var totalAssets = 0;
	
	/**
	 * Sets the list of asset item's container to a width that will contain all items
	 */
	var assetListWidth = 0;
	
	/**
	 * The item index of the next full asset item we need to tell
	 * the scroller to stop at.
	 */
	var nextStop = 0;
	
	/**
	 * How many items to step through per scroll
	 */
	var stepper = 0;
	
	/**
	 * How fast to scroll asset items
	 */
	var scrollSpeed = 500;
	
	/**
	 * layout tabs 
	 */
	var tabs = $('#tabs').tabs({
		'add': function(evt, ui) {}
	});
			
	var initGroupDrops = function() {
		$('#group-items .item').droppable({
			'accept':'#asset-list .item',
			'drop': function(evt, ui) {
				var assetId = $(ui.draggable).children('.asset-id').val();
				var groupId = $(this).children('.grp-id').val();
				console.log('Asset ID: ' + assetId);
				console.log('Group ID: ' + groupId);
			},
			'over': function(evt, ui) {},
			'out': function(evt, ui) {}
		});
	};
	
	var initAssetDrags = function(lay, tab) {
		var items = tab.find('#asset-list .item');

		items.draggable({
			'revert':true,
			'opacity':0.7,
			'zIndex':999999,
			'containement':tab.attr('id'),
			'start':function(evt, ui) {
				lay.allowOverflow('south');
			},
			'stop': function(evt, ui) {
				lay.resetOverflow('south');
			},
			'drag': function(evt, ui) {}
		});
	};
	
	/**
	 * Sets up the dimaensions and steps needed for scrolling the assets
	 */
	var setSlider = function() {
		totalAssets = $('#asset-list .item').length;
		assetListWidth = ($('#asset-list .item').outerWidth()+4)*totalAssets;
		$('#asset-list').width(assetListWidth);
		nextStop = ($('#asset-list .item').width()+$('#asset-list .item').border().left+$('#asset-list .item').border().right+$('#asset-list .item').margin().left+$('#asset-list .item').margin().right);
		nextStop = Math.round($('#asset-list-holder').width()/nextStop)-2;
		stepper = nextStop;
	};
	
	var initGroup = function(obj) {
		$(obj).droppable({
			'accept':'#asset-list .item',
			'drop': function(evt, ui) {
				var assetId = $(ui.draggable).children('.asset-id').val();
				var grpId = $('#grp-id').val();
				debug('Asset ID: ' + assetId);
				debug('Group ID: ' + grpId);
			},
			'over': function(evt, ui) {},
			'out': function(evt, ui) {}
		});
		
		$(obj).find('.item').draggable({
			revert: function(sock) {
				this.isValid = false;
				
				if(sock != false) {
					this.isValid = true;
				}
			},
			opacity:0.7,
			zIndex:999999,
			containment:'#groups-tab',
			scroll:false,
			helper: function(evt, ui) {							
				var copy = $(this).clone().appendTo('body').css({'z-index':5}).show();
				
				return copy;
			},
			'start': function(evt, ui) {
				layout.allowOverflow();
				
				$('<div />').attr({'id':'delete-asset'}).css({
					'background-color':'#fff',
					'color':'#990000',
					'text-align':'center',
					'position':'absolute',
					'left':0,
					'top':0,
					'border':'1px dashed #990000',
					'margin':'4px 3px 0 4px',
					'font-size':'350%',
					'height':$('.ui-layout-south').height()+($('.ui-layout-south').padding().bottom)+'px',
					'width':$('.ui-layout-south').outerWidth()+'px'
				}).html('Drag asset here to delete from group.').droppable({
					'accept':'#group-assets-list .item',
					'drop': function(evt, ui) {
						debug('Asset ID: '+$(ui.draggable).children('.asset-id').val());
						
						//////////////////////////////
						// wrap in Ajax call
						$(ui.helper.context).remove();
						//////////////////////////////
					}
				}).appendTo($('.ui-layout-south'));
			},
			'stop': function(evt, ui) {
				layout.resetOverflow();
				
				$('#delete-asset').fadeOut(500, function() {
					$(this).remove();
				});
			},
			'drag': function(evt, ui) {}
		});
	};
	
	/**
	 * Load up groups
	 */
	var getGroups = function(obj) {
		$('.ui-layout-center').block({
			'message':'Loading...',
			'css':{'border':'2px solid #e0e0e0', 'background-color':'#333', 'color':'#fff'}
		});
		
		$('#groups-holder').load(obj.controller, obj.query, function() {
			$('.ui-layout-center').unblock();
			
			initGroupDrops();
			
			$('#group-items .item').live('click', function() {
				var grpId = $(this).children('.grp-id').val();
				
				$('.ui-layout-center').load(globals.ajaxurl+'organize.php', {_mode:'groups', _task:'get_one', 'id':grpId}, function() {
					initGroup(this);
				});
			});
		});
	};
	
	/**
	 * Load up asset items
	 */
	var getAssets = function(obj) {
		$('.ui-layout-south').block({
			'message':'Loading...',
			'css':{'border':'2px solid #e0e0e0', 'background-color':'#333', 'color':'#fff'}
		});
		
		$('#asset-list').load(obj.controller, obj.query, function() {
			$('.ui-layout-south').unblock();
			
			setSlider();
			
			initAssetDrags(layout, $('#groups-tab'));
		});
	};
	
	getGroups({'controller': globals.ajaxurl+'organize.php', 'query': {_mode:'groups', _task:'get_all'}});
	
	getAssets({'controller':globals.ajaxurl+'organize.php', 'query':{_mode:'assets', _task:'get_all'}});
	
	/**
	 * Need to reset asset item slider dimensions and stepper
	 */
	$(window).bind('resize', function() {
		setSlider();
	});
	
	/**
	 * The click event for scrolling asset items left
	 */
	$('#asset-nav .prev').live('click', function() {
		var assetList = $(this).siblings('#asset-list-holder').find('#asset-list');
		var assetListItems = $(this).siblings('#asset-list-holder').find('.item');
		
		if(nextStop > 0) {
			nextStop -= stepper;

			assetList.animate({'left':-($(assetListItems[nextStop]).position().left)}, scrollSpeed, function() {});
		}
	});
	
	/**
	 * The click event for scrolling asset items right
	 */
	$('#asset-nav .next').live('click', function() {
		var assetList = $(this).siblings('#asset-list-holder').find('#asset-list');
		var assetListItems = $(this).siblings('#asset-list-holder').find('.item');
		
		if($(assetListItems[nextStop]).position() != null) {
			assetList.animate({'left':-($(assetListItems[nextStop]).position().left)}, scrollSpeed, function() {
				nextStop += stepper;
			});
		}
	});
});