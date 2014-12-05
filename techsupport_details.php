<?php
/*Before using, you should add some stuff to you 'techsupport' table. Refer to 'techsupport.php' for more info.
*/

require_once 'include/_universal.php';
require_once 'include/cl_validation.php';
$x = new universal('tech support','',0);
$x->add_related_link('assign/solve/edit jobs','admin_techsupport.php',2);
if ($x->is_secure() && $toggle['techsupport']) {
    $x->display_top();
    echo "<strong>technical support</strong>: details<br /><br />";
    $x->display_related_links();
    $colours = array('009999','3333cc','009900','66cc00','99cc00','ffff00','ffcc00','ff6600','ff0000','990000');
    begitem('tech support details');
    if(!empty($_GET['sid'])){

        $data = $dbc->database_query('SELECT * FROM `techsupport` WHERE `itemid`='.intval($_GET["sid"]));
        if(!$dbc->database_num_rows($data)){
          echo 'your sid didnt work';
        }
        
        $row = $dbc->database_fetch_array($data);
        $temprow = $dbc->database_fetch_assoc($dbc->database_query('SELECT * FROM users WHERE userid='.(int)$row['userid']));
        
        ?>
        additional details for tech support entry.
        <br /><br />
       	<table border="0" cellpadding="3" cellspacing="0" width="100%">
       	<tr><td width="25%"><b>field</b></td><td><b>info</b></td></tr>
       	<tr>
            <td><b>entry no</b></td>
            <td><?php echo $row['itemid']; ?></td>
        </tr>
       	<tr>
            <td><b>username</b></td>
            <td><?php echo $temprow["username"]; ?></td>
        </tr>
        <tr>
            <td><b>time</b></td>
            <td><?php $time = round((date('U')-date('U',strtotime($row['itemtime'])))/3600,1); ?>
				<?php echo ($time!=0?$time.' hours ago':'now'); ?></td>
        </tr>
        <tr>
            <td><b>severity</b></td>
            <td><table width="0%">
            <tr><td bgcolor="#<?php echo $colours[($row['severity'] * 2) - 1]; ?>"><font color="#000000">&nbsp;<b><?php
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
					}?>&nbsp;</font></b></td></tr></table>
        </tr>
        <tr>
            <td><b>complete information given by <?php echo $temprow["username"]; ?></b></td>
            <td><?php echo str_replace("\n", "\n<br />", $row["info"]); ?></td>
        </tr>
        <tr>
            <td><b>status</b></td>
            <td><?php if (empty($row['assigned_to']) && empty($row['fixed'])){
                                //Unassigned and unfixed.
                                echo 'Unassigned.';
                            }elseif(!empty($row['assigned_to']) && empty($row['fixed'])){
                                //Assigned and unfixed
                                echo 'Assigned.';
                                $user_row = $dbc->database_fetch_assoc($dbc->database_query('SELECT * FROM  `users` WHERE `userid`='.$row['assigned_to']));
                                echo ' ['.$user_row["username"].']';
                            }elseif(!empty($row['assigned_to']) && !empty($row['fixed'])){
                                //assigned and fixed.
                                echo 'Fixed.';
                                $user_row = $dbc->database_fetch_assoc($dbc->database_query('SELECT * FROM  `users` WHERE `userid`='.$row['assigned_to']));
                                echo ' ['.$user_row["username"].']';
                            }?></td>
        </tr>
        <?php if (!empty($row["result"])){ ?>
        <tr>
            <td><b>result of tech support</b></td>
            <td><?php echo str_replace("\n", "\n<br />", $row["result"]); ?></td>
        </tr>
        <?php } ?>
        </table>
        <?php
    }else{

        echo 'you didnt give me an sid!';
    
    }
    echo '<br /><br />';
    enditem('tech support details');
    
    if (($_COOKIE['userid'] == $row['assigned_to'] || current_security_level() >= 2) && $row['fixed'] == 0){
        ?><b><a href="techsupport_solve.php?jid=<?php echo $row['itemid'];?>">solve this problem</a></b>
        <?php
    }
    ?>
    <br /><br />
    <a href="techsupport.php">back</a>
    <br /><br />
    <?php
   	$x->display_bottom();
} else {
	$x->display_slim('you are not authorized to view this page.');
}
?>