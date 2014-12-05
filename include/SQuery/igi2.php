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

include_once($libpath."gameSpy.php");

/**
 * @brief Extends the gameSpy protocol to support IGI2
 * @author Curtis Brown (volfin1@earthlink.net)
 */
class igi2 extends gameSpy
{

  function query_server($getPlayers=TRUE,$getRules=TRUE)
  {       
    $this->playerkeys=array();
    $this->debug=array();
    $this->errstr="";
    $this->password=-1;
    
   $command="\\status\\";
	
    if(!($result=$this->_sendCommand($this->address, $this->queryport, $command))) {
      $this->errstr="No reply received";
      return FALSE;
    }

   //$fp=fopen("out.bin","w");
  //fwrite($fp,$result);
  //fclose($fp);
    $this->online = TRUE;


    // xxx: not a nice way 
   $pos=strpos($result,'\player_');
  if ($pos)
   {	
   
   $players=substr($result,$pos);
   $rules=substr($result,0,$pos);
 
    $this->_processServerInfo($result);
    $this->_processRules($rules); 

    $this->_processPlayers($players);   
    }
  else {
  // here if no players on server
  $this->_processServerInfo($result);
  $this->_processRules($result);
 }
  
    return TRUE;
  }  


/**
   * @internal @brief Process the given raw data and stores everything
   *
   * @param rawdata data that has the basic server infos
   * @return TRUE on success 
   */
  function _processServerInfo($rawdata)
  {

    $temp=explode("\\",$rawdata);
    $count=count($temp);
    for($i=1;$i<$count;$i++) {
      $data[$temp[$i]]=$temp[++$i];
    }

if ($data["gamename"] <> "") {$this->gamename = $data["gamename"];} 
if ($data["game_id"] <> "") {$this->gamename = $data["game_id"];}     // for BF:V
    $this->hostport = $data["hostport"];
    $this->gameversion = $data["gamever"];
    $this->servertitle = $data["hostname"];
    $this->maptitle = isset($data["maptitle"]) ? $data["maptitle"] : "";
    $this->mapname = $data["mapname"];
    $this->gametype = $data["gametype"];
    $this->numplayers = $data["numplayers"];
    $this->maxplayers = $data["maxplayers"];
    $this->teamscore1 = $data["score_t0"];
    $this->teamscore2 = $data["score_t1"];
    $this->team1 = $data["team_t0"];
    $this->team2 = $data["team_t1"];

 if(isset($data["password"]) && ($data["password"]==0 || $data["password"]==1)) {  
      $this->password=$data["password"];
    }
    
    if(!$this->gamename) {
      $this->gamename="unknown";
    }

    return TRUE;
  }


  function sortPlayers($players, $sortkey="name") 
  {
    if(!sizeof($players)) {
      return array();
    }
    switch($sortkey) {
    case "ping":
      uasort($players, array("igi2", "_sortbyPing"));
      break;
    case "deaths":
      uasort($players, array("igi2", "_sortbyDeath"));
      break;
    case "score":
      uasort($players, array("igi2", "_sortbyScore"));
      break;
    case "name":
      uasort($players, array("igi2", "_sortbyName"));
      break;
    default:
      $players=parent::sortPlayers($players, $sortkey);
    }
    return ($players);
  }


  // private methods

  function _sortbyPing($a, $b) 
  {
    if($a["ping"]==$b["ping"]) { return 0; } 
    elseif($a["ping"]<$b["ping"]) { return 1; }
    else { return -1; }
  }

  function _sortbyDeath($a, $b) 
  {
    if($a["deaths"]==$b["deaths"]) { return 0; } 
    elseif($a["deaths"]<$b["deaths"]) { return 1; }
    else { return -1; }
  }

  function _sortbyScore($a, $b) 
  {
    if($a["score"]==$b["score"]) { return 0; } 
    elseif($a["score"]<$b["score"]) { return 1; }
    else { return -1; }
  }  
function _sortbyName($a, $b) 
  {
    if($a["name"]==$b["name"]) { return 0; } 
    elseif($a["name"]<$b["name"]) { return 1; }
    else { return -1; }
  }  

