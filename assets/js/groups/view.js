var sets = {
    getAllAssets: function (groupId) {
        return $.post(globals.ajaxurl + 'groupAssetsList.php', {
            _gid: groupId
        }, null, 'html');
    },
    getAllByGroup: function (groupId) {
        $.when(sets.getAllAssets(groupId)).done(function (a) {
            $('#grpAssetList').html(a);
            initAssetTips();
        });
    },
    setAssetDefault: function (assetId, groupId) {
        return $.post(globals.ajaxurl + 'groups.php', {
            _mode: 'asset',
            _task: 'default',
            _aid: assetId,
            _gid: groupId
        }, null, 'json');
    },
    groupAssetSearch: function (terms, groupId) {
        return $.post(globals.ajaxurl + 'groupAssetSearch.php', {
            term: terms,
            _gid: groupId
        }, null, 'html');
    },
    removeAssetFromGroup: function (assetId, groupId) {
        return $.post(globals.ajaxurl + 'groupAssetsList.php', {
            _mode: 'asset',
            _task: 'del',
            aid: assetId,
            gid: groupId
        }, null, 'html');
    },
    assignAssetToGroup: function (assetId, groupId) {
        return $.post(globals.ajaxurl + 'groups.php', {
            '_mode': 'asset',
            '_task': 'assign',
            '_gid': groupId,
            '_aid': assetId
        }, null, 'json');
    },
    suggestKeywords: function (terms) {
        return $.post(globals.ajaxurl + 'keywords.php', {
            _mode: 'suggest',
            'q': terms
        }, null, 'json');


    },
    assignKeywordToGroup: function (keyword, groupId) {
        return $.post(globals.ajaxurl + 'groups.php', {
            _mode: 'keywords',
            _task: 'add',
            kword: keyword,
            gid: groupId
        }, null, 'json');
    },
    removeKeywordFromGroup: function (keyword, groupId) {
        return $.post(globals.ajaxurl + 'groups.php', {
            _mode: 'keywords',
            _task: 'rmv',
            kword: kword,
            gid: $.url.param('id')
        }, null, 'json');
    },
    deactivateStatus: function(groupId) {
        return $.post(globals.ajaxurl + 'batch.php', {
            '_mode': 'status',
            '_stat': 0,
            '_gid': groupId
        }, null, 'json');
    },
    activateStatus: function(groupId) {

    },
    getLocations:function(groupId) {
        return $.post(globals.ajaxurl + 'groups.php', {
            _mode: 'loc',
            _task: 'all',
            group_id: groupId
        }, null, 'json');
    },
    getSets: function() {
        return null;
    }
};

