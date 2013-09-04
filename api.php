<?php
/*
MikroGlass - Mikrotik looking glass. For the web.
Copyright (C) 2013 Tom Hetmer (http://tom.hetmer.cz)

This program is free software: you can redistribute it and/or modify
it under the terms of the GNU Affero General Public License as
published by the Free Software Foundation, either version 3 of the
License, or (at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU Affero General Public License for more details.

You should have received a copy of the GNU Affero General Public License
along with this program.  If not, see <http://www.gnu.org/licenses/>.
*/

header('Content-Type: application/json');

function fail($why) {
	echo json_encode(array(
		'error' => $why
	));
	exit();
}

$config		= parse_ini_file("config.ini");
$user 		= $config['user'];
$password	= $config['password'];

$tools = array(
	'ping'		=> '/ping count=4',
	'trace'		=> '/tool traceroute duration=3 use-dns=yes',
	'exactroute'	=> '/ip r pr de where dst-address=',
	'route'		=> '/ip route print',
	'peers'		=> '/routing bgp peer print',
	'status'	=> '/routing bgp peer print status'
);

$type 		= $_POST['type'];		// type of command (ping, trace, ..)
$tool 		= $tools[$type];		// command syntax
$argument	= $_POST['command'];		// user's argument
$server		= $config['fqdn'][$_POST['id']]; // resolve FQDN host
$exec 		= $tool;			// simple commands execute immediately

if (!$tool || !$server) {
	// Does not match our tool or server arrays.
	fail('Wrong parameters.');
}

if ($type == 'ping' || $type == 'trace' || $type == 'exactroute') {
	// We need argument for these tools
	if (empty($argument)) {
		fail('Empty parameter.');
	}

	// BGP route can't have space
	if ($type == 'exactroute') {
		$space = '';
	} else {
		$space = ' ';
	}

	// Need to sanitize hostname
	if ($type == 'ping' || $type == 'trace') {
		// Always returns safely with IP, even for IPs
		$host = gethostbynamel($argument);
		if($host) {
			$argument = $host[0];
		} else {
			fail('Wrong hostname.');
		}
	}

	$exec = $tool . $space . escapeshellcmd($argument);	
}

// Can't really ssh with passwords
// Key auth might help - what about mikrotik sshd?
if (strtoupper(substr(PHP_OS, 0, 3)) !== 'WIN' && empty($config['password'])) {
	// Linux, no password, let's ssh
	$fp = popen('ssh -o UserKnownHostsFile=/dev/null -o StrictHostKeyChecking=no ' . $user . '@' . $server . ' ' . $exec, 'r');
} else {
	// Putty Link fallback (Linux, Windows)
	$fp = popen($config['path'] . ' -ssh -l ' .  $user . ' -pw ' . $password . ' ' . $server . ' ' . $exec, 'r');
}

// Handle stream
$out = null;
while (!feof($fp)) {
	$out .= fgets($fp);
}
fclose($fp);

// Return result and our command for display
echo json_encode(array(
	'command'	=> $exec,
	'result'	=> $out
));
?>
