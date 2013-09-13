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

    if (currentAction == 'exactroute' && prevAction != 'exactroute') {
    	$('#form #fqdn').val('');
    }
    if (currentAction != 'exactroute' && prevAction == 'exactroute') {
    	$('#form #fqdn').val('');
    }

    if (currentAction == 'exactroute') {
        $('#btnText').html('BGP Route');
        $('#fqdn').attr('placeholder', 'Enter exact route, i.e. 83.208.0.0/16');
    } else {
    	$('#fqdn').attr('placeholder', 'ip address or domain name');
    }

	if (currentAction == 'ping') {
		$('#btnText').html('Ping');
	}

	if (currentAction == 'trace') {
		$('#btnText').html('Traceroute');
	}

	if($('#form #fqdn').val() != "") {
		post(currentServer, currentAction, $('#form #fqdn').val());
	}
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
        	if (data.error)
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

    selectServer(0);

    $("#form").submit(function(event) {
    	event.preventDefault();
    	post(currentServer, currentAction, $('#form #fqdn').val());
    });

});