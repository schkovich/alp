<?php
require_once 'include/_universal.php';
$x = new universal('tech support','',0);
$x->add_related_link('assign/solve/edit jobs','admin_techsupport.php',2);

if ($x->is_secure() && $toggle['techsupport']) { 
	$x->display_top();
    echo "<strong>technical support</strong>: <br /><br />";
    $x->display_related_links();
	if (empty($_POST)) {
		$colours = array('009999','3333cc','009900','66cc00','99cc00','ffff00','ffcc00','ff6600','ff0000','990000');
		$data = $dbc->database_query('SELECT * FROM techsupport ORDER BY itemtime DESC');
		if ($dbc->database_num_rows($data)) {
			begitem('tech support queue'); ?>
			this list is intended to be a queue for anyone's technical troubles. if you're having any technical problems, post it here. you are allowed to have only one request at a time; and our admins should be assigned the job shortly after that.<br />

			<table border="0" cellpadding="3" cellspacing="0" width="100%">
			<tr><td><?php spacer(1,28); ?></td><td><b>username</b><br /><?php spacer(0,1); ?></td><td><b>details</b><br /><?php spacer(0,1); ?></td><td><b>time</b><br /><?php spacer(100); ?></td><td><div align="center"><b>severity</b></div></td><td width="100%"><b>problem</b></td><td><b>status</b></td><td>&nbsp;</td></tr>
			<?php
			$counter = 0;
			while($row = $dbc->database_fetch_assoc($data)) {
				$temprow = $dbc->database_fetch_assoc($dbc->database_query('SELECT * FROM users WHERE userid='.(int)$row['userid'])); ?>
				<tr <?php echo ($counter%2 == 0?'bgcolor="'.$colors['cell_alternate'].'"':''); ?>>
				<td><?php spacer(1,28); ?></td>
				<td>#<?php echo $row['itemid']; ?> <a href="disp_users.php?id=<?php echo $row['userid']; ?>"<?php echo ($row['fixed']==0?"style=\"color: #".$colours[($row['severity']*2) -1]."\"><b>":">"); ?><?php echo $temprow['username']; ?><?php echo ($row['fixed']==0?'</b>':''); ?></a></td>
                <td><a href="techsupport_details.php?sid=<?php echo $row["itemid"];?>"><img src="img/goto.gif" border=0 alt="view details"></a></td>
				<?php $time = round((date('U')-date('U',strtotime($row['itemtime'])))/3600,1); ?>
				<td><?php echo ($time!=0?$time.' hours ago':'now'); ?></td>
				<td bgcolor="#<?php echo $colours[($row['severity']*2)-1]; ?>"><div align="center"><font color="#000000">&nbsp;<b><?php //
										switch($row['severity']){
											case(1):
												echo "1 [annoying]";
												break;
											case(2):
												echo "2 [minor]";
												break;
											case(3):
												echo "3 [important]";
												break;
											case(4):
												echo "4 [major]";
												break;
											case(5):
												echo "5 [critical]";
												break;
										}
											//echo $row['severity']; ?></b>&nbsp;</font></div></td>
				<td><?php echo str_replace("\n", "\n<br />", $row['info']); ?></td>
				<td><?php if (empty($row['assigned_to']) && empty($row['fixed'])){
                                //Unassigned and unfixed.
                                echo 'Unassigned.';
                            }elseif(!empty($row['assigned_to']) && empty($row['fixed'])){
                                //Assigned and unfixed
                                echo 'Assigned.';
                                $user_row = $dbc->database_fetch_assoc($dbc->database_query('SELECT * FROM  `users` WHERE `userid`='.$row['assigned_to']));
                                echo ' ['.$user_row["username"].']';
                            }elseif(!empty($row['assigned_to']) && !empty($row['fixed'])){
                                //assigned and fixed.
                                echo '<a href="techsupport_details.php?sid='.$row["itemid"]. '" title="View result of tech support">Fixed.</a>';
                                $user_row = $dbc->database_fetch_assoc($dbc->database_query('SELECT * FROM  `users` WHERE `userid`='.$row['assigned_to']));
                                echo ' ['.$user_row["username"].']';
                            }?>
                            </td>

				<td>
					<?php
					if ($row['userid'] == $_COOKIE['userid'] || current_security_level() >= 2 || $row['assigned_to'] == $_COOKIE['userid']) { ?>
						<form action="<?php echo get_script_name(); ?>" method="POST"><?php
						if ($row['fixed'] == 0) { ?>
							<input type="hidden" name="type" value="delete" />
							<input type="hidden" name="itemid" value="<?php echo $row['itemid']; ?>" />
							<input type="submit" name="submit" value="cancel!" style="width: 50px; font: 10px Verdana;" class="formcolors" /><?php
						} elseif ($row['fixed'] == 1 && current_security_level() >= 2) { ?>		
							<input type="hidden" name="type" value="delete" />
							<input type="hidden" name="itemid" value="<?php echo $row['itemid']; ?>" />
							<input type="submit" name="submit" value="delete!" style="width: 50px; font: 10px Verdana;" class="formcolors" /><?php
						} ?></form><?php
					} ?>
				</td></tr>
				<?php
				$counter++;
			} ?>
            </table>
			<br />
			<?php
            enditem('tech support queue');

            
		} elseif (current_security_level() == 0) {
			begitem('tech support queue'); ?>
			there are no items currently in the queue.<br />
			<br />
			<?php
			enditem('tech support queue');
		}
		
		$fixt = $dbc->database_query("SELECT * FROM techsupport WHERE userid='".$_COOKIE['userid']."' AND fixed='0' ORDER BY itemtime");
			if(current_security_level()>=1) {
				if($fixtrow = $dbc->database_fetch_assoc($fixt)) { ?>
				<br />
				<?php begitem('problem solved'); ?>
                <b>is your problem solved?</b><br /><br />
				    <form action="techsupport.php" method="POST">
 			        <table border="0" cellpadding="3" cellspacing="0" width="100%">
 			        <tr>
                        <td width="25%"></td>
                        <td></td>
                    <tr>
                    <tr>
                        <td>&nbsp;<b>problem title:</b></td>
                        <td><?php   if (strlen($fixtrow['info'] > 50)){
                                        echo substr($fixtrow['info'], 0, 47)."...";
                                    }else{
                                        echo $fixtrow['info'];
                                    }
                            ?> </td>
 			        <tr>
                        <td>&nbsp;<b>problem solved by: </b></td>
                        <td><select name="fixer" style="width: 200px;">
                        <?php if (!empty($fixtrow['assigned_to'])){
                                $temprow = $dbc->database_fetch_assoc($dbc->database_query('SELECT * FROM `users` WHERE `userid`='.$fixtrow['assigned_to']));
                        ?>
                                <option value="<?php echo $temprow['userid']; ?>"><?php echo $temprow['username'];?></option>
    					<?php
    					}else{
                        ?>
                            <option value=""></option>
                        <?php
                        }
                        if (!empty($fixtrow['assigned_to'])){
                            $temp = $dbc->database_query('SELECT userid, username FROM users WHERE `userid`!='.$fixtrow['assigned_to']);
                        }else{
	   				        $temp = $dbc->database_query('SELECT userid, username FROM users');
                        }

	   				    while($temprow = $dbc->database_fetch_array($temp)) {
					       	echo '<option value="'.$temprow['userid'].'">'.$temprow['username'].'</option>';
					    }
					    ?>
					       </select></td>
                    </tr>
                    <tr>
                        <td>&nbsp;<b>result/solution to problem</b></td>
                        <td><textarea name="result"></textarea></td>
                    </tr>
                    <tr>
                        <td></td>
                        <td><input type="submit" name="submit" value="fixed!" class="formcolors" /></td>
                    </tr></table>

					<input type="hidden" name="type" value="modify" />
					<input type="hidden" name="itemid" value="<?php echo $fixtrow['itemid']; ?>" />



					</form>
					<br />
					<?php
					enditem('problem solved');
                }
            }

					
		$data = $dbc->database_query('SELECT * from techsupport WHERE userid='.(int)$_COOKIE['userid'].' AND fixed=0');
		if (!$dbc->database_num_rows($data) && current_security_level() >= 1) { 
			begitem('add tech support request'); ?>
			<table border="0" cellpadding="4" cellspacing="4" width="99%"><tr><td>
			<form action="techsupport.php" method="POST">
			<input type="hidden" name="type" value="add">
			<font size=1><b>severity of problem</b> (1 is least severe and 5 is most severe)<br /></font>
			<table border=0 width=100% cellpadding=3 cellspacing=0>
				<tr style="color: #000000">
				<td><select name="severity">
					<option value="1">1 [annoying]</option>
					<option value="2">2 [minor]</option>
					<option value="3">3 [important]</option>
					<option value="4">4 [major]</option>
					<option value="5">5 [critical]</option>
				</select>
				<!--
				<input type="radio" name="severity" class="radio" value=""> <b></b>--></td>
				
				</tr>
			</table>
			<br />
			<font size=1><b>details about your problem</b> (try to include your OS, what you are doing, and anything else you think is related)<br /></font>
			<table border=0 width=100% cellpadding=3 cellspacing=0>
				<tr style="color: #000000">
				<td><textarea name="info" rows="4" cols="40"></textarea>
				
				</tr>
			</table>
			<br />
			<div align="right"><input type="submit" name="submit" value="help!" style="width: 160px" class="formcolors" /></div>
			</form></td></tr></table>
			<?php 
			enditem('add tech support request');
        }
        
        $data = $dbc->database_query("SELECT * FROM `techsupport` WHERE assigned_to=".(int)$_COOKIE['userid']);
        if ($dbc->database_num_rows($data)){
            begitem('your jobs');
            
            $data = $dbc->database_query("SELECT * FROM `techsupport` WHERE `assigned_to`=".(int)$_COOKIE['userid']." AND `fixed`!=1");

            ?><b>unfixed jobs</b><br /><?php
            while($row = $dbc->database_fetch_assoc($data)){
                $user = $dbc->database_fetch_assoc($dbc->database_query('SELECT * FROM `users` WHERE `userid`='.$row['userid']));
                ?>#<?php echo $row['itemid'];?> - <a href="disp_users.php?id=<?php echo $row['userid'];?>"><?php echo $user['username'];?></a> <span style="color:#<?php echo $colours[($row['severity'] *2)-1];?>;">
   				<?php					switch($row['severity']){
											case(1):
												echo "1 [annoying]";
												break;
											case(2):
												echo "2 [minor]";
												break;
											case(3):
												echo "3 [important]";
												break;
											case(4):
												echo "4 [major]";
												break;
											case(5):
												echo "5 [critical]";
												break; 
										}
                                        ?></span> <a href="techsupport_details.php?sid=<?php echo $row['itemid'];?>">[details]</a><br /><?php
            }

            $data = $dbc->database_query("SELECT * FROM `techsupport` WHERE `assigned_to`=".(int)$_COOKIE['userid']." AND `fixed`!=0");
            ?><b>fixed jobs</b><br /><?php
            while($row = $dbc->database_fetch_assoc($data)){
                $user = $dbc->database_fetch_assoc($dbc->database_query('SELECT * FROM `users` WHERE `userid`='.$row['userid']));
                ?>#<?php echo $row['itemid'];?> - <a href="disp_users.php?id=<?php echo $row['userid'];?>"><?php echo $user['username'];?></a> <span style="color:#<?php echo $colours[($row['severity'] *2)-1];?>;">
   				<?php					switch($row['severity']){
											case(1):
												echo "1 [annoying]";
												break;
											case(2):
												echo "2 [minor]";
												break;
											case(3):
												echo "3 [important]";
												break;
											case(4):
												echo "4 [major]";
												break;
											case(5):
												echo "5 [critical]";
												break;
										}
                                        ?></span> <a href="techsupport_details.php?sid=<?php echo $row['itemid'];?>">[details]</a><br /><?php
            }

            
		      enditem('your jobs');
        }
	} else {
		require_once 'include/cl_validation.php';
		$valid = new validate();
		
		if ($valid->get_value('type') == 'add') {
			$valid->is_empty('severity','you need to input the severity of your request.');
			$valid->is_empty('info', 'you need to provide additional info to assist us helping you.');
			if(!$valid->is_error()) {
				if (!$dbc->database_num_rows($dbc->database_query("SELECT * FROM techsupport WHERE userid='".$_COOKIE['userid']."' AND fixed='0'"))) {
					if($dbc->database_query("INSERT INTO techsupport (userid,itemtime,severity,info,fixed) VALUES ('".$_COOKIE['userid']."','".date('Y-m-d H:i:s')."','".$valid->get_value('severity')."','".$valid->get_value('info')."', '0')")) {
						echo 'tech support request successfully added.<br /><br /> &gt; <a href="techsupport.php">view tech support requests</a>.<br /><br />';
					} else {
						echo 'there has been an error adding your tech support request.  it has _not_ been added.<br /><br />';
					}
				} else {
					echo 'you already have a tech support request in the database!  fix your current problem first.<br /><br />';
				}
			}else{
                $valid->display_errors();
            }
		} elseif ($valid->get_value('type') == 'delete') {
			if ($dbc->database_num_rows($dbc->database_query("SELECT * FROM techsupport WHERE itemid='".$valid->get_value("itemid")."' AND userid='".$_COOKIE['userid']."'"))||current_security_level()>=2) {
				if ($dbc->database_query("DELETE FROM techsupport WHERE itemid='".$valid->get_value('itemid')."'")) {
					echo 'your tech support request was successfully deleted.<br /><br /> &gt; <a href="techsupport.php">view tech support requests</a>.<br /><br />';
				} else {
					echo 'there was an error and your tech support request was _not_ deleted.';
				}
			} else {
				echo 'you are not authorized to delete someone else\'s tech support request.';
			}	
		} elseif ($valid->get_value("type")=="modify") {
            $valid->is_empty('fixer','you need to specify the fixer.');
            $valid->is_empty('result', 'you need to specify the solution.');
			if(!$valid->is_error()) {
                if ($dbc->database_query("UPDATE techsupport set fixed=1, fixer='".$valid->get_value('fixer')."', result='".$valid->get_value('result')."', assigned_to='".intval($valid->get_value('fixer'))."' WHERE itemid='".$valid->get_value('itemid')."' AND userid='".$_COOKIE['userid']."'")) {
                    echo 'your tech support request was successfully fulfilled.<br /><br /> &gt; <a href="techsupport.php">view tech support requests</a>.<br /><br />';
                } else {
				    echo 'there was an error and your tech support request was _not_ updated.';
                }
            }else{
                $valid->display_errors();
            }
            
		}elseif ($valid->get_value('type') == 'assign'){
			if ($dbc->database_query("UPDATE techsupport set `assigned_to`='".$valid->get_value('assign_to')."' WHERE `itemid`=".$valid->get_value('job')))  {
				echo 'assigned to job.<br /><br /> &gt; <a href="techsupport.php">back</a>.<br /><br />';
			} else {
				echo 'there was an error and your job assignment was _not_ addded.';
			}
        }
	}
	$x->display_bottom();
} else {
	$x->display_slim('you are not authorized to view this page.');
}
?>