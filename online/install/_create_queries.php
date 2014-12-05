<?php
if(file_exists('DISABLED')) { echo 'install has been disabled because it has already been run.<br />If you wish to run it again, please delete the file /install/DISABLED'; exit();}
	$create_table_queries = array(
        "tempusers" => "userid BIGINT NOT NULL AUTO_INCREMENT, 
            username varchar(40) NOT NULL, 
            first_name varchar(30) NOT NULL, 
            last_name varchar(30) NOT NULL, 
            passwd varchar(34) NOT NULL, 
            paid BOOL DEFAULT '0',
            email varchar(60) NOT NULL,
            language varchar(10) NOT NULL,
            skin varchar(45) NOT NULL,
            dateformat varchar(45) NOT NULL, 
            display_email BOOL NOT NULL, 
            gender varchar(10) NOT NULL, 
            gaming_group varchar(20) NOT NULL, 
            quote varchar(255) NULL,
            room_loc int(11) DEFAULT '0', 
            caffeine_mg double(10,2) DEFAULT '0', 
            proficiency int(4) NOT NULL, 
            recent_ip varchar(15) NOT NULL, 
            display_ip BOOL NOT NULL, 
            priv_level int(5) NOT NULL, 
            sesid varchar(34) NULL, 
            date_of_arrival datetime NOT NULL,  
            date_of_departure datetime NULL,
            sharename varchar(35) NULL,
            ftp_server BOOL DEFAULT '0', 
            comp_proc varchar(60) NULL,
            comp_proc_spd varchar(60) NULL,
            comp_proc_type varchar(60) NULL,
            comp_mem varchar(60) NULL,
            comp_mem_type varchar(60) NULL,
            comp_hdstorage varchar(60) NULL, 
            comp_gfx_gpu varchar(60) NULL,
            comp_gfx_type varchar(60) NULL,
            ccode varchar(34) NULL,
            marathon_points int(5) DEFAULT '0',
            marathon_points_tourney int(10) DEFAULT '0',
            marathon_rank int(10) DEFAULT '0',
            PRIMARY KEY (userid)",
		"settings" => "`pw` text NOT NULL,
			  `group` text NOT NULL,
			  `event` text NOT NULL,
			  `start_date` varchar(10) NOT NULL default '0000-00-00',
			  `end_date` varchar(10) NOT NULL default '0000-00-00',
			  `start_time` time NOT NULL default '00:00:00',
			  `end_time` time NOT NULL default '00:00:00',
			  `admin_email` text NOT NULL,
			  `send_mail` tinyint(1) NOT NULL default '0',
			  `max_attendance` smallint(6) NOT NULL default '0',
			  `paypal_account` text NOT NULL,
			  `attendance_price_door` decimal(10,0) NOT NULL default '0',
			  `attendance_price_online` decimal(10,0) NOT NULL default '0',
			  `prepay_toggle` tinyint(1) NOT NULL default '0',
			  `bleh` text NOT NULL"
		);

?>