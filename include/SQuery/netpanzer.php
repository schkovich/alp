<?php
// Improved ALP security exploit fix - added on July 19, 2006
defined('SQUERY_INVOKED') or die('No access.');

include_once($libpath."gameSpy.php");

/**
 * @brief Extends the gameSpy protocol to support IGI2
 * @author Curtis Brown (volfin1@earthlink.net)
 */
class netpanzer extends gameSpy
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
   //$rules=substr($result,0,$pos);
 
    $this->_processServerInfo($result);
    //$this->_processRules($rules);

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
    for($i=0;$i<$count;$i++) {
      $this->rules[$temp[$i]]=$temp[++$i];
    }
  
if ($this->rules["gamename"] <> "") {$this->gamename = $this->rules["gamename"];}
    $this->hostport = $this->queryport;
    $this->gameversion = "0.8";
    $this->servertitle = $this->rules["hostname"];
    $this->maptitle =$this->rules["maptitle"];
    $this->mapname = $this->rules["mapname"];
    $this->gametype = $this->rules["gamestyle"];
    $this->numplayers = $this->rules["numplayers"];
    $this->maxplayers = $this->rules["maxplayers"];

 if(isset($this->rules["password"]) && ($this->rules["password"]==0 || $this->rules["password"]==1)) {
   $this->password=$this->rules["password"];
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
    return "netpanzer";
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
   case "netpanzer":
$retval="<table cellspacing=0 cellpadding=0 width=\"100%\">"
  . "		<tr>"
  . "		<td class=\"row\">"
  . "		<table cellspacing=0 cellpadding=0>"
  . "		<tr><td style=\"padding-right: 5px;\">".checkmark()." <font class=\"color\">Full:</font></td><td>".($gameserver->rules["full"]== 1 ? "Yes" : "No")."</td></tr>"
  . "		<tr><td style=\"padding-right: 5px;\">".checkmark()." <font class=\"color\">Units:</font></td><td>".$gameserver->rules["units_per_player"] ."</td></tr>"
  . "		<tr><td style=\"padding-right: 5px;\">".checkmark()." <font class=\"color\">Time:</font></td><td>".$gameserver->rules["time"]."</td></tr>"

  . "		</table>"
  . "		</td>"
  . "		<td class=\"row\">"
  . "		<table cellspacing=0 cellpadding=0>"
. "		<tr><td style=\"padding-right: 5px;\">".checkmark()." <font class=\"color\">Timelimit:</font></td><td>".$gameserver->rules["timelimit"]."</td></tr>"
  . "		<tr><td style=\"padding-right: 5px;\">".checkmark()." <font class=\"color\">Fraglimit:</font></td><td>".$gameserver->rules["fraglimit"]."</td></tr>"
  . "		<tr><td style=\"padding-right: 5px;\">".checkmark()." <font class=\"color\">Objec. Limit:</font></td><td>".$gameserver->rules["objectivelimit"]."</td></tr>"
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
