<?php
$libpath='include/SQuery/';
define('SQUERY_INVOKED', TRUE);

require($libpath.'main.lib.php');
include_once($libpath.'gsQuery.php');
include_once 'include/gamelauncher/hlsw_supported_games.php';

function queryServer($address, $port, $protocol, $getPlayers=FALSE, $getRules=FALSE)
{
  global $libpath;
  global $gametable,$dbc;
  include_once($libpath."gsQuery.php"); 
  
  	require_once($libpath.'main.lib.php');
  	$ip = $address.":".$port;
  	$result = $dbc->queryOne("SELECT g.`short` FROM `servers` s LEFT OUTER JOIN `games` g ON `s`.`gameid` = `g`.`gameid` WHERE `s`.`ipaddress` = '$ip' AND `g`.`querystr2` = '$protocol'");
  	if(!$result)
  		$result = $dbc->queryOne("SELECT g.`short` FROM `game_requests` s LEFT OUTER JOIN `games` g ON `s`.`gameid` = `g`.`gameid` WHERE `s`.`ipaddress` = '$ip' AND `g`.`querystr2` = '$protocol'");
  	$qport = calcqport($port,$result);
	if(!$qport)
		echo "Unable to calculate query port for $address";
	else
		$port = $qport;
	//echo $port." ==> ".$qport;
  if(!$address && !$port && !$protocol) {
    echo "No parameters given\n";
    return FALSE;
  }
	//echo "  ".$protocol." ".$address." : ".$port;
  $gameserver=gsQuery::createInstance($protocol, $address, $port);
  if(!$gameserver) {
    //echo "Could not instantiate gsQuery class. Does the protocol you've specified exist?\n";
    return FALSE;
  }
  
  if(!$gameserver->query_server($getPlayers, $getRules)) {
    // query was not succesful, dumping some debug info
    //echo "<div>Error ".$gameserver->errstr."</div>\n";
    return FALSE;
  }

  return $gameserver;
}
?>