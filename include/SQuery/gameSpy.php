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
 * @brief Uses the gameSpy protcol to communicate with the server
 * @author Jeremias Reith (jr@terragate.net)
 * @version $Id: gameSpy.php,v 1.3 2006/07/19 21:33:15 synth_spring Exp $
 * @bug some games does not escape the backslash, so we have a problem when somebody has a backlsash in its name
 *
 * The following games have been tested with this class:
 *
 *   - Unreal Tournamnet (and most mods)
 *   - Unreal Tournamnet 2003 (and most mods)
 *   - Battlefield 1942 (and most mods)
 */
class gameSpy extends gsQuery
{

  
  function query_server($getPlayers=TRUE,$getRules=TRUE)
  {       
    $this->playerkeys=array();
    $this->debug=array();
    $this->errstr="";
    $this->password=-1;
    
    $cmd="\\basic\\\\info\\";
    if(!($response=$this->_sendCommand($this->address, $this->queryport, $cmd))) {
      $this->errstr="No reply received";
      return FALSE;
    }
   
    $this->_processServerInfo($response);

    $this->online=TRUE;

    // get rules
    if($getRules) {
      $cmd="\\rules\\";
      if(!($response2=$this->_sendCommand($this->address, $this->queryport, $cmd))) {
	return FALSE;
      } 
       
      $response2=$response.$response2;
      
      $this->_processRules($response2);
    }

 
    // get players
    if($this->numplayers && $getPlayers) {
      $cmd="\\players\\";
      if(!($response=$this->_sendCommand($this->address, $this->queryport, $cmd))) {
	//return FALSE;
      } 
      
      $this->_processPlayers($response);
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
// MOHAA fix
 if(eregi("mohaa", $this->gamename)) {
      $this->gamename="mohaa";
    }

    $this->hostport = $data["hostport"];
    $this->gameversion = $data["gamever"];

/*
	// FOR Rallisport Challenge
    $tmp=$data["hostname"];
    for($i=2;$i<strlen($tmp);$i=$i+4)
    {
     $this->servertitle=$this->servertitle.chr(hexdec($tmp[$i].$tmp[$i+1]));
	}	
*/

    $this->servertitle = $data["hostname"];
    $this->maptitle = isset($data["maptitle"]) ? $data["maptitle"] : "";
    $this->mapname = $data["mapname"];
    $this->gametype = $data["gametype"];
    $this->numplayers = $data["numplayers"];
    $this->maxplayers = $data["maxplayers"];
   
    // fix for MOHAA
    if ($this->gamename=="mohaa")
    {
    $temp=explode("/",$this->mapname);
    $this->mapname=$temp[1];
    }
    if(isset($data["password"]) && ($data["password"]==0 || $data["password"]==1)) {  
      $this->password=$data["password"];
    }
    // for Tactical Ops
    if(eregi("TO", $this->gametype)) {
      $this->gamename="tacops";
    }
    
    if(!$this->gamename) {
      $this->gamename="unknown";
    }

    return TRUE;
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
    $this->playerkeys["name"]=TRUE;
    $count=count($temp);
    
    for($i=1;$i<$count;$i++) {
      list($var, $playerid)=explode("_", $temp[$i]);
      switch($var) {
      case "player":
      case "playername":
	$players[$playerid]["name"]=$temp[++$i];
     	break;
      case "team":	
      case "teamname":
	if ($playerid=="t0"||$playerid=="t1"||$playerid=="t2")
	{
	if ($playerid=="t0"||$playerid=="t2") $this->team1=ucwords($temp[++$i]);
	else $this->team2=ucwords($temp[++$i]);
	}
	else {
	$this->playerkeys["team"]=TRUE;
	$chk=$this->gamename;
	if ($chk=="ut2"||$chk=="ut2004"||$chk=="ut"||$chk=="chaser"||$chk=="descent3"||$chk=="tacops"||$chk=="unreal") {
	$this->playerteams[$playerid]=$temp[++$i]+1;
    	$players[$playerid]["team"]=$temp[$i]+1;
        if ($temp[$i]+1==0) {
  		$players[$playerid]["team"]=3;
		$this->playerteams[$playerid]=3;
		$this->spec++;
		
		}
	elseif (($temp[$i]+1)==1) $this->teamcnt1++;
           else $this->teamcnt2++;
	}
	else {
	$this->playerteams[$playerid]=$temp[++$i];
    	$players[$playerid]["team"]=$temp[$i];
	if ($temp[$i]==0) {
  		$players[$playerid]["team"]=3;
		$this->playerteams[$playerid]=3;
		$this->spec++;
		
		}
	elseif ($temp[$i]==1) $this->teamcnt1++;
           else $this->teamcnt2++;
	}
	}
	break;
      case "frags":
	$players[$playerid]["score"]=$temp[++$i];	
	$this->playerkeys["score"]=TRUE;    
	break;
      default:
	$players[$playerid][$var]=$temp[++$i];
	$this->playerkeys[$var]=TRUE;
      }
    }
    $this->players=$players;
    return TRUE;
  }

  /**
   * @internal @brief Extracts the rules out of the given data 
   *
   * @param rawData data with rules
   * @return TRUE on success 
   */  
  function _processRules($rawData)
  {
    $temp=explode("\\",$rawData);
    $count=count($temp);
    for($i=1;$i<$count;$i++) { 
      if($temp[$i]!="queryid" && $temp[$i]!="final" && $temp[$i]!="password") {
// MINE	
$temp[$i]=strtolower($temp[$i]);
$this->rules[$temp[$i]]=$temp[++$i]; 
      } else {
	if($temp[$i++]=="password") {
	  switch(strtolower($temp[$i]))
	  {
		case "true":
		$this->password=1;
		break;
		case "false":
		$this->password=0;
		break;
		case "1":
		$this->password=1;
		break;
		case "0":
		$this->password=0;
		break;
		default:
	  }	
	  	  
	}
      }
    } 

    return TRUE;
  }
  
  /**
   * @internal @brief sorts the given gamespy data
   *
   * @param data raw data to sort
   * @return raw data sorted
   */
  function _sortByQueryId($data)
  {
    $result="";
    $data=preg_replace("/\\\final\\\/", "", $data);
    $exploded_data=explode("\\queryid\\", $data);
    $count=count($exploded_data);
    for($i=0;$i<$count-1;$i++) { 
      preg_match("/^\d+\.(\d+)/", $exploded_data[$i+1], $id);
      $sorted_data[$id[1]]=$exploded_data[$i];
      $exploded_data[$i+1]=substr($exploded_data[$i+1],strlen($id[0]-1),strlen($exploded_data[$i+1]));
    }

    if(!$sorted_data) {
      // the request is probably incomplete  
      return $data;
    }

    // sort the hash
    ksort($sorted_data);
    foreach($sorted_data as $key => $value) {
      $result.=isset($value) ? $value : "";
    }
    return($result);
  }  

  function _sendCommand($address, $port, $command, $timeout=500000)
  {
    $data=parent::_sendCommand($address, $port, $command, $timeout);
    if(!$data) {
      return FALSE;
    }
    return $this->_sortByQueryId($data);
  }


  function _getClassName() 
  {
    return "gameSpy";
  }

function htmlize($var) 
  {
    $var = htmlspecialchars($var);
    while(ereg('\^([0-9])', $var)) {
      foreach(array('orange', 'red', 'darkgreen', 'yellow', 'blue', 'cyan', 'pink', 'white', 'black', 'yellow') as $num_color => $name_color) {
	if (ereg('\^([0-9])(.*)\^([0-9])', $var)) {
	  $var = preg_replace("#\^".$num_color."(.*)\^([0-9])#Usi", "<span class=\"gsquery-".$name_color."\">$1</span>^$2", $var);
	} else {
	  $var = preg_replace("#\^".$num_color."(.*)$#Usi", "<span class=\"gsquery-".$name_color."\">$1</span>", $var);
	}
      }
    }
    return $var;
  }

/* this is for game specific cvar displays  */
function docvars($gameserver)
{

switch(strtolower($gameserver->gamename))
	{
	case "ut2":	// 2003
	case "ut2004":  // 2004
$retval="<table cellspacing=0 cellpadding=0 width=\"100%\">"
  . "		<tr>"
  . "		<td class=\"row\">"
  . "		<table cellspacing=0 cellpadding=0>"
. "		<tr><td style=\"padding-right: 5px;\">".checkmark()." <font class=\"color\">Game Mode:</font></td><td>".$gameserver ->rules["gamemode"]."</td></tr>"
."              </table>"
  . "		</td>"
  . "		<td class=\"row\">"
  . "		<table cellspacing=0 cellpadding=0>"

  . "		</table>"
  . "		</td>"
  . "		</tr>"
  . "		</table>";
	break;
	case "descent3":
$retval="<table cellspacing=0 cellpadding=0 width=\"100%\">"
  . "		<tr>"
  . "		<td class=\"row\">"
  . "		<table cellspacing=0 cellpadding=0>"
  . "		<tr><td style=\"padding-right: 5px;\">".checkmark()." <font class=\"color\">Game Mode:</font></td><td>".$gameserver ->rules["gamemode"]."</td></tr>"
  . "		<tr><td style=\"padding-right: 5px;\">".checkmark()." <font class=\"color\">Time Limit:</font></td><td>".$gameserver ->rules["timelimit"]."</td></tr>"
  . "		<tr><td style=\"padding-right: 5px;\">".checkmark()." <font class=\"color\">Frag Limit:</font></td><td>".$gameserver ->rules["fraglimit"]."</td></tr>"
  
  
  . "		</table>"
  . "		</td>"
  . "		<td class=\"row\">"
  . "		<table cellspacing=0 cellpadding=0>"
  . "		<tr><td style=\"padding-right: 5px;\">".checkmark()." <font class=\"color\">Team Play:</font></td><td>".$gameserver ->rules["teamplay"]."</td></tr>"
  . "		<tr><td style=\"padding-right: 5px;\">".checkmark()." <font class=\"color\">Powerups:</font></td><td>".($gameserver ->rules["randpowerup"] == 1 ? "Yes" : "No")."</td></tr>"
  . "		<tr><td style=\"padding-right: 5px;\">".checkmark()." <font class=\"color\">Bright Ships:</font></td><td>".($gameserver ->rules["brightships"] == 1 ? "Yes" : "No")."</td></tr>"
  . "		</table>"
  . "		</td>"
  . "		</tr>"
  . "		</table>";
	break;
	case "il2sturmovik":
	case "il2sturmovikfb":
$retval="<table cellspacing=0 cellpadding=0 width=\"100%\">"
  . "		<tr>"
  . "		<td class=\"row\">"
  . "		<table cellspacing=0 cellpadding=0>"
  . "		<tr><td style=\"padding-right: 5px;\">".checkmark()." <font class=\"color\">Game Mode:</font></td><td>".$gameserver ->rules["gamemode"]."</td></tr>"
  . "		<tr><td style=\"padding-right: 5px;\">".checkmark()." <font class=\"color\">Overheating:</font></td><td>".($gameserver ->rules["engineoverheat"] == 1 ? "Yes" : "No")."</td></tr>"
  . "		<tr><td style=\"padding-right: 5px;\">".checkmark()." <font class=\"color\">Flutter FX:</font></td><td>".($gameserver ->rules["fluttereffects"] == 1 ? "Yes" : "No")."</td></tr>"
  . "		<tr><td style=\"padding-right: 5px;\">".checkmark()." <font class=\"color\">Torque/Gyro FX:</font></td><td>".($gameserver ->rules["torquegyroeffects"] == 1 ? "Yes" : "No")."</td></tr>"
  . "		<tr><td style=\"padding-right: 5px;\">".checkmark()." <font class=\"color\">Realistic Guns:</font></td><td>".($gameserver ->rules["realisticgunnery"] == 1 ? "Yes" : "No")."</td></tr>"
  . "		<tr><td style=\"padding-right: 5px;\">".checkmark()." <font class=\"color\">Limited Ammo:</font></td><td>".($gameserver ->rules["limitedammo"] == 1 ? "Yes" : "No")."</td></tr>"
  
  . "		</table>"
  . "		</td>"
  . "		<td class=\"row\">"
  . "		<table cellspacing=0 cellpadding=0>"
  . "		<tr><td style=\"padding-right: 5px;\">".checkmark()." <font class=\"color\">Turbulence:</font></td><td>".($gameserver ->rules["windturbulence"] == 1 ? "Yes" : "No")."</td></tr>"
  . "		<tr><td style=\"padding-right: 5px;\">".checkmark()." <font class=\"color\">Stall/Spin:</font></td><td>".($gameserver ->rules["stallsspins"] == 1 ? "Yes" : "No")."</td></tr>"
  . "		<tr><td style=\"padding-right: 5px;\">".checkmark()." <font class=\"color\">Vulnerability:</font></td><td>".($gameserver ->rules["vulnerability"] == 1 ? "Yes" : "No")."</td></tr>"
  . "		<tr><td style=\"padding-right: 5px;\">".checkmark()." <font class=\"color\">Blackouts:</font></td><td>".($gameserver ->rules["blackoutsredouts"] == 1 ? "Yes" : "No")."</td></tr>"
  . "		<tr><td style=\"padding-right: 5px;\">".checkmark()." <font class=\"color\">Realistic Landing:</font></td><td>".($gameserver ->rules["realisticlandings"] == 1 ? "Yes" : "No")."</td></tr>"
  . "		<tr><td style=\"padding-right: 5px;\">".checkmark()." <font class=\"color\">Limited Fuel:</font></td><td>".($gameserver ->rules["limitedfuel"] == 1 ? "Yes" : "No")."</td></tr>"
  . "		</table>"
  . "		</td>"
  . "		</tr>"
  . "		</table>";
	break;	
	case "mohaa":
$retval="<table cellspacing=0 cellpadding=0 width=\"100%\">"
  . "		<tr>"
  . "		<td class=\"row\">"
  . "		<table cellspacing=0 cellpadding=0>"
  . "		<tr><td style=\"padding-right: 5px;\">".checkmark()." <font class=\"color\">FragLimit:</font></td><td>".$gameserver ->rules["fraglimit"]."</td></tr>"
  . "		</table>"
  . "		</td>"
  . "		<td class=\"row\">"
  . "		<table cellspacing=0 cellpadding=0>"
  . "		<tr><td style=\"padding-right: 5px;\">".checkmark()." <font class=\"color\">TimeLimit:</font></td><td>".$gameserver ->rules["timelimit"]."</td></tr>"
  . "		</table>"
  . "		</td>"
  . "		</tr>"
  . "		</table>";
	break;
	case "globalops":
$retval="<table cellspacing=0 cellpadding=0 width=\"100%\">"
  . "		<tr>"
  . "		<td class=\"row\">"
  . "		<table cellspacing=0 cellpadding=0>"
  . "		<tr><td style=\"padding-right: 5px;\">".checkmark()." <font class=\"color\">Server Mode:</font></td><td>".$gameserver ->rules["gamemode"]."</td></tr>"
  . "		<tr><td style=\"padding-right: 5px;\">".checkmark()." <font class=\"color\">Time Left:</font></td><td>".$gameserver ->rules["timeleft"]."</td></tr>"
  . "		<tr><td style=\"padding-right: 5px;\">".checkmark()." <font class=\"color\">Frag Limit:</font></td><td>".$gameserver ->rules["fraglimit"]."</td></tr>"


  . "		</table>"
  . "		</td>"
  . "		<td class=\"row\">"
  . "		<table cellspacing=0 cellpadding=0>"
  . "		<tr><td style=\"padding-right: 5px;\">".checkmark()." <font class=\"color\">Team Play:</font></td><td>".($gameserver ->rules["teamplay"] == True ? "Yes" : "No")."</td></tr>"
  . "		<tr><td style=\"padding-right: 5px;\">".checkmark()." <font class=\"color\">OS Type:</font></td><td>".$gameserver ->rules["os"]."</td></tr>"

  . "		</table>"
  . "		</td>"
  . "		</tr>"
  . "		</table>";
	break;
	case "tacops":
	$retval="<table cellspacing=0 cellpadding=0 width=\"100%\">"
  . "		<tr>"
  . "		<td class=\"row\">"
  . "		<table cellspacing=0 cellpadding=0>"
  . "		<tr><td style=\"padding-right: 5px;\">".checkmark()." <font class=\"color\">Listen Server:</font></td><td>".($gameserver ->rules["listenserver"] == "True" ? "Yes" : "No")."</td></tr>"
  . "		<tr><td style=\"padding-right: 5px;\">".checkmark()." <font class=\"color\">Timelimit:</font></td><td>".$gameserver ->rules["timelimit"]."</td></tr>"
  . "		<tr><td style=\"padding-right: 5px;\">".checkmark()." <font class=\"color\">Change Levels:</font></td><td>".($gameserver ->rules["changelevels"] == "True" ? "Yes" : "No")."</td></tr>"
  . "		<tr><td style=\"padding-right: 5px;\">".checkmark()." <font class=\"color\">Maximum Teams:</font></td><td>".$gameserver ->rules["maxteams"]."</td></tr>"
  
  . "		</table>"
  . "		</td>"
  . "		<td class=\"row\">"
  . "		<table cellspacing=0 cellpadding=0>"
. "		<tr><td style=\"padding-right: 5px;\">".checkmark()." <font class=\"color\">Game Style:</font></td><td>".$gameserver ->rules["gamestyle"]."</td></tr>"
  . "		<tr><td style=\"padding-right: 5px;\">".checkmark()." <font class=\"color\">Tournament:</font></td><td>".($gameserver ->rules["tournament"] == "True" ? "Yes" : "No")."</td></tr>"
. "		<tr><td style=\"padding-right: 5px;\">".checkmark()." <font class=\"color\">Team Balance:</font></td><td>".($gameserver ->rules["balanceteams"] == "True" ? "Yes" : "No")."</td></tr>"
  . "		<tr><td style=\"padding-right: 5px;\">".checkmark()." <font class=\"color\">Players Balance Teams:</font></td><td>".($gameserver->rules["playersbalanceteams"] == "True" ? "Yes" : "No")."</td></tr>"
  . "		<tr><td style=\"padding-right: 5px;\">".checkmark()." <font class=\"color\">Friendly Fire:</font></td><td>".$gameserver ->rules["friendlyfire"]."</td></tr>"

    
  . "		</table>"
  . "		</td>"
  . "		</tr>"
  . "		</table>";
	break;
	case "ut":
$retval="<table cellspacing=0 cellpadding=0 width=\"100%\">"
  . "		<tr>"
  . "		<td class=\"row\">"
  . "		<table cellspacing=0 cellpadding=0>"
  . "		<tr><td style=\"padding-right: 5px;\">".checkmark()." <font class=\"color\">Listen Server:</font></td><td>".($gameserver ->rules["listenserver"] == "True" ? "Yes" : "No")."</td></tr>"
  . "		<tr><td style=\"padding-right: 5px;\">".checkmark()." <font class=\"color\">Timelimit:</font></td><td>".$gameserver ->rules["timelimit"]."</td></tr>"
  . "		<tr><td style=\"padding-right: 5px;\">".checkmark()." <font class=\"color\">Goal Team Score:</font></td><td>".$gameserver ->rules["goalteamscore"]."</td></tr>"
  . "		<tr><td style=\"padding-right: 5px;\">".checkmark()." <font class=\"color\">Change Levels:</font></td><td>".($gameserver ->rules["changelevels"] == "True" ? "Yes" : "No")."</td></tr>"
  . "		<tr><td style=\"padding-right: 5px;\">".checkmark()." <font class=\"color\">Maximum Teams:</font></td><td>".$gameserver ->rules["maxteams"]."</td></tr>"
  
  . "		</table>"
  . "		</td>"
  . "		<td class=\"row\">"
  . "		<table cellspacing=0 cellpadding=0>"
. "		<tr><td style=\"padding-right: 5px;\">".checkmark()." <font class=\"color\">Game Style:</font></td><td>".$gameserver ->rules["gamestyle"]."</td></tr>"
  . "		<tr><td style=\"padding-right: 5px;\">".checkmark()." <font class=\"color\">Tournament:</font></td><td>".($gameserver ->rules["tournament"] == "True" ? "Yes" : "No")."</td></tr>"
. "		<tr><td style=\"padding-right: 5px;\">".checkmark()." <font class=\"color\">Team Balance:</font></td><td>".($gameserver ->rules["balanceteams"] == "True" ? "Yes" : "No")."</td></tr>"
  . "		<tr><td style=\"padding-right: 5px;\">".checkmark()." <font class=\"color\">Players Balance Teams:</font></td><td>".($gameserver->rules["playersbalanceteams"] == "True" ? "Yes" : "No")."</td></tr>"
  . "		<tr><td style=\"padding-right: 5px;\">".checkmark()." <font class=\"color\">Friendly Fire:</font></td><td>".$gameserver ->rules["friendlyfire"]."</td></tr>"

    
  . "		</table>"
. "		<tr><td style=\"padding-right: 5px;\">".checkmark()." <font class=\"color\">Mutators:</font></td><td>".$gameserver ->rules["mutators"]."</td></tr>"
  . "		</td>"
  . "		</tr>"
  . "		</table>";
	break;
	case "bfield1942":
	$retval="<table cellspacing=0 cellpadding=0 width=\"100%\">"
  . "		<tr>"
  . "		<td class=\"row\">"
  . "		<table cellspacing=0 cellpadding=0>"
  
  . "		<tr><td style=\"padding-right: 5px;\">".checkmark()." <font class=\"color\">Soldier FF:</font></td><td>".$gameserver ->rules["soldier_friendly_fire"]."</td></tr>"
  . "		<tr><td style=\"padding-right: 5px;\">".checkmark()." <font class=\"color\">Vehicle FF:</font></td><td>".$gameserver ->rules["vehicle_friendly_fire"]."</td></tr>"
  . "		<tr><td style=\"padding-right: 5px;\">".checkmark()." <font class=\"color\">Num. of Rounds:</font></td><td>".$gameserver ->rules["number_of_rounds"]."</td></tr>"
  . "		<tr><td style=\"padding-right: 5px;\">".checkmark()." <font class=\"color\">Game Start Delay:</font></td><td>".$gameserver ->rules["game_start_delay"]."</td></tr>";
if ($gameserver->rules["timelimit"])
 $retval.="		<tr><td style=\"padding-right: 5px;\">".checkmark()." <font class=\"color\">Timelimit:</font></td><td>".$gameserver ->rules["timelimit"]."</td></tr>";
if ($gameserver->rules["time_limit"])
$retval.="		<tr><td style=\"padding-right: 5px;\">".checkmark()." <font class=\"color\">Timelimit:</font></td><td>".$gameserver ->rules["time_limit"]."</td></tr>";

$retval.="		<tr><td style=\"padding-right: 5px;\">".checkmark()." <font class=\"color\">Dedicated:</font></td><td>".$gameserver ->rules["dedicated"]."</td></tr>"
  
  . "		</table>"
  . "		</td>"
  . "		<td class=\"row\">"
  . "		<table cellspacing=0 cellpadding=0>"
. "		<tr><td style=\"padding-right: 5px;\">".checkmark()." <font class=\"color\">NoseCam Allowed:</font></td><td>".$gameserver ->rules["allow_nose_cam"]."</td></tr>"
  . "		<tr><td style=\"padding-right: 5px;\">".checkmark()." <font class=\"color\">Free Camera:</font></td><td>".$gameserver ->rules["free_camera"]."</td></tr>"
  . "		<tr><td style=\"padding-right: 5px;\">".checkmark()." <font class=\"color\">Auto Balance:</font></td><td>".$gameserver ->rules["auto_balance_teams"]."</td></tr>"
. "		<tr><td style=\"padding-right: 5px;\">".checkmark()." <font class=\"color\">Game Mode:</font></td><td>".$gameserver ->rules["gamemode"]."</td></tr>"
. "		<tr><td style=\"padding-right: 5px;\">".checkmark()." <font class=\"color\">External View:</font></td><td>".$gameserver ->rules["external_view"]."</td></tr>" 
. "		<tr><td style=\"padding-right: 5px;\">".checkmark()." <font class=\"color\">Ticket Ratio:</font></td><td>".$gameserver ->rules["ticket_ratio"]."</td></tr>"
  
  . "		</table>"
  . "		</td>"
  . "		</tr>"
  . "		</table>";
	break;
	case "chaser":
        $retval="";
	break;
        default:
	$retval="";
   	break;
   	}
return $retval;
}

}

?>
