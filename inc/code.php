<?php defined('ABSPATH') or die("Bye!"); ?>

<script type="text/javascript">
	(function(){ 
		var s	= document.createElement('script'); 
		s.type	= 'text/javascript'; 
		s.async = true; 
		s.src = '//c.netrox.sc/<?php echo($this->siteID); ?>/c.js?tmpl=<?php echo($this->widgetID); ?>'; 
		var ss = document.getElementsByTagName('script')[0];
		ss.parentNode.insertBefore(s, ss); 
	})(); 
</script>