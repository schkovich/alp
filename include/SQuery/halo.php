<?php
// Improved ALP security exploit fix - added on July 19, 2006
defined('SQUERY_INVOKED') or die('No access.');


include_once($libpath."gsQuery.php");


class halo extends gsQuery
{

  
  function query_server($getPlayers=TRUE,$getRules=TRUE)
  {       
    $this->playerkeys=array();
    $this->debug=array();
    $this->errstr="";
    $this->password=-1;
    
    $cmd="þý".Chr(0)."wj??ÿÿÿÿ";
    if(!($response=$this->_sendCommand($this->address, $this->queryport, $cmd))) {
      $this->errstr="No reply received";
      return FALSE;
    }  
  
$response=substr($response,5,strlen($response));
  $r = explode(chr(0), $response);  

	if ($r[2]) {
			
			
			for ($i=0;$i<32;$i+=2) {
				
				
				switch ($r[$i])
			       {
				case "hostname":
				   $this->servertitle=$r[$i+1];
				  break;
				case "numplayers":
				  $this->numplayers=$r[$i+1];
			          break;
				case "maxplayers":
				  $this->maxplayers=$r[$i+1];
				  break;
				case "gamever":
				  $this->gameversion=$r[$i+1];
				  break;
				case "mapname":
				  $this->mapname=$r[$i+1];
				  break;
				case "gametype":
				  $this->gametype=$r[$i+1];
				  break;
				case "password":
				  $this->password=$r[$i+1];
				  break;
				case "hostport":
				 $this->hostport=$r[$i+1];
				 if (!$this->hostport) $this->hostport=$this->queryport;
				 break;
				default:
				$this->rules["$r[$i]"] = $r[$i+1];
				}
				

			}
			$this->gamename = "halo";
			$this->rules["numberoflives"] = $this->player_flag("numberoflives",$this->rules["player_flags"] & 3);
			$this->rules["maximumhealth"] = $this->player_flag("maximumhealth",($this->rules["player_flags"] >> 2) & 7);
			$this->rules["shields"] = $this->player_flag("shields",($this->rules["player_flags"] >> 5) & 1);
			$this->rules["respawntime"] = $this->player_flag("respawntime",($this->rules["player_flags"] >> 6) & 3);
			$this->rules["respawngrowth"] = $this->player_flag("respawngrowth",($this->rules["player_flags"] >> 8) & 3);
			$this->rules["oddmanout"] = $this->player_flag("oddmanout",($this->rules["player_flags"] >> 10) & 1);
			$this->rules["invisibleplayers"] = $this->player_flag("invisibleplayers",($this->rules["player_flags"] >> 11) & 1);
			$this->rules["suicidepenalty"] = $this->player_flag("suicidepenalty",($this->rules["player_flags"] >> 12) & 3);
			$this->rules["infinitegrenades"] = $this->player_flag("infinitegrenades",($this->rules["player_flags"] >> 14) & 1);
			$this->rules["startingequip"] = $this->player_flag("startingequip",($this->rules["player_flags"] >> 19) & 1);
			$this->rules["indicator"] = $this->player_flag("indicator",($this->rules["player_flags"] >> 20) & 3);
			$this->rules["otherplayersonradar"] = $this->player_flag("otherplayersonradar",($this->rules["player_flags"] >> 22) & 3);
			$this->rules["friendindicators"] = $this->player_flag("friendindicators",($this->rules["player_flags"] >> 24) & 1);
			$this->rules["friendlyfire"] = $this->player_flag("friendlyfire",($this->rules["player_flags"] >> 25) & 3);
			$this->rules["friendlyfirepenalty"] = $this->player_flag("friendlyfirepenalty",($this->rules["player_flags"] >> 27) & 3);
			$this->rules["autoteambalance"] = $this->player_flag("autoteambalance",($this->rules["player_flags"] >> 29) & 1);
			$this->rules["weaponset"] = $this->player_flag("weaponset",($this->rules["player_flags"] >> 15) & 15);
			

			$this->playerkeys["name"]=TRUE;
			
			$this->playerkeys["score"]=TRUE;
			$this->playerkeys["team"]=TRUE;
			$xc = 39;
     
			for ($i=0;$i<$this->numplayers;$i++) {
				$this->players[$i]["name"] = $r[$xc];
				$xc++;
				$this->players[$i]["score"] = $r[$xc]; $xc++;
				$this->players[$i]["ping"] = $r[$xc];
				 $xc++;
				$this->playerteams[$i] = $r[$xc]+1;
				$this->players[$i]["team"] = $r[$xc]+1; $xc++;
				if ($this->players[$i]["team"] == 1)
					$this->teamcnt1++;
				elseif ($this->players[$i]["team"] == 2)
					$this->teamcnt2++;
				//$totalscore = $totalscore+$playerdat[$i]->score;
			}

			if ($this->rules["teamplay"] == 1) {
				$xc = $xc+5;
				if ($r[$xc] > 0)
					$this->teamscore1 = $r[$xc];
				$xc = $xc+2;
				if ($r[$xc] > 0)
					$this->teamscore2 = $r[$xc];		
				if ($this->rules["gametype"] == "King" || $this->rules["gametype"] == "Oddball") {
					if ($this->teamscore2 > 0)
						$this->teamscore2 = nduration($this->teamscore2 / 30);
					if ($this->teamscore1 > 0)
						$this->teamscore1 = nduration($this->teamscore1 / 30);
				}
			}
       	return TRUE;
	}
 	else {   
     	  return FALSE;
  	}
 
}

function player_flag($flag, $n) {
	switch ($flag) {
		case "numberoflives":
			switch ($n) {
				case 0:
					return "Infinite";
				case 1:
					return "1 Life";
				case 2:
					return "3 Lives";
				case 3:
					return "5 Lives";
			}
		case "maximumhealth":
			switch ($n) {
				case 0:
					return "50%";
				case 1:
					return "100%";
				case 2:
					return "150%";
				case 3:
					return "200%";
				case 4:
					return "300%";
				case 5:
					return "400%";
			}
		case "shields":
			return ($n == 0 ? "Yes" : "No");
		case "respawntime":
			switch ($n) {
				case 0:
					return "Instant";
				case 1:
					return "5 sec";
				case 2:
					return "10 sec";
				case 3:
					return "15 sec";
			}
		case "respawngrowth":
			switch ($n) {
				case 0:
					return "Instant";
				case 1:
					return "5 sec";
				case 2:
					return "10 sec";
				case 3:
					return "15 sec";
			}
		case "oddmanout":
			return ($n == 0 ? "No" : "Yes");
		case "invisibleplayers":
			return ($n == 0 ? "No" : "Yes");
		case "suicidepenalty":
			switch ($n) {
				case 0:
					return "None";
				case 1:
					return "5 sec";
				case 2:
					return "10 sec";
				case 3:
					return "15 sec";
			}
		case "infinitegrenades":
			return ($n == 0 ? "No" : "Yes");
		case "weaponset":
			switch ($n) {
				case 0:
					return "Normal";
				case 1:
					return "Pistols";
				case 2:
					return "Rifles";
				case 3:
					return "Plasma";
				case 4:
					return "Sniper";
				case 5:
					return "No Sniping";
				case 6:
					return "Rocket Launchers";
				case 7:
					return "Shotguns";
				case 8:
					return "Short Range";
				case 9:
					return "Human";
				case 10:
					return "Covenant";
				case 11:
					return "Classic";
				case 12:
					return "Heavy Weapons";
			}
		case "startingequip":
			return ($n == 0 ? "Custom" : "Generic");
		case "indicator":
			switch ($n) {
				case 0:
					return "Motion Tracker";
				case 1:
					return "Nav Points";
				case 2:
					return "None";
			}
		case "otherplayersonradar":
			switch ($n) {
				case 0:
					return "No";
				case 1:
					return "All";
				case 2:
					return "Friends";
			}
		case "friendindicators":
			return ($n == 0 ? "No" : "Yes");
		case "friendlyfire":
			switch ($n) {
				case 0:
					return "Off";
				case 1:
					return "On";
				case 2:
					return "Shields Only";
				case 3:
					return "Explosives Only";
			}
		case "friendlyfirepenalty":
			switch ($n) {
				case 0:
					return "None";
				case 1:
					return "5 sec";
				case 2:
					return "10 sec";
				case 3:
					return "15 sec";
			}
		case "autoteambalance":
			return ($n == 0 ? "No" : "Yes");
	}
}
  
