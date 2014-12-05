<?php
// Improved ALP security exploit fix - added on July 19, 2006
defined('SQUERY_INVOKED') or die('No access.');


include_once($libpath."gsQuery.php");


class hlife2 extends gsQuery
{
  
  function query_server($getPlayers=TRUE,$getRules=TRUE)
  {      
    $this->playerkeys=array();
    $this->debug=array();
    $this->password=-1;

     // get Challenge string
     $command="\xFF\xFF\xFF\xFFW";
    if(!($result=$this->_sendCommand($this->address,$this->queryport,$command))) {
      return FALSE;
    }
   
  $i=4;// start after header
  $check_byte=ord($result[$i++]);
  
  if ($check_byte!=65) {
	echo "Bad Byte!";
	return FALSE;
	}
  // store challenge value
  $challenge=substr($result,$i++,4);

    $command="\xFF\xFF\xFF\xFFTSource Engine Query";
    if(!($result=$this->_sendCommand($this->address,$this->queryport,$command))) {
      return FALSE;
    }
  // $fp=fopen("out.bin","w");
  //fwrite($fp,$result);
  //fclose($fp);
   $i=5;// start after header 
   
    $this->rules["network"]=ord($result[$i++]);
    $this->gameversion="";
    $this->hostport.=$this->queryport;
    $this->servertitle="";
    $this->mapname="";
    $this->rules["gamedir"]="";
    $this->gametype="";
    while ($result[$i]!="\x00") $this->servertitle.=$result[$i++];
    $i++;
    while ($result[$i]!="\x00") $this->mapname.=$result[$i++];
    $i++;
    while ($result[$i]!="\x00") $this->rules["gamedir"].=$result[$i++];
    $i++;
    while ($result[$i]!="\x00") $this->gametype.=$result[$i++];
    if ($this->gametype=="Counter-Strike: Source") $this->gamename="cs-source";
    if ($this->gametype=="Day of Defeat: Source") $this->gamename="dod-source";
    if ($this->rules["gamedir"]=="hl2mp") $this->gamename="hl2mp";
    $i++;
    $this->rules["steamid"]=ord($result{$i}) | (ord($result{$i+1})<<8);
    $i+=2;
    $this->numplayers=ord(substr($result,$i++,1));
    $this->maxplayers=ord(substr($result,$i++,1));
    $this->rules["botplayers"]=ord(substr($result,$i++,1));
    $this->rules["dedicated"]=($result[$i++]=="d" ? "Yes" : "No");
    $this->rules["server_os"]=($result[$i++]=="l" ? "Linux" : "Windows");
    $this->password=ord(substr($result,$i++,1));
    $this->rules["secure"]=($result[$i++]=="1" ? "Yes" : "No");
    while ($result[$i]!="\x00") $this->gameversion.=$result[$i++];
   
// do rules
    $command="\xFF\xFF\xFF\xFFV".$challenge;
    if(!($result=$this->_sendCommand($this->address,$this->queryport,$command))) {
    return FALSE;
    }
   
$exploded_data = explode("\x00", $result);
        
     
   $z=count($exploded_data);
    for($i=1;$i<$z;$i++) {
      switch($exploded_data[$i++]) {
      case 'deathmatch':
	if ($exploded_data[$i]=='1') $this->gametype='Deathmatch';
	break;
      case 'coop':
	if ($exploded_data[$i]=='1') $this->gametype='Cooperative';
	break;
        default:
	if(isset($exploded_data[$i-1]) && isset($exploded_data[$i])) {
	  $this->rules[strtolower($exploded_data[$i-1])]=$exploded_data[$i];
	}
      }
    }

 if($getPlayers) {
// do players
 $command="\xFF\xFF\xFF\xFFU".$challenge;
    if(!($result=$this->_sendCommand($this->address,$this->queryport,$command))) {
    return FALSE;
    }
  
$j=7;
$listedplayers=ord($result{5});// this number is not always accurate???
$l=strlen($result);
for ($i=0;$i<$listedplayers;$i++)
{
 if ($j>=$l) break;
 while ($result[$j]!="\x00") $players[$i]["name"].=$result[$j++];
  
 $j++;
 

 $t= ord($result{$j}) | (ord($result{$j+1})<<8) | (ord($result{$j+2})<<16) | (ord($result{$j+3})<<24); 
 
 
 $players[$i]["score"]=$t;
if($players[$i]["score"]>128) {
	$players[$i]["score"]-=256;
      }

 $j+=4;
 $t= unpack("ftime", substr($result, $j, 4));
 $t= mktime(0, 0, $t['time']);
 $players[$i]["time"] = date("H:i:s", $t);
 $j+=5;
$this->playerkeys["name"]=TRUE;
$this->playerkeys["score"]=TRUE;
$this->playerkeys["time"]=TRUE;

}
  }
      
      $this->players=$players;
    

    $this->online = TRUE;
    return TRUE; 
  }
  


