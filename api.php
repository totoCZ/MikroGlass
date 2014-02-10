<?php
/*
MikroGlass - Mikrotik looking glass. For the web.

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

$config		= parse_ini_file("config.ini.php");
$user 		= $config['user'];
$password	= $config['password'];

$tools = array(
	'ping'          => '/ping count=4',
	'trace'         => '/tool traceroute use-dns=yes duration=2',
	'ping6'          => '/ping count=5',
	'trace6'         => '/tool traceroute use-dns=yes',
	'route'         => '/ip r pr de where bgp %s in dst-address',
	'ipv4-route-info'    => '/ip route print',
	'ipv6-route-info'    => '/ipv6 route print',
	'bgp-peer'      => '/routing bgp peer print',
	//Commented out by default to disallow any chance for users to see BGP peer password details etc
	//'bgp-status'    => '/routing bgp peer print status',
	'ospf-neighbor' => '/routing ospf neigh print',
	'v4-neighbor'   => '/ip neigh pr de',
	'v6-neighbor'   => '/ipv6 neigh pr',
);

$type       = $_POST['type'];				// type of command (ping, trace, ..)
$tool       = $tools[$type];				// command syntax
$argument   = $_POST['command'];			// user's argument
$server     = $config['fqdn'][$_POST['id']];// resolve FQDN host
$exec       = $tool;						// simple commands execute immediately

if (!$tool || !$server) {
	// Does not match our tool or server arrays.
	fail('Wrong parameters.');
}

if ($type == 'ping' || $type == 'trace' || $type == 'ping6' || $type == 'trace6' || $type == 'route') {
	// We need argument for these tools
	if (empty($argument)) {
		fail('Empty parameter.');
	}

	// Need to sanitize IPV4 hostname
	if ($type == 'ping' || $type == 'trace' || $type == 'route') {
		// check that the argument - is the ip
		// if realy ip - ok we are go further
		// if not ip - maybe it's hostname?
		// get dns A record
		if(ip2long($argument)){
			$argument = $argument;
		} else {
			$host = dns_get_record($argument,DNS_A);
			if($host) {
				$argument = $host[0][ip];
			} else {
				fail('Wrong IPV4 hostname.');
			}
		}
	}
	// Need to sanitize IPV6 hostname
	if ($type == 'ping6' || $type == 'trace6') {
		// Always returns safely with IP, even for IPs
		//$host = gethostbynamel($argument);
		$host = dns_get_record($argument,DNS_AAAA);
		if($host) {
			$argument = $host[0][ipv6];
		} else {
			fail('Wrong IPV6 hostname.');
		}
	}

	// BGP route lookup
	if ($type == 'route') {
		$exec = sprintf($tool, escapeshellcmd($argument));
	}else{
		$exec = $tool . ' ' . escapeshellcmd($argument);
	}
}

// Can't really ssh with passwords
// Key auth works.
if (strtoupper(substr(PHP_OS, 0, 3)) !== 'WIN' && empty($config['password'])) {
	// Linux, no password/keys, let's ssh
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
pclose($fp);

// Return result and our command for display
echo json_encode(array(
	'command'	=> $exec,
	'result'	=> $out
));
?>
