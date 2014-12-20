<?php
global $dbc;

$query = "SELECT * FROM ".$this->_table;
if(!empty($this->_order)) {
    if(empty($_GET)||empty($_GET["sort"])) {
        $query .= " ORDER BY ".$this->_order;
    } else {
        $query .= " ORDER BY ".$_GET["sort"];
    }
}
if(!empty($this->_grouplist)) { ?>
            <form action="<?php echo get_script_name(); ?>" method="GET">
                <input type="hidden" name="sort" value="<?php echo (!empty($_GET)&&!empty($_GET["sort"])?$_GET["sort"]:""); ?>" />
                <font style="font-size: 11px">show: </font><select name="show" style="width: 200px; font: 10px Verdana;">
                    <option value=""<?php echo (empty($_GET)||empty($_GET["show"])?" selected":""); ?>></option>
                <?php
	            foreach($this->_grouplist as $key => $val) { ?>
                        <option value="<?php echo $key; ?>"<?php echo (!empty($_GET['show'])&&$_GET["show"]==$key?" selected":""); ?>><?php echo $val; ?></option>
                        <?php
                } ?>
                </select>
                <input type="submit" value="go" style="font: 10px Verdana;" class="formcolors" />
                </form>
                <br />
                <?php
				} ?>
				<table cellpadding="3" cellspacing="0" style="border: 0px; width: 100%; font-size: 13px" align="center">
				<tr bgcolor="<?php echo $colors["cell_title"];?>" style="font-size: 11px">
				<?php
				if($this->_count_bool) { ?>
					<td><b><u><a href="<?php echo get_script_name(); ?>?<?php echo (!empty($_GET)&&!empty($_GET["show"])?"show=".$_GET["show"]."&":""); ?>sort=" style="color: <?php echo $colors["blended_text"]; ?>">#</a></u></b></td>
					<?php
				}
				if(empty($_GET)||empty($_GET["show"])) {
					$temp = 0;
				} else {
					$temp = $_GET["show"];
				}
				if(!empty($this->_default)) {
					foreach($this->_default as $key => $val) { ?>
						<td><a href="<?php echo get_script_name(); ?>?<?php echo (!empty($_GET)&&!empty($_GET["show"])?"show=".$_GET["show"]."&":""); ?>sort=<?php echo $key; ?>" style="color: <?php echo $colors["blended_text"]; ?>"><b><u><?php echo $val->get_description(); ?></u></b></a></td>
						<?php
					}
				}
				foreach($this->_tables[$temp] as $key => $val) { 
					if(current_security_level()>=$val->get_security()) { ?>
						<td><?php
							$sort_boolean = $dbc->database_num_rows($dbc->database_query("SELECT * FROM ".$this->_table." ORDER BY ".$key));
							if(sizeof($val->get_interp())==0&&$sort_boolean) { ?>
								<a href="<?php echo get_script_name(); ?>?<?php echo (!empty($_GET)&&!empty($_GET["show"])?"show=".$_GET["show"]."&":""); ?>sort=<?php echo $key; ?>" style="color: <?php echo $colors["blended_text"]; ?>"><u>
								<?php
							} else { ?><font style="color: <?php echo $colors["blended_text"]; ?>"><?php } ?>
							<b><?php echo $val->get_description(); ?></b><?php echo (sizeof($val->get_interp())==0&&$sort_boolean?"</u></a>":"</font>"); ?></td>
						<?php
					}
				} ?>
				</tr>
				<?php
				$counter = 1;
				$data = $dbc->database_query($query);
				while($row = $dbc->database_fetch_assoc($data)) { 
                    $bgc = ($counter%2 == 1)?$colors['cell_background']:$colors['cell_alternate'];
                    echo '<tr bgcolor="'.$bgc.'">';
                    echo ($this->_count_bool?"<td>".$counter."</td>":""); ?>
					<?php
					$counter++;
					if(!empty($this->_default)) { 
						foreach($this->_default as $key => $field) {
							$interp = $field->get_interp();
							if(sizeof($field->get_link())>0) {
								$t = $field->get_link();
								if(stristr($t[0],"[".$key."]")) {
									$u = str_replace("[".$key."]",$row[$key],$t[0]);
								} else {
									$u = str_replace("[".$this->_id."]",$row[$this->_id],$t[0]);
								}
								if(sizeof($interp)==3) { 
									$tempor = $dbc->database_fetch_assoc($dbc->database_query("SELECT * FROM ".$interp[0]." WHERE ".$interp[1]."='".$row[$key]."'"));
									$holder = $tempor[$interp[2]];
								} else {
									$holder = $row[$key];
								} ?>
								<td><a href="<?php echo $u; ?>"><?php echo (sizeof($t)==1?$holder:(sizeof($t)==2?"<b>".$t[1]."</b>":"")); ?></a></td>
								<?php
							} else { ?>
								<td>
								<?php 
								if(sizeof($interp)==3) { 
									$tempor = $dbc->database_fetch_assoc($dbc->database_query("SELECT * FROM ".$interp[0]." WHERE ".$interp[1]."='".$row[$key]."'"));
									echo $tempor[$interp[2]];
								} else {
									echo $row[$key];
								} ?>
								</td>
								<?php
							}
						}
					}
					foreach($this->_tables[$temp] as $key => $field) {
						if(current_security_level()>=$field->get_security()) { 
							$interp = $field->get_interp();
							$crutch = $field->get_crutch();
							if(!empty($row[$key])&&(sizeof($crutch)==0||$dbc->database_num_rows($dbc->database_query("SELECT * FROM ".$crutch[1]." WHERE ".$crutch[0]." AND ".$crutch[2]."=".$row[$this->_id])))||(!empty($key)&&sizeof($field->get_link())>0)) {
								if(sizeof($field->get_list())>0) {
									echo "<td>";
									$t = $field->get_list();
									if(!empty($t[$row[$key]])) {
										echo $t[$row[$key]];
									} else {
										echo $row[$key];
									}
									echo "</td>";
								} elseif(sizeof($field->get_link())>0) {
									$t = $field->get_link();
									if(stristr($t[0],"[".$key."]")) {
										$u = str_replace("[".$key."]",urlencode($row[$key]),$t[0]);
									} elseif(stristr($t[0],"[".$this->_id."]")) {
										$u = str_replace("[".$this->_id."]",urlencode($row[$this->_id]),$t[0]);
									} else {
										$u = $t[0];
										foreach($row as $i_key => $i_val) {
											if(stristr($u,"[".$i_key."]")) {
												$u = str_replace("[".$i_key."]",urlencode($row[$i_key]),$u);
											}
										}
									}
									if(sizeof($interp)==3) { 
										$tempor = $dbc->database_fetch_assoc($dbc->database_query("SELECT * FROM ".$interp[0]." WHERE ".$interp[1]."='".$row[$key]."'"));
										$holder = $tempor[$interp[2]];
									} else {
										$holder = $row[$key];
									}
									echo "<td>";
									echo "<a href=\"".urldecode($u)."\">".(sizeof($t)==1?$holder:(sizeof($t)==2?"<b>".$t[1]."</b>":""))."</a>";
									echo "</td>";
								} elseif($field->get_date()!="") {
									echo "<td width=190>";
									echo "<font color=\"".(date("U",strtotime($row[$key]))>date("U")?$colors["primary"]:(date("U",strtotime($row[$key]))<date("U")?$colors["secondary"]:""))."\">".date($field->get_date(),strtotime($row[$key]))."</font>";
									if((date("U",strtotime($row[$key]))-date("U"))<3600&&(date("U",strtotime($row[$key]))-date("U"))>0) {
										echo "&nbsp;&nbsp;".round((date("U",strtotime($row[$key]))-date("U"))/60)." minutes";
									}
									echo "</td>";
								} else { 
									echo "<td>";
									if(sizeof($interp)==3) { 
										$tempor = $dbc->database_fetch_assoc($dbc->database_query("SELECT * FROM ".$interp[0]." WHERE ".$interp[1]."='".$row[$key]."'"));
										echo $tempor[$interp[2]];
									} else {
										echo $row[$key];
									}
									echo "</td>";
								}
							} else {
								echo "<td>&nbsp;</td>";
							} 
						}
    } ?>
    </tr>
<?php
} ?>
</table>
