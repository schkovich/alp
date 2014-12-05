<?php
require_once 'include/_universal.php';
include_once 'include/cl_display.php';
include_once 'include/cl_pager.php';

$x = new universal('music','',1);
$x->add_related_link('Scan for new music','music.php?action=scan',3);
$x->add_related_link('Manage Song DB','music.php?show=1',2);
$x->add_related_link('listen to stream (ONLY ONE AT A TIME!!!)','music.php?listen=1',2);
$x->add_related_link('skip current song','music.php?action=skip',2);
$x->add_related_link('view current playlist/vote tracks up','music.php?action=playlist',1);
$x->add_related_link('browse/request songs','music.php',1);

$music_storage = $master['music_files'];
$music_min_time = $master['music_min_time'];
$music_max_line = $master['music_max_queue'];

// should add a trailing slash if one is not found
if (!preg_match('/^.*[\\/\\\\]$/', $music_storage)) {
    $music_storage .= '/';
} 

// should detect incorrect settings and redirect user to settings page to alleviate
if (!is_dir($master['music_files']) && $userinfo['priv_level'] > 1) {
    $x->display_slim('The path specified for Music Files appears to be incorrect or inaccessable, please set a valid path','admin_index.php',4);
    exit;
}

if ($x->is_secure() && $toggle['music']) {
    if (isset($_GET['listen']) && $_GET['listen'] == 1) {
        // serve the playlist up
        if(current_security_level() >= 2) {
            music_listen();
            exit;
        } else {
            $x->display_slim('You are not authorized to listen to the stream!','music.php',2);    
        }
    } else {
        $x->display_top(); 
        echo "<strong>music</strong>: <br /><br />";
        $x->display_related_links();

        switch ($_GET['action']) {
            // TODO: make it so admin can edit/update id3 tags
            case 'playlist':
 
                $y = new display('music','music',1,1,'music WHERE playingid!=\'0\' AND `nowplaying` != \'1\'','votes','votes DESC,playingid ASC');
                $y->add_default('artist','artist',array());
                $y->add_default('title','title',array());
                $y->add_default('genre','genre',array());
                $y->add_default('plays','plays',array());
                $y->add_default('votes','votes',array());
                $y->add_field('musicid','vote',1,0,array(),array('music.php?action=vote&musicid=[musicid]','vote'));
                $y->add_field('musicid ','del',3,0,array(),array('music.php?action=cancel&musicid=[musicid]','cancel'));
                $y->add_field('musicid  ','next',3,0,array(),array('music.php?action=force&musicid=[musicid]','set next'));
                if ($dbc->queryOne('SELECT playingid FROM music WHERE `nowplaying` = \'1\'') == 0) {
					// random play mode
                    $random = true;
                    $query = "SELECT artist, title, plays, votes FROM music 
                            WHERE `nowplaying` = '1'";
				} else {
                    // queued play mode
                    $random = false;
                    $query = "SELECT m.artist, m.title, m.plays, m.votes, u.userid, u.username
                            FROM music_votes AS v
                            LEFT JOIN music AS m ON v.playingid = m.playingid
                            LEFT JOIN users AS u ON v.userid = u.userid
                            WHERE m.nowplaying = '1'
                            ORDER BY v.subtime ASC 
                            LIMIT 1";
                }
                $nowplaying = $dbc->query($query);
                if ($nowplaying->numRows() >0) {
                    $nowplaying = $nowplaying->fetchRow();
                    if ($random) 
                    {
                        // fill random play details to array
                        $nowplaying['userid'] = 0;
                        $nowplaying['username'] = 'random play';
                    }
                    echo '  <table width="100%" cellpadding="3" cellspacing="0">'."\r\n";
                    echo '    <tr class="title" bgcolor="'.$colors['cell_title'].'">'."\r\n";
                    echo '      <td width="22%" align="right">Now Playing:</td><td width="78%">'.$nowplaying['artist'].' - '.$nowplaying['title'].'</td>'."\r\n";
                    echo '    </tr>'."\r\n";
                    echo '    <tr class="sm" bgcolor="'.$colors['cell_alternate'].'">'."\r\n";
                    echo '      <td align="right">Requested by:</td><td><a href="disp_users.php?id='.$nowplaying['userid'].'">'.$nowplaying['username'].'</a></td>'."\r\n";
                    echo '    </tr>'."\r\n";
                    echo '    <tr class="smm" bgcolor="'.$colors['cell_alternate'].'">'."\r\n";
                    echo '      <td>&nbsp;</td><td>[votes: '.$nowplaying['votes'].'] [plays: '.$nowplaying['plays'].']</td>'."\r\n";
                    echo '    </tr>'."\r\n";    
                    echo '  </table>'."\r\n";
                }
                $y->display_solo();
    
                break;
            case 'scan':
                music_scan_now($music_storage);
                break;
            case 'upload':
                if ($toggle['uploading'] || current_security_level() >= 2) {
                    music_upload_file();
				} else {
					echo "Uploads Disabled.";
				}
                break;
            case 'vote':
                if (isset($_GET['musicid']) && !empty($_GET['musicid'])) {
                    // work with musicid - do vote
                    music_vote($_GET['musicid']);
                    break;
                } // otherwise fall thru to default;
            case 'request':
                if (isset($_GET['musicid']) && !empty($_GET['musicid'])) {
    				// work with musicid - do request
                    music_request($_GET['musicid']);
                    break;
    			} // otherwise fall thru to default;
            case 'skip':
                music_skip_current();
                break;
            case 'force':
                if (isset($_GET['musicid']) && !empty($_GET['musicid'])) {
                    music_force($_GET['musicid']);
                    break;
                } // otherwise fall thru to default;
            case 'cancel':
                if (isset($_GET['musicid']) && !empty($_GET['musicid'])) {
                    music_cancel($_GET['musicid']);
                    break;
                } // otherwise fall thru to default;
            case 'delete':
                if (isset($_GET['musicid']) && !empty($_GET['musicid'])) {
                    music_delete($_GET['musicid']);
                    break;
                } // otherwise fall thru to default;
    		default:
                $pager = new pager();
        
                $y = new display('music','music',1,0,"music","artist","artist");
                $group = 0;
        
                // request songs
                $groups[$group] = "Browse/Request Songs";
                $y->add_default('artist','artist',array());
                $y->add_default('title','title',array());
                $y->add_default('genre','genre',array());
                $y->add_default('plays','plays',array());
                $y->add_field('musicid','request',1,$group,array(),array('music.php?action=request&musicid=[musicid]','request'));
                
                $group++;
                // admin controls
                $groups[$group] = "Administrator: Manage Song DB";
                $y->add_field('musicid','del',3,$group,array(),array('music.php?action=delete&musicid=[musicid]','delete'));
                $y->add_field('musicid ','next',3,$group,array(),array('music.php?action=force&musicid=[musicid]','set next'));
                $y->groups($groups);
                
                $y->display_table($pager, $URL_handler);
                unset($group);
    			break;
    	}
    }
    if ($toggle['uploading']) {
        ?>
        <div align="left">
        <br /><br />upload your own song:<br />
        <form action="music.php?action=upload" method="POST" enctype="multipart/form-data">
        <font size=1><b>file</b><font color="<?php echo $colors['primary']; ?>">(required)</font><br /></font>
        <input type="file" name="userfile"><br /><input type="submit" name="Upload" value="upload file" style="width:160px">
        </div>
    <?php
    }
    $x->display_bottom();
} else {
	$x->display_slim('you are not authorized to view this page.');
}