  /**
   * @brief Sends a rcon command to the game server
   * 
   * @param command the command to send
   * @param rcon_pwd rcon password to authenticate with
   * @return the result of the command or FALSE on failure
   */
  function rcon_query_server($command, $rcon_pwd) 
  {
    $get_challenge="\xFF\xFF\xFF\xFFchallenge rcon\n";
    if(!($challenge_rcon=$this->_sendCommand($this->address,$this->queryport,$get_challenge))) {
      $this->debug["Command send " . $command]="No challenge rcon received";
      return FALSE;
    }
    if (!ereg('challenge rcon ([0-9]+)', $challenge_rcon)) {
      $this->debug["Command send " . $command]="No valid challenge rcon received";
      return FALSE;
    }
    $challenge_rcon=substr($challenge_rcon, 19,10);
    $command="\xFF\xFF\xFF\xFFrcon \"".$challenge_rcon."\" ".$rcon_psw." ".$command."\n";
    if(!($result=$this->_sendCommand($this->address,$this->queryport,$command))) {
      $this->debug["Command send " . $command]="No reply received";
      return FALSE;
    } else {
      return substr($result, 5);
    }
  }


/* this is for game specific cvar displays  */
function docvars($gameserver)
{
$retval="<table cellspacing=0 cellpadding=0 width=\"100%\">"
  . "		<tr>"
  . "		<td class=\"row\">"
  . "		<table cellspacing=0 cellpadding=0>"
  . "		<tr><td style=\"padding-right: 5px;\">".checkmark()." <font class=\"color\">TeamPlay:</font></td><td>".($gameserver ->rules["mp_teamplay"] == 1 ? "Yes" : "No")."</td></tr>"
  . "		<tr><td style=\"padding-right: 5px;\">".checkmark()." <font class=\"color\">Frag Limit:</font></td><td>".($gameserver ->rules["mp_fraglimit"] == 1 ? "Yes" : "No")."</td></tr>"
  . "		<tr><td style=\"padding-right: 5px;\">".checkmark()." <font class=\"color\">Time Limit:</font></td><td>".($gameserver ->rules["mp_timelimit"] == 1 ? "Yes" : "No")."</td></tr>"
  . "		<tr><td style=\"padding-right: 5px;\">".checkmark()." <font class=\"color\">Friendly Fire:</font></td><td>".($gameserver ->rules["mp_friendlyfire"] == 1 ? "Yes" : "No")."</td></tr>"
  . "		<tr><td style=\"padding-right: 5px;\">".checkmark()." <font class=\"color\">Flashlight:</font></td><td>".($gameserver ->rules["mp_flashlight"] == 1 ? "Yes" : "No")."</td></tr>"
  . "		<tr><td style=\"padding-right: 5px;\">".checkmark()." <font class=\"color\">Footsteps:</font></td><td>".($gameserver ->rules["mp_footsteps"] == 1 ? "Yes" : "No")."</td></tr>"
. "		<tr><td style=\"padding-right: 5px;\">".checkmark()." <font class=\"color\">Dedicated:</font></td><td>".$gameserver ->rules["dedicated"]."</td></tr>"
  . "		</table>"
  . "		</td>"
  . "		<td class=\"row\">"
  . "		<table cellspacing=0 cellpadding=0>"
  . "		<tr><td style=\"padding-right: 5px;\">".checkmark()." <font class=\"color\">Fall Damage:</font></td><td>".($gameserver ->rules["mp_falldamage"] == 1 ? "Yes" : "No")."</td></tr>"
  . "		<tr><td style=\"padding-right: 5px;\">".checkmark()." <font class=\"color\">Weapons Stay:</font></td><td>".($gameserver ->rules["mp_weaponstay"] == 1 ? "Yes" : "No")."</td></tr>"
  . "		<tr><td style=\"padding-right: 5px;\">".checkmark()." <font class=\"color\">Force Respawn:</font></td><td>".($gameserver ->rules["mp_forcerespawn"] == 1 ? "Yes" : "No")."</td></tr>"
  . "		<tr><td style=\"padding-right: 5px;\">".checkmark()." <font class=\"color\">Auto Crosshair:</font></td><td>".($gameserver ->rules["mp_autocrosshair"] == 1 ? "Yes" : "No")."</td></tr>"
  . "		<tr><td style=\"padding-right: 5px;\">".checkmark()." <font class=\"color\">Allow NPCs:</font></td><td>".($gameserver ->rules["mp_allownpcs"] == 1 ? "Yes" : "No")."</td></tr>"
  . "		<tr><td style=\"padding-right: 5px;\">".checkmark()." <font class=\"color\">BotPlayers:</font></td><td>".$gameserver ->rules["botplayers"]."</td></tr>"
  . "		<tr><td style=\"padding-right: 5px;\">".checkmark()." <font class=\"color\">Server OS:</font></td><td>".$gameserver ->rules["server_os"]."</td></tr>"
  . "		</table>"
  . "		</td>"
  . "		</tr>"
  . "		</table>";
  return $retval;
}

}

?>
