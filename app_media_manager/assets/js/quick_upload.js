var uploader;
var uppy = {
    addToSession: function(asset_id) {
        return $.post(globals.ajaxurl + 'asset.php', {
		_mode:'sess',
		'asset_id':asset_id,
		'is_edit':1
	}, null, 'json');
    },
    current_group:false
};

$(function() {
	var pu = $('#uploader').pluploadQueue({
		runtimes: 'gears,html5,flash,silverlight,browserplus',
		unique_names:true,
		max_file_size:'50mb',
		resize: {},
		url: globals.relurl+'?_page=uploader',
		//flash_swf_url:globals.relurl + 'assets/js/plupload/js/plupload.flash.swf',
        flash_swf_url: globals.relurl + 'assets/js/plupload-2.1.4/js/Moxie.swf',
		//silverlight_xap_url:globals.relurl + 'assets/js/plupload/js/plupload.silverlight.xap',
        silverlight_xap_url: globals.relurl + 'assets/js/plupload-2.1.4/js/Moxie.xap',
		filters: [{title: 'Image files', extensions:'jpg,tif'}],
		multipart: true,
		multipart_params: {'uid':uid, 'orgs':gatherUserOrgs('.chkUserOrg')},
                init: {
                    FileUploaded: function(up, file, info) {
                        // grab new upload's asset ID
                        var asset_id = $.parseJSON(info.response).id;
                        // add the ID to the session for batch editing
                        $.when(uppy.addToSession(asset_id)).done(function(a) {
                            //console.log(a);
                            // set current batch set ID
                            if(a.status == true && uppy.current_group == false) {
                                uppy.current_group = a.data.temp_group;
                            }
                        });
                    },
                    UploadComplete: function(up, files) {
                        //console.log(files);
                        // when entire upload is complete
                        // redirect to new batch set
                        if(uppy.current_group != false) {
                            window.location.href = globals.relurl + '?_page=group&_mode=view&id=' + uppy.current_group;
                        }
                    }
                }
	});
	
        /**
         * rechecks the user org list based on user selecting/deselecting asset assignments
         */
	$(document).on('change', '.chkUserOrg', function(e) {
		var up = $('#uploader').pluploadQueue();
		up.settings.multipart_params.orgs = gatherUserOrgs('.chkUserOrg');
	});
	
	$('#uploaderForm').submit(function(e) {
		var uploader = $('#uploader').pluploadQueue();
		
		if(uploader.files.length > 0) {
			uploader.bind('StateChanged', function() {
				if(uploader.files.length === (uploader.total.uploaded + uploader.total.failed)) {
					$('#uploaderForm').submit();
				}
			});
			
			uploader.start();
		} else {
			alert('You must queue at least one file.');
		}
		
		return false;
	});
});

function gatherUserOrgs(class_name) {
	var user_orgs = new Array();
	
	$(class_name).each(function(i, v) {
		if($(v).is(':checked')) {
			user_orgs.push($(v).val());
		}
	});
	
	return user_orgs;
}