// functions (actions)
function music_request($musicid) {
    global $dbc, $music_min_time, $music_max_line;
    $userid = $_COOKIE['userid'];
    $lasttime = $dbc->queryOne("SELECT subtime FROM `music_votes` WHERE `userid` = '$userid' ORDER BY `subtime` DESC LIMIT 1");
    if((time() - $lasttime)/60 < $music_min_time AND current_security_level() <= 1) {
        echo "ERROR: You may only request one song every $music_min_time Minutes<br />";
    } elseif($dbc->queryOne("SELECT count(musicid) FROM `music` WHERE `playingid` != '0'") >= $music_max_line AND current_security_level() <= 1) {
        echo "ERROR: Queue is full... (max $music_max_line queue items)<br />Please try again later.<br />";
    } elseif($dbc->queryOne("SELECT count(musicid) FROM `music` WHERE `musicid` = '$musicid'") != 1) {
        echo 'ERROR: Referenced song is not in the database<br />';
    } elseif($dbc->queryOne("SELECT count(musicid) FROM `music` WHERE `musicid` = '$musicid' AND `playingid` != '0'") AND current_security_level() <= 1) {
        echo 'This song is already in the queue...you can <a href="music.php?action=vote&musicid='.$musicid.'">vote for it</a> to be moved up the queue<br />';
    } else {
        $now = time();
        $playingid = $dbc->queryOne("SELECT MAX(playingid) FROM `music_votes`");
        // must be first ever request
        if ($playingid === null) { $playingid = 0; }
        $playingid++;
        if ($dbc->query("INSERT INTO `music_votes` (playingid,musicid,userid,subtime) VALUES ('$playingid','$musicid','$userid','$now')")) {
            if ($dbc->query("UPDATE `music` SET `playingid` = '$playingid', `votes` = '1' WHERE `musicid` = '$musicid' LIMIT 1")) {
                echo 'ADDED: Music added to queue! <br />';
    		} else {
                echo 'FAILED: query failed to add music to queue<br />';
    		}
    	} else {
    		echo 'FAILED: query failed to add music to queue<br />';
    	}
    }
}

