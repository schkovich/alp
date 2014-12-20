<?php
// passed variables: 
//		redirect_time: the time before the page refreshes.
//		redirect: the url the page refreshes to. ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd ">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
<title><?php echo $lan['name']; if(!empty($title)) { echo " - $title"; } ?></title>
<style type="text/css" media="all">
	<?php include_once $master['currentskin'].'x.css.php'; ?>
</style>
<meta http-equiv="Refresh" content="<?php echo $redirect_time; ?>; url=<?php echo $redirect; ?>">
	<script language="javascript"><!--
	var counter = 0;
	function countdown() {
		document.getElementById('pendule').innerHTML = <?php echo $redirect_time; ?> - counter;
		counter++;
		setTimeout("countdown(counter)",1000);
	}
	// --></script>
</head>
<body text="<?php echo $colors['text']; ?>" link="<?php echo $colors['text']; ?>" alink="<?php echo $colors['text']; ?>" vlink="<?php echo $colors['text']; ?>" bgcolor="<?php echo $colors['background']; ?>"<?php echo (!empty($images['background'])?' background="'.$master['currentskin'].$images['background'].'"':''); ?> onLoad="countdown()">
<br /><br /><br /><br /><br /><br /><br /><br />
<div align="center"><table bgcolor="<?php echo $colors['border']; ?>" border="0" cellpadding="0" cellspacing="1" width="95%">
<tr><td bgcolor="<?php echo $colors['cell_background']; ?>"><div align="center">
