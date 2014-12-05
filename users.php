<?php
require_once 'include/_universal.php';
include_once 'include/cl_display.php';
include_once 'include/cl_pager.php';
$pager = new pager();
$x = new display('users','user',(ALP_TOURNAMENT_MODE?2:0),1,'users','userid','userid');
if(ALP_TOURNAMENT_MODE) {
	$x->add_default('username','username',array('disp_users.php?id=[userid]'));
	$x->add_field('first_name','first name',2);
	$x->add_field('last_name','last name',2);
	$x->add_field('email','email',2,0,array(),array('mailto:[email]'));
} else {
	$groups = array('default','default (page 2)');
	if (current_security_level() >= 2) { $groups[] = 'paid (admin only)'; }
	if ($toggle['filesharing']) {
		$groups[] = 'file sharing';
		$groups[] = 'ftp servers';
	}
	if ($toggle['gamingrigs']) {
		$groups[] = 'gaming rigs - cpu';
		$groups[] = 'gaming rigs - memory';
		$groups[] = 'gaming rigs - storage and graphics';
	}
	$groups[] = 'time of attendance';
	$groups[] = 'ip addresses';
	
	$x->groups($groups);
	$x->add_default('username','username',array('disp_users.php?id=[userid]'));
	$group = 0;
	// default
	$x->add_field('first_name','first name',0,$group);
	$x->add_field('last_name','last name',0,$group);
	$x->add_field('gaming_group','gaming group',0,$group);
	if($toggle['seating']) $x->add_field('room_loc','map',2,$group,array(),array('seating.php?c=[room_loc]&grid=0','map'));
	$x->add_field('userid','edit',2,$group,array(),array('admin_users.php?id=[userid]','edit'));
	$group++;
	// emails
	if (current_security_level() >= 2) {
		$x->add_field('email','email',2,$group,array(),array('mailto:[email]'));
	} else {
		$x->add_field('email','email',1,$group,array('display_email=1','users','userid'),array('mailto:[email]'));
	}
	$x->add_field('gender','gender',0,$group);
	$x->add_field('priv_level','admin',0,$group,array(),array(),array('&nbsp;','&nbsp;','admin','admin'));
	$x->add_field('userid','edit',2,$group,array(),array('admin_users.php?id=[userid]','edit'));
	$group++;
	if (current_security_level() >= 2) {
		// paid
		$x->add_field('paid','paid',2,$group,array(),array('admin_paid.php','X'));
		$group++;
	}
	if ($toggle['filesharing']) {
		// file sharing
		$x->add_field('sharename','sharename',0,$group);
		$x->add_field('recent_ip','connect',1,$group,array("display_ip=1 AND sharename!=''",'users','userid'),array('file:///[recent_ip]/','connect'));
		$x->add_field('userid','edit',2,$group,array(),array('admin_gamingrig.php?id=[userid]','edit'));
		$group++;
		// ftp server
		$x->add_field('ftp_server','ftp server',0,$group,array(),array(),array('no','yes'));
		$x->add_field('recent_ip','connect',1,$group,array('display_ip=1 AND ftp_server=1','users','userid'),array('ftp:///net:net@[recent_ip]/','connect'));
		$x->add_field('userid','edit',2,$group,array(),array('admin_gamingrig.php?id=[userid]','edit'));
		$group++;
	}
	if ($toggle['gamingrigs']) {
		// gaming rigs
		$x->add_field('comp_proc','cpu [brand]',0,$group);
		$x->add_field('comp_proc_type','cpu [type]',0,$group);
		$x->add_field('comp_proc_spd','cpu [speed MHz]',0,$group);
		$x->add_field('userid','edit',2,$group,array(),array('admin_gamingrig.php?id=[userid]','edit'));
		$group++;
		$x->add_field('comp_mem','memory [amount MB]',0,$group);
		$x->add_field('comp_mem_type','memory [type]',0,$group);
		$x->add_field('userid','edit',2,$group,array(),array('admin_gamingrig.php?id=[userid]','edit'));
		$group++;
		$x->add_field('comp_hdstorage','storage [amount GB]',0,$group);
		$x->add_field('comp_gfx_gpu','graphics',0,$group);
		$x->add_field('userid','edit',2,$group,array(),array('admin_gamingrig.php?id=[userid]','edit'));
		$group++;
	}
	// time of attendance
	$x->add_field('date_of_arrival','time of arrival',0,$group,array(),array(),array(),'h:i a - D d M Y');
	$x->add_field('date_of_departure','time of departure',0,$group,array(),array(),array(),'h:i a - D d M Y');
	$group++;
	// ip addresses
	if (current_security_level() >= 2) {
		$x->add_field('recent_ip','ip address',2,$group);
	} else {
		$x->add_field('recent_ip','ip address',1,$group,array('display_ip=1','users','userid'));
	}
}
$x->display_pagingfields($pager, $URL_handler);
?>