  function _getClassName() 
  {
    return "igi2";
  }

/**
   * @internal @brief Extracts the players out of the given data 
   *
   * @param rawPlayerData data with players
   * @return TRUE on success 
   */
  function _processPlayers($rawPlayerData) 
  {
    $temp=explode("\\", $rawPlayerData);
   
    $count=count($temp);
  // use $l as the playerid because the game does not number players consecutively! first player might be player 20 or who knows!
  // we just look for changes in this number and increment $l accordingly.
    $l=-1;
    for($i=1;$i<$count;$i++) {
      list($var, $playerid)=explode("_", $temp[$i]);
      if ($curid<>$playerid)
	{ $l++;
      	  $curid=$playerid;
	}
      
      switch($var) {
      case "player":
      case "playername":
	$this->playerkeys["name"]=TRUE;
	$players[$l]["name"]=$temp[++$i];	    
	break;
      case "team":
      case "teamname":
	$this->playerkeys["team"]=TRUE;
	$this->playerteams[$l]=$temp[++$i]+1;
	$players[$l]["team"]=$temp[$i]+1;
	if ($temp[$i]==0) $this->teamcnt1++;
           else $this->teamcnt2++;	    
	break;
      case "frags":
	 $this->playerkeys["score"]=TRUE;
	$players[$l]["score"]=$temp[++$i];	    
	break;
      case "ping":
	 $this->playerkeys["ping"]=TRUE;
        $players[$l]["ping"]=$temp[++$i];	    
	break;
      case "deaths":
	$this->playerkeys["deaths"]=TRUE;
	$players[$l]["deaths"]=$temp[++$i];
	break;
      default:
	$players[$l][$var]=$temp[++$i];
	$this->playerkeys[$var]=TRUE;
      }
    }
	
    $this->players=$players;
   
    return TRUE;
  }

/* this is for game specific cvar displays  */
function docvars($gameserver)
{
  switch ($gameserver->gamename)
{
   case "serioussam":
   case "serioussamse":
$retval="<table cellspacing=0 cellpadding=0 width=\"100%\">"
  . "		<tr>"
  . "		<td class=\"row\">"
  . "		<table cellspacing=0 cellpadding=0>"
  . "		<tr><td style=\"padding-right: 5px;\">".checkmark()." <font class=\"color\">Game Mode:</font></td><td>".$gameserver ->rules["gamemode"]."</td></tr>"
  . "		<tr><td style=\"padding-right: 5px;\">".checkmark()." <font class=\"color\">Friendly Fire:</font></td><td>".($gameserver ->rules["friendlyfire"] == 1 ? "Yes" : "No")."</td></tr>"
  . "		<tr><td style=\"padding-right: 5px;\">".checkmark()." <font class=\"color\">Ammo Stays:</font></td><td>".($gameserver ->rules["ammostays"] == 1 ? "Yes" : "No")."</td></tr>"
  . "		<tr><td style=\"padding-right: 5px;\">".checkmark()." <font class=\"color\">Allow Health:</font></td><td>".($gameserver ->rules["allowhealth"] == 1 ? "Yes" : "No")."</td></tr>"
  . "		<tr><td style=\"padding-right: 5px;\">".checkmark()." <font class=\"color\">Allow Armor:</font></td><td>".($gameserver ->rules["allowarmor"] == 1 ? "Yes" : "No")."</td></tr>"
  . "		<tr><td style=\"padding-right: 5px;\">".checkmark()." <font class=\"color\">Dedicated:</font></td><td>".($gameserver ->rules["dedicatedserver"] == 'yes' ? "Yes" : "No")."</td></tr>"
  
  . "		</table>"
  . "		</td>"
  . "		<td class=\"row\">"
  . "		<table cellspacing=0 cellpadding=0>"
. "		<tr><td style=\"padding-right: 5px;\">".checkmark()." <font class=\"color\">Difficulty:</font></td><td>".$gameserver ->rules["difficulty"]."</td></tr>"
  . "		<tr><td style=\"padding-right: 5px;\">".checkmark()." <font class=\"color\">Weapons Stay:</font></td><td>".($gameserver ->rules["weaponsstay"] == 1 ? "Yes" : "No")."</td></tr>"
  . "		<tr><td style=\"padding-right: 5px;\">".checkmark()." <font class=\"color\">Health/Armor Stays:</font></td><td>".($gameserver ->rules["healthandarmorstays"] == 1 ? "Yes" : "No")."</td></tr>"
  . "		<tr><td style=\"padding-right: 5px;\">".checkmark()." <font class=\"color\">Infinite Ammo:</font></td><td>".($gameserver ->rules["infiniteammo"]==1 ? "Yes" : "No")."</td></tr>"
  . "		<tr><td style=\"padding-right: 5px;\">".checkmark()." <font class=\"color\">Respawn In-Place:</font></td><td>".($gameserver ->rules["respawninplace"] == 1 ? "Yes" : "No")."</td></tr>"
  
  . "		</table>"
  . "		</td>"
  . "		</tr>"
  . "		</table>";
   break;	
   default:	
$retval="<table cellspacing=0 cellpadding=0 width=\"100%\">"
  . "		<tr>"
  . "		<td class=\"row\">"
  . "		<table cellspacing=0 cellpadding=0>"
  . "		<tr><td style=\"padding-right: 5px;\">".checkmark()." <font class=\"color\">Game Mode:</font></td><td>".$gameserver ->rules["gamemode"]."</td></tr>"
  . "		<tr><td style=\"padding-right: 5px;\">".checkmark()." <font class=\"color\">Time Left:</font></td><td>".$gameserver ->rules["timeleft"]."</td></tr>"
  . "		<tr><td style=\"padding-right: 5px;\">".checkmark()." <font class=\"color\">Auto Balance:</font></td><td>".($gameserver ->rules["autobalance"] == 1 ? "Yes" : "No")."</td></tr>"
  . "		<tr><td style=\"padding-right: 5px;\">".checkmark()." <font class=\"color\">Max Ping:</font></td><td>".$gameserver ->rules["pingmax"]."</td></tr>"
  . "		<tr><td style=\"padding-right: 5px;\">".checkmark()." <font class=\"color\">Timelimit:</font></td><td>".$gameserver ->rules["timelimit"]."</td></tr>"
  
  . "		</table>"
  . "		</td>"
  . "		<td class=\"row\">"
  . "		<table cellspacing=0 cellpadding=0>"
. "		<tr><td style=\"padding-right: 5px;\">".checkmark()." <font class=\"color\">Team Damage:</font></td><td>".($gameserver ->rules["teamdamage"] == 1 ? "Yes" : "No")."</td></tr>"
  . "		<tr><td style=\"padding-right: 5px;\">".checkmark()." <font class=\"color\">Snipers:</font></td><td>".($gameserver ->rules["snipers"] == 1 ? "Yes" : "No")."</td></tr>"
  . "		<tr><td style=\"padding-right: 5px;\">".checkmark()." <font class=\"color\">Dedicated:</font></td><td>".($gameserver ->rules["dedicated"] == 1 ? "Yes" : "No")."</td></tr>"
  . "		<tr><td style=\"padding-right: 5px;\">".checkmark()." <font class=\"color\">Bomb Repository:</font></td><td>".($gameserver ->rules["bombrepos"]==1 ? "Yes" : "No")."</td></tr>"
  . "		<tr><td style=\"padding-right: 5px;\">".checkmark()." <font class=\"color\">Spec Mode:</font></td><td>".($gameserver ->rules["leased"] == 1 ? "Yes" : "No")."</td></tr>"
  
  . "		</table>"
  . "		</td>"
  . "		</tr>"
  . "		</table>";
	break;
}
return $retval;
}

}
?>
