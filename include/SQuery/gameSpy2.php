<?php
// Improved ALP security exploit fix - added on July 19, 2006
defined('SQUERY_INVOKED') or die('No access.');


class gameSpy2 extends gsQuery
{

  

  function query_server($getPlayers=TRUE,$getRules=TRUE)
  {       
    $this->playerkeys=array();
    $this->debug=array();
    $this->errstr="";
    $this->password=-1;
    
    $cmd="\xFE\xFD\x00SQUR\xCC\xCC\xCC\xCC\xFF\xFF\xFF\x01";
    if(!($response=$this->_sendCommand($this->address, $this->queryport, $cmd))) {
      $this->errstr="No reply received";
      return FALSE;
    }  

  
   $this->_processServerInfo($response);

    $this->online=TRUE;


    // get players
    if($this->numplayers && $getPlayers) {
      $cmd="\xFE\xFD\x00SQUR\xCC\xCC\xCC\xCC\xFF\xFF\xFF\x01";
      if(!($response=$this->_sendCommand($this->address, $this->queryport, $cmd))) {
	return FALSE;
      } 
    
     
    $this->_processPlayers($response);
    }

 
    // get Team Info
    
      $cmd="\xFE\xFD\x00SQUR\xCC\xCC\xCC\xCC\xFF\xFF\xFF\x01";
      if(!($response=$this->_sendCommand($this->address, $this->queryport, $cmd))) {
	return FALSE;
      } 

   
     $this->_processTeams($response);
       

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
    $rawdata=str_replace("SQUR","",$rawdata);
    $temp=explode("\x00",$rawdata);
    $count=count($temp);
    for($i=1;$i<$count;$i++) {
    switch($temp[$i]) {
      case "gamename":
      case "game_id":
	$this->gamename =strtolower($temp[++$i]);
	break;
      case "hostport":
	$this->hostport = $temp[++$i];
	break;
      case "gamever":
	$this->gameversion = $temp[++$i];
	break;
      case "hostname":
	$this->servertitle = $temp[++$i];
	break;
      case "mapname":
	$this->mapname = $temp[++$i];
	break;
      case "maptitle":
	$this->maptitle = $temp[++$i];
	break;
	case "punkbuster":
	case "bf2_anticheat":
	$this->rules["sv_punkbuster"]= $temp[++$i];
	break;
      case "gametype":
	$this->gametype = $temp[++$i];
	break;
      case "numplayers":
	$this->numplayers = $temp[++$i];
	break;
      case "maxplayers":
	$this->maxplayers = $temp[++$i];
	break;
      case "password":
	if($temp[++$i] == 0 || $temp[$i] == 1) {
	  $this->password = $temp[$i];
	}
	break;
      default:
	$this->rules[$temp[$i]] = $temp[++$i];
      }
    }

         
    if(!$this->gamename) {
     // see if this is Star wars battlefront  
     if ($this->rules["swbregion"]!='') $this->gamename="swbattlefront";
      else $this->gamename="unknown";
    }

    return TRUE;
  }





function _processTeams($rawdata) 
  {
    $rawdata=str_replace("SQUR","",$rawdata);
    $fc=$rawdata{2};
    $rawdata{2}="\x00";
    $temp=explode("\x00", $rawdata);


$x=3; $fc=0;// we start here
    while ($temp[$x]!='')
    {
     $fields[$x-3]=$temp[$x];
     if ($fields[$x-3]=='player') $fields[$x-3]='name';
     $x++;$fc++;
    }
	 $x++;

 
 $count=count($temp);
    $pi=0;
	
    for($i=$x;$i<$count-1;$i+=$fc) {
	for ($j=0;$j<$fc;$j++)
	{
	 $teamrule[$fields[$j].$pi]=$temp[$i+$j];
	} //end for $j
      $pi++;
   } // end for $i  

$this->team1=ucwords($teamrule["team_t1"]);
$this->team2=ucwords($teamrule["team_t0"]);
return TRUE;
  }
  
  function _processPlayers($rawPlayerData) 
  {
    $rawPlayerData=str_replace("SQUR","",$rawPlayerData);
    $rawPlayerData{2}="\x00";
    $temp=explode("\x00", $rawPlayerData);
    
    $x=3; $fc=0;// we start here
    while ($temp[$x]!='')
    {
     $fields[$x-3]=substr($temp[$x],0,strlen($temp[$x])-1);
     if ($fields[$x-3]=='player') $fields[$x-3]='name';
     $x++;$fc++;
    }
	 $x++;
     	 
	
    foreach($fields as $tag) $this->playerkeys[$tag]=TRUE;
    
    
    $count=count($temp);
    $pi=0;
	
    for($i=$x;$i<$count-1;$i+=$fc) {
	
	for ($j=0;$j<$fc;$j++)
	{
	 $players[$pi][$fields[$j]]=$temp[$i+$j];
	 if ($fields[$j]=="team"){
	  $this->playerteams[$pi]=$temp[$i+$j];
	  if ($temp[$i+$j]==1) $this->teamcnt1++;
           else $this->teamcnt2++;
	  }
	} //end for $j
      $pi++;
   } // end for $i  
	
     $this->players=$players;
	
      
    return TRUE;
  }

  
  
