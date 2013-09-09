<?php $c = parse_ini_file("config.ini.php"); ?>
<!DOCTYPE html>
<html>
<head>
	<title>MikroGlass Looking Glass for Mikrotik routers</title>
	<meta charset="utf-8"> 
	<meta name="author" content="tom.hetmer.net 2013">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<link href="//fonts.googleapis.com/css?family=Josefin+Sans|Droid+Sans&amp;subset=latin" rel="stylesheet">
	<link href="//cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/3.0.0/css/bootstrap.min.css" rel="stylesheet" media="screen">
	<link href="//cdnjs.cloudflare.com/ajax/libs/font-awesome/3.2.1/css/font-awesome.min.css" rel="stylesheet">
	<link href="./app.css" rel="stylesheet" media="screen">
	<link rel="shortcut icon" href="/favicon.ico" type="image/x-icon">
	<link rel="icon" href="/favicon.ico" type="image/x-icon">
</head>

<body>
	<nav class="navbar ug-navbar navbar-fixed-top" role="navigation">
		<div class="container">
			<div class="navbar-header">
				<button type="button" class="navbar-toggle navbar-default" data-toggle="collapse" data-target=".navbar-ex1-collapse">
					<span class="sr-only">Toggle navigation</span>
					<span class="icon-bar"></span>
					<span class="icon-bar"></span>
					<span class="icon-bar"></span>
				</button>
				<a href="./" class="navbar-brand">
					µ<i class="icon-glass"></i>
				</a>
			</div>
			<div class="collapse navbar-collapse navbar-ex1-collapse">
				<ul class="nav navbar-nav">
					<?php $i = 0; ?>
					<?php foreach($c['fqdn'] as $server) { ?>
					<li class="server server-<?php echo $i ?>"><a href="javascript:selectServer(<?php echo $i ?>)"><?php echo $server ?></a></li>
					<?php $i++;?>
					<?php } ?>
				</ul>
				<ul class="nav navbar-nav navbar-right">
					<li><a href="<?php echo $c['companyUrl'] ?>"><?php echo $c['name'] ?></a></li>
				</ul>
			</div>
		</div>
	</nav>

	<a href="https://github.com/TomHetmer/MikroGlass"><img style="position: absolute; top: 0; right: 0; border: 0;" src="https://s3.amazonaws.com/github/ribbons/forkme_right_red_aa0000.png" alt="Fork me on GitHub"></a>

	<div class="container">

		<div class="jumbotron">
			<h1>
				MikroGlass LG
			</h1>
			<a href="<?php echo $c['companyUrl'] ?>">
				<img src="<?php echo $c['logo'] ?>" class="pull-right" alt="Company logo">
			</a>
			<p class="lead">
				You have connected to a looking glass server operated by <a href="<?php echo $c['companyUrl'] ?>"><?php echo $c['name'] ?></a>.
			</p>
			<?php echo $c['customText'] ?>
		</div>

		<noscript>
			<div class="alert alert-block">
				<h4>Warning!</h4>
				<p>This tool does not function without JavaScript. Sorry.</p>
			</div>
		</noscript>

		<div class="page-header">
			<h2>
				Step 1
				<small class="warhead">Select your weapon.</small>
			</h2>
		</div>

		<form id="form">
			<div class="input-group">
				<div class="input-group-btn">
					<button id="button" class="btn btn-lg btn-danger" tabindex="-1">
						<span id="btnText">Ping</span>
					</button>
					<button type="button" class="btn btn-lg btn-danger dropdown-toggle" data-toggle="dropdown" tabindex="-1">
						<span class="caret"></span>
					</button>
					<ul class="dropdown-menu">
						<li><a href="javascript:switchAction('ping')">Ping</a></li>
						<li><a href="javascript:switchAction('trace')">Traceroute</a></li>
						<li><a href="javascript:switchAction('exactroute')">BGP Route</a></li>
					</ul>
				</div>
				<input class="form-control input-lg" id="fqdn" type="text" onclick="$(this).val('')" placeholder="hetmer.net" autofocus="autofocus" required="required">
			</div>
			<p class="help-block">
				Quickcheck:
				<a href="javascript:quick('route-info')">IP Routes</a>
				<a href="javascript:quick('bgp-peer')">BGP Peers</a>
				<a href="javascript:quick('bgp-status')">BGP Status</a>
			<br>
				Neighbors:
				<a href="javascript:quick('ospf-neighbor')">OSPF</a>
				<a href="javascript:quick('v4-neighbor')">IPv4</a>
				<a href="javascript:quick('v6-neighbor')">IPv6</a>
			</p>
		</form>

		<div class="page-header">
			<h2>
				Step 2
				<small class="command"></small>
			</h2>
		</div>

		<pre class="result">Awaiting instructions.</pre>

		<footer>
			<p>
				<!-- you are not allowed to remove original author attribution (Affero GPL 3+) -->
				<!-- you can integrate this line into your design however you want -->
				Powered by <a href='//github.com/TomHetmer/MikroGlass'>MikroGlass</a>, &copy; <a href='//hetmer.net'>Tomáš Hetmer</a>.
			</p>
			<p>
				<img src="./vendor/made_mfm.png" class="mfm pull-right" alt="Mikrotik logo">
			</p>
		</footer>

	</div>

	<script src="//ajax.googleapis.com/ajax/libs/jquery/2.0.3/jquery.min.js"></script>
	<script src="//cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/3.0.0/js/bootstrap.min.js"></script>
	<script src="./app.js"></script>
	
</body>
</html>
