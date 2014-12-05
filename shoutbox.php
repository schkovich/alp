<?php
require_once 'include/_universal.php';
$x = new universal('shoutbox','',0);
if ($x->is_secure() && $toggle['shoutbox']) { 
	if (empty($_POST)) { 
		$x->display_top(); ?>
		<strong>shoutbox</strong>:<br /><br />
		the shoutbox is a place to complain, praise, or just yell random things you feel like talking about.  please keep all posts
		appropriate according to the rules of the lan party.<br />
		<br />
		<?php
		if (current_security_level() >= 1) { ?>
			<table border=0 cellpadding=4 cellspacing=0 width="99%"><tr><td>
			<form action="shoutbox.php" method="POST">
			<input type="hidden" name="type" value="add">
			<textarea cols="" rows="3" name="post" style="font-size:10px; width: 99%">your post here.</textarea><br />
			</td><td width="50" valign="bottom" align="center"><input type="submit" name="submit" value="add" style="font-size:10px; width: 50px" class="formcolors"></form></td></tr></table>
			<?php
		}
		$data = $dbc->query('SELECT itemid, post, itemtime, userid FROM shoutbox ORDER BY itemtime DESC');
		$counter = 0; 
		if ($data->numRows()) { ?>
			<table cellpadding="0" cellspacing="0" width="100%">
			<?php
			while ($row = $data->fetchRow()) {
				$user = $dbc->queryOne('SELECT username FROM users WHERE userid='.(int)$row['userid']); ?>
				<tr<?php echo ($row['itemid']%2==1?' bgcolor="'.$colors['cell_alternate'].'"':''); ?>>
                                    <td><font class="sm">
                                        <strong><font color="<?php echo ($row['itemid']%2==0?$colors['primary']:$colors['secondary']); ?>">
                                        <?php echo $user; ?></font></strong>
                                        <font color="<?php echo $colors['blended_text']; ?>"> on 
                                        <?php echo disp_datetime(strtotime($row['itemtime']), 0); ?></font><br /></font>
				<?php 
                if (!get_magic_quotes_gpc()){
                    $post = stripslashes($row['post']);
                } else {
                    $post = $row['post'];
                }
                echo $post
                ?><br /><img src="img/pxt.gif" width="1" height="3" border="0" alt="" /><br /></td>
				<?php
				if (current_security_level() >= 2 || (current_security_level() >= 1 && $_COOKIE['userid'] == $row['userid'])) { ?>
					<td valign="middle" align="right">
					<form action="shoutbox.php" method="POST">
					<input type="hidden" name="type" value="delete">
					<input type="hidden" name="itemid" value="<?php echo $row['itemid']; ?>">
					<input type="submit" name="submit" value="delete" style="font-size:10px; width: 50px" class="formcolors">&nbsp;
					</form></td>
				<?php
				} else { ?>
					<td>&nbsp;</td>
				<?php
				} ?>
				</tr>
				<?php
			} ?>
			</table>
			<?php
		} else { ?>
			the shoutbox is empty.<br />
			<br />
			<?php
		}
		
		$x->display_bottom();
	} else {
		if (current_security_level() >= 1) {
			require_once 'include/cl_validation.php';
			$valid = new validate();
			if ($valid->get_value('type') == 'add') {
                $valid->set_value('post',str_replace("\r",' ',$valid->get_value_unclean('post')));
                $valid->set_value('post',str_replace("\n",' ',$valid->get_value_unclean('post')));
                $valid->set_value('post',strip_tags($valid->get_value_unclean('post')));
                $valid->set_value('post',substr($valid->get_value('post'),0,255));
				if ($dbc->database_query("INSERT INTO shoutbox (userid,itemtime,post) VALUES ('".$_COOKIE['userid']."','".date('Y-m-d H:i:s')."','".$valid->get_value('post')."')")) {
					$x->display_slim('post successful.','shoutbox.php');
				} else {
					$x->display_slim('error! message not posted.','shoutbox.php');
				}
			} elseif ($valid->get_value('type') == 'delete') {
				$user = $dbc->queryOne('SELECT userid FROM shoutbox WHERE itemid='.(int)$valid->get_value('itemid'));		
				if (current_security_level() >= 2 || $_COOKIE['userid'] == $user) {
					if ($dbc->query('DELETE FROM shoutbox WHERE itemid='.(int)$valid->get_value('itemid'))) {
						$x->display_slim('delete successful.','shoutbox.php');
					} else {
						$x->display_slim('error! delete unsuccessful.','shoutbox.php');
					}
				} else {
					$x->display_slim('you are not authorized to view this page.');
				}
			}
		} else {
			$x->display_slim('you are not authorized to view this page.');
		}
	}
} else {
	$x->display_slim('you are not authorized to view this page.');
}
?>