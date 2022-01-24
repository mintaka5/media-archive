<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		
		<title>UCI Image Archive - Organize</title>
	
		<!-- CSS -->
		<link type="text/css" rel="stylesheet" href="<?php echo $this->manager->getURI(); ?>assets/js/jq/css/uci-theme/jquery-ui-1.8.7.custom.css" />
		<link type="text/css" rel="stylesheet" href="<?php echo $this->manager->getURI(); ?>assets/css/organize.css" />
		
		<!-- Javscript -->
		<script type="text/javascript" src="<?php echo $this->manager->getURI(); ?>assets/js/jq/jquery-1.6.min.js"></script>
		<script type="text/javascript" src="<?php echo $this->manager->getURI(); ?>assets/js/jq/jquery-ui-1.8.13.custom.min.js"></script>
		<script type="text/javascript" src="<?php echo $this->manager->getURI(); ?>assets/js/jq/jquery.url.min.js"></script>
		<script type="text/javascript" src="<?php echo $this->manager->getURI(); ?>assets/js/jq/jquery.imgpreload.min.js"></script>
		<script type="text/javascript" src="<?php echo $this->manager->getURI(); ?>assets/js/jq/jquery.blockUI.js"></script>
		<script type="text/javascript" src="<?php echo $this->manager->getURI(); ?>assets/js/jq/jquery.masonry.min.js"></script>
		<script type="text/javascript" src="<?php echo $this->manager->getURI(); ?>assets/js/jq/jquery.layout-latest.js"></script>
		<script type="text/javascript" src="<?php echo $this->manager->getURI(); ?>assets/js/jq/jquery.sizes.min.js"></script>
		
		<!-- inline Javascript -->
		<script type="text/javascript">
			var globals = { 'relurl': '<?php echo $this->manager->getURI(); ?>' };
			globals.ajaxurl = globals.relurl + 'controllers/ajax/';

			// for the maximum year in datepicker drop downs
			var maxDate = new Date();
		</script>
		<script type="text/javascript" src="<?php echo $this->manager->getURI(); ?>assets/js/organize/main.js"></script>
	</head>

	<body style="margin:0;">
		<div id="tabs">
			<ul>
				<li><a href="#groups-tab">Sets</a></li>
			</ul>
			<div id="groups-tab">
				<div style="height:90%; width:100%;" id="groups-layout">
					<div class="ui-layout-center">
						<div id="groups-holder"></div>
					</div>
					<div class="ui-layout-north"></div>
					<div class="ui-layout-south">
						<div id="asset-search">
							<div class="go-left">
								<select id="sel-asset-groups">
									<option value="">All content</option>
								</select>
							</div>
							<div class="go-left">
								<input type="text" id="asset-search-txt" />
								<button id="asset-search-btn">Search</button>
							</div>
							<div class="clear-left"></div>
						</div>
						<div id="asset-utils" class="clear-left"></div>
						<div id="asset-nav">
							<div id="asset-list-holder">
								<div id="asset-list"></div>
							</div>
							<div class="previ-hider"></div>
							<div class="prev">
								<button class="btn filler">&lt;</button>
							</div>
							<div class="next">
								<button class="btn filler">&gt;</button>
							</div>
							<div class="next-hider"></div>
						</div>
					</div>
					<div class="ui-layout-east"></div>
					<div class="ui-layout-west"></div>
				</div>
			</div>
		</div>
	</body>
</html>