function music_vote($musicid) {
	global $dbc, $music_min_time, $music_max_line;
	$userid = $_COOKIE['userid'];
    if ($playingid = $dbc->queryOne("SELECT playingid FROM `music` WHERE `musicid` = '$musicid'")) {
		// playingid is set
        if($dbc->queryOne("SELECT count(userid) FROM `music_votes` WHERE `playingid` = '$playingid' AND `musicid` = '$musicid' AND `userid` = '$userid'") > 0) {
            echo 'ERROR: You may only vote for a song once';
        } elseif($dbc->queryOne("SELECT votes FROM `music` WHERE `musicid` = '$musicid'") < 1) {
            echo 'ERROR: This song is not in the queue, <a href="music.php?action=request&musicid='.$musicid.'">request it now</a>';
        } else {
            $now = time();
            if($dbc->query("INSERT INTO `music_votes` (playingid,musicid,userid,subtime) VALUES ('$playingid','$musicid','$userid','$now')")) {
                $votes = $dbc->queryOne("SELECT COUNT(userid) FROM `music_votes` WHERE `playingid` != '0' AND `musicid` = '$musicid'");
                if($dbc->query("UPDATE `music` SET `votes` = '$votes' WHERE `musicid` = '$musicid'")) {
                    echo 'ADDED: Vote added<br />';
                } else {
                    echo 'FAILED: query failed to update vote count<br />';
                }
            } else {
                echo 'FAILED: query failed to add vote<br />';
            }
        }
	} else {
        // no playingid
		echo 'FAILED: queue identifier not set or not valid<br />';
	}
}

function music_cancel($musicid) {
    global $dbc;
    if(current_security_level() >= 2) {
        if ($playingid = $dbc->queryOne("SELECT playingid FROM `music` WHERE `musicid` = '$musicid'")) {
            // playingid is set
            if($dbc->database_query("DELETE FROM `music_votes` WHERE `playingid` = '$playingid'")) {
                if($dbc->query("UPDATE `music` SET `votes` = '0', `playingid` = '0' WHERE `musicid` = '$musicid'")) {
                    echo 'REMOVED: Song removed<br />';
                } else {
                    echo 'FAILED: query failed to update vote count<br />';
                }
            } else {
                echo 'FAILED: query failed to delete votes<br />';
            }
        } else {
            // no playingid
            echo 'FAILED: song is not in the queue...<br />';
        }
    } else {
        echo 'you are not authorized to view this page.<br />';
    }
}

function music_skip_current() {
	global $dbc;
    if(current_security_level() >= 2) {
        if ($dbc->database_query("UPDATE `music` SET `nowplaying`='0' WHERE `nowplaying`='1'")) {
            echo 'SUCCESS: skipped currently playing song<br />';
        } else {
            echo 'FAILED: unable to skip currently playing song<br />';
        }
    } else {
        echo 'you are not authorized to view this page.<br />';
    }
}

function music_force($musicid) {
    global $dbc;
    if(current_security_level() >= 3) {
        $now = time() - 36000000;
        $userid = $_COOKIE['userid'];
        if ($dbc->database_query("UPDATE `music` SET `votes` = '99999', `playingid` = '1' WHERE `musicid` = '$musicid'")) {
			echo 'SUCCESS: forced file #'.$musicid.' to the top of the queue.';
		} else {
			echo 'FAILED: unable to force file #'.$musicid.' to the top of the queue.';
		}
        music_skip_current();
    } else {
        echo 'you are not authorized to view this page.';
    }
}

function music_delete($musicid) {
    global $dbc,$music_storage;
    if(current_security_level() >= 3) {
        $path = $dbc->queryOne("SELECT `path` FROM `music` WHERE `musicid` = '$musicid'");
        if (unlink($music_storage.$path)) {
			echo "DELETED: successfully removed file #$musicid ($path)";
            music_delete_record($musicid);
		} else {
			echo "FAILED: removal of file #$musicid ($path) failed";
		}        
    } else {
        $y->display_slim('you are not authorized to view this page.',(!empty($_GET['ref'])?urldecode($_GET['ref']):''));
    }
}

