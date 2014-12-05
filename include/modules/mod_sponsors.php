<?php
global $master;
if ($master['sponsors_index_limit'] != 0) {
	$holder = ' LIMIT '.$master['sponsors_index_limit'];
} else {
	$holder = '';
}
$data = $dbc->query('SELECT * FROM sponsors ORDER BY priority, id '.$holder); ?>
<a href="disp_sponsors.php"><strong>event sponsors</strong></a><?php get_go('disp_sponsors.php'); ?><br />
<?php
if ($data->numRows()) {
	spacer(1,4,1); ?>
	<table align="center" border="0" width="100%" cellpadding="4" cellspacing="1" bgcolor="<?php echo $colors["cell_title"]; ?>"><?php
	$counter = 0;
	while($row = $data->fetchRow()) {
?>
  <tr><td align="center" valign="middle"><strong>
  <?php
  if (!empty($row['sponsor_url'])) {
  ?>
  <a href="<?php echo $row['sponsor_url']; ?>" style="color: <?php echo $colors['primary']; ?>">
  <?php
  }
  if (!empty($row['sponsor']) && empty($row['img_sidebar_url'])) {
    echo $row['sponsor'];
  }
  elseif (!empty($row['caption']) && empty($row['img_sidebar_url'])) {
    echo $row['caption'];
  }
  elseif (!empty($row['img_sidebar_url'])) {
  ?><img width="<?php echo $master['sponsors_width']; ?>" src="<?php echo $row['img_sidebar_url']; ?>" border="<?php echo $master['sponsors_border']; ?>" alt="<?php echo $row['img_alt']; ?>"><?php
  }
  else {
       	echo "No Data!";
  }
  if (!empty($row['sponsor_url'])) {
    echo '</a>';
  }
  ?>
  </strong></td></tr>
  <tr>
    <td bgcolor="<?php echo $colors['cell_alternate']; ?>" align="center" valign="middle">
        <span class="sm"><font color="<?php echo $colors['blended_text']; ?>">
        <?php
        if (!empty($row['caption'])) {
           echo $row['caption'];
        }
        elseif (!empty($row['sponsor'])) {
           echo $row['sponsor'];
        }
        ?>
        </font></span>
    </td>
  </tr>
<?php
		$counter++;
	} ?>
	</table>
	<?php
}
?>
