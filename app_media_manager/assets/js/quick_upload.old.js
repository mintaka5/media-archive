var $up = $.noConflict();

$up(function() {
	$up("#file_upload").uploadify({
		'uploader': globals.relurl + "assets/js/jq/jquery.uploadify-v2.1.4/uploadify.swf",
		'script': globals.relurl + "?_page=uploader",
		'scriptData': {'mid': $("#modifyId").val()},
		'cancelImg': globals.relurl + "assets/js/jq/jquery.uploadify-v2.1.4/cancel.png",
		'auto': false,
		'fileExt': '*.jpg;*.JPG;*.tif',
		'multi': true,
		'fileDataName':'upload',
		'removeCompleted':false,
		'onAllComplete': function(event, data) {
			$up.post(globals.relurl, {
				'_page': 'uploader',
				'_mode': 'new'
			}, function(d) {
				$up(".completed").remove();
				$up.post(globals.relurl, {
					'_page': 'new_images',
					'ids': d
				}, function(data) {
					$up("#newImagesList").html(data);
				}, "html");
			}, 'json');
		},
		'onComplete': function(event, ID, fileObj, response, data) {
			$up.post(globals.relurl, {
				'_mode':'store',
				'_page':'uploader',
				'id':response
			});
		}
	});

	$up("#uploaderLyr").height($(window).height() - $("#newImages").height());
	$up(window).resize(function() {
		$up("#uploaderLyr").height($(window).height() - $("#newImages").height());
	});
});

$(function() {
	$("#mainContent").height($(window).height() - $("#footContent").height());
	$(window).resize(function() {
		$("#mainContent").height($(window).height() - $("#footContent").height());
	});
	
	$(".newImage").live("click", function() {
		//console.log($(this).attr("id"));
		var imgId = $(this).attr("id");
		$.post(globals.ajaxurl + 'image.php', {
			'_mode': 'edit',
			'id': imgId
		}, function(data) {
			$("#editLyr").html(data);
		}, "html");
	});

	$("button[name='submitBtn']").live("click", function() {
		$.post(globals.ajaxurl + 'image.php', $("#editImageForm").serialize(), function(data) {
			$("#editLyr").html(data);
		}, "html");

		return false;
	});
});