  /**
   * @internal @brief sorts the given gameSpy2 data
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
    return "gameSpy2";
  }

/* this is for game specific cvar displays  */
function docvars($gameserver)
{
 switch($this->gamename)
 {
  case "swbattlefront":
$retval="<table cellspacing=0 cellpadding=0 width=\"100%\">"
  . "		<tr>"
  . "		<td class=\"row\">"
  . "		<table cellspacing=0 cellpadding=0>"
  
  . "		<tr><td style=\"padding-right: 5px;\">".checkmark()." <font class=\"color\">Game Mode:</font></td><td>".$gameserver ->rules["gamemode"]."</td></tr>"
  . "		<tr><td style=\"padding-right: 5px;\">".checkmark()." <font class=\"color\">Frag Limit:</font></td><td>".$gameserver ->rules["fraglimit"]."</td></tr>"
  . "		</table>"
  . "		</td>"
  . "		<td class=\"row\">"
  . "		<table cellspacing=0 cellpadding=0>"
. "		<tr><td style=\"padding-right: 5px;\">".checkmark()." <font class=\"color\">Team Play:</font></td><td>".($gameserver ->rules["teamplay"]?"Yes":"No")."</td></tr>"
  . "		<tr><td style=\"padding-right: 5px;\">".checkmark()." <font class=\"color\">Time Limit:</font></td><td>".nduration($gameserver ->rules["timelimit"])."</td></tr>"
  . "		</table>"
  . "		</td>"
  . "		</tr>"
  . "		</table>";
    break;
    case "battlefield2":
$retval="<table cellspacing=0 cellpadding=0 width=\"100%\">"
  . "		<tr>"
  . "		<td class=\"row\">"
  . "		<table cellspacing=0 cellpadding=0>"

  . "		<tr><td style=\"padding-right: 5px;\">".checkmark()." <font class=\"color\">Dedicated:</font></td><td>".($gameserver ->rules["bf2_dedicated"]?"Yes":"No")."</td></tr>"
  . "		<tr><td style=\"padding-right: 5px;\">".checkmark()." <font class=\"color\">Ranked:</font></td><td>".($gameserver ->rules["bf2_ranked"]?"Yes":"No")."</td></tr>"
  . "		<tr><td style=\"padding-right: 5px;\">".checkmark()." <font class=\"color\">O/S:</font></td><td>".$gameserver ->rules["bf2_os"]."</td></tr>"
  . "		<tr><td style=\"padding-right: 5px;\">".checkmark()." <font class=\"color\">Team Balance:</font></td><td>".($gameserver ->rules["bf2_autobalanced"]?"Yes":"No")."</td></tr>"
  . "		<tr><td style=\"padding-right: 5px;\">".checkmark()." <font class=\"color\">Pure:</font></td><td>".($gameserver ->rules["bf2_pure"]?"Yes":"No")."</td></tr>"
  . "		<tr><td style=\"padding-right: 5px;\">".checkmark()." <font class=\"color\">Time Limit:</font></td><td>".nduration($gameserver ->rules["timelimit"])."</td></tr>"
  . "		</table>"
  . "		</td>"
  . "		<td class=\"row\">"
  . "		<table cellspacing=0 cellpadding=0>"
 . "		<tr><td style=\"padding-right: 5px;\">".checkmark()." <font class=\"color\">Friendly Fire:</font></td><td>".($gameserver ->rules["bf2_friendlyfire"]?"Yes":"No")."</td></tr>"
  . "		<tr><td style=\"padding-right: 5px;\">".checkmark()." <font class=\"color\">TK Handling:</font></td><td>".$gameserver ->rules["bf2_tkmode"]."</td></tr>"
  . "		<tr><td style=\"padding-right: 5px;\">".checkmark()." <font class=\"color\">Start Delay:</font></td><td>".$gameserver ->rules["bf2_startdelay"]."</td></tr>"
  . "		<tr><td style=\"padding-right: 5px;\">".checkmark()." <font class=\"color\">ScoreLimit:</font></td><td>".$gameserver ->rules["bf2_scorelimit"]."</td></tr>"
  . "		<tr><td style=\"padding-right: 5px;\">".checkmark()." <font class=\"color\">Ticket Ratio:</font></td><td>".$gameserver ->rules["bf2_ticketratio"]."</td></tr>"
  . "		<tr><td style=\"padding-right: 5px;\">".checkmark()." <font class=\"color\">Bot Players:</font></td><td>".$gameserver ->rules["bf2_bots"]."</td></tr>"
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
}
return $retval;
}

}

?>
