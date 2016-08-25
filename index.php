<?php
ini_set('display_errors', 'On');
?>
<html>
<head>
	
	<meta http-equiv="content-type" content="text/html; charset=utf-8">
	<link rel="shortcut icon" type="image/x-icon" href="/img/colormap.gif">
	<link rel="stylesheet" href="theme.min.css" type="text/css" />
	<link rel="stylesheet" href="style.css" type="text/css" />
	<!--<script type="text/javascript" src="jscolor/jscolor.js"></script>-->
	<script type="text/javascript" src="scripts/jquery-2.1.4.min.js"></script>
	<script type="text/javascript" src="scripts/jquery-ui-1.9.2.custom.min.js"></script>
	<script type="text/javascript" src="scripts/mustache.min.js"></script>
	<script type="text/javascript" src="scripts/script.js"></script>
	
	<title>Control Page v.2</title>
</head>
<body>

<button type="button" class="btn btn-info settings-button">Einstellungen</button>

<!--include "constants.php";-->


<h1>Control Page</h1>

<div id="device-list" class="container"></div>

<script id="template-device" type="x-tmpl-mustache">
	<div class="row">
			<div class="center-block limmited well">
				<div class="panel panel-success autocollapse">
					<div id="click-dev-{{ id }}" class="panel-heading row">
							<h3>{{ name }}</h3> <span id="chev-dev-{{ id }}" class="hide pull-right glyphicon glyphicon-chevron-down"></span>
					</div>
					
					<div id="vals-dev-{{ id }}" class="panel-body"></div>
				</div>
			</div>
	</div>
</script>

<script id="template-values" type="x-tmpl-mustache">
	<div class="row">
							{{ name }}
	</div>
</script>




</div>
</body></html>
