<script src="<?php echo $this->manager->getURI(); ?>assets/js/jq/jquery-1.11.3.min.js"></script>
<script src="<?php echo $this->manager->getURI(); ?>assets/js/jq/jquery-ui-1.11.4/jquery-ui.min.js"></script>
<script src="<?php echo $this->manager->getURI(); ?>assets/js/jq/jquery.url.min.js"></script>
<script src="<?php echo $this->manager->getURI(); ?>assets/js/jq/jquery.jeditable.mini.js"></script>
<script src="<?php echo $this->manager->getURI(); ?>assets/js/jq/jquery.validate.min.js"></script>
<script src="<?php echo $this->manager->getURI(); ?>assets/js/jq/utilities.js"></script>
<script src="<?php echo $this->manager->getURI(); ?>assets/js/jq/jquery.ba-outside-events.min.js"></script>
<script src="<?php echo $this->manager->getURI(); ?>assets/js/jq/qtip/jquery.qtip.min.js"></script>
<script src="<?php echo $this->manager->getURI(); ?>assets/js/superfish/js/hoverIntent.js"></script>
<script src="<?php echo $this->manager->getURI(); ?>assets/js/superfish/js/superfish.js"></script>
<script src="<?php echo $this->manager->getURI(); ?>assets/js/plupload-2.1.4/js/plupload.full.min.js"></script>
<script src="<?php echo $this->manager->getURI(); ?>assets/js/plupload-2.1.4/js/jquery.plupload.queue/jquery.plupload.queue.min.js"></script>
<script src="<?php echo $this->manager->getURI(); ?>assets/js/jq/jquery.imgpreload.min.js"></script>
<script src="http://cdn.leafletjs.com/leaflet-0.4/leaflet.js"></script>
<script src="<?php echo $this->manager->getURI(); ?>assets/js/jq/jquery.blockUI.js"></script>
<script src="<?php echo $this->manager->getURI(); ?>assets/js/bootstrap-3.3.4/js/bootstrap.min.js"></script>

<script>
    var globals = { 'relurl': '<?php echo $this->manager->getURI(); ?>' };
    globals.ajaxurl = globals.relurl + 'controllers/ajax/';

    // for the maximum year in datepicker drop downs
    var maxDate = new Date();
</script>

<!-- Google Analytics -->
<script>
  var _gaq = _gaq || [];
  _gaq.push(['_setAccount', 'UA-2419317-14']);
  _gaq.push(['_trackPageview']);

  (function() {
    var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
    ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
    var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
  })();
</script> <!-- end Google Analytics -->