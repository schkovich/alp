<?php
require_once 'include/_universal.php';
include_once 'include/cl_display.php';
$x = new universal('restaurants','',0);
if ($x->is_secure()) { 
	$x->display_top(); ?>
		<b>restaurants</b>:<br />
		<br />
		<?php
		$x->add_related_link('modify restaurants in the area.','admin_restaurant.php',2);
		$x->add_related_link('back to modifying your food run.','foodrun.php',1);
		$x->display_related_links(); 

		$y = new display('restaurants','restaurant',0,0,'foodplaces','itemid','name');
		$y->add_field('name','restaurant name',0,0);
		$y->add_field('address','address',0,0);
		$y->add_field('city','city',0,0);
		//$y->add_field('state','state',0,0);
		//$y->add_field('zipcode','zip code',0,0);
		$y->add_field('phone','phone #',0,0);
		$y->add_field('traveltime','travel time from lan',0,0);
		$y->add_field('delivery','delivery?',0,0,array(),array(),array('no','yes'));
		if($master['internetmode']) $y->add_field(rand(),'mapquest',0,0,array(),array('http://www.mapquest.com/maps/map.adp?country='.$country.'&addtohistory=&address=[address]&city=[city]&state=[state]&zipcode=[zipcode]&submit=Get+Map','mapquest'));
		$y->display_solo(); ?>
		<br />
	<?php
	$x->display_bottom();
} else {
	$x->display_slim('you are not authorized to view this page.');
}
?>