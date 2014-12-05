<?php
// Improved ALP security exploit fix - added on July 19, 2006
defined('SQUERY_INVOKED') or die('No access.');
/*
 *  gsQuery - Querys game servers
 *  Copyright (c) 2002-2004 Jeremias Reith <jr@terragate.net>
 *  http://gsquery.terragate.net
 *
 *  This file is part of the gsQuery library.
 *
 *  The gsQuery library is free software; you can redistribute it and/or
 *  modify it under the terms of the GNU Lesser General Public
 *  License as published by the Free Software Foundation; either
 *  version 2.1 of the License, or (at your option) any later version.
 *
 *  The gsQuery library is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU
 *  Lesser General Public License for more details.
 *
 *  You should have received a copy of the GNU Lesser General Public
 *  License along with the gsQuery library; if not, write to the
 *  Free Software Foundation, Inc.,
 *  59 Temple Place, Suite 330, Boston,
 *  MA  02111-1307  USA
 *
 */

include_once($libpath."gameSpy.php");

/**
 * @brief Uses the gameSpy protocol
 * @author Jeremias Reith (jr@terragate.net)
 * @version $Id: ut2004.php,v 1.3 2006/07/19 21:33:15 synth_spring Exp $
 *
 * Adds UT2004 Color code support
 */
class ut2004 extends gameSpy
{

   /**
   * @brief htmlizes all color codes
   *
   * @param string a raw string
   * @return a html version of the given string
   */
  function htmlize($string) 
  {
    $length = strlen($string);
    $result = "";
    $numtags = 0;
    
    for($i=0;$i<$length;$i++) {
      if($string[$i] == "\x1B") {
        if($i<$length-4) {
	  $result .= "<span style=\"color: #". bin2hex(substr($string, $i+1, 3)) .'">';
          $i+=3;
          $numtags++;
        } else {
          break;
        }
      } else {
        $result .= htmlspecialchars($string[$i]);
      }
    }

    for($i=0;$i<$numtags;$i++) {
      $result .= '</span>';
    }
    
    return $result;
  }

  function _getClassName() 
  {
    return get_class($this);
  }


}

?>
