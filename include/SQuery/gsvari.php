<?php
// Improved ALP security exploit fix - added on July 19, 2006
defined('SQUERY_INVOKED') or die('No access.');

include_once($libpath."gameSpy.php");


class gsvari extends gameSpy
{

function query_server($getPlayers=TRUE,$getRules=TRUE)
  {       
    $this->playerkeys=array();
    $this->debug=array();
    $this->errstr="";
    $this->password=-1;
    
    $cmd="\\basic\\\\info\\";
    if(!($result=$this->_sendCommand($this->address, $this->queryport, $cmd))) {
      $this->errstr="No reply received";
      return FALSE;
    }  

 $pos=strpos($result,'\player_');
  if ($pos)
   {	
   
   $players=substr($result,$pos);
   $rules=substr($result,0,$pos);
    $this->_processServerInfo($result);
    $this->_processRules($rules); 
    $this->_processPlayers($players);   
    }
  else {
  // here if no players on server
  $this->_processServerInfo($result);
  $this->_processRules($result);
 }

  $this->online=TRUE;
  return TRUE;
  }


  function _getClassName() 
  {
    return get_class($this);
  }


}

?>
