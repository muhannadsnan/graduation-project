<?php
include_once '../common/pframe.php';
include_once '../obj/picture.class.php';
include_once '../cms/navigator.php'; 

$myframe=new pframe();
$myframe->header(View_pictures);
$myPIC=new picture($_REQUEST['NID']);
$pagePRIV = "PICTURES_MAN";
if(user_has_permission(array("A", $pagePRIV))){	$adding ="";/*if adding is about another OBJ*/	}else{$adding=""; $pagePRIV="";	}

switch ($_REQUEST['v'])
{
//EDITOR_______________________________________________________________________________________
case "e":

/***/IS_SECURE($pagePRIV);/***/

	if ($myPIC->NID['IsNew']){	$ttl=Add_Pic; }else { $ttl=Edit_Pic; }
	
	$myframe->open_box("withTree", $ttl,"panel", $pagePRIV, $adding);
	$myPIC->DisplayEditor("_n");
	$myframe->close_box("panel");
	?>
	<script>

// the text box of the album is locked with value of session var

		$("#txt_pic_album").val(<?=json_encode($_SESSION['album'])?>);
	</script>
	<?
break;
//Viewer________________________________________________________________________________________
case "c":

	$myframe->open_box("withTree", $myPIC->{'pic_title_'.$GLOBALS['lang']}['value'],"panel", $pagePRIV, $adding);

	$myframe->card(
			$myPIC->pic_id['value'],
			$myPIC->{'pic_title_'.$GLOBALS['lang']}['value'],
			$myPIC->{'pic_text_'.$GLOBALS['lang']}['value'],
			$myPIC->Draw_Photo($myPIC->pic_ext, "thumb"),
			"",more,true,$myPIC->NVNom['value'],
			$myPIC->NDate['value'],"",$pagePRIV);

	$myPIC->More_DVNom();

	$myframe->close_box("panel");

break;
//TABLE_________________________________________________________________________________________
case "t":
	
	$myframe->open_box("withTree", get_data_in("select ".album_title_x." from album where album_id='{$_REQUEST["album"]}'", album_title_x),"panel", $adding);

// bring pictures of the album

	$wherestr=" where pic_album='{$_REQUEST['album']}' ";
	$sql="select * from {$myPIC->tblname} {$wherestr} order by NDate desc";	
	$pic=new Navigator($sql, $_GET['cur_page'], 6, "select count(NID) from {$myPIC->tblname} {$wherestr} ");
	
	if(@mysql_num_rows(mysql_query($sql)) != 0)
	{
		/////START CARDS EXPLORER
		while ($picRow=mysql_fetch_array($pic->result)){
			
			$myPIC->FillIn($picRow);
			
			$myframe->card($myPIC->NID['value'],
							$myPIC->{'pic_title_'.$GLOBALS['lang']}['value'],
							$myPIC->{'pic_desc_'.$GLOBALS['lang']}['value'],
							$myPIC->Draw_Photo($myPIC->pic_ext, "thumb"),
							"",more,true,$myPIC->NVNom['value'],
                                              $myPIC->NDate['value'],"", $pagePRIV);
		}
		//////END CARDS EXPLORER
		$pic->Draw_Navigator_Line();
	}else{
		?><div style="color:#999;width:90%;border:1px #ccc solid;padding:15px;margin:auto;text-align: center;"><?=No_Rows_Selected?></div><?
		}
	$myframe->close_box("panel");

break;
//DELETE________________________________________________________________________________________
case "d":
	/***/IS_SECURE($pagePRIV);/***/
	
	$name = pic_title_x;
	
	if ($myPIC->NID['IsNew'])  break;
	
	$myframe->open_box("withTree", Delete_Pic,"panel", $pagePRIV, $adding);
	$myPIC->DisplayDelMsg();
	$myframe->close_box("panel");

break;	
}
/*************************   related pages  ******************************/
$myframe->Display_Related_Pages(Picture, array("A", $pagePRIV));

$myframe->footer();
?>