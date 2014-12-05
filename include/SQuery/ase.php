<?php
// Improved ALP security exploit fix - added on July 19, 2006
defined('SQUERY_INVOKED') or die('No access.');

include_once($libpath."gsQuery.php");

/**
 * @brief Uses the FarCry protcol to communicate with the server
 */
class ase extends gsQuery
{

  

  function query_server($getPlayers=TRUE,$getRules=TRUE)
  {       
    $this->playerkeys=array();
    $this->debug=array();
    $this->errstr="";
    $this->password=-1;
    
    $cmd="s";
    if(!($response=$this->_sendCommand($this->address, $this->queryport, $cmd))) {
      $this->errstr="No reply received";
      return FALSE;
    }    
  
 
$gamearray = array();

        $pos = 4;
        $gamearray[0] = substr($response,0,4);
        for($i = 1;$i < 10;$i++) {
            $gamearray[$i] = substr($response,$pos+1,ord(substr($response,$pos,1))-1);
            $pos = $pos + ord(substr($response,$pos,1));
        }
$this->numplayers=$gamearray[8];
$this->maxplayers=$gamearray[9];
$this->gametype=$gamearray[1];
$this->gamename=$gamearray[1];
$this->gameversion=$gamearray[6];
$this->servertitle=$gamearray[3];
$this->mapname=$gamearray[5];
$this->hostport=$gamearray[2];
$this->gametype=$gamearray[4];
$this->password=$gamearray[7];

// get rules and basic infos
 $endrules=0;
if (ord(substr($response,$pos,1))!=1) //skip rules
 {
do {
    $rulename= substr($response,$pos+1,ord(substr($response,$pos,1))-1);
              $pos = $pos + ord(substr($response,$pos,1));
    $rulevalue=substr($response,$pos+1,ord(substr($response,$pos,1))-1);
		    $pos = $pos + ord(substr($response,$pos,1));
			 	
	 switch ($rulename)
	 {
	  case "gr_ScoreLimit":  
	$this->scorelimit=$rulevalue;
	break;
      case "gr_NextMap":
     	$this->nextmap=$rulevalue;
	break;
     default:
	$rulename=strtolower($rulename);
	$this->rules[$rulename] = $rulevalue;
      }
	  
	}while(ord(substr($response,$pos,1))!=1); // the \x01 at the end indicates transfer to player list. 
	  
  }
	$pos++;  
    $playerdata=substr($response,$pos,strlen($response));
  if ($playerdata!=NULL) $this->_processPlayers($playerdata);
   
    $this->online=TRUE;

   return TRUE;
  }

  

  /**
   * @internal @brief Extracts the players out of the given data 
   *
   * @param rawPlayerData data with players
   * @return TRUE on success 
   */
  function _processPlayers($rawchunk) 
  {
 
    $pos=0;$endplayers=0;$i=0;$skipread=0;

    do {

      $delimiter=ord($rawchunk{$pos++}); // this is a flag byte 
/*
   the flag byte is broken down the following way:
   
         XX111111
	 ||||||||
	 |||||||-----   Name is present
	 ||||||------   Team Info is present
	 |||||-------   Skin Info is present
	 ||||--------   Score Info is present
	 |||---------   Ping Info is present
	 ||----------   Time Info is present
	 |-----------   Undefined
	 ------------   Undefined
	 
*/


      for($j=0;$j<6;$j++)// there are 6 possible data types, cycle through and grab each if present
      {
        $flag=($delimiter & (1<<$j));

        switch($flag)
        {
         case 1: // name
          $datname="name";
         break;
         case 2: // team info
          $datname="team";
         break;
         case 4: // skin
          $datname="skin";
         break;
         case 8: // score
          $datname="score";
         break;
         case 16:// ping
          $datname="ping";
         break;
         case 32:// time
          $datname="time";
         break;
         default:// item not supported
          $skipread=1;
         break;
        }
         // read the data
        if (!$skipread) {
          $this->playerkeys[$datname]=TRUE; 
          $this->players[$i][$datname] = substr($rawchunk,$pos+1,ord(substr($rawchunk,$pos,1))-1);
          $pos = $pos + ord(substr($rawchunk,$pos,1));
	if ($datname=="team") {
	   
		switch($this->players[$i][$datname])
        	{
        	case "red":
		case "RED":
		$this->teamcnt2++;
		$this->playerteams[$i]=2;
		$this->players[$i]["team"]=2;
		break;
		case "blue":
		case "BLUE":
		$this->teamcnt1++;
		$this->playerteams[$i]=1;
		$this->players[$i]["team"]=1;
		break;
		case "spectators":
		$this->spec++;
		$this->playerteams[$i]=3;
		$this->players[$i]["team"]=3;
		break;
         	default:// item not supported
          	echo "New Team Type:".$this->players[$i][$datname];
         	break;
        	}
          }
        }
        $skipread=0;

      } // end for 
      $i++; //next player

      if ($i==$this->numplayers) $endplayers++;	// we have reached the max # of players, stop looping. 

    }while (!$endplayers);
 
    return TRUE;
  }


