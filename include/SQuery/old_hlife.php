<?php
// Improved ALP security exploit fix - added on July 19, 2006
defined('SQUERY_INVOKED') or die('No access.');
/*
 *  gsQuery - Querys game servers
 *  Copyright (c) 2002-2004 Jeremias Reith <jr@terragate.net>
 *  http://www.gsquery.org
 *
 *  This file is part of the gsQuery library.
 *
 *  The gsQuery library is free software; you can redistribute it and/or
 *  modify it under the terms of the GNU Lesser General Public
 *  License as published by the Free Software Foundation; either
 *  version 2.1 of the License, or (at your option) any later version.
 *
 *  The gsQuery library is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU
 *  Lesser General Public License for more details.
 *
 *  You should have received a copy of the GNU Lesser General Public
 *  License along with the gsQuery library; if not, write to the
 *  Free Software Foundation, Inc.,
 *  59 Temple Place, Suite 330, Boston,
 *  MA  02111-1307  USA
 *
 */


include_once($libpath."gsQuery.php");

/**
 * @brief Querys a halflife server
 * @author Jeremias Reith (jr@terragate.net)
 * @version $Id: old_hlife.php,v 1.3 2006/07/19 21:33:15 synth_spring Exp $
 * @bug negative scores are not shown correctly 
 * @todo extract time field out of the player data

 * Code is very ugly at the moment.
 * Does anyone have the protocol specs?<br />
 *
 * This class works with Halflife only. 
 */
class hlife extends gsQuery
{
  
  function query_server($protocol="gsqp",$getPlayers=TRUE,$getRules=TRUE)
  {      
    $this->playerkeys=array();
    $this->debug=array();
    $this->password=-1;
            
    $command="\xFF\xFF\xFF\xFFinfostring\n";
    if(!($result=$this->_sendCommand($this->address,$this->queryport,$command))) {
      return FALSE;
    }
    
    $this->password = -1;
    $exploded_data = explode("\\", $result);
    for($i=1;$i<count($exploded_data);$i++) {
      switch($exploded_data[$i++]) {
      case "address":
	if ($exploded_data[$i] == 'loopback') {
	  $this->hostport = $this->queryport;
	} else {
	  list($ip, $this->hostport) = explode(":", $exploded_data[$i]);
	}
	break;
      case "hostname":
	$this->servertitle = $exploded_data[$i];
	break;
      case "map":
	$this->mapname = $exploded_data[$i];
	break;
      case "players":
	$this->numplayers = $exploded_data[$i];
	break;
      case "max":
	$this->maxplayers = $exploded_data[$i];
	break;
      case "protocol":
	$this->gameversion = ($exploded_data[$i] == 47)? '1.6' : '1.5';
	break;
      case "password":
	$this->password = $exploded_data[$i];
	break;
      case "gamedir":
	$this->gamename = "hlife_" . $exploded_data[$i];
	$this->gametype = $exploded_data[$i];
	break;
      }
    } 

    
    // get players
    if($this->numplayers && $getPlayers) {
      $command="\xFF\xFF\xFF\xFFplayers\n";
      if(!($result=$this->_sendCommand($this->address,$this->queryport,$command))) {
	return FALSE;
      }

$j=7;
$listedplayers=ord($result{5});
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


}

      $this->playerkeys["name"]=TRUE;
      $this->playerkeys["score"]=TRUE;
      $this->playerkeys["time"]=TRUE;
      $this->players=$players;
    }


   $this->gametype = ($this->gametype == 'cstrike') ? $this->gametype.' '.$this->gameversion : $this->gametype;

    // get rules
    $command="\xFF\xFF\xFF\xFFrules\n";
    if(!($result=$this->_sendCommand($this->address,$this->queryport,$command))) {
      return FALSE;
    }

