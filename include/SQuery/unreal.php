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
 * @brief Extends the gameSpy protocol to support Vietcong
 * @author Jeremias Reith (jr@terragate.net)
 * @version $Id: unreal.php,v 1.3 2006/07/19 21:33:15 synth_spring Exp $
 * @todo process rules
 *
 * Vietcong's default query port seems to be 15426.
 * Vietcong does not provide a ganename. 
 * This class takes note of the changed vietcong query commands.
 * Rules are currently not processed.
 */
class unreal extends gameSpy
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
  
  while(ereg("queryid", $response)) {
      $response=preg_replace("#[\\\]queryid[\\\](\S{1,2})\.(\S{1})#","",$response);
     }
  
    $this->_processServerInfo($response);
    $this->_processRules($response);

    $this->online=TRUE;
   
      $this->_processPlayers($response);
   
    $this->gamename="unreal";
    return TRUE;
  }  

  function _getClassName() 
  {
    return get_class($this);
  }





/* this is for game specific cvar displays  */
function docvars($gameserver)
{
$retval="<table cellspacing=0 cellpadding=0 width=\"100%\">"
  . "		<tr>"
  . "		<td class=\"row\">"
  . "		<table cellspacing=0 cellpadding=0>"
  
  . "		<tr><td style=\"padding-right: 5px;\">".checkmark()." <font class=\"color\">Time Limit:</font></td><td>".$gameserver ->rules["timelimit"]."</td></tr>"
  . "		<tr><td style=\"padding-right: 5px;\">".checkmark()." <font class=\"color\">Frag Limit:</font></td><td>".$gameserver ->rules["fraglimit"]."</td></tr>"
  . "		<tr><td style=\"padding-right: 5px;\">".checkmark()." <font class=\"color\">Game Mode:</font></td><td>".$gameserver ->rules["gamemode"]."</td></tr>"

  . "		</table>"
  . "		</td>"
  . "		<td class=\"row\">"
  . "		<table cellspacing=0 cellpadding=0>"
  . "		<tr><td style=\"padding-right: 5px;\">".checkmark()." <font class=\"color\">Use Bots:</font></td><td>".$gameserver->rules["multiplayerbots"]."</td></tr>"
  . "		<tr><td style=\"padding-right: 5px;\">".checkmark()." <font class=\"color\">Change Levels:</font></td><td>".$gameserver->rules["changelevels"]."</td></tr>"
  
  . "		</table>"
  . "		</td>"
  . "		</tr>"
  . "		</table>";
return $retval;
}


 function _sendCommand($address, $port, $command, $timeout=500000)
  {
    if(!$socket=@fsockopen("udp://".$address, $port)) {
      $this->debug["While trying to open a socket"]="Couldn't reach server";
      $this->errstr="Cannot open a socket!";
      return FALSE;
    } else {
      socket_set_blocking($socket, true);
      // socket_set_timeout should be used here but this requires PHP >=4.3 
      socket_set_timeout($socket, 0, $timeout);
      
      // send command
      if(fwrite($socket, $command, strlen($command))==-1) {
	fclose($socket);
	$this->debug["While trying to write on a open socket"]="Unable to write on open socket!";
	$this->errstr="Unable to write on open socket!";
	return FALSE;
      }
      
      $result="";
      $x=0; 
      do {
	$x++;
      do {
	$result .= fread($socket,128);
        $socketstatus = socket_get_status($socket);
      } while ($socketstatus["unread_bytes"]);
       }while (!eregi("final\\\\",$result));
      fclose($socket);
      if(!isset($result)) {
	$this->debug["Command send " . $command]="No response from game server received";
	return FALSE;
      }
      $this->debug["Command send " . $command]="Answer received: " .$result;
     
      return $result;
	
    }
  }


}

?>
