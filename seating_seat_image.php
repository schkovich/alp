<?php
require_once '_config.php';
require_once 'include/_functions.php';
require_once $master['currentskin'] . '_config.inc.php';

function grabcolors($hexval)
{
	$hex = substr($hexval, 1);
	$ret["r"] = hexdec(substr($hex, 0, 2));
	$ret["g"] = hexdec(substr($hex, 2, 2));
	$ret["b"] = hexdec(substr($hex, 4, 2));
	return $ret;
}
$control = $dbc->database_fetch_array($dbc->database_query("SELECT * FROM seating_control"));
$objects = $dbc->database_query("SELECT * FROM seating_seats");
$corr = $control['pixelcorr'];
$img = imagecreate($corr,$corr)
	or die("cannot initialize new GD image stream.<br />administrator: please verify that the GD library is included in your PHP build.");
foreach($seat AS $id => $hexval) {
	$c = grabcolors($hexval);
	$imageColor[$id] = imagecolorallocate($img,$c['r'],$c['g'],$c['b']);
}
imagefill($img, 0, 0, $imageColor['background']);
if($_GET['dir'] == 'T') {
	imagefilledrectangle($img,0,0,$corr-1,$corr-1,$imageColor['tablecolor']);
	imagerectangle($img,0,0,$corr-1,$corr-1,$imageColor['tableborder']);
	imagefilledrectangle($img,3,3,$corr-4,5,$imageColor['background']);
}
elseif($_GET['dir'] == 'R') {
	imagefilledrectangle($img,0,0,$corr-1,$corr-1,$imageColor['tablecolor']);
	imagerectangle($img,0,0,$corr-1,$corr-1,$imageColor['tableborder']);
	imagefilledrectangle($img,$corr-6,3,$corr-4,$corr-4,$imageColor['background']);
}
elseif($_GET['dir'] == 'B') {
	imagefilledrectangle($img,0,0,$corr-1,$corr-1,$imageColor['tablecolor']);
	imagerectangle($img,0,0,$corr-1,$corr-1,$imageColor['tableborder']);
	imagefilledrectangle($img,3,$corr-6,$corr-4,$corr-4,$imageColor['background']);
}
elseif($_GET['dir'] == 'L') {
	imagefilledrectangle($img,0,0,$corr-1,$corr-1,$imageColor['tablecolor']);
	imagerectangle($img,0,0,$corr-1,$corr-1,$imageColor['tableborder']);
	imagefilledrectangle($img,3,3,5,$corr-4,$imageColor['background']);
}
elseif($_GET['dir'] == 'TL') {
	imagefilledarc($img,$corr,$corr,2*$corr,2*$corr,90,0,$imageColor['tablecolor'],IMG_ARC_EDGED);
	imagefilledarc($img,$corr-3,$corr-3,($corr*2)-12,($corr*2)-12,90,0,$imageColor['background'],IMG_ARC_EDGED);
	imagefilledarc($img,$corr-3,$corr-3,($corr*2)-18,($corr*2)-18,90,0,$imageColor['tablecolor'],IMG_ARC_EDGED);
}
elseif($_GET['dir'] == 'TR') {
	imagefilledarc($img,0,$corr,2*$corr,2*$corr,270,0,$imageColor['tablecolor'],IMG_ARC_EDGED);
	imagefilledarc($img,3,$corr-3,($corr*2)-12,($corr*2)-12,270,0,$imageColor['background'],IMG_ARC_EDGED);
	imagefilledarc($img,3,$corr-3,($corr*2)-18,($corr*2)-18,270,0,$imageColor['tablecolor'],IMG_ARC_EDGED);
}
elseif($_GET['dir'] == 'BR') {
	imagefilledarc($img,0,0,2*$corr,2*$corr,0,90,$imageColor['tablecolor'],IMG_ARC_EDGED);
	imagefilledarc($img,3,3,($corr*2)-12,($corr*2)-12,0,90,$imageColor['background'],IMG_ARC_EDGED);
	imagefilledarc($img,3,3,($corr*2)-18,($corr*2)-18,0,90,$imageColor['tablecolor'],IMG_ARC_EDGED);
}
elseif($_GET['dir'] == 'BL') {
	imagefilledarc($img,$corr,0,2*$corr,2*$corr,90,180,$imageColor['tablecolor'],IMG_ARC_EDGED);
	imagefilledarc($img,$corr-3,3,($corr*2)-12,($corr*2)-12,90,180,$imageColor['background'],IMG_ARC_EDGED);
	imagefilledarc($img,$corr-3,3,($corr*2)-18,($corr*2)-18,90,180,$imageColor['tablecolor'],IMG_ARC_EDGED);
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