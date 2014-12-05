<?php
// Improved ALP security exploit fix - added on July 19, 2006
defined('SQUERY_INVOKED') or die('No access.');
/*
 *  gsQuery - Querys various game servers
 *  Copyright (c) 2002-2004 Jeremias Reith <jr@terragate.net>
 *  http://www.gsquery.org
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

include_once("q3a.php");


/**
 * @brief Uses the Quake 3 protcol to communicate with the server
 * @author Jeremias Reith (jr@terragate.net)
 * @version $Id: et.php,v 1.3 2006/07/19 21:33:15 synth_spring Exp $
 *
 * This class can communicate with most games based on the Quake 3
 * engine.
 */
class ET extends q3a
{

 /**
   * @brief htmlizes the given raw string
   *
   * @param var a raw string from the gameserver that might contain special chars
   * @return a html version of the given string
   */
 function htmlize($var)
  {
    $num_tags=0;
    $var = htmlspecialchars($var);

  // preprocess special case: two carets next to each other.
   while(ereg("\^\^", $var)) {
      	$var = preg_replace("#\^\^(.*)$#Usi", "@!c!@^$1", $var);
     }

    while(ereg("\^.", $var)) {
   	$var = preg_replace("#\^(.)(.*)$#e", "'<span class=\"gsqueryet-'.strtolower('\\1').'\">'.'\\2'", $var);
   //$var = preg_replace("#\^(.)(.*)$#Usi", "<span class=\"gsqueryet-$1\">$2", $var);
	$num_tags++;
     }

// replace illegal css  (yah narfight!)
$array_find = array(
   "gsqueryet-&\">lt;",
   "gsqueryet-&\">gt;",
   "gsqueryet-&\">amp;",
   "gsqueryet-'",
   "gsqueryet-=",
   "gsqueryet-?",
   "gsqueryet-.",
   "gsqueryet-,",
   "gsqueryet-!",
   "gsqueryet-*",
   "gsqueryet-$",
   "gsqueryet-#",
   "gsqueryet-(",
   "gsqueryet-)",
   "gsqueryet-@",
   "gsqueryet-%",
   "gsqueryet-+",
   "gsqueryet-|",
   "gsqueryet-{",
   "gsqueryet-}",
   "gsqueryet-\"",
   "gsqueryet-:",
   "gsqueryet-[",
   "gsqueryet-]",
   "gsqueryet-\\",
   "gsqueryet-/",
   "gsqueryet-;",
   "@!c!@"
);
$array_replace = array (
   "gsqueryet-less\">",
   "gsqueryet-greater\">",
   "gsqueryet-and\">",
   "gsqueryet-tick",
   "gsqueryet-equal",
   "gsqueryet-questionmark",
   "gsqueryet-point",
   "gsqueryet-comma",
   "gsqueryet-exc",
   "gsqueryet-star",
   "gsqueryet-dollar",
   "gsqueryet-pound",
   "gsqueryet-lparen",
   "gsqueryet-rparen",
   "gsqueryet-at",
   "gsqueryet-percent",
   "gsqueryet-plus",
   "gsqueryet-bar",
   "gsqueryet-lbracket",
   "gsqueryet-rbracket",
   "gsqueryet-quote",
   "gsqueryet-colon",
   "gsqueryet-lsqr",
   "gsqueryet-rsqr",
   "gsqueryet-lslash",
   "gsqueryet-rslash",
   "gsqueryet-semic",
   "^",
);

return str_replace($array_find, $array_replace, $var) . str_repeat("</span>", $num_tags);;
  }
}
?>
