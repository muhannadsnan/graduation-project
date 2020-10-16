<?php
include_once '../common/pframe.php';
include_once '../obj/video.class.php';
include_once '../cms/navigator.php'; 

$myframe=new pframe();
$myframe->header( Video );
$myVID=new Video($_REQUEST['NID']);
$pagePRIV = "VIDEOS_MAN";
if(user_has_permission(array("A", $pagePRIV))){	$adding ="";/*if adding is about another OBJ*/	}else{$adding=""; $pagePRIV="";	}

switch ($_REQUEST['v'])
{
//EDITOR_______________________________________________________________________________________
case "e":

/***/IS_SECURE($pagePRIV);/***/

	if ($myVID->NID['IsNew']){	$ttl=Add_x." ".Video; }else { $ttl=Edit_x." ".Video; }
	
	$myframe->open_box("withTree", $ttl,"panel", $pagePRIV, $adding);
	$myVID->DisplayEditor("_n");
	$myframe->close_box("panel");
	?>
	<script>
		$("#txt_vid_album").val(<?=json_encode($_SESSION['album'])?>);
	</script>
	<?
break;
//Viewer______________________________________________________________________________________
case "c":

/***/IS_SECURE($pagePRIV);/***/
	
	$myframe->open_box("withTree", $myVID->{'pic_title_'.$GLOBALS['lang']}['value'],"panel", $pagePRIV, $adding);

	$myframe->card(
			$myVID->vid_id['value'],
			$myVID->{'vid_title_'.$GLOBALS['lang']}['value'],
			$myVID->{'vid_text_'.$GLOBALS['lang']}['value'],
			$myVID->Draw_Photo($myVID->pic_ext, "thumb"),
			"",more,true,$myVID->NVNom['value'],
			$myVID->NDate['value'],"",$pagePRIV);

	$myVID->More_DVNom();

	$myframe->close_box("panel");

break;

//TABLE_______________________________________________________________________________________
case "t":
	
	$myframe->open_box("withTree", get_data_in("select ".album_title_x." from album where album_id='{$_REQUEST["album"]}'", album_title_x),"panel", $adding);

	$wherestr=" where pic_album='{$_REQUEST['album']}' ";
	$sql="select * from {$myVID->tblname} {$wherestr} order by NDate desc";	
	$pic=new Navigator($sql, $_GET['cur_page'], 6, "select count(NID) from {$myVID->tblname} {$wherestr} ");
	
	
	if(@mysql_num_rows(mysql_query($sql)) != 0)
	{
		/////START CARDS EXPLORER
		while ($picRow=mysql_fetch_array($pic->result)){
			
			$myVID->FillIn($picRow);
			
			$myframe->card($myVID->NID['value'],
							$myVID->{'vid_title_'.$GLOBALS['lang']}['value'],
							$myVID->{'vid_desc_'.$GLOBALS['lang']}['value'],
							$myVID->Draw_Photo($myVID->pic_ext, "thumb"),
							"",more,true,$myVID->NVNom['value'],
                                              $myVID->NDate['value'],"", $pagePRIV);
		}
		//////END CARDS EXPLORER
		$pic->Draw_Navigator_Line();
	}else{
		?><div style="color:#999;width:90%;border:1px #ccc solid;padding:15px;margin:auto;text-align: center;"><?=No_Rows_Selected?></div><?
		}
	$myframe->close_box("panel");

break;
//DELETE____________________________________________________________________________________
case "d":
	
/***/IS_SECURE($pagePRIV);/***/
	
	$name = pic_title_x;
	
	if ($myVID->NID['IsNew'])  break;
	
	$myframe->open_box("withTree", Del_Pic,"panel", $pagePRIV, $adding);
	$myVID->DisplayDelMsg();
	$myframe->close_box("panel");

break;	

}
/*************************   related pages  ******************************/
$myframe->Display_Related_Pages(Picture, array("A", $pagePRIV));

$myframe->footer();
?>