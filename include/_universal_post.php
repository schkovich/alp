<?php
// add 2 does not currently work with dates or checkboxes (?).
global $dbc;

require_once 'include/cl_validation.php';
$valid = new validate();

$sbool = true;
if(!$this->is_secure()) $sbool = false;
if(current_security_level()==1&&$this->element_exists('userid')) {
	if($valid->get_value('userid') != $_COOKIE['userid']) $sbool = false;
}

if($sbool) {
	if(!empty($redirect)) {
	    $url = $redirect.($valid->get_value("_hidden_id")!=""?"?id=".$valid->get_value("_hidden_id"):"");
	} else {
	    $url = get_script_name().($valid->get_value("_hidden_id")!=""?"?id=".$valid->get_value("_hidden_id"):"");
	}
	$errorurl = get_script_name().($valid->get_value("_hidden_id")!=""?"?id=".$valid->get_value("_hidden_id"):"");
	
	$curr = $this->get_elements();
	if($valid->get_value("type")=="add") {
	    if($this->_permissions["add"]>0) {
	        foreach($curr as $key=>$val) {
	            if($val->get_type()=="datetime") {
	                if(
						($valid->get_value($val->get_name()."_hour")!="")
						&&($valid->get_value($val->get_name()."_minute")!="")
						&&($valid->get_value($val->get_name()."_month")!="")
						&&($valid->get_value($val->get_name()."_day")!="")
						&&($valid->get_value($val->get_name()."_year")!="")) {
	                    $date = mktime($valid->get_value($val->get_name()."_hour"),$valid->get_value($val->get_name()."_minute"),0,$valid->get_value($val->get_name()."_month"),$valid->get_value($val->get_name()."_day"),$valid->get_value($val->get_name()."_year"));
	                    if(!$master["alldates"]) {
	                        if((date("U",$start)>date("U",$date))||(date("U",$date)>date("U",$end))) {
	                            $valid->add_error("the datetime you specified is out of range.  please go back and specify a different time.");
	                        }
	                    }
	                    $dd[$val->get_name()] = "'".date("Y-m-d H:i:s",$date)."'";
	                } else {
	                    $dd[$val->get_name()] = "NULL";
	                }
	            }
	            if($val->get_is_required()) {
	                if($val->get_type()=="datetime") {
	                    if(($valid->get_value($val->get_name()."_hour")==='')
							||($valid->get_value($val->get_name()."_minute")==='')
							||($valid->get_value($val->get_name()."_month")=='')
							||($valid->get_value($val->get_name()."_day")=='')
							||($valid->get_value($val->get_name()."_year")=='')) {
	                        $valid->add_error("the datetime field is required and was empty.");
	                    }
	                } else {
	                    $valid->is_empty($val->get_name(),$val->get_error("empty"));
	                }
	            }
	        }
	        if(!$valid->is_error()) {
	            $allgood = true;
	            if($this->_permissions["add"]==1) {
	                $fieldnames = "";
	                $fieldvalues = "";
	                $counter = 0;
	                foreach($curr as $key=>$val) {
	                    $fieldnames .= $val->get_name();
	                    if($val->get_type()=="datetime") {
	                        $fieldvalues .= $dd[$val->get_name()];
	                    } elseif($val->get_type()=="checkbox") {
	                        // highlander
	                        if($val->get_specific()&&$valid->get_value($val->get_name())!="") {
	                            if(!$dbc->database_query("UPDATE ".$this->_table_name." SET ".$val->get_name()."='0'")) {
	                                $allgood = false;
	                            }
	                        }
	                        if($valid->get_value($val->get_name())=="") {
	                            $fieldvalues .= "'0'";
	                        } else {
	                            $fieldvalues .= "'1'";
	                        }
	                    } elseif($val->get_type()=="hidden") {
	                        $fieldvalues .= "'".$val->get_specific()."'";
	                    } else {
	                        if($val->get_is_unclean()) $fieldvalues .= "'".$valid->get_value_unclean($val->get_name())."'";
	                        else $fieldvalues .= "'".$valid->get_value($val->get_name())."'";
	                    }
	                    if($counter!=(sizeof($curr)-1)) {
	                        $fieldnames .= ", ";
	                        $fieldvalues .= ", ";
	                    }
	                    $counter++;
	                }
	                $query = "INSERT INTO ".$this->_table_name." (".$fieldnames.") VALUES (".$fieldvalues.")";
	                //$query = str_replace("''", "NULL", $query);
	                if(!$dbc->database_query($query)) {
	                    $allgood = false;
	                }
	            } elseif($this->_permissions["add"]==2) {
	                $id = $this->_id;
	                $userid = $curr[$id]->get_specific();
	                foreach($curr as $key=>$val) {
	                    if($id!=$key) {
	                        $query = "INSERT INTO ".$this->_table_name." (".$this->_order.",".$id.",value) VALUES ('".$key."','".$userid."','".$valid->get_value($key)."')";
	                        //$query = str_replace("''", "NULL", $query);
	                        if(!$dbc->database_query($query)) {
	                            $allgood = false;
	                        }
	                    }
	                }
	            }
	            if($allgood) {
	                $this->display_slim("success.",$url);
	            } else {
	                $this->display_slim("unknown error!",$errorurl);
	            }
	        } else {
	            $this->display_top();
	            $valid->display_errors();
	            $this->display_bottom();
	        }
	    } else {
	        $this->display_top();
	        echo "this script does not have the permissions to execute that action.";
	        $this->display_bottom();
	    }
	} elseif($valid->get_value("type")=="del") {
		if($this->_permissions["del"]) {
			$keys = array_keys($_POST);
		    $allgood = true;
		    for($i=0;$i<sizeof($keys);$i++) {
		    	if($keys[$i]!="type"&&$valid->get_value($keys[$i])) {
		        	if($dbc->database_query("DELETE from ".$this->_table_name." WHERE ".$this->_id."='".$keys[$i]."'")) {
		            	if(!empty($this->_extra["del"])) {
		                	foreach($this->_extra["del"] as $deleting) {
		                    	if(!$dbc->database_query($deleting[0]." WHERE ".$deleting[1]."='".$keys[$i]."'")) {
		                        	$allgood = false;
		                        }
		                    }
		                }
		            } else {
		            	$allgood = false;
		            }
		        }
		    }
		                        
		    if($allgood) {
		    	$this->display_slim("success.",$url);
		    } else {
		        $this->display_slim("unknown error!",$errorurl);
		    }
		} else {
		    $this->display_top();
		    echo "this script does not have the permissions to execute that action.";
		    $this->display_bottom();
		}
	} elseif($valid->get_value("type")=="mod") {
		if($this->_permissions["mod"]>0) {
			if($this->_permissions["mod"]==2||$this->_permissions["mod"]==3) {
		    	$query = "SELECT * FROM ".$this->_table_name;
		        //if(!empty($this->_exclude)) $query .= $this->_exclude;
		        $temp = $dbc->database_num_rows($dbc->database_query($query));
		    } elseif($this->_permissions["mod"]==1) {
		        $temp = 1;
		    }
		    for($i=1;$i<=$temp;$i++) {
		    	if($this->_permissions["mod"]==2) {
		        	$holder = $i."_";
		        } else {
		            $holder = "";
		        }
						
    		    $query = "SELECT * FROM ".$this->_table_name." WHERE ".$this->_crutch."=1";
    		    if(!empty($this->_id)) $query .= " AND ".$this->_id."='".$valid->get_value($holder.$this->_id)."'";
    		   	$crutch = $dbc->database_num_rows($dbc->database_query($query));
						
    		    foreach($curr as $key=>$val) {
    		    	if($val->get_type()=="datetime") {
    		        	if(
    						($valid->get_value($holder.$val->get_name()."_hour")!="")
    						&&($valid->get_value($holder.$val->get_name()."_minute")!="")
    						&&($valid->get_value($holder.$val->get_name()."_month")!="")
    						&&($valid->get_value($holder.$val->get_name()."_day")!="")
    						&&($valid->get_value($holder.$val->get_name()."_year")!="")) {
    		            	$date = mktime($valid->get_value($holder.$val->get_name()."_hour"),$valid->get_value($holder.$val->get_name()."_minute"),0,$valid->get_value($holder.$val->get_name()."_month"),$valid->get_value($holder.$val->get_name()."_day"),$valid->get_value($holder.$val->get_name()."_year"));
    		                if(!$master["alldates"]) {
    		                	if((date("U",$start)>date("U",$date))||(date("U",$date)>date("U",$end))) {
    		                    	$valid->add_error("the datetime you specified is out of range.  please go back and specify a different time.");
    		                    }
    		                }
    		                $dd[$val->get_name()] = "'".date("Y-m-d H:i:s",$date)."'";
    		            } else {
    		            	$dd[$val->get_name()] = "NULL";
    		            }
    		        }
    		    	if($val->get_is_required()&&$val->get_is_modifiable()&&(!$val->get_is_dep_crutch()||!$crutch)) {
    		        	if($val->get_type()=="datetime") {
    		            	if(
    							($valid->get_value($holder.$val->get_name()."_hour")=="")
    							||($valid->get_value($holder.$val->get_name()."_minute")=="")
    							||($valid->get_value($holder.$val->get_name()."_month")=="")
    							||($valid->get_value($holder.$val->get_name()."_day")=="")
    							||($valid->get_value($holder.$val->get_name()."_year")=="")) {
    		                	$valid->add_error("the datetime field is required and was empty.");
    		                }
    		            } else {
    		                $valid->is_empty($val->get_name(),$val->get_error("empty"));
    		            }
    		        }
    		   	}
    		}
		
    		if(!$valid->is_error()) {
    			if($this->_permissions["mod"]==2) {
    		    	$temp = $dbc->database_num_rows($dbc->database_query("SELECT * FROM ".$this->_table_name));
    		    } elseif($this->_permissions["mod"]==1) {
    		        $temp = 1;
    		    }
    		    $allallgood = true;
    		    for($i=1;$i<=$temp;$i++) {
    		    	if($this->_permissions["mod"]==2) {
    		        	$holder = $i."_";
    		        } else {
    		            $holder = "";
    		        }
    							
    		        $query = "SELECT * FROM ".$this->_table_name." WHERE ".$this->_crutch."=1";
    		        if(!empty($this->_id)) $query .= " AND ".$this->_id."='".$valid->get_value($holder.$this->_id)."'";
    		        $crutch = $dbc->database_num_rows($dbc->database_query($query));
    		
    		        $allgood = true;
    		        $items = "";
    		        $counter = 0;
    		        foreach($curr as $key=>$val) {
    		        	if($counter!=0&&$val->get_is_modifiable()&&(!$val->get_is_dep_crutch()||!$crutch)) {
                            $items .= ", ";
                        }
    					if($val->get_type()=="datetime"&&$val->get_is_modifiable()&&(!$val->get_is_dep_crutch()||!$crutch)) {
    						$items .= $val->get_name()."=".$dd[$val->get_name()];
    					} elseif($val->get_type()=="checkbox"&&$val->get_is_modifiable()&&(!$val->get_is_dep_crutch()||!$crutch)) {
    						// highlander
    						if($val->get_specific()&&$valid->get_value($holder.$val->get_name())!="") {
    							if(!$dbc->database_query("UPDATE ".$this->_table_name." SET ".$val->get_name()."=0")) {
    								$allgood = false;
    							}
    						}
    						if($valid->get_value($holder.$val->get_name())!="") {
    							$items .= $val->get_name()."=1";
    						} else {
    							$items .= $val->get_name()."=0";
    						}
    					} elseif($val->get_is_modifiable()&&(!$val->get_is_dep_crutch()||!$crutch)) {
                            $test = str_replace("&lt;br /&gt;","",($val->get_is_unclean()? $valid->get_value_unclean($holder.$val->get_name()): $valid->get_value($holder.$val->get_name())));
    						$test = str_replace("<br />","",$test);
    						if($val->get_is_unclean()) $items .= $val->get_name()."='".($val->get_type()=="textarea"?$test:$valid->get_value_unclean($holder.$val->get_name()))."'";
    						else $items .= $val->get_name()."='".($val->get_type()=="textarea"?$test:$valid->get_value($holder.$val->get_name()))."'";
    					}
    					if($val->get_is_modifiable()&&(!$val->get_is_dep_crutch()||!$crutch)) $counter++;
    				}
    				if(!empty($this->_extra["mod"])) {
    					foreach($this->_extra["mod"] as $modifying) {
    						$query = $modifying[0];
    						if(!empty($this->_id)) $query .= " WHERE ".$modifying[1]."='".$valid->get_value($holder.$this->_id)."'";
    						//$query = str_replace("''", "NULL", $query);
    						if(!$dbc->database_query($query)) {
    							$allgood = false;
    						}
    					}
    				}
    				$query = "UPDATE ".$this->_table_name." SET ".$items;
    				if(!empty($this->_id)) $query .= " WHERE ".$this->_id."='".$valid->get_value($holder.$this->_id)."'";
    				//$query = str_replace("''", "NULL", $query);
    				if(!$dbc->database_query($query)||!$allgood) {
    					$allallgood = false;
    				}
    				if($i==$temp&&$allallgood) {
    					$this->display_slim("success.",$url.(!empty($this->_id)&&$this->_permissions["mod"]==1&&$valid->get_value("_one_row_only")>1?($valid->get_value("_hidden_id")!=""?"&":"?")."mod=1&q=".$valid->get_value($holder.$this->_id):""));
    				} elseif($i==$temp&&!$allallgood) {
    					$this->display_slim("unknown error!",$errorurl);
    				}
    			}
    		} else {
    			$this->display_top();
    			$valid->display_errors();
    			$this->display_bottom();
    		}
		} else {
			$this->display_top();
			echo "this script does not have the permissions to execute that action.";
			$this->display_bottom();
		}
	} elseif($valid->get_value("type")=="update") {
		if($this->_permissions["update"]) {
			foreach($curr as $key=>$val) {
				if($val->get_is_required()&&$val->get_is_modifiable()) {
					$valid->is_empty($val->get_name(),$val->get_error("empty"));
				}
			}
			if(!$valid->is_error()) {
				$allgood = true;
				$items = "";
				$counter = 0;
				foreach($curr as $key=>$val) {
					if($counter!=0&&$val->get_is_modifiable()) { $items .= ", "; }
					if($val->get_is_modifiable()) {
						$items .= $val->get_name()."='".$valid->get_value($val->get_name())."'";
					}
					$counter++;
				}
				if(!empty($this->_extra["update"])) {
					foreach($this->_extra["update"] as $updating) {
						$query = $updating[0];
						if(!empty($this->_id)) $query .= " WHERE ".$updating[1]."='".$valid->get_value($this->_id)."'";
						//$query = str_replace("''", "NULL", $query);
						if(!$dbc->database_query($query)) {
							$allgood = false;
						}
					}
				}
				if($allgood) {
					$this->display_slim("success.",$url);
				} else {
					$this->display_slim("unknown error!",$errorurl);
				}
			} else {
				$this->display_top();
				$valid->display_errors();
				$this->display_bottom();
			}
		} else {
			$this->display_top();
			echo "this script does not have the permissions to execute that action.";
			$this->display_bottom();
		}
	}
} else {
	$this->display_slim("nice try...");
}
?>