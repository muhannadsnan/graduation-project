<?php

include_once '../common/pframe.php';
include_once '../obj/news.class.php';
include_once '../cms/navigator.php';

$myframe=new pframe();
$myframe->header(View_News);
$myNews=new News($_REQUEST['NID']);
$pagePRIV="NEWS_MAN";
if(user_has_permission(array("A", $pagePRIV))){	$adding ="";/*if adding is about another OBJ*/	}else{$adding=""; $pagePRIV="";	}

switch ($_REQUEST['v'])
	{
	//EDITOR_________________________________________________________________________________
	case "e":	
	/***/IS_SECURE($pagePRIV);/***/		
		if ($myNews->NID['IsNew']){	$ttl=Add_News; } else{ $ttl=Edit_News; }
		
		$myframe->open_box("withTree", $ttl,"panel", $adding);
		$myNews->DisplayEditor("_n");
		$myframe->close_box("panel");
	
	break;
	//Card____________________________________________________________________________________
	case "c":
	
		$myframe->open_box("withTree", $myNews->{'news_title_'.$GLOBALS['lang']}['value'],
                                                                                 "panel", $adding);
		$myframe->card($myNews->NID['value'],
                            $myNews->{'news_title_'.$GLOBALS['lang']}['value'],
                            $myNews->{'news_text_'.$GLOBALS['lang']}['value'],
                            $myNews->Draw_Photo($myNews->news_pic,
                            "thumb", "", $vid, "lightwindow"),"",more,true,
                            $myNews->NVNom['value'], $myNews->NDate['value'],"",$pagePRIV);
	
		$myNews->More_DVNom();	
		$myframe->close_box("panel");
	break;
	//TABLE___________________________________________________________________________________
	case "t":
		$myframe->open_box("withTree", View_News. $_REQUEST['NID'],"panel", $adding);
		
		$wherestr="";
		$sql="select * from {$myNews->tblname} {$wherestr} order by NDate desc ";
		$newstable=new Navigator($sql, $_GET['cur_page'], 6, "select count(NID) from {$myNews->tblname} {$wherestr}");
		if(@mysql_num_rows(mysql_query($sql)) != 0)
		{
			/////START CARDS EXPLORER
			while ($NewsRow=mysql_fetch_array($newstable->result)){
				$myNews->FillIn($NewsRow);
				$myframe->card($myNews->NID['value'],$myNews->{'news_title_'.$GLOBALS['lang']}['value'],$myNews->{'news_desc_'.$GLOBALS['lang']}['value'],$myNews->Draw_Photo($myNews->news_pic, "small", "", $vid , "lightwindow"),"",more,true,$myNews->NVNom['value'],$myNews->NDate['value'],"",$pagePRIV);	
			}
			//////END CARDS EXPLORER
			$newstable->Draw_Navigator_Line("jbtn");
		}else{
			?><div style="color:#999;width:90%;border:1px #ccc solid;padding:15px;margin:auto;text-align: center;"><?=No_Rows_Selected?></div><?
		}
		$myframe->close_box("panel");
	
	break;
	//DELETE__________________________________________________________________________________
	case "d":
	
	/***/IS_SECURE($pagePRIV);/***/
		if ($myNews->NID['IsNew'])  break;
		
		$myframe->open_box("withTree", Del_News,"panel", $pagePRIV, $adding);
		$myNews->DisplayDelMsg();
		$myframe->close_box("panel");
	
	break;	
}

/*************************   related pages  ******************************/
$myframe->Display_Related_Pages(News,array("A", $pagePRIV));

$myframe->footer();
?>