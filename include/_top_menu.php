		<div align="<?php echo $container["title_menu"]; ?>"><?php spacer(18); 
		if(!ALP_TOURNAMENT_MODE) { 
			?><a href="index.php" class="menu"><strong><?php echo get_lang("home"); ?></strong></a><?php 
		} 
		if($toggle["files"] && !ALP_TOURNAMENT_MODE) {
			?>&nbsp;&nbsp;<font color="<?php echo $colors["blended_text"]; ?>">|</font>&nbsp;&nbsp;<a href="files.php" class="menu"><strong><?php echo get_lang("files"); ?></strong></a><?php
		}
		if($toggle["seating"] && !ALP_TOURNAMENT_MODE) {
			?>&nbsp;&nbsp;<font color="<?php echo $colors["blended_text"]; ?>">|</font>&nbsp;&nbsp;<a href="seating.php" class="menu"><strong><?php echo get_lang("map"); ?></strong></a><?php
		}
		if($toggle["music"] && !ALP_TOURNAMENT_MODE) { 
			?>&nbsp;&nbsp;<font color="<?php echo $colors["blended_text"]; ?>">|</font>&nbsp;&nbsp;<a href="music.php" class="menu"><strong><?php echo get_lang("music"); ?></strong></a><?php
		}
		if($toggle["schedule"]) { 
			?>&nbsp;&nbsp;<font color="<?php echo $colors["blended_text"]; ?>">|</font>&nbsp;&nbsp;<?php
			?><a href="disp_schedule.php" class="menu"><strong><?php echo get_lang("schedule"); ?></strong></a><?php   
		} 
        if($toggle["pizza"]) { 
            ?>&nbsp;&nbsp;<font color="<?php echo $colors["blended_text"]; ?>">|</font>&nbsp;&nbsp;<?php
            ?><a href="pizza.php" class="menu"><strong><?php echo get_lang('pizza'); ?></strong></a><?php   
        } 
		if($toggle["servers"] && ALP_TOURNAMENT_MODE_COMPUTER_GAMES) {
			?>&nbsp;&nbsp;<font color="<?php echo $colors["blended_text"]; ?>">|</font>&nbsp;&nbsp;<a href="servers.php" class="menu"><strong><?php echo get_lang("servers"); ?></strong></a><?php  
		} 
		?>&nbsp;&nbsp;<font color="<?php echo $colors["blended_text"]; ?>">|</font>&nbsp;&nbsp;<?php
		?><a href="tournaments.php" class="menu"><strong><?php echo get_lang("tournaments"); ?></strong></a>
		<?php 
        if($toggle["sponsors"] && !ALP_TOURNAMENT_MODE) {
            ?>&nbsp;&nbsp;<font color="<?php echo $colors["blended_text"]; ?>">|</font>&nbsp;&nbsp;<a href="disp_sponsors.php" class="menu"><strong><?php echo get_lang("sponsors"); ?></strong></a><?php
        }
        if(!ALP_TOURNAMENT_MODE && $toggle["staff"]) {
            ?>&nbsp;&nbsp;<font color="<?php echo $colors["blended_text"]; ?>">|</font>&nbsp;&nbsp;<a href="staff.php" class="menu"><strong><?php echo get_lang("staff"); ?></strong></a><?php
        }
		if(!ALP_TOURNAMENT_MODE) { 
			?>&nbsp;&nbsp;<font color="<?php echo $colors["blended_text"]; ?>">|</font>&nbsp;&nbsp;<a href="users.php" class="menu"><strong><?php echo get_lang("users"); ?></strong></a><?php 
			}
		spacer($container["horizontalpadding"]); ?></div>