<?php
/*Before using, you should add some stuff to you 'techsupport' table. Refer to 'techsupport.php' for more info.
*/

require_once 'include/_universal.php';
require_once 'include/cl_validation.php';
$x = new universal('tech support','',0);
$x->add_related_link('assign/solve/edit jobs','admin_techsupport.php',2);
if ($x->is_secure() && $toggle['techsupport']) {
    $x->display_top();
    echo "<strong>technical support</strong>: solve<br /><br />";
    
    if (empty($_POST)){
       if (!empty($_GET['jid'])){
            $jobrow = $dbc->database_query('SELECT * FROM `techsupport` WHERE `itemid`='.intval($_GET['jid']));
           if ($dbc->database_num_rows($jobrow)){
                if (!($_COOKIE['userid'] != $jobrow && current_security_level() < 2)){
                    //Allowed to solve
                    begitem('solve');
                    $job = $dbc->database_fetch_assoc($jobrow);
                    ?>
                        <b>solving problem #<?php echo $_GET['jid']; ?></b><br />
                        <table width="100%">
                        <tr>
                            <td width="25%"></td>
                            <td></td>
                        </tr>
                        <tr>
                            <td>
                        <form action="<?php echo get_script_name();?>" method="POST">
                        <input type="hidden" name="action" value="solve" />
                        <input type="hidden" name="jid" value="<?php echo $_GET['jid'];?>" />
                        <b>result</b>
                        <br />
                        <textarea name="result"></textarea>
                        <br />
                            </td>
                            <td></td>
                        </tr>
                        <tr>
                            <td></td>
                            <td><input type="submit" name="submit" value="solved!" class="formcolors" /></td>
                        </tr>
                        </form>
                        </table>
                        <b><a href="techsupport.php">back.</a></b><br /><br />
                    <?php
                    enditem('solve');
                }else{
                    //Piss off
                ?>you are not authorised<?php
                }

            
            }else{
            ?>
                jid not valid.
            <?php
            }
        }else{
            ?>no jid given.<?php
        }
    }else{
  		require_once 'include/cl_validation.php';
		$valid = new validate();
		if ($valid->get_value('action') == 'solve'){
            $valid->is_empty('result', 'you need to include a result.');
            if(!$valid->is_error()) {
                if ($dbc->database_num_rows($dbc->database_query("SELECT * FROM techsupport WHERE `itemid`=".$valid->get_value('jid')))) {
					if($dbc->database_query("UPDATE `techsupport` SET `fixed`=1, `fixer`=".$_COOKIE['userid'].", `result`='".$valid->get_value('result')."' WHERE `itemid`=".$valid->get_value('jid'))) {
						echo 'problem solved. good work.<br /><br /> &gt; <a href="techsupport.php">go back</a>.<br /><br />';
					} else {
						echo 'there has been an error solving the tech support request.  it has _not_ been solved.<br /><br />';
					}
				} else {
					echo 'arg! there is no tech support request there.<br /><br />';
				}
            }
        }
    }
    $x->display_bottom();
} else {
	$x->display_slim('you are not authorized to view this page.');
}
?>