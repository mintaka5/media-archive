var droppGrps = false;
var draggImgs = false;

$(function() {
	$('.droppGrp').live('click', function() {
		window.location.href = globals.relurl + '?_page=group&_mode=view&id=' + $(this).attr('gid');
	});
	
	groupsList(1);
	
	assetsList(1);
	
	$('#assetsLoader').dialog({
		modal:true,
		closeOnEscape:false,
		draggable:false,
		resizable:false,
		'title':'Loading...',
		'open':function(evt, ui) {
			$(this).parent().children().children('.ui-dialog-titlebar-close').hide();
		},
		'overlay':{'opacity':1, 'background':'black'},
		'height':95
	});

	$("#grpContainer").autocomplete({
		'source': function(request, response) {
			$.post(globals.ajaxurl + 'containerAutocomplete.php', {
				'query': request.term
			}, function(d) {
				response($.map(d, function(item) {
					return {
						'label': item.title,
						'value': item.id
					};
				}));
			}, "json");
		},
		'minLength': 2,
		'select': function(event, ui) {
			$("#grpContainer").val(ui.item.label);
			$("#grpContainerId").val(ui.item.value);
			
			return false;
		}
	});

	$("#addGroup").live("click", function() {
		$("#addGroupForm").submit();
	});
	
	$(".assetTile").live("mouseover", function() {
		$(this).parent().css({'background-color':'#bce0f9'});
		
		
	});
	
	$(".assetTile").live("mouseout", function() {
		$(this).parent().css({'background-color':'#bfbfbf'});
	});
	
	$(".assetTile").live("click", function() {
		window.location.href = $(this).find("a.assetsListThumb").attr('href');
	});
	
	$('#searchAssetsBtn').on('click', function(e) {
		$('#curSearchAssets').val($('#searchAssets').val());
		
		$('#indexMain').block();
		
		$("#assetsList").load(globals.ajaxurl + 'assetSearch.php', {'qry':$('#searchAssets').val()}, function() {
			$('#indexMain').unblock();
			$('#searchAssets').val('');
			
			initAssetTips();
			initDraggables();
			initDroppables();
		});
	});
	
	$('#searchAssets').on('keyup', function(e) {
		if(e.keyCode == 13) {
			$('#curSearchAssets').val($('#searchAssets').val());
			
			$('#indexMain').block();
			
			$("#assetsList").load(globals.ajaxurl + 'assetSearch.php', {'qry':$('#searchAssets').val()}, function() {
				$('#indexMain').unblock();
				$('#searchAssets').val('');
				
				initAssetTips();
				initDraggables();
				initDroppables();
			});
		}
	});
	
	$('#searchGroups').autocomplete({
		'source':function(req, res) {},
		'minLength':0,
		'search':function(evt, ui) {
			$('#groupsList').html("");
			
			$('#groupsList').load(globals.ajaxurl + 'groupSearch.php', {'qry':$(evt.target).val()}, function() {
				initDraggables();
				initDroppables();
			});
		}
	});
	
	$(".clickGrpDelete").live("click", function() {
		var ele = this;
		
		$.post(globals.ajaxurl + 'groups.php', {
			'_mode':'del',
			'_id':$(ele).attr('id')
		}, function(d) {
			$.post(globals.ajaxurl + 'groupsList.php', {}, function(data) {
				$("#groupsList").html(data);
			}, "html");
		}, 'json');
	});
	
	$('#addGrpBtn').click(function() {
		$.post(globals.ajaxurl + 'groups.php', {
			'_mode':'add',
			'_task':'quick',
			'title':$('#grpTitle').val()
		}, function(d) {
			groupsList(1);
			$('#grpTitle').val("");
		}, 'json');
	});
	
	/*$('.aDelAsset').live("click", function() {
		var ele = this;
		
		$.post(globals.ajaxurl + 'asset.php', {
			'_mode':'del',
			'aid':$(ele).siblings('.hdnAssetId').val()
		}, function(d) {
			$(ele).parent().parent().fadeOut('slow', function() {
				assetsList(1);
			});
		}, 'json');
	});*/
});


// FUNCTIONS

