<?php
// Improved ALP security exploit fix - added on July 19, 2006
defined('SQUERY_INVOKED') or die('No access.');

include_once($libpath."gsQuery.php");


/**
 * @brief Uses the Quake 2 protcol to communicate with the server
 
 * This class can communicate with most games based on the Quake 2
 * engine.
 */
class qworld extends gsQuery
{

  function query_server($getPlayers=TRUE,$getRules=TRUE)
  { 
    $this->playerkeys=array();
    $this->debug=array();
    $this->password=-1;
      
    $command="\xFF\xFF\xFF\xFFstatus\x0a\x00";
    if(!($result=$this->_sendCommand($this->address,$this->queryport,$command))) {
      $this->errstr="No reply received";
      return FALSE;
    }

    $temp=explode("\x0a",$result);
    $rawdata=explode("\\",substr($temp[0],1,strlen($temp[0])));
 
    // get rules and basic infos
    for($i=1;$i< count($rawdata);$i++) {
    
      switch ($rawdata[$i++]) {
      case "*gamedir":     
	$this->gametype=$rawdata[$i];
	break;
      case "*version":
	$this->gameversion=$rawdata[$i];
	break;
      case "hostname":
	$this->servertitle=$rawdata[$i];
	break;
      case "map":
	$this->mapname=$rawdata[$i];
	break;
      case "capturelimit":	
      case "scorelimit":  
	$this->scorelimit=$rawdata[$i];
	break;
      case "needpass":
	$this->password=$rawdata[$i];
	break;
      case "maxclients":
	$this->rules["sv_maxclients"]=$rawdata[$i];
	break; 
      default:
	$rawdata[$i-1]=strtolower($rawdata[$i-1]);
	$this->rules[$rawdata[$i-1]] = $rawdata[$i];
      }
    }

    $this->gamename="qworld"; 
    
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


  

  
  function htmlize($var) 
  {
    
    return htmlentities($var);
  }

function _processPlayers($allplayers)
{

for($i=1;$i< count($allplayers)-1;$i++) {
      if(preg_match("/^(-?\d+) (-?\d+) (-?\d+) (-?\d+) \"(.*)\" \"(.*)\" (-?\d+) (-?\d+)$/", $allplayers[$i], $curplayer)) {
	  $players[$i-1]["name"]=$curplayer[5];
	  $players[$i-1]["score"]=$curplayer[2];
	  $players[$i-1]["ping"]=$curplayer[4];	
	  $players[$i-1]["time"]=$curplayer[3]." min";
	  $players[$i-1]["skin"]=$curplayer[6];
	 
	}
 	}	      
      $this->playerkeys["name"]=TRUE;
      $this->playerkeys["score"]=TRUE;
      $this->playerkeys["ping"]=TRUE;
      $this->playerkeys["skin"]=TRUE;
      $this->playerkeys["time"]=TRUE;		
      $this->players=$players;
   }

/* this is for game specific cvar displays  */
function docvars($gameserver)
{
$retval="<table cellspacing=0 cellpadding=0 width=\"100%\">"
  . "		<tr>"
  . "		<td class=\"row\">"
  . "		<table cellspacing=0 cellpadding=0>"
  . "		<tr><td style=\"padding-right: 5px;\">".checkmark()." <font class=\"color\">Max Spectators:</font></td><td>".$gameserver->rules["maxspectators"]."</td></tr>"
  . "		<tr><td style=\"padding-right: 5px;\">".checkmark()." <font class=\"color\">Deathmatch:</font></td><td>".$gameserver ->rules["deathmatch"]."</td></tr>"
  
  . "		</table>"
  . "		</td>"
  . "		<td class=\"row\">"
  . "		<table cellspacing=0 cellpadding=0>"
. "		<tr><td style=\"padding-right: 5px;\">".checkmark()." <font class=\"color\">Timelimit:</font></td><td>".$gameserver ->rules["timelimit"]."</td></tr>"
. "		<tr><td style=\"padding-right: 5px;\">".checkmark()." <font class=\"color\">Fraglimit:</font></td><td>".$gameserver ->rules["fraglimit"]."</td></tr>"
  
  . "		</table>"
  . "		</td>"
  . "		</tr>"
  . "		</table>";
return $retval;
}

}
?>