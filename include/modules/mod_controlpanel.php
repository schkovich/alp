<?php
global $images, $colors, $master, $toggle, $userinfo, $lan, $container, $dbc; 
if (current_security_level() >= 1) { ?>
	<table border="0" cellpadding="0" cellspacing="0" width="100%">
	<tr><td class="smm"><font color="<?php echo $colors['blended_text']; ?>"><?php get_lang("cp_cuser",1); ?></font></td><td class="sm"><strong><a href="disp_users.php?id=<?php echo $userinfo['userid']; ?>"><?php echo $userinfo['username']; ?></a></strong></td></tr>
	<tr><td class="smm"><font color="<?php echo $colors['blended_text']; ?>"><?php get_lang("cp_cip",1); ?></font></td><td class="sm"><?php echo $userinfo["recent_ip"]; ?></td></tr>
	<tr><td colspan="2"><?php spacer(1,8,1); ?></td></tr>
		<?php
		$bool = false;
		if ($toggle['gamerequests']) { ?>
			<tr><td colspan="2" class="sm"><?php get_arrow(); ?>&nbsp;<a href="gamerequest.php" class="radio"><?php get_lang("open_play",1); ?> <strong><?php get_lang("gr",1); ?></strong></a><br /></td></tr><?php 
			$bool = true;
		}
		if ($toggle['prizes'] && $dbc->queryOne('SELECT count(prizeid) FROM prizes')) { ?>
			<tr><td colspan="2" class="sm"><?php get_arrow(); ?>&nbsp;<a href="chng_prizes.php" class="radio"><?php get_lang("reg_for",1); ?> <strong><?php get_lang("prizes",1); ?></strong></a><br /></td></tr><?php 
			$bool = true;
		}
	  	if ($toggle['foodrun']) { ?>
			<tr><td colspan="2" class="sm"><?php get_arrow(); ?>&nbsp;<a href="foodrun.php" class="radio"><?php get_lang("going_for",1); ?> <strong><?php get_lang("food",1); ?></strong>?</a><br /></td></tr><?php
            $bool = true;
		}
        if ($toggle['pizza']) { ?>
            <tr><td colspan="2" class="sm"><?php get_arrow(); ?>&nbsp;<a href="pizza.php" class="radio"><?php get_lang('want_pizza',1); ?>?</a><br /></td></tr><?php
            $bool = true;
        }
		if ($bool) {
			echo '<tr><td colspan="2">';
			spacer(1,8,1);
			echo '</td></tr>';
		}
	$menu = array();
	if ($toggle['benchmarks']) { $menu[get_lang("benchmarks")] = 'chng_benchmarks.php'; }
	if ($dbc->queryOne('SELECT count(itemid) FROM lodging')) { $menu[get_lang("cp_lodge")] = 'lodging.php'; }
	if ($dbc->queryOne('SELECT count(itemid) FROM foodplaces')) { $menu[get_lang("restaurants")] = 'restaurants.php'; }
	if ($toggle['techsupport']) { $menu[get_lang("tech_support")] = 'techsupport.php'; }
	if ($toggle['shoutbox']) { $menu[get_lang("shoutbox")] = 'shoutbox.php'; }
	if ($toggle['policy'] && !empty($master['policyurl'])) { $menu[get_lang("policy")] = $master['policyurl']; }
    if ($toggle['staff']) { $menu[get_lang("staff")] = 'staff.php'; }
	
	$counter = 0;
	if(sizeof($menu) > 0) {
		foreach ($menu as $key => $val) { 
			echo ($counter%2==0?'<tr class="sm">':''); ?>
			<td><?php get_arrow(); ?>&nbsp;<a href="<?php echo $val; ?>" class="menu"><?php echo $key; ?></a></td>
			<?php 
			echo ((sizeof($menu)-$counter)==1&&$counter%2==0?'<td>&nbsp;</td>':'');
			echo ($counter%2==1?'<tr class="sm">':'');
			$counter++;
		} 
		?><tr><td colspan=2><?php spacer(1,8,1); ?></td></tr> <?php
	} ?>
	<?php if(!ALP_TOURNAMENT_MODE) { ?><tr><td colspan="2" class="sm"><?php get_arrow(); ?>&nbsp;<a href="chng_userinfo.php" class="menu">><?php get_lang("profile",1); ?></a></td></tr><?php } ?>
	<?php if (($master['doublecheckpassword'] && $master['authbyiponly']) || !$master['authbyiponly']) { ?><tr><td colspan="2" class="sm"><?php get_arrow(); ?>&nbsp;<a href="chng_passwd.php" class="menu">><?php get_lang("change_pw",1); ?></a></td></tr><?php } ?>
	<tr><td colspan="2" class="sm"><?php get_arrow(); ?>&nbsp;<a href="logout.php" class="menu"><strong>><?php get_lang("logout",1); ?></strong></a></td></tr>
	</table>
	<?php
} else {
	if (!$master['authbyiponly']) { ?>
		<script language="javascript" type="text/javascript" src="include/_md5.js"></script>
		<script language="javascript" type="text/javascript"> 
		<!-- 
		function doLogin() { 
		if(document.loginn.passwd.value != "") document.loginn.passwd.value = calcMD5(document.loginn.passwd.value);
		document.loginn.javascript.value = "yes";
		} 
		// --> 
		</script>
		<?php
	} ?>
	<form action="login.php" method="post" name="loginn"><?php if (!$master['authbyiponly']) { ?><input type="hidden" name="javascript" value="" /><?php } ?>
	<font size="1">&nbsp;username<br /></font>
	<?php
	if ($master['loginselect']) { ?>
		<select name="username" style="width: <?php echo $this->get_inner_width(); ?>; font-size: 11px"><option value=""></option>
		<?php
		$data = $dbc->query('SELECT username FROM users ORDER BY username');
		while ($row = $data->fetchRow()) { ?>
			<option value="<?php echo $row['username']; ?>"><?php echo $row['username']; ?></option>
			<?php
		} ?>
		</select>
		<?php
	} else { ?>
		<input type="text" name="username" maxlength="40" style="width: <?php echo $this->get_inner_width(); ?>px" /><br />
		<?php
	}
	if (!$master['authbyiponly']) { ?>
		<font size="1">&nbsp;password<br /></font>
		<input type="password" name="passwd" maxlength="34" style="width: <?php echo $this->get_inner_width(); ?>px" /><br />
		<?php
	} ?>
	<font size="1">&nbsp;<a href="register.php"><?php echo get_lang("cp_register",1); ?></a></font><br />
	<?php
	if (!$master['authbyiponly']) { ?>
		<font size="1">&nbsp;<a href="passwd.php"><?php get_lang("forgot",1); ?></a></font><br />
		<font size="1" color="<?php echo $colors['blended_text']; ?>"><?php get_lang("cp_security",1); ?></font>
		<?php
	} ?>
	<img src="img/pxt.gif" width="1" height="4" border="0" alt="" /><br />
	<div align="right"><input type="submit" value="log in" class="formcolors"<?php if (!$master['authbyiponly']) { ?> onClick="doLogin(); return true;"<?php } ?> /></div>
	</form>	
	<?php
	$menu = array();
	if ($toggle['techsupport']) { $menu[get_lang("tech_support")] = 'techsupport.php'; }
	if ($dbc->queryOne('SELECT count(itemid) FROM lodging')) { $menu[get_lang("cp_lodge")] = 'lodging.php'; }
	if ($dbc->queryOne('SELECT count(itemid) FROM foodplaces')) { $menu[get_lang("restaurants")] = 'restaurants.php'; }
	if ($toggle['shoutbox']) { $menu[get_lang("shoutbox")] = 'shoutbox.php'; }
	if ($toggle['policy'] && !empty($master['policyurl'])) { $menu[get_lang("policy")] = $master['policyurl']; }
	if(sizeof($menu) > 0) {
		?>
		<table border="0" width="100%" cellpadding="0" cellspacing="0" class="sm">
		<tr><td colspan="2"><strong><?php get_lang("cp_other",1); ?></strong></td></tr>
		<?php
		
		$counter = 0;
		foreach($menu as $key => $val) { ?>
			<?php echo ($counter%2==0?'<tr class="sm">':''); ?>
				<td><?php get_arrow(); ?>&nbsp;<a href="<?php echo $val; ?>"><?php echo $key; ?></a></td>
			<?php echo ((sizeof($menu)-$counter)==1&&$counter%2==0?'<td>&nbsp;</td>':''); ?>
			<?php echo ($counter%2==1?'<tr class="sm">':''); ?>
			<?php
			$counter++;
		} ?>
		</table>
		<?php
	}
}
?>
