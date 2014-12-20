<?php
if($userinfo['skin'] AND !$master['skin_override'])
	$master['currentskin'] = $userinfo['skin'];

include_once $master['currentskin'].'_config.inc.php';

include_once 'include/cl_form.php';
include_once 'include/cl_URL_handler.php';

include_once 'include/tournaments/tournament_types.php';
?>