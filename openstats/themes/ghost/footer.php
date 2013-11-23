<?php
if (!isset($website) ) { header('HTTP/1.1 404 Not Found'); die; }
?>

<div id="bottom">
<div class="wrapper">
  <div class="footer-wrap section" id="footer-wrap">
  <?php os_footer();?>
   <div class="widget HTML" id="HTML2">
    <div>Generated in: <?=$total_time?> sec.</div>
    <div class="gototop"><a href="#" rel="nofollow">Return to top of page</a> </div>
    <div class="creds">Copyright &#169; <?=date("Y")?> &#183; Powered by 
	<a class="footerlinks" href="http://ohsystem.net/">OpenHosting</a> | 
	<a class="footerlinks" target="_blank" href="http://openstats.iz.rs/">OpenStats</a> | 
	<a class="footerlinks" href="<?=OS_HOME?>?tos">Terms of Service</a> 
	</div>
	
	<div class="creds">
	GeoLite data created by <a target="_blank" class="footerlinks" href="http://www.maxmind.com">MaxMind</a>
	</div>
    <div class="clr"></div>
   </div>
  </div>
 </div><!-- /ct-wrapper -->
</div><!-- footer-wrapper -->

<?php os_after_footer(); ?>
<script>
  (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
  (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
  m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
  })(window,document,'script','//www.google-analytics.com/analytics.js','ga');

  ga('create', 'UA-7450286-13', 'ohsystem.net');
  ga('send', 'pageview');

</script>
</body>
</html>
