var orgs = {
    saveTitle: function(org_id, title) {
        return $.post(globals.ajaxurl+'orgs.php', {
            _mode:'title',
            _task:'save',
            'title':title,
            'org_id':org_id
        }, null, 'json');
    },
    removeUser: function(user_id, org_id) {
        return $.post(globals.ajaxurl+'users.php', {
            _mode:'org',
            _task:'rmv',
            'user_id':user_id,
            'org_id':org_id
        }, null, 'json');
    }
};

$(function() {
    $('#orgTabs').tabs({
        select: function(evt, ui) {
            if(ui.panel.id === 'flickrTab') {
                
            }
        }
    });
    
    $(document).on('click', '#saveTitle', function(e) {
        var org_id = $(e.currentTarget).data('orgid');
        var title = $('#title').val();
        
        $.when(orgs.saveTitle(org_id, title)).done(function(a) {
            $('<div />').addClass('notification').html('Successfully updated title.').prependTo('#infoTab').delay(1500).fadeOut('slow', function() {
                $(this).remove();
            })
        });
    });
    
    $(document).on('click', '.userDel', function(e) {
        e.preventDefault();
        var user_id = $(e.currentTarget).data('id');
        var org_id = $(e.currentTarget).data('orgid');
        $.when(orgs.removeUser(user_id, org_id)).done(function(a) {
            $(e.currentTarget).parent().parent().parent().parent().remove();
        });
    });
});