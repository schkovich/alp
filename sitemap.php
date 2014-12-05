<?php
require_once 'include/_universal.php';
$x = new universal('site map','',0);
if ($x->is_secure()) {
    $x->display_top(); ?>
	<strong>site map</strong>:<br />
	<br />
	<?php
	foreach ($tree as $val) {
		if ($val[2]==-1) {
            echo $val[0].'<br />';
        }
	}
	$x->display_bottom();
} else {
	$x->display_slim('you are not authorized to view this page.');
}
?>