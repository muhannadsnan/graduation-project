<?php
include_once '../common/pframe.php';
include_once '../obj/product.class.php';
include_once '../cms/navigator.php'; 

$myframe=new pframe();
$myframe->header(View_Products);
$myP=new Product($_REQUEST['NID']);
$pagePRIV = "PRODUCTS_MAN";
if(user_has_permission(array("A", $pagePRIV))){	$adding ="";/*if adding is about another OBJ*/	}else{$adding=""; $pagePRIV="";	}

switch ($_REQUEST['v'])
{
//EDITOR_____________________________________________________________________________________
case "e":

/***/IS_SECURE($pagePRIV);/***/
	
	if ($myP->NID['IsNew']){	$ttl=Add_Products; }else { $ttl=Edit_Products; }
	
	$myframe->open_box("withTree", $ttl,"panel", $pagePRIV ,$adding);
	$myP->DisplayEditor("_n");
	$myframe->close_box("panel");

break;
//Viewer_____________________________________________________________________________________
case "c":

	$myframe->open_box("withTree", $myP->{'prod_title_'.$GLOBALS['lang']}['value'],"panel", $pagePRIV ,$adding);
	$myframe->card(
			$myP->prod_id['value'],
			$myP->{'prod_title_'.$GLOBALS['lang']}['value'],
			$myP->{'prod_text_'.$GLOBALS['lang']}['value'],
			$myP->Draw_Photo($myP->prod_pic, "thumb"),
			"",more,true,$myP->NVNom['value'],
			$myP->NDate['value'],"",$pagePRIV);

	$myP->More_DVNom();
	$myframe->close_box("panel");

break;
//TABLE______________________________________________________________________________________
case "t":
	
	$myframe->open_box("withTree", View_Products,"panel", $pagePRIV, $adding);
	
	if ($_REQUEST['NID']!="" && !$_REQUEST['cat_id']) {$c=" where NID = '{$_REQUEST['NID']}' ";} else {$c = "";}
	if ($_REQUEST['cat_id'] != "" && !$_REQUEST['NID']){$c=" where prod_cat='{$_REQUEST['cat_id']}' ";} else{$c="";}
	
	$wherestr=" where prod_only_dw=0 "; 

//NOT a prod_only_dw which is (supplying program that is not our product and we provide a download for it)
	if($_REQUEST['cat']){	$wherestr.=" and prod_cat='{$_REQUEST['cat']}' ";}
	
	$sql="select * from {$myP->tblname} {$wherestr} order by NDate desc";
	$prod=new Navigator($sql, $_GET['cur_page'], 6, "select count(NID) from {$myP->tblname} {$wherestr} ");
	
	if(@mysql_num_rows(mysql_query($sql)) != 0)
	{
		/////START CARDS EXPLORER			
		while ($ProdRow=mysql_fetch_array($prod->result)){
			
			$myP->FillIn($ProdRow);
			$myframe->card($myP->NID['value'],
							$myP->{'prod_title_'.$GLOBALS['lang']}['value'],
							$myP->{'prod_desc_'.$GLOBALS['lang']}['value'],
							$myP->Draw_Photo($myP->prod_pic, "thumb"),
							"",more,true,$myP->NVNom['value'],$myP->NDate['value'],"", $pagePRIV);
		}
		//////END CARDS EXPLORER
		$prod->Draw_Navigator_Line();
	}else{
		?><div style="color:#999;width:90%;border:1px #ccc solid;padding:15px;margin:auto;text-align: center;"><?=No_Rows_Selected?></div><? 
	}
	$myframe->close_box("panel");

break;
//DELETE______________________________________________________________________________________
case "d":

/***/IS_SECURE($pagePRIV);/***/	
	if ($myP->NID['IsNew'])  break;
	
	$myframe->open_box("withTree", Del_Products,"panel", $pagePRIV ,$adding);
	$myP->DisplayDelMsg();
	$myframe->close_box("panel");

break;	

}
/*************************   related pages  ******************************/
$myframe->Display_Related_Pages(Products,array("A", $pagePRIV));

$myframe->footer();
?>