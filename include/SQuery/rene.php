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
 * @version $Id: rene.php,v 1.3 2006/07/19 21:33:15 synth_spring Exp $
 * @bug some games does not escape the backslash, so we have a problem when somebody has a backlsash in its name
 *
  */
class rene extends gsQuery
{

  
  function query_server($getPlayers=TRUE,$getRules=TRUE)
  {       
    $this->playerkeys=array();
    $this->debug=array();
    $this->errstr="";
    $this->password=-1;
    
    $cmd="\\status\\";
    if(!($response=$this->_sendCommand($this->address, $this->queryport, $cmd))) {
      $this->errstr="No reply received";
      return FALSE;
    }  
   
    $this->_processServerInfo($response);
    $this->_processRules($response);
	

    $this->online=TRUE;

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
    $this->hostport = $data["hostport"];
    $this->gameversion = $data["gamever"];


    $this->servertitle = $data["hostname"];
    $this->maptitle = isset($data["maptitle"]) ? $data["maptitle"] : "";
    $this->mapname = $data["mapname"];
    $this->gametype = $data["gametype"];
    $this->numplayers = $data["numplayers"];
    $this->maxplayers = $data["maxplayers"];
   
  
    if(isset($data["password"]) && ($data["password"]==0 || $data["password"]==1)) {  
      $this->password=$data["password"];
    }
    
    if(!$this->gamename) {
      $this->gamename="unknown";
    }

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
    return "rene";
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
	case "ccrenegade":
$retval="<table cellspacing=0 cellpadding=0 width=\"100%\">"
  . "		<tr>"
  . "		<td class=\"row\">"
  . "		<table cellspacing=0 cellpadding=0>"
  . "		<tr><td style=\"padding-right: 5px;\">".checkmark()." <font class=\"color\">Dedicated:</font></td><td>".($gameserver ->rules["ded"] == 1 ? "Yes" : "No")."</td></tr>"
  . "		<tr><td style=\"padding-right: 5px;\">".checkmark()." <font class=\"color\">FriendlyFire:</font></td><td>".($gameserver ->rules["ff"] == 1 ? "Yes" : "No")."</td></tr>"
  . "		<tr><td style=\"padding-right: 5px;\">".checkmark()." <font class=\"color\">Start Credits:</font></td><td>".$gameserver ->rules["sc"]."</td></tr>"
  
  
  . "		</table>"
  . "		</td>"
  . "		<td class=\"row\">"
  . "		<table cellspacing=0 cellpadding=0>"
  . "		<tr><td style=\"padding-right: 5px;\">".checkmark()." <font class=\"color\">Driver is Gunner:</font></td><td>".($gameserver ->rules["dg"]== 1 ? "Yes" : "No")."</td></tr>"
  . "		<tr><td style=\"padding-right: 5px;\">".checkmark()." <font class=\"color\">Team Choose:</font></td><td>".($gameserver ->rules["tc"]== 1 ? "Yes" : "No")."</td></tr>"
  . "		<tr><td style=\"padding-right: 5px;\">".checkmark()." <font class=\"color\">Clan Server:</font></td><td>".($gameserver ->rules["csvr"]== 1 ? "Yes" : "No")."</td></tr>"
  . "		</table>"
  . "		</td>"
  . "		</tr>"
  . "		</table>";
	break;
	default:
	$retval="";
   	break;
   	}
return $retval;
}

}

?>