  /* this is for game specific cvar displays  */
function docvars($gameserver)
{
	  
$retval="<table cellspacing=0 cellpadding=0 width=\"100%\">"
  . "		<tr>"
  . "		<td class=\"row\">"
  . "		<table cellspacing=0 cellpadding=0>"
  . "		<tr><td style=\"padding-right: 5px;\">".checkmark()." <font class=\"color\"># of Lives:</font></td><td>".$gameserver ->rules["numberoflives"]."</td></tr>"
  . "		<tr><td style=\"padding-right: 5px;\">".checkmark()." <font class=\"color\">Max Health:</font></td><td>".$gameserver ->rules["maximumhealth"]."</td></tr>"
  . "		<tr><td style=\"padding-right: 5px;\">".checkmark()." <font class=\"color\">Shields:</font></td><td>".$gameserver ->rules["shields"]."</td></tr>"
  . "		<tr><td style=\"padding-right: 5px;\">".checkmark()." <font class=\"color\">Respawn Time:</font></td><td>".$gameserver ->rules["respawntime"]."</td></tr>"
  . "		<tr><td style=\"padding-right: 5px;\">".checkmark()." <font class=\"color\">Respawn Growth:</font></td><td>".$gameserver ->rules["respawngrowth"]."</td></tr>"
  . "		<tr><td style=\"padding-right: 5px;\">".checkmark()." <font class=\"color\">Odd Man Out:</font></td><td>".$gameserver ->rules["oddmanout"]."</td></tr>"
  . "		<tr><td style=\"padding-right: 5px;\">".checkmark()." <font class=\"color\">Invisible Players:</font></td><td>".$gameserver ->rules["invisibleplayers"]."</td></tr>"
  . "		<tr><td style=\"padding-right: 5px;\">".checkmark()." <font class=\"color\">Suicide Penalty:</font></td><td>".$gameserver ->rules["suicidepenalty"]."</td></tr>"
  ."              </table>"
  . "		</td>"
  . "		<td class=\"row\">"
  . "		<table cellspacing=0 cellpadding=0>"
  . "		<tr><td style=\"padding-right: 5px;\">".checkmark()." <font class=\"color\">Infin. Grenades:</font></td><td>".$gameserver ->rules["infinitegrenades"]."</td></tr>"
  . "		<tr><td style=\"padding-right: 5px;\">".checkmark()." <font class=\"color\">Indicator:</font></td><td>".$gameserver ->rules["indicator"]."</td></tr>"
  . "		<tr><td style=\"padding-right: 5px;\">".checkmark()." <font class=\"color\">Enemy On Radar:</font></td><td>".$gameserver ->rules["otherplayersonradar"]."</td></tr>"
  . "		<tr><td style=\"padding-right: 5px;\">".checkmark()." <font class=\"color\">Friend Indic.:</font></td><td>".$gameserver ->rules["friendindicators"]."</td></tr>"
  . "		<tr><td style=\"padding-right: 5px;\">".checkmark()." <font class=\"color\">Friendly Fire:</font></td><td>".$gameserver ->rules["friendlyfire"]."</td></tr>"
  . "		<tr><td style=\"padding-right: 5px;\">".checkmark()." <font class=\"color\">FF Penalty:</font></td><td>".$gameserver ->rules["friendlyfirepenalty"]."</td></tr>"
  . "		<tr><td style=\"padding-right: 5px;\">".checkmark()." <font class=\"color\">Team Balance:</font></td><td>".$gameserver ->rules["autoteambalance"]."</td></tr>"
  . "		<tr><td style=\"padding-right: 5px;\">".checkmark()." <font class=\"color\">Weapon Set:</font></td><td>".$gameserver ->rules["weaponset"]."</td></tr>"
  . "		</table>"
  . "		</td>"
  . "		</tr>"
  . "		</table>";
	
return $retval;
}
  
  function _getClassName() 
  {
    return "halo";
  }
}

?>
