<?php
// Improved ALP security exploit fix - added on July 19, 2006
defined('SQUERY_INVOKED') or die('No access.');


include_once($libpath."gameSpy.php");


class gore extends gameSpy
{

  

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
    
	$l=-1;
    for($i=1;$i<$count;$i++) {
      list($var, $playerid)=explode("_", $temp[$i]);
      if ($curid<>$playerid)
	{ $l++;
      	  $curid=$playerid;
	}
	
    switch($var) {
      case "player":
        $players[$l]["name"]=$temp[++$i];
        		
     	break;
      case "team":	
     	
	$this->playerkeys["team"]=TRUE;
    $this->playerteams[$l]=$temp[++$i];
    	$players[$l]["team"]=$temp[$i];
        if ($temp[$i]==0) {
  		$players[$l]["team"]=3;
		$this->playerteams[$l]=3;
		$this->spec++;
		
		}
	elseif (($temp[$i])==1) $this->teamcnt1++;
           else $this->teamcnt2++;
	 $curid=56;// trigger a change
	break;
      case "frags":
	$players[$l]["score"]=$temp[++$i];	
	$this->playerkeys["score"]=TRUE;    
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

switch(strtolower($gameserver->gamename))
	{
	
	case "gore":  
$retval="<table cellspacing=0 cellpadding=0 width=\"100%\">"
  . "		<tr>"
  . "		<td class=\"row\">"
  . "		<table cellspacing=0 cellpadding=0>"
  . "		<tr><td style=\"padding-right: 5px;\">".checkmark()." <font class=\"color\">Game Mode:</font></td><td>".$gameserver ->rules["gamemode"]."</td></tr>"
  . "		<tr><td style=\"padding-right: 5px;\">".checkmark()." <font class=\"color\">Score Limit:</font></td><td>".$gameserver ->rules["scorelimit"]."</td></tr>"
."              </table>"
  . "		</td>"
  . "		<td class=\"row\">"
  . "		<table cellspacing=0 cellpadding=0>"
  . "		<tr><td style=\"padding-right: 5px;\">".checkmark()." <font class=\"color\">Time Limit:</font></td><td>".$gameserver ->rules["timelimit"]."</td></tr>"
  . "		<tr><td style=\"padding-right: 5px;\">".checkmark()." <font class=\"color\">Team Play:</font></td><td>".($gameserver ->rules["teamplay"]== 1 ? "Yes":"No")."</td></tr>"
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
