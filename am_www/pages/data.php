<?php 
include_once '../common/pframe.php';
include_once '../db/mysqlcon.php';
include_once '../cms/grid/conf.php';


$myframe=new pframe();
$myframe->header();

/*****/IS_LOGGED_IN();/*****/

if($_REQUEST['v'] == 'd')
{
	$dg = new C_DataGrid("SELECT * FROM person", "pid", "person");
	// change column titles
	$dg->set_col_title("gid", gid);
	$dg->set_col_title("name", name);
	$dg->set_col_title("father", father);
	$dg->set_col_title("mother", mother);
	
	//$dg->enable_autowidth(true)->enable_autoheight(true);
	//$dg->set_pagesize(200); // need to be a large number
	//$dg->set_scroll(true);
	//$dg->enable_kb_nav(true);
	//$dg->set_col_hidden('comments');
	//$dg->enable_edit('INLINE', 'CRUD');
	$dg -> display();
}
?>
<style>
    .ui-jqgrid-bdiv input{width:100%;}
    .pg_notify{display:none;}    
</style>
<?php

$myframe->footer();