	if ($result[7]=="\x00") $start=0;
           else $start=1;
// rules can be in multiple packets, we have to sort it out
$str="/\xFE\xFF\xFF\xFF/";
 $block=preg_split($str,$result,-1,PREG_SPLIT_NO_EMPTY);

$str="/\xFF\xFF\xFF\xFF/";
  
if (!empty($block[0]) && !empty($block[1]))
{
	if (preg_match($str, $block[0]))
	{
		$result = substr($block[0], 12, strlen($block[0])).substr($block[1], 5, strlen($block[1]));		
	}
	elseif (preg_match($str, $block[1]))
	{
		$result = substr($block[1], 12, strlen($block[1])).substr($block[0], 5, strlen($block[0]));
	}
} 
elseif (!empty($block[0]))
{
	$result = substr($block[0], 5, strlen($block[0]));
}
  
    $exploded_data = explode("\x00", $result);
        
     
    for($i=$start;$i<count($exploded_data);$i++) {
     
      switch($exploded_data[$i++]) {
      case "sv_password":
	$this->password=$exploded_data[$i];
	break;
      case "amx_nextmap":
	$this->nextmap=$exploded_data[$i];
	break;
      case "cm_nextmap":
	$this->nextmap=$exploded_data[$i];
	break;
      default:
	if(isset($exploded_data[$i-1]) && isset($exploded_data[$i])) {
	  $this->rules[$exploded_data[$i-1]]=$exploded_data[$i];
	}
      }
    }
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
switch ($gameserver->gamename)
	{
	case "hlife_gearbox":
$retval="<table cellspacing=0 cellpadding=0 width=\"100%\">"
  . "		<tr>"
  . "		<td class=\"row\">"
  . "		<table cellspacing=0 cellpadding=0>"
  . "		<tr><td style=\"padding-right: 5px;\">".checkmark()." <font class=\"color\">Allow Spectators:</font></td><td>".($gameserver ->rules["allow_spectators"] == 1 ? "Yes" : "No")."</td></tr>"
  . "		<tr><td style=\"padding-right: 5px;\">".checkmark()." <font class=\"color\">Frag Limit:</font></td><td>".($gameserver ->rules["mp_fraglimit"] == 1 ? "Yes" : "No")."</td></tr>"
  . "		<tr><td style=\"padding-right: 5px;\">".checkmark()." <font class=\"color\">Frags Left:</font></td><td>".($gameserver ->rules["mp_fragsleft"] == 1 ? "Yes" : "No")."</td></tr>"
  . "		<tr><td style=\"padding-right: 5px;\">".checkmark()." <font class=\"color\">Allow Spectate:</font></td><td>".($gameserver ->rules["allow_spectators"] == 1 ? "Yes" : "No")."</td></tr>"
  . "		<tr><td style=\"padding-right: 5px;\">".checkmark()." <font class=\"color\">Flashlight:</font></td><td>".($gameserver ->rules["mp_flashlight"] == 1 ? "Yes" : "No")."</td></tr>"
  . "		<tr><td style=\"padding-right: 5px;\">".checkmark()." <font class=\"color\">Footsteps:</font></td><td>".($gameserver ->rules["mp_footsteps"] == 1 ? "Yes" : "No")."</td></tr>"
  . "		</table>"
  . "		</td>"
  . "		<td class=\"row\">"
  . "		<table cellspacing=0 cellpadding=0>"
  . "		<tr><td style=\"padding-right: 5px;\">".checkmark()." <font class=\"color\">Friendly Fire:</font></td><td>".($gameserver ->rules["mp_friendlyfire"] == 1 ? "Yes" : "No")."</td></tr>"
  . "		<tr><td style=\"padding-right: 5px;\">".checkmark()." <font class=\"color\">Reserve Slots:</font></td><td>".$gameserver ->rules["reserve_slots"]."</td></tr>"
  . "		<tr><td style=\"padding-right: 5px;\">".checkmark()." <font class=\"color\">Min Rate:</font></td><td>".spBytes($gameserver ->rules["sv_minrate"])."</td></tr>"
  . "		<tr><td style=\"padding-right: 5px;\">".checkmark()." <font class=\"color\">Max Rate:</font></td><td>".spBytes($gameserver ->rules["sv_maxrate"])."</td></tr>"
  . "		<tr><td style=\"padding-right: 5px;\">".checkmark()." <font class=\"color\">Time Limit:</font></td><td>".$gameserver ->rules["mp_timelimit"]."</td></tr>"
  . "		<tr><td style=\"padding-right: 5px;\">".checkmark()." <font class=\"color\">Time Left:</font></td><td>".nduration($gameserver ->rules["mp_timeleft"])."</td></tr>"
  . "		</table>"
  . "		</td>"
  . "		</tr>"
  . "		</table>";
	break;
	case "hlife_cstrike":
	case "hlife_czero":
$retval="<table cellspacing=0 cellpadding=0 width=\"100%\">"
  . "		<tr>"
  . "		<td class=\"row\">"
  . "		<table cellspacing=0 cellpadding=0>"
  . "		<tr><td style=\"padding-right: 5px;\">".checkmark()." <font class=\"color\">Allow Spectators:</font></td><td>".($gameserver ->rules["allow_spectators"] == 1 ? "Yes" : "No")."</td></tr>"
  . "		<tr><td style=\"padding-right: 5px;\">".checkmark()." <font class=\"color\">Auto Kick:</font></td><td>".($gameserver ->rules["mp_autokick"] == 1 ? "Yes" : "No")."</td></tr>"
  . "		<tr><td style=\"padding-right: 5px;\">".checkmark()." <font class=\"color\">Team Balance:</font></td><td>".($gameserver ->rules["mp_autoteambalance"] == 1 ? "Yes" : "No")."</td></tr>"
  . "		<tr><td style=\"padding-right: 5px;\">".checkmark()." <font class=\"color\">C4 Timer:</font></td><td>".$gameserver ->rules["mp_c4timer"]."</td></tr>"
  . "		<tr><td style=\"padding-right: 5px;\">".checkmark()." <font class=\"color\">Flashlight:</font></td><td>".($gameserver ->rules["mp_flashlight"] == 1 ? "Yes" : "No")."</td></tr>"
  . "		<tr><td style=\"padding-right: 5px;\">".checkmark()." <font class=\"color\">Footsteps:</font></td><td>".($gameserver ->rules["mp_footsteps"] == 1 ? "Yes" : "No")."</td></tr>"
  . "		</table>"
  . "		</td>"
  . "		<td class=\"row\">"
  . "		<table cellspacing=0 cellpadding=0>"
  . "		<tr><td style=\"padding-right: 5px;\">".checkmark()." <font class=\"color\">Friendly Fire:</font></td><td>".($gameserver ->rules["mp_friendlyfire"] == 1 ? "Yes" : "No")."</td></tr>"
  . "		<tr><td style=\"padding-right: 5px;\">".checkmark()." <font class=\"color\">Hostage Penalty:</font></td><td>".$gameserver ->rules["mp_hostagepenalty"]."</td></tr>"
  . "		<tr><td style=\"padding-right: 5px;\">".checkmark()." <font class=\"color\">Reserve Slots:</font></td><td>".$gameserver ->rules["reserve_slots"]."</td></tr>"
  . "		<tr><td style=\"padding-right: 5px;\">".checkmark()." <font class=\"color\">Min Rate:</font></td><td>".spBytes($gameserver ->rules["sv_minrate"])."</td></tr>"
  . "		<tr><td style=\"padding-right: 5px;\">".checkmark()." <font class=\"color\">Max Rate:</font></td><td>".spBytes($gameserver ->rules["sv_maxrate"])."</td></tr>"
  . "		<tr><td style=\"padding-right: 5px;\">".checkmark()." <font class=\"color\">Time Limit:</font></td><td>".$gameserver ->rules["mp_timelimit"]."</td></tr>"
  . "		</table>"
  . "		</td>"
  . "		</tr>"
  . "		</table>";
	break;
	case "hlife_dmc":
	case "hlife_dod":
	case "hlife_tfc":
$retval="<table cellspacing=0 cellpadding=0 width=\"100%\">"
  . "		<tr>"
  . "		<td class=\"row\">"
  . "		<table cellspacing=0 cellpadding=0>"
  
  . "		<tr><td style=\"padding-right: 5px;\">".checkmark()." <font class=\"color\">Flashlight:</font></td><td>".($gameserver ->rules["mp_flashlight"] == 1 ? "Yes" : "No")."</td></tr>"
  . "		<tr><td style=\"padding-right: 5px;\">".checkmark()." <font class=\"color\">Footsteps:</font></td><td>".($gameserver ->rules["mp_footsteps"] == 1 ? "Yes" : "No")."</td></tr>"
  . "		<tr><td style=\"padding-right: 5px;\">".checkmark()." <font class=\"color\">Friendly Fire:</font></td><td>".($gameserver ->rules["mp_friendlyfire"] == 1 ? "Yes" : "No")."</td></tr>"
  . "		</table>"
  . "		</td>"
  . "		<td class=\"row\">"
  . "		<table cellspacing=0 cellpadding=0>"
  . "		<tr><td style=\"padding-right: 5px;\">".checkmark()." <font class=\"color\">Min Rate:</font></td><td>".spBytes($gameserver ->rules["sv_minrate"])."</td></tr>"
  . "		<tr><td style=\"padding-right: 5px;\">".checkmark()." <font class=\"color\">Max Rate:</font></td><td>".spBytes($gameserver ->rules["sv_maxrate"])."</td></tr>"
  . "		<tr><td style=\"padding-right: 5px;\">".checkmark()." <font class=\"color\">Time Limit:</font></td><td>".$gameserver ->rules["mp_timelimit"]."</td></tr>"
  . "		</table>"
  . "		</td>"
  . "		</tr>"
  . "		</table>";
	break;
	
	case "hlife_nsp":
$retval="<table cellspacing=0 cellpadding=0 width=\"100%\">"
  . "		<tr>"
  . "		<td class=\"row\">"
  . "		<table cellspacing=0 cellpadding=0>"
  . "		<tr><td style=\"padding-right: 5px;\">".checkmark()." <font class=\"color\">Allow Spectators:</font></td><td>".($gameserver ->rules["mp_allowspectators"] == 1 ? "Yes" : "No")."</td></tr>"
  . "		<tr><td style=\"padding-right: 5px;\">".checkmark()." <font class=\"color\">Flashlight:</font></td><td>".($gameserver ->rules["mp_flashlight"] == 1 ? "Yes" : "No")."</td></tr>"
  . "		<tr><td style=\"padding-right: 5px;\">".checkmark()." <font class=\"color\">Footsteps:</font></td><td>".($gameserver ->rules["mp_footsteps"] == 1 ? "Yes" : "No")."</td></tr>"
  . "		<tr><td style=\"padding-right: 5px;\">".checkmark()." <font class=\"color\">Force Respawn:</font></td><td>".($gameserver ->rules["mp_forcerespawn"] == 1 ? "Yes" : "No")."</td></tr>"
  . "		</table>"
  . "		</td>"
  . "		<td class=\"row\">"
  . "		<table cellspacing=0 cellpadding=0>"
  . "		<tr><td style=\"padding-right: 5px;\">".checkmark()." <font class=\"color\">Friendly Fire:</font></td><td>".($gameserver ->rules["mp_friendlyfire"] == 1 ? "Yes" : "No")."</td></tr>"
  . "		<tr><td style=\"padding-right: 5px;\">".checkmark()." <font class=\"color\">Min Rate:</font></td><td>".spBytes($gameserver ->rules["sv_minrate"])."</td></tr>"
  . "		<tr><td style=\"padding-right: 5px;\">".checkmark()." <font class=\"color\">Max Rate:</font></td><td>".spBytes($gameserver ->rules["sv_maxrate"])."</td></tr>"
  . "		<tr><td style=\"padding-right: 5px;\">".checkmark()." <font class=\"color\">Time Limit:</font></td><td>".$gameserver ->rules["mp_timelimit"]."</td></tr>"
  . "		</table>"
  . "		</td>"
  . "		</tr>"
  . "		</table>";
	break;
	default:
	$retval="";
	} 
return $retval;
}

}

?>
