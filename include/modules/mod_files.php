<?php
require_once 'include/cl_display_dir.php';
?>
<table border="0" cellpadding="0" cellspacing="0" width="100%">
	<tr>
		<td valign="top" width="100%"><a href="files.php"><b>files</b></a>&nbsp;</td>
		<td align="right" class="sm"><a href="files_list.php?type=0" target="FILES" style="color: <?php echo $colors['blended_text']; ?>">files</a>&nbsp;|&nbsp;<br /><font color="<?php echo $colors['blended_text']; ?>">x<b><?php echo number_of_files(0); ?></b></font>&nbsp;|&nbsp;</td>
		<td align="right" class="sm"><a href="files_list.php?type=1" target="FILES" style="color: <?php echo $colors['blended_text']; ?>">pictures</a>&nbsp;|&nbsp;<br /><font color="<?php echo $colors['blended_text']; ?>">x<b><?php echo number_of_files(1); ?></b></font>&nbsp;|&nbsp;</td>
		<td align="right" class="sm"><a href="files_list.php?type=2" target="FILES" style="color: <?php echo $colors['blended_text']; ?>">screenshots</a>&nbsp;|&nbsp;<br /><font color="<?php echo $colors['blended_text']; ?>">x<b><?php echo number_of_files(2); ?></b></font>&nbsp;|&nbsp;</td>
		<td align="right" class="sm"><a href="files_list.php?type=3" target="FILES" style="color: <?php echo $colors['blended_text']; ?>">demos</a><br /><font color="<?php echo $colors['blended_text']; ?>">x<b><?php echo number_of_files(3); ?></b></font></td>
	</tr>
</table>
<iframe src="files_list.php?type=0" name="FILES" height="80" scrolling="yes" frameborder="0" style="width: 100%;
	scrollbar-3dlight-color: <?php echo $colors['cell_title']; ?>;
	scrollbar-arrow-color: <?php echo $colors['cell_title']; ?>;
	scrollbar-base-color: <?php echo $colors['cell_background']; ?>;
	scrollbar-darkshadow-color: <?php echo $colors['cell_background']; ?>;
	scrollbar-face-color: <?php echo $colors['cell_background']; ?>;
	scrollbar-highlight-color: <?php echo $colors['text']; ?>;
	scrollbar-shadow-color: <?php echo $colors['blended_text']; ?>;
	scrollbar-track-color: <?php echo $colors['cell_title']; ?>"></iframe>