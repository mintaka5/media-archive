$(function() {
    $('#apiKeyGen').click(function() {
        var ele = this;
        //var user = $('#userId').val();
        $.post(globals.ajaxurl+'account.php', {'_mode':'api', '_task':'gen'}, function(d) {
            $('#apiKey').html(d.data.apikey);
        }, 'json');
    });
});