function initDroppables() {
	droppGrps = $(".droppGrp");
	
	$(droppGrps).droppable({
		'accept':$(draggImgs),
		'drop':function(evt, ui) {
			var aid = $(ui.draggable).attr("aid");
			var gid = $(this).attr("gid");
			
			var dragClone = $(ui.draggable).clone().css({'position':'absolute', 'left':$(ui.draggable).offset().left, 'top':$(ui.draggable).offset().top}).appendTo("body");
			
			$(evt.target).css({'background-color':'#7ec6d7'});
			
			$(dragClone).hide('scale', {'percent':0, 'left':$(ui.draggable).position().left, 'top':$(ui.draggable).position().top}, 250, function() {
				// add to database
				$.post(globals.ajaxurl + 'addAssetToGroup.php', {
					'aid':aid,
					'gid':gid
				}, function(d) {
					$('<div />').attr({'id':'sucImgGrp', 'class':'notification'}).html("Successfully added image to group.").appendTo($(evt.target));
					setTimeout(function() {
						$('#sucImgGrp').fadeOut(1000, 'easeInQuart', function() {
							$(this).remove();
						});
					}, 500);
				}, 'json');
				
				$(this).remove();
			});
		},
		'over':function(evt, ui) {
			$(evt.target).css({'background-color':'#3e93bf'});
		},
		'out':function(evt, ui) {
			$(evt.target).css({'background-color':'#7ec6d7'});
		}
	});
}

function initDraggables() {
	draggImgs = $(".draggImg");
	
	$(draggImgs).draggable({
		'revert':true,
		'opacity':0.35,
		'zIndex':999999
	});
}

function groupsList(num) {
	$.get(globals.ajaxurl + 'groupsList.php', {'pageNum': num}, function(data) {
		$("#groupsList").html(data);
		
		/*$('.droppGrp').click(function() {
			window.location.href = globals.relurl + '?_page=group&_mode=view&id=' + $(this).attr('gid');
		});*/
		
		droppGrps = $(".droppGrp");
		
		initDroppables();
	}, "html");
}

function initAssetTips() {
	$('.aPreview').qtip({
            'position':{ 'at':'top right' },
            'style': { 'name':'dark' },
            'show': {'delay': 500, 'solo':true, 'effect': function(offset) {
                    $(this).fadeIn(750);
            }},
            'hide': {'delay': 1000, 'effect': function(offset) {
                    $(this).fadeOut(500);
            }},
            'content':{
                    'text':"loading&#133;", 
                    'ajax':{
                            'url':globals.ajaxurl + 'asset.php',
                            'data':{'_mode':'tip', 'aid':''}
                    }
            }, 
            'events': {
                    'focus':function(evt, api) {
                            $.post(globals.ajaxurl + 'asset.php', {
                                    '_mode':'tooltip', 'aid':$(evt.originalEvent.currentTarget).siblings('input.hdnAssetId').val() 
                            }, function(d) { api.set('content.text', d); }, 'html');
                    }
            }
	});
}

function assetsList(num, query) {
	var query = $('#curSearchAssets').val();
	
	$('body').css({'overflow':'hidden'});
	
	$('#assetsLoader').dialog('open');
	
	$.get(globals.ajaxurl + 'assetsList.php', {'pageNum': num, 'qry':query}, function(data) {
		$("#assetsList").html(data);
		
		var numTiles = $('.tileImg').length;
		var numLoaded = 0;
		var percentLoaded = 0;
		$('.tileImg').imgpreload({
			'each': function() {
				percentLoaded = (100*numLoaded)/numTiles;
				$('#progBar').width(percentLoaded);
				$('#progress').html(percentLoaded*10 + '%');
				
				numLoaded++;
			},
			'all': function() {
				$('#assetsLoader').dialog('close');
				$('body').css({'overflow':''});
				$('#progress').html('0%');
				$('#progBar').width(0);
			}
		});
		
		if($('.tileImg').length <= 0) {
			$('#assetsLoader').dialog('close');
		}
		
		initAssetTips();
		
		initDraggables();
		
		initDroppables();
	}, "html");
}