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
 * @brief Extends the gameSpy protocol to support Nascar SimRacer
 * @author Curtis Brown (volfin1@earthlink.net)
 */
class simracer extends gameSpy
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
  
  // $fp=fopen("out.bin","w");
 // fwrite($fp,$result);
  //fclose($fp);
    $this->online = TRUE;


  $this->_processServerInfo($result);
  $this->_processRules($result);

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

        if (preg_match("/mapname\\\GAMEDATA\\\TRACKS\\\(.*)\\\\numplayers/",$rawdata,$result))
	{
	$temp=explode("\\",$result[1]);
	$this->mapname=$temp[2];
	}

    $temp=explode("\\",$rawdata);
    $count=count($temp);
    for($i=1;$i<$count;$i++) {
      $data[strtolower($temp[$i])]=$temp[++$i];
    }

    $this->gamename = $data["gamename"];
    if(eregi("nsr", $this->gamename)) $this->gamename="simracer";
    
    $this->hostport = $data["hostport"];
    $this->gameversion = $data["gamever"];
    $this->servertitle = $data["hostname"];
    
    $this->gametype = $data["seriesname"];
    $this->numplayers = $data["numplayers"];
    $this->maxplayers = $data["maxplayers"];

 if(isset($data["password"]) && ($data["password"]==0 || $data["password"]==1)) {  
      $this->password=$data["password"];
    }


    return TRUE;
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
    return "simracer";
  }

/* this is for game specific cvar displays  */
function docvars($gameserver)
{
$retval="<table cellspacing=0 cellpadding=0 width=\"100%\">"
  . "		<tr>"
  . "		<td class=\"row\">"
  . "		<table cellspacing=0 cellpadding=0>"
  . "		<tr><td style=\"padding-right: 5px;\">".checkmark()." <font class=\"color\">Voice Chat:</font></td><td>".($gameserver ->rules["voicechat"] == 1 ? "Yes" : "No")."</td></tr>"
  . "		<tr><td style=\"padding-right: 5px;\">".checkmark()." <font class=\"color\">Difficulty:</font></td><td>".$gameserver ->rules["difficulty"]."</td></tr>"
  . "		<tr><td style=\"padding-right: 5px;\">".checkmark()." <font class=\"color\">Invulnerable:</font></td><td>".($gameserver ->rules["invulnerability"] == 1 ? "Yes" : "No")."</td></tr>"
  . "		<tr><td style=\"padding-right: 5px;\">".checkmark()." <font class=\"color\">AutoClutch:</font></td><td>".($gameserver ->rules["autoclutch"] == 1 ? "Yes" : "No")."</td></tr>"
  . "		<tr><td style=\"padding-right: 5px;\">".checkmark()." <font class=\"color\">AutoShift:</font></td><td>".($gameserver ->rules["autoshifting"] == 1 ? "Yes" : "No")."</td></tr>"
  . "		<tr><td style=\"padding-right: 5px;\">".checkmark()." <font class=\"color\">AutoPit:</font></td><td>".($gameserver ->rules["autopit"] == 1 ? "Yes" : "No")."</td></tr>"
  . "		<tr><td style=\"padding-right: 5px;\">".checkmark()." <font class=\"color\">AntiLockBrakes:</font></td><td>".($gameserver ->rules["antilockbrakes"] == 1 ? "Yes" : "No")."</td></tr>"
  
  . "		</table>"
  . "		</td>"
  . "		<td class=\"row\">"
  . "		<table cellspacing=0 cellpadding=0>"
. "		<tr><td style=\"padding-right: 5px;\">".checkmark()." <font class=\"color\">SteeringHelp:</font></td><td>".($gameserver ->rules["steeringhelp"] == 1 ? "Yes" : "No")."</td></tr>"
  . "		<tr><td style=\"padding-right: 5px;\">".checkmark()." <font class=\"color\">BrakingHelp:</font></td><td>".($gameserver ->rules["brakinghelp"] == 1 ? "Yes" : "No")."</td></tr>"
  . "		<tr><td style=\"padding-right: 5px;\">".checkmark()." <font class=\"color\">StabilityCtrl:</font></td><td>".($gameserver ->rules["stabilitycontrol"] == 1 ? "Yes" : "No")."</td></tr>"
  . "		<tr><td style=\"padding-right: 5px;\">".checkmark()." <font class=\"color\">SpinRecovery:</font></td><td>".($gameserver ->rules["spinrecovery"]==1 ? "Yes" : "No")."</td></tr>"
  . "		<tr><td style=\"padding-right: 5px;\">".checkmark()." <font class=\"color\">Tire Wear:</font></td><td>".($gameserver ->rules["tirewear"] == 1 ? "Yes" : "No")."</td></tr>"
  . "		<tr><td style=\"padding-right: 5px;\">".checkmark()." <font class=\"color\">Fuel Usage:</font></td><td>".($gameserver ->rules["fuelusage"] == 1 ? "Yes" : "No")."</td></tr>"
  . "		<tr><td style=\"padding-right: 5px;\">".checkmark()." <font class=\"color\">Mech. Failure:</font></td><td>".($gameserver ->rules["mechanicalfailure"] == 1 ? "Yes" : "No")."</td></tr>"
  . "		</table>"
  . "		</td>"
  . "		</tr>"
  . "		</table>";

return $retval;
}

}
?>
