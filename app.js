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

    if (currentAction == 'route' && prevAction != 'route') {
    	$('#form #fqdn').val('');
    }
    if (currentAction != 'route' && prevAction == 'route') {
    	$('#form #fqdn').val('');
    }

    if (currentAction == 'route') {
      $('#btnText').html('BGP Route');
      $('#fqdn').attr('placeholder', 'ip address');
    } else {
    	$('#fqdn').attr('placeholder', 'ip address or domain name');
    }

	if (currentAction == 'ping') {
		$('#btnText').html('Ping IPV4');
	}

	if (currentAction == 'trace') {
		$('#btnText').html('Traceroute  IPV4');
	}
		if (currentAction == 'ping6') {
		$('#btnText').html('Ping IPV6');
	}

	if (currentAction == 'trace6') {
		$('#btnText').html('Traceroute  IPV6');
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