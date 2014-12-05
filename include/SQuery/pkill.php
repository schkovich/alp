<?php
// Improved ALP security exploit fix - added on July 19, 2006
defined('SQUERY_INVOKED') or die('No access.');

include_once($libpath."gameSpy2.php");


class pkill extends gameSpy2
{

  
function _processTeams($rawdata) 
  {
  $this->gamename="pkill";  
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
	 
	} //end for $j
      $pi++;
   } // end for $i  
	
     $this->players=$players;
	print_r($players);
      
    return TRUE;
  }

  
  

/* this is for game specific cvar displays  */
function docvars($gameserver)
{
$retval="<table cellspacing=0 cellpadding=0 width=\"100%\">"
  . "		<tr>"
  . "		<td class=\"row\">"
  . "		<table cellspacing=0 cellpadding=0>";
if ($gameserver->rules["timelimit"])
 $retval.="		<tr><td style=\"padding-right: 5px;\">".checkmark()." <font class=\"color\">Timelimit:</font></td><td>".$gameserver ->rules["timelimit"]."</td></tr>";
if ($gameserver->rules["time_limit"])
$retval.="		<tr><td style=\"padding-right: 5px;\">".checkmark()." <font class=\"color\">Timelimit:</font></td><td>".$gameserver ->rules["time_limit"]."</td></tr>";

$retval.="		<tr><td style=\"padding-right: 5px;\">".checkmark()." <font class=\"color\">Max Spectators:</font></td><td>".$gameserver ->rules["max_spectators"]."</td></tr>"
  
  . "		</table>"
  . "		</td>"
  . "		<td class=\"row\">"
  . "		<table cellspacing=0 cellpadding=0>"
. "		<tr><td style=\"padding-right: 5px;\">".checkmark()." <font class=\"color\">Game Mode:</font></td><td>".$gameserver ->rules["gamemode"]."</td></tr>"
. "		<tr><td style=\"padding-right: 5px;\">".checkmark()." <font class=\"color\">Frag Limit:</font></td><td>".$gameserver ->rules["fraglimit"]."</td></tr>" 
  
  . "		</table>"
  . "		</td>"
  . "		</tr>"
  . "		</table>";
return $retval;
}

 function htmlize($var) 
  {
    $num_tags=0;	
    $var = htmlspecialchars($var);
 
  // preprocess special case: two carets next to each other.
   while(ereg("\#\#", $var)) {
      	$var = preg_replace("#\#\#(.*)$#Usi", "@!c!@#$1", $var);
     }
    
    while(ereg("\#.", $var)) {
   	$var = preg_replace("#\#(.)(.*)$#Usi", "<span class=\"gsquery-$1\">$2", $var);
	$num_tags++;
     }
    
// replace illegal css  (yah narfight!)
$array_find = array( 
   "gsquery-&\">lt;", 
   "gsquery-&\">gt;",
   "gsquery-&\">amp;", 
   "gsquery-'", 
   "gsquery-=", 
   "gsquery-?", 
   "gsquery-.", 
   "gsquery-,", 
   "gsquery-!", 
   "gsquery-*", 
   "gsquery-$", 
   "gsquery-#", 
   "gsquery-(", 
   "gsquery-)", 
   "gsquery-@", 
   "gsquery-%", 
   "gsquery-+", 
   "gsquery-|", 
   "gsquery-{", 
   "gsquery-}", 
   "gsquery-\"", 
   "gsquery-:", 
   "gsquery-[", 
   "gsquery-]", 
   "gsquery-\\", 
   "gsquery-/", 
   "gsquery-;",
   "@!c!@" 
); 
$array_replace = array ( 
   "gsquery-less\">", 
   "gsquery-greater\">",
   "gsquery-and\">", 
   "gsquery-tick", 
   "gsquery-equal", 
   "gsquery-questionmark", 
   "gsquery-point", 
   "gsquery-comma", 
   "gsquery-exc", 
   "gsquery-star", 
   "gsquery-dollar", 
   "gsquery-pound", 
   "gsquery-lparen", 
   "gsquery-rparen", 
   "gsquery-at", 
   "gsquery-percent", 
   "gsquery-plus", 
   "gsquery-bar", 
   "gsquery-lbracket", 
   "gsquery-rbracket", 
   "gsquery-quote", 
   "gsquery-colon", 
   "gsquery-lsqr", 
   "gsquery-rsqr", 
   "gsquery-lslash", 
   "gsquery-rslash", 
   "gsquery-semic",
   "^", 
); 

return str_replace($array_find, $array_replace, $var) . str_repeat("</span>", $num_tags);;  
  }

}

?>
