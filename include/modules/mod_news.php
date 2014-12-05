<?php
global $colors, $dbc;
$beh = $dbc->database_query("SELECT * FROM news WHERE hide_item=0 ORDER BY itemtime DESC");
if ($dbc->database_num_rows($beh)) { 
	?><div class="sm" style="padding: 4px 4px 4px 4px"><?php
	while($behrow = $dbc->database_fetch_assoc($beh)) { ?>
		<font color="<?php echo $colors['primary']; ?>"><strong><?php echo $behrow['headline']; ?></strong><br /></font>
		<font color="<?php echo $colors['blended_text']; ?>" class="smm"><?php echo (!empty($behrow['itemtime']) ? disp_datetime(strtotime($behrow['itemtime']),1) : 'Invalid Date'); ?></font><br />
		<?php
		$article = $behrow['news_article'];
		$article = str_replace("&lt;","<",$article);
		$article = str_replace("&gt;",">",$article);
		$article = strip_tags($article,'<a><strong><b><i><u><font><img>');
		echo nl2br($article); 
		spacer(1,6,1); 
		dotted_line(4,4);
	} 
	?></div><?php
}
?>