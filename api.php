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

error_reporting(0);
header('Content-Type: application/json');

function fail($why) {
	echo json_encode(array(
		'error' => $why
		));
	exit();
}

if (!is_readable("config.ini"))
	fail("Nothing to do, my friend.");
$config = parse_ini_file("config.ini");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
	$user     = $config['user'];
	$password = $config['password'];
	
	$tools = array(
		'ping' => '/ping count=4',
		'trace' => '/tool traceroute use-dns=yes',
		'route' => '/ip route print',
        'exactroute' => '/ip r pr de where dst-address=',
		'peers' => '/routing bgp peer print',
		'status' => '/routing bgp peer print status'
		);
	
	$type    = $_POST['type'];
	$server  = $config['fqdn'][$_POST['id']];
	$command = $tools[$type];
	
	error_log($type . ' ' . $command . ' ' . $cmd);

	if ($command == "" || $server == "")
		fail('wrong parameters');

	$cmd = $_POST['command'];

	if ($type == 'ping' || $type == 'trace' || $type == 'exactroute') {
		if ($cmd == "")
			fail('empty parameter');
		
		if ($type == 'exactroute')
			$space = '';
		else
			$space = ' ';

		if ($type == 'ping' || $type == 'trace') {
			$host = gethostbynamel($cmd);
			if($host) {
				$cmd = $host[0];
			}
		}

		$command = $command . $space . escapeshellcmd($cmd);
		
	}

	if (strtoupper(substr(PHP_OS, 0, 3)) !== 'WIN' && $config['password'] == '') { // linux no password
		$fp = popen('ssh -o UserKnownHostsFile=/dev/null -o StrictHostKeyChecking=no ' . $user . '@' . $server . ' ' . $command, 'r');
	} else { // putty link
		$fp = popen($config['path'] . ' -ssh -l ' .  $user . ' -pw ' . $password . ' ' . $server . ' ' . $command, 'r');
	}

	$out = null;
	while (!feof($fp)) {
		$out .= fgets($fp);
	}
	fclose($fp);
	
	echo json_encode(array(
		'command' => $command,
		'result' => $out
		));
	
} else {
	unset($config["user"]);
	unset($config["password"]);

	echo json_encode($config);
}
?>