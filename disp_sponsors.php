<?php
require_once 'include/_universal.php';
// universal('plural name', 'singular name', minimum security level)
$x = new universal('Sponsors','',0);
if ($x->is_secure()) {
	$x->display_top();
	?>
		<strong>Event Sponsors</strong>:<br />
		<br />
	<?php
		$x->add_related_link('add/modify sponsors','admin_sponsors.php',2);
		$x->display_related_links();
	        $sponsors = $dbc->database_query('SELECT * FROM sponsors
                                WHERE enabled=1
                                ORDER BY priority, id');
                echo '<CENTER>';
	while($row = $dbc->database_fetch_array($sponsors)) {
?>
<table align="center" border="0" width="80%" cellpadding="4" cellspacing="1" bgcolor="<?php echo $colors["cell_title"]; ?>">
  <tr><td align="center" valign="middle"><strong>
  <?php
  if (!empty($row['sponsor_url'])) {
  ?>
  <a href="<?php echo $row['sponsor_url']; ?>" style="color: <?php echo $colors['primary']; ?>">
  <?php
  }
  if (!empty($row['sponsor']) && empty($row['img_banner_url']) && empty($row['img_sidebar_url'])) {
    echo $row['sponsor'];
  }
  elseif (!empty($row['caption']) && empty($row['img_banner_url']) && empty($row['img_sidebar_url'])) {
    echo $row['caption'];
  }
  elseif (!empty($row['img_banner_url'])) {
  ?><img width="<?php echo $master['sponsors_banner_width']; ?>" src="<?php echo $row['img_banner_url']; ?>" border="0" alt="<?php echo $row['img_alt']; ?>"><?php
  }
  elseif (!empty($row['img_sidebar_url'])) {
  ?><img width="<?php echo $master['sponsors_width']; ?>" src="<?php echo $row['img_sidebar_url']; ?>" border="0" alt="<?php echo $row['img_alt']; ?>"><?php
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
        if (!empty($row['description'])) {
           echo $row['description'];
        }
        elseif (!empty($row['caption'])) {
           echo $row['caption'];
        }
        elseif (!empty($row['sponsor'])) {
           echo $row['sponsor'];
        }
        else {
           echo 'No Information';
        }
        ?>
        </font></span>
    </td>
  </tr>
</table>
<br />
<?php
		$counter++;
	}
	        echo '</CENTER>';
	$x->display_bottom();
} else {
	$x->display_slim('you are not authorized to view this page.');
}
?>
