<?php
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
$img = imagecreate($control['room_width'], 15)
	or die("cannot initialize new GD image stream.<br />administrator: please verify that the GD library is included in your PHP build.");
foreach($seat AS $id => $hexval) {
	$c = grabcolors($hexval);
	$imageColor[$id] = imagecolorallocate($img,$c['r'],$c['g'],$c['b']);
}
$imageColor['text'] = grabcolors($colors['text']);
imagefill($img, 0, 0, $imageColor['background']);
for($i = 0,$j = 0; $i < $control['room_width']; $i+=$corr,$j++) {
	imageline($img,$i,0,$i,15,$imageColor['gridcolor']);
	imagestring($img,1,$i+($corr/2)-6,3,$j,$imageColor['text']);
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