<?php 
$control = $dbc->database_fetch_array($dbc->database_query("SELECT * FROM seating_control"));
$objects = $dbc->database_query("SELECT * FROM seating_seats");

$corr = $control['pixelcorr'];

// this puts all of the seats into an array ($objectArray[x][y]) that can be tested for existence (boolean-style)
// it also arrays the data ($objectData[x][y]) --see keys in array declaration
unset($objectArray);
while($thisObject = $dbc->database_fetch_array($objects)) {
	$objectArray[$thisObject['x']][$thisObject['y']] = 1;
    $objectData[$thisObject['x']][$thisObject['y']] = array('typeID' => ''/*$thisObject['typeID']*/,'reservedID' => $thisObject['reservedID'], 'userID' => ''/*$thisObject['userID']*/, 'groupnum' => $thisObject['groupnum']);
} ?>

<!-- this is the administrator DEV map -->
<map name="devmap"><?php
for($i = 0; $i < ($control['room_height'] / $corr); $i++) {
	for($j = 0; $j < ($control['room_width'] / $corr); $j++) {
		unset($onClickData);
		if($objectArray[$j][$i]) {
			$onClickData = "document.select.selectX.value=" . $j . ";document.select.selectY.value=" . $i . ";document.select.selectGroupNumber.value=" . ($objectData[$j][$i]['groupnum'] ? $objectData[$j][$i]['groupnum'] : "''") . ";document.select.submit();";
		} else {
			$onClickData = "document.create.coordX.value=" . $j . ";document.create.coordY.value=" . $i . ";document.create.submit()";
		}?>
		<area shape="rect" coords="<?php print ($j*$corr) . "," . $i*$corr . "," . ($j+1)*$corr . "," . ($i+1)*$corr; ?>" alt="<?php print $j . "," . $i; ?>" onClick="<?php print $onClickData; ?>"><?php
	}
} ?>
</map>


<!-- this is the normal USER map -->
<map name="usermap"><?php
for($i = 0; $i < ($control['room_height'] / $corr); $i++) {
	for($j = 0; $j < ($control['room_width'] / $corr); $j++) {
		if($objectArray[$j][$i]) {
			if($objectData[$j][$i]['reservedID']) $alt = "reserved."; // eventually, add username here (who it's reserved for)
			elseif($objectData[$j][$i]['userID']) $alt = "user here."; // see above comment ?>
			<area shape="rect" coords="<?php print ($j*$corr) . "," . $i*$corr . "," . ($j+1)*$corr . "," . ($i+1)*$corr; ?>" alt="<?php print $alt; ?>" onClick="document.currentForm.currentX.value=<?php print $j; ?>;document.currentForm.currentY.value=<?php print $i; ?>;document.currentForm.submit();"><?php
		}
	}
} ?>
</map>