$(function () {
    $('#manageOrgsWin').dialog({
        'title': 'Organization Management',
        'width': 400,
        autoOpen: false,
        modal: true,
        buttons: [
            {
                'text': 'OK', 'click': function () {
                $('#manageOrgsWin').dialog('close');
            }
            },
            {
                'text': 'Cancel', 'click': function () {
                $('#manageOrgsWin').dialog('close');
            }
            }
        ],
        close: function (evt, ui) {
            var group_id = $('#manageOrgs').attr('data-group');

            $.when(getGroupOrgsJson(group_id)).done(function (a) {
                $('#grpOrgStr').text(a.data);
            });
        }
    });

    $('#grpAddImages').dialog({
        'title': 'Add Existing Images',
        'autoOpen': false,
        'modal': true,
        'resizable': false,
        'width': 458
    });

    $(document).on('click', '#aAddCurImages', function (e) {
        $('#grpAddImages').dialog('open');
    });

    /**
     * Gather all assets for the set
     */
    sets.getAllByGroup($.url.param('id'));

    /**
     * Make an asset the default for the set
     */
    $(document).on('change', '.makeDefault', function (e) {
        var ele = $(this).filter(":checked");

        $.when(sets.setAssetDefault($(ele).siblings(".rmvFromGrp").attr('id'), $.url.param('id'))).done(function (a) {
            sets.getAllByGroup($.url.param('id'));
        });
    })

    $("#grpTitle").editable(globals.ajaxurl + 'groups.php', {
        'submitdata': {
            '_mode': 'edit',
            '_task': 'title',
            '_id': $.url.param('id')
        }, 'callback': function (val, set) {
            $(this).html(val);
        },
        'tooltip': 'Click to edit title...',
        'indicator': 'Saving...',
        'submit': 'Save',
        'cancel': 'Cancel',
        'style': 'display:inline',
        'height': 20,
        'width': 150
    });

    $(document).on('click', '.rmvFromGrp', function (e) {
        var assetId = $(this).attr('id');
        var groupId = $.url.param('id');

        $.when(sets.removeAssetFromGroup(assetId, groupId)).done(function (a) {
            sets.getAllByGroup(groupId);
        });
    });

    $("#existingAstQry").autocomplete({
        'source': function () {
        },
        'minLength': 1,
        'search': function (evt, ui) {
            $("#existingAsts").html("");

            $.when(sets.groupAssetSearch($(evt.target).val(), $.url.param('id'))).done(function (a) {
                $("#existingAsts").html(a);
            });

            return false;
        }
    });

    $(document).on('click', '.selectAssetForGrp', function (e) {
        var ele = this;

        $.when(sets.assignAssetToGroup($(ele).children("input.selectAssetId").val(), $.url.param('id'))).done(function (a) {
            $("#existingAstQry").val("");
            $(ele).remove();

            sets.getAllByGroup($.url.param('id'));

            $('#grpAddImages').dialog('close');
        });
    });

    $('#assetWords').tagit({
        'allowSpaces': true,
        'minLength': 2,
        'tagSource': function (req, res) {
            $.when(sets.suggestKeywords(req.term)).done(function (a) {
                res($.map(d.data, function (item) {
                    return {
                        'label': item.keyword,
                        'value': item.keyword
                    };
                }));
            });
        },
        'onTagAdded': function (evt, tag) {
            var kword = $(tag).find('span.tagit-label').text();
            $.when(sets.assignKeywordToGroup(kword, $.url.param('id'))).done(function (a) {});
        },
        'onTagRemoved': function (evt, tag) {
            var kword = $(tag).find('span.tagit-label').text();
            $.when(sets.removeKeywordFromGroup(kword, $.url.param('id'))).done(function (a) {});
        }
    });

    $(document).on('keydown', '#txtNewKword', function(e) {
        if (e.which == 13) {procNewKeyword($(this).val());}
    });

    $(document).on('click', '#btnNewKword', function(e) {
        procNewKeyword($('#txtNewKword').val());
    });

    $(document).on('click', '#statBatchDe', function(e) {
        $.when(sets.deactivateStatus($.url.param('id'))).done(function(a) {});
    });

    $(document).on('click', '#statBatchAct', function(e) {
        $.when(sets.activateStatus($.url.param('id'))).done(function(a) {});
    });

    $('#grpTabs').tabs({
        show: function (e, ui) {}
    });

    // add photographer form validation
    var validAddPhotog = $('#addPhotogForm').validate();
    $(document).on('click', '#addPhotogBtn', function () {
        if ($('#addPhotogForm').valid()) {
            $.post(globals.ajaxurl + 'photographer.php', $('#addPhotogForm').serializeJSON(), function (d) {
                $('#spanGrpPhotog').html(d.data.addPhotogFname + ' ' + d.data.addPhotogLname);

                $('#dialogPhotog').dialog('close');

                $('#addPhotogForm').find('input').val('');

                validAddPhotog.resetForm();
            }, 'json');
        }
    });

    // shoot settings dialog
    $('#shootWin').dialog({
        'title': 'Photo Shoot Assignment',
        autoOpen: false,
        modal: true,
        'width': 400,
        buttons: [
            {
                'text': 'Cancel', 'click': function () {
                $(this).dialog('close');
            }
            }
        ]
    });

    var shootFormValid = $('#shootForm').validate();

    $('#shootTabs').tabs({
        'select': function (evt, ui) {
            if ($(ui.tab).attr('href') === '#selectShootTab') {
                $('#shootsList').load(globals.ajaxurl + 'shoots.php', {_mode: 'list'});
            }
        }
    });

    $(document).on('click', '#assignShoot', function () {
        $('#shootWin').dialog('open');
    });

    $('#shootDate').datepicker({
        changeMonth: true,
        changeYear: true,
        yearRange: '2000:' + maxDate.getFullYear()
    });

    $(document).on('click', '#addShootBtn', function () {
        if ($('#shootForm').valid()) {
            $.post(globals.ajaxurl + 'shoots.php', $('#shootForm').serializeJSON(), function (d) {
                $('#shootNameTxt').html(d.data.shootTitle);

                $('#shootWin').dialog('close');

                shootFormValid.resetForm();
            }, 'json');
        }
    });

    $(document).on('click', '.selectShoot', function (e) {
        e.preventDefault();

        var shootId = $(e.currentTarget).siblings('.shootId').val();

        $.when(assignShoot($.url.param('id'), shootId)).done(function (a) {
            $('#shootNameTxt').html(a.data);

            var note = $('#shootNameTxt').parent().parent();
            $('<div />').attr({'class': 'notification'}).html('Successfully assigned shoot.').prependTo(note).delay(1500).fadeOut('slow', function () {
                $(this).remove();
            });

            $('#shootWin').dialog('close');
        });
    });

    // photographer settings dialog
    $('#dialogPhotog').dialog({
        'title': 'Photographer Assignment',
        'width': 400,
        autoOpen: false,
        modal: true,
        buttons: [
            {
                'text': 'Cancel',
                'click': function () {
                    $(this).dialog('close');
                }
            }
        ]
    });

    $(document).on('click', '#addPhotogBtn', function () {

    });

    $('#photogTabs').tabs({
        select: function (evt, ui) {
            if ($(ui.tab).attr('href') === '#selectPhotogTab') {
                $('#photogsList').load(globals.ajaxurl + 'photographer.php', {_mode: 'all'});
            }
        }
    });

    $(document).on('click', '#aGrpPhotog', function () {
        $('#dialogPhotog').dialog('open');
    });

    $(document).on('click', '.selectPhotog', function () {
        var gid = $.url.param('id');
        var pid = $(this).siblings('.photogId').val();

        $.post(globals.ajaxurl + 'photographer.php', {
            _mode: 'group',
            _task: 'assign',
            _gid: gid,
            _id: pid
        }, function (d) {
            $('#spanGrpPhotog').html(d.data.firstname + ' ' + d.data.lastname);

            $('#dialogPhotog').dialog('close');
        }, 'json');
    });

    $('#chkOuttake').change(function () {
        if ($(this).is(':checked')) {
            $.post(globals.ajaxurl + 'groups.php', {
                '_mode': 'outtake',
                '_task': 'yes',
                'gid': $.url.param('id')
            }, function (d) {
                $('#remOuttake').remove();
                $('<div />').attr({
                    'id': 'sucOuttake',
                    'class': 'notification'
                }).html('Outtake restriction added').appendTo($('#chkOuttake').parent());
            }, 'json');
        } else {
            $.post(globals.ajaxurl + 'groups.php', {
                '_mode': 'outtake',
                '_task': 'no',
                'gid': $.url.param('id')
            }, function (d) {
                $('#sucOuttake').remove();
                $('<div />').attr({
                    'id': 'remOuttake',
                    'class': 'notification'
                }).html('Outtake restriction removed').appendTo($('#chkOuttake').parent());
            }, 'json');
        }
    });

    $('#chkSelect').change(function () {
        if ($(this).is(':checked')) {
            $.post(globals.ajaxurl + 'groups.php', {
                '_mode': 'select',
                '_task': 'yes',
                'gid': $.url.param('id')
            }, function (d) {
                $('#remSelect').remove();
                $('<div />').attr({
                    'id': 'sucSelect',
                    'class': 'notification'
                }).html('Select restriction added').appendTo($('#chkSelect').parent());
            }, 'json');
        } else {
            $.post(globals.ajaxurl + 'groups.php', {
                '_mode': 'select',
                '_task': 'no',
                'gid': $.url.param('id')
            }, function (d) {
                $('#sucSelect').remove();
                $('<div />').attr({
                    'id': 'remSelect',
                    'class': 'notification'
                }).html('Select restriction removed').appendTo($('#chkSelect').parent());
            }, 'json');
        }
    });

    $('#txtPubName').blur(function () {
        procPubRestriction($(this).val(), $('#datePublished').val());
    });

    $('#txtPubName').autocomplete({
        'source': function (req, res) {
            $.post(globals.ajaxurl + 'pubs.php', {'_mode': 'search', 'query': req.term}, function (d) {
                res($.map(d.data, function (item) {
                    return {'label': item.title, 'value': item.id};
                }));
            }, 'json');
        },
        'focus': function (evt, ui) {
        },
        'minLength': 2,
        'select': function (evt, ui) {
            $('#hdnPubId').val(ui.item.value);
            $('#txtPubName').val(ui.item.label);

            return false;
        }
    });

    $('#datePublished').datepicker({
        changeMonth: true,
        changeYear: true,
        yearRange: '2000:' + maxDate.getFullYear(),
        'onSelect': function (date, obj) {
            procPubRestriction($('#txtPubName').val(), date);
        }
    });

    $('#chkPublished').change(function () {
    });

    $(document).on('click', '.delAsset', function () {
        var ele = this;

        $.post(globals.ajaxurl + 'asset.php', {'_mode': 'del', 'aid': $(ele).siblings('.assetId').val()}, function (d) {
            $(ele).parent().parent().fadeOut('slow', function () {
                $("#grpAssetList").load(globals.ajaxurl + 'groupAssetsList.php', {'_gid': $.url.param('id')}, function () {
                    initAssetTips();
                });
            });
        }, 'json');
    });

    /**
     * adds a new keyowrd to the database,
     * and will assign it to the group's assets
     */
    function procNewKeyword(keyword) {
        if (keyword != "") {
            $.post(globals.ajaxurl + 'groups.php', {
                '_mode': 'keywords',
                '_task': 'new',
                'kword': keyword,
                'gid': $.url.param('id')
            }, function (d) {
                $('#txtNewKword').val("");
                $('#assetWords').tagit("createTag", keyword);
            }, 'json');
        }
    }

    $('#activityStatus').click(function () {
        if ($('#assetsStatus').is(':checked') == false) {
            alert('In order to make the group public, please, set at least one of its assets to be publicly available.');
            $(this).prop('checked', false);
            return false;
        }

        var ele = this;

        var groupId = $.url.param('id');

        var isChecked = $(this).is(':checked');

        if (isChecked == false) {
            $.post(globals.ajaxurl + 'groups.php', {
                '_mode': 'appr',
                '_task': 'no',
                'group': $.url.param('id')
            }, function (d) {
                $(ele).siblings('#apprvStatus').html('Private');

                // unset group being featured if it is set
                var featureIsChecked = $('#featureStatus').is(':checked');
                if (featureIsChecked == true) {
                    $.post(globals.ajaxurl + 'groups.php', {
                        _mode: 'featured',
                        _task: 'no',
                        group: groupId
                    }, function (d) {
                        $('#txtFeatureStatus').html('Not featured');

                        $('#featureStatus').prop('checked', false);
                    }, 'json');
                }
            }, 'json');

        } else {
            // check to see if there is a default image before making group public
            $.post(globals.ajaxurl + 'groups.php', {
                '_mode': 'has',
                '_task': 'default',
                'group': $.url.param('id')
            }, function (d) {
                if (d.status == true) {
                    $.post(globals.ajaxurl + 'groups.php', {
                        '_mode': 'appr',
                        '_task': 'yes',
                        'group': $.url.param('id')
                    }, function (d) {
                        $(ele).siblings('#apprvStatus').html('Public');
                    }, 'json');
                } else {
                    alert('Please, select a default image, before making this group public!');
                }
            }, 'json');
        }
    });

    $('#delGrp').click(function () {
        var conf = confirm('Are you sure you want to delete this group?');
        if (conf == true) {
            $.post(globals.ajaxurl + 'groups.php', {'_mode': 'del', '_id': $.url.param('id')}, function (d) {
                window.location.href = globals.relurl + '?_page=index';
            }, 'json');
        }
    });

    $(document).on('click', '#assetsStatus', function () {
        var isChecked = $(this).is(':checked');
        var ele = this;

        if (isChecked == true) {
            $.post(globals.ajaxurl + 'groups.php', {
                '_mode': 'asset',
                '_task': 'activity',
                'public': 'yes',
                'group': $.url.param('id')
            }, function (d) {
                $('<div />').attr({
                    'class': 'notification',
                    'id': 'confirmActivity'
                }).html('Assets are now all public.').prependTo($(ele).parent());
                setTimeout(function () {
                    $('#confirmActivity').fadeOut(500, function () {
                        $(this).remove();
                    });
                }, 2000);
            }, 'json');
        } else {
            $.post(globals.ajaxurl + 'groups.php', {
                '_mode': 'asset',
                '_task': 'activity',
                'public': 'no',
                'group': $.url.param('id')
            }, function (d) {
                $('<div />').attr({
                    'class': 'notification',
                    'id': 'confirmActivity'
                }).html('Assets are now all private.').prependTo($(ele).parent());
                setTimeout(function () {
                    $('#confirmActivity').fadeOut(500, function () {
                        $(this).remove();
                    });
                }, 2000);
            }, 'json');
        }
    });

    $(document).on('click', '#featureStatus', function () {
        if ($('#activityStatus').is(':checked') == false) {
            alert('Group must be approved for the public, in order to be featured.');

            $(this).prop('checked', false);

            return false;
        }

        var groupId = $.url.param('id');

        var isChecked = $(this).is(":checked");

        if (isChecked == true) {
            $.post(globals.ajaxurl + 'groups.php', {_mode: 'featured', _task: 'yes', group: groupId}, function (d) {
                $('#txtFeatureStatus').html('Featured');
            }, 'json');
        } else {
            $.post(globals.ajaxurl + 'groups.php', {_mode: 'featured', _task: 'no', group: groupId}, function (d) {
                $('#txtFeatureStatus').html('Not featured');
            }, 'json');
        }
    });

    /**
     * Handles adding a published status to a group of assets
     *
     * @param pub_name
     * @param date
     */
    function procPubRestriction(pub_name, date) {
        if (pub_name != "" && date != "") {
            $.post(globals.ajaxurl + 'groups.php', {
                '_mode': 'pubd',
                '_task': 'yes',
                'gid': $.url.param('id'),
                'pub_name': pub_name,
                'date': date
            }, function (d) {
                $('#chkPublished').attr({'checked': 'checked'});
                $('#remPubd').remove();
                $('<div />').attr({
                    'id': 'sucPubd',
                    'class': 'notification'
                }).html('Set assets to publish status.').appendTo($('#chkPublished').parent());
                setTimeout(function () {
                    $('#sucPubd').fadeOut(500, function () {
                        $(this).remove();
                    });
                }, 2000);
            }, 'json');
        } else {
            $.post(globals.ajaxurl + 'groups.php', {
                '_mode': 'pubd',
                '_task': 'no',
                'gid': $.url.param('id')
            }, function (d) {
                $('#chkPublished').removeAttr('checked');
                $('#sucPubd').remove();
                $('<div />').attr({
                    'id': 'remPubd',
                    'class': 'notification'
                }).html('Removed publish status.').appendTo($('#chkPublished').parent());
                setTimeout(function () {
                    $('#remPubd').fadeOut(500, function () {
                        $(this).remove();
                    });
                }, 2000);
            }, 'json');
        }
    }

    /**
     * Group's image uploader
     */
    $('#uploader').pluploadQueue({
        runtimes: 'gears,html5,flash,silverlight,browserplus',
        unique_names: true,
        max_file_size: '50mb',
        resize: {},
        url: globals.relurl + '?_page=uploader&_mode=grp',
        flash_swf_url: globals.relurl + 'assets/js/plupload/js/plupload.flash.swf',
        silverlight_xap_url: globals.relurl + 'assets/js/plupload/js/plupload.silverlight.xap',
        filters: [{title: 'Image files', extensions: 'jpg,tif'}],
        multipart: true,
        multipart_params: {'uid': uid, 'gid': gid},
        init: {
            FileUploaded: function (up, file, info) {
                $("#grpAssetList").load(globals.ajaxurl + 'groupAssetsList.php', {'_gid': $.url.param('id')}, function () {
                    initAssetTips();
                });
            }
        }
    });

    /**
     * end image uploader
     */

    $(document).on('click', '#saveMeta', function (e) {
        e.preventDefault();

        $('#infoTab').block({message: 'Saving metadata...'});

        var group_id = $(e.currentTarget).attr('data-id');

        $.when(saveGroupMetadata(group_id)).done(function (a) {
            $('#infoTab').unblock();

            $('<div />').attr({'class': 'notification'}).html('Successfully update metadata for all images.').prependTo('#infoTab').delay(1500).fadeOut('slow', function () {
                $(this).remove();
            });
        });
    });

    $(document).on('click', '#manageOrgs', function (e) {
        e.preventDefault();

        var group_id = $(e.currentTarget).attr('data-group');

        $.when(getGroupOrgs(group_id)).done(function (a) {
            $('#manageOrgsList').html(a);

            $('#manageOrgsWin').dialog('open');
        });
    });

    $(document).on('click', '.rmvOrgFromGrp', function (e) {
        e.preventDefault();

        var ele = $(e.currentTarget);

        var group_id = $(e.currentTarget).attr('data-group');
        var org_id = $(e.currentTarget).attr('data-org');

        $.when(removeGroupFromOrg(group_id, org_id)).done(function (a) {
            $(ele).parent().parent().fadeOut('fast', function () {
                $(this).remove();
            });
        });
    });

    $(document).on('keyup', '#findOrgs', function (e) {
        var terms = $(e.currentTarget).val();
        var group_id = $(e.currentTarget).attr('data-group');

        if (terms.length > 1) {
            $.when(searchOrgs(terms, group_id)).done(function (a) {
                $('#orgResults').html(a);
            });
        }
    });

    $(document).on('click', '.addOrgToGrp', function (e) {
        e.preventDefault();

        var ele = $(e.currentTarget);

        var org_id = $(e.currentTarget).attr('data-org');
        var group_id = $(e.currentTarget).attr('data-group');

        $.when(assignGroupToOrg(org_id, group_id)).done(function (a) {
            $(ele).parent().parent().fadeOut('fast', function () {
                $(this).remove();
            });

            $.when(getGroupOrgs(group_id)).done(function (b) {
                $('#manageOrgsList').html(b);
                //$('#orgResults').html('');
                $('findOrgs').val('');
            });
        });
    });

    $(document).on('change', '#imgRights', function (e) {
        var group_id = $(e.currentTarget).attr('data-group');
        var rights_id = $(e.currentTarget).val();

        $.when(setGroupRights(group_id, rights_id)).done(function () {
            $('<div />').attr('class', 'notification').html("successfully updated group's asset rights").prependTo($(e.currentTarget).parent().parent()).delay(1500).fadeOut('slow', function () {
                $(this).remove();
            });
        });
    });

    $(document).on('click', '.reassignAssets', function (e) {
        e.preventDefault();

        var org_id = $(e.currentTarget).attr('data-org');
        var group_id = $(e.currentTarget).attr('data-group');

        $.when(reassignGroupAssetOrgs(org_id, group_id)).done(function (a) {
            $('<div />').addClass('notification').html('Successfully assigned all group assets to the selected organization.').prependTo('#manageOrgsWin').delay(1500).fadeOut('fast', function () {
                $(this).remove();
            });
        });
    });

    /**
     * if set assignment tab is available grab available sets
     */
    if ($('#setsTab').is('*')) {
        $.when(sets.getSets()).done(function (a) {});
    }
});

