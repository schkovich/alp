<?php
// Improved ALP security exploit fix - added on July 19, 2006
defined('SQUERY_INVOKED') or die('No access.');


//Still a couple issues with fs_game and si_version to work out

include_once($libpath."q3a.php");

/**
 * @brief Uses the Doom 3 protcol to communicate with the server
 * @author Jeremias Reith (jr@gsquery.org)
 * @version $Revision: 1.3 $
 *
 * Uses color code routines from q3a
 */
class doom3 extends q3a
{

  function query_server($getPlayers=TRUE,$getRules=TRUE)
  { 
    // flushing old data if necessary
    if($this->online) {
      $this->_init();
    }
      
    $command="\xFF\xFFgetInfo\x00\x00\x00\x00";
    if(!($result=$this->_sendCommand($this->address,$this->queryport,$command))) {
      $this->errstr='No reply received';
      return FALSE;
    }

    // strip header
    $noHeader = substr($result, strpos($result, "\x00\x00", 20)+2);
    // $noHeader = substr($result, 23);
    // find rules/players separator
    $seperatorPos = strpos($noHeader, "\x00\x00");

    // extract rule data
    $ruleData = substr($noHeader, 0, $seperatorPos);
    $rawdata=explode("\x00", $ruleData);
    
    // get rules and basic infos
    for($i=0;$i< count($rawdata);$i++) {
      switch (strtolower($rawdata[$i++])) {
      case 'si_gametype':
	$this->gametype=$rawdata[$i];
      case 'gamename':	
	$this->gamename=$rawdata[$i];
	break;
      case 'si_version':
	$this->gameversion=$rawdata[$i];
	break;
      case 'si_name':
	$this->servertitle=$rawdata[$i];
	break;
      case 'si_map':
	$mapdata=explode("/",$rawdata[$i]);
	$this->mapname=strtoupper($mapdata[2]);
	break;
      case 'si_maxplayers':
	$this->maxplayers=$rawdata[$i];
	break;
      case 'si_usepass':
	$this->password=$rawdata[$i];
	break;
      default:
	$this->rules[strtolower($rawdata[$i-1])] = $rawdata[$i];
      }
    }
 
    // game port is identical to query port
    $this->hostport = $this->queryport;
    $this->online =TRUE;

    if(!$getPlayers) {
      return TRUE;
    } 

    // getting player data
    $playerData = substr($noHeader, $seperatorPos+2);

    // length of player data
    $len = strlen($playerData)-8;

    for($i=0;$i<$len;$i=$posNextPlayer) { 
      // unpacking ping and client rate
      $curPlayer = unpack('@'.$i.'/x/nping/nrate', $playerData);
      // finding start offset of next player
      $posNextPlayer = strpos($playerData, "\x00", $i+8);
      if($posNextPlayer == FALSE) { break; } // abort on bogus data
      // extract player name
      $curPlayer['name'] = substr($playerData, $i+8, $posNextPlayer-$i-8);
      // add player to the list of players
      $this->players[$this->numplayers++] = $curPlayer; 
    }
    
    $this->playerkeys = array('name' => TRUE, 'ping' => TRUE, 'rate' => TRUE);

    return TRUE;
  }

/* this is for game specific cvar displays  */
function docvars($gameserver)
{

switch(strtolower($gameserver->gamename))
	{
	
	case "basedoom-1":
$retval="<table cellspacing=0 cellpadding=0 width=\"100%\">"
  . "		<tr>"
  . "		<td class=\"row\">"
  . "		<table cellspacing=0 cellpadding=0>"
  . "		<tr><td style=\"padding-right: 5px;\">".checkmark()." <font class=\"color\">Spectators:</font></td><td>".($gameserver ->rules["si_spectators"]== 1 ? "Yes" : "No")."</td></tr>"
  . "		<tr><td style=\"padding-right: 5px;\">".checkmark()." <font class=\"color\">Time Limit:</font></td><td>".$gameserver ->rules["si_timelimit"]."</td></tr>"
  . "		<tr><td style=\"padding-right: 5px;\">".checkmark()." <font class=\"color\">Frag Limit:</font></td><td>".$gameserver ->rules["si_fraglimit"]."</td></tr>"
  
  
  . "		</table>"
  . "		</td>"
  . "		<td class=\"row\">"
  . "		<table cellspacing=0 cellpadding=0>"
  . "		<tr><td style=\"padding-right: 5px;\">".checkmark()." <font class=\"color\">Warmup:</font></td><td>".($gameserver ->rules["si_warmup"] == 1 ? "Yes" : "No")."</td></tr>"
  . "		<tr><td style=\"padding-right: 5px;\">".checkmark()." <font class=\"color\">Pure:</font></td><td>".($gameserver ->rules["si_pure"] == 1 ? "Yes" : "No")."</td></tr>"
  . "		<tr><td style=\"padding-right: 5px;\">".checkmark()." <font class=\"color\">Team Damage:</font></td><td>".($gameserver ->rules["si_teamdamage"] == 1 ? "Yes" : "No")."</td></tr>"
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
