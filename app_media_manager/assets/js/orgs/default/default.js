$(function() {
    $('#orgUserWin').dialog({
        modal: true,
        autoOpen: false,
        title: 'User Assignment',
        close: function(evt, ui) {
            $.when(listOrgs(1)).done(function(a) {
                $('#orgList').html(a);
            });
        },
        buttons: [
            {'text': 'OK', 'click': function() {
                    $('#orgUserWin').dialog('close');
                }},
            {'text': 'Cancel', 'click': function() {
                    $('#orgUserWin').dialog('close');
                }}
        ]
    });

    $.when(listOrgs(1)).done(function(a) {
        $('#orgList').html(a);
    });

    $(document).on('click', '.orgDel', function(e) {
        e.preventDefault();

        var org_id = $(e.currentTarget).attr('data-org');

        var sure = confirm("Are you sure that you want to permanently delete this organization/group?");

        if (sure) {
            $.when(deleteOrganization(org_id)).done(function(a) {
                $(e.currentTarget).parent().parent().parent().parent('tr').fadeOut('fast', function() {
                    $(this).remove();
                });
            });
        }
    });

    $(document).on('click', '#btnAddOrg', function(e) {
        processAddOrg();
    });

    $(document).on('keyup', '#orgTitle', function(e) {
        if (e.keyCode == 13) {
            processAddOrg();
        }
    });

    $(document).on('click', '.orgUsers', function(e) {
        e.preventDefault();

        var org_id = $(e.currentTarget).attr('data-org');
        $.when(getOrgUsers(org_id)).done(function(a) {
            $('#orgUserWin').html(a);

            $('#orgUserWin').dialog('open');
        });
    });

    $(document).on('click', '.rmvOrgUser', function(e) {
        e.preventDefault();

        var user_id = $(e.currentTarget).attr('data-user');
        var org_id = $(e.currentTarget).attr('data-org');
        var ele = e.currentTarget;

        $.when(unassignUserFromOrg(user_id, org_id)).done(function(a) {
            $(ele).parent().parent().fadeOut('fast', function() {
                $(this).remove();
            });
        });
    });

    $(document).on('keyup', '#findUsers', function(e) {
        if ($(e.currentTarget).val().length >= 2) {
            var org_id = $(e.currentTarget).attr('data-org');

            $.when(searchOrgUsers(org_id, $(e.currentTarget).val())).done(function(a) {
                $('#userResults').html(a);
            });
        }
    });

    $(document).on('click', '.assignUserToOrg', function(e) {
        var org_id = $(e.currentTarget).attr('data-org');
        var user_id = $(e.currentTarget).attr('data-user');

        $.when(assignUserToOrg(user_id, org_id)).done(function(a) {
            $('#findUsers').val('');

            $.when(getOrgUsersList(org_id)).done(function(b) {
                $('#ouList').html(b);
            });
        });
    });
});

function assignUserToOrg(user_id, org_id) {
    return $.post(globals.ajaxurl + 'orgs.php', {
        _mode: 'users',
        _task: 'add',
        'user_id': user_id,
        'org_id': org_id
    }, null, 'json');
}

function getOrgUsersList(org_id) {
    return $.get(globals.ajaxurl + 'orgs.php', {
        _mode: 'users',
        _task: 'list',
        'org_id': org_id
    }, null, 'html');
}

function searchOrgUsers(org_id, terms) {
    return $.post(globals.ajaxurl + 'orgs.php', {
        _mode: 'users',
        _task: 'find',
        'terms': terms,
        'org_id': org_id
    }, null, 'html');
}

function unassignUserFromOrg(user_id, org_id) {
    return $.post(globals.ajaxurl + 'orgs.php', {
        _mode: 'users',
        _task: 'rmv',
        'user_id': user_id,
        'org_id': org_id
    }, null, 'json')
}

function getOrgUsers(org_id) {
    return $.get(globals.ajaxurl + 'orgs.php', {
        _mode: 'users',
        'org_id': org_id
    }, null, 'html');
}

function processAddOrg() {
    var title = $('#orgTitle').val();

    if (title != '') {
        $.when(addOrganization(title)).done(function(a) {
            $('#orgTitle').val('');

            $.when(getOrgList(1)).done(function(b) {
                $('#orgList').html(b);
            });

            $('<div />').attr({'class': 'notification'}).html('Successfully added organization, ' + title).prependTo('#addOrgForm').delay(1500).fadeOut('slow', function() {
                $(this).remove();
            });
        });
    } else {
        alert('Please, provide a valid title in order to add a new organization.');
    }
}

function addOrganization(title) {
    return $.post(globals.ajaxurl + 'orgs.php', {
        _mode: 'add',
        'title': title
    }, null, 'json');
}

function listOrgs(num) {
    $.when(getOrgList(num)).done(function(a) {
        $('#orgList').html(a);
    });
}

function getOrgList(num) {
    return $.get(globals.ajaxurl + 'orgs.php', {'pageNum': num}, null, 'html');
}

function deleteOrganization(org_id) {
    return $.post(globals.ajaxurl + 'orgs.php', {
        _mode: 'del',
        'org_id': org_id
    }, null, 'json');
}