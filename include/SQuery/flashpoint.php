<?php
// Improved ALP security exploit fix - added on July 19, 2006
defined('SQUERY_INVOKED') or die('No access.');


include_once($libpath."gameSpy.php");


class flashpoint extends gameSpy
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
    
    for($i=1;$i<$count;$i++) {
      list($var, $playerid)=explode("_", $temp[$i]);
      
      switch($var) {
      case "player":
      case "playername":
	$players[$playerid]["name"]=$temp[++$i];
     	break;
      case "team":	
      case "teamname":
	$i++;
	break;
	
      case "frags":
	$players[$playerid]["score"]=$temp[++$i];	
	$this->playerkeys["score"]=TRUE;    
	break;
      default:
	$players[$playerid][$var]=$temp[++$i];
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
	case "postal2":
$retval="<table cellspacing=0 cellpadding=0 width=\"100%\">"
  . "		<tr>"
  . "		<td class=\"row\">"
  . "		<table cellspacing=0 cellpadding=0>"
  . "		<tr><td style=\"padding-right: 5px;\">".checkmark()." <font class=\"color\">Game Mode:</font></td><td>".$gameserver ->rules["gamemode"]."</td></tr>"
  . "		<tr><td style=\"padding-right: 5px;\">".checkmark()." <font class=\"color\">Time Limit:</font></td><td>".$gameserver ->rules["timelimit"]."</td></tr>"
    
  
  . "		</table>"
  . "		</td>"
  . "		<td class=\"row\">"
  . "		<table cellspacing=0 cellpadding=0>"
  . "		<tr><td style=\"padding-right: 5px;\">".checkmark()." <font class=\"color\">Goal Score:</font></td><td>".$gameserver ->rules["goalscore"]."</td></tr>"
  . "		<tr><td style=\"padding-right: 5px;\">".checkmark()." <font class=\"color\">Listen Server:</font></td><td>".$gameserver ->rules["listenserver"]."</td></tr>"
  . "		</table>"  
  . "		<tr><td colspan=\"2\" style=\"padding-right: 5px;\">".checkmark()." <font class=\"color\">Mutators:</font>&nbsp;&nbsp;".$gameserver ->rules["mutators"]."</td></tr>" 
  . "		</td>"
  . "		</tr>"  
  . "		</table>";

	break;
	case "rune":
$retval="<table cellspacing=0 cellpadding=0 width=\"100%\">"
  . "		<tr>"
  . "		<td class=\"row\">"
  . "		<table cellspacing=0 cellpadding=0>"
  . "		<tr><td style=\"padding-right: 5px;\">".checkmark()." <font class=\"color\">Game Mode:</font></td><td>".$gameserver ->rules["gamemode"]."</td></tr>"
  . "		<tr><td style=\"padding-right: 5px;\">".checkmark()." <font class=\"color\">Time Limit:</font></td><td>".$gameserver ->rules["timelimit"]."</td></tr>"
  . "		<tr><td style=\"padding-right: 5px;\">".checkmark()." <font class=\"color\">Frag Limit:</font></td><td>".$gameserver ->rules["fraglimit"]."</td></tr>"
  
  
  . "		</table>"
  . "		</td>"
  . "		<td class=\"row\">"
  . "		<table cellspacing=0 cellpadding=0>"
  . "		<tr><td style=\"padding-right: 5px;\">".checkmark()." <font class=\"color\">Game Speed:</font></td><td>".$gameserver ->rules["gamespeed"]."</td></tr>"
  . "		<tr><td style=\"padding-right: 5px;\">".checkmark()." <font class=\"color\">Listen Server:</font></td><td>".$gameserver ->rules["listenserver"]."</td></tr>"
  . "		<tr><td style=\"padding-right: 5px;\">".checkmark()." <font class=\"color\">Auto Pickup:</font></td><td>".$gameserver ->rules["autopickup"]."</td></tr>"
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
