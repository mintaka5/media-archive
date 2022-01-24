$(function() {
	$('#editKwordHolder').dialog({
		autoOpen:false,
		modal:true,
		width:350,
		title:'Keyword editor',
                buttons: [
                    {text:'OK', click: function(e) {
                        var keyword = $('#editKeywordTxt').val();
                        var id = $('#editKwordId').val();

                        if(keyword != '') {
                            $.when(editKeyword(keyword, id)).done(function(a) {
                                if(a.status != false) {
                                    $('<div />').attr({'class':'notification'}).html('Successfully updated keyword, ' + keyword).prependTo($('#editKeywordTxt').parent()).delay(5000).fadeOut('slow', function() {
                                            $(this).remove();

                                            //$('#editKwordHolder').dialog('close');
                                    });
                                } else {
                                    $('<div />').attr({'class':'error'}).html('Failed to update keyword, ' + keyword).prependTo($('#editKeywordTxt').parent()).delay(5000).fadeOut('slow', function() {
                                            $(this).remove();
                                    });
                                }
                                
                                $.when(getKeywords(1)).done(function(b) {
                                    $('#keywordsList').html(b);
                                });
                                
                            });
                        }   
                    }},
                    {text:'Cancel', click:function(e) {
                           $('#editKwordHolder').dialog('close'); 
                    }}
                ]
	});
	
	$.when(getKeywords(1)).done(function(gk) {
		$('#keywordsList').html(gk);
	});
	
	$(document).on('click', '.rmvKword', function(e) {
		e.preventDefault();
		
		var id = $(this).siblings('.kwordId').val();
		
		$.when(deleteKeyword(id), getKeywords(1)).done(function(rmvk, gk) {
			$('#keywordsList').html(gk[0]);
			
			$('<div />').attr('class', 'notification').html('Successfully removed keyword').prependTo($('#keywordsList').parent().parent()).delay(5000).fadeOut('slow', function() {
				$(this).remove();
			});
		});
	});
	
	$('#kwordSch').on('keyup', function(e) {
		var keyword = $(this).val();
		
		$.when(searchKeywords(keyword)).done(function(a) {
			$('#keywordsList').html(a);
		});
	});
	
	$('#aListAllKwords').on('click', function(e) {
		e.preventDefault();
		$.when(getKeywords(1)).done(function(gk) {
			$('#keywordsList').html(gk);
		});
	});
	
	$(document).on('click', '.editKword', function(e) {
		e.preventDefault();
		
		$.when(getKeywordById($(this).siblings('.kwordId').val())).done(function(a) {
			if(a.status == true) {
				$('#editKwordId').val(a.data.id);
				$('#editKeywordTxt').val(a.data.keyword);
				$('#editKwordHolder').dialog('open');
			}
		});
	});
	
	/*$('#editKwordBtn').on('click', function(e) {
		var keyword = $('#editKeywordTxt').val();
		var id = $('#editKwordId').val();
		
		if(keyword != '') {
			$.when(editKeyword(keyword, id), getKeywords(1)).done(function(a, b) {
				if(a[0].status != false) {
					$('<div />').attr({'class':'notification'}).html('Successfully updated keyword, ' + keyword).prependTo($('#editKeywordTxt').parent()).delay(5000).fadeOut('slow', function() {
						$(this).remove();
						
						//$('#editKwordHolder').dialog('close');
					});
				} else {
					$('<div />').attr({'class':'error'}).html('Failed to update keyword, ' + keyword).prependTo($('#editKeywordTxt').parent()).delay(5000).fadeOut('slow', function() {
						$(this).remove();
					});
				}
				
				$('#keywordsList').html(b[0]);
			});
		}
	});*/
	
	$('#kwordAddBtn').on('click', function(e) {
		if($('#kwrdTxt').val() != '') {
			var keyword = $('#kwrdTxt').val();
			
			$.when(getKeyword(keyword)).done(function(a) {
				if(a.status == false) {
					$.when(addKeyword(keyword), keywordsList(1)).done(function(a, b) {
						$('#kwrdTxt').val('');
						
						$('<div />').attr({'class':'notification'}).html('Successfully added new keyword, ' + keyword).prependTo($('#kwordAddBtn').parent()).delay(5000).fadeOut('slow', function() {
							$(this).remove();
						});
					});
				} else {
					$.when(searchKeywords(keyword)).done(function(a) {
						$('<div />').attr({'class':'notification'}).html(keyword + ' already exists.').prependTo($('#kwordAddBtn').parent()).delay(5000).fadeOut('slow', function() {
							$(this).remove();
						});
						
						$('#keywordsList').html(a);
					});
				}
			});
		} else {
			
		}
	});
});

function editKeyword(keyword, id) {
	return $.post(globals.ajaxurl + 'keywords.php', {_task:'edit', 'kword':keyword, 'id':id}, null, 'json');
}

function addKeyword(keyword) {
	return $.post(globals.ajaxurl + 'keywords.php', {'_task':'add', 'kword':keyword}, null, 'json');
}

function searchKeywords(keyword) {
	return $.post(globals.ajaxurl + 'keywords.php', {'_mode':'suggest', '_task':'html', 'q':keyword}, null, 'html');
}

function getKeyword(keyword) {
	return $.post(globals.ajaxurl + 'keywords.php', {'_mode':'get', '_task':'one', 'kword':keyword}, null, 'json');
}

function getKeywordById(id) {
	return $.post(globals.ajaxurl + 'keywords.php', {'_mode':'get', '_task':'byid', 'id':id}, null, 'json');
}

function keywordsList(pagenum) {
	$.when(getKeywords(pagenum)).done(function(gk) {
		$('#keywordsList').html(gk);
	});
}

function getKeywords(pagenum) {
	return $.get(globals.ajaxurl + 'keywords.php', {'pageNum':pagenum}, null, 'html');
}

function deleteKeyword(id) {
	return $.post(globals.ajaxurl + 'keywords.php', {
		_task:'del',
		'id':id
	}, null, 'json');
}

/*function keywordsList(pagenum) {
	$.when(getKeywords(pagenum)).done(function(gk) {
		$('#keywordsList').html(gk);
	});
}*/
