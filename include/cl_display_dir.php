<?php
function display_dir($dir,$label=1)
{
	$types = array(
		array('other','important files'),
		array('pictures','pictures'),
		array('screenshots','game screenshots'),
		array('demos','game demos'),
		array('benchmarks','screenshots')); 
	if (!empty($types[$dir])) {
		if ($handle = @opendir(getcwd().'/files/'.$types[$dir][0].'/')) { 
			$counter = 0;
			while (false!== ($file = @readdir($handle))) {
				if($file != '.' && $file != '..') $counter++;
			}
			if ($label) { ?>
				<b><?php echo $types[$dir][1]; ?></b>:<br />
				<?php
			} 
			if ($counter!=0) { ?>
				<table border="0" cellpadding="0" cellspacing="1" width="100%">
				<?php 
			}
			$totalfiles = 0;
			rewinddir($handle);
			while (false!== ($file = @readdir($handle))) {
				$filesize = @filesize('files/'.$types[$dir][0].'/'.$file)/1024;
				$totalfiles += $filesize;
				$filesize = round($filesize,2);
				$holder = ' KB';
				if ($filesize>1024) {
					$filesize = round($filesize/1024,2);
					$holder = ' MB';
				} elseif ($filesize==0) {
					$filesize = '<b>folder</b>';
					$holder = '';
				}
				if ($file != '.' && $file != '..' && $file != 'CVS') { ?>
					<tr class="sm"><td><?php if($label) { ?>&nbsp;&nbsp;&nbsp; <?php } ?><a href="files/<?php echo $types[$dir][0]; ?>/<?php echo $file; ?>"<?php echo (!$label?" target=\"_top\"":""); ?>><?php echo (!$label&&strlen($file)>23?substr($file,0,23)."...":$file); ?></a> </td><td width="50%">&nbsp;&nbsp;<font color="<?php echo $colors['blended_text']; ?>"><?php echo $filesize.$holder; ?></font><br /></td></tr>
					<?php
				}
			}
			$totalfiles = round($totalfiles/1024,2);
			@closedir($handle);
			if ($counter!=0) { ?>
				</table>
				<br />
				<?php 
			} else { ?>
				<span class="sm"><br />no <?php echo $types[$dir][1]; ?> found.<br /><br /></span>
				<?php
			}
		}
	} else {
		echo 'that directory is not accessible.<br />';
	}
}
function number_of_files($dir)
{
	$types = array(
		array('other','important files'),
		array('pictures','pictures'),
		array('screenshots','game screenshots'),
		array('demos','game demos'),
		array('benchmarks','screenshots')); 
	if (!empty($types[$dir])) {
		if($handle = @opendir(getcwd().'/files/'.$types[$dir][0].'/')) { 
			$counter = 0;
			while (false!== ($file = @readdir($handle))) {
				if ($file != '.' && $file != '..' && $file != 'CVS') $counter++;
			}
			return $counter;
		}
	} else {
		return 0;
	}
} ?>