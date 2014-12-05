<?php
chdir('..');
include '_config.php';
include 'include/_functions.php';
chdir('music');

// Details of the stream
$stream['name']    = $lan['name']." Music Stream"; // Name of the stream  
$stream['genre']   = "various"; // Genre of the stream  
$stream['url']     = $lan["address"]; // URL of the site hosting phpcast  
$stream['public']  = "1"; // Set to 0 to make the server private  
$stream['bitrate'] = "192"; // Set this to the bitrate of your mp3 files  
$stream['path']    = $master['music_files']; // Path to the mp3 files. Relative or absolute  
$stream['fetch_id3'] = "1"; // Set to 1 to fetch ID3 tag infos from mp3 files 
$stream['polling_delay'] = 5; // number of seconds between play status checks (longer = less load on db)
$stream['debug']   = 0; // Set to 1 to enable debugging

// Configuration of the cache
// Has no effect if you put fetch_id3 to "0"
// Still deciding if this feature will be needed in ALP
/*
$cache['active'] = false; // Set to false to disable cache (up to 3x slower for tags extraction ) 
$cache['type'] 	 = gdbm; // Type of cache (gdbm, ndbm, db2, db3, db4) depending on your php compile option 
$cache['directory'] = "cache"; // Relative or absolute path to save cache files (beware of chmod) 
*/

// Size of shoutcast frames
$taille = 8192;

/* You won't have to touch below for a normal setup */

// check that the stream id is authorised
$stream['music_stream_id'] = (int)$_GET['stream_id'];
if (!$stream['music_stream_id'] || $stream['music_stream_id'] != $dbc->queryOne('SELECT `music_stream_id` FROM `master` LIMIT 1')) {
    // not an authorised stream, best serve a 403 forbidden header
    header('HTTP/1.0 403 Forbidden');
    exit();
}

// initialisation of the random number generator
srand((float) microtime()*1000000);

//Change Script settings
ob_start(); // need to start buffers to be able to detect client disconnects
ignore_user_abort(true); // allow script to complete execution after user disconnects from script
@ini_set("max_execution_time", "0"); // Allow endless run 
@ini_set("error_reporting", "0"); // Disable debugging for getting rid of stream desynch

if($stream['fetch_id3'])
{
    require_once './getid3/getid3.php';
}

// headers shoutcast
header("icy-notice1:This is a WINAMP ShoutCast Stream");
header("icy-notice2:You will only see binary data, please press STOP button");
header("icy-name:".$stream['name']);
header("icy-genre:".$stream['genre']);
header("icy-url:".$stream['url']);
if ($stream['public'] == "1") {
    header("icy-pub:1");
}
else {
    header("icy-pub:0");
}
header("icy-br:".$stream['bitrate']);
header("icy-metaint:".$taille);

/**
 * streamfile(string file, int offset, int musicid)
 * Function that alternates mp3 / data while keeping good sync (example 8192
 * bytes of mp3 - X bytes of meta data)
 * @author skullshot
 * 
 * @param string file The path of the file to use for stream source
 * @param int offset Stream data offset for current or next file
 * @param int musicid Database ID of source file
 * 
 * @return int Returns the finishing data offset of stream for next file
 *
 */
