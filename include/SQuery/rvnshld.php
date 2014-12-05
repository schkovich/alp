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
/* still not known:
'W1' => 'snames',
'X1' => 'iserver',
'E2' => 'lid',
'F2' => 'gid',
'I2' => 'aiback',
'J1' => Gametypes per map list K1
'L3' => ??
*/


include_once($libpath."gsQuery.php");


/**
 * @brief Implements the properitary protocol used by Raven Shield
 * @author Jeremias Reith (jr@terragate.net)
 * @version $Id: rvnshld.php,v 1.3 2006/07/19 21:33:15 synth_spring Exp $
 * @todo Some variables are missing
 *
 * As far as I know this works with 'Rainbox Six: Raven Shield' only.
 */
class rvnshld extends gsQuery
{
  
  function query_server($getPlayers=TRUE,$getRules=TRUE)
    {
    $this->playerkeys=array();
    $this->debug=array();
    $this->password=-1;
    $unknown_variables = 0;
    
    $command="REPORT";
    if(!($result=$this->_sendCommand($this->address,$this->queryport,$command))) {
      return FALSE;
    }
  //$fp=fopen("out.bin","w");
  //fwrite($fp,$result);
  //fclose($fp);
    $this->online=TRUE;
    $this->gamename="rvnshld";
    
    $temp=explode("\x20\xb6",$result);
    
    foreach($temp as $curvalue) {
      switch(substr($curvalue, 0, 2)) {
      case "P1":
	$this->hostport=substr($curvalue, 3);
	break;
      case "I1":
	$this->servertitle=substr($curvalue, 3);
	break;
      case "A1":
	$this->maxplayers=substr($curvalue, 3);
	break;
      case "B1":
	$this->numplayers=substr($curvalue, 3);
	break;
      case "D2":
	$this->gameversion=substr($curvalue, 3);
	break;
      case "L2":
        $this->gamename=substr($curvalue, 3);
	break;	
      case "E1":
	$this->mapname=substr($curvalue, 3);
	break;
      case "J2":
        $this->rules["locked"]=substr($curvalue, 3);
	break;	
      case "H1":
        $this->rules["dedicated"]=substr($curvalue, 3);
	break;	
      case "G1":
	$this->password=substr($curvalue, 3);
	break;
      case "F1":
	$this->gametype=substr($curvalue, 3);
	break;
      case "Y1":
        $this->rules["friendly_fire"]=substr($curvalue, 3);
	break;	
      case "Z1":
        $this->rules["team_balance"]=substr($curvalue, 3);
	break;	
      case "A2":
        $this->rules["tk_penalty"]=substr($curvalue, 3);
	break;	
      case "B2":
        $this->rules["radar_enabled"]=substr($curvalue, 3);
	break;	
      case "H2":
        $this->rules["num_terrorist"]=substr($curvalue, 3);
	break;	
      case "K2":
        $this->rules["force_fpw"]=substr($curvalue, 3);
	break;	
      case "R1":
	$this->rules["round_time"]=nduration(substr($curvalue, 3));
	break;
      case "Q1":
	$this->rules["round_number"]=substr($curvalue, 3);
	break;
      case "T1":
	$this->rules["bomb_timer"]=substr($curvalue, 3);
	break;
      case "S1":
	$this->rules["intermission_time"]=substr($curvalue, 3);
	break;
      case "G2":
	$this->rules["query_port"]=substr($curvalue, 3);
	break;
      case "K1":
	$this->maplist=explode("/", substr($curvalue, 4));
	break;
      case "L1":	
	$this->playerkeys["name"]=TRUE;
	$playernames=explode("/", $curvalue);
	for($i=1;$i<count($playernames);$i++) {
	  $this->players[$i-1]["name"]=$playernames[$i];
        }
	
	break;
      case "O1":
	$this->playerkeys["score"]=TRUE;
	$playerscores=explode("/", substr($curvalue, 3));
	for($i=1;$i<count($playerscores);$i++) {
	  $this->players[$i-1]["score"]=$playerscores[$i];
	}
	break;
      case "N1":
	$this->playerkeys["ping"]=TRUE;
	$playerpings=explode("/", substr($curvalue, 3));
	for($i=1;$i<count($playerpings);$i++) {
	  $this->players[$i-1]["ping"]=$playerpings[$i];
	}   
	break;
      case "M1":
	$this->playerkeys["time"]=TRUE;
	$playertimes=explode("/", substr($curvalue, 3));
	for($i=1;$i<count($playertimes);$i++) {
	  $this->players[$i-1]["time"]=$playertimes[$i];
	}
	break;
      default:
	// Don't know this variable
	
      }
      if(!empty($this->maplist)) {
	$i=0;
	while($this->mapname != $this->maplist[$i++] && $i<count($this->maplist));
	$this->nextmap=$this->maplist[$i % count($this->maplist)];
      }
    }
    return TRUE;
  }

/* this is for game specific cvar displays  */
function docvars($gameserver)
{
$retval="<table cellspacing=0 cellpadding=0 width=\"100%\">"
  . "		<tr>"
  . "		<td class=\"row\">"
  . "		<table cellspacing=0 cellpadding=0>"
  . "		<tr><td style=\"padding-right: 5px;\">".checkmark()." <font class=\"color\">Locked:</font></td><td>".($gameserver ->rules["locked"] == 1 ? "Yes" : "No")."</td></tr>"
  . "		<tr><td style=\"padding-right: 5px;\">".checkmark()." <font class=\"color\">Dedicated:</font></td><td>".($gameserver ->rules["dedicated"] == 1 ? "Yes" : "No")."</td></tr>"
  . "		<tr><td style=\"padding-right: 5px;\">".checkmark()." <font class=\"color\">Radar Enabled:</font></td><td>".($gameserver ->rules["radar_enabled"] == 1 ? "Yes" : "No")."</td></tr>"
  . "		<tr><td style=\"padding-right: 5px;\">".checkmark()." <font class=\"color\">Friendly Fire:</font></td><td>".($gameserver ->rules["friendly_fire"] == 1 ? "Yes" : "No")."</td></tr>"
  . "		<tr><td style=\"padding-right: 5px;\">".checkmark()." <font class=\"color\">Team Balance:</font></td><td>".($gameserver ->rules["team_balance"] == 1 ? "Yes" : "No")."</td></tr>"
 . "		<tr><td style=\"padding-right: 5px;\">".checkmark()." <font class=\"color\">TK Penalty:</font></td><td>".($gameserver ->rules["tk_penalty"] == 1 ? "Yes" : "No")."</td></tr>"
  
  . "		</table>"
  . "		</td>"
  . "		<td class=\"row\">"
  . "		<table cellspacing=0 cellpadding=0>"
. "		<tr><td style=\"padding-right: 5px;\">".checkmark()." <font class=\"color\"># of Terrorists:</font></td><td>".$gameserver ->rules["num_terrorist"]."</td></tr>"
. "		<tr><td style=\"padding-right: 5px;\">".checkmark()." <font class=\"color\"># of Rounds:</font></td><td>".$gameserver ->rules["round_number"]."</td></tr>"
  . "		<tr><td style=\"padding-right: 5px;\">".checkmark()." <font class=\"color\">Round Time:</font></td><td>".$gameserver ->rules["round_time"]."</td></tr>"
  . "		<tr><td style=\"padding-right: 5px;\">".checkmark()." <font class=\"color\">Bomb Timer:</font></td><td>".$gameserver ->rules["bomb_timer"]."</td></tr>"
  . "		<tr><td style=\"padding-right: 5px;\">".checkmark()." <font class=\"color\">Intermission Time:</font></td><td>".$gameserver ->rules["intermission_time"]."</td></tr>"
  . "		<tr><td style=\"padding-right: 5px;\">".checkmark()." <font class=\"color\">Force FPW:</font></td><td>".($gameserver ->rules["force_fpw"] == 1 ? "Yes" : "No")."</td></tr>"
  
  . "		</table>"
  . "		</td>"
  . "		</tr>"
  . "		</table>";
return $retval;
}

}
?>
