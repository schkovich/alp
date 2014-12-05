<?php
global $master;
if ($master['shoutbox_index_limit'] != 0) {
	$holder = ' LIMIT '.$master['shoutbox_index_limit'];
} else {
	$holder = '';
}
$data = $dbc->database_query('SELECT userid, post, itemtime FROM shoutbox ORDER BY itemtime DESC '.$holder); ?>
<a href="shoutbox.php"><strong>shoutbox</strong></a><?php get_go('shoutbox.php'); ?><br />
<?php
if ($dbc->database_num_rows($data)) {
	spacer(1,4,1); ?>
	<table cellpadding="0" cellspacing="0" width="100%"><?php
	$counter = 0;
	while($row = $dbc->database_fetch_assoc($data)) { 
		$user = $dbc->queryOne('SELECT username FROM users WHERE userid='.(int)$row['userid']); 
		?>
		<tr>
                        <td><span class="sm"><a href="disp_users.php?id=<?php echo $row['userid']; ?>"><strong><?php echo $user; ?></strong></a>
                                <font color="<?php echo $colors['blended_text']; ?>"> at <?php echo disp_datetime(strtotime($row['itemtime']), 0); ?></font><br />
		<?php 
        $magicquotes = ini_get('magic_quotes_gpc');
        if (!$magicquotes){
            $post = stripslashes($row['post']);
        } else {
            $post = $row['post'];
        }
        echo $post; 
        
        ?><br /></span><img src="img/pxt.gif" width="1" height="3" border="0" alt="" /><br /></td>
		</tr>
		<?php
		$counter++;
	} ?>
	</table>
	<?php 
}
?>