 function htmlize($str) 
  {
    $colors = array("black", "white", "blue", "green", "red", "light-blue", "yellow", "pink", "orange", "grey");
    
    $str = htmlentities($str);
    
  $str=str_replace("\$\$","@!c!@",$str);
  $str=str_replace("\$=","",$str);
  $str=str_replace('$&amp;','',$str);	
    $num_tags = preg_match_all("/\\$(\d)/", $str, $matches);
    $str = preg_replace("/\\$(\d)/e", "'<span class=\"gsquery-'. \$colors[\$1] .'\">'", $str);
    
    return str_replace("@!c!@", "\$", $str) . str_repeat("</span>", $num_tags);
  }
  

  function _getClassName() 
  {
    return "ase";
  }

/* this is for game specific cvar displays  */
function docvars($gameserver)
{
    switch ($gameserver->gamename)
{
   case "farcry":
$retval="<table cellspacing=0 cellpadding=0 width=\"100%\">"
  . "		<tr>"
  . "		<td class=\"row\">"
  . "		<table cellspacing=0 cellpadding=0>"
  . "		<tr><td style=\"padding-right: 5px;\">".checkmark()." <font class=\"color\">Time Limit:</font></td><td>".$gameserver ->rules["gr_timelimit"]."</td></tr>"
  . "		<tr><td style=\"padding-right: 5px;\">".checkmark()." <font class=\"color\">Damage Scale:</font></td><td>".$gameserver ->rules["gr_damagescale"]."</td></tr>"
  . "		<tr><td style=\"padding-right: 5px;\">".checkmark()." <font class=\"color\">PreWar:</font></td><td>".($gameserver ->rules["gr_prewaron"] == 1 ? "Yes" : "No")."</td></tr>"
  . "		<tr><td style=\"padding-right: 5px;\">".checkmark()." <font class=\"color\">Respawn Time:</font></td><td>".$gameserver ->rules["gr_respawntime"]."</td></tr>"
  . "		<tr><td style=\"padding-right: 5px;\">".checkmark()." <font class=\"color\">Friendly Fire:</font></td><td>".($gameserver ->rules["gr_friendlyfire"] == 1 ? "Yes" : "No")."</td></tr>"
 . "		<tr><td style=\"padding-right: 5px;\">".checkmark()." <font class=\"color\">Headshot Mult:</font></td><td>".$gameserver ->rules["gr_headshotmultiplier"]."</td></tr>"
  
  . "		</table>"
  . "		</td>"
  . "		<td class=\"row\">"
  . "		<table cellspacing=0 cellpadding=0>"
. "		<tr><td style=\"padding-right: 5px;\">".checkmark()." <font class=\"color\">Min Team Limit:</font></td><td>".$gameserver ->rules["gr_minteamlimit"]."</td></tr>"
  . "		<tr><td style=\"padding-right: 5px;\">".checkmark()." <font class=\"color\">Max Team Limit:</font></td><td>".$gameserver ->rules["gr_maxteamlimit"]."</td></tr>"
  . "		<tr><td style=\"padding-right: 5px;\">".checkmark()." <font class=\"color\">DropFade Time:</font></td><td>".$gameserver ->rules["gr_dropfadetime"]."</td></tr>"
  . "		<tr><td style=\"padding-right: 5px;\">".checkmark()." <font class=\"color\">Invuln Timer:</font></td><td>".$gameserver ->rules["gr_invulnerabilitytimer"]."</td></tr>"
  . "		<tr><td style=\"padding-right: 5px;\">".checkmark()." <font class=\"color\">Dedicated:</font></td><td>".$gameserver ->rules["gr_dedicatedserver"]."</td></tr>"
  
  . "		</table>"
  . "		</td>"
  . "		</tr>"
  . "		</table>";
 	break;
       case "chrome":
$retval="<table cellspacing=0 cellpadding=0 width=\"100%\">"
  . "		<tr>"
  . "		<td class=\"row\">"
  . "		<table cellspacing=0 cellpadding=0>"
  . "		<tr><td style=\"padding-right: 5px;\">".checkmark()." <font class=\"color\">Dedicated:</font></td><td>".$gameserver ->rules["dedicated"]."</td></tr>"
  . "		<tr><td style=\"padding-right: 5px;\">".checkmark()." <font class=\"color\">Time Limit:</font></td><td>".$gameserver ->rules["time limit"]."</td></tr>"
  . "		<tr><td style=\"padding-right: 5px;\">".checkmark()." <font class=\"color\">Point Limit:</font></td><td>".$gameserver ->rules["points limit"]."</td></tr>"
  . "		<tr><td style=\"padding-right: 5px;\">".checkmark()." <font class=\"color\">Respawn Limit:</font></td><td>".$gameserver ->rules["respawns limit"]."</td></tr>"
  . "		<tr><td style=\"padding-right: 5px;\">".checkmark()." <font class=\"color\">Respawn Delay:</font></td><td>".$gameserver ->rules["respawn delay"]."</td></tr>"
 . "		<tr><td style=\"padding-right: 5px;\">".checkmark()." <font class=\"color\">Enemies on Map:</font></td><td>".$gameserver ->rules["enemies visible on map"]."</td></tr>"
  
  . "		</table>"
  . "		</td>"
  . "		<td class=\"row\">"
  . "		<table cellspacing=0 cellpadding=0>"
. "		<tr><td style=\"padding-right: 5px;\">".checkmark()." <font class=\"color\">Inventory Room:</font></td><td>".$gameserver ->rules["available inventory room"]."</td></tr>"
  . "		<tr><td style=\"padding-right: 5px;\">".checkmark()." <font class=\"color\">Identify Enemies:</font></td><td>".$gameserver ->rules["identify enemy players"]."</td></tr>"
  . "		<tr><td style=\"padding-right: 5px;\">".checkmark()." <font class=\"color\">Vehicles:</font></td><td>".$gameserver ->rules["available vehicles"]."</td></tr>"
 . "		<tr><td style=\"padding-right: 5px;\">".checkmark()." <font class=\"color\">Vehicle Respawn:</font></td><td>".$gameserver ->rules["vehicle respawn delay"]."</td></tr>"
  . "		<tr><td style=\"padding-right: 5px;\">".checkmark()." <font class=\"color\">Team Balance:</font></td><td>".$gameserver ->rules["team balance"]."</td></tr>"
  . "		<tr><td style=\"padding-right: 5px;\">".checkmark()." <font class=\"color\">Friendly Fire:</font></td><td>".$gameserver ->rules["friendly fire"]."</td></tr>"
  
  . "		</table>"
  . "		</td>"
  . "		</tr>"
  . "		</table>";
	break;
	case "purge":
$retval="<table cellspacing=0 cellpadding=0 width=\"100%\">"
  . "		<tr>"
  . "		<td class=\"row\">"
  . "		<table cellspacing=0 cellpadding=0>"
  . "		<tr><td style=\"padding-right: 5px;\">".checkmark()." <font class=\"color\">Time:</font></td><td>".nduration($gameserver ->rules["time"])."</td></tr>"
  . "		<tr><td style=\"padding-right: 5px;\">".checkmark()." <font class=\"color\">Dead Talk:</font></td><td>".($gameserver ->rules["deadtalk"]== 1 ?"Yes":"No")."</td></tr>"
  . "		<tr><td style=\"padding-right: 5px;\">".checkmark()." <font class=\"color\">Max Level:</font></td><td>".$gameserver ->rules["maxlevel"]."</td></tr>"
  . "		<tr><td style=\"padding-right: 5px;\">".checkmark()." <font class=\"color\">Altars:</font></td><td>".$gameserver ->rules["altar"]."</td></tr>"
 
  . "		</table>"
  . "		</td>"
  . "		<td class=\"row\">"
  . "		<table cellspacing=0 cellpadding=0>"
. "		<tr><td style=\"padding-right: 5px;\">".checkmark()." <font class=\"color\">Rounds:</font></td><td>".$gameserver ->rules["rounds"]."</td></tr>"
  . "		<tr><td style=\"padding-right: 5px;\">".checkmark()." <font class=\"color\">Portals:</font></td><td>".$gameserver ->rules["portal"]."</td></tr>"
  . "		<tr><td style=\"padding-right: 5px;\">".checkmark()." <font class=\"color\">Min Level:</font></td><td>".$gameserver ->rules["minlevel"]."</td></tr>"
 . "		<tr><td style=\"padding-right: 5px;\">".checkmark()." <font class=\"color\">Lamer Guard:</font></td><td>".($gameserver ->rules["lamerguard"]== 1 ?"Yes":"No")."</td></tr>"

  . "		</table>"
  . "		</td>"
  . "		</tr>"
  . "		</table>";
	break;
	case "soldat":
$retval="<table cellspacing=0 cellpadding=0 width=\"100%\">"
  . "		<tr>"
  . "		<td class=\"row\">"
  . "		<table cellspacing=0 cellpadding=0>"
  . "		<tr><td style=\"padding-right: 5px;\">".checkmark()." <font class=\"color\">Respawn Time:</font></td><td>".$gameserver ->rules["respawn time"]."</td></tr>"
  . "		<tr><td style=\"padding-right: 5px;\">".checkmark()." <font class=\"color\">Bonus Freq:</font></td><td>".$gameserver ->rules["bonus frequency"]."</td></tr>"
   
  . "		</table>"
  . "		</td>"
  . "		<td class=\"row\">"
  . "		<table cellspacing=0 cellpadding=0>"
. "		<tr><td style=\"padding-right: 5px;\">".checkmark()." <font class=\"color\">Realistic:</font></td><td>".($gameserver ->rules["realistic mode"]==1? "Yes":"No")."</td></tr>"
  
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
