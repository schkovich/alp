<?php
$lang["plural"] = "delete users";
$lang["singular"] = "delete user";
if(!ALP_TOURNAMENT_MODE) $lang["notes_update"] = "deletes a user, including all poll votes and prize registration entries.  will leave any tournament entries, but they will appear blank.  this is not reversible, so be careful.  super administrators are not shown here, you must first demote a user to administrator or normal user status to delete them.";
else $lang["notes_update"] = "deletes a user.  this is not reversible, so be careful.  super administrators are not shown here, you must first demote a user to administrator or normal user status to delete them.";
$lang["desc_userid"] = "username";
$lang["error_userid"] = "you forgot to select a username.";
?>