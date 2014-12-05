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
 * @brief Extends the gameSpy protocol to support America's Army
 * @author Jeremias Reith (jr@terragate.net)
 * @version $Id: armygame.php,v 1.3 2006/07/19 21:33:15 synth_spring Exp $
 *
 * This is a quick hack to support the changed America's Army protocol.
 * It is slow, incomplete and ugly. Does anyone have the protocol specs?
 * @todo Add rules & clean up
 */
class armyGame extends gameSpy
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
  

    $this->online = TRUE;

    // xxx: not a nice way 
   if (ereg("^(.*)(\\\\leader_0.*)$", $result, $matches))
   {	
    

    $this->_processServerInfo($matches[1]);
    $this->_processRules($matches[1]); 

    $this->_processPlayers($matches[2]);  

    }
  else {
  // here if no players on server
  $this->_processServerInfo($result);
  $this->_processRules($result);
 }
  
    return TRUE;
  }  

  function sortPlayers($players, $sortkey="name") 
  {
    if(!sizeof($players)) {
      return array();
    }
    switch($sortkey) {
    case "roe":
      uasort($players, array("armyGame", "_sortbyRoe"));
      break;
    case "kia":
      uasort($players, array("armyGame", "_sortbyKia"));
      break;
    case "enemy":
      uasort($players, array("armyGame", "_sortbyEnemy"));
      break;
    default:
      $players=parent::sortPlayers($players, $sortkey);
    }
    return ($players);
  }


  // private methods

  function _sortbyRoe($a, $b) 
  {
    if($a["roe"]==$b["roe"]) { return 0; } 
    elseif($a["roe"]<$b["roe"]) { return 1; }
    else { return -1; }
  }

  function _sortbyKia($a, $b) 
  {
    if($a["kia"]==$b["kia"]) { return 0; } 
    elseif($a["kia"]<$b["kia"]) { return 1; }
    else { return -1; }
  }

  function _sortbyEnemy($a, $b) 
  {
    if($a["enemy"]==$b["enemy"]) { return 0; } 
    elseif($a["enemy"]<$b["enemy"]) { return 1; }
    else { return -1; }
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
	$this->playerkeys["leader"]=TRUE;
	$this->playerkeys["goal"]=TRUE;
	$this->playerkeys["score"]=TRUE;
	$this->playerkeys["ping"]=TRUE;
	$this->playerkeys["roe"]=TRUE;
	$this->playerkeys["kia"]=TRUE;
	$this->playerkeys["enemy"]=TRUE;

    $count=count($temp);
    for($i=1;$i<$count;$i++) {
      list($var, $playerid)=explode("_", $temp[$i]);
      switch($var) {
      case "player":
      case "playername":
	$players[$playerid]["name"]=$temp[++$i];	    
	break;
      case "honor":
	$players[$playerid]["score"]=$temp[++$i];	    
	break;
      case "score":
        if ($playerid=="t0") $this->teamscore1=$temp[++$i];
	else $this->teamscore2=$temp[++$i];
 	break;	
      default:
	$players[$playerid][$var]=$temp[++$i];
	$this->playerkeys[$var]=TRUE;
      }
    }
    $this->players=$players;
  
    return TRUE;
  }


  function _getClassName() 
  {
    return "armyGame";
  }


/* this is for game specific cvar displays  */
function docvars($gameserver)
{
$retval="<table cellspacing=0 cellpadding=0 width=\"100%\">"
  . "		<tr>"
  . "		<td class=\"row\">"
  . "		<table cellspacing=0 cellpadding=0>"
  . "		<tr><td style=\"padding-right: 5px;\">".checkmark()." <font class=\"color\">Game Mode:</font></td><td>".$gameserver ->rules["gamemode"]."</td></tr>"
  . "		<tr><td style=\"padding-right: 5px;\">".checkmark()." <font class=\"color\">Min Honor:</font></td><td>".$gameserver ->rules["minhonor"]."</td></tr>"
  . "		<tr><td style=\"padding-right: 5px;\">".checkmark()." <font class=\"color\">Max Honor:</font></td><td>".$gameserver ->rules["maxhonor"]."</td></tr>"
  . "		<tr><td style=\"padding-right: 5px;\">".checkmark()." <font class=\"color\">Current Round:</font></td><td>".$gameserver ->rules["current_round"]."</td></tr>"
  . "		<tr><td style=\"padding-right: 5px;\">".checkmark()." <font class=\"color\">Mission Time:</font></td><td>".$gameserver ->rules["mission_time"]."</td></tr>"
. "		<tr><td style=\"padding-right: 5px;\">".checkmark()." <font class=\"color\">Tour:</font></td><td>".$gameserver ->rules["tour"]."</td></tr>"
  
  . "		</table>"
  . "		</td>"
  . "		<td class=\"row\">"
  . "		<table cellspacing=0 cellpadding=0>"
. "		<tr><td style=\"padding-right: 5px;\">".checkmark()." <font class=\"color\">Tournament:</font></td><td>".($gameserver ->rules["tournament"] == 1 ? "Yes" : "No")."</td></tr>"
  . "		<tr><td style=\"padding-right: 5px;\">".checkmark()." <font class=\"color\">Ultimate Arena:</font></td><td>".($gameserver ->rules["ultimate_arena"] == 1 ? "Yes" : "No")."</td></tr>"
  . "		<tr><td style=\"padding-right: 5px;\">".checkmark()." <font class=\"color\">Custom:</font></td><td>".($gameserver ->rules["custom"] == 1 ? "Yes" : "No")."</td></tr>"
  . "		<tr><td style=\"padding-right: 5px;\">".checkmark()." <font class=\"color\">Official:</font></td><td>".($gameserver ->rules["official"]==1 ? "Yes" : "No")."</td></tr>"
  . "		<tr><td style=\"padding-right: 5px;\">".checkmark()." <font class=\"color\">Leased:</font></td><td>".($gameserver ->rules["leased"] == 1 ? "Yes" : "No")."</td></tr>"
 . "		<tr><td style=\"padding-right: 5px;\">".checkmark()." <font class=\"color\">Team Score:</font></td><td>".$gameserver->teamscore1."/".$gameserver->teamscore2."</td></tr>"
  
  . "		</table>"
  . "		</td>"
  . "		</tr>"
  . "		</table>";
return $retval;
}

}

?>