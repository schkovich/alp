<?php
//need code here to draw border around void same color as room border
require_once("_config.php");
require_once("include/_functions.php");
require_once($master["currentskin"] . "_config.inc.php");
function grabcolors($hexval) {
	$hex = substr($hexval, 1);
	$ret["r"] = hexdec(substr($hex, 0, 2));
	$ret["g"] = hexdec(substr($hex, 2, 2));
	$ret["b"] = hexdec(substr($hex, 4, 2));
	return $ret;
}
$control = $dbc->database_fetch_array($dbc->database_query("SELECT * FROM seating_control"));
$objects = $dbc->database_query("SELECT * FROM seating_seats");
$corr = $control['pixelcorr'];
$img = imagecreate($control['room_width'], $control['room_height'])
	or die("cannot initialize new GD image stream.<br />administrator: please verify that the GD library is included in your PHP build.");
foreach($seat AS $id => $hexval) {
	$c = grabcolors($hexval);
	$imageColor[$id] = imagecolorallocate($img,$c['r'],$c['g'],$c['b']);
}
imagefill($img, 0, 0, $imageColor['background']);
imagerectangle($img,0,0,$control['room_width']-1,$control['room_height']-1,$imageColor['border']);
while($thisObject = $dbc->database_fetch_array($objects)) {
	$isUserHere = $dbc->database_num_rows($dbc->database_query("SELECT * FROM users WHERE room_loc=" . $thisObject['id']));
	if($thisObject['shape'] == "rect") {
		imagefilledrectangle($img,$thisObject['x']*$corr,$thisObject['y']*$corr,($thisObject['x']*$corr)+$corr,($thisObject['y']*$corr)+$corr,($thisObject['dev_selected'] || ($thisObject['id'] == $_GET['s']) ? $imageColor['currentcolor'] : ($isUserHere ? $imageColor['occupied'] : $imageColor['tablecolor'])));
		imagerectangle($img,$thisObject['x']*$corr,$thisObject['y']*$corr,($thisObject['x']*$corr)+$corr,($thisObject['y']*$corr)+$corr,$imageColor['tableborder']);
		if($thisObject['sittable']) {
			if($thisObject['direction'] == "t") {
				imagefilledrectangle($img,($thisObject['x']*$corr)+3,($thisObject['y']*$corr)+3,($thisObject['x']*$corr)+$corr-3,($thisObject['y']*$corr)+5,$imageColor['background']);
			}
			elseif($thisObject['direction'] == "r") {
				imagefilledrectangle($img,($thisObject['x']*$corr)+$corr-6,($thisObject['y']*$corr)+3,($thisObject['x']*$corr)+$corr-4,($thisObject['y']*$corr)+$corr-3,$imageColor['background']);
			}
			elseif($thisObject['direction'] == "b") {
				imagefilledrectangle($img,($thisObject['x']*$corr)+3,($thisObject['y']*$corr)+$corr-6,($thisObject['x']*$corr)+$corr-4,($thisObject['y']*$corr)+$corr-4,$imageColor['background']);
			}
			elseif($thisObject['direction'] == "l") {
				imagefilledrectangle($img,($thisObject['x']*$corr)+3,($thisObject['y']*$corr)+3,($thisObject['x']*$corr)+5,($thisObject['y']*$corr)+$corr-4,$imageColor['background']);
			}
		}
	}
	elseif($thisObject['shape'] == "circ") {
		if($thisObject['direction'] == "tl") {
			imagefilledarc($img,($thisObject['x']*$corr)+$corr,($thisObject['y']*$corr)+$corr,2*$corr,2*$corr,180,270,($thisObject['dev_selected'] || ($thisObject['id'] == $_GET['s']) ? $imageColor['currentcolor'] : ($isUserHere ? $imageColor['occupied'] : $imageColor['tablecolor'])),IMG_ARC_EDGED);
			imagefilledarc($img,($thisObject['x']*$corr)+$corr,($thisObject['y']*$corr)+$corr,2*$corr,2*$corr,180,270,$imageColor['tableborder'],IMG_ARC_EDGED | IMG_ARC_NOFILL);
			if($thisObject['sittable']) {
				imagefilledarc($img,($thisObject['x']*$corr)+$corr-3,($thisObject['y']*$corr)+$corr-3,($corr*2)-12,($corr*2)-12,180,270,$imageColor['background'],IMG_ARC_EDGED);
				imagefilledarc($img,($thisObject['x']*$corr)+$corr-3,($thisObject['y']*$corr)+$corr-3,($corr*2)-18,($corr*2)-18,180,270,($thisObject['dev_selected'] || ($thisObject['id'] == $_GET['s']) ? $imageColor['currentcolor'] : ($isUserHere ? $imageColor['occupied'] : $imageColor['tablecolor'])),IMG_ARC_EDGED);
			}
		}
		elseif($thisObject['direction'] == "tr") {
			imagefilledarc($img,($thisObject['x']*$corr),($thisObject['y']*$corr)+$corr,2*$corr,2*$corr,270,0,($thisObject['dev_selected'] || ($thisObject['id'] == $_GET['s']) ? $imageColor['currentcolor'] : ($isUserHere ? $imageColor['occupied'] : $imageColor['tablecolor'])),IMG_ARC_EDGED);
			imagefilledarc($img,($thisObject['x']*$corr),($thisObject['y']*$corr)+$corr,2*$corr,2*$corr,270,0,$imageColor['tableborder'],IMG_ARC_EDGED | IMG_ARC_NOFILL);
			if($thisObject['sittable']) {
				imagefilledarc($img,($thisObject['x']*$corr)+3,($thisObject['y']*$corr)+$corr-3,($corr*2)-12,($corr*2)-12,270,0,$imageColor['background'],IMG_ARC_EDGED);
				imagefilledarc($img,($thisObject['x']*$corr)+3,($thisObject['y']*$corr)+$corr-3,($corr*2)-18,($corr*2)-18,270,0,($thisObject['dev_selected'] || ($thisObject['id'] == $_GET['s']) ? $imageColor['currentcolor'] : ($isUserHere ? $imageColor['occupied'] : $imageColor['tablecolor'])),IMG_ARC_EDGED);
			}
		}
		elseif($thisObject['direction'] == "br") {
			imagefilledarc($img,($thisObject['x']*$corr),($thisObject['y']*$corr),2*$corr,2*$corr,0,90,($thisObject['dev_selected'] || ($thisObject['id'] == $_GET['s']) ? $imageColor['currentcolor'] : ($isUserHere ? $imageColor['occupied'] : ($isUserHere ? $imageColor['occupied'] : $imageColor['tablecolor']))),IMG_ARC_EDGED);
			imagefilledarc($img,($thisObject['x']*$corr),($thisObject['y']*$corr),2*$corr,2*$corr,0,90,$imageColor['tableborder'],IMG_ARC_EDGED | IMG_ARC_NOFILL);
			if($thisObject['sittable']) {
				imagefilledarc($img,($thisObject['x']*$corr)+3,($thisObject['y']*$corr)+3,($corr*2)-12,($corr*2)-12,0,90,$imageColor['background'],IMG_ARC_EDGED);
				imagefilledarc($img,($thisObject['x']*$corr)+3,($thisObject['y']*$corr)+3,($corr*2)-18,($corr*2)-18,0,90,($thisObject['dev_selected'] || ($thisObject['id'] == $_GET['s']) ? $imageColor['currentcolor'] : ($isUserHere ? $imageColor['occupied'] : $imageColor['tablecolor'])),IMG_ARC_EDGED);
			}
		}
		elseif($thisObject['direction'] == "bl") {
			imagefilledarc($img,($thisObject['x']*$corr)+$corr,($thisObject['y']*$corr),2*$corr,2*$corr,90,180,($thisObject['dev_selected'] || ($thisObject['id'] == $_GET['s']) ? $imageColor['currentcolor'] : ($isUserHere ? $imageColor['occupied'] : $imageColor['tablecolor'])),IMG_ARC_EDGED);
			imagefilledarc($img,($thisObject['x']*$corr)+$corr,($thisObject['y']*$corr),2*$corr,2*$corr,90,180,$imageColor['tableborder'],IMG_ARC_EDGED | IMG_ARC_NOFILL);
			if($thisObject['sittable']) {
				imagefilledarc($img,($thisObject['x']*$corr)+$corr-3,($thisObject['y']*$corr)+3,($corr*2)-12,($corr*2)-12,90,180,$imageColor['background'],IMG_ARC_EDGED);
				imagefilledarc($img,($thisObject['x']*$corr)+$corr-3,($thisObject['y']*$corr)+3,($corr*2)-18,($corr*2)-18,90,180,($thisObject['dev_selected'] || ($thisObject['id'] == $_GET['s']) ? $imageColor['currentcolor'] : ($isUserHere ? $imageColor['occupied'] : $imageColor['tablecolor'])),IMG_ARC_EDGED);
			}
		}
	}
	elseif($thisObject['shape'] == "void") {
		imagefilledrectangle($img,$thisObject['x']*$corr,$thisObject['y']*$corr,($thisObject['x']*$corr)+$corr-1,($thisObject['y']*$corr)+$corr-1,($thisObject['dev_selected'] || ($thisObject['id'] == $_GET['s']) ? $imageColor['currentcolor'] : $imageColor['voidcolor']));
		if($dbc->database_num_rows($dbc->database_query("SELECT * FROM seating_seats WHERE x=" . $thisObject['x'] . " AND y=" . ($thisObject['y']-1))) == 0) imageline($img,($thisObject['x']*$corr),($thisObject['y']*$corr),(($thisObject['x']*$corr)+$corr),($thisObject['y']*$corr),$imageColor['border']);
		if($dbc->database_num_rows($dbc->database_query("SELECT * FROM seating_seats WHERE x=" . ($thisObject['x'] + 1) . " AND y=" . $thisObject['y'])) == 0) imageline($img,(($thisObject['x']*$corr)+$corr),($thisObject['y']*$corr),(($thisObject['x']*$corr)+$corr),(($thisObject['y']*$corr)+$corr),$imageColor['border']);
		if($dbc->database_num_rows($dbc->database_query("SELECT * FROM seating_seats WHERE x=" . $thisObject['x'] . " AND y=" . ($thisObject['y']+1))) == 0) imageline($img,($thisObject['x']*$corr),(($thisObject['y']*$corr)+$corr),(($thisObject['x']*$corr)+$corr),(($thisObject['y']*$corr)+$corr),$imageColor['border']);
		if($dbc->database_num_rows($dbc->database_query("SELECT * FROM seating_seats WHERE x=" . ($thisObject['x']-1) . " AND y=" . $thisObject['y'])) == 0) imageline($img,($thisObject['x']*$corr),($thisObject['y']*$corr),($thisObject['x']*$corr),(($thisObject['y']*$corr)+$corr),$imageColor['border']);
	}
	//if($thisObject['dev_selected']) imagerectangle($img,$thisObject['x']*$corr,$thisObject['y']*$corr,($thisObject['x']*$corr)+$corr-1,($thisObject['y']*$corr)+$corr-1,$color['selected']);
}
if($control['grid']) {
	for($i = 0; $i < $control['room_width']; $i+=$corr) {
		if($i != 0) imageline($img,$i,0,$i,$control['room_height'],$imageColor['gridcolor']);
	}
	for($i = 0; $i < $control['room_height']; $i+=$corr) {
		if($i != 0) imageline($img,0,$i,$control['room_width'],$i,$imageColor['gridcolor']);
	}
}
if (imagetypes() & IMG_GIF) {
    header ("Content-type: image/gif");
    imagegif ($img);
}
elseif(imagetypes() & IMG_PNG) {
	header("Content-type: image/png");
	imagepng($img);
}
elseif (imagetypes() & IMG_JPG) {
	header("Content-type: image/jpeg");
	imagejpeg($img, "", 100);
}
elseif(imagetypes() & IMG_WBMP) {
	header("Content-type: image/vnd.wap.wbmp");
	imagebmp($img);
}
else die("No image support in this PHP server");
//imagepng($img); // change to gif !
imagedestroy($img);
?>