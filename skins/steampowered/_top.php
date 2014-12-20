<table border="0" cellpadding="0" cellspacing="0" width="100%" bgcolor="#000000">
<tr>
	<td width="980">
		<table border="0" cellpadding="0" cellspacing="0" width="100%">
		<tr>
			<td valign="middle" align="left"><nobr><?php 
			spacer($container['leftmodule'] + $container['horizontalpadding'],1,1);
			spacer(14); ?><a href="index.php" class="menu"><b><?php echo $lan["name"]; ?></b></a><?php spacer(36); ?></td>
			<td valign="middle" width="100%">
			<div id="topmenu"><?php spacer(1,10,1); ?><font class="sm"><?php if(!ALP_TOURNAMENT_MODE) { ?><a href="index.php" class="menu" style="color: #ffffff"><strong><?php echo strtoupper(get_lang('home')); ?></strong></a><?php }
				  if($toggle["files"] && !ALP_TOURNAMENT_MODE) { spacer(18); ?><a href="files.php" class="menu" style="color: #ffffff"><strong><?php echo strtoupper(get_lang('files')); ?></strong></a><?php  }
				  if($toggle["seating"] && !ALP_TOURNAMENT_MODE) { spacer(18); ?><a href="seating.php" class="menu" style="color: #ffffff"><strong><?php echo strtoupper(get_lang('map')); ?></strong></a><?php  }
				  if($toggle["music"] && !ALP_TOURNAMENT_MODE) { spacer(18); ?><a href="music.php" class="menu" style="color: #ffffff"><strong><?php echo strtoupper(get_lang('music')); ?></strong></a><?php  }
				  if($toggle["schedule"]) { spacer(18); ?><a href="disp_schedule.php" class="menu" style="color: #ffffff"><strong><?php echo strtoupper(get_lang('schedule')); ?></strong></a><?php  }
				  if($toggle["servers"] && ALP_TOURNAMENT_MODE_COMPUTER_GAMES) { spacer(18); ?><a href="servers.php" class="menu" style="color: #ffffff"><strong><?php echo strtoupper(get_lang('servers')); ?></strong></a><?php  }
				  spacer(18); ?><a href="tournaments.php" class="menu" style="color: #ffffff"><strong><?php echo strtoupper(get_lang('tournaments')); ?></strong></a>
            <?php if($toggle["sponsors"] && !ALP_TOURNAMENT_MODE) { spacer(18); ?><a href="disp_sponsors.php" class="menu" style="color: #ffffff"><strong><?php echo strtoupper(get_lang('sponsors')); ?></strong></a><?php  }
                  if(!ALP_TOURNAMENT_MODE && $toggle["staff"]) { spacer(18); ?><a href="staff.php" class="menu" style="color: #ffffff"><strong><?php echo strtoupper(get_lang('staff')); ?></strong></a><?php  }
			      if(!ALP_TOURNAMENT_MODE) { spacer(18); ?><a href="users.php" class="menu" style="color: #ffffff"><strong><?php echo strtoupper(get_lang('users')); ?></strong></a></font><?php } ?><br />
			<?php spacer(1,10,1); ?></div>
			</td>
			<td align="right" valign="middle" class="smm">
			<nobr><?php 
			spacer(1,10,1);
			echo cp_menu(); //include/_functions.php
			spacer($container["horizontalpadding"],1,1);
			spacer(1,10,1); ?>
			</td>
		</tr>
		</table>
		<?php spacer(980,1,1); ?>
	</td>
	<td width="100%"><?php spacer(); ?></td>
</tr>
</table>
<br />
<table border="0" cellpadding="0" cellspacing="0" width="980" bgcolor="<?php echo $colors["background"]; ?>">
<tr><td>
	<?php spacer(1,$container['verticalpadding']); ?>
	<table border="0" cellpadding="0" cellspacing="0" width="100%">
		<tr>
			<td width="<?php $modules->get_Width("left"); ?>" valign="top"><?php $modules->display_all_modules("left"); ?></td>
			<td width="100%" valign="top">
