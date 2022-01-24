$(function() {
	$('#categoryTree').bind('selected.jstree', function(evt, data) {
		console.log(data);
	}).tree({
		data: {
			type:'xml_flat',
			url: globals.ajaxurl + 'help_menu.php'
		},
		callback:{
			onselect:function(obj, tree) {
				var node = $(obj).attr('id');
				var node_splitter = node.split('_');
				var cat_id = node_splitter[1];
				
				console.log(cat_id);
				
				$.post(globals.ajaxurl+'help.php', {_mode:false, _task:false, cid:cat_id}, function(d) {
					$('#categoryList').html(d);
				}, 'html');
			}
		}
	});
});