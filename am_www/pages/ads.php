<?php

// OK, let's show you how we bahave in all of our pages
// we use this general behavior in each page.
// so we will mention more details this time but not in every page ☺
// we are one the 100th page already, only in report 7 !!

//calling class page and function pages
include_once '../common/pframe.php';
include_once '../obj/ads.class.php';
include_once '../cms/navigator.php';

//declare objects 
$myframe=new pframe();
$myframe->header(View_Ads);
$myAds=new Ads($_REQUEST['NID']);
$pagePRIV = "ADS_MAN"; //declare the privilege of this page
//show adding tool if this user is admin (A) or has the page's privilege
if(user_has_permission(array("A", $pagePRIV))){	$adding ="";/*if adding is about another OBJ*/	}else{$adding=""; $pagePRIV="";	}

switch ($_REQUEST['v'])
{		
	case "e":/*******************  Add/Edit Editor  ********************/
	/***/IS_SECURE($pagePRIV);/***/ 
//in most switch cases we use this to make sure that the user accessing the page DOES has the permission to access and operate
		if ($myAds->NID['IsNew']){		$ttl=Add_Ads;	} else{ $ttl=Edit_Ads;	}
		
//open box with tree header and adding tool (if permitted)
		$myframe->open_box("withTree", $ttl,"panel", $adding);
//display Editor that draws html add/edit from 
		$myAds->DisplayEditor("_n");

		$myframe->close_box("panel");
	break;//____________________________________________
	
	case "c":/*******************  Card (one object info)  ********************/
	/***/IS_SECURE($pagePRIV);/***/
		$myframe->open_box("withTree", $myAds->{'ads_title_'.$GLOBALS['lang']}['value'],"panel", $adding);

// calling the previously explained "card" 
//that shows a single object (photo, title, description, text, NumOfVisits, last update Date)
		$myframe->card($myAds->NID['value'],$myAds->{'ads_title_'.$GLOBALS['lang']}['value'],$myAds->{'ads_text_'.$GLOBALS['lang']}['value'],$myAds->Draw_Photo($myAds->ads_pic, "thumb", "", $vid, "lightwindow"),"",more,true,$myAds->NVNom['value'],$myAds->NDate['value'],"");

		$myframe->close_box("panel");
	break;//____________________________________________
	
	case "t":/*******************  Table of objects  ********************/
	/***/IS_SECURE($pagePRIV);/***/
		$myframe->open_box("withTree", View_Ads. $_REQUEST['NID'],"panel", $adding);
		
// SQL query with where clause
		$wherestr="";
		$sql="select * from {$myAds->tblname} {$wherestr} order by NDate desc ";

// declare a Navigator object from this sql query (Pagination), with 10 result in each page
		$adstable=new Navigator($sql, $_GET['cur_page'], 10, "select count(NID) from {$myAds->tblname} {$wherestr}");
		if(@mysql_num_rows(mysql_query($sql)) != 0)
// if the SQL query returns results
		{
			$color="";			
			/////START CARDS EXPLORER
			while ($AdsRow=mysql_fetch_array($adstable->result)){
//Fetch every single result of the query
				
//Fill the "Fetched row result" into the object of the current class (Ads)
				$myAds->FillIn($AdsRow);
//when an advertisement expires, highlight the date with color red, and don't show it in side bar
				/** check the expired ADS **/if( strtotime($myAds->ads_end['value']) < strtotime(nowandate)) $color='red'; else $color='green';
				$myframe->card($myAds->NID['value'],$myAds->{'ads_title_'.$GLOBALS['lang']}['value'],$myAds->{'ads_desc_'.$GLOBALS['lang']}['value'].'</br>'.ads_end.' : <p style="color:'.$color.'">'.$myAds->ads_end['value']."</p>",$myAds->Draw_Photo($myAds->ads_pic, "thumb"),"",more,/*$_REQUEST['NType']=="1" ? false : true*/true,$myAds->NVNom['value'],$myAds->NDate['value'],/*$myAds->NType['value']*/"");
			}
			//////END CARDS EXPLORER
			$adstable->Draw_Navigator_Line("jbtn");
		}else{
// when the SQL query returns no result, show a message
			?><div style="color:#999;width:90%;border:1px #ccc solid;padding:15px;margin:auto;text-align: center;"><?=No_Rows_Selected?></div><?
			}
		$myframe->close_box("panel");
	break;//____________________________________________
	
	case "d":/*******************  Delete an object  ********************/

//security ..
	/***/IS_SECURE($pagePRIV);/***/
		if ($myAds->NID['IsNew'])  break; //security ..
	
		$myframe->open_box("withTree", Del_Ads,"panel", $pagePRIV, $adding);
             //Delete form (message, Yes/No)
		$myAds->DisplayDelMsg();
		$myframe->close_box("panel");
	break;//____________________________________________
}	
/*************************   related pages  ******************************/
$myframe->Display_Related_Pages(Ads,array("A", $pagePRIV));

// Footer foreach page
	$myframe->footer();
	?>