function music_delete_record($musicid) {
	global $dbc;
    if($dbc->database_query("DELETE FROM `music` WHERE `musicid` = '$musicid' LIMIT 1")) {
        echo "DELETED: Removed non-existent file #$musicid<br />";
        if($dbc->database_query("DELETE FROM `music_votes` WHERE `musicid` = '$musicid'")) {
            echo "DELETED: Removed vote records for file #$musicid<br />";
        } else {
            echo "DELETE FAILED: Removal of vote records for file #$musicid failed<br />";
        }
    } else {
        echo "DELETE FAILED: Removal of non-existent file #$musicid failed<br />";
    }
}

function music_upload_file() {
    global $dbc,$music_storage;
    // TODO: handle errors
    $orgname = strtolower($_FILES['userfile']['name']);
    $uploaded_name = $music_storage.'/'.$orgname;
    $uploaded_name = stripslashes($uploaded_name);
    $bad_chars = array(",","'","\"","?","&","*","$","#","@","%","^","!");
    $uploaded_name = str_replace($bad_chars,"",$uploaded_name);
    // TODO: add check for free space - maybe check free space before display upload file section?
    // TODO: log the userid that uploads the song
   	 if(eregi(".mp3$",$uploaded_name)) {
    	    // die("Only mp3 files accepted");
			//echo $$_FILES['userfile']['tmp_name'];
			//echo "<br />";
			//echo $uploaded_name;
  	      if(move_uploaded_file($_FILES['userfile']['tmp_name'],$uploaded_name)) {
		        $orgname = stripslashes($orgname);
		        $orgname = str_replace($bad_chars,"",$orgname);
		        define('GETID3_HELPERAPPSDIR',$music_storage);
		        require_once './music/getid3/getid3.php';
	    	    $getID3 = new getID3();
		        $fileInfo = $getID3->analyze("$uploaded_name");
		        getid3_lib::CopyTagsToComments($fileInfo); 
	    	    if($fileInfo["id3v2"]["comments"]["title"][0] != "") {
	        	    $title = $fileInfo["id3v2"]["comments"]["title"][0];
	            	$artist = $fileInfo["id3v2"]["comments"]["artist"][0]; 
	            	$genre = $fileInfo["id3v2"]["comments"]["genre"][0];
		            $album = $fileInfo["id3v2"]["comments"]["album"][0]; 
		        }
		         elseif ($fileInfo['id3v1']['title'] != "")
		        {
		            $title = $fileInfo['id3v1']['title'];
		            $artist = $fileInfo['id3v1']['artist'];
		            $genre = $fileInfo['id3v1']['genre'];
		        } else {
		            $title = $orgname;
		        }
		        $title = str_replace($bad_chars,"",$title);
	 	       $genre = str_replace($bad_chars,"",$genre);
	 	       $artist = str_replace($bad_chars,"",$artist);
	 	       echo "ADDING: $title - $artist - $genre<br />";
	 	       if ($dbc->database_query("INSERT INTO `music` (title,artist,genre,path,plays) VALUES ('$title','$artist','$genre','$orgname','0')")) {
	 	           echo "Success! <br />You may now <a href='music.php'>go back</a> and request this file be played";
		        } else {
		            echo "FAILED! Insertion failed, <a href='music.php'>go back</a> and try again, if it continues to fail contact an administrator";
	 	       }
		        // echo "INSERT INTO `music` (title,artist,genre,path,plays) VALUES ('$title','$artist','$genre','$orgname','0')<br />";
    	    } else {
    	    	echo '<br />Error uploading your song, might be the server, might be you. please make sure the mp3s directory on the server has write permissions';
    	    	echo '<br /> Error: ';
    	    	switch ($_FILES['userfile']['error']) {
				   case UPLOAD_ERR_INI_SIZE:
				       echo("The uploaded file exceeds the upload_max_filesize directive (".ini_get("upload_max_filesize").") in php.ini.");
				   break;
				   case UPLOAD_ERR_FORM_SIZE:
				       echo("The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form.");
				   break;
				   case UPLOAD_ERR_PARTIAL:
				       echo("The uploaded file was only partially uploaded.");
				   break;
				   case UPLOAD_ERR_NO_FILE:
				       echo("No file was uploaded.");
				   break;
				   case UPLOAD_ERR_NO_TMP_DIR:
				       echo("Missing a temporary folder.");
				   default:
				       echo("An unknown file upload error occured");
				}
				echo '<br /><br />';
    	    	print_r($_FILES);
     	   }
 	   } else {
  	      echo 'FAILED: Only accepting MP3 files';
  	  }
}

