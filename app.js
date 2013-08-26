var currentServer = 0;
var currentAction = 'ping';

function selectServer(id) {
    $('.command').html('Locked on ' + $('.server-' + id).text());
    currentServer = id;
    $('.server').removeClass('active');
    $('.server-' + id).addClass('active');
}

function quick(type) {
    post(currentServer, type);
}

function switchAction(action) {
    prevAction = currentAction;
    currentAction = action;
    if (currentAction == 'exactroute' && prevAction != 'exactroute') $('#form #fqdn').val('');
    if (currentAction != 'exactroute' && prevAction == 'exactroute') $('#form #fqdn').val('');
    if ($('#form #fqdn').val() !== "") post(currentServer, currentAction, $('#form #fqdn').val());
    if (currentAction == 'ping') $('#btnText').html('Ping');
    if (currentAction == 'trace') $('#btnText').html('Traceroute');
    if (currentAction == 'exactroute') {
        $('#btnText').html('BGP Route');
        $('#fqdn').attr('placeholder', 'Enter exact route, i.e. 1.2.3.4/24');
    } else $('#fqdn').attr('placeholder', 'hetmer.net');
}

function post(id, type, command) {
    $('.command').fadeOut();
    $.ajax({
        type: 'POST',
        url: './api.php',
        data: {
            id: id,
            type: type,
            command: command
        },
        dataType: 'json',
        timeout: 30000,
        context: $('body'),
        success: function(data) {
            if (data.error) // Apparently can't use REST.
            alert('Error: ' + data.error)
            else {
                $('.command').html(data.command).fadeIn();
                $('.result').prepend(data.result);
                $('.result').prepend('<h3>[' + $('.server-' + id).text() + '] ' + data.command + '</h3>');
            }
        },
        error: function(xhr, type) {
            alert('Failure in API.');
        }
    });
}
$(document).ready(function() {
    function init(d) {
        // fix html
        $('.name').html(d.name);
        $('.logo').attr("src", d.logo);
        $('a.name').attr("href", d.companyUrl);
        $('.version').html(d.version);
        $('.customText').html(d.customText);
        $.each(d.fqdn, function(index, item) {
            $('.routers').append('<li class="server server-' + index + '"><a href="javascript:selectServer(' + index + ')">' + item + '</a></li>');
        });
        selectServer(0);
    }
    // initializer
    $.ajax({
        type: 'GET',
        url: './api.php',
        dataType: 'json',
        timeout: 3000,
        context: $('body'),
        success: function(data) {
            if (data.error) // Apparently can't use REST.
            alert('Error: ' + data.error)
            init(data);
        },
        error: function(xhr, type) {
            alert('Failure in API.');
        }
    });
    $("#form").submit(function(event) {
        event.preventDefault();
        post(currentServer, currentAction, $('#form #fqdn').val());
    });
});