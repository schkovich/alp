<?php
//English language array...have fun :)

$lang = array(
	"global" => array(
	//menu and breadcrumb (structuretree) text
	"home" => "home",
	"files" => "files",
	"map" => "map",
	"music" => "music",
	"servers" => "servers",
	"schedule" => "schedule",
	"tournaments" => "tournaments",
	"sponsors" => "sponsors",
	"staff" => "staff",
    "pizza" => "order pizzas",
	"shoutbox" => "shoutbox",
	"benchmarks" => "benchmarks",
	"tech_support" => "tech support",
	"restaurants" => "restaurants",
	"policy" => "policy",
	"toggle" => "toggle",
	"users" => "users",
	"profile" => "profile",
	"benchmarks" => "benchmarks",
	"benchmark_cheaters" => "benchmark cheaters",
	"caffeine" => "caffeine log",
	"caffeine_cheaters" => "caffeine cheaters",
	"caffeine_types" => "caffeine item types",
	"foodruns" => "food runs",
	"gamerequest" => "open play game request",
	"games" => "games",
	"gamingrig" => "gaming rigs",
	"settings" => "settings",
	"lodging" => "lodging",
	"messaging" => "messaging",
	"news" => "news",
	"paid" => "paid users",
	"polls" => "polls",
	"privileges" => "privileges",
	"prizes" => "prizes",
	"prizes_control" => "prize control panel",
	"prizes_print" => "print prize slips",
	"prizes_draw" => "draw prizes interactively",
	"profic" => "gamer proficiencies",
	"resetpassword" => "reset password",
	"restaurants" => "restaurants",
	"satellites" => "satellites",
	"schedule" => "schedule",
	"seeding" => "tournament seeding",
	"seeding_erase" => "erase tournament seeding",
	"tournament_servers" => "tournament servers",
	"tournament_teams" => "tournament teams",
	"tournament_teams_delete" => "delete tournament teams",
	"tournament_teams_type" => "tournament team types",
	"tournament_start" => "start tournament",
	"tournament_unstart" => "unstart tournament",
	"chng_benchmarks" => "update scores",
	"chng_gamingrig" => "update gaming rig",
	"chng_passwd" => "change password",
	"chng_prizes" => "prize registration",
	"chng_teams" => "update team information",
	"chng_userinfo" => "update profile",
	"chng_vote" => "submit vote",
	"credits" => "credits",
	"rules" => "display rules",
	"standings" => "standings",
	"teams" => "teams",
	"license" => "license",
	"maps" => "map voting",
	"themarathon" => "the marathon",
	"upload" => "upload files",
	"information" => "information",
	"chng_gamerequests" => "add request",
	"help" => "help",
	"checklists" => "checklists",
	"viewserver" => "view server",
	"chng_staff" => "fields",
	"techsupport_details" => "details",
	"techsupport_solve" => "solve",
	"modules" => "modules",
	
	//General
	"noauth" => "you are not authorized to view this page.",
	"file" => "file",
	"logout" => "logout",
	"login" => "login",
	"log in" => "log in",
	"register" => "register",
	"password" => "password",
	"username" => "username",
	"logedout_message" => "you are not logged in &gt; login",
	"forgot" => "forgot your password?",
	"admin" => "admin",
    "note" => "note",
	"administrator" => "administrator",
	"sadministrator" => "super administrator",
    "user" => "normal user",
    "guest" => "guest",
	"change_pw" => "change password",
	"yes" => "yes",
	"no" => "no",
	"success" => "success",
	"error_unknown" => "unknown error!",
	//Left hand module stuff
 		//cpanel	
	"cpanel" => "control panel",
	"cp_cuser" => "client username",
	"cp_cip" => "client ip address",
	"food" => "food",
	"gr" => "game request",
	"prizes" => "prizes",
	"going_for" => "going for",
	"reg_for" => "register for",
    "want_pizza" => "want some <strong>pizza</strong>",
	"open_play" => "open play",
	"cp_lodge" => "local lodging",
	"cp_register" => "need an account? register here",
	"cp_security" => "&nbsp;for maximum security, enable javascript.<br />&nbsp;requires cookies.<br />",
	"cp_other" => "other links",
	
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
    // links (related links text)
    "link_admin_tournament" => "tournaments",
    "link_admin_teams" => "manage teams",
    "link_disp_teams" => "list teams",
    "link_admin_teams_delete" => "delete teams",
    "link_admin_seeding" => "seed teams",
    "link_admin_seeding_erase" => "erase seeding",
    "link_admin_tournament_start" => "start tournaments",
    "link_admin_tournament_unstart" => "unstart tournaments",
    "link_admin_prizes" => "setup prizes",
    "link_admin_prize_control" => "prize management",
    "link_admin_prizes_print" => "print prize draw slips",
    "link_admin_prizes_draw" => "draw prizes interactively",
    "link_chng_prizes" => "register for prizes.",
    "link_disp_prizes" => "view all prizes.",
    "link_gamerequest" => "view all game requests",
    "link_admin_mapvoting" => "map voting poll",
    "link_admin_pizza" => "add/modify pizza orders",
    "link_admin_pizza_list" => "pizza orders summary",
    "link_chng_pizza" => "add/modify pizzas",
    "link_pizza" => "add/modify pizza orders",
    "link_pizza_list" => "detailed pizza descriptions and costs",
	),
	
	"admin_benchmark_cheaters" => array(
	"plural" => "benchmark cheaters",
	"singular" => "reset cheater",
	"notes_update" => "reset a user's benchmarks if they are suspected of being false.  this is irreversible, so be careful.",
	"desc_userid" => "username",
	"error_userid" => "you forgot to select the cheater's username.",
	"noauth" => "you are not authorized to view this page.",
	),
	
	"admin_benchmarks" => array(
	"plural" => "benchmarks",
	"singular" => "benchmark",
	"notes_add" => "add or modify benchmarks available to the user.  keep in mind that all benchmarks listed as part of the composite score will be required for the overall global benchmark winner.",
	"desc_name" => "benchmark name",
	"error_name" => "you forgot to enter a benchmark name!",
	"desc_abbreviation" => "benchmark abbrevation",
	"desc_composite" => "part of global composite score?",
	"desc_deflate" => "percentage to deflate score when adding to composite.",
	),
	
	"admin_caffeine_cheaters" => array(
	"plural" => "caffeine cheaters",
	"singular" => "reset cheater",
	"notes_update" => "reset a user's caffeine count if they are suspected of cheating.  this is irreversible, so be careful.",
	"desc_userid" => "cheater's username",
	"error_userid" => "you forgot to select the cheater's username.",
	),
	
	"admin_caffeine_types" => array(
	"plural" => "caffeine types",
	"singular" => "caffeine type",
	"notes_add" => "add or modify caffeine types available to the user.",
	"desc_name" => "caffeine type name",
	"error_name" => "you forgot to enter a caffeine type name!",
	),
	
	"admin_caffeine" => array(
	"plural" => "caffeine items",
	"singular" => "caffeine",
	"notes_add" => "add or modify caffeine items available to the user.",
	"desc_name" => "caffeine item name",
	"error_name" => "you forgot to enter a caffeine item name!",
	"desc_caffeine_permg" => "amount of caffeine per milligram (up to 10 decimal places)",
	"error_caffeine_permg" => "to calculate caffeine content, you must input the amount of caffeine per milligram in the substance",
	"desc_ttype" => "type of substance",
	"descother_ttype" => "add more types",
	),
	
	"admin_checklists" => array(
	"plural" => "checklists",
	"tourney_step1" => "enter the tournament into the database.",
	"tourney_step2" => "input the teams for the tournament.",
	"tourney_step3" => "view the teams to make sure they are correct.",
	"tourney_step4" => "delete any teams you don't want.",
	"tourney_step5" => "seed any teams you wish OR ",
	"tourney_step6" => "once you are happy with the teams, start the tournament. (this will lock the teams and create the brackets)",
	"tourney_step7" => "promote winners by clicking on the winning team name or inputting the scores and clicking the [+] button.",
	"tourney_step8" => "did you screw up the tournament? (be careful with this) you can erase all the brackets and modify the teams in the tournament by un-starting the tournament.  <b>this will erase all scores, matches, and brackets, but not teams entered into the tournament.</b>",
	"prizes_step1" => "enter the prizes into the database.",
	"prizes_step2" => "time: allow sufficient time for users to register for prizes (ideally you'd wait until the end of the LAN)",
	"prizes_step3" => "lock the prizes so that you can begin prize drawings.  this will not allow you to add any more prizes, will not allow users to change registration of prizes, and is not reversable after you've drawn a prize.  be careful with this step.",
	"prizes_step4" => "draw prizes: there are two methods to prize drawing,  if you're using slips of paper, you're done, don't read on",
	"prizes_step5" => "once you're finished drawing a group of prizes, you must display prize winners to everyone.  this is optional if you decide to just read the prizes over a P.A. system.",
	"prizes_step6" => "if someone leaves and isn't there to claim a prize, you can redraw the prize, but this will place them on an absentee blacklist.  you can remove them from the absentee blacklist.",
	),
	
	"admin_config" => array(
	"plural" => "settings",
	"singular" => "setting",
	"desc_name" => "event name",
	"error_name" => "you forgot to enter an event name!",
	"desc_org" => "organization hosting event",
	"error_org" => "you forgot to enter your organization name!",
	"desc_location" => "event location (ex. Dallas, Texas)",
	"error_location" => "you forgot to enter your event location!",
	"desc_country" => "select country",
	"error_country" => "you did not select a country!",
	"desc_max" => "maximum number of seats for attendees",
	"error_max" => "you forgot to enter maximum number of seats!",
	"desc_datetimestart" => "event start time",
	"desc_datetimeend" => "event finish time",
	"desc_email" => "admin email address",
	"error_email" => "you forgot to enter the admins email address!",
	"desc_websiteurl" => "web site address (enter the url of the internet web site for this event)",
	"error_websiteurl" => "you forgot to enter the event web site address!",
	"desc_alp_tournament_mode" => "alp tournament mode only",
	"desc_alp_tournament_mode_computer_games" => "alp tournament mode style",
	"mode_games" => "for computer games",
	"mode_sports" => "for anything else (sports)",
	
	),
	
	//is this legal?
	"admin_deleteuser" => array(
	"plural" => "delete users",
	"singular" => "delete user",
	"notes_update" => "deletes a user, including all associated info.  will leave any tournament entries, but they will appear blank.  this is not reversible, so be careful.  super administrators are not shown here, you must first demote a user to administrator or normal user status to delete them.",
	//tournament...how to implement?"notes_update" => "deletes a user.  this is not reversible, so be careful.  super administrators are not shown here, you must first demote a user to administrator or normal user status to delete them.",
	"desc_userid" => "username",
	"error_userid" => "you forgot to select a username.",
	),
	
	"admin_disp_scores" => array(
	"plural" => "user submitted scores",
	"singular" => "",
	"teamname" => "team name",
	"id" => "id",
	),
	
	"admin_foodrun" => array(
	"plural" => "food runs",
	"singular" => "food run",
	"notes_del" => "this feature is currently toggled off and users are not allowed to add food runs.  if you wish to change its status, you can <a href=\"admin_toggle.php\">toggle it on</a>.",
	"desc_userid" => "posted by",
	"desc_datetime_leaving" => "time of departure",
	"error_datetime_leaving" => "you forgot to enter a time of departure!",
	"desc_headline" => "destination",
	"error_headline" => "you didn't say where you're going!",
	),

	"admin_gamerequest" => array(
	"plural" => "open play game requests",
	"singular" => "game request",
	"desc_gameid" => "game",
	"desc_userid" => "user",
	"desc_gamename" => "other game",
	"desc_ipaddress" => "ip address and game port -- both are required (ie: 10.0.0.2:27015)",
	"desc_queryport" => "query port (if not default) (ie: 7778)",
	"desc_itemtime" => "item time",
	),
	
	"admin_games" => array(
	"plural" => "games",
	"singular" => "game",
	"notes_add" => "list all games eligible for tournaments or otherwise.  if you are going to play a game in a tournament, you must list it here.",
	"desc_name" => "game name",
	"error_name" => "you forgot to enter a game name!",
	"desc_current_version" => "current version",
	"desc_url_update" => "relative or absolute url to game updates",
	"desc_url_maps" => "relative or absolute url to map directory or a map pack",
	),
	
	"admin_gamingrig" => array(
	"plural" => "gaming rig details",
	"singular" => "gaming rig",
	"notes_mod" => "modify a user's gaming rig information.",
	"desc_ms_sharename" => "microsoft share name",
	"desc_ms_workgroup" => "microsoft workgroup name",
	"desc_ftp_server" => "have an ftp server?",
	"desc_comp_proc" => "gaming rig cpu (brand name and processor speed)",
	"desc_comp_mem" => "gaming rig memory (amount, in mb, and type)",
	"desc_comp_hdstorage" => "gaming rig storage (amount, in gb, and number of drives)",
	"desc_comp_gfx" => "gaming rig graphics (amount of memory and chipset)",
	),
	
	"admin_generic" => array(
		"plural" => "generic",
		"singular" => "generic",
		"incorrect" => "incorrect usage.",
		),
	
	"admin_index" => array(
		"plural" => "more settings",
		"singular" => "setting",
		"str_misc" => "misc module options",
		"str_staff" => "staff module options",
		"str_sponsors" => "sponsor module options",
		"str_music" => "music module options",
		"str_ts" => "teamspeak module options",
		"desc_internetmode" => "allow to reset forgotten passwords through e-mail -- requires php mail() function and internet)",
		"desc_currentlanguage" => "current language",
		"error_currentlanguage" => "you must have a language selected!",
		"desc_currentskin" => "alp skin",
		"error_currentskin" => "you must select a skin!",
		"desc_skin_override" => "force users to use the default theme?",
		"desc_dateformat" => "alp date format",
		"error_dateformat" => "you must select a date format!",
		"desc_ip_register_lock" => "allow only one registration per ip address?",
		"desc_useskinforcaffeine" => "use the current skin for the caffenine page instead of the default caffeine page skin.",
		"desc_loginselect" => "make username a select list instead of a text box (for login form)",
		"desc_proficiencylock" => "lock out user modification of their own proficiency rating?",
		"desc_alldates" => "allow dates and times not within the time frame of the event?",
		"desc_pollsguest" => "allow guest accounts to see poll results",
		"desc_max_file_upload_size" => "maximum file size for attendee uploaded files (in bytes) -- conversion: (value/2^20 = MB) or (MB * 2^20 = value)",
		"desc_policyurl" => "url to waiver form",
		"desc_files_redirect" => "redirect files link (ie. ftp://webserver/files or http://webserver/files)",
		"desc_techsupport_index_limit" => "how many techsupport requests to display on the portal page? (0 = no limit)",
		"desc_shoutbox_index_limit" => "how many shoutbox posts to display on the portal page? (0 = no limit)",
		"desc_gamerhour" => "gamer of the hour benefit",
		"desc_important_info" => "important info content (module may be turned on and off in the module management)",
        "desc_pizza_orders_lock" => "lock pizza orders? (will stop users from adding/modifying orders)",
		"desc_staff_photo_url" => "relative base url to photo images used in staff page(no trailing slash)<br />(leave blank to disable photos)",
		"desc_staff_photo_width" => "width of photos on staff page (controls width of left column in staff tables also)",
		"desc_sponsors_index_limit" => "how many sponsor images to display on the portal page? (0 indicates no limit)",
		"desc_sponsors_width" => "width in pixels of all images displayed on the portal page",
		"desc_sponsors_banner_width" => "width in pixels of all banner images displayed on the sponsors page",
		"desc_sponsors_border" => "turn on borders around sponsor images (both portal and sponsors list pages)",
		"desc_music_files" => "full disk path to writable location of mp3s (eg. d:/music or /home/music)",
		"desc_music_min_time" => "minimum time (minutes) between song requests, 0 to disable",
		"desc_music_max_queue" => "maximum number of queued songs including currently playing song",
		"desc_music_stream_id" => "current music stream ID",
		"desc_voice_mode" => "type of voice server you are running",
		"desc_voice_name" => "name of voice server",
		"desc_voice_ip" => "ip of voice server with port (i.e. 7.23.96.100:11234)",
		"desc_voice_pass" => "password of voice server, blank for none",
		),
	
	"admin_lockteams" => array(
		"plural" => "lock teams creation and joining",
		"singular" => "",
		"error_tournament" => "specify the tournament",
		"locked" => "teams locked.",
		"unlocked" => "teams unlocked.",
		"error_tournament_bad" => 'tournament doesn\'t exist.',
		"incorrect" => "incorrect usage.",
		),
		
	"admin_lodging" => array(
		"plural" => "places of lodging",
		"singular" => "lodging",
		"notes_add" => "area places the gamer might find a good nights rest if they so wish.",
		"desc_name" => "business name",
		"error_name" => "you forgot to enter a business name",
		"desc_address" => "business address",
		"desc_phone" => "business phone number",
		"desc_costpernight" => "cost per night (it's a number, not text, so leave off the label)",
		"desc_traveltime" => "travel time to and from business (it's text, not a number, so don't forget a label)",
		),

	"admin_modules" => array(
		"title" => "module config",
		"add_notes" => "This allows you to move modules up and down as well as disable/enable them them. Just select a module and click the button to move it around<br>NOTE: This still requires the corresponding toggle (if any) to be on",
		"enable" => "enable",
		"enabled" => "enabled",
		"up" => " move up ",
		"down" => "move down",
		"disable" => "disable",
		"disabled" => "disabled",
		),
        
    "admin_news" => array(
		"plural" => "news",
		"singular" => "news item",
		"notes_add" => "news postings are displayed on the portal page to ALP.  it's commonly used for LAN announcements.  the announcement text area will display the following html tags: &lt;a&gt; &lt;b&gt; &lt;i&gt; &lt;u&gt; &lt;font&gt; &lt;img&gt; &lt;strong&gt;",
		"desc_headline" => "news headline",
		"desc_userid" => "posted by",
		"error_headline" => "you forgot to enter a news headline!",
		"desc_itemtime" => "date and time of item",
		"desc_news_article" => "news announcement",
		"error_news_article" => "you forget to enter a news announcement!",
		"desc_hide_item" => "hide this news item?",
		),
    
    "admin_paid" => array(
		"plural" => "paid gamers",
		"singular" => "paid gamer",
		"notes_mod" => "if a gamer has paid, check his box.",
		),

    "admin_pizza" => array(
        "plural" => "pizza orders",
        "singular" => "pizza order",
        "desc_pizzaid" => "pizza",
        "error_pizzaid" => "you forgot to select a pizza",
        "desc_quantity" => "quantity",
        "error_quantity" => "you forgot to enter a quantity!",
        "desc_delivered" => "is delivered?",
        "desc_paid" => "is paid for?",
        "desc_userid" => "ordered by",
        "error_userid" => "you forgot to select a user",
        ),
        
    "admin_pizza_list" => array(
        "plural" => "pizza orders summaries",
        "singular" => "pizza orders summary",
        "tr_pizza" => "Pizza",
        "tr_numorder" => "No. to Arrive/Order",
        "tr_numdelivered" => "No. Delivered",
        "tr_numpaid" => "No. Paid for",
        "tr_remain" => "Cost Remaining",
        "tr_total" => "TOTALS",
        "tr_username" => "Username",
        "tr_quantity" => "Quantity",
        "tr_paid" => "Paid for",
        "tr_delivered" => "Delivered",
        "detail" => "in detail",
        ),    
    
    "admin_poll" => array(
		"plural" => "polls",
		"singular" => "poll",
		"notes_add" => "an abstain (view results) option will be automatically added to each poll and it is unnecessary to list one below.  the active poll is the poll displayed on the sidebar.",
		"desc_headline" => "poll question",
		"error_headline" => "you forgot to enter a poll question!",
		"desc_activepoll" => "set as the active poll?",
		"error_choice" => "you need to put at least 2 poll choices!",
		"desc_choice" => "choice ",
		),
    
    "admin_priv" => array(
		"plural" => "privilege levels",
		"singular" => "privilege level",
		"notes_mod" => "promote or demote users to or from administrator level access.  the administrator name listed in the config file is immune to this page.",
		),
    
    "admin_prize_control" => array(
		"plural" => "prizes",
		"singular" => "prizes",
		"title_cp" => "prize control panel",
		"note_lock" => " Locking the prizes is an irreversible action. Before you lock prizes, make sure that you've <a href=\"admin_prizes.php\" border=\"0\">entered
                all of the prizes</a> that are to be drawn for or won in tournaments. <b>Once prizes are locked, no more prizes can be added, and users cannot change the
                prizes they've registered for</b>",
		"note_locked" => "prizes have been locked.",
		"note_locked_drawn" => "they cannot be unlocked, because one or more prizes have been drawn.",
		"note_locked_undrawn" => "however, since no prizes have been drawn yet, you can still unlock prizes.",
		"submit_unlock" => "Unlock Prize Registration",
		"title_winners" => "Display Winners",
		"note_winners" => "(prize winners will not be displayed until the group of prizes is checked below.  an entire group must be drawn before it appears here):",
		"group" => "Group",
		"prizegroup" => "Prize Group ",
        // prizes_drawn: other languages may need to change the order the two values appear
        // to do so use argument swapping: "only %2\$s out of %1\$s prizes have been drawn."
        "prizes_drawn" => "only %s out of %s prizes have been drawn.",
        "submit_settings" => "update settings",
        "title_absentees" => "Absentees",
        "note_absentees" => "When you redraw a prize, the interactive prize drawing script assumes the person has left the LAN and makes them ineligible for any future prizes.  If you want to reinstate a users eligibility for prizes, do so below:",
        "select_user" => "select user",
        "submit_absentees" => "Remove from Absentees",
        "note_unlocked" => "In order to access the prize drawing features, prize registration must be locked. If you lock prizes, you cannot unlock prizes once any prize has been drawn for.", 
        "lockprize_sure" => "ARE YOU SURE??",
        "submit_lockprize" => "Lock Prize Registration",
        "error_noprizes" => "there are no prizes in the database.",
        "error_disabled" => "the administrator has disabled prizes for this LAN."
		),
    "admin_prize_draw" => array(
    	"plural" => "prizes",
    	"singular" => "prize",
    	"draw_prizes" => "draw prizes interactively",
    	"absentees" => "there are users on the absentee list, do you want to mark them as returned?",
    	"random_prizes" => "Random Prizes",
    	"col_name" => "name",
    	"col_value" => "value",
    	"col_winner" => "winner",
    	"prize_group" => "prize group",
    	"submit_draw_group" => "Draw Group",
    	"quantity" => "quantity",
    	"image" => "image",
        "eligible" => "eligible",
        "submit_draw_all" => "draw all",
        "claimed" => "claimed",
        "submit_unclaim" => "Unclaim",
        "submit_claim" => "claim",
        "submit_redraw" => "redraw",
        "submit_draw" => "draw",
        "tournament_prizes" => "Tournament Prizes",
        "col_tournament" => "tournament",
        "col_winners" => "winner(s)",
        "submit_draw_instead" => "draw prize instead",
        "error_must_be_locked" => "prizes must be locked in order to draw prizes",
        "error_no_prizes" => "there are no prizes in the database.",
        "error_prizes_disabled" => "the administrator has disabled prizes for this LAN.",
    	),
    "admin_prizes_print" => array(
		"plural" => "prize registration cutup drawings",
		),
    "admin_prizes" => array(
		"plural" => "prizes",
		"singular" => "prize",
		"notes_add" => 'list the prizes that you have available to give away here.  if the prize is to be given away in a tournament, list the tournament and the placing.  if you have more than one of a prize and wish to give them out to different tournaments, please list them seperately.',
		"desc_prizename" => "prize name",
		"error_prizename" => "you forgot to enter a prize name!",
		"desc_prizequantity" => "quantity of prize available (numeric input only)",
		"error_prizequantity" => "how many of this prize are available?",
		"desc_prizevalue" => "value of prize in ".MONEY_SYMBOL." (numeric input only)",
		"desc_prizepicture" => "picture of prize (url can be relative or absolute)",
		"desc_prizegroup" => "drawing group of prize (numeric input only)",
		"desc_tourneyid" => "given away for tournament?",
		"desc_tourneyplace" => "placing in tournament required to win prize",
        "error_locked" => "prizes cannot be changed because prizes have been locked.",
		),
    "admin_profic" => array(
		"plural" => 'gamer proficiencies',
		"singular" => "gamer proficiency",
		"notes_mod" => 'modify the gamer proficiences that you feel to be incorrect before you start a random tournament to ensure the fairest teams.',
		),
    "admin_resetpassword" => array(
		"plural" => "reset password",
		"singular" => "",
		"title" => "reset password",
		"username" => "username",
		"newpass" => "new password",
		"confirm" => "confirm",
		"submit_reset" => "reset",
		"error_username" => "the username field is blank.",
		"error_new_passwd" => 'the new_passwd field is blank.',
		"error_new_passwd_confirm" => 'the new_passwd_confirm field is blank.',
		"error_new_passwd_same" => 'your new_passwd does not match the new_passwd_confirm field.',
		"update_success" => "the user's password has been successfully updated.",
        "update_another" => "change another user's password",
        "update_error" => "there has been an error updating that user's password.  it has _not_ been updated.",
		),

    "admin_restaurant" => array(
		"plural" => "restaurants",
		"singular" => "restaurant",
		"notes_add" => 'restaurants are listed as being available under food runs.',
		"desc_name" => 'restaurant name',
		"error_name" => 'you forgot to enter a restaurant name!',
		"desc_address" => 'address',
		"desc_city" => 'city',
		"desc_state" => 'state',
		"desc_zipcode" => 'zip code',
		"desc_phone" => 'phone number',
		"desc_traveltime" => 'travel time from event (text input, don\'t forget a time label)',
		"desc_delivery" => 'possible delivery to event?',
		),
        
    "admin_schedule" => array(
		"plural" => "schedule",
		"singular" => "schedule item",
		"desc_itemtime" => 'date and time',
		"error_itemtime" => 'you forgot to enter a date and time!',
		"desc_itemtime_priv" => 'minimum security level to view',
		"desc_headline" => 'name',
		"error_headline" => 'you forgot to enter the item name!',
		),

    "admin_seeding_erase" => array(
		"plural" => "erase seedings",
		"singular" => "erase seed",
		"notes_update" => 'to completely delete all seeds from a tournament, select it below.',
		"desc_tourneyid" => 'tournament name',
		"error_tourneyid" => 'don\'t forget to select a tournament.',
		),
        
    "chng_pizza" => array(
        "plural" => "pizzas",
        "singular" => "pizza",
        "notes_add" => "pizzas are listed as available under pizza orders when foodruns are enabled",
        "desc_pizza" => "pizza name",
        "error_pizza" => "you forgot to enter a pizza name!",
        "desc_description" => "description",
        "desc_price" => "price",
        "desc_enabled" => "enabled",
        ),
    
    "login" => array(
    	"singular" => "log into your account",
    	"al_loged" => "you are already logged in",
    	"security_long" => "for maximum security, enable javascript. &nbsp;do not be alarmed, your password will autoencrypt when you click the register button. &nbsp;this is to prevent sniffing of passwords on the local network.",
    	"bad_ip" => "you could not be authenticated because your ip address was incorrect.  if your DHCP ip address changed, blame the administrator.",
    	"bad_user" => "that username is not in our database.",
    	"bad_pass" => "your password is incorrect.  remember, passwords are case sensitive.",
		"blank_user" => "the username field is blank.",
    	"blank_pass" => "the password field is blank.",
    ),
        
    "pizza_list" => array(
        "plural" => "detailed pizza list",
        "singular" => "detailed pizza list",
        "tr_pizza" => "Pizza Type",
        "tr_description" => "Description",
        "tr_price" => "Price",
        ),
        
    "pizza" => array(
        "plural" => "pizza orders",
        "singular" => "pizza order",
        "notes_add" => "Pizzas will be ordered at the time specified on the schedule and should start arriving a little later as indicated in the schedule, see an admin if you have questions or to pay for your order.",
        "desc_pizzaid" => "pizza toppings",
        "error_pizzaid" => "the pizza type is required",
        "desc_quantity" => "number of pizzas",
        "error_quantity" => "the quantity field is required",
        "desc_userid" => "ordered by",
        "pizzas_locked" => "pizza orders are now locked, you cannot add or modify your orders at this time.",
        ),
    
);











