<?php
require_once 'include/_universal.php';
$x = new universal('servers','',0);
if($toggle['servers']&& $_GET['ip'] && $_GET['port'] && $_GET['enginetype'] && $x->is_secure()) {
	$x->display_top();
$displaytips = 0;

/*
error_reporting(0);
// redefine the user error constants - PHP 4 only
define("FATAL", E_ERROR);
define("ERROR", E_WARNING);
define("WARNING", E_NOTICE);
define("OTHER", E_PARSE);
 error handler function
*/

function myErrorHandler($errno, $errstr, $errfile, $errline) 
{
  echo "<!--";  
switch ($errno) {
  case FATAL:
   echo "<b>FATAL</b> [$errno] $errstr<br />\n";
   echo "  Fatal error in line $errline of file $errfile";
   echo ", PHP " . PHP_VERSION . " (" . PHP_OS . ")<br />\n";
   echo "</body></html>";
   die();
   break;
  case ERROR:
   echo "<b>ERROR</b> [$errno] $errstr<br />\n";
   echo "  Error in line $errline of file $errfile";
   echo ", PHP " . PHP_VERSION . " (" . PHP_OS . ")<br />\n";
   break;
  case WARNING:
   echo "<b>WARNING</b> [$errno] $errstr<br />\n";
   echo "  Non-Fatal error in line $errline of file $errfile";
   echo ", PHP " . PHP_VERSION . " (" . PHP_OS . ")<br />\n";
   break;
 case OTHER:
   echo "<b>PARSE</b> [$errno] $errstr<br />\n";
   echo "  Parse error in line $errline of file $errfile";
   echo ", PHP " . PHP_VERSION . " (" . PHP_OS . ")<br />\n";
   break;
  default:
  echo $errno;
  break;
  }
  echo "-->";
}

// set to the user defined error handler
$old_error_handler = set_error_handler("myErrorHandler");

function showFavorites() {
	global $favorites;
	$cnt = count($favorites);
	if ($cnt > 0)
		echo "(";
	for ($i=0;$i<$cnt;$i++) {
		$z = explode(",", $favorites[$i]);
		echo " <a href=\"".get_script_name()."?name=SQuery&ip=$z[1]&port=$z[2]&game=$z[3]&block=0\" class=\"link\">$z[0]</a> ";
		if ($i+1 < $cnt) echo checkmark();
	}
	if ($cnt > 0)
		echo ")";
}

$blockmode=0;
$ip = $_GET['ip']; $port = $_GET['port'];$enginetype = $_GET['enginetype']; 
//$blockmode = $_GET['block'];

// require our main library =)
require_once 'include/_squery.php';

//////////////////////////////////////////////////////////

//<BODY LEFTMARGIN=0 TOPMARGIN=0 MARGINWIDTH=0 MARGINHEIGHT=0>
?>

<div id="overDiv" style="position:absolute; visibility:hidden; z-index:1000;"></div>
<script language="JavaScript" src="<?php echo $libpath; ?>overlib.js"><!-- overLIB (c) Erik Bosrup --></script> 
<br />
<TABLE BORDER=0 CELLSPACING=0 CELLPADDING=3 width=100%>
<TR><TD align=center>
<?php

if ($ip && $port) {
	?>
	<TABLE BORDER=0 CELLSPACING=0 CELLPADDING=3 class="main_table" align=center>
	<TR>
	<TD align=center>
	<?php
    $qport=$port;
	$gameserver=queryServer($ip,$qport,$enginetype,TRUE,TRUE);

	if ($gameserver) {
	?>
		<strong class="big"><?php echo gametitle(htmlentities($gameserver->gamename)); ?></strong><br />
		<TABLE cellpadding=0 cellspacing=0 width="550">
		<tr><td valign=top><br />
		<table width="100%" cellspacing=0 cellpadding=3 align="center">
		<tr><td width="100%" align="left" valign=top style="padding-left: 10px; padding-right: 10px;">
		<table cellspacing=0 cellpadding=2>
		<tr><td align="left" class="row"><font class="color"><strong>Server name:</strong></font></td><td align="left" class="row"><?php echo $gameserver->htmlize($gameserver->servertitle); ?></td>
		</tr>
		<tr><td align="left" class="row"><font class="color"><strong>Server Address:</strong></font></td><td align="left" class="row"><?php echo $gameserver->address; ?>:<?php echo$gameserver->hostport; ?>&nbsp;&nbsp;&nbsp;</td>
		</tr>
		<tr><td align="left" class="row"><font class="color"><strong>Server Version:</strong></font></td><td align="left" class="row"><?php echo htmlentities($gameserver->gameversion); ?></td>
		</tr>
		<tr><td align="left" class="row"><font class="color"><strong>Players:</strong></font></td><td align="left" class="row"><?php echo $gameserver->numplayers; ?> / <?php echo$gameserver->maxplayers;
	
		/*
		if ($gameserver->numplayers == $gameserver->maxplayers)
			echo "&nbsp;&nbsp;&nbsp;(<font class=\"color\">This server is FULL</font>)";
		elseif ($gameserver->numplayers == 0)
			echo "&nbsp;&nbsp;&nbsp;(<font class=\"color\">This server is EMPTY</font>)";
		*/
		?></td>
		</tr>
<?php
if ($gameserver->rules[".admin"]<>"") 
{ ?>
<tr><td align="left" class="row"><font class ="color"><strong>Admin Name:</strong></font></td>
<td align="left" class="row"><?php echo $gameserver->rules[".admin"]; ?></td>
</tr>
<?php }
if ($gameserver->rules["admin"]<>"") 
{ ?>
<tr><td align="left" class="row"><font class ="color"><strong>Admin Name:</strong></font></td>
<td align="left" class="row"><?php echo $gameserver->rules["admin"]; ?></td>
</tr>
<?php }
if ($gameserver->rules["adminname"]<>"") 
{ ?>
<tr><td align="left" class="row"><font class ="color"><strong>Admin Name:</strong></font></td>
<td align="left" class="row"><?php echo $gameserver->rules["adminname"]; ?></td>
</tr>
<?php }
if ($gameserver->rules["admin name"]<>"") 
{ ?>
<tr><td align="left" class="row"><font class ="color"><strong>Admin Name:</strong></font></td>
<td align="left" class="row"><?php echo $gameserver->rules["admin name"]; ?></td>
</tr>
<?php }
if ($gameserver->rules["administrator"]<>"") 
{ ?>
<tr><td align="left" class="row"><font class ="color"><strong>Admin Name:</strong></font></td>
<td align="left" class="row"><?php echo $gameserver->rules["administrator"]; ?></td>
</tr>
<?php }
if ($gameserver->rules[".administrator"]<>"") 
{ ?>
<tr><td align="left" class="row"><font class ="color"><strong>Admin Name:</strong></font></td>
<td align="left" class="row"><?php echo $gameserver->rules[".administrator"]; ?></td>
</tr>
<?php }
if ($gameserver->rules[".email"]<>"") 
{ ?>
<tr><td align="left" class="row"><font class ="color"><strong>Admin Email:</strong></font></td>
<td align="left" class="row"><?php echo $gameserver->rules[".email"]; ?></td>
</tr>
<?php }
if ($gameserver->rules["sv_contact"]<>"") 
{ ?>
<tr><td align="left" class="row"><font class ="color"><strong>Admin Email:</strong></font></td>
<td align="left" class="row"><?php echo $gameserver->rules["sv_contact"]; ?></td>
</tr>
<?php }
if ($gameserver->rules["adminemail"]<>"") 
{ ?>
<tr><td align="left" class="row"><font class ="color"><strong>Admin Email:</strong></font></td>
<td align="left" class="row"><?php echo $gameserver->rules["adminemail"]; ?></td>
</tr>
<?php }
if ($gameserver->rules["admin email"]<>"") 
{ ?>
<tr><td align="left" class="row"><font class ="color"><strong>Admin Email:</strong></font></td>
<td align="left" class="row"><?php echo $gameserver->rules["admin email"]; ?></td>
</tr>
<?php }
if ($gameserver->rules["admin e-mail"]<>"") 
{ ?>
<tr><td align="left" class="row"><font class ="color"><strong>Admin Email:</strong></font></td>
<td align="left" class="row"><?php echo $gameserver->rules["admin e-mail"]; ?></td>
</tr>
<?php }
if ($gameserver->rules[".e-mail"]<>"") 
{ ?>
<tr><td align="left" class="row"><font class ="color"><strong>Admin Email:</strong></font></td>
<td align="left" class="row"><?php echo $gameserver->rules[".e-mail"]; ?></td>
</tr>
<?php }
if ($gameserver->rules[".icq"]<>"") 
{ ?>
<tr><td align="left" class="row"><font class ="color"><strong>Admin ICQ:</strong></font></td>
<td align="left" class="row"><?php echo $gameserver->rules[".icq"]; ?></td>
</tr>
<?php }
if ($gameserver->rules["icq"]<>"") 
{ ?>
<tr><td align="left" class="row"><font class ="color"><strong>Admin ICQ:</strong></font></td>
<td align="left" class="row"><?php echo $gameserver->rules["icq"]; ?></td>
</tr>
<?php }
if ($gameserver->rules[".website"]<>"") 
{ ?>
<tr><td align="left" class="row"><font class ="color"><strong>Website:</strong></font></td>
<td align="left" class="row"><?php echo $gameserver->rules[".website"]; ?></td>
</tr>
<?php }
if ($gameserver->rules[".location"]<>"") 
{ ?>
<tr><td align="left" class="row"><font class ="color"><strong>Server Location:</strong></font></td>
<td align="left" class="row"><?php echo $gameserver->rules[".location"]; ?></td>
</tr>
<?php } 
if ($gameserver->rules["location"]<>"") 
{ ?>
<tr><td align="left" class="row"><font class ="color"><strong>Server Location:</strong></font></td>
<td align="left" class="row"><?php echo $gameserver->rules["location"]; ?></td>
</tr>
<?php } 
if ($gameserver->rules["email"]<>"") 
{ ?>
<tr><td align="left" class="row"><font class ="color"><strong>Admin Email:</strong></font></td>
<td align="left" class="row"><?php echo $gameserver->rules["email"]; ?></td>
</tr>
<?php }
if ($gameserver->rules[".url"]<>"") 
{ ?>
<tr><td align="left" class="row"><font class ="color"><strong>Website:</strong></font></td>
<td align="left" class="row"><?php echo $gameserver->rules[".url"]; ?></td>
</tr>
<?php }
if ($gameserver->rules["web"]<>"") 
{ ?>
<tr><td align="left" class="row"><font class ="color"><strong>Website:</strong></font></td>
<td align="left" class="row"><?php echo $gameserver->rules["web"]; ?></td>
</tr>
<?php }
if ($gameserver->rules["webpage"]<>"") 
{ ?>
<tr><td align="left" class="row"><font class ="color"><strong>Website:</strong></font></td>
<td align="left" class="row"><?php echo $gameserver->rules["webpage"]; ?></td>
</tr>
<?php }
if ($gameserver->rules["url"]<>"") 
{ ?>
<tr><td align="left" class="row"><font class ="color"><strong>Website:</strong></font></td>
<td align="left" class="row"><?php echo $gameserver->rules["url"]; ?></td>
</tr>
<?php }
if ($gameserver->rules[".irc"]<>"") 
{ ?>
<tr><td align="left" class="row"><font class ="color"><strong>IRC Channel:</strong></font></td>
<td align="left" class="row"><?php echo $gameserver->rules[".irc"]; ?></td>
</tr>
<?php }
if ($gameserver->rules["irc"]<>"") 
{ ?>
<tr><td align="left" class="row"><font class ="color"><strong>IRC Channel:</strong></font></td>
<td align="left" class="row"><?php echo $gameserver->rules["irc"]; ?></td>
</tr>
<?php }
if ($gameserver->rules["cpu"]<>"") 
{ ?>
<tr><td align="left" class="row"><font class ="color"><strong>CPU:</strong></font></td>
<td align="left" class="row"><?php echo $gameserver->rules["cpu"]; ?></td>
</tr>
<?php }
if ($gameserver->rules[".cpu"]<>"") 
{ ?>
<tr><td align="left" class="row"><font class ="color"><strong>CPU:</strong></font></td>
<td align="left" class="row"><?php echo $gameserver->rules[".cpu"]; ?></td>
</tr>
<?php }
if ($gameserver->rules["server spec"]<>"") 
{ ?>
<tr><td align="left" class="row"><font class ="color"><strong>CPU:</strong></font></td>
<td align="left" class="row"><?php echo $gameserver->rules["server spec"]; ?></td>
</tr>
<?php }
if ($gameserver->rules["connection"]<>"") 
{ ?>
<tr><td align="left" class="row"><font class ="color"><strong>Connection:</strong></font></td>
<td align="left" class="row"><?php echo $gameserver->rules["connection"]; ?></td>
</tr>
<?php }
if ($gameserver->rules["gamestartup"]<>"") 
{ ?>
<tr><td align="left" class="row"><font class ="color"><strong>Last Boot:</strong></font></td>
<td align="left" class="row"><?php echo $gameserver->rules["gamestartup"]; ?></td>
</tr>
<?php }
if ($gameserver->rules["gameversion"]<>"") 
{ ?>
<tr><td align="left" class="row"><font class ="color"><strong>Game Ver:</strong></font></td>
<td align="left" class="row"><?php echo $gameserver->rules["gameversion"]; ?></td>
</tr>
<?php }
if ($gameserver->rules["plug"]<>"") 
{ ?>
<tr><td align="left" class="row"><font class ="color"><strong>Motto:</strong></font></td>
<td align="left" class="row"><?php echo $gameserver->rules["plug"]; ?></td>
</tr>
<?php }
if ($gameserver->rules["motd"]<>"") 
{ ?>
<tr><td align="left" class="row"><font class ="color"><strong>Motto:</strong></font></td>
<td align="left" class="row"><?php echo $gameserver->rules["motd"]; ?></td>
</tr>
<?php }

?>
		</table>
		<br />
		<table cellspacing=0 cellpadding=0 width="100%">
		<tr><td class="row">
		<font class="color"><?php

switch($gameserver->password)
{
case "1":
echo "This server requires a password to join</font> (Private Server)";
break;
case"0":
echo "This server is open to the public </font>(No password)";
break;
default:
echo "Server Password Setting is Unknown.";
break;
}

?></td></tr>
		</table>
		<br />

<?php echo $gameserver->docvars($gameserver);
/////////////////////////////////////////////////////////
// function is called, sees server type, creates a pathname to pictures based on mapname and server type.

$serveradd = $ip.":".$port;
$dir = $dbc->queryOne("SELECT `g`.`thumbs_dir` FROM `games` g LEFT OUTER JOIN `servers` s ON `g`.`gameid` = `s`.`gameid` WHERE `s`.`ipaddress` = '$serveradd'");
if(!$dir)
	$dir = $dbc->queryOne("SELECT `g`.`thumbs_dir` FROM `games` g LEFT OUTER JOIN `game_requests` s ON `g`.`gameid` = `s`.`gameid` WHERE `s`.`ipaddress` = '$serveradd'");
$map = strtolower($gameserver->mapname);
if($dir) {
	if(file_exists('img/map_thumbnails/'.$dir.'/'.$map.'.jpg')) {
		$mappic = 'img/map_thumbnails/'.$dir.'/'.$map.'.jpg';
	} elseif (file_exists('img/map_thumbnails/'.$dir.'/'.$map.'.gif')) {
		$mappic = 'img/map_thumbnails/'.$dir.'/'.$map.'.gif';
	} elseif (file_exists('img/map_thumbnails/'.$dir.'/'.$map.'.png')) {
		$mappic = 'img/map_thumbnails/'.$dir.'/'.$map.'.png';
	} else {
		$mappic = 'img/map_thumbnails/unknown.gif'; 
	}
} else {
	$mappic = "img/map_thumbnails/unknown.gif";
}

//$mappic=domappic($gameserver);
// if the picture isn't there, sets to unknown.gif.
///////////////////////////////////////////////////////////////////////////////////
?>

		</TD><td width="20%" valign="top" style="padding-left: 10px; padding-right: 10px;">
		<img src="<?php echo$mappic?>" alt="<?php echo htmlentities($gameserver->maptitle); ?>" width="175" height="143" style="border: 1px solid #000000;"><br />
		<br />
		<table width="100%" cellspacing=1 cellpadding=1>
		<?php $gameserver->mapname=ucwords(htmlentities($gameserver->mapname)); ?>
		<tr><td><font class="color">Current Map:</font></td><td><?php echo $gameserver->mapname?></td></tr>
		<?php $gameserver->gametype=strtoupper(htmlentities($gameserver->gametype)); ?>
		<tr><td><font class="color">Game Type:</font></td><td><?php echo $gameserver->gametype?></td></tr>

<?php
if ($gameserver->rules["sv_punkbuster"]<>"") 
{ ?>
<tr><td><font class="color">PunkBuster:</font></td><td><?php echo ($gameserver->rules["sv_punkbuster"] == 1 ? "Enabled" : "Disabled")?></td></tr>
<?php } ?>

		</table>
		</td>
		</tr>
		</table>
		</table>
		<br />

		<div align=center><strong class="big">Player Information</strong></div><br />
		<TABLE cellpadding=0 cellspacing=0 width="520">

		<?php 
		if(!count($gameserver->playerteams))  
		////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		 {  // No Team Info (like COD)
		?>
		
		<tr><td align=center valign=top>
		<table width="100%" cellspacing=0 cellpadding=3><tr>
<?php
 if ($gameserver->playerkeys["name"]) {
  ?>
 <td align="left" class="bluebox" style="padding-left: 4px; border: 1px solid <?php echo $col_border; ?>;"><strong>Player Name</strong></td>
<?php } 
if ($gameserver->playerkeys["score"]) {
  ?>
<td align=center class="bluebox" style="border: 1px solid <?php echo $col_border; ?>; border-left: none;"><strong>Score</strong></td>
 <?php }
if ($gameserver->playerkeys["goal"]) {
  ?>
<td align=center class="bluebox" style="border: 1px solid <?php echo $col_border; ?>; border-left: none;"><strong>Goals</strong></td>
 <?php }
if ($gameserver->playerkeys["leader"]) {
  ?>
<td align=center class="bluebox" style="border: 1px solid <?php echo $col_border; ?>; border-left: none;"><strong>Leader</strong></td>
 <?php }
if ($gameserver->playerkeys["enemy"]) {
  ?>
<td align=center class="bluebox" style="border: 1px solid <?php echo $col_border; ?>; border-left: none;"><strong>Enemy</strong></td>
 <?php }
if ($gameserver->playerkeys["kia"]) {
  ?>
<td align=center class="bluebox" style="border: 1px solid <?php echo $col_border; ?>; border-left: none;"><strong>KIA</strong></td>
 <?php }
if ($gameserver->playerkeys["roe"]) {
  ?>
<td align=center class="bluebox" style="border: 1px solid <?php echo $col_border; ?>; border-left: none;"><strong>ROE</strong></td>
 <?php }
if ($gameserver->playerkeys["ping"]) {
  ?>
<td align=center class="bluebox" style="border: 1px solid <?php echo $col_border; ?>; border-left: none;"><strong>Ping</strong></td>
<?php }
if ($gameserver->playerkeys["kills"]) {
  ?>
<td align=center class="bluebox" style="border: 1px solid <?php echo $col_border; ?>; border-left: none;"><strong>Kills</strong></td>
 <?php }
if ($gameserver->playerkeys["deaths"]) {
  ?>
<td align=center class="bluebox" style="border: 1px solid <?php echo $col_border; ?>; border-left: none;"><strong>Deaths</strong></td>
 <?php } 
if ($gameserver->playerkeys["skill"]) {
  ?>
<td align=center class="bluebox" style="border: 1px solid <?php echo $col_border; ?>; border-left: none;"><strong>Skill</strong></td>
 <?php } 
if ($gameserver->playerkeys["time"]) {
  ?>
<td align=center class="bluebox" style="border: 1px solid <?php echo $col_border; ?>; border-left: none;"><strong>Time</strong></td>
<?php }
 ?>
   </tr>
 
	<?php
	if(!count($gameserver->players)) {
    	echo "<tr><td style=\"padding-left: 4px; border: 1px solid $col_border; border-top: none;\" class=\"bluebox\">(none)</td></tr>";
    	}
	else {
	for ($i=0;$i<$gameserver->numplayers;$i++) {
 echo "<tr>";
if ($gameserver->playerkeys["name"]) {
  ?>
 <td align="left" class="bluebox" style="padding-left: 4px; border: 1px solid <?php echo $col_border; ?>; border-top: none;"><?php echo $gameserver->htmlize($gameserver->players[$i]["name"]); ?></td>
<?php } 
if ($gameserver->playerkeys["score"]) {
  ?>
 <td align=center class="bluebox" style="border-bottom: 1px solid <?php echo $col_border; ?>; border-right: 1px solid <?php echo $col_border; ?>;"><?php echo $gameserver->players[$i]["score"]; ?></td>
<?php }
if ($gameserver->playerkeys["goal"]) {
  ?>
 <td align=center class="bluebox" style="border-bottom: 1px solid <?php echo $col_border; ?>; border-right: 1px solid <?php echo $col_border; ?>;"><?php echo $gameserver->players[$i]["goal"]; ?></td>
<?php }
if ($gameserver->playerkeys["leader"]) {
  ?>
 <td align=center class="bluebox" style="border-bottom: 1px solid <?php echo $col_border; ?>; border-right: 1px solid <?php echo $col_border; ?>;"><?php echo $gameserver->players[$i]["leader"]; ?></td>
<?php }
if ($gameserver->playerkeys["enemy"]) {
  ?>
 <td align=center class="bluebox" style="border-bottom: 1px solid <?php echo $col_border; ?>; border-right: 1px solid <?php echo $col_border; ?>;"><?php echo $gameserver->players[$i]["enemy"]; ?></td>
<?php }
if ($gameserver->playerkeys["kia"]) {
  ?>
 <td align=center class="bluebox" style="border-bottom: 1px solid <?php echo $col_border; ?>; border-right: 1px solid <?php echo $col_border; ?>;"><?php echo $gameserver->players[$i]["kia"]; ?></td>
<?php }
if ($gameserver->playerkeys["roe"]) {
  ?>
 <td align=center class="bluebox" style="border-bottom: 1px solid <?php echo $col_border; ?>; border-right: 1px solid <?php echo $col_border; ?>;"><?php echo $gameserver->players[$i]["roe"]; ?></td>
<?php }
if ($gameserver->playerkeys["ping"]) {
  ?>
 <td align=center class="bluebox" style="border-bottom: 1px solid <?php echo $col_border; ?>; border-right: 1px solid <?php echo $col_border; ?>;"><?php echo $gameserver->players[$i]["ping"]; ?></td>
<?php } 
if ($gameserver->playerkeys["kills"]) {
  ?>
 <td align=center class="bluebox" style="border-bottom: 1px solid <?php echo $col_border; ?>; border-right: 1px solid <?php echo $col_border; ?>;"><?php echo $gameserver->players[$i]["kills"]; ?></td>
<?php }
if ($gameserver->playerkeys["deaths"]) {
  ?>
 <td align=center class="bluebox" style="border-bottom: 1px solid <?php echo $col_border; ?>; border-right: 1px solid <?php echo $col_border; ?>;"><?php echo $gameserver->players[$i]["deaths"]; ?></td>
<?php }
if ($gameserver->playerkeys["skill"]) {
  ?>
 <td align=center class="bluebox" style="border-bottom: 1px solid <?php echo $col_border; ?>; border-right: 1px solid <?php echo $col_border; ?>;"><?php echo $gameserver->players[$i]["skill"]; ?></td>
<?php }
if ($gameserver->playerkeys["time"]) {
  ?>
 <td align=center class="bluebox" style="border-bottom: 1px solid <?php echo $col_border; ?>; border-right: 1px solid <?php echo $col_border; ?>;"><?php echo $gameserver->players[$i]["time"]; ?></td>
<?php }
            echo "</tr>";
			}
		 }
			
		?>
		</table>
		</td></tr>
		</table>

		<?php
		}
		else {
		///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		// with teams (like SOF2)
		?>
		<tr><td valign=top><br />
		<table border=0 cellspacing=0 cellpadding=0 width="100%">
		<tr><td align=center width="50%" style="padding-bottom: 2px;"><font class="blueteam"><?php echo $gameserver->team1; ?></font>
		<br />
		<table width=100% cellspacing=0 cellpadding=0>
		<tr>
		<td style="padding-left: 3px;">Players on this team: <font class=blueteam><?php echo $gameserver->teamcnt1; ?></font></td>
<?php
   if ($gameserver->teamscore1) { ?>
<td align=right style="padding-right: 3px;">Points scored: <font class=blueteam><?php echo $gameserver->teamscore1; ?></font>
<?php if ($gameserver->scorelimit) { ?>
 / <font class=blueteam><?php echo $gameserver->scorelimit; ?></font>
<?php } ?>
</td>
<?php } ?>
		</tr>
		</table>
		<td align=center width="50%" style="padding-bottom: 2px;"><font class="redteam"><?php echo $gameserver->team2; ?></font>
		<br />
		<table width=100% cellspacing=0 cellpadding=0>
		<tr>
		<td style="padding-left: 6px;">Players on this team: <font class=redteam><?php echo $gameserver->teamcnt2; ?></font></td>
<?php
   if ($gameserver->teamscore2) { ?>
<td align=right style="padding-right: 3px;">Points scored: <font class=redteam><?php echo $gameserver->teamscore2; ?></font>
<?php if ($gameserver->scorelimit) { ?>
 / <font class=redteam><?php echo $gameserver->scorelimit; ?></font>
<?php } ?>
</td>
<?php } ?>
		</tr>
		</table>
		</td></tr>
		<tr><td align=center valign=top>
		<table width="100%" cellspacing=0 cellpadding=3><tr>

<?php
 if ($gameserver->playerkeys["name"]) {
  ?>
 <td class="bluebox" style="padding-left: 4px; border: 1px solid <?php echo $col_border; ?>;"><strong>Player Name</strong></td>
<?php } 
if ($gameserver->playerkeys["ping"]) {
  ?>
<td align=center class="bluebox" style="border: 1px solid <?php echo $col_border; ?>; border-left: none;"><strong>Ping</strong></td>
<?php }
if ($gameserver->playerkeys["score"]) {
  ?>
<td align=center class="bluebox" style="border: 1px solid <?php echo $col_border; ?>; border-left: none;"><strong>Score</strong></td>
 <?php }
if ($gameserver->playerkeys["deaths"]) {
  ?>
<td align=center class="bluebox" style="border: 1px solid <?php echo $col_border; ?>; border-left: none;"><strong>Deaths</strong></td>
 <?php }
if ($gameserver->playerkeys["kills"]) {
  ?>
<td align=center class="bluebox" style="border: 1px solid <?php echo $col_border; ?>; border-left: none;"><strong>Kills</strong></td>
 <?php }
if ($gameserver->playerkeys["time"]) {
  ?>
<td align=center class="bluebox" style="border: 1px solid <?php echo $col_border; ?>; border-left: none;"><strong>Time</strong></td>
 <?php } ?>

</tr>
		<?
		$o = 0;
		
		
		for ($i=0;$i<$gameserver->numplayers+1;$i++) {
			if ($gameserver->playerteams[$i] == "1") {
			$o++;
            echo "<tr>";
if ($gameserver->playerkeys["name"]) {
echo "<td class=\"bluebox\" style=\"padding-left: 4px; border: 1px solid $col_border; border-top: none;\">".$gameserver->htmlize($gameserver->players[$i]["name"])."</td>";
}
if ($gameserver->playerkeys["ping"]) {
echo "<td align=center class=\"bluebox\" style=\"border-bottom: 1px solid $col_border; border-right: 1px solid $col_border;\">".$gameserver->players[$i]["ping"]."</td>";
}
if ($gameserver->playerkeys["score"]) {
echo "<td align=center class=\"bluebox\" style=\"border-bottom: 1px solid $col_border; border-right: 1px solid $col_border;\">".$gameserver->players[$i]["score"]."</td>";
}
if ($gameserver->playerkeys["deaths"]) {
echo "<td align=center class=\"bluebox\" style=\"border-bottom: 1px solid $col_border; border-right: 1px solid $col_border;\">".$gameserver->players[$i]["deaths"]."</td>";
}
if ($gameserver->playerkeys["kills"]) {
echo "<td align=center class=\"bluebox\" style=\"border-bottom: 1px solid $col_border; border-right: 1px solid $col_border;\">".$gameserver->players[$i]["kills"]."</td>";
}
if ($gameserver->playerkeys["time"]) {
echo "<td align=center class=\"bluebox\" style=\"border-bottom: 1px solid $col_border; border-right: 1px solid $col_border;\">".$gameserver->players[$i]["time"]."</td>";
}
echo "</tr>";
			}
		}

		if ($o == 0)
			echo "<tr><td style=\"padding-left: 4px; border: 1px solid $col_border; border-top: none;\" class=\"bluebox\">(none)</td></tr>";
		?>
		</table>
		</td><td align=center valign=top>
		<table width="100%" cellspacing=0 cellpadding=3><tr>

<?php
 if ($gameserver->playerkeys["name"]) {
  ?>
 <td class="redbox" style="padding-left: 4px; border: 1px solid <?php echo $col_border; ?>;"><strong>Player Name</strong></td>
<?php } 
if ($gameserver->playerkeys["ping"]) {
  ?>
<td align=center class="redbox" style="border: 1px solid <?php echo $col_border; ?>; border-left: none;"><strong>Ping</strong></td>
<?php }
if ($gameserver->playerkeys["score"]) {
  ?>
<td align=center class="redbox" style="border: 1px solid <?php echo $col_border; ?>; border-left: none;"><strong>Score</strong></td>
 <?php }
if ($gameserver->playerkeys["deaths"]) {
  ?>
<td align=center class="redbox" style="border: 1px solid <?php echo $col_border; ?>; border-left: none;"><strong>Deaths</strong></td>
 <?php }
if ($gameserver->playerkeys["kills"]) {
  ?>
<td align=center class="bluebox" style="border: 1px solid <?php echo $col_border; ?>; border-left: none;"><strong>Kills</strong></td>
 <?php }
if ($gameserver->playerkeys["time"]) {
  ?>
<td align=center class="redbox" style="border: 1px solid <?php echo $col_border; ?>; border-left: none;"><strong>Time</strong></td>
 <?php } ?>





</tr>
		<?
		$o = 0;
		for ($i=0;$i<$gameserver->numplayers+1;$i++) {
			if ($gameserver->playerteams[$i] == "2") {
				$o++;
				
echo "<tr>";
if ($gameserver->playerkeys["name"]) {
echo "<td class=\"redbox\" style=\"padding-left: 4px; border: 1px solid $col_border; border-top: none;\">".$gameserver->htmlize($gameserver->players[$i]["name"])."</td>";
}
if ($gameserver->playerkeys["ping"]) {
echo"<td align=center class=\"redbox\" style=\"border-bottom: 1px solid $col_border; border-right: 1px solid $col_border;\">".$gameserver->players[$i]["ping"]."</td>";
}
if ($gameserver->playerkeys["score"]) {
echo"<td align=center class=\"redbox\" style=\"border-bottom: 1px solid $col_border; border-right: 1px solid $col_border;\">".$gameserver->players[$i]["score"]."</td>";
}
if ($gameserver->playerkeys["deaths"]) {
echo"<td align=center class=\"redbox\" style=\"border-bottom: 1px solid $col_border; border-right: 1px solid $col_border;\">".$gameserver->players[$i]["deaths"]."</td>";
}
if ($gameserver->playerkeys["kills"]) {
echo "<td align=center class=\"bluebox\" style=\"border-bottom: 1px solid $col_border; border-right: 1px solid $col_border;\">".$gameserver->players[$i]["kills"]."</td>";
}
if ($gameserver->playerkeys["time"]) {
echo"<td align=center class=\"redbox\" style=\"border-bottom: 1px solid $col_border; border-right: 1px solid $col_border;\">".$gameserver->players[$i]["time"]."</td>";
}

echo"</tr>";
			}
		}

		if ($o == 0)
			echo "<tr><td style=\"padding-left: 4px; border: 1px solid $col_border; border-top: none;\" class=\"redbox\">(none)</td></tr>";
		?>
		</table>
		</td></tr>
		</table>
		</td></tr>
		</table><br />
		<font class="specteam">Spectators:</font> <?php
		$o = 0;
		for ($i=0;$i<$gameserver->numplayers;$i++) {
			if ($gameserver->playerteams[$i] == "3") {
				$o++;
				echo $gameserver->htmlize($gameserver->players[$i]["name"]);
				if ($o < $gameserver->spec)
					echo "<font class=\"specteam\">,</font> ";
			}
		}
		if ($o == 0)
			echo "(none)";
		} 

	}
	else {
		echo "We were unable to contact the server you requested, it is most likely <font class=red>Offline</font>";
	}

	?>
	</TD>
	</TR>
</TABLE>
	<?php
}

?>
<br />
<div align=center><?php
//if ($displaytips) echo showTip();
?></div>
</TD></TR>
</TABLE>
<?php echo "<div align=center> Powered By:<br />".showCredits(showVersion())."</div>"; ?>
</BODY>
</HTML>
<?php
restore_error_handler();
$x->display_bottom();
} else {
	$x->display_slim('you are not authorized to view this page.');
}
?>