function streamfile($file, $offset=0, $musicid=0)
{
	global $taille;
	global $cache;
    global $stream;
    global $dbc;

   /* First we check if we fetch id3 tags */
   if($stream['fetch_id3'])
   {
        /* Fetch file info*/
        define('GETID3_HELPERAPPSDIR','./');
        $getID3 = new getID3();
        $fileInfo = $getID3->analyze($file);
        getid3_lib::CopyTagsToComments($fileInfo); 
   }
	$fp = fopen($file,"rb");
	if(!$fp)
    // TODO: if file does not exist remove it from the list, and continue
	die("Access denied for $file");
	
	// number of bytes left to stream
	$taillefichier = filesize($file);
	
	// Title of current mp3 file
	// Depending of ID3 tag version
    if($stream['fetch_id3'])
    {
        if($fileInfo["id3v2"]["comments"]["title"][0] != "") {
		      $title = "StreamTitle='".$fileInfo["id3v2"]["comments"]["title"][0]." - ".$fileInfo["id3v2"]["comments"]["artist"][0]."';StreamUrl='".$stream['url']."';\n\0";
	     }
	     else
        {
		      $title = "StreamTitle='".$fileInfo['id3v1']['title'][0]." - ".$fileInfo['id3v1']['artist'][0]." - ID3v1';StreamUrl='".$stream['url']."';\n\0";
	     }
    }
    else 
    {
   /* we fall back to default : putting filename in Stream Title */
        $title = "StreamTitle='".addslashes($file)."';StreamUrl='".$stream['url']."';\n\0";
    }
   
	// generating appropriate header
	$tailletitre = strlen($title);
	$headersize = ceil( (float)$tailletitre / 16.0 );
	$headerbyte = chr($headersize);
	
	// while we aren't at the end of the file
    $check_timer = time();
	while(!feof($fp) && $offset !== false)
	{
        // every 5s check if song is still "nowplaying"
        if (time() >= $check_timer + $stream['polling_delay']) {
            if (!$dbc->queryOne("SELECT `nowplaying` FROM `music` WHERE `musicid`='$musicid'")) {
                // song has been skipped or a new stream started, kill this loop
                break; 
            }    
        }
        
		// we read $taille data( except if we begin with
		// the end of an other file )
		$meuh = fread($fp,$taille-$offset);
		
		if(!$stream['debug'])
		{
			// mpeg stream
			echo $meuh;
            // flushes standard buffer
            flush();
            if(connection_aborted()) {
                $offset = false;
                ob_end_flush();
                break;
            } else {
                // flushes stored buffer
                ob_flush();
            }
		}
		
		// we finished the file
		if(feof($fp))
		{
			// we might have to add data to have
			//  8192 bytes of mpeg
			$offset = $taillefichier;
		}
		else
		{
			// and we count what has already been streamed
			$taillefichier -= ($taille-$offset);
			// delay is cancelled for next turn,
			// packets are $taille now !
			$offset = 0;
			
			if(!$stream['debug'])
			{
				// the first byte is title length / 16 ,
				// then if the titre is 64 bytes long, we'll have 4  (0x04)
				echo $headerbyte;
				// the title
				echo $title;
				// if it doesn't fill $headerbyte * 16 bytes,
				// we put random data for the byte to be the
				// right length ( ici un A, ca marche avec un espace aussi ,
				// meme s'il est recommandé de le faire avec des 0 binaires
				// ( 0x00 ) mais je ne sais pas comment php l'interprete ,
				// et ca semble marcher ainsi )
				for($i=$tailletitre;$i<($headersize*16);$i++)echo chr(65);
			}
		}
		
	}
	fclose($fp);
    unset($check_timer);
	if ($offset!==false) {
        // We keep the delay for the next frame
    	$offset %= $taille;
    	if($offset < 0)$offset += $taille;
    }
	return $offset;
}

// Little ereg, that compares an string with
// *aster*is*ques with a normal string
function match($pattern, $string)
{
	$stucks = explode("*",$pattern);
	$str = $string;
	for($i=0;$i<count($stucks);$i++)
	{
		$stuck = $stucks[$i];
		if(empty($stuck))continue;
		
		$pos = strpos($str,$stuck);
		$str = stristr($str,$stuck);
		if(($i==0)&&($pos != 0))return false;
		if(!is_string($str))return false;
	}
	return true;
}

// don't touch !
$offset = 0;

while($offset !== false)
{
    // check stored stream id against that in db and exit loop if its different
    if($stream['music_stream_id'] != $dbc->queryOne('SELECT `music_stream_id` FROM `master` LIMIT 1')) {
        // stream is not the most current, clear the offset and break out of the loop
        $offset = false;
        break;
    }
    
    /* query should return the highest priority song every 
     * time. there is potential that if 2 songs are "now playing" then it would
     * would return the higher voted song - but in that case there would be 2
     * songs now playing which in itself is a situation that should never occur
     * */
	$result = $dbc->query("SELECT * FROM `music` WHERE `playingid` != '0' ORDER BY `votes` DESC,`playingid` ASC LIMIT 1");
	if($result->numRows() < 1) {
        //nothing queued, random play mode
	   $result = $dbc->query("SELECT * FROM `music` ORDER BY rand() LIMIT 1");
	}
    // TODO: if no songs get selected, throw an error and exit
	$row = $result->fetchRow();
	$item = $stream['path']."/".$row['path'];
    // set all other songs to nowplaying=0 incase this is a second stream, this will kill the second stream
    $dbc->database_query("UPDATE `music` SET `nowplaying` = '0' WHERE `musicid` != '".$row['musicid']."' AND `nowplaying` = '1'");
	$dbc->database_query("UPDATE `music` SET `nowplaying` = '1' WHERE `musicid` = '".$row['musicid']."'");

	$offset = StreamFile($item, $offset, $row['musicid']);
	if($stream['debug']) {
		echo $item."<br />";
		echo "offset vaut maintenant $offset<br />";
        flush();
        ob_flush();
	}
    $musicid = $row['musicid'];
    $plays = $row['plays'] + ($offset !== false ? 1: 0);
	$dbc->database_query("UPDATE `music` SET `playingid` = '0', `plays` = '$plays', `votes` = '0', `nowplaying` = '0' WHERE `musicid` = '$musicid'");
	if($stream['debug']) { 
        echo "--------- ended !!<br />"; 
        flush();
        ob_flush();
    }
}
ob_end_clean();
?>