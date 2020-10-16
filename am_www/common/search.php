<?php
session_start();
require_once '../db/mysqlcon.php';
include_once '../common/pframe.php';
require_once '../lang/lang_'.$GLOBALS['lang'].'.inc';
$myframe=new pframe();
$myframe->header(View_Ads);


?><script type="text/javascript" src="../cms/jquery.highlight.js"></script><? 
    
    if($_REQUEST['q']){
	    $myframe->open_box("withTree", Search_Results,"panel", $adding);

	    $i=0;
	    $res = $_SESSION['search_result']['val']; //print_r($_SESSION['search_result']['val']);//echo "<br/><br/>".$_SESSION['search_result']['val'][0]. $_SESSION['search_result']['tbl'][0].$_SESSION['search_result']['span'][0];

	    $arr_tbl = $_SESSION['search_result']['tbl'];//Table of each result. came from the page search_sql.php
	    $span_tbl = $_SESSION['search_result']['span'];
	    $ttl= $GLOBALS['lang'] == 'ar'?'title_ar':'title_en';
	    
	    foreach($res as $val){
	    	$v='c';
	    	//SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = 'hadara' AND TABLE_NAME = 'product'  limit 3

	    	// bringing NID of the element accoding to its TITLE

	    	// + building the link which the result take you to.

	    	$id_col=get_data_in("SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = '".DB_NAME."' AND TABLE_NAME = '".$arr_tbl[$i]."'   limit 1 ", "COLUMN_NAME");
	    	$ttl_col=get_data_in("SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = '".DB_NAME."' AND TABLE_NAME = '".$arr_tbl[$i]."' and COLUMN_NAME like '%{$ttl}%'  limit 1 ", "COLUMN_NAME");
	    	$NID=get_data_in("SELECT {$id_col} FROM {$arr_tbl[$i]} WHERE {$ttl_col}='{$val}' ", $id_col);
	    	
	    	$page=$arr_tbl[$i]=='category'?$arr_tbl[$i].".php":$arr_tbl[$i]."s.php";
	    	
	    	?><p class="p_res"> > <a class="search_result_a"  style="<? if($_REQUEST['q'] == $val){echo "color:#7b03bc;font-weight:bold;border:1px #000 solid;padding:4px 10px";}?>"
	    	 href="../pages/<?=$page?>?v=<?=$v?>&NID=<?=$NID?>"><?=$val?></a><?=$span_tbl[$i]?></p>	<?
	    	$i++;
	    }
	    
	    $myframe->close_box("panel");
	    
	    ?>
	    <script>
	    </script>
	    <? 
    }
    
?>