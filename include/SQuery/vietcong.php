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
 * @version $Id: vietcong.php,v 1.3 2006/07/19 21:33:16 synth_spring Exp $
 * @todo process rules
 *
 * Vietcong's default query port seems to be 15426.
 * Vietcong does not provide a ganename. 
 * This class takes note of the changed vietcong query commands.
 * Rules are currently not processed.
 */
class vietcong extends gameSpy
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
     echo $response;
    $this->_processServerInfo($response);
    $this->_processRules($response);

    $this->online=TRUE;

    // get players
    //if($this->numplayers && $getPlayers) {
      $cmd="\\players\\";
      if(!($response=$this->_sendCommand($this->address, $this->queryport, $cmd))) {
	return FALSE;
      }    
	echo "PLAYERS";
       echo $response;
      $this->_processPlayers($response);
   // }

    $this->gamename="vietcong";
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
  
  . "		<tr><td style=\"padding-right: 5px;\">".checkmark()." <font class=\"color\">Dedicated:</font></td><td>".($gameserver ->rules["dedic"] == 1 ? "Yes" : "No")."</td></tr>"
  . "		<tr><td style=\"padding-right: 5px;\">".checkmark()." <font class=\"color\">Vietnam:</font></td><td>".($gameserver ->rules["vietnam"] == 1 ? "Yes" : "No")."</td></tr>"

  . "		</table>"
  . "		</td>"
  . "		<td class=\"row\">"
  . "		<table cellspacing=0 cellpadding=0>"
. "		<tr><td style=\"padding-right: 5px;\">".checkmark()." <font class=\"color\">Game Mode:</font></td><td>".$gameserver->rules["gamemode"]."</td></tr>"
  
  . "		</table>"
  . "		</td>"
  . "		</tr>"
  . "		</table>";
return $retval;
}

}

?>
