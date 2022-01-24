$(function() {
    $('.downloadBtn').live('click', function() {
       var ele = this;
       
       window.location.href = globals.relurl+"?_page=account&_mode=orders&_task=download&id="+$(ele).siblings('.orderId').val();
    });
    
    $('.deleteBtn').live('click', function() {
        var ele = this;
        var order = $(ele).siblings('.orderId').val();
        
        var conf = confirm('Are you sure you want to permanently delete this order?');
        
        if(conf == true) {
            $.post(globals.ajaxurl+'order.php', {'_mode':'del', 'order':order}, function(d) {
                $(ele).parent().parent('tr').fadeOut(1000, function() {
                    $(this).remove();
                });
            }, 'json');
        }
    });
});