<?php 
	global $master, $dbc;


if($master['voice_mode'] == "ts") {

//error_reporting(0);
// TS-Viewer
//
// (C) 2005 by Curtis Brown
// non-commercial use approved, commercial users please contact me at webmaster@squery.com

//
/*######################################*\
##\¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯/##
###>   designed by www.SQuery.com     <###
##/____________________________________\##
\*######################################*/
//
//
//-------------------------------------------------------------------------------------------------
//---> Orginalcode
///CONFIG FILE IMPORTED BELOW
//

class tss2info {

var $serverAddress;
var $sitetitle;
//-------------------------------------------------------------------------------------------------
// **** Configuration ****
// var $sitetitle = $master['ts_name']; // The Title of your Teamspeak Server
 //var $serverAddress = $ip[0]; // The IP of your Teamspeak server, (don't include the port here)
 var $serverQueryPort = 51234; // TeamSpeak QueryPort.. You usually don't need to change this (Standard 51234)
// var $serverUDPPort = $ip[1]; // UDP Port of your Teamspeak, This is the port that goes with the IP (Standard 8767)
 var $tablewidth = "153"; // The Width of the teamspeak block (optimal 153)
//var $serverpassword = $master['ts_pass']; // Server Password.  If you don't have a password set to "".

function tss2info() {
	global $master;
	$this->sitetitle = $master['voice_name'];
	$this->ip = explode(":",$master['voice_ip']);
	$this->serverAddress = $this->ip[0];
	$this->serverUDPPort = $this->ip[1];
	$this->serverpassword = $master['voice_pass'];
}

// 

// TS-Viewer
//
// (C) 2005 by Curtis Brown
// non-commercial use approved, commercial users please contact me at webmaster@squery.com

//
/*######################################*\
##\¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯/##
###>   designed by www.SQuery.com     <###
##/____________________________________\##
\*######################################*/
//

// **** Configuration ****
//-------------------------------------------------------------------------------------------------
var $themename = "ts-viewer"; // Do not change this, directory where graphics are found under img.
//-------------------------------------------------------------------------------------------------
//
// This is the end of the configuration section.  Don't modify anything below this line.
//
//-------------------------------------------------------------------------------------------------
var $socket;
var $serverStatus = "offline";
var $playerList = array();
var $channelList = array();
function getSocket($host, $port, $errno, $errstr, $timeout) {
  unset($socket);
 $socket=0;// added
  $attempts = 1;
  while($attempts <= 1 and !$socket) {
	$attempts++;
    @$socket = fsockopen($host, $port, $errno, $errstr, $timeout);
    $this->errno = $errno;
    $this->errstr = $errstr;
    if($socket and fread($socket, 4) == "[TS]") {
      fgets($socket, 128);
      return $socket;
	}
  }
  return false;
}
function sendQuery($socket, $query) {
  fputs($socket, $query."\n");
}
function getOK($socket) {
  $result = fread($socket, 2);
  fgets($socket, 128);
  return($result == "OK");
}
function closeSocket($socket) {
  fputs($socket, "quit");
  fclose($socket);
}
function getNext($evalString) {
  $pos = strpos($evalString, "\t");
  if(is_integer($pos)) {
    return substr($evalString, 0, $pos);
  } else {
    return $evalString;
  }
}
function chopNext($evalString) {
  $pos = strpos($evalString, "\t");
  if(is_integer($pos)) {
    return substr($evalString, $pos + 1);
  } else {
    return "";
  }
}
function stripQuotes($evalString) {
  if(strpos($evalString, '"') == 0) $evalString = substr($evalString, 1, strlen($evalString) - 1);
  if(strrpos($evalString, '"') == strlen($evalString) - 1) $evalString = substr($evalString, 0, strlen($evalString) - 1);
  return $evalString;
}
function getVerboseCodec($codec) {
  if($codec == 0) {
    $codec = "CELP 5.1 Kbit";
  } elseif($codec == 1) {
    $codec = "CELP 6.3 Kbit";
  } elseif($codec == 2) {
    $codec = "GSM 14.8 Kbit";
  } elseif($codec == 3) {
    $codec = "GSM 16.4 Kbit";
  } elseif($codec == 4) {
    $codec = "CELP Windows 5.2 Kbit";
  } elseif($codec == 5) {
    $codec = "Speex 3.4 Kbit";
  } elseif($codec == 6) {
    $codec = "Speex 5.2 Kbit";
  } elseif($codec == 7) {
    $codec = "Speex 7.2 Kbit";
  } elseif($codec == 8) {
    $codec = "Speex 9.3 Kbit";
  } elseif($codec == 9) {
    $codec = "Speex 12.3 Kbit";
  } elseif($codec == 10) {
    $codec = "Speex 16.3 Kbit";
  } elseif($codec == 11) {
    $codec = "Speex 19.5 Kbit";
  } elseif($codec == 12) {
    $codec = "Speex 25.9 Kbit";
  } else {
    $codec = "unknown (".$codec.")";
  }
  return $codec;
}
function getInfo() {
$errno=0; // added 
$errstr=0; // added 
$isdefault=0; // added 
$this->socket = $this->getSocket($this->serverAddress, $this->serverQueryPort, $errno, $errstr, 0.3);
if($this->socket == false) {
  return;
  echo ("No Server");
} else {
  $this->serverStatus = "online";
  $this->sendQuery($this->socket, "sel ".$this->serverUDPPort);
  if(!$this->getOK($this->socket)) {
    echo "Server didn't answer \"OK\" after last command. Aborting.";
    return;
  }
  $this->sendQuery($this->socket,"pl");
  $this->playerList = array();
  do {
    $playerinfo = fscanf($this->socket, "%s %d %d %d %d %d %d %d %d %d %d %d %d %s %s %s"); 
    list($playerid, $channelid, $receivedpackets, $receivedbytes, $sentpackets, $sentbytes, $paketlost, $pingtime, $totaltime, $idletime, $privileg, $userstatus, $attribute, $s, $playername, $playername2) = $playerinfo;
    if($playerid != "OK") {
      if (strcmp($playername2,"\"\"")&& $playername2[0]!="\"") $playername=$playername." ".$playername2;
      $this->playerList[$playerid] = array(
      "playerid" => $playerid,
      "channelid" => $channelid,
      "receivedpackets" => $receivedpackets,
      "receivedbytes" => $receivedbytes,
      "sentpackets" => $sentpackets,
      "sentbytes" => $sentbytes,
      "paketlost" => $paketlost / 100,
//-------------------------------------------------------------------------------------------------
      "pingtime" => $pingtime,
      "totaltime" => $totaltime, 
      "idletime" => $idletime, 
      "privileg" => $privileg, 
      "userstatus" => $userstatus,
      "attribute" => $attribute, 
//-------------------------------------------------------------------------------------------------
      "s" => $s,
      "playername" => $this->stripQuotes($playername)
      );
    }
  } while($playerid != "OK");
  $this->sendQuery($this->socket,"cl");
  $this->channelList = array();
  do {
    $channelinfo = "";
    do {
      $input = fread($this->socket, 1);
      if($input != "\n" && $input != "\r") $channelinfo .= $input;
    } while($input != "\n");
    $channelid = $this->getNext($channelinfo);
    $channelinfo = $this->chopNext($channelinfo);
    $codec = $this->getNext($channelinfo);
    $channelinfo = $this->chopNext($channelinfo);
    $parent = $this->getNext($channelinfo);
    $channelinfo = $this->chopNext($channelinfo);
    $d = $this->getNext($channelinfo);
    $channelinfo = $this->chopNext($channelinfo);
    $maxplayers = $this->getNext($channelinfo);
    $channelinfo = $this->chopNext($channelinfo);
    $channelname = $this->getNext($channelinfo);
    $channelinfo = $this->chopNext($channelinfo);
    $d = $this->getNext($channelinfo);
    $channelinfo = $this->chopNext($channelinfo);
    $d = $this->getNext($channelinfo);
    $channelinfo = $this->chopNext($channelinfo);
    $topic = $this->getNext($channelinfo);
    if($channelid != "OK") {
      if($isdefault == "Default") $isdefault = 1; else $isdefault = 0;
      $playercount = 0;
      foreach($this->playerList as $playerInfo) {
        if($playerInfo['channelid'] == $channelid) $playercount++;
      }
      $this->channelList[$channelid] = array(
      "channelid" => $channelid,
      "codec" => $codec,
      "parent" => $parent,
      "maxplayers" => $maxplayers,
      "channelname" => $this->stripQuotes($channelname),
      "isdefault" => $isdefault,
      "topic" => $this->stripQuotes($topic),
      "currentplayers" => $playercount);
    }
  } while($channelid != "OK");
  $this->closeSocket($this->socket);
  }
}
}
$tss2info = new tss2info;
//END CONFIG IMPORT

global $cookie;
$tss2info->getInfo();
$tss2info->userName=$cookie[1];;
//---> Orginalcode
//-------------------------------------------------------------------------------------------------

// tsduration(unix timestamp) - returns duration in a [x]:xx format
function tsduration($time) {
	$time = floor($time);
      if ($time>3600) {
         $h=floor($time / 3600);
 	   $time=$time-($h*3600);
	   $m = floor($time / 60);
		$s = $time - ($m * 60);
		$s = substr("00".$s,-2);
		return " $h:$m:$s Hrs";

       }
	elseif ($time > 60) {
		$m = floor($time / 60);
		$s = $time - ($m * 60);
		$s = substr("00".$s,-2);
		return " $m:$s Min";
	} else {
		$s = substr("00".$time,-2);
		return " $s Sec";
	}
}


$content='<table border="0" width="';
$content.=$tss2info->tablewidth;
$content.='" cellpadding="0" cellspacing="0">
 <tr>
  <td width="100%" class="tshead">
   <table border="0" width="100%" cellpadding="0" cellspacing="0">
    <tr>';

$content.='<td width="16"><img src="img/';
$content.=$tss2info->themename;
$content.='/graphics/teamspeak.gif" width="16"height="16" border="0" alt=""></td>
     <td class="tshead">&nbsp;<b>';
$content.=$tss2info->sitetitle;
$content.='</b></td>
    </tr>
   </table>
<table border="0" width="98%" cellspacing="1"><tr>
        <td class="tshead" colspan="2" align="center">
        <a href="http://www.goteamspeak.com/">
        <img src="img/ts-viewer/graphics/link_ts.gif" border="0">
        </a></td></tr></table>
  </td>
 </tr>
 <!-- start -->';


//-------------------------------------------------------------------------------------------------
//---> Orginalcode
$counter = 0;
foreach($tss2info->channelList as $channelInfo) {
  $channelname = $channelInfo['channelname'];
  $codec = $tss2info->getVerboseCodec($channelInfo['codec']);
  if($channelInfo['isdefault'] == "1")  $isDefault = "yes"; else $isDefault = "no";
  if ($channelInfo['channelid'] != "id") {
//---> Orginalcode
//---> Channel <---\\

$content.='<!-- Channel -->
 <tr>
  <td width="100%" class="tsip">
   <table border="0" width="100%" cellpadding="0" cellspacing="0">
    <tr>
     <td width="32" class="tsip"><img width="16"height="16" src="img/';
$content.=$tss2info->themename;
$content.='/graphics/gitter2.gif" border="0" alt=""><img src="img/';
$content.=$tss2info->themename;
$content.='/graphics/channel.gif" width="16"height="16" border="0" alt=""></td>
     <td class="tsip">&nbsp;<a class="tsip" href="teamspeak://';
$content.=$tss2info->serverAddress;
$content.=':';
$content.=$tss2info->serverUDPPort;
$content.='?channel=';
$content.=$channelname;
$content.='?password=';
$content.=$tss2info->serverpassword;
$content.='?nickname=';
$content.=$tss2info->userName;
$content.='" title="[Topic:]';
$content.=$channelInfo['topic'];
$content.=" [Codec:]".$codec;
$content.='"><b>';
$content.=$channelname;
$content.='</b></a> (';
$content.=$channelInfo['currentplayers'];
$content.='/';
$content.=$channelInfo['maxplayers'];
$content.=')</td>
    </tr>
   </table>
  </td>
 </tr>
<!-- Channel -->';

//---> Channel <---\\
//-------------------------------------------------------------------------------------------------
//---> Orginalcode
    $counter_player = 0;
    foreach($tss2info->playerList as $playerInfo) {
          if ($playerInfo['channelid'] == $channelInfo['channelid']) {
//---> Orginalcode
//-------------------------------------------------------------------------------------------------
//--- UserStatusBuild --\\
$privilegrec="";//ADDED BY ME
if  ($playerInfo['attribute'] == "0") $playergif = "player.gif";
if (($playerInfo['attribute'] == "8") or
    ($playerInfo['attribute'] == "9") or
    ($playerInfo['attribute'] == "12") or
    ($playerInfo['attribute'] == "13") or
    ($playerInfo['attribute'] == "24") or
    ($playerInfo['attribute'] == "25") or
    ($playerInfo['attribute'] == "28") or
    ($playerInfo['attribute'] == "29") or
    ($playerInfo['attribute'] == "40") or
    ($playerInfo['attribute'] == "41") or
    ($playerInfo['attribute'] == "44") or
    ($playerInfo['attribute'] == "45") or
    ($playerInfo['attribute'] == "56") or
    ($playerInfo['attribute'] == "57")) $playergif = "away.gif";
if (($playerInfo['attribute'] == "16") or
    ($playerInfo['attribute'] == "17") or
    ($playerInfo['attribute'] == "20") or
    ($playerInfo['attribute'] == "21")) $playergif = "mutemicro.gif";
if (($playerInfo['attribute'] == "32") or
    ($playerInfo['attribute'] == "33") or
    ($playerInfo['attribute'] == "36") or
    ($playerInfo['attribute'] == "37") or
    ($playerInfo['attribute'] == "48") or
    ($playerInfo['attribute'] == "49") or
    ($playerInfo['attribute'] == "52") or
    ($playerInfo['attribute'] == "53")) $playergif = "mutespeakers.gif";
if  ($playerInfo['attribute'] == "4") $playergif = "player.gif";
if (($playerInfo['attribute'] == "1") or
    ($playerInfo['attribute'] == "5")) $playergif = "channelcommander.gif";
if  ($playerInfo['attribute'] >= "64") {
 $playergif = "record.gif";
 $privilegrec = " rec";
}
//--- UserStatusBuild --\\
//-------------------------------------------------------------------------------------------------
//--- UserRegistration ---\\
if ($playerInfo['userstatus'] < "4") $playerstatus = "U"; // Unregistered
if ($playerInfo['userstatus'] == "4") $playerstatus = "R"; // Registered
if ($playerInfo['userstatus'] == "5") $playerstatus = "R SA"; // Serveradmin
//--- UserRegistration ---\\
//-------------------------------------------------------------------------------------------------
//--- Privilege ---\\
if ($playerInfo['privileg'] == "0") $privileg = ""; // nix
if ($playerInfo['privileg'] == "1") $privileg = " CA"; // Channeladmin
//--- Privilege ---\\
//-------------------------------------------------------------------------------------------------
//--- Online display ---\\
 $playertotaltime=tsduration($playerInfo['totaltime']);
//--- Online display ---\\
//-------------------------------------------------------------------------------------------------
//--- Idle display ---\\
$playeridletime=tsduration($playerInfo['idletime']);

//--- Idle display ---\\
//-------------------------------------------------------------------------------------------------
//---> Player <---\\

$content.='<!-- Player -->
 <tr>
  <td width="100%">
   <table border="0" width="100%" cellpadding="0" cellspacing="0">
    <tr>
     <td width="48" class="tsplrs"><img src="img/';
$content.=$tss2info->themename;
$content.='/graphics/gitter.gif" width="16"height="16" border="0" alt=""><img src="img/';
$content.=$tss2info->themename;
$content.='/graphics/gitter2.gif" width="16"height="16" border="0" alt=""><img src="img/';
$content.=$tss2info->themename;
$content.='/graphics/';
$content.=$playergif;
$content.='" width="16"height="16" border="0" alt="Time [online:';
$content.=$playertotaltime;
$content.=' | idle:';
$content.=$playeridletime;
$content.='] Ping:';
$content.=$playerInfo['pingtime'];
$content.='ms"></td>
     <td class="tsplrs" title="Time [online:';
$content.=$playertotaltime;
$content.=' | idle:';
$content.=$playeridletime;
$content.='] Ping:';
$content.=$playerInfo['pingtime'];
$content.='ms">&nbsp;';
$content.=$playerInfo['playername'];
$content.=' (';
$content.=$playerstatus;
$content.=$privileg;
$content.=$privilegrec;
$content.=')</td>
    </tr>
   </table>
  </td>
 </tr>
<!-- Player -->';

//---> Player <---\\
//-------------------------------------------------------------------------------------------------
//---> Orginalcode
                $counter_player++;
          }
    }
  }
  $counter++;
}
//---> Orginalcode
//-------------------------------------------------------------------------------------------------
//---> Offline <---\\
if ($counter == 0) { 

$content.='<!-- Offline -->
 <tr>
  <td width="100%">
   <table border="0" width="100%" cellpadding="0" cellspacing="0">
    <tr>
     <td style="color: #FF0000;" width="110" align="center"><b>Offline</b></td>
    </tr>
   </table>
  </td>
 </tr>
<!-- Offline -->';

}
//---> Offline <---\\
//-------------------------------------------------------------------------------------------------
//---> End <---\\
$content.='<!-- End -->
 <tr>
  <td width="100%" class="tshead">
  <b>&nbsp;&nbsp;&nbsp;';
$content.=$tss2info->serverAddress;
$content.=':';
$content.=$tss2info->serverUDPPort;
$content.='</b>
  </td>
 </tr>
</table>
<!-- End -->
<!-- TeamSpeak Viewer -->';

//---> End <---\\
//-------------------------------------------------------------------------------------------------
if($master['voice_ip'])
	echo $content;
} elseif($master['voice_mode'] == "vent") {
	
	
}
?>
