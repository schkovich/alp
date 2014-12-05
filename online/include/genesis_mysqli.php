<?php
//Genesis PHP Class 0.99.4
//Written By: Travis Kreikemeier
//Database abstraction layer, login code, plus more.

DEFINE('CONFIG_LOCATION', '_genesis.php');

class genesis {

	var $database	= array();
	var $auth		= array();
	var $settings	= array();
	var $db;
	var $query_result;
	
	//Constructor
	function genesis() {

		require_once(CONFIG_LOCATION);
		$this->database['number_of_queries'] = 0;
		$this->auth['secure'] = 0;
	}
	
	//Get'rs
	function getDatabase()			{ return $this->database; }
	function getNumberOfQueries()	{ return $this->database['number_of_queries']; }
	function getAuth()				{ return $this->auth; }
	function isSecure()				{ return $this->auth['secure']; }
	function getSettings()			{ return $this->settings; }

	//Public functions ****************************
	
	function database_connect_select($server, $user, $passwd, $database, $connect_error='', $select_error='') {
		$this->database['server']	= $server;
		$this->database['user']		= $user;
		$this->database['passwd']	= $passwd;
		$this->database['database']	= $database;
		
		if (!@$this->database_connect($this->database['server'], $this->database['user'], $this->database['passwd'])) {
			if(!$connect_error) {$connect_error='Could not connect to database';}
			$this->error($connect_error);
			exit();
		}
		if (!@$this->database_select_db($this->database['database'])) {
			if(!$select_error) {$select_error='Unable to access selection on database';}
			$this->error($select_error);
			exit();
		} else {
			$this->database['connected'] = true;
		}
		return $this->db;
	}
	
	function database_connect($server, $user, $passwd) {
		return $this->db = @mysqli_connect($server, $user, $passwd);
	}
	
	function database_select_db($database) {
		return mysqli_select_db($this->db, $database);
	}
	
	function database_data_seek($result, $row) {
		return mysqli_data_seek($result, $row);
	}
	
	function database_error() {
		return mysqli_error($this->db);
	}
	
	function database_errno() {
		return mysqli_errno($this->db);
	}
	
	function database_query($query) {
		if ($this->database['debug']) {
			if($this->database['debug'] == 2) {print "Query: $query<br />";}
			$result = mysqli_query($this->db, $query);
		} else {
		$result = @mysqli_query($this->db, $query);
		}
		if (!$result) {
			$this->error('Error in SQL Query');
		}
		++$this->database['number_of_queries'];
		return $result;
	}
	
	function database_fetch_array($result) {
		return mysqli_fetch_array($result);
	}
	
	function database_fetch_assoc($result) {
		return mysqli_fetch_assoc($result);
	}
	
	function database_fetch_row($result) {
		return mysqli_fetch_row($result);
	}
	
	function database_result($result, $row = 0) {
		$data = mysqli_fetch_row($result);
		return $data[$row];
	}
	
	function database_num_rows($result) {
		return mysqli_num_rows($result);
	}
	
	//**OO Query Function
	
	function query($query) {
		return new resultSet($this->database_query($query));
	}
	
	//**OO Query Function
	
	function queryOne($query) {
		return $this->database_result($this->database_query($query));
	}
	
	function queryRow($query) {
		return $this->database_fetch_assoc($this->database_query($query));
	}
    
    function quote($string) {
		return $this->_quote_val($string);
	}
		
	function database_get($table, $condition="", $sort="") {
		$query = "SELECT * FROM $table";
		$query .= $this->_makeWhereList( $condition );  
		if ( $sort != "" )
			$query .= " order by $sort";
		return $this->database_query($query);
	}
 
 	function database_insert($table, $add_array) {
		$add_array = $this->_quote_vals( $add_array );
		$keys = "(".implode( array_keys( $add_array ), ", ").")";
		$values = "values (".implode( array_values( $add_array ), ", ").")";
		$query = "INSERT INTO $table $keys $values";
		return $this->database_query($query);
	}
	
	function database_insert_id() {
		return mysqli_insert_id($this->db);
	}
		
	function database_update($table, $update_array, $condition="") {
		$update_pairs=array();
		foreach( $update_array as $field=>$val )
		array_push( $update_pairs, "$field=".$this->_quote_val( $val ) );
		
		$query = "UPDATE $table set ";
		$query .= implode( ", ", $update_pairs );
		$query .= $this->_makeWhereList( $condition );  
		return $this->database_query($query);
	}
 
	function database_delete($table, $condition="") {
		$query = "DELETE FROM $table";
		$query .= $this->_makeWhereList( $condition );  
		return $this->database_query($query);
	}
   
	function _makeWhereList($condition) {
		if (empty($condition)) return "";
		$retstr = " WHERE ";
		if (is_array($condition)) {
			$cond_pairs=array();
			foreach( $condition as $field=>$val )
			array_push( $cond_pairs, "$field=".$this->_quote_val( $val ) );
			$retstr .= implode( " and ", $cond_pairs );
		} elseif ( is_string( $condition ) && ! empty( $condition ) )
			$retstr .= $condition;
			return $retstr;
	}
	
	function _quote_val($val) {
		if ( is_numeric( $val ) ) return $val;
		if (get_magic_quotes_gpc()==1) {
			return "'$val'";
		}
		else {
			return "'".addslashes($val)."'";
		}
	}

	function _quote_vals($array) {
		foreach( $array as $key=>$val ) {
			$ret[$key]=$this->_quote_val( $val );
		}
		return $ret;
	}
	
