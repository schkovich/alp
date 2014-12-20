<?php
global $dbc;
    if(empty($_GET['mod'])&&empty($_GET['q'])) {
        if($this->_permissions['add']>0||!empty($this->_notes['add'])) {
            begitem('add '.$this->_singular); 
            if(!empty($this->_notes['add'])) {
                echo $this->_notes['add'].'<br /><br />';
            } 
?>
                            <table border="0" cellpadding="4" cellspacing="4" width="420" align="center" class="centerd"><tr><td>
<?php
            if($this->_permissions['add']>0) {
                $this->startform(get_script_name(),'POST');
                $this->add_hidden('type','add');
                if(!empty($_GET['id'])) $this->add_hidden('_hidden_id',$_GET['id']);
                    $this->display_elements();
                    $this->endform('add '.$this->_singular); 
                } 
?>
			</td></tr></table>
<?php
				enditem('add '.$this->_singular);
            }
    }
    if(empty($this->_delmod_query)) {
        $query = 'SELECT * FROM '.$this->_table_name;
        if(!empty($_GET['mod'])&&!empty($_GET["q"])) {
            $query .= ' WHERE '.$this->_id."=".(int)$_GET["q"];
        } else {
            if(!empty($this->_order)) $query .= ' ORDER BY '.$this->_order;
        }
    } else {
        $query = $this->_delmod_query;
        if(!empty($_GET['mod'])&&!empty($_GET['q'])) {
            $query .= (stristr($query,'WHERE')?' AND ':' WHERE ').$this->_id.'='.(int)$_GET['q'];
        }
    }

	$total_rows = $dbc->database_num_rows($dbc->database_query($query));
	if($this->_permissions['mod']==2) {
		include_once 'include/cl_pager.php';
		$pager = new pager();
		$pager->change_per_var(25);
		// pager limits
		$query .= ' LIMIT ';
		$query .= (!empty($_GET[$pager->get_GET_start_var()]) ? $_GET[$pager->get_GET_start_var()] : 0);
		$query .= ',';
		$query .= (!empty($_GET[$pager->get_GET_per_var()]) && $_GET[$pager->get_GET_per_var()] <= 100 ? $_GET[$pager->get_GET_per_var()] : $pager->get_per_var());
	}
    $data = $dbc->database_query($query);
    if(empty($_GET['mod'])&&empty($_GET['q'])) {
        if($dbc->database_num_rows($data)) {
            if(($this->_permissions['mod']==1&&!$this->_permissions['del']&&$dbc->database_num_rows($data)>1) || ($this->_permissions['mod']==1&&$this->_permissions['del']==1) || ($this->_permissions['del']&&$this->_permissions['mod']==2)) {
                if($this->_permissions['mod']==1||$this->_permissions['del']==1||!empty($this->_notes['del'])) {
                    begitem(($this->_permissions['mod']==1?'modify ':'delete ').$this->_singular);
                    if($this->_permissions['del']&&!empty($this->_notes['del'])) {
                        echo $this->_notes['del'].'<br /><br />';
                    } 
?>
					<table border="0" cellpadding="4" cellspacing="4" width="420" align="center" class="centerd"><tr><td>
<?php
					if($this->_permissions['del']||$this->_permissions['mod']==1) {
						if($this->_permissions['del']) {
                            $this->startform(get_script_name(),'POST');
                            $this->add_hidden('type','del');
                            if(!empty($_GET['id'])) $this->add_hidden('_hidden_id',$_GET['id']);
                        }
?>
					<table width="99%" cellpadding="2" cellspacing="0" border="0">
<?php
						if($this->_permissions['del']) { 
?>
                                                <tr>
                                                    <td><div align="center"><b>delete?</b></div></td>
											<td<?php if($this->_permissions['mod']==1) { ?> colspan="2"<?php } ?>>&nbsp;</td>
										</tr>
<?php
                        }
                        $counter = 0;
                        while($row = $dbc->database_fetch_array($data)) { ?>
                                                            <tr<?php echo ($counter%2==0?" bgcolor=\"".$colors['cell_alternate']."\"":''); ?>>
											<?php if($this->_permissions['del']) { ?><td><div align="center"><nobr><input type=checkbox name="<?php echo $row[$this->_id]; ?>" value=1 class=radio></div></td><?php } ?>
											<td><?php echo $row[$this->_order]." <font class=\"sm\" color=\"".$colors['blended_text']."\">[".$row[$this->_id].']</font></b>'; ?></td>
											<?php if($this->_permissions['mod']==1) { ?><td><div align="center"><nobr>[<a href="<?php echo get_script_name(); ?>?mod=1&q=<?php echo $row[$this->_id]; ?>"  title="modify <?php echo $row[$this->_order]; ?>"><b>modify</b></a>]</div></td><?php } ?>
										</tr>
										<?php
										$counter++;
									}
									if($this->_permissions['del']) { ?>
										<tr>
											<td><?php $this->endform('delete',0); ?></td>
											<td<?php if($this->_permissions['mod']==1) { ?> colspan="2"<?php } ?>>&nbsp;</td>
										</tr>
										<?php
									} ?>
									</table>
									<?php
								} ?>
								</td></tr></table>
								<?php
								enditem(($this->_permissions['mod']==1?'modify ':'delete ').$this->_singular);
								$dbc->database_data_seek($data,0);
							} 
						}
					} elseif(!$this->_permissions['add']&&$this->_permissions['del']) {
							echo 'there are no '.$this->_name.' in the database.<br /><br />';
					}
				}
				if((!empty($_GET['mod'])&&!empty($_GET["q"])&&$this->_permissions['mod']==1)||(empty($_GET['mod'])&&empty($_GET['q'])&&$this->_permissions['mod']==2)||($this->_permissions['mod']&&!$this->_permissions['del']&&$dbc->database_num_rows($data)==1)) {
					if($this->_permissions['mod']||!empty($this->_notes['mod'])) {
						if($dbc->database_num_rows($data)) {
							if($this->_permissions['mod']==2) {
								begitem('modify '.$this->_name);
							} elseif($this->_permissions['mod']==1) {
								begitem('modify '.($dbc->database_num_rows($data)>1?'specific ':'').$this->_singular); 
							}
							if(!empty($this->_notes['mod'])) {
								echo $this->_notes['mod'].'<br /><br />';
							} ?>
							<table border="0" cellpadding="4" cellspacing="4" width="420" align="center" class="centerd"><tr><td>
							<?php
							if($this->_permissions['mod']==2) {
								echo $pager->display_numeric_links($URL_handler, $total_rows).'<br />';
								$this->startform(get_script_name(),'POST');
								$this->add_hidden('type','mod');
								if(!empty($_GET['id'])) $this->add_hidden('_hidden_id',$_GET['id']);
								$counter = 1; ?>
								<table border="0" cellpadding="4" cellspacing="2" width="100%">
								<?php
								while($row = $dbc->database_fetch_assoc($data)) { ?>
									<tr>
										<td>
										<?php start_module(); ?>
										<font color="<?php echo $colors["primary"]; ?>"><b><?php echo $row[$this->_order]; ?></b></font><br />
										<?php $this->add_hidden($counter."_".$this->_id,$row[$this->_id]); ?>
										<?php $this->display_elements($row,$counter,1); ?>
										<?php end_module(); ?>
										</td>
									</tr>
									<?php
									$counter++;
								} ?>
								</tr></td></table>
								<?php
								$this->endform('modify '.$this->_name); 
							} elseif($this->_permissions['mod']==1) { ?>
								<table border=0 cellpadding=3 cellspacing=0 width="100%">
								<?php
								while($row = $dbc->database_fetch_assoc($data)) { ?>
									<tr><td>
									<?php
									$this->startform(get_script_name(),'POST');
									//if(!$this->element_exists($this->_order)||!$this->element_is_modifiable($this->_order)) { ?>
									<table border=0 cellpadding=0 cellspacing=0 width="100%"><tr><td>
										<a name="<?php echo $row[$this->_id]; ?>"></a><?php echo ($this->element_exists($this->_order)?$this->element_description($this->_order).": ":(!empty($this->_order)?$this->_order.": ":"")); ?><?php echo (!empty($row[$this->_order])?"<b>".$row[$this->_order]:""); ?>
									</td><td align="right">
										<?php 
										if($this->_permissions['add']||$this->_permissions['del']) {
											echo ' <font class="sm" color="'.$colors['blended_text'].'">['.$row[$this->_id].']</font></b><br />';
										} else { echo '&nbsp;'; } ?>
									</td></tr>
									</table>
										<img src="img/pxt.gif" width="1" height="5" border="0" alt="" /><br /><?php
									//}
									$this->add_hidden('type','mod');
									if(!empty($this->_id)) $this->add_hidden($this->_id, $row[$this->_id]);
									if(!empty($_GET['id'])) $this->add_hidden('_hidden_id',$_GET['id']);
									if($this->_permissions['add']||$this->_permissions['del']) {
										$temp = 2;
									} else {
										$temp = $dbc->database_num_rows($data);
									}
									$this->add_hidden('_one_row_only',$temp);
									$this->display_elements($row);
									$this->endform('modify '.$this->_singular); ?>
									<br />
									</td></tr>
									<?php
								} ?>
								</table>
								<?php
							} ?>
							</td></tr></table>
							<?php
							if($this->_permissions['mod']==2) {
								enditem('modify '.$this->_name);
							} elseif ($this->_permissions['mod']==1) {
								enditem('modify '.($dbc->database_num_rows($data)>1?'specific ':'').$this->_singular); 
								if(!empty($_GET['mod'])&&!empty($_GET['q'])) { ?>
									<div align="right"><font color="<?php echo $colors['blended_text']; ?>">[<a href="<?php echo get_script_name(); ?>">back to all <?php echo $this->_name; ?></a>]</font></div>
									<?php
								}
							}
						}
					}
				}
				if (empty($_GET['mod'])&&empty($_GET['q'])) {
					if($this->_permissions['update']||!empty($this->_notes['update'])) {
						begitem($this->_name);
						if (!empty($this->_notes['update'])) {
							echo $this->_notes['update'].'<br /><br />';
						} ?>
						<table border="0" cellpadding="4" cellspacing="4" width="420" class="centerd"><tr><td>
						<?php
						if ($this->_permissions['update']) {
							$this->startform(get_script_name(),'POST');
							$this->add_hidden('type','update');
							if(!empty($_GET['id'])) $this->add_hidden('_hidden_id',$_GET['id']);
							$this->display_elements();
							echo '<br />';
							$this->endform($this->_singular); 
						} 
?>
						</td></tr></table>
<?php
						enditem($this->_name);
					}
				}
?>