$(function() {
	$(document).on('change', '#userType', function(e) {
		var user_id = $(e.currentTarget).siblings('#userId').val();
		var user_type_id = $(e.currentTarget).val();
		
		$.when(updateUserType(user_id, user_type_id)).done(function(a) {
			$('<div />').attr({'class':'notification'}).text('Successfully updated user type.').prependTo($(e.currentTarget).parent()).delay(1500).fadeOut('slow', function() {
				$(this).remove();
			});
		});
	});
	
	$(document).on('click', '.rmvOrg', function(e) {
		var user_id = $(e.currentTarget).attr('data-user');
		var org_id = $(e.currentTarget).attr('data-org');
		
		$.when(removeUserFromOrg(user_id, org_id)).done(function(a) {
			$(e.currentTarget).parent().parent('tr').fadeOut('fast', function() {
				$(this).remove();
				
				$('<div />').attr({'class':'notification'}).text('Successfully removed user from organization.').prependTo('#orgTable').delay(2000).fadeOut('fast', function() {
					$(this).remove();
				});
			});
		});
	});
	
	$(document).on('click', '#btnAssignOrg', function(e) {
		var org_id = $('#assignOrg').val();
		var user_id = $('#assignOrg').attr('data-user');
		
		if(org_id != '') {
			$.when(assignUserToOrg(user_id, org_id)).done(function(a) {
				$('#userOrgList').html(a);
			});
		} else {
			alert('Please, select an organization');
		}
	});
});

function assignUserToOrg(user_id, org_id) {
	return $.post(globals.ajaxurl + 'users.php', {
		_mode:'org',
		_task:'add',
		'user_id':user_id,
		'org_id':org_id
	}, null, 'html');
}

function removeUserFromOrg(user_id, org_id) {
	return $.post(globals.ajaxurl + 'users.php', {
		'_mode':'org',
		'_task':'rmv',
		'user_id':user_id,
		'org_id':org_id
	}, null, 'json');
}

function updateUserType(user_id, type_id) {
	return $.post(globals.ajaxurl + 'users.php', {
		'_mode':'type',
		'_task':'edit',
		'user':user_id,
		'type':type_id
	}, null, 'json');
}