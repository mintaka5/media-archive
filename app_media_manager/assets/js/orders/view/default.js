$(function() {
        $('#notifierDialog').dialog({
            'autoOpen':false,
            'modal':true,
            'width':500,
            'height':250,
            'title':'Request notification'
        });
        
        $('#aNotifier').click(function() {
            $('#notifierDialog').dialog("open");
        });
        
        $('#sendNotification').click(function() {
            $.post(globals.ajaxurl+'order.php', {
            	'_mode':'notify', 
            	'order':$('#notificationOrder').val(), 
            	'msg':$('#txtNotification').val()
            }, function(d) {
            	$('<div />').attr({'class':'notification'}).html('Message sent!').prependTo('#notifierDialog').delay(1000).fadeOut('slow', function() {
            		$(this).remove();
            		
            		$('#txtNotification').html('');
            		
            		$('#notifierDialog').dialog("close");
            	});
            }, 'json');
        });
    
	$('.apprBtn').live('click', function() {
		var ele = this;
		var item = $(ele).siblings('input.itemID').val();
		
		$.post(globals.ajaxurl + 'lineitem.php', {'_mode':'approve', 'item':item}, function(d) {
			console.log($(ele).parent().parent().parent().siblings('.title').find('span.approval'));
			$(ele).parent().parent().parent().siblings('.title').find('span.approval').html(d.data.text).attr({'class':d.data.className+' approval'});
		}, 'json');
	});
	
	$('.unapprBtn').live('click', function() {
		var ele = this;
		var item = $(ele).siblings('input.itemID').val();
		
		$.post(globals.ajaxurl + 'lineitem.php', {'_mode':'approve', '_task':'no', 'item':item}, function(d) {
			console.log($(ele).parent().parent().parent().siblings('.title').find('span.approval'));
			$(ele).parent().parent().parent().siblings('.title').find('span.approval').html(d.data.text).attr({'class':d.data.className+' approval'});
		}, 'json');
	});
});