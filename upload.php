<?php
require_once 'include/_universal.php';
$x = new universal('file upload','',1);
if ($x->is_secure() && $toggle['uploading']) { 
	$x->display_top();
	if (empty($_POST)) {
		if (!empty($_GET) && !empty($_GET['type'])) {
			$selector = preg_replace('/[^a-z]/','',utf8_decode($_GET['type']));
		} else {
			$selector = '';
		}
		if (!empty($_GET) && !empty($_GET['files'])) {
			$num_of_files = (int)$_GET['files'];
		} else {
			$num_of_files = 1;
		}
		begitem('upload files'); ?>
		<form action="upload.php" method="GET">
		<input type="hidden" name="type" value="<?php echo $selector; ?>">
		upload: <select name="files" style="width: 200px; font: 10px Verdana;">
		<?php 
		for($i=1;$i<=10;$i++) { ?>
			<option value="<?php echo $i; ?>"<?php echo ($num_of_files==$i?' selected':''); ?>><?php echo $i; ?> file<?php echo ($i>1?'s':''); ?></option>
			<?php
		} ?>
		</select>
		<input type="submit" value="go" style="font: 10px Verdana;">
		</form>
		<table border=0 cellpadding=4 cellspacing=4 width="99%"><tr><td colspan=2>
		<b>no pornography</b>.  please only upload files documenting or pertaining to the lan party.  keep the total size of all files combined less than <?php echo round($master['max_file_upload_size']/1048576,1); ?> MB.  you will <b>not</b> have permission to delete or modify files that have been uploaded.  uploaded images must be in the GIF format, the JPEG format, or the PNG format.<br />
		<br />
		all benchmarks will be automatically re-named.  you may rename any non-benchmark files below (the extension will be automatically added).  benchmark names are required for any benchmark files.<br />
		<br />
		<form action="upload.php" method="POST" enctype="multipart/form-data">
		<input type="hidden" name="MAX_FILE_SIZE" value="<?php echo $master['max_file_upload_size']; ?>">
		<input type="hidden" name="num_of_files" value="<?php echo $num_of_files; ?>">
		</td></tr>
		<?php
			for($i=0;$i<$num_of_files;$i++) { ?>
				<tr><td width="100%" valign="top">
				<font size=1><b>file</b> <?php echo ($num_of_files!=1?'(#'.($i+1).')':''); ?> <font color="<?php echo $colors['primary']; ?>">(required)</font><br /></font>
				<input type="file" name="userfile[]" style="width: 99%"><br />
				<img src="img/pxt.gif" width="1" height="4" border="0" alt="" /><br />
				<font size=1><b>file name</b> <?php echo ($num_of_files!=1?'(#'.($i+1).')':''); ?> (can only contain letters, numbers, and underscores) <font color="<?php echo $colors['blended_text']; ?>">(extension will be added)</font><br /></font>
				<input type="text" name="file_name_<?php echo $i; ?>" style="width: 99%" maxlength="255"><br />
				</td><td valign="top">
				<font size=1><b>file type</b> <?php echo ($num_of_files!=1?'(#'.($i+1).')':''); ?> <font color="<?php echo $colors['primary']; ?>">(required)</font><br /></font>
				<select name="file_type_<?php echo $i; ?>" style="width: 200px; font: 10px Verdana;">
				<option value=""></option>
				<?php
				$types = array(
					'pictures'    => 'lan picture',
					'demos'       => 'game demo (motion playback)',
					'screenshots' => 'game screenshot',
					'benchmarks'  => 'benchmark screenshot'
				);
				foreach($types as $key => $val) { ?>
					<option value="<?php echo $key; ?>"<?php echo ($selector==$key?' selected':''); ?>><?php echo $val; ?></option>
					<?php
				} ?>
				</select><br />
				<img src="img/pxt.gif" width="1" height="4" border="0" alt="" /><br />
				<font size=1><nobr><b>benchmark</b> <?php echo ($num_of_files!=1?"(#".($i+1).")":""); ?> <font color="<?php echo $colors['primary']; ?>">(required for bench screenshot)</font><br /></font>
				<select name="benchmark_name_<?php echo $i; ?>" style="width: 200px; font: 10px Verdana;"><option value=""></option>
				<?php
				$data = $dbc->query('SELECT benchid FROM users_benchmarks WHERE userid='.(int)$_COOKIE['userid']);
				while ($row = $data->fetchRow()) { 
					$bench_name = $dbc->queryOne('SELECT name FROM benchmarks WHERE id='.(int)$row['benchid']); ?>
					<option value="<?php print preg_replace('/[^a-zA-Z0-9_ ]/','',utf8_decode($bench_name)); ?>"><?php echo preg_replace('/[^a-zA-Z0-9_ ]/','',utf8_decode($bench_name)); ?></option>
					<?php
				} ?>
				</select>
				</td></tr>
				<?php
			} ?>
		<tr><td colspan=2>
		<br />
		<div align="right"><input type="submit" value="upload file<?php echo ($num_of_files>1?'s':''); ?>" style="width:160px"></div>
		</form>
		</td></tr></table>
		<?php
		enditem('upload files'); ?>
		<div align="right">[<a href="files.php"><font style="font-size: 11px"><b>back to files</b></font></a>]</div>
		<?php
	} else {
		require_once 'include/cl_validation.php';
		$valid = new validate();
	
		if ($valid->is_empty('num_of_files','there was an unexpected error and your file(s) could not be uploaded.')) {	
			if (empty($_FILES['userfile']['name'][0]) || $_FILES['userfile']['error'][0] == 4) {
				$valid->add_error('please input at least one file to be uploaded.');
			}
			
			for ($i=0;$i < $valid->get_value('num_of_files'); $i++) {
				if (!empty($_FILES['userfile']['name'][$i]) && $_FILES['userfile']['error'][$i] != 4) {
					$valid->is_empty('file_type_'.$i,'every uploaded file must have a file type to go along with it.');
					if ($valid->get_value('file_type_'.$i) == 'benchmarks') {
						$valid->is_empty('benchmark_name_'.$i,'for every benchmark screenshot, you must list the name of the benchmark.');
					}
					$picture_extensions = array('gif','jpeg','jpg','png');
					if ($valid->get_value('file_type_'.$i) == 'pictures' || $valid->get_value('file_type_'.$i) == 'screenshots' || $valid->get_value('file_type_'.$i) == 'benchmarks') {
						$good = false;
						for ($j=0; $j < sizeof($picture_extensions); $j++) {
							if (substr(strtolower($_FILES['userfile']['name'][$i]), strrpos($_FILES['userfile']['name'][$i],'.')+1) == $picture_extensions[$j])
								$good = true;
						}
						if ($good != true) { $valid->add_error('file number '.($i+1).' does not have a valid file extension.<br />'); }
					
						if ($_FILES['userfile']['size'][$i] > $master['max_file_upload_size'] || $_FILES['userfile']['error'][$i] == 1 || $_FILES['userfile']['error'][$i] == 2) {
							$valid->add_error('that file is much too big to upload.');
						}
					} elseif ($valid->get_value('file_type_'.$i) == 'demos') {
						if (substr($_FILES['userfile']['name'][$i], strrpos($_FILES['userfile']['name'][$i],'.')+1) != 'dem') {
							$valid->add_error('your file type does not match the file extension of the file you uploaded.');
						}
						if ($_FILES['userfile']['size'][$i]>$master['max_file_upload_size'] || $_FILES['userfile']['error'][$i] == 1 || $_FILES['userfile']['error'][$i] == 2) {
							$valid->add_error('that file is much too big to upload.');
						}
					}
					else
						$valid->add_error('a valid file type was not specified for file #'.($i+1).'.');

					if ($_FILES['userfile']['error'][$i] == 3) {
						$valid->add_error('your file was only partially uploaded.  try again.');
					}
				}
			}
		}

		if (!$valid->is_error()) {
            // TODO: check permissions for file uploads before even trying anything like this...
            // outputs php warnings etc from move_uploaded_file()
			for ($i=0; $i < $valid->get_value('num_of_files'); $i++) {
				if (!empty($_FILES['userfile']['name'][$i]) && $_FILES['userfile']['error'][$i] != 4) {
					if ($valid->get_value('file_type_'.$i) == 'benchmarks') {
						$uploaded_name = preg_replace('/[^a-zA-Z0-9_ ]/','',utf8_decode($userinfo["username"]."_".$valid->get_value("benchmark_name_".$i))).substr($_FILES['userfile']['name'][$i], strrpos($_FILES['userfile']['name'][$i],"."));
					} else {
						if ($valid->get_value('file_name_'.$i) == '') {
							$uploaded_name = preg_replace('/[^a-zA-Z0-9_]/','',utf8_decode($_FILES['userfile']['name'][$i]));
						} else {
							$uploaded_name = preg_replace('/[^a-zA-Z0-9_]/','',utf8_decode($valid->get_value('file_name_'.$i))).substr($_FILES['userfile']['name'][$i], strrpos($_FILES['userfile']['name'][$i],'.'));
						}
					}
					if (move_uploaded_file($_FILES['userfile']['tmp_name'][$i],getcwd().'/files/'.$valid->get_value('file_type_'.$i).'/'.$uploaded_name)) {
						echo 'file number (#'.($i+1).') ('.$valid->get_value('file_type_'.$i).') was successfully uploaded.<br />';
					} else {
						echo 'there was an error and file #".($i+1)." was not successfully uploaded.<br />';
					}
				}
			}
			echo '<br /> &gt; <a href="upload.php?files='.(int)$valid->get_value('num_of_files').'">upload more files</a>.<br /><br />';
		} else {
			$valid->display_errors();
		}
	}
	$x->display_bottom();
} else {
	$x->display_slim('you are not authorized to view this page.');
}
?>