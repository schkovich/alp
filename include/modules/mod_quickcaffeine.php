<form action="caffeine.php" method="POST" name="caffeine">
<input type="hidden" name="type" value="quickadd" />
<input type="hidden" name="quantity" value="1" />
<font class="sm"><b>quick caffeine</b>:<br /></font>
<select name="id" style="font-size: 10px; width: 260px">
<?php
while($row = $dbc->database_fetch_assoc($data)) {
	$temp = $dbc->database_fetch_assoc($dbc->database_query("SELECT * FROM caffeine_items WHERE id='".$row["caffeine_id"]."'"));
	$type = $dbc->database_fetch_assoc($dbc->database_query("SELECT name FROM caffeine_types WHERE id='".$temp["ttype"]."'"));
	echo "<option value=\"".$temp["id"]."\">".$type["name"]." :: ".$temp["name"]."</option>";
} ?>
</select>
&nbsp;amount:<input type="text" value="12" size="1" maxlength="3" name="oz" /><input type="radio" name="oztype" value="0" checked class="radio" /> oz <input type="radio" value="1" name="oztype" class="radio" /> L&nbsp;<input type="submit" value="add" style="font-size: 10px" class="formcolors" />
</form><div align="right"><font class="sm">[<a href="caffeine.php"><b>go to caffeine log</b></a>]</font></div>
