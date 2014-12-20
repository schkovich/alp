<?php
// IF YOU ARE MAKING A CUSTOM SKIN, PLEASE INCLUDE WHAT VERSION OF ALP IT WAS MADE FOR.
// ie: this custom skin was made for ALP 0.97.2d

// colors (can also accept standard html name colors such as pink or green)
$colors['background'] = '#2D2D3A';
$colors['primary'] = '#E8860C';
$colors['secondary'] = '#804F01';
$colors['border'] = '#4C4D5C';
$colors['border_alternate'] = '#181821'; // currently only used on bargraph borders
$colors['cell_title'] = '#595B68';
$colors['cell_background'] = '#2D2D3A';
$colors['cell_alternate'] = '#282832';
$colors['text'] = '#9999AC';
$colors['blended_text'] = '#767A8A';
$colors['graphs'] = $colors['primary'];
$colors['alert'] = '#ff0000';

$colors['image_text'] = 'white'; // ONLY white or black

// images
$images['background'] = '';
$images['title'] = 'title.gif';
$images['arrow_on'] = 'phpwcms_arrow_off.gif';
$images['arrow_off'] = 'phpwcms_arrow.gif';
$images['dotted_line'] = 'dotted_line.gif';
$images['empty_bargraph_background'] = 'emptybargraphbg.gif';
$images['go'] = 'white_go.gif';

// widths of right and left columns
$container['leftmodule'] = 200;
$container['rightmodule'] = 200;
$container['indexmodule'] = 250;

// padding (spacing inbetween modules and columns)
$container['horizontalpadding'] = 10;
$container['verticalpadding'] = 10;

// module padding (spacing inside the module between content and border)
$container['horizontalmodulepadding'] = 8;
$container['verticalmodulepadding'] = 3;

// border size: these are used only if mod****.gif images do not exist in the current skin directory
$container['border_width'] = 1;
$container['border_height'] = 1;

// seating chart colors
$seat['background'] = $colors['background']; //'#555555';
$seat['border'] = '#FFFFFF';
$seat['tablecolor'] = $colors['secondary'];
$seat['tableborder'] = $colors['primary'];
$seat['voidcolor'] = $colors['cell_title']; //$colors['cell_background'];
$seat['gridcolor'] = '#CCCCCC';
$seat['currentcolor'] = $colors['primary'];
$seat['occupied'] = $colors['text'];
$seat['reserved'] = '#FF9900';

// display title menu items on the left, right, or center.
$container['title_menu'] = 'left';

// display index.php modules on left or right of body.
$container['index_modules'] = 'right';

// these are the modules displayed.  if you delete a module from below, it will not show up on ALP.
// this also controls the order of the modules.

// array(location, type)
// location options: 'left', 'right', or 'main'
// type options: 'left', 'right', or 'main'
$modulelist['mod_controlpanel'] = array('left','main');
$modulelist['mod_register'] = array('left','main');
$modulelist['mod_admincontrolpanel'] = array('left','main');
$modulelist['mod_guides'] = array('left','main');
if(ALP_TOURNAMENT_MODE) $modulelist['mod_news'] = array('left','main');
$modulelist['mod_schedule'] = array('left','main');
$modulelist['mod_tournaments'] = array('left','main');
$modulelist['mod_polls'] = array('left','main');
?>
