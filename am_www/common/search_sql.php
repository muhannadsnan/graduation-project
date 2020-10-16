<?php
ob_start();
session_start();
require_once '../db/mysqlcon.php';
include_once '../common/pframe.php';
$myf=new pframe();

if($_GET['key']) $key=$_GET['key'];
elseif($_GET['typeahead']) $key=$_GET['typeahead'];

//collect many keywords to search
$pieces = explode(" ", $key);
$key="";
foreach($pieces as $val){
	$key .= $val."|";
}
$key=rtrim($key, "|"); //echo $key;

// search keywords through these tables : product, news, album, picture, videos, service, job, faq, category
$sql = "
(Select prod_title_ar as ttl_ar,prod_title_en as ttl_en,prod_desc_ar as desc_ar,prod_desc_en as desc_ar ,'product' as tbl from product where lower(Concat(prod_title_ar, '', prod_title_en, '', prod_desc_ar, '', prod_desc_en, '' )) REGEXP '$key')
UNION
(Select news_title_ar,news_title_en,news_desc_ar,news_desc_ar ,'news' as tbl from news where lower(Concat(news_title_ar, '', news_title_en, '', news_desc_ar, '', news_desc_en, '' )) REGEXP '$key')
UNION
(Select album_title_ar,album_title_en,'-','-' ,'album' as tbl from album where lower(Concat(album_title_ar, '', album_title_en, '')) REGEXP '$key')
UNION
(Select vid_title_ar,vid_title_en,vid_desc_en,vid_desc_en ,'video' as tbl from video where lower(Concat(vid_title_ar, '', vid_title_en, '', vid_desc_ar, '', vid_desc_en, '')) REGEXP '$key')
UNION
(Select srv_title_ar,srv_title_en,srv_desc_ar,srv_desc_en ,'service' as tbl from service where lower(Concat(srv_title_ar, '',srv_title_en, '', srv_desc_ar, '', srv_desc_en, '')) REGEXP '$key')
UNION
(Select pic_title_ar,pic_title_en,'-','-' ,'picture' as tbl from picture where lower(Concat(pic_title_ar, '',pic_title_en, '')) REGEXP '$key')
UNION
(Select job_title_ar,job_title_en,job_desc_ar,job_desc_en ,'job' as tbl from job where lower(Concat(job_title_ar, '', job_title_en, '', job_desc_ar, '', job_desc_en, '')) REGEXP '$key')
UNION
(Select faq_title_ar,faq_title_en,faq_desc_ar,faq_desc_en ,'faq' as tbl from faq where lower(Concat(faq_title_ar, '',faq_title_en, '', faq_desc_ar, '', faq_desc_en, '')) REGEXP '$key')
UNION
(Select cat_title_ar,cat_title_en,'-','-' ,'category' as tbl from category where lower(Concat(cat_title_ar, '',cat_title_en, '')) REGEXP '$key') order by 1";

 //echo "<br/>".$sql."<br/>";

$lang = $GLOBALS['lang'] == 'ar' ? 'ar' : 'en';
$ttl_lang = 'ttl_'.$lang;
$query=table($sql);
$res = array();
$span_tbl = array();
$arr_tbl = array();
$i=0;

//collect results In arrays

while($row=mysql_fetch_assoc($query))
{
	$res[$i] = $row[$ttl_lang];
	$span_tbl[$i] = ' <span style="color:#ccc">('.$row['tbl'].')</span>';
	$arr_tbl[$i] = $row['tbl'];
	$i++;
}

if($_REQUEST['typeahead']){

// using session vars to pass results to view page

	$_SESSION['search_result']['val'] = $res;
	$_SESSION['search_result']['tbl'] = $arr_tbl; //print_r($_SESSION['search_result']);
	$_SESSION['search_result']['span'] = $span_tbl;
	header("location: ../common/search.php?q={$_REQUEST['typeahead']}");
}
echo json_encode($res);

?>