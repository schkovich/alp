<table border="0" cellpadding="0" cellspacing="1" width="980" align="center" bgcolor="<?php echo $colors["border"]; ?>" class="centerd">
<tr><td bgcolor="<?php echo $colors["background"]; ?>">
	&nbsp;&nbsp;&nbsp;&nbsp;<a href="index.php"><img src="<?php echo $master["currentskin"].$images["title"]; ?>" border="0" alt="<?php echo $lan["name"]; ?>" /></a><br />
	<?php spacer(1,4,1); ?>
	<table border="0" cellpadding="2" cellspacing="2" width="100%" bgcolor="<?php echo $colors["cell_title"]; ?>">
	<tr>
		<td>
		<?php include('include/_top_menu.php'); ?>
		</td>
	</tr>
	</table>
	<?php spacer(1,4,1); ?>
	<table border="0" cellpadding="0" cellspacing="2" width="100%" bgcolor="<?php echo $colors["cell_title"]; ?>" class="smm">
	<tr>
		<td>
		<?php spacer(20,1); ?><?php echo mini_menu(); //include/_functions.php ?><br />
		</td>
		<td align="right">
		<?php echo cp_menu(); //include/_functions.php ?>
		<?php spacer($container["horizontalpadding"],1,1); ?>
		</td>
	</tr>
	</table>
	<br />
	<table border="0" cellpadding="0" cellspacing="0" width="100%">
		<tr>
			<td width="<?php $modules->get_Width("left"); ?>" valign="top"><?php $modules->display_all_modules("left"); ?></td>
			<td width="100%" valign="top">