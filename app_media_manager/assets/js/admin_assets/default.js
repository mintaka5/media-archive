var adminAssets = {
    remove: function(assetId) {
        return $.post(globals.ajaxurl + 'asset.php', {
            _mode: 'del',
            aid: assetId
        }, null, 'json');
    },
    setSessionEdit: function(assetId, isEdit) {
        return $.post(globals.ajaxurl + 'asset.php', {
            _mode:'sess',
            'asset_id':assetId,
            'is_edit':isEdit
        }, null, 'json');
    },
    clearEditSession: function() {
        return $.post(globals.ajaxurl + 'asset.php', {
            _mode:'sess',
            _task:'clear'
        }, null, 'json');
    }
};

$(function() {
    $(document).on('click', '.rmvAsset', function(e) {
        e.preventDefault();

        var assetId = $(e.currentTarget).parent().siblings('.assetId').val();

        var confirm = window.confirm("Are you sure you want to delete this asset?");

        if(confirm == true) {
            $.when(adminAssets.remove(assetId)).done(function(a) {
                $(e.currentTarget).parent().parent().parent().parent().parent().fadeOut('fast', function() {
                    $(this).remove();
                });
            });
        }
    });

    $(document).on('change', '#org_id', function() {
        $('#limitOrgs').submit();
    });

    $(document).on('change', '.chkEditAsset', function(e) {
        var is_edit = 0;
        var asset_id = $(e.currentTarget).val();
        var group_id = $(e.currentTarget).attr('data-group');

        if($(e.currentTarget).is(':checked')) {
            is_edit = 1;
        }

        $.when(adminAssets.setSessionEdit(asset_id, is_edit)).done(function(a) {
            if(a.data.cur_assets.length == 1) {
                $('#editBatch').html('<a href="' + globals.relurl + '?_page=group&_mode=view&id=' + group_id + '">Edit selected assets</a> [<a href="#" id="clearEdits">Clear all</a>]');
            }

            if(a.data.cur_assets == null || a.data.cur_assets.length < 1) {
                $('#editBatch').empty();
            }
        });
    });

    $(document).on('click', '#clearEdits', function(e) {
        e.preventDefault();

        $.when(adminAssets.clearEditSession()).done(function(a) {
            $('#editBatch').empty();
            $('.chkEditAsset').removeAttr('checked');
        });
    });
});