<?php
// Improved ALP security exploit fix - added on July 19, 2006
defined('SQUERY_INVOKED') or die('No access.');


include_once($libpath."gsQuery.php");

class savage extends gsQuery
{

  function query_server($getPlayers=TRUE,$getRules=TRUE)
  { 
    $this->playerkeys=array();
    $this->debug=array();
    $this->password=-1;
      
 
$command="\x9E\x4C\x23\x00\x00\xCE";
    if(!($result=$this->_sendCommand($this->address,$this->queryport,$command))) {
      $this->errstr="No reply received";
      return FALSE;
    }
  $this->hostport=$this->queryport;
  $this->gamename="savage";
/* process data */
/* cut string into pieces */
		$pieces = explode('ÿ', $result);
		$cnt = count($pieces, COUNT_RECURSIVE);
		$j=0;
		for($i=1; $i<$cnt; $i++)
		{
			$smpieces = explode('þ',$pieces[$i]);
			$output[$j++] = $smpieces[0];
			$output[$j++] = $smpieces[1];
		}

// Do Rules:
 $total=count($output);
 $j=0;
 while ($j<$total)
 {
 switch($output[$j++])
 {
 case "name":
 $this->servertitle=$output[$j++];
 break;
  case "cnum":
 $this->numplayers=$output[$j++];
 break;
  case "cmax":
 $this->maxplayers=$output[$j++];
 break;
  case "world":
 $this->mapname=$output[$j++];
 break;
 case "race1":
 $this->team1=strtoupper($output[$j++]);
 break;
 case "race2":
 $this->team2=strtoupper($output[$j++]);
 break; 
 case "gametype":
 $this->gametype=$output[$j++];
 break; 
 case "pass":
 $this->password=$output[$j++];
 break; 
 case "players":
 $playerstring=$output[$j++];
 break;
 default:
 $this->rules[$output[$j-1]]=$output[$j++];
 break;
 }
 }
 
/* sort players */
if (isset($playerstring))
{
	/* get lines, remove last (empty) line */
	$lines = preg_split("/\n/", $playerstring);

	$cnt = count($lines)-1;
	unset($lines[$cnt]);
    
	/* go through lines */	
	$team_name = 'unknown';
	$team_id = -1;
	$player_cnt = 0;
        $j=0;
	for ($i=0; $i<$cnt; $i++)
	{

		/* get team name & number */
		if (preg_match("/^Team (\d) \((.+)\):$/", $lines[$i], $match))
		{
			$team_id = $match[1];
			$team_name = $match[2];
		}
		/* set player */
		elseif ($lines[$i] != '--empty team--') {
			$this->players[$j]["name"] = $lines[$i];
			$this->players[$j]["team_name"] = $team_name;
			$this->players[$j]["team"]= $team_id;
			$this->playerteams[$j++]=$team_id;
			}
	}
	$this->playerkeys["name"]=TRUE;
	$this->playerkeys["team"]=TRUE;
	$this->playerkeys["team_name"]=TRUE;

	
}
 $this->online = TRUE;
    return TRUE;

}
    
function htmlize($var) 
  {
    $var = htmlspecialchars($var);
    while(ereg('\^([0-9][0-9][0-9])', $var)) {
     
	 $var = preg_replace("#\^([0-9][0-9][0-9])(.*)$#Usi", "$2", $var);
	      
    }
    while(ereg('\^([a-z])', $var)) {
     
	 $var = preg_replace("#\^([a-z])(.*)$#Usi", "$2", $var);
	      
    }
    return $var;
  }


/* this is for game specific cvar displays  */
function docvars($gameserver)
{
     $retval="<table cellspacing=0 cellpadding=0 width=\"100%\">"
  . "		<tr>"
  . "		<td class=\"row\">"
  . "		<table cellspacing=0 cellpadding=0>"
  . "		<tr><td style=\"padding-right: 5px;\">".checkmark()." <font class=\"color\">Protocol:</font></td><td>".$gameserver ->rules["protocol"]."</td></tr>"
  . "		<tr><td style=\"padding-right: 5px;\">".checkmark()." <font class=\"color\">Mod Name:</font></td><td>".$gameserver ->rules["fs_game"]."</td></tr>"
  . "		<tr><td style=\"padding-right: 5px;\">".checkmark()." <font class=\"color\">Time Limit:</font></td><td>".$gameserver ->rules["timelimit"]."</td></tr>"
  . "		<tr><td style=\"padding-right: 5px;\">".checkmark()." <font class=\"color\">Max Rate:</font></td><td>".spBytes($gameserver ->rules["sv_maxrate"])."</td></tr>"
  . "		<tr><td style=\"padding-right: 5px;\">".checkmark()." <font class=\"color\">Min Ping:</font></td><td>".$gameserver ->rules["sv_minping"]." ms</td></tr>"
  . "		<tr><td style=\"padding-right: 5px;\">".checkmark()." <font class=\"color\">Max Ping:</font></td><td>".$gameserver ->rules["sv_maxping"]." ms</td></tr>"
  . "		</table>"
  . "		</td>"
  . "		<td class=\"row\">"
  . "		<table cellspacing=0 cellpadding=0>"
  . "		<tr><td style=\"padding-right: 5px;\">".checkmark()." <font class=\"color\">Private Clients:</font></td><td>".($gameserver ->rules["sv_privateclients"]>0 ? $gameserver ->rules["sv_privateclients"] : "None")."</td></tr>"
  . "		<tr><td style=\"padding-right: 5px;\">".checkmark()." <font class=\"color\">Flood Protection:</font></td><td>".($gameserver ->rules["sv_floodprotect"] == 1 ? "Yes" : "No")."</td></tr>"
  . "		<tr><td style=\"padding-right: 5px;\">".checkmark()." <font class=\"color\">Allow Download:</font></td><td>".($gameserver ->rules["sv_allowdownload"]==1 ? "Yes" : "No")."</td></tr>"
  . "		<tr><td style=\"padding-right: 5px;\">".checkmark()." <font class=\"color\">Force Ready:</font></td><td>".($gameserver ->rules["g_forceready"] == 1 ? "Yes" : "No")."</td></tr>"
  . "		<tr><td style=\"padding-right: 5px;\">".checkmark()." <font class=\"color\">Force Respawn:</font></td><td>".($gameserver ->rules["g_forcerespawn"] == 1 ? "Yes" : "No")."</td></tr>"
  . "		<tr><td style=\"padding-right: 5px;\">".checkmark()." <font class=\"color\">Join Time:</font></td><td>".$gameserver ->rules["g_allowjointime"]."</td></tr>"
  . "		</table>"
  . "		</td>"
  . "		</tr>"
  . "		</table>";
return $retval;
}

}
?>