	function mail($to, $subject, $message, $from_header) {
		if($this->settings['email_override']) $to = $this->settings['email_override'];
		if($this->settings['email_allow']) {
			return mail($to, $subject, $message, $from_header);
		} else {
			echo "E-mail is disabled, would of sent e-mail to $to";
			return true;
		}
	}
	
	function error($additional) {
		//echo "<p>There has been a crictical error and we are unable to process your request.<br />Please contact us and report this error. <br /><br />Additonal: $additional";
		echo "Error: $additional";
		echo '<br /><br />' . $this->database_errno() . ' : ' . $this->database_error();

		/*
		$to = $this->settings['email_errorsTo'];
		$from_header = "From: " . $this->settings['email_errorsFrom'];
		$subject = "Script Error";
		$message = "There has been an error in " . $this->settings['app_name'] . " - php script\n\n" . mysqli_errno() . " : " . mysqli_error() . "\n\nAdditonal: $additional";
		$this->mail($to, $subject, $message, $from_header);
		//exit();
		*/
	}

	function geturl() {
		return $_SERVER['REQUEST_URI'];
	}
	
	function redirecturl($url) {
		echo "<script language=\"JavaScript\">location.href = \"$url\";</script>";
	}
	
	function secure() {
		if(!$this->$auth['secure']) {
			if($this->auth['require_ssl']) {
				$port = $_SERVER['SERVER_PORT']; 
				if($port === "443") { 
					//echo "You are using an SSL Connection, Your transactions are secured!"; 
				} else { 
					echo "You are not using a SSL Secure connection, connection terminated!";
					exit();
				} 
			}
			
			session_start();
			
			if($_POST['login_submit'])  // If this was called by a login form run this.
			{
				$userdata = $this->secure_check_user($_POST['username'], $_POST['password']);
				if($userdata)
				{
					session_register($this->auth['session_prefix'] . '_UID');
					session_register($this->auth['session_prefix'] . '_USER');
					session_register($this->auth['session_prefix'] . '_PASS');
					session_register($this->auth['session_prefix'] . '_RIGHTS');
				
					$_SESSION[$this->auth['session_prefix'] . '_UID']	= $userdata[$this->auth['db_uid_field']];
					$_SESSION[$this->auth['session_prefix'] . '_USER']	= $userdata[$this->auth['db_username_field']];
					$_SESSION[$this->auth['session_prefix'] . '_PASS']	= $userdata[$this->auth['db_password_field']];
					$_SESSION[$this->auth['session_prefix'] . '_RIGHTS']= $userdata[$this->auth['db_rights_field']];
							
					$this->redirecturl($HTTP_REFERER);
					exit();
						
				}
				else {
			
					if($this->auth['bad_login_action'] == 1) {
						$this->redirecturl($HTTP_REFERER);
					}
					else if($this->auth['bad_login_action'] == 2) {
						$this->redirecturl($bad_login_message);
					}
					if($this->auth['bad_login_action'] == 3) {
						$this->redirecturl($this->auth['bad_login_url']);
					}
					exit();
				}
			}
			if($this->secure_check_user($_SESSION[$this->auth['session_prefix'] . "_USER"], $_SESSION[$this->auth['session_prefix'] . "_PASS"], $database)) {
				//Sucessfully Authenicated
				$this->$auth['secure'] = 1;

				$this->auth['id']	= $_SESSION[$this->auth['session_prefix'] . '_UID'];
				$row = $this->database_fetch_array($this->database_query("SELECT * FROM custsoft_staff WHERE staff_staffid='".$this->auth['id']."'"));
				$this->auth['username']		= $row[$this->auth['db_username_field']];
				$this->auth['accesslevel']	= $row[$this->auth['db_accesslevel_field']];
				$this->auth['loggedInUserData'] = $row;
			} else {
				// No Session or invalid session.
				include($this->auth['login_page']);
				exit();
			}
		}
		return $this->$auth['secure'];
	}

	function secure_logout($homepage) {				
		if(session_is_registered($this->auth['session_prefix'] . "_UID")) {
			session_unset($this->auth['session_prefix'] . "_UID");
			session_unset($this->auth['session_prefix'] . "_USER");
			session_unset($this->auth['session_prefix'] . "_PASS");
			session_unset($this->auth['session_prefix'] . "_RIGHTS");
			session_destroy();
		}
		$this->redirecturl($homepage);
	}
			
	function secure_check_user($uname, $passwd) {
		$database_name				= $this->auth['db_name_field'];
		$database_username_field	= $this->auth['db_username_field'];
		$database_password_field	= $this->auth['db_password_field'];
			
		$sql_query = "SELECT * FROM `$database_name` WHERE `$database_username_field` = '$uname' AND `$database_password_field` = '$passwd'";
		$sql = $this->database_query($sql_query);
			
		$sql_data = $this->database_fetch_array($sql);
		if($sql_data[$database_password_field] == $passwd) { // Check Password again to be case sensitive.
			return $sql_data;
		} else {
			return false;
		}
	}
	
	function checkEmailAddress($mail) {
	    $valid = false;
		$email_host = explode("@", $mail);
    	$email_host = $email_host['1'];
    	$email_resolved = gethostbyname($email_host);
    	if ($email_resolved != $email_host && eregi("^[0-9a-z]([-+_.~]?[0-9a-z])*@[0-9a-z]([-.]?[0-9a-z])*\\.[a-z]{2,4}$",$mail)) {
	        $valid = true;
		}
    return $valid;
	}

}

class resultSet {
	
	var $query_result;
	
	function resultSet($result) {
		$this->query_result = $result;
	}
		
	function fetchRow() {
		return mysqli_fetch_assoc($this->query_result);
	}
	
	function numRows() {
		return mysqli_num_rows($this->query_result);
	}
}

?>