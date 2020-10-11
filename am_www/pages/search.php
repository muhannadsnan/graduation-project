<?php 
include_once '../db/mysqlcon.php';
include_once '../common/pframe.php';
require_once("../cms/grid/conf.php");

$myframe=new pframe();
$myframe->header();

/*****/IS_LOGGED_IN();/*****/

switch ($_REQUEST['v']){
	case 's':
		echo "222";
			?>
			
			<?php
	break;
}
$myframe->footer();