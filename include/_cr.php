<?php
// removing the credits without replacing or modifying with an appropriate substitute constitutes a violation of the license.
// we worked hard on this program, and all we're receiving for it is a little name recognition.  so respect the ideals of 
// open source software and do not remove the credits.  thanks.
global $dbc, $start_time;
?>
<!--<font class="sm">ALP <?php echo $master['alpver']; ?> | <a href="license.php"><b>license</b></a> | &copy; 2004 <a href="credits.php"><font color="<?php echo $colors['primary']; ?>"><b>the nerdclub programming team</b></font></a><br /><br /></font>-->
<div align="right">
		<font size="1">
		<?php
	 	list($usec, $sec) = explode(" ", microtime());  //Code for process timing clock
		$end_time = ((float)$usec + (float)$sec);		//Code for process timing clock
		echo number_format($end_time - $start_time, 5, '.', '') . ' seconds - queries: ' . $dbc->getNumberOfQueries();
		spacer(20);
		?>
		</font size="0">
    <a href="license.php"><img src="img/<?php echo (!empty($colors['image_text'])?$colors['image_text']:'white'); ?>_license.gif" width="50" height="7" border="0" alt="license" /></a>
    <?php 
/*
		if(false) { 
			spacer(10); ?><a href="sitemap.php"><img src="img/<?php echo (!empty($colors["image_text"])?$colors['image_text']:'white'); ?>_sitemap.gif" width="56" height="7" border="0" alt="site map" /></a>
    <?php 
		}
*/
		spacer(10); ?><a href="credits.php"><img src="img/<?php echo (!empty($colors["image_text"])?$colors["image_text"]:'white'); ?>_copyright.gif" width="248" height="7" border="0" alt="alp (c) 2003-2006 the nerdclub programming team" /></a><?php spacer(20); ?>
</div>