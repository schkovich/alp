<?php
$x_mem_types = array(
	"FPM"=>"FPM", 
	"EDO"=>"EDO", 
	"PC66 SDRAM"=>"PC66 SDRAM", 
	"PC100 SDRAM"=>"PC100 SDRAM", 
	"PC133 SDRAM"=>"PC133 SDRAM", 
	"PC150 SDRAM"=>"PC150 SDRAM", 
	"PC1600 DDR SDRAM"=>"PC1600 DDR SDRAM", 
	"PC2100 DDR SDRAM"=>"PC2100 DDR SDRAM", 
	"PC2400 DDR SDRAM"=>"PC2400 DDR SDRAM", 
	"PC2700 DDR SDRAM"=>"PC2700 DDR SDRAM",
	"PC3000 DDR SDRAM"=>"PC3000 DDR SDRAM", 
	"PC3200 DDR SDRAM"=>"PC3200 DDR SDRAM", 
	"PC3500 DDR SDRAM"=>"PC3500 DDR SDRAM", 
	"PC4000 DDR SDRAM"=>"PC4000 DDR SDRAM", 
	"800 RDRAM"=>"800 RDRAM", 
	"1066 RDRAM"=>"1066 RDRAM", 
	"Other..." => "Other...");

$x_mem_sizes = array(
	"32"=>"32MB",
	"64"=>"64MB",
	"128"=>"128MB");
for($i=256;$i<=4096;$i+=128) {
	$x_mem_sizes[$i] = $i." MB";
}

$x_storage = array();
for($i=20;$i<=5000;$i+=20) {
	$x_storage[$i] = $i." GB";
}
$x_processors = array(
	"AMD" => "AMD",
	"Apple (IBM PowerPC)" => "Apple (IBM PowerPC)",
	"Apple (Intel)" => "Apple (Intel)",
	"Cyrix" => "Cyrix",
	"IDT" => "IDT",
	"Intel" => "Intel",
	"NEC" => "NEC",
	"Transmeta" => "Transmeta",
	"VIA" => "VIA",
	"Other..." => "Other..."
);

$x_gpus = array(
	"ATI" => "ATI",
	"Intel" => "Intel",	
	"Nvidia" => "Nvidia",
	"VIA/S3" => "VIA/S3",
	"Other..." => "Other..."
);

// keyword searches
/*
$x_ati = array('ati','radeon','rage','xpert','firegl');
$x_nvidia = array('geforce','gefroce','gf4','gf3','annihilator','vidia','quadro','nforce','n-force','tnt');

function logo_return($keywords, $field, $userid) {
	global $dbc;
	$bool = false;
	if(sizeof($keywords)>0) {
		foreach($keywords as $val) {
			if($dbc->database_num_rows($dbc->database_query("SELECT * FROM users WHERE ".$field." LIKE '%".$val."%' AND userid='".$userid."'"))) {
				$bool = true;
			}
		}
	}
	return $bool;
}
*/
?>