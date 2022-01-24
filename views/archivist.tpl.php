<html>
	<head>
		<title>Image Archive</title>
		
		<meta charset="UTF-8" />
		<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
		
	</head>
	<body>
		<div id="left">
			<div id="groupsSearchHolder">
				<input type="text" name="grpSearch" id="grpSearch" value="" />
			</div>
			<div id="groupsHolder"></div>
		</div>
		
		<div id="right"></div>
		
		<script type="text/javascript" src="<?php echo $this->manager->getURI(); ?>assets/js/jq/jquery-1.7.1.min.js"></script>
		<script type="text/javascript" src="<?php echo $this->manager->getURI(); ?>assets/js/underscore-min.js"></script>
		<script type="text/javascript" src="<?php echo $this->manager->getURI(); ?>assets/js/backbone-min.js"></script>
		<script type="text/javascript" src="<?php echo $this->manager->getURI(); ?>assets/js/archivist.js"></script>
	</body>
</html>