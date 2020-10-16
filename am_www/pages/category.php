<?php

// by now every thing seems to be routine, we are going to explain non routine thing by now

include_once '../common/pframe.php';
include_once '../obj/category.class.php';
include_once '../cms/navigator.php'; 

$myframe=new pframe();
$myframe->header(View_x);
$myP=new Category($_REQUEST['NID']);
$pagePRIV = "CATEGORY_MAN";
if(user_has_permission(array("A", $pagePRIV))){	$adding ="";/*if adding is about another OBJ*/	}else{$adding=""; $pagePRIV="";	}

switch ($_REQUEST['v'])
	{
	//EDITOR_______________________________________________________________________________
	case "e":
	
	/***/IS_SECURE($pagePRIV);/***/	
		if ($myP->NID['IsNew']){	$ttl=Add_x; }else { $ttl=Edit_x; }
		
		$myframe->open_box("withTree", $ttl,"panel", $pagePRIV, $adding);
		$myP->DisplayEditor("_n");
		$myframe->close_box("panel");	
	break;
	//Viewer_______________________________________________________________________________
	case "c":
	
// when clicking on a category, the card case will redirect you to the products of that category

		header("location: ../pages/products?lang={$GLOBALS['lang']}&v=t&cat={$_REQUEST['NID']}");
	
	break;	
	//TABLE_________________________________________________________________________________
	case "t":
		$myframe->open_box("withTree", Categories,"panel", $adding);
		$wherestr=" ";	
		$sql="select * from {$myP->tblname} {$wherestr} order by NDate desc";
		$cat=new Navigator($sql, $_GET['cur_page'], 6, "select count(NID) from {$myP->tblname} {$wherestr} ");
		
		if(@mysql_num_rows(mysql_query($sql)) != 0)
		{
			/////START CARDS EXPLORER				
			while ($catRow=mysql_fetch_array($cat->result)){
				
				$myP->FillIn($catRow);
				$myframe->card($myP->NID['value'],
								$myP->{'cat_title_'.$GLOBALS['lang']}['value'],
								"",
								"",
								"",more,true,""/*$myCat->NVNom['value']*/,
                                                     $myP->NDate['value'],"", $pagePRIV);
			}
			//////END CARDS EXPLORER
			$cat->Draw_Navigator_Line();
		}else{	?><div style="color:#999;width:90%;border:1px #ccc solid;padding:15px;margin:auto;text-align: center;"><?=No_Rows_Selected?></div><?	}
// Or emprty result, then display message as usual
			
		$myframe->close_box("panel");	
	break;
	//DELETE_______________________________________________________________________________
	case "d":
		/***/IS_SECURE($pagePRIV);/***/		
		if ($myP->NID['IsNew'])  break;
		
		$myframe->open_box("withTree", Del_x,"panel", $pagePRIV, $adding);
		$myP->DisplayDelMsg();
		$myframe->close_box("panel");
	
	break;	
}
/*************************   related pages  ******************************/
$myframe->Display_Related_Pages(Categories, array("A", $pagePRIV));

$myframe->footer();
?>