function reassignGroupAssetOrgs(org_id, group_id) {
    return $.post(globals.ajaxurl + 'groups.php', {
        _mode: 'orgs',
        _task: 'assets',
        'org_id': org_id,
        'group_id': group_id
    });
}

function setGroupRights(group_id, rights_id) {
    return $.post(globals.ajaxurl + 'groups.php', {
        _mode: 'rights',
        _task: 'change',
        'group_id': group_id,
        'rights_id': rights_id
    }, null, 'json');
}

function assignGroupToOrg(org_id, group_id) {
    return $.post(globals.ajaxurl + 'groups.php', {
        _mode: 'orgs',
        _task: 'add',
        'org_id': org_id,
        'group_id': group_id
    }, null, 'json');
}

function searchOrgs(terms, group_id) {
    return $.post(globals.ajaxurl + 'groups.php', {
        _mode: 'orgs',
        _task: 'find',
        'terms': terms,
        'group_id': group_id
    }, null, 'html');
}

function removeGroupFromOrg(group_id, org_id) {
    return $.post(globals.ajaxurl + 'groups.php', {
        _mode: 'orgs',
        _task: 'del',
        'org_id': org_id,
        'group_id': group_id
    }, null, 'json');
}

function getGroupOrgs(group_id) {
    return $.get(globals.ajaxurl + 'groups.php', {
        _mode: 'orgs',
        _task: 'get',
        'group_id': group_id
    }, null, 'html');
}