function music_listen() {
    global $lan, $dbc;
    // initialise and store unique stream id
    $stream_id = rand(1,99999999);
    $dbc->query("UPDATE `master` SET `music_stream_id` = '".$stream_id."'");
	// generate pls file response
    $alp_root = eregi_replace('/music.php$','', get_script_name());
    $link = 'http://'.$_SERVER['SERVER_NAME'].$alp_root.'/music/phpcast.php?stream_id='.$stream_id;
    $pls = "[playlist]\r\n".
    "NumberOfEntries=1\r\n".
    "File1=$link\r\n".
    "Title1=".$lan['name']." stream\r\n".
    "Length1=-1\r\n".
    "Version=2";
    header('Content-type: audio/x-scpls');
    header('Content-Disposition: attachment; filename="phpcast.pls"');
    echo $pls;
}

function music_scan_now($dir = MUSIC_DIR) { 
    global $dbc,$music_storage;
    if(current_security_level() >= 3) {
        // checks/fixes trailing slash in dir
        if (substr($dir,-1) == '/') {
            $dir = substr($dir,0,-1);
        }
        $result = $dbc->database_query("SELECT musicid, path FROM `music`");
        while($row = $dbc->database_fetch_array($result)) {
            // extract($row);
            $paths[$row['musicid']] = $row['path'];
            $valid[$row['musicid']] = 0;
        }
        if($dbc->database_num_rows($result) < 1) {
            $paths[0] = '0';
        }    
        if ($handle = opendir($dir)) 
        {
            // for each file
            while (false !== ($file = readdir($handle)))
            {
                // if it's a read file ( no shortcut / directory )
                // and its name ends with .mp3 (case insensitive)
                if(is_file($dir.'/'.$file) && eregi(".mp3",strtolower($file)))
                {
                    // if scan finds an "illegally named" mp3 it fixes it up
                    $bad_chars = array(",","'","\"","?","&","*","$","#","@","%","^","!");
                    $safe_name = str_replace($bad_chars,"",$file);
                    if($safe_name !== $file) {
                        if (rename($dir.'/'.$file, $dir.'/'.$safe_name)) {
                            echo "RENAMED: $file to $safe_name";
                            $file = $safe_name;    
                        } else {
                            echo "RENAME FAILED: renaming $file to $safe_name failed";    
                        }
                    }
                    $full_path = ($dir."/".$file);
                    
                    if($musicid = array_search($file,$paths)) {
                        $valid[$musicid] = 1;
                    } else {
                        require_once './music/getid3/getid3.php';
                        // this static needed to silence a getid3 startup error, doesnt effect anything else?
                        define('GETID3_HELPERAPPSDIR',$music_storage);
                        $getID3 = new getID3();
                        //settype($fileInfo, "array");
                        $fileInfo = $getID3->analyze($full_path);
                        getid3_lib::CopyTagsToComments($fileInfo);
                        if($fileInfo["id3v2"]["comments"]["title"][0] != "") {
                            $title = $fileInfo["id3v2"]["comments"]["title"][0];
                            $artist = $fileInfo["id3v2"]["comments"]["artist"][0];
                            $genre = $fileInfo["id3v2"]["comments"]["genre"][0];
                            $album = $fileInfo["id3v2"]["comments"]["album"][0];
                        }
                         elseif ($fileInfo['id3v1']['title'] != "")
                        {
                            $title = $fileInfo['id3v1']['title'];
                            $artist = $fileInfo['id3v1']['artist'];
                            $genre = $fileInfo['id3v1']['genre'];
                        } else {
                            $title = $file;
                        }
                        $bad_chars = array("'","\"","?","*","$","#","@","%","^","!");
                        $title = str_replace($bad_chars,"",$title);
                        $genre = str_replace($bad_chars,"",$genre);
                        $artist = str_replace($bad_chars,"",$artist);
                        // var_dump($fileInfo);
                        if ($dbc->database_query("INSERT INTO `music` (title,artist,genre,path,plays) VALUES ('$title','$artist','$genre','$file','0')")) {
                            echo "ADDED: $title-$artist-$genre<br />";
                        } else {
                            echo "FAILED: $title-$artist-$genre<br />";
                        }
                        unset($title, $artist, $genre, $file, $fileInfo);
                    }
                    if(!$valid)
                    	$valid["null"] = 1;
                }
            // end !
            }
            closedir($handle);
        } else {
            echo "ERROR: Unable to open directory<br />";
        }
        foreach($valid as $musicid => $found) {
            if($found == 0)
                music_delete_record($musicid);
        }
        echo "<br />All done scanning directory!!!<br />";
    } else {
        echo 'you are not authorized to view this page.';
    }
}
?>