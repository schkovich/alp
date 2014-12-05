<?php
/*
 * $Id: staff.php,v 1.4 2005/12/04 01:54:54 skullshot Exp $
 */
require_once 'include/_universal.php';
$x = new universal('staff members','staff member',0);
$x->display_top();
if($toggle['staff']&&$x->is_secure()) { ?>
    <b>staff</b>: <br />
    <br />
    <?php
    $x->add_related_link('add/modify staff details','admin_staff.php',2);
    $x->add_related_link('add/modify staff fields','chng_staff.php',2);
    $x->display_related_links();
    
    $data = $dbc->database_query("SELECT us.id,us.userid,u.username,us.staffid,s.name,us.data,s.enabled,s.priority FROM users_staff AS us LEFT JOIN users AS u USING(userid) LEFT JOIN staff AS s ON us.staffid = s.staffid WHERE s.enabled='1' ORDER BY u.username, s.priority");
    // id userid username staffid name data enabled priority
    // TODO: restrict number of columns returned if not needed
    $staff = array();
    $staffnames = array();
    $fieldnames = array();
    // now massage data into usable grouping
    while($row = $dbc->database_fetch_assoc($data)) {
        $staff[$row['userid']][$row['staffid']] = $row['data'];
        $staffnames[$row['userid']] = $row['username'];
        $fieldnames[$row['staffid']] = $row['name'];
    }
    
    // work through array to deliver output
    while (list($staff_id, $staff_detail) = each($staff)) {
    ?>
<TABLE BGCOLOR="<?php echo $colors['border']; ?>" ALIGN="center" WIDTH="80%" BORDER="0" CELLSPACING="1" CELLPADDING="0" CLASS="cell_title">
  <TR>
        <?php
        if ($master['staff_photo_url'] != '') { 
            // photos enabled, display photo information if available...
        ?>
        <TD bgcolor="<?php echo $colors['cell_title']; ?>" WIDTH="<?php echo (isset($master['staff_photo_width'])?$master['staff_photo_width']:"200"); ?>" ALIGN="RIGHT" VALIGN="TOP" ROWSPAN="2"><P>
            <?php
            if (isset($staff_detail[array_search('photo',$fieldnames)]) && $staff_detail[array_search('photo',$fieldnames)] != '') {
                // photo allowed and provided
                printf('<IMG SRC="%s/%s" WIDTH="%s">',$master['staff_photo_url'],$staff_detail[array_search('photo',$fieldnames)],$master['staff_photo_width']);
            } else {
                // photo enabled but none provided, show no-photo image
                printf('<IMG SRC="img/staff_nopic.jpg" WIDTH="%s">',$master['staff_photo_width']);
            }
            echo "</TD>";
        } else {
            echo "&nbsp;";
        }
        ?></P>
        <TD bgcolor="<?php echo $colors['cell_title']; ?>" ALIGN="RIGHT" VALIGN="TOP"><A HREF="disp_users.php?id=<?php echo $staff_id; ?>"><?php echo $staffnames[$staff_id]; ?></A></TD>
  </TR>
  <TR>
        <TD bgcolor="<?php echo $colors['cell_title']; ?>" ALIGN="LEFT" VALIGN="TOP"> 
            <TABLE BGCOLOR="<?php echo $colors['cell_background']; ?>" WIDTH="100%" BORDER="0" CELLSPACING="0" CELLPADDING="4" CLASS="smm">
    <?php
        $counter = 0;
		while (list($field_id, $field_value) = each($staff_detail)) {
			// now we have staff_name, field_name, field_value
            if ($fieldnames[$field_id] != 'photo') {
        ?>
        <TR>
            <TD<?php echo ($counter%2==1?" bgcolor=\"".$colors['cell_alternate']."\"":""); ?> WIDTH="25%">
            <?php echo $fieldnames[$field_id]; ?>:
            </TD>
            <TD<?php echo ($counter%2==1?" bgcolor=\"".$colors['cell_alternate']."\"":""); ?> WIDTH="75%">
            <?php echo $field_value; ?>
            </TD>
        </TR>
        <?php
            $counter++;
            }
		}
    ?>
      </TABLE>
    </TD>
  </TR>
</TABLE>
<br />
<br />
    <?php
	}
} else {
    echo "you are not authorized to view this page.<br /><br />";
}
$x->display_bottom();
?>