function saveGroupMetadata(group_id) {
    return $.post(globals.ajaxurl + 'groups.php', {
        _mode: 'meta',
        _task: 'update',
        'group_id': group_id
    }, null, 'json');
}

function initAssetTips() {
    $('.aPreview').qtip({
        'position': {'at': 'top right'},
        'style': {'width': '500px'},
        'show': {
            'delay': 500, 'solo': true, 'effect': function (offset) {
                $(this).fadeIn(750);
            }
        },
        'hide': {
            'delay': 1000, 'effect': function (offset) {
                $(this).fadeOut(500);
            }
        },
        'content': {
            'text': 'loading&#133;',
            'ajax': {
                'url': globals.ajaxurl + 'asset.php',
                'data': {'_mode': 'tip', 'aid': ''}
            }
        },
        'events': {
            'focus': function (evt, api) {
                $.post(globals.ajaxurl + 'asset.php', {
                    '_mode': 'tooltip', 'aid': $(evt.originalEvent.currentTarget).siblings('input.assetId').val()
                }, function (d) {
                    api.set('content.text', d);
                }, 'html');
            }
        }
    });
}

function assignShoot(group_id, shoot_id) {
    return $.post(globals.ajaxurl + 'shoots.php', {
        _mode: 'group',
        _task: 'assign',
        gid: group_id,
        sid: shoot_id
    }, null, 'json');
}

function getGroupOrgsJson(group_id) {
    return $.get(globals.ajaxurl + 'groups.php', {
        _mode: 'orgs',
        _task: 'list',
        'group_id': group_id
    }, null, 'json');
}