<?php
// Improved ALP security exploit fix - added on July 19, 2006
defined('SQUERY_INVOKED') or die('No access.');
/*
 *  gsQuery - Querys various game servers
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

include_once("gsQuery.php");


/**
 * @brief Uses the Quake 3 protcol to communicate with the server
 * @author Jeremias Reith (jr@terragate.net)
 * @version $Id: mohaa.php,v 1.3 2006/07/19 21:33:15 synth_spring Exp $
 *
 * This class can communicate with most games based on the Quake 3
 * engine.
 */
class mohaa extends gsQuery
{

  function query_server($getPlayers=TRUE,$getRules=TRUE)
  { 
    $this->playerkeys=array();
    $this->debug=array();
    $this->password=-1;
      
    $command="\xFF\xFF\xFF\xFF\x02getstatus\x0a\x00";
    if(!($result=$this->_sendCommand($this->address,$this->queryport,$command))) {
      $this->errstr="No reply received";
      return FALSE;
    }


    $temp=explode("\x0a",$result);
    $rawdata=explode("\\",substr($temp[1],1,strlen($temp[1])));
    
    // get rules and basic infos
    for($i=0;$i< count($rawdata);$i++) {
      switch ($rawdata[$i++]) {
      case "g_gametypestring":
      case "g_gametype":     // for COD, SOF2
	$this->gametype=$rawdata[$i];
	break;
      case "gamename":
	$this->gamename=$rawdata[$i];
	break;
      case "shortversion":  // for COD
      case "game_version":  // for SOF2
      case "version":
	$this->gameversion=$rawdata[$i];
	break;
      case "sv_hostname":
	$this->servertitle=$rawdata[$i];
	break;
      case "mapname":
	$this->mapname=$rawdata[$i];
	break;
      case "scorelimit":  //sof2
	$this->scorelimit=$rawdata[$i];
	break;
      case "team_redName":  //sof2
	$this->team2=$rawdata[$i];
	break;
      case "team_blueName":  //sof2
	$this->team1=$rawdata[$i];
	break;
      case "redscore":  //sof2
	$this->teamscore2=$rawdata[$i];
	break;
      case "bluescore":  //sof2
	$this->teamscore1=$rawdata[$i];
	break;
      case "pswrd":  // for COD
      case "g_needpass":
	$this->password=$rawdata[$i];
	break;
      case "sv_maplist":
	$this->maplist=explode(" ", $rawdata[$i]);
	break;
      case "sv_maxClients":
	$this->rules["sv_maxclients"]=$rawdata[$i];
	break; 
      default:
	$rawdata[$i-1]=strtolower($rawdata[$i-1]);
	$this->rules[$rawdata[$i-1]] = $rawdata[$i];
      }
    }

    // for MoHAA
    if(eregi("Medal of Honor", $this->gameversion)) {
      $this->gamename="mohaa";
    }
   // for Castle Wolfenstein
	if(eregi("Wolf", $this->gameversion)) {
      $this->gamename="rtcw";
    }
   // for Castle Wolfenstein Enemy Territory
	if(eregi("ET", $this->gameversion)) {
      $this->gamename="rtcw-et";
    }
   // for Jedi Academy 2
	if(eregi("JK2MP", $this->gameversion)) {
      $this->gamename="jk2";
    }
   // for Jedi Academy 3
	if(eregi("JAmp", $this->gameversion)) {
      $this->gamename="jk3";
    }
   // for Quake 3
	if(eregi("Q3", $this->gameversion)) {
      $this->gamename="q3";
    }    
   // for Soldier of Fortune II
	if(eregi("SOF2MP", $this->gameversion)) {
      $this->gamename="sof2mp";
    }    
// for Star Trek Elite Force
	if(eregi("ST:V HM", $this->gameversion)) {
      $this->gamename="steforce";
    }  
// for Star Trek Elite Force 2
	if(eregi("Elite Force II", $this->gameversion)) {
      $this->gamename="steforce2";
    }  


    if(!empty($this->maplist)) {
      $i=0;
      while($this->mapname!=$this->maplist[$i++] && $i<count($this->maplist));
      $this->nextmap=$this->maplist[$i % count($this->maplist)];
    }
    
    //for MoHAA
    $this->mapname=preg_replace("/.*\//", "", $this->mapname);
    
    $this->hostport = $this->queryport;
    $this->maxplayers = $this->rules["sv_maxclients"]-$this->rules["sv_privateClients"];
    
    //get playerdata
    $temp=substr($result,strlen($temp[0])+strlen($temp[1])+1,strlen($result));
    $allplayers=explode("\n", $temp);
    $this->numplayers=count($allplayers)-2;
    
    // get players
    if(count($allplayers)-2 && $getPlayers) {

    $this->_processPlayers($allplayers);
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
    $command="\xFF\xFF\xFF\xFF\x02rcon ".$rcon_pwd." ".$command."\x0a\x00";
    if(!($result=$this->_sendCommand($this->address,$this->queryport,$command))) {
      $this->errstr="Error sending rcon command";
      return FALSE;
    } else {
      return $result;
    }
  } 

  /**
   * @brief htmlizes the given raw string
   *
   * @param var a raw string from the gameserver that might contain special chars
   * @return a html version of the given string
   */
  function htmlize($var) 
  {
    $var = htmlspecialchars($var);
    while(ereg('\^([0-9])', $var)) {
      foreach(array('black', 'red', 'darkgreen', 'yellow', 'blue', 'cyan', 'pink', 'red-night', 'blue-night', 'white') as $num_color => $name_color) {
	if (ereg('\^([0-9])(.*)\^([0-9])', $var)) {
	  $var = preg_replace("#\^".$num_color."(.*)\^([0-9])#Usi", "<span class=\"gsquery-".$name_color."\">$1</span>^$2", $var);
	} else {
	  $var = preg_replace("#\^".$num_color."(.*)$#Usi", "<span class=\"gsquery-".$name_color."\">$1</span>", $var);
	}
      }
    }
    return $var;
  }

function _processPlayers($allplayers)
{


      for($i=1;$i< count($allplayers)-1;$i++) {
	// match with team info
	if(preg_match("/(\d+)[^0-9](\d+)[^0-9](\d+)[^0-9]\"(.*)\"/", $allplayers[$i], $curplayer)) {
	  
	  $players[$i-1]["name"]=$curplayer[4];
	  $players[$i-1]["score"]=$curplayer[1];
	  $players[$i-1]["ping"]=$curplayer[2];	
	  // this needed?

  if ($this->gametype!="dm"&&$this->gametype!="DM")
  {
	  $players[$i-1]["team"]=$curplayer[3];
	$this->playerteams[$i-1]=$curplayer[3];
		if ($this->playerteams[$i-1] == 2)
		$this->teamcnt1++;
		elseif ($this->playerteams[$i-1] == 1)
		$this->teamcnt2++;
		elseif ($this->playerteams[$i-1] == 3)
		$this->spec++;
	  $teamInfo=TRUE;
  }
	  $pingOnly=FALSE;
	} elseif(preg_match("/(\d+)[^0-9](\d+)[^0-9]\"(.*)\"/", $allplayers[$i], $curplayer)) {
	  $players[$i-1]["name"]=$curplayer[3];
	  $players[$i-1]["score"]=$curplayer[1];
	  $players[$i-1]["ping"]=$curplayer[2];	
	  $pingOnly=FALSE;
	  $teamInfo=FALSE;
	}
	else {
	  if(preg_match("/(\d+).\"(.*)\"/", $allplayers[$i], $curplayer)) {
	    $players[$i-1]["name"]=$curplayer[2];
	    $players[$i-1]["ping"]=$curplayer[1];
	    $pingOnly=TRUE; // for MoHAA
	  }
	  else {
	    $this->errstr="Could not extract player infos!";
	    return FALSE;
	  }
	}
      }
      $this->playerkeys["name"]=TRUE;
      if(!$pingOnly) {
	$this->playerkeys["score"]=TRUE;
	if($teamInfo) {
	  $this->playerkeys["team"]=TRUE;
	}
      }
      $this->playerkeys["ping"]=TRUE;
      $this->players=$players;
}

/* this is for game specific cvar displays  */
function docvars($gameserver)
{
$retval="<table cellspacing=0 cellpadding=0 width=\"100%\">"
  . "		<tr>"
  . "		<td class=\"row\">"
  . "		<table cellspacing=0 cellpadding=0>"
  . "		<tr><td style=\"padding-right: 5px;\">".checkmark()." <font class=\"color\">Protocol:</font></td><td>".$gameserver ->rules["protocol"]."</td></tr>"
  . "		<tr><td style=\"padding-right: 5px;\">".checkmark()." <font class=\"color\">Mod Name:</font></td><td>".$gameserver ->rules["fs_game"]."</td></tr>"
  . "		<tr><td style=\"padding-right: 5px;\">".checkmark()." <font class=\"color\">Time Limit:</font></td><td>".$gameserver ->rules["timelimit"]."</td></tr>"
  . "		<tr><td style=\"padding-right: 5px;\">".checkmark()." <font class=\"color\">Max Rate:</font></td><td>".spBytes($gameserver ->rules["sv_maxrate"])."</td></tr>"
  . "		<tr><td style=\"padding-right: 5px;\">".checkmark()." <font class=\"color\">Min Ping:</font></td><td>".$gameserver ->rules["sv_minping"]." ms</td></tr>"
  . "		<tr><td style=\"padding-right: 5px;\">".checkmark()." <font class=\"color\">Max Ping:</font></td><td>".$gameserver ->rules["sv_maxping"]." ms</td></tr>"
  . "		</table>"
  . "		</td>"
  . "		<td class=\"row\">"
  . "		<table cellspacing=0 cellpadding=0>"
  . "		<tr><td style=\"padding-right: 5px;\">".checkmark()." <font class=\"color\">Private Clients:</font></td><td>".($gameserver ->rules["sv_privateclients"]>0 ? $gameserver ->rules["sv_privateclients"] : "None")."</td></tr>"
  . "		<tr><td style=\"padding-right: 5px;\">".checkmark()." <font class=\"color\">Flood Protection:</font></td><td>".($gameserver ->rules["sv_floodprotect"] == 1 ? "Yes" : "No")."</td></tr>"
  . "		<tr><td style=\"padding-right: 5px;\">".checkmark()." <font class=\"color\">Allow Download:</font></td><td>".($gameserver ->rules["sv_allowdownload"]==1 ? "Yes" : "No")."</td></tr>"
  . "		<tr><td style=\"padding-right: 5px;\">".checkmark()." <font class=\"color\">Force Ready:</font></td><td>".($gameserver ->rules["g_forceready"] == 1 ? "Yes" : "No")."</td></tr>"
  . "		<tr><td style=\"padding-right: 5px;\">".checkmark()." <font class=\"color\">Force Respawn:</font></td><td>".($gameserver ->rules["g_forcerespawn"] == 1 ? "Yes" : "No")."</td></tr>"
  . "		<tr><td style=\"padding-right: 5px;\">".checkmark()." <font class=\"color\">Join Time:</font></td><td>".$gameserver ->rules["g_allowjointime"]."</td></tr>"
  . "		</table>"
  . "		</td>"
  . "		</tr>"
  . "		</table>";
return $retval;
}

}
?>