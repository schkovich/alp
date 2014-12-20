<?php
@error_reporting(E_ALL ^ E_NOTICE); // This will NOT report uninitialized variables

require_once 'include/genesis_include.php';

//Database Connection Setup for ALP
$dbc = new genesis(); //Create Genesis Ojbect

DEFINE(DATABASE_CONNECT_ERROR, "<strong>ALP Error : 1 : Could not connect to database server
				<br />Please check ALP's database settings or the database server. (have you ran the ALP installer? - /install/install.php)</strong>");
DEFINE(DATABASE_SELECT_ERROR, "<strong>ALP Error : 2 : Could not select a database on the database server
				<br />Please make sure that there is a database created on the server (have you ran the ALP installer? - /install/install.php).  Does the username you are tring to use have full access to that database?  Also, check over the database settings in _config.php and make sure that they are correct.</strong>");
$db = $dbc->database_connect_select($database['server'],$database['user'],$database['passwd'],$database['database'],DATABASE_CONNECT_ERROR,DATABASE_SELECT_ERROR);

//Set database strict mode
if($database['strict_mode'] == -1) {
	$dbc->database_query("SET sql_mode = ''", FALSE); //Turn off MySQL 5 strict mode
} elseif ($database['strict_mode'] == 1) {
	$dbc->database_query("SET sql_mode = 'TRADITIONAL'", FALSE);  //Turn on MySQL 5 strict mode
}
	
//Dump data from database to global used variables
$master		= $dbc->database_fetch_assoc($dbc->database_query('SELECT * FROM master'));
$toggle		= $dbc->database_fetch_assoc($dbc->database_query('SELECT * FROM toggle'));
$lan		= $dbc->database_fetch_assoc($dbc->database_query('SELECT * FROM config'));
$language 	= $master['currentlanguage'];
$country 	= $lan['country']; //only used in restaurants.php for mapquest links.

//Set $userinfo variable
$userinfo = $dbc->queryRow("SELECT * FROM users WHERE BINARY username=".(!empty($_COOKIE['username'])?$dbc->quote($_COOKIE['username']):"''")." AND sesid<>'' AND sesid=".(!empty($_COOKIE['sesid'])?$dbc->quote($_COOKIE['sesid']):"''")." AND userid=".(ctype_digit($_COOKIE['userid'])?$_COOKIE['userid']:"''"));
if(!$userinfo) {
	$userinfo = array('priv_level' => 0,'userid' => 0, 'username' => '');
}

//Get lang files
require_once 'include/lang/get_lang.php';
$lang_dir = 'include/lang/';
if ($userinfo['language'] && is_file($lang_dir.$userinfo['language'].'.php')) {
    $lang_tmp = $userinfo['language'];
} elseif(is_file($lang_dir.$master['currentlanguage'].'.php')) {
    $lang_tmp = $master['currentlanguage'];
} else {
    $lang_tmp = 'en';
}
require_once $lang_dir.$lang_tmp.'.php';
require_once 'include/structuretree.php';

//Define ALP Mode from database
define('ALP_TOURNAMENT_MODE',$lan['alp_tournament_mode']);
define('ALP_TOURNAMENT_MODE_COMPUTER_GAMES',$lan['alp_tournament_mode_computer_games']);

//Define overrides
define('MONEY_SYMBOL','$');
define('MONEY_PREFIX',1);

//Set event start and end variables
if(!ALP_TOURNAMENT_MODE) {
	$start = strtotime($lan['datetimestart']? $lan['datetimestart'] : 'now');
	$end = strtotime($lan['datetimeend']? $lan['datetimeend'] : '+1 day');
}

//Define OS specific defines
if(empty($directory_seperator)) {
	if(strstr(getcwd(),'/')) {
		define('__DIRECTORY_SEPERATOR__','/');
	} elseif(strstr(getcwd(),'\\')) {
		define('__DIRECTORY_SEPERATOR__','\\');
	}
} else {
	define('__DIRECTORY_SEPERATOR__',$directory_seperator);
}

if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
	define('__INCLUDE_PATH_SEPERATOR__',';');
} else {
	define('__INCLUDE_PATH_SEPERATOR__',':');
}


//*******************Functions*****************************

function current_security_level()
{
	global $userinfo;
	if($userinfo) {
		return $userinfo['priv_level'];
	} else { 
		return 0;
	}
}

function disp_datetime($datetime, $style=0)
{
	global $master,$userinfo;
	switch ($style) {
		case 0:
			//$dateformat = 'h:i a - d M Y'; //03:20 pm - 30 Nov 2005
			if($userinfo['dateformat'])
				$dateformat = $userinfo['dateformat'];
			else
				$dateformat = $master['dateformat'];
			break;
		case 1:
			//$dateformat = 'h:i a - D d M Y'; //01:00 pm - Wed 30 Nov 2005
			if($userinfo['dateformat'])
				$dateformat = $userinfo['dateformat'];
			else
				$dateformat = $master['dateformat'];
			break;
		case 2:
			$dateformat = 'h:i a'; //04:20 pm
			break;

	}
	return date($dateformat,$datetime);
}

function spacer($width=1,$height=1, $br=0, $align='')
{
	// returns a width x height pixel image.
    echo '<img src="img/pxt.gif" border="0" width="'.$width."\" height=\"".$height."\" alt=\"\"".(!empty($align)?" align=\"".$align."\"":"")." />".($br?"<br />":"");
}

function cp_menu()
{
	global $master, $userinfo, $colors, $toggle;
	$menu = array();
	if(current_security_level()>=1) {
		if(!ALP_TOURNAMENT_MODE) {
			$menu[] = array($userinfo["username"],"disp_users.php?id=".$userinfo['userid']);
			$menu[] = array(get_lang("profile"),"chng_userinfo.php");
			if($toggle["messaging"])
				$menu[] = array(get_lang("messaging"),"messaging.php");
		} else {
			$menu[] = array($userinfo['username'],'');
		}
		if(($master["doublecheckpassword"]&&$master["authbyiponly"])||!$master["authbyiponly"]) $menu[] = array(get_lang("change_pw"),"chng_passwd.php");
		$menu[] = array(get_lang("logout"),"logout.php");
	} else {
		$menu[] = array(get_lang("logedout_message"),"login.php");
		$menu[] = array(get_lang("forgot"),"passwd.php");
		$menu[] = array(get_lang("register"),"register.php");
	}
	$str = '';
	for($i=0;$i<sizeof($menu);$i++) {
		if($i>0) {
			$str .= "&nbsp;&nbsp;<font color=\"".$colors["blended_text"]."\">|</font>&nbsp;&nbsp;";
		}
		if(!empty($menu[$i][1])) $str .= "<a href=\"".$menu[$i][1]."\" class=\"menu\">".$menu[$i][0]."</a>";
		else $str .= $menu[$i][0];
	}
	return $str;
}

function mini_menu()
{
	global $tree;
	$menu = array();
	$file = basename(get_script_name());
	$save = -1;
	$i = 0;
	while(!empty($tree[$i])&&$save==-1) {
		if($tree[$i][1]==$file) {
			$save = $i;
		}
		$i++;
	}
	if($save>-1) {
		$menu[] = $save;
		$temp = $tree[$save][2];
		while($temp != -1) {
			$oldtemp = $temp;
			$menu[] = $oldtemp;
			$temp = $tree[$oldtemp][2];
		}
	}
	$str = "";
	for($i=sizeof($menu)-1;$i>=0;$i--) {
		if(!empty($tree[$menu[$i]][1])) $str .= "<a href=\"".$tree[$menu[$i]][1]."\" class=\"menu\">";
		$str .= $tree[$menu[$i]][0];
		if(!empty($tree[$menu[$i]][1])) $str .= "</a>";
		if($i!=0) {
			$str .=" &gt; ";
		}
	}
	return $str;
}

function get_arrow($type = 'off')
{
	global $images,$master;
	$tempurl = $master['currentskin'].$images['arrow_'.$type];
	$tempsize = getimagesize($tempurl);
 	?><img src="<?php echo $tempurl; ?>" width="<?php echo $tempsize[0]; ?>" height="<?php echo $tempsize[1]; ?>" border="0" alt="arrow" align="absmiddle" /><?php
}

function get_go($url)
{
	global $images,$master,$colors;
	$tempurl = $master['currentskin'].$images['go'];
	$tempsize = getimagesize($tempurl);
 	?>&nbsp;<a href="<?php echo $url; ?>"><img src="<?php echo $tempurl; ?>" width="<?php echo $tempsize[0]; ?>" height="<?php echo $tempsize[1]; ?>" border="0" alt="go" align="absmiddle" /></a><?php
}

function begitem($str, $endlines=1)
{ 
	global $colors; ?>
	<span style="color: <?php echo $colors['primary']; ?>;">&lt;<strong><?php echo $str; ?></strong>&gt;</span><?php echo ($endlines?'<br /><br />':''); ?>
	<?php
}

function enditem($str, $endlines=1)
{ 
	global $colors; ?>
	<font color="<?php echo $colors['secondary']; ?>">&lt;/<b><?php echo $str; ?></b>&gt;</font><?php echo ($endlines?"<br /><br />":""); ?>
	<?php
}

function dotted_line($toppadding=4,$botpadding=0,$width='100%')
{
	global $images, $master; 
	spacer(1,$toppadding,1); ?>
	<table border="0" cellpadding="0" cellspacing="0" width="<?php echo $width; ?>" style="background: url(<?php echo $master['currentskin'].$images['dotted_line']; ?>)">
	<tr><td><?php spacer('100%',1); ?></td></tr></table>
	<?php
	spacer(1,$botpadding,1);
}

function get_time_diff($before_date,$after_date,$long_labels=0)
{
	// both vars must be unix timestamps
	if($after_date-$before_date>0) {
		$totaltime = $after_date - $before_date;
		$days = floor($totaltime/86400);
		$hours = floor(($totaltime-(floor($totaltime/86400)*86400))/3600);
		$minutes = floor(($totaltime-(floor($totaltime/86400)*86400+floor(($totaltime-(floor($totaltime/86400)*86400))/3600)*3600))/60);
		$seconds = ($totaltime-(floor($totaltime/86400)*86400+floor(($totaltime-(floor($totaltime/86400)*86400))/3600)*3600+floor(($totaltime-(floor($totaltime/86400)*86400+floor(($totaltime-(floor($totaltime/86400)*86400))/3600)*3600))/60)*60));
		return ($days>0?$days.($long_labels?' days ':'d '):'').($hours>0?$hours.($long_labels?" hours ":"h "):"").($minutes>0?$minutes.($long_labels?" minutes ":"m "):"").($seconds>0?$seconds.($long_labels?" seconds ":"s"):"");
	} else {
		return '&nbsp;';
	}
}

function select_tournament($started='')
{ ?>
	<span class="sm">&nbsp;please select a tournament:<br /></span>
	<?php
	global $dbc;
	$data = $dbc->database_query("SELECT * FROM tournaments".($started!==''?" WHERE lockstart='".$started."'":"")." ORDER BY name");
	if($dbc->database_num_rows($data)) {
		while($row = $dbc->database_fetch_assoc($data)) { ?>
			&nbsp;&nbsp;&nbsp;<?php get_arrow(); ?>&nbsp;<a href="<?php echo get_script_name(); ?>?id=<?php echo $row["tourneyid"]; ?>"><?php echo $row["name"]; ?></a><br />
			<?php
		}
	} else { ?>
		&nbsp;&nbsp;&nbsp;there are no <?php echo ($started===''?"":($started===0?"unstarted":($started===1?"started":""))); ?> tournaments to list.  do you wish to <a href="admin_tournament.php">add a tournament</a>?
		<?php
	} ?>
	<br /><?php
}

function select_get($name_plural,$name_singular,$query,$query_id,$query_name,$get_var_name,$link_to_add)
{ ?>
	<span class="sm">&nbsp;please select a <?php echo $name_singular; ?>:<br /></span>
	<?php
	global $dbc;
	$data = $dbc->database_query($query);
	if($dbc->database_num_rows($data)) {
		while($row = $dbc->database_fetch_assoc($data)) { ?>
			&nbsp;&nbsp;&nbsp;<?php get_arrow(); ?>&nbsp;<a href="<?php echo get_script_name(); ?>?<?php echo $get_var_name; ?>=<?php echo $row[$query_id]; ?>"><?php echo $row[$query_name]; ?></a><br />
			<?php
		}
	} else { ?>
		&nbsp;&nbsp;&nbsp;there are no <?php echo $name_plural; ?> to list.  do you wish to <a href="<?php echo $link_to_add; ?>">add a <?php echo $name_singular; ?></a>?
		<?php
	} ?>
	<br /><?php
}


function adminlink($url,$security_level=2)
{
	if($security_level>=2&&current_security_level()>=$security_level) { 
		?><span class="sm">&nbsp;[<strong><a href="<?php echo $url; ?>">admin</a></strong>]</span><?php
	}
}

function is_email($email) {
	// check taken from http://www.phpexamples.net/codeExSnippet-58.html
	if(!eregi("^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,3})$", $email)) {
		return false;
	} else {
		return true;
	}
}

function is_ipaddress($str)
{
	// check taken from http://us4.php.net/ereg
	if (ereg("^([0-9]{1,3}(\.[0-9]{1,3}){3})$|^([0-9]{8,10})$|^([0-9a-fA-F]{0,4}(:[0-9a-fA-F]{0,4}){2,7}(([0-9]{1,3}(\.[0-9]{1,3}){3})?|(/[0-9]+)?))$", $str)) {

		return true;
	} else {
		return false;
	}
}

function get_modulelist()
{
	global $modulelist;
	return $modulelist;
}

function get_skinlist() {
    $skins = array();
    $dir   = 'skins';
    $dh    = opendir($dir);
    while (false !== ($filename = readdir($dh))) {
        if (is_dir('skins/'.$filename) && $filename != '.' && $filename != '..' && $filename != 'CVS') {
            $skins['skins/'.$filename.'/'] = $filename;
        }
    }
    return $skins;
}

function get_langlist() {
    $languages = array();
    $dir       = 'include/lang';
    $dh        = opendir($dir);
    while (false !== ($filename = readdir($dh))) {
        if (is_file('include/lang/'.$filename) && eregi("^[A-Z]{1,2}\.php$",$filename)&& $filename != '.' && $filename != '..' && $filename != 'CVS') {
            $language = str_replace('.php','',$filename);
            $languages[$language] = $language; 
        }
    }
    return $languages;
}

function get_datelist() {
    $dates = array();
    $example =  mktime(19,35,23,10,19,2007);
    $dates['h:i a - d M Y'] = date('h:i a - d M Y',$example);
    $dates['h:i a - D d M Y'] = date('h:i a - D d M Y',$example);
    $dates['H:i - d M Y'] = date('H:i - d M Y',$example);
    $dates['H:i - D d M Y'] = date('H:i - D d M Y',$example);
    $dates['h:i a - M d Y'] = date('h:i a - M d Y',$example);
    $dates['h:i a - D M d Y'] = date('h:i a - D M d Y',$example);
    $dates['H:i - M d Y'] = date('H:i - M d Y',$example);
    $dates['H:i - D M d Y'] = date('H:i - D M d Y',$example);
    $dates['h:i a - d M'] = date('h:i a - d M',$example);
    $dates['h:i a - D d M'] = date('h:i a - D d M',$example);
    $dates['H:i - d M'] = date('H:i - d M',$example);
    $dates['H:i - D d M'] = date('H:i - D d M',$example);
    $dates['h:i a - M d'] = date('h:i a - M d',$example);
    $dates['h:i a - D M d'] = date('h:i a - D M d',$example);
    $dates['H:i - M d'] = date('H:i - M d',$example);
    $dates['H:i - D M d'] = date('H:i - D M d',$example);
    return $dates;
}

function get_script_name() {
    // get the environment var script name and do sanity check
  $regex = '/[^a-zA-Z0-9_=&\/\.\-\?\+]/';
  $php_self = utf8_decode($_SERVER['PHP_SELF']);
  // should this also be done for tidiness?
  //  $in = urldecode($in);
  // callback is simply removing the offending chars
  // should we use preg_split() instead?
  $result = preg_replace_callback( $regex,
        create_function('$matches','return \'\';')
        ,$php_self );
  return $result;
}

function makeCommaDel($array, $name, $seperator) {
	// make a list of ids seperated by OR or AND for database queries.
	$counter = 0;
	$str = '';
	if(sizeof($array)>0) {
		foreach($array as $val) {
			if($counter>0) $str .= ' '.trim($seperator).' ';
			$str .= $name.'=\''.$val.'\'';
			$counter++;
		}
	}
	return $str;
}
?>
