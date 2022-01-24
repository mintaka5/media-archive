/**
 * 
 */
$(function() {
	/**
	 * list all groups assigned to this asset
	 */
	$("#groupsList").load(globals.ajaxurl + 'assetGroupsList.php', {'id':$("#assetId").val()});
	
	/**
	 * Asset approval function
	 */
	$("#chkAssetAppr").live("change", function() {
		var ele = this;
		
		$.post(globals.ajaxurl + 'asset.php', {
			'_mode':'appr',
			'id':$(ele).val()
		}, function(d) {
			if(d == 0) {
				$(ele).prev('#astStatus').html("Not active");
			} else {
				$(ele).prev('#astStatus').html("Active");
			}
		}, 'json');
	});
	
	/**
	 * edit assets' caption
	 */
	$("#captionTxt").editable(globals.ajaxurl + 'asset.php', {
		'submitdata': {
			'_mode':'edit',
			'_task':'caption',
			'aid':$.url.param('id')
		},
		'callback': function(val, settings) {
			$(this).html(val);
		},
		'tooltip':'Click to edit caption...',
		'indicator': 'Saving...',
		'submit':'Save',
		'cancel':'Cancel',
		'style':'display:inline',
		'height':20,
		'width':150
	});
	
	/**
	 * edit assets' title
	 */
	$("#titleTxt").editable(globals.ajaxurl + 'asset.php', {
		'submitdata': {
			'_mode':'edit',
			'_task':'title',
			'aid':$.url.param('id')
		}, 'callback': function(val, set) {
			$(this).html(val);
		},
		'tooltip':'Click to edit title...',
		'indicator':'Saving...',
		'submit':'Save',
		'cancel':'Cancel',
		'style':'display:inline',
		'height':20,
		'width':200
	});
	
	/**
	 * edit assets' description
	 */
	$("#descTxt").editable(globals.ajaxurl + 'asset.php', {
		'submitdata':{
			'_mode':'edit',
			'_task':'desc',
			'aid':$.url.param('id')
		}, 'callback': function(val, set) {
			$(this).html(val);
		},
		'tooltip':'Click to edit description...',
		'indicator':'Saving...',
		'submit':'Save',
		'cancel':'Cancel',
		'type':'textarea',
		'height':100,
		'width':300
	});
	
	/**
	 * edit assets' credit
	 */
	$("#creditTxt").editable(globals.ajaxurl + 'asset.php', {
		'submitdata': {
			'_mode':'edit',
			'_task':'credit',
			'aid':$.url.param('id')
		}, 'callback': function(val, set) {
			$(this).html(val);
		},
		'tooltip':'Click to edit credit...',
		'indicator':'Saving...',
		'submit':'Save',
		'cancel':'Cancel',
		'style':'display:inline',
		'height':20,
		'width':200
	});
});