//Installer stuff...old format
$lang["install"]['install'] = 'install ALP';
$lang["install"]['success'] = 'success!';
$lang["install"]['failure'] = 'failure.';
$lang["install"]['on'] = 'on';
$lang["install"]['off'] = 'off';
$lang["install"]['nolongerrequired'] = 'no longer required, it is ok if on.';
$lang["install"]['optional'] = 'optional, but recommended.';
$lang["install"]['start'] = 'start';
$lang["install"]['end'] = 'end';
$lang["install"]['errors'] = 'ERRORS';
$lang["install"]['warning'] = 'WARNING!';
$lang["install"]['and'] = 'and';
	
$lang["install"]['stepone'] = 'step one of five';
$lang["install"]['stepone_passed'] = '<strong>Database test: </strong>Passed';
//$lang["install"]['stepone_description'] = 'edit the _config.php file located in the / directory of ALP.  edit the variables to describe your lan party.';
$lang["install"]['stepone_description'] = 'configure database settings - please enter your SQL server database settings below.';
$lang["install"]['stepone_next'] = 'move on to step two - validate _config.php and your php.ini';
$lang["install"]['stepone_repeat'] = 'change database settings';
	
$lang["install"]['steptwo'] = 'step two of five';
$lang["install"]['steptwo_varone'] = 'lan name';
$lang["install"]['steptwo_varone_error'] = "the name of your party cannot be empty.";
$lang["install"]['steptwo_vartwo'] = "gaming group name";
$lang["install"]['steptwo_vartwo_error'] = "the name of your gaming group cannot be empty.";
$lang["install"]['steptwo_varthree'] = "max attendees";
$lang["install"]['steptwo_varthree_error'] = "the number of maximum gamers must be greater than zero.";
$lang["install"]['steptwo_varfour'] = "super admin username";
$lang["install"]['steptwo_varfour_error'] = "the name of your super administrator account cannot be empty.";
$lang["install"]['steptwo_varfive'] = "mysql connection info";
$lang["install"]['steptwo_varfive_error'] = "your mysql connection information is incorrect.";
$lang["install"]['steptwo_varsix'] = "php.ini variable (magic_quotes_gpc)";
$lang["install"]['steptwo_varsix_error'] = "edit your php.ini to have magic_quotes_gpc to be on.  if you're unsure on how to do this; consult google or look on the support forums.";
$lang["install"]['steptwo_varseven'] = "default language";
$lang["install"]['steptwo_varseven_error'] = "you're missing a default language or the language you specified is not included in the ALP files.";
$lang["install"]['steptwo_vareight'] = "php.ini variable (short_open_tag)";
$lang["install"]['steptwo_vareight_error'] = "edit your php.ini to have short_open_tags to be on.  if you're unsure on how to do this; consult google or look on the support forums.";
$lang["install"]['steptwo_varnine'] = "php.ini variable (register_globals)";
$lang["install"]['satellitenotes'] = "other notes (ALP satellite):";
$lang["install"]['satellitenotes_valone'] = "the domain name of your web server must be alp. (ie: http://alp/ is the address set through DNS; not windows WINS).";
$lang["install"]['satellitenotes_valtwo'] = "your php must have the ftp and the secure sockets extensions enabled.<br />(Ignore this if you are not planning to use ALP Satellites)";
$lang["install"]['steptwo_varten'] = "start/stop dates";
$lang["install"]['steptwo_varten_error'] = "the end date of your lan must be after the starting date.";
$lang["install"]['steptwo_vareleven'] = "php.ini variable (error reporting)";
$lang["install"]['steptwo_vareleven_error'] = "not the default value (E_ALL & ~E_NOTICE) or (2039).<br />&nbsp;&nbsp;&nbsp;if your value is more strict; ALP will give you errors.";
$lang["install"]['steptwo_vartwelve'] = "mysql database";
$lang["install"]['steptwo_vartwelve_error'] = "the mysql database name does not currently exist.<br />&nbsp;&nbsp;&nbsp;if you continue; and it still doesn't exist in step four; it <br />&nbsp;&nbsp;&nbsp;will be created automatically.";
	
