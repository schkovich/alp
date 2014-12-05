<?php
require_once 'include/_universal.php';
$x = new universal('seating chart','seating',0);
if ($toggle['seating']&&($dbc->database_num_rows($dbc->database_query("SELECT * FROM seating_control")) || current_security_level() >= 2)) {
	if ($_POST['roomConfigure'] && current_security_level() >= 2) {
		unset($error);
		if ($_POST['roomH'] <= 0) $error[] = "Room height must be greater than 0.";
		if ($_POST['roomW'] <= 0) $error[] = "Room width must be greater than 0.";
		if ($_POST['roomPixelCorr'] < 12) $error[] = "Room pixel correlation must be 12 or greater.";
		if ($_POST['roomDistanceCorr'] <= 0) $error[] = "Room distance correlation must be greater than 0.";
		if (empty($error)) {
			if ($_POST['currentSeats'] == "purge") $dbc->database_query("DELETE FROM seating_seats");
			$dbc->database_query("UPDATE seating_seats SET dev_selected=0");
			$roomWPixels = (($_POST['roomPixelCorr'] * $_POST['roomW']) / $_POST['roomDistanceCorr']);
			$roomHPixels = (($_POST['roomPixelCorr'] * $_POST['roomH']) / $_POST['roomDistanceCorr']);
			$dbc->database_query("DELETE FROM seating_control");
			$dbc->database_query("INSERT INTO seating_control VALUES(
				'',
				'" . $_POST['roomPixelCorr']  . "',
				'" . $_POST['roomDistanceCorr'] . "',
				'" . $_POST['roomDistanceType'] . "',
				'" . ($roomWPixels + $_POST['roomPixelCorr'] - ($roomWPixels % $_POST['roomPixelCorr'])) . "',
				'" . ($roomHPixels + $_POST['roomPixelCorr'] - ($roomHPixels % $_POST['roomPixelCorr'])) . "',
				'0',
				'1',
				'0'
			)");
		}
	}
	
	$control = $dbc->database_fetch_array($dbc->database_query("SELECT * FROM seating_control"));
	$objects = $dbc->database_query("SELECT * FROM seating_seats");
	if (is_array($control)) {
		$corr = $control['pixelcorr'];
		$widthBound = $control['room_width'] / $corr;
		$heightBound = $control['room_height'] / $corr;
	}
	
	
	if ((!$control['room_width'] || !$control['room_height'] || !$control['distancecorr'] || !$control['pixelcorr'] || $_POST['editRoomData']) && current_security_level() >= 2) {
		$x->display_top(); ?>
		<b>setup the seating map</b>: <br /><br />
		
		<form action="<?php echo get_script_name(); ?>" method="POST">
		Default Measurement <select name="roomDistanceType" size="1"><option value="feet">feet</option><option value="meters">meters</option></select><br />
		<input type="text" name="roomPixelCorr" size="5" maxlength="5" value="<?php print ($control['pixelcorr'] ? $control['pixelcorr'] : 15); ?>"> Pixels = <input type="text" name="roomDistanceCorr" size="5" maxlength="5" value="<?php print ($control['distancecorr'] ? $control['distancecorr'] : 3); ?>"> feet/meters<br />
		Room Width <input type="text" name="roomW" size="5" maxlength="5" value="<?php print (is_array($control) ? ($control['room_width']/$corr)*$control['distancecorr'] : ""); ?>"> feet/meters<br />
		Room Height <input type="text" name="roomH" size="5" maxlength="5" value="<?php print (is_array($control) ? ($control['room_height']/$corr)*$control['distancecorr'] : ""); ?>"> feet/meters<br />
		<input type="radio" name="currentSeats" value="preserve" CHECKED class="radio"> Preserve current seats in database.<br />
		<input type="radio" name="currentSeats" value="purge" class="radio"> Delete current seats in database.<br />
		<input type="submit" value="Configure" class="formcolors">
		<input type="hidden" name="roomConfigure" value="1"></form><?php
		if($error) { ?>
			<font color="red"><b>Errors:</b><br /><?php
			foreach($error AS $e) { 
				print $e . "<br />";
			} ?>
			</font><?php
		}
		$x->display_bottom();
	} else {
		if($_POST['currentSubmit']) {
			if (is_numeric($_POST['currentX']) && is_numeric($_POST['currentY'])) {
				$control = $dbc->database_fetch_array($dbc->database_query("SELECT `room_height`,`room_width`,`pixelcorr` FROM seating_control"));
				$ytotal = $control['room_height'] / $control['pixelcorr'];
				$xtotal = $control['room_width'] / $control['pixelcorr'];
				if($_POST['currentX'] <= $xtotal && $_POST['currentY'] <= $ytotal) {
					$c = $dbc->database_fetch_array($dbc->database_query("SELECT * FROM seating_seats WHERE x=" . $_POST['currentX'] . " AND y=" . $_POST['currentY']));
					$currentID = $c['id'];
				}
			}
		}
		elseif ($_POST['currentID']) $currentID = $_POST['currentID'];
		elseif ($_GET['c']) $currentID = $_GET['c'];

		if (current_security_level() >= 1) {
			if (($_POST['userSitID']!=$userinfo['userid']&&current_security_level()>=2)||($_POST['userSitID']==$userinfo['userid'])||($_POST['userStandID']!=$userinfo['userid']&&current_security_level()>=2)||($_POST['userStandID']==$userinfo['userid'])) {
				if($_POST['userSitID'] && $currentID) {
					$dbc->database_query("UPDATE users SET room_loc=" . $currentID . " WHERE userid=" . $_POST['userSitID']);
				}
				if($_POST['userStandID'] && $currentID) {
					$dbc->database_query("UPDATE users SET room_loc='' WHERE userid=" . $_POST['userStandID']);
				}
			}
		}
		if (current_security_level() >= 2) {
			if ($_POST['devToggle']) {
				$dbc->database_query("UPDATE seating_control SET dev_mode=" . ($_POST['devToggle'] == "TurnOn" ? 1 : 0));
				$dbc->database_query("UPDATE seating_control SET grid=" . ($_POST['devToggle'] == "TurnOn" ? 1 : 0));
				if ($_POST['devToggle'] == "TurnOff") $dbc->database_query("UPDATE seating_seats SET dev_selected=0");
			}
			if ($_POST['gridToggle']) {
				$dbc->database_query("UPDATE seating_control SET grid=" . ($_POST['gridToggle'] == "turnOn" ? 1 : 0));
			}
			if ($_POST['javascriptToggle']) {
				$dbc->database_query("UPDATE seating_control SET dev_javascript=" . ($_POST['javascriptToggle'] == "turnOn" ? 1 : 0));
			}
			if ($control['dev_mode'] == 1) {
				// this function recursively selects contiguous seats in the seating map
				$touched = array();
				function selectContiguous($x,$y,$sval)
                {
					global $corr,$control,$touched,$widthBound,$heightBound,$dbc;
					$thisElement = $dbc->database_fetch_array($dbc->database_query("SELECT * FROM seating_seats WHERE x=" . $x . " AND y=" . $y));
					$dbc->database_query("UPDATE seating_seats SET dev_selected=" . $sval . " WHERE id=" . $thisElement['id']);
					$touched[$x][$y] = 1;
					if(!$touched[$x][$y-1] && ($y-1) >= 0 && $dbc->database_num_rows($dbc->database_query("SELECT * FROM seating_seats WHERE x=" . $x . " AND y=" . ($y-1))) > 0) selectContiguous($x,$y-1,$sval);
					if(!$touched[$x+1][$y] && ($x+1) < $widthBound && $dbc->database_num_rows($dbc->database_query("SELECT * FROM seating_seats WHERE x=" . ($x+1) . " AND y=" . $y)) > 0) selectContiguous($x+1,$y,$sval);
					if(!$touched[$x][$y+1] && ($y+1) < $heightBound && $dbc->database_num_rows($dbc->database_query("SELECT * FROM seating_seats WHERE x=" . $x . " AND y=" . ($y+1))) > 0) selectContiguous($x,$y+1,$sval);
					if(!$touched[$x-1][$y] && ($x-1) >= 0 && $dbc->database_num_rows($dbc->database_query("SELECT * FROM seating_seats WHERE x=" . ($x-1) . " AND y=" . $y)) > 0) selectContiguous($x-1,$y,$sval);
				}
				
				// this function checks whether any seats that move will overlap other seats. it returns the first pair 
				//		that satisfies this condition. if no overlap, it returns 0.
				function move_overlap($offsetX, $offsetY)
                {
					global $corr,$control,$widthBound,$heightBound,$dbc;
					$selected = $dbc->database_query("SELECT * FROM seating_seats WHERE dev_selected=1");
					$objects = $dbc->database_query("SELECT * FROM seating_seats WHERE dev_selected=0");
					while($o = $dbc->database_fetch_array($objects)) {
						$obj[$o['x']][$o['y']] = 1;
					}
					
					$overlap = array();
					while ($s = $dbc->database_fetch_array($selected)) {
						$newX = $s['x']+$offsetX; $newY = $s['y']+$offsetY;
						if($obj[$newX][$newY]) $overlap[] = $s['x'] . "," . $s['y'] . " will overlap another seat.";
						if($newX < 0 || $newX >= $widthBound) $overlap[] = $s['x'] . "," . $s['y'] . " will go outside the room.";
						if($newY < 0 || $newY >= $heightBound) $overlap[] = $s['x'] . "," . $s['y'] . " will go outside the room.";
					}
					return $overlap;
				}
				
				// this function moves the seats with dev_selected=1 the offset distances
				function move_selected($offsetX, $offsetY)
                {
                    global $dbc;
					$overlap = move_overlap($offsetX, $offsetY);
					if(empty($overlap)) {
						$selected = $dbc->database_query("SELECT * FROM seating_seats WHERE dev_selected=1");
						while($s = $dbc->database_fetch_array($selected)) {
							$dbc->database_query("UPDATE seating_seats SET x=" . ($s['x'] + $offsetX) . " WHERE id=" . $s['id']);
							$dbc->database_query("UPDATE seating_seats SET y=" . ($s['y'] + $offsetY) . " WHERE id=" . $s['id']);
						}
					}
					else return $overlap;
				}
				
				function find_leftmost_topmost()
                {
                    global $dbc;
					$coords = array();
					$minX = $dbc->database_result($dbc->database_query("SELECT min(x) FROM seating_seats WHERE dev_selected=1"), 0);
					$minY = $dbc->database_result($dbc->database_query("SELECT min(y) FROM seating_seats WHERE x=" . $minX . " AND dev_selected=1"), 0);
					$coords['x'] = $minX;
					$coords['y'] = $minY;
					return $coords;
				}
				if ($_POST['createSubmit']) {
					$dbc->database_query("UPDATE seating_seats SET dev_selected=0");
					$exists = $dbc->database_fetch_array($dbc->database_query("SELECT * FROM seating_seats WHERE x=" . $_POST['coordX'] . " AND y=" . $_POST['coordY']));
					if (!is_array($exists) && $_POST['coordX'] >= 0 && $_POST['coordY'] >= 0 && $_POST['coordX'] < $widthBound && $_POST['coordY'] < $heightBound && $_POST['tileType']) {
						switch ($_POST['tileType']) {
						   case 't': case 'r': case 'b': case 'l': $shape = "rect"; break;
						   case 'tl': case 'tr': case 'bl': case 'br': $shape = "circ"; break;
						   case 'v': $shape = "void"; break;
						}
						$dbc->database_query("INSERT INTO seating_seats VALUES(
							'',
							'" . $_POST['coordX'] . "',
							'" . $_POST['coordY'] . "',
							'" . $shape . "',
							'" . $_POST['tileType'] . "',
							'" . ($_POST['sittable'] ? 1 : 0) . "',
							'',
							'" . $_POST['groupNumber'] . "',
							'0'
						)");
					}
					if(is_array($exists)) $error[] = "There is already a seat at that location.";
					if(!$_POST['coordX'] && $_POST['coordX'] != 0) $error[] = "No X coordinate specified for create.";
					elseif($_POST['coordX'] >= $widthBound) $error[] = "X coordinate outside bounds of room.";
					if(!$_POST['coordY'] && $_POST['coordY'] != 0) $error[] = "No Y coordinate specified for create.";
					elseif($_POST['coordY'] >= $heightBound) $error[] = "Y coordinate outside bounds of room.";
					if(!$_POST['tileType']) $error[] = "No tile selected for create (select one of the seat tiles below).";
					unset($exists);
				}
				elseif($_POST['selectSubmit']) {
					if(($_POST['selectX'] >= 0 && $_POST['selectY'] >= 0 && $_POST['selectX'] < $widthBound && $_POST['selectY'] < $heightBound) || $_POST['selectGroupNumber']) {
						$clicked = $dbc->database_fetch_array($dbc->database_query("SELECT * FROM seating_seats WHERE x=" . $_POST['selectX'] . " AND y=" . $_POST['selectY']));
						if(is_array($clicked)) {
							if(!$_POST['selectShift']) $dbc->database_query("UPDATE seating_seats SET dev_selected=0");
							
							if($_POST['selectType'] == "all") $dbc->database_query("UPDATE seating_seats SET dev_selected=" . ($_POST['unselect'] ? 0 : 1));
							elseif($_POST['selectType'] == "seat") $dbc->database_query("UPDATE seating_seats SET dev_selected=" . ($_POST['unselect'] ? 0 : 1) . " WHERE id=" . $clicked['id']);		
							elseif($_POST['selectType'] == "group") $dbc->database_query("UPDATE seating_seats SET dev_selected=" . ($_POST['unselect'] ? 0 : 1) . " WHERE groupnum=" . $_POST['selectGroupNumber']);
							elseif($_POST['selectType'] == "contiguous") {
								unset($touched); 
								selectContiguous($clicked['x'],$clicked['y'],($_POST['unselect'] ? 0 : 1));
							}
						}
						else $error[] = "no seat exists at specified coordinates";
					}
					if(!$_POST['selectGroupNumber']) {
						if(!$_POST['selectX'] && $_POST['selectX'] != 0) $error[] = "No X coordinate specified for select.";
						elseif($_POST['selectX'] >= $widthBound) $error[] = "X coordinate outside bounds of room.";
						if(!$_POST['selectY'] && $_POST['selectY'] != 0) $error[] = "No Y coordinate specified for select.";
						elseif($_POST['selectY'] >= $widthBound) $error[] = "Y coordinate outside bounds of room.";
					}
				}
				elseif($_POST['moveSubmit']) {
					if($_POST['moveDistance']) {
						$offsetX = 0; $offsetY = 0;
						if($_POST['moveDirection'] == "up") $offsetY = -1 * $_POST['moveDistance'];
						if($_POST['moveDirection'] == "down") $offsetY = $_POST['moveDistance'];
						if($_POST['moveDirection'] == "left") $offsetX = -1 * $_POST['moveDistance'];
						if($_POST['moveDirection'] == "right") $offsetX = $_POST['moveDistance'];
						$ms = move_selected($offsetX,$offsetY);
						if(!empty($ms)) $error[] = "Seat(s) not moved because overlap occurred or room bounds were exceeded when moved.";
					}
					elseif($_POST['moveX'] >= 0 && $_POST['moveY'] >= 0 ){
						$image = find_leftmost_topmost();
						$offsetX = 0; $offsetY = 0;
						if($_POST['moveX'] < $image['x']) $offsetX = -1*($image['x'] - $_POST['moveX']);
						if($_POST['moveX'] > $image['x']) $offsetX = $_POST['moveX'] - $image['x'];
						if($_POST['moveY'] < $image['y']) $offsetY = -1*($image['y'] - $_POST['moveY']);
						if($_POST['moveY'] > $image['y']) $offsetY = $_POST['moveY'] - $image['y'];
						$ms = move_selected($offsetX,$offsetY);
						if(!empty($ms)) $error[] = "Overlap occured on move.";
					}
					else {
						if($_POST['moveX'] < 0) $error[] = "X destination cannot be negative for move.";
						if($_POST['moveY'] < 0) $error[] = "Y destination cannot be negative for move.";
						if(!$_POST['moveX']) $error[] = "no X destination specifed for move.";
						if(!$_POST['moveY']) $error[] = "no Y destination specified for move.";
					}
				
				}
				elseif($_POST['editSubmit']) {
					if($_POST['editSittable'] == "allSittable") $dbc->database_query("UPDATE seating_seats SET sittable=1 WHERE dev_selected=1");
					elseif($_POST['editSittable'] == "noneSittable") $dbc->database_query("UPDATE seating_seats SET sittable=0 WHERE dev_selected=1");
					if($_POST['editGroupNumber']) $dbc->database_query("UPDATE seating_seats SET groupnum=" . $_POST['editGroupNumber'] . " WHERE dev_selected=1");
				}
				elseif($_POST['otherSubmit']) {
					if($_POST['otherSelection'] == "deleteSelection") {
						$selected = $dbc->database_query("SELECT * FROM seating_seats WHERE dev_selected=1");
						while($s = $dbc->database_fetch_array($selected)) {
							$dbc->database_query("UPDATE users SET room_loc='' WHERE room_loc=" . $s['id']);
							$dbc->database_query("DELETE FROM seating_seats WHERE id=" . $s['id']);
						}
					}
				}
			}
		} 
		if($x->is_secure()) { 
			$x->display_top();
			?>
			<b>seating map</b>:<br />
			<br />
			<table border="0" cellpadding="0" cellspacing="3" align="center" class="centerd">
			<?php require_once("seating_map.php"); ?>
			<tr><td colspan="2">
				<table width="100%" border="0" cellpadding="0" cellspacing="3" class="sm" style="font-weight: bold; border: 1px solid <?php echo $colors["border"]; ?>">
				<tr><td colspan="2">legend:</td></tr>
				<tr>
					<td bgcolor="<?php echo $seat["currentcolor"]; ?>"><?php spacer(12,12); ?></td><td width="33%">current selection</td>
					<td bgcolor="<?php echo $seat["occupied"]; ?>"><?php spacer(12,12); ?></td><td width="33%">occupied</td>
					<td bgcolor="<?php echo $seat["reserved"]; ?>"><?php spacer(12,12); ?></td><td width="33%">reserved</td>
				</tr>
				</table>
			</td></tr>
			<?php if($control['dev_mode']&&current_security_level()>=2) { ?><tr><td>&nbsp;</td><td><img src="seating_ruler_horizontal.php" border="0"></td></tr><?php } ?>
			<tr><?php if($control['dev_mode']&&current_security_level()>=2) { ?><td><img src="seating_ruler_vertical.php" border="0"></td><?php } ?>
			<td><img src="seating_image.php<?php print ($currentID ? "?s=" . $currentID : ""); ?>" width="<?php print $control['room_width']; ?>" height="<?php print $control['room_height']; ?>" name="seat" usemap="<?php print ($control['dev_mode'] && current_security_level() >= 2 ? "#devmap" : "#usermap"); ?>" border="0" ismap></td></tr>
			
			</table><br /><?php
			if(current_security_level() >= 2) { ?>
				<table border="0" cellpadding="2" cellspacing="0" align="center" class="centerd">
				<tr><td><form action="<?php echo get_script_name(); ?>" method="POST">
				<input type="hidden" name="devToggle" value="<?php print ($control['dev_mode'] ? "TurnOff" : "TurnOn"); ?>">
				<input type="submit" value="Turn Development Mode <?php print ($control['dev_mode'] ? "Off" : "On"); ?>" class="formcolors">
				</form>
				</td></tr>
				</table><?php
			} ?>
			<br /><?php
			if(current_security_level() >=2 && $control['dev_mode'] == 1) { ?>
				<form action="<?php echo get_script_name(); ?>" method="POST" name="create">
				<table border="0" cellpadding="2" cellspacing="4" align="center" class="centerd"><?php
				if($error) { ?>
					<tr><td colspan="4"><b>Errors</b></td></tr>
					<tr><td colspan="4"><font color="red"><?php
					foreach($error AS $e) { ?>
						Error: <?php print $e; ?><br /><?php
					} ?>
					</font></td></tr><?php
				} ?>
				<tr><td colspan="2" bgcolor="<?php print $colors['cell_title']; ?>"><b>Create</b></td><td colspan="2" bgcolor="<?php print $colors['cell_title']; ?>"><b>Select</b></tr>
				<tr><td valign="top">
					<table border="0" cellpadding="1" cellspacing="0">
					<tr><td colspan="4" align="center"><b>Tiles</b></td></tr>
					<tr><td><input type="radio" class="radio" name="tileType" value="t" <?php print ($_POST['tileType'] == "t" ? "CHECKED" : ""); ?>></td>
					<td><img src="seating_seat_image.php?dir=T" border="0"></td>
					<td><input type="radio" class="radio" name="tileType" value="tl" <?php print ($_POST['tileType'] == "tl" ? "CHECKED" : ""); ?>></td>
					<td><img src="seating_seat_image.php?dir=TL" border="0"></td></tr>
					
					<tr><td><input type="radio" class="radio" name="tileType" value="r" <?php print ($_POST['tileType'] == "r" ? "CHECKED" : ""); ?>></td>
					<td><img src="seating_seat_image.php?dir=R" border="0"></td>
					<td><input type="radio" class="radio" name="tileType" value="tr" <?php print ($_POST['tileType'] == "tr" ? "CHECKED" : ""); ?>></td>
					<td><img src="seating_seat_image.php?dir=TR" border="0"></td></tr>
					
					<tr><td><input type="radio" class="radio" name="tileType" value="b" <?php print ($_POST['tileType'] == "b" ? "CHECKED" : ""); ?>></td>
					<td><img src="seating_seat_image.php?dir=B" border="0"></td>
					<td><input type="radio" class="radio" name="tileType" value="br" <?php print ($_POST['tileType'] == "br" ? "CHECKED" : ""); ?>></td>
					<td><img src="seating_seat_image.php?dir=BR" border="0"></td></tr>
					
					<tr><td><input type="radio" class="radio" name="tileType" value="l" <?php print ($_POST['tileType'] == "l" ? "CHECKED" : ""); ?>></td>
					<td><img src="seating_seat_image.php?dir=L" border="0"></td>
					<td><input type="radio" class="radio" name="tileType" value="bl" <?php print ($_POST['tileType'] == "bl" ? "CHECKED" : ""); ?>></td>
					<td><img src="seating_seat_image.php?dir=BL" border="0"></td></tr>
					<tr><td colspan="2" align="right"><input type="radio" class="radio" name="tileType" value="v" <?php print ($_POST['tileType'] == "v" ? "CHECKED" : ""); ?>></td>
					<td colspan="2" align="left">void</td></tr>
					</table>
				</td><td valign="top">
					<table border="0" cellpadding="1" cellspacing="0">
					<tr><td colspan="2"><input type="checkbox" class="radio" name="sittable" <?php print ($_POST['sittable'] ? "CHECKED" : ""); ?>> Sittable</td></tr>
					<tr><td colspan="2">group # <input type="text" name="groupNumber" size="6" maxlength="3" value="<?php print ($_POST['groupNumber'] ? $_POST['groupNumber'] : "0"); ?>"></td></tr><?php
					if($control['dev_javascript']) { ?>
						<input type="hidden" name="coordX" value="<?php print $_POST['coordX']; ?>">
						<input type="hidden" name="coordY" value="<?php print $_POST['coordY']; ?>"><?php
					} else { ?>
					<tr><td>X <input type="text" name="coordX" size="2" maxlength="3" value="<?php print $_POST['coordX']; ?>"></td>
					<td>Y <input type="text" name="coordY" size="2" maxlength="3" value="<?php print $_POST['coordY']; ?>"></td></tr><?php
					} ?>
					
					<!-- this is reserved for future use. eventually, admins may choose different tilesets on the same map
					<tr><td colspan="2">tileset <select name="tileset" size="1">
					<option value="1" <?php print ($_POST['tileset'] == 1 ? "SELECTED" : ""); ?>>default tileset</option>
					</select></td></tr>
					-->
					
					<input type="hidden" name="createSubmit" value="1"><?php
					if(!$control['dev_javascript']) { ?>
						<tr><td colspan="2" align="center"><input type="submit" value="Create" class="formcolors"></td></tr><?php
					} ?></form>
					</table>
				</td><form action="<?php echo get_script_name(); ?>" method="POST" name="select"><td>
					<table border="0" cellpadding="1" cellspacing="0">
					<tr><td><input type="radio" class="radio" name="selectType" value="seat" <?php print (($_POST['selectType'] == "seat" || !$_POST['selectType']) ? "CHECKED" : ""); ?>> seat</td></tr>
					<tr><td><input type="radio" class="radio" name="selectType" value="group" <?php print ($_POST['selectType'] == "group" ? "CHECKED" : ""); ?>> group</td></tr>
					<tr><td><input type="radio" class="radio" name="selectType" value="contiguous" <?php print ($_POST['selectType'] == "contiguous" ? "CHECKED" : ""); ?>> contiguous</td></tr>
					<tr><td><input type="radio" class="radio" name="selectType" value="all" <?php print ($_POST['selectType'] == "all" ? "CHECKED" : ""); ?>> all</td></tr>
					</table>
				</td><td valign="top">
					<table border="0" cellpadding="1" cellspacing="0">
					<tr><td colspan="2"><input type="checkbox" class="radio" name="selectShift" <?php print ($_POST['selectShift'] ? "CHECKED" : ""); ?>> Shift</td></tr>
					<tr><td colspan="2"><input type="checkbox" class="radio" name="unselect" <?php print ($_POST['unselect'] ? "CHECKED" : ""); ?>> Unselect</td></tr>
					<?php
					if($control['dev_javascript']) { ?>
						<input type="hidden" name="selectX" value="<?php print $_POST['selectX']; ?>">
						<input type="hidden" name="selectY" value="<?php print $_POST['selectY']; ?>"><?php
					} else { ?>
						<tr><td>X <input type="text" name="selectX" size="2" maxlength="3" value="<?php print $_POST['selectX']; ?>"></td>
						<td>Y <input type="text" name="selectY" size="2" maxlength="3" value="<?php print $_POST['selectY']; ?>"></td></tr><?php
					} ?>
					<tr><td colspan="2">group # <input type="text" name="selectGroupNumber" size="6" maxlength="3" value="<?php print $_POST['selectGroupNumber']; ?>"></td></tr>
					<input type="hidden" name="selectSubmit" value="1"><?php
					if(!$control['dev_javascript']) { ?>
						<tr><td colspan="2"><input type="submit" value="Select" class="formcolors"></td></tr><?php
					} ?>
					</table>
				</td></tr></form><?php
				if($dbc->database_num_rows($dbc->database_query("SELECT * FROM seating_seats WHERE dev_selected=1")) > 0) { ?>
				
					<tr><td colspan="4" bgcolor="<?php print $colors['cell_title']; ?>"><b>Current Selection</b></td></tr><form action="<?php echo get_script_name(); ?>" method="POST">
					<tr><td valign="top">
						<table border="0" cellpadding="1" cellspacing="0">
						<tr><td colspan="2"><b>move</b></td></tr>
						<tr><td colspan="2"><font size="2">move (leftmost<br />then topmost)<br />to:</font></td></tr>
						<?php $lt = find_leftmost_topmost(); ?>
						<tr><td>X <input type="text" name="moveX" size="2" maxlength="3" value="<?php print $lt['x']; ?>"></td><td>Y <input type="text" name="moveY" size="2" maxlength="3" value="<?php print $lt['y']; ?>"></td></tr>
						</table>
					</td>
					<td valign="top">
						<table border="0" cellpadding="1" cellspacing="0">
						<tr><td><b>or move</b></td></tr>
						<tr><td><font size="2">move selection</font></td></tr>
						<tr><td><select name="moveDirection" size="1">
							<option value="up">up</option>
							<option value="down">down</option>
							<option value="left">left</option>
							<option value="right">right</option></select></td></tr>
						<tr><td><input type="text" name="moveDistance" size="2" maxlength="3"> <font size="2">grid spaces</font></td></tr>
						<input type="hidden" name="moveSubmit" value="1">
						<tr><td><input type="submit" value="Move" class="formcolors"></td></tr></form>
						</table>
					</td><form action="<?php echo get_script_name(); ?>" method="POST">
					<td valign="top">
						<table border="0" cellpadding="1" cellspacing="0">
						<tr><td><b>edit</b></td></tr><?php
						$numSelected = $dbc->database_num_rows($dbc->database_query("SELECT * FROM seating_seats WHERE dev_selected=1"));
						$numSittable = $dbc->database_num_rows($dbc->database_query("SELECT * FROM seating_seats WHERE dev_selected=1 AND sittable=1")); ?>
						<tr><td><input type="radio" class="radio" name="editSittable" value="preserve" <?php print ($numSittable < $numSelected || !$_POST['editSittable'] || $numSelected == 0 ? "CHECKED" : ""); ?>> preserve sittability</td></tr>
						<tr><td><input type="radio" class="radio" name="editSittable" value="allSittable" <?php print ($numSittable == $numSelected ? "CHECKED" : ""); ?>> change to sittable</td></tr>
						<tr><td><input type="radio" class="radio" name="editSittable" value="noneSittable" <?php print ($numSittable == 0 ? "CHECKED" : ""); ?>> change to not sittable</td></tr>
						<tr><td>group # <input type="text" name="editGroupNumber" size="6" maxlength="3"></td></tr>
						
						<!-- future use
						<tr><td>tileset [select]</td></tr>
						-->
						
						<input type="hidden" name="editSubmit" value="1">
						<tr><td><input type="submit" value="Edit" class="formcolors"></td></tr></form>
						</table>
					</td><form action="<?php echo get_script_name(); ?>" method="POST">
					<td valign="top">
						<table border="0" cellpadding="1" cellspacing="0">
						<tr><td><b>other</b></td></tr>
						<tr><td><input type="radio" class="radio" name="otherSelection" value="deleteSelection"> Delete Selection</td></tr>
						<input type="hidden" name="otherSubmit" value="1">
						<tr><td><input type="submit" value="Go" class="formcolors"></td></tr></form>
						</table>
					</td></tr><?php
				} ?>
				<tr><td colspan="4" bgcolor="<?php print $colors['cell_title']; ?>"><b>Control</b></td></tr>
				<tr><td colspan="4" align="left" valign="top">
					<table border="0" cellpadding="1" cellspacing="0">
					<tr><td><form action="<?php echo get_script_name(); ?>" method="post">
					<input type="hidden" name="editRoomData" value="1">
					<input type="submit" value="Edit Room Data" class="formcolors"></form></td>
					<td><form action="<?php echo get_script_name(); ?>" method="post">
					<input type="hidden" name="gridToggle" value="<?php print ($control['grid'] ? "turnOff" : "turnOn"); ?>">
					<input type="submit" value="Turn Grid <?php print ($control['grid'] ? "Off" : "On"); ?>" class="formcolors"></form></td>
					<td><form action="<?php echo get_script_name(); ?>" method="post">
					<input type="hidden" name="javascriptToggle" value="<?php print ($control['dev_javascript'] ? "turnOff" : "turnOn"); ?>">
					<input type="submit" value="JavaScript Entry <?php print ($control['dev_javascript'] ? "Off" : "On"); ?>" class="formcolors"></form></td>
					</table>
				</td></tr>
				</table><br /><?php
			} else { ?>
				<table border="0" cellpadding="2" cellspacing="2" class="centerd">
				<tr><td bgcolor="<?php print $colors['cell_title']; ?>"><b>Data</b></td><td bgcolor="<?php print $colors['cell_title']; ?>"><b>Select</b></td><td bgcolor="<?php print $colors['cell_title']; ?>"><b>Current Selection</b></td></tr>
				<tr><td><?php
					$numSeats = $dbc->database_num_rows($dbc->database_query("SELECT * FROM seating_seats WHERE shape!='void' AND sittable=1"));
					//$numSittableSeats = $dbc->database_num_rows($dbc->database_query("SELECT * FROM seating_seats WHERE sittable=1"));
					$numSitting = $dbc->database_num_rows($dbc->database_query("SELECT * FROM users WHERE room_loc > 0")); ?>
					# seats: <?php print $numSeats; ?><br />
					<!--# sittable: <?php print $numSittableSeats; ?><br />-->
					# occupied: <?php print $numSitting; ?><br />
					# available: <?php print $numSeats - $numSitting; ?>
				</td>
				<td><form action="<?php echo get_script_name(); ?>" method="POST" name="currentForm">
					<table border="0" cellpadding="2" cellspacing="0">
					<input type="hidden" name="currentSubmit" value="1">					 	
						<tr><td>X <input type="text" name="currentX" size="3" maxlength="3"></td><td>Y <input type="text" name="currentY" size="3" maxlength="3"></td></tr>
						<tr><td colspan="2"><input type="submit" value="Select" class="formcolors"></td></tr><?php
					if($currentID) { ?><input type="hidden" name="currentID" value="<?php print $currentID; ?>"><?php } ?>
					</table>
				</form>	
				</td><td valign="top"><?php
				if($currentID) {
					$current = $dbc->database_fetch_array($dbc->database_query("SELECT * FROM seating_seats WHERE id="  .$currentID));
					$userHere = $dbc->database_fetch_array($dbc->database_query("SELECT * FROM users WHERE room_loc=" . $currentID));
					if(current_security_level() >= 1) {
						if(is_array($userHere) && $current['sittable'] && $current['shape'] != "void") {
							print $userHere['username'] . " is here.";
							if($userinfo['room_loc'] == $currentID || current_security_level() >= 2) { ?>
								<form action="<?php echo get_script_name(); ?>" method="post">
								<!-- EDIT userStandID to have the USERID of the user at this table!! -->
								<input type="hidden" name="userStandID" value="<?php print $userHere['userid']; ?>">
								<input type="hidden" name="currentID" value="<?php print $currentID; ?>">
								<input type="submit" value="Stand<?php print (current_security_level() >= 2 ? " " . $userHere['username'] : ""); ?> Up" class="formcolors">
								</form>
								<?php
							}
						} elseif($current['sittable'] && $current['shape'] != "void") { ?>
							<form action="<?php echo get_script_name(); ?>" method="post"><?php
							if(current_security_level() >= 2) { ?>
								<select name="userSitID" size="1"><?php
								$allUsers = $dbc->database_query("SELECT * FROM users ORDER BY username");
								while($u = $dbc->database_fetch_array($allUsers)) { ?>
									<option value="<?php print $u['userid']; ?>" <?php print ($u['userid'] == $userinfo['userid'] ? "SELECTED" : ""); ?>><?php print $u['username']; ?></option><?php
								} ?>
								</select><?php
							} else { ?>
								<input type="hidden" name="userSitID" value="<?php print $userinfo['userid']; ?>"><?php
							} ?>
							<input type="hidden" name="currentID" value="<?php print $currentID; ?>">
							<input type="submit" value="Sit Down" class="formcolors">
							</form>
							<?php
						}
					}
				} ?>
				&nbsp;</td></tr>
				</table><?php
			}
			$x->display_bottom();
		}
	} 
} elseif($toggle['seating']) {
	$x->display_top(); ?>
	<b>seating map</b>:<br />
	<br />
	the seating map hasn't been configured yet.<br />
	<br />
	<?php
	$x->display_bottom();
} else {
	$x->display_slim('you are not authorized to view this page.');
}
?>