<?php
// global language file.
// only change the text on the RIGHT...DO NOT CHANGE THE LEFT SIDE!!!!
// Also please retain any html tags or markup

$filename = substr(get_script_name(),strrpos(get_script_name(),"/")+1,strlen(get_script_name()));
@include "include/lang/".$master["currentlanguage"]."/".$filename;

$lang["global"] = array(
//General
"noauth" => "you are not authorized to view this page.",
"home" => "home",
"file" => "file",
"files" => "files",
"map" => "map",
"music" => "music",
"servers" => "servers",
"schedule" => "schedule",
"tournaments" => "tournaments",
"sponsors" => "sponsors",
"staff" => "staff",
"users" => "users",
"profile" => "profile",
"logout" => "logout",
"login" => "login",
"register" => "register",
"logedout_message" => "you are not logged in &gt; login",
"forgot" => "forgot your password?",
"admin" => "admin",
"administrator" => "administrator",
"sadministrator" => "super administrator",
"change_pw" => "change password",
//Left hand module stuff
"cpanel" => "control panel",
"admin_guides" => "administrator guides",
"register_account" => "register for an account",
"schedule_hour" => "schedule for the next hour",
"view_all" => "view all",
"bench_link" => "benchmarking competition",
"caffeine" => "caffeine",
"caffeine_log" => "caffeine log",
"marathon" => "the marathon",
"polls" => "polls",
"announcements" => "announcements",
//omfg were gonna need a giant error set :)
);

?>