$lang["install"]['steptwo_next'] = "move on to step three - setting up the mysql table structure";
$lang["install"]['steptwo_redo'] = "make the necessary modifications to the _config.php file and refresh this page.";
	
$lang["install"]["stepthree"] = "step three of five";
$lang["install"]["stepthree_warning"] = "Continuing will delete all existing tables of ALP data.  If you have a previous install of ALP that you wish to save; please back up your database.  This script will replace those tables with empty ones.  Due to the vast changes made from the previous release; there is no upgrade script.  Sorry.";
$lang["install"]["stepthree_doublewarning"] = "YOU HAVE BEEN WARNED!!!";
$lang["install"]["stepthree_tournamentmodetitle"] = "tournament only mode";
$lang["install"]["stepthree_tournamentmode"] = "tournament mode will automatically configure the ALP database with your intention to use ALP for tournaments only.  it will automatically disable all the extra unnecessary features.  these features can be re-enabled later. alp tournaments is for computer game tournament, alp sports tournament is for any other type of tournaments (football, pool, basketball, etc.)";
$lang["install"]["stepthree_next_choice1"] = "move on to step four - Full Version - creating the mysql table structure";
$lang["install"]["stepthree_next_choice2"] = "move on to step four - ALP in tournament mode only - creating the mysql table structure";
$lang["install"]["stepthree_next_choice3"] = "move on to step four - ALP in sports tournament mode only - creating the mysql table structure";
	
$lang["install"]["stepfour"] = "step four of five";
$lang["install"]["stepfour_creatingdatabase"] = "creating the ALP database";
$lang["install"]["stepfour_newtable"] = "creating new table";
$lang["install"]["stepfour_defaultvalues"] = "inserting default values into";
$lang["install"]["stepfour_success"] = "table structure creation successful";
$lang["install"]["stepfour_warning"] = "make sure you delete the install.php file before using the script live";
$lang["install"]["stepfour_next"] = "move on to step five - register the super admin account";
$lang["install"]["stepfour_redo"] = "there has been an unexpected error.  refresh this page to try again.";
	
$lang["install"]['coffee']    = 'coffee';
$lang["install"]['softdrink'] = 'soft drink';
$lang["install"]['tea']       = 'tea';
$lang["install"]['other']     = 'other';
?>
