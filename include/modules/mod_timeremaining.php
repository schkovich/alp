<?php
global $container, $userinfo, $end, $start;
include_once 'include/cl_bargraph.php';
/*
if (current_security_level() >= 1 && isset($userinfo['date_of_departure'])) {
	$left = date('U',strtotime($userinfo['date_of_departure'])) - date('U');
	$total = date('U',strtotime($userinfo['date_of_departure'])) - date('U',$start);
} else {
*/
	$left = date('U',$end) - date('U');
	$total = date('U',$end) - date('U', $start);
//}
if ($left > $total) { $percent = 1; } elseif ($left < 0) { $percent = 0; } else { $percent = $left/$total; } ?>
<strong>time remaining</strong><br />
<?php 
$b = new bargraph($percent,100,1);
$b->set_labels(1);
$b->set_padding(4,2);
$b->display(); ?>
<div style="font-family: courier new; font-size:10px">
start: <?php echo strtolower(disp_datetime($start, 1)) ?> <br />
end &nbsp;: <?php echo strtolower(disp_datetime($end, 1)) ?><br />
</div>
