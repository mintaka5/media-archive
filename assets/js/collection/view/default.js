var collection = {
	ajaxurl:globals.ajaxurl + 'collection.php',
	listGroups:function(container_id) {
		return $.get(this.ajaxurl, {
			_mode:'list',
			_task:'groups',
			'container_id':container_id
		}, null, 'html');
	},
	saveTitle:function(container_id, title) {
		if(title != '') {
			$('#saveCollTitle').attr('disabled', 'disabled');
			
			$.when(this.updateTitle(container_id, title)).done(function(a) {
				$('<div />').attr('class', 'notification').html('Sucessfully updated collection title.').prependTo('#infoTab').delay(1500).fadeOut('slow', function() {
					$(this).remove();
					
					$('#saveCollTitle').removeAttr('disabled');
				});
			});
		}
	},
	updateTitle:function(container_id, title) {
		//console.log(container_id);
		return $.post(this.ajaxurl, {
			_mode:'title',
			_task:'update',
			'container_id':container_id,
			'title':title
		}, null, 'json');
	},
	updateDesc:function(container_id, desc) {
		return $.post(this.ajaxurl, {
			_mode:'desc',
			_task:'update',
			'container_id':container_id,
			'desc':desc
		}, null, 'json');
	},
	getAllSets:function(container_id) {
		return $.get(this.ajaxurl, {
			_mode:'sets',
			_task:'all',
			'container_id':container_id
		}, null, 'html');
	},
	searchSets:function(terms, container_id) {
		return $.post(this.ajaxurl, {
			_mode:'sets',
			_task:'search',
			'terms':terms,
			'container_id':container_id
		}, null, 'html');
	},
	addSet:function(container_id, group_id) {
		return $.post(this.ajaxurl, {
			_mode:'sets',
			_task:'add',
			'container_id':container_id,
			'group_id':group_id
		}, null, 'json');
	},
	removeSet:function(container_id, group_id) {
		return $.post(this.ajaxurl, {
			_mode:'sets',
			_task:'remove',
			'container_id':container_id,
			'group_id':group_id
		}, null, 'json');
	},
	updateApproval: function(container_id, appr) {
		return $.post(this.ajaxurl, {
			_mode:'approval',
			'container_id':container_id,
			'appr':(appr == true) ? 1 : 0
		}, null, 'json');
	}
};

$(function() {
	$.when(collection.listGroups($('#groupsList').attr('data-id'))).done(function(a) {
		$('#groupsList').html(a);
	});
	
	$('#collTabs').tabs();
	
	$(document).on('click', '#saveCollTitle', function(e) {
		collection.saveTitle($('#colltitle').attr('data-id'), $('#colltitle').val());
	});
	
	$(document).on('keyup', '#colltitle', function(e) {
		if(e.keyCode == 13) {
			collection.saveTitle($('#colltitle').attr('data-id'), $('#colltitle').val());
		}
	});
	
	$(document).on('click', '#saveCollDesc', function(e) {
		var container_id = $(e.currentTarget).siblings('.descArea').attr('data-id');
		var desc = $(e.currentTarget).siblings('.descArea').val();
		
		$(e.currentTarget).attr('disabled', 'disabled');
		
		$.when(collection.updateDesc(container_id, desc)).done(function(a) {
			$('<div />').attr('class', 'notification').html('Sucessfully updated collection description.').prependTo('#infoTab').delay(1500).fadeOut('slow', function() {
				$(this).remove();
				
				$(e.currentTarget).removeAttr('disabled');
			});
		});
	});
	
	$.when(collection.getAllSets($('#container_id').val())).done(function(a) {
		$('#setsToAdd').html(a);
	});
	
	$('#setSearch').autocomplete({
		minLength:1,
		source:function(request, response) {
			$.when(collection.searchSets(request.term, $('#container_id').val())).done(function(a) {
				$('#setsToAdd').html(a);
			});
		}
	});
	
	$(document).on('click', '#btnSetSearch', function(e) {
		if($('#setSearch').val() != '') {
			$.when(collection.searchSets($('#setSearch').val(), $('#container_id').val())).done(function(a) {
				$('#setsToAdd').html(a);
			});
		}
	});
	
	$(document).on('click', '.collSetToAdd', function(e) {
		var container_id = $(e.currentTarget).attr('data-container');
		var group_id = $(e.currentTarget).attr('data-group');
		
		$.when(collection.addSet($('#container_id').val(), group_id)).done(function(a) {
			$.when(collection.listGroups($('#container_id').val())).done(function(b) {
				$('#groupsList').html(b);
			});
			
			$(e.currentTarget).fadeOut('fast', function() {
				$(this).remove();
			});
		});
	});
	
	$(document).on('click', '.rmvFromColl', function(e) {
		e.preventDefault();
		
		var container_id = $(e.currentTarget).attr('data-container');
		var group_id = $(e.currentTarget).attr('data-group');
		
		$.when(collection.removeSet(container_id, group_id)).done(function(a) {
			$(e.currentTarget).parent().parent().fadeOut('fast', function() {
				$(this).remove();
				
				$.when(collection.getAllSets($('#container_id').val())).done(function(a) {
					$('#setsToAdd').html(a);
				});
			});
		});
	});
	
	$(document).on('change', '#chxCollAppr', function(e) {
		var container_id = $(e.currentTarget).val();
		
		var isChecked = $(e.currentTarget).is(':checked');
		
		$.when(collection.updateApproval(container_id, isChecked)).done(function(a) {
			$('#spanCollAppr').html(a.data.status);
		});
	});
});