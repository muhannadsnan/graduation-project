<?php
ob_start();
session_start();
include_once '../common/pframe.php';
include_once '../obj/album.class.php';
include_once '../cms/navigator.php'; 

$myframe=new pframe();
$myframe->header(Albums);
$myAlbum=new Album($_REQUEST['NID']);
$pagePRIV = "ALBUMS_MAN";
if(user_has_permission(array("A", $pagePRIV))){	$adding ="";/*if adding is about another OBJ*/	}else{$adding=""; $pagePRIV="";	}

switch ($_REQUEST['v'])
	{
	//EDITOR_____________________________________________________________________________
	
//as we mentioned previously, most editors goes like this, Secure access to page, then EDITOR
	/***/IS_SECURE($pagePRIV);/***/		
		if ($myAlbum->NID['IsNew']){	$ttl=Add_Album; }else { $ttl=Edit_Album; }
		
		$myframe->open_box("withTree", $ttl,"panel", $pagePRIV, $adding);
		$myAlbum->DisplayEditor("_n");
		$myframe->close_box("panel");
	
	break;
	//Card Viewer_____________________________________________________________________________
	case "c":

// the "Album" page has special behavior, it's an object that contains two sub-object. So, we declare them at the beginning (Picture, Video)
		$_SESSION["album"] = $_REQUEST['NID'];
		
		include_once '../obj/picture.class.php';
		include_once '../obj/video.class.php';
		$myPIC=new Picture();
		$myVID=new Video();
		
// Open Box with tree & adding tool
		$myframe->open_box("withTree", $myAlbum->{album_title_x}['value'],"panel", "");
		
// SQL query with where clause (Pictures + Videos in the sqme Query !)
		$wherestr1=" where pic_album='{$_REQUEST['NID']}' ";
		$sql="(select 'pic' as tbl , pic_id ,pic_album ,pic_title_ar ,pic_title_en ,pic_desc_ar ,pic_desc_en ,pic_ext ,NVNom ,NDate from {$myPIC->tblname} {$wherestr1} order by NDate desc)";
// separator result
		$sql.=" UNION(select 'x','x','x','x','x','x','x','x','x','x' from video)"; 
		$wherestr2=" where vid_album='{$_REQUEST['NID']}' ";
		$sql.=" UNION (select 'vid' as tbl , vid_id ,vid_album ,vid_title_ar ,vid_title_en ,vid_desc_ar ,vid_desc_en ,vid_link ,NVNom ,NDate from {$myVID->tblname} {$wherestr2} order by NDate desc) ";
		$num1=get_data_in("select count(pic_id) as v from {$myPIC->tblname} {$wherestr1} ", "v");
		$num2=get_data_in("select count(vid_id) as v from {$myVID->tblname} {$wherestr2} ", "v");
		$nav=new Navigator($sql, $_GET['cur_page'], 6,0 );
		
		if(@mysql_num_rows(mysql_query($sql)) != 0)
// result not empty
		{
			/////START CARDS EXPLORER
			while ($Row=mysql_fetch_array($nav->result))
			{
// if picture object then show picture card
				if($Row['tbl'] == 'pic')
				{
					$myPIC->FillIn($Row);
					
					$myframe->card($myPIC->NID['value'],
							$myPIC->{'pic_title_'.$GLOBALS['lang']}['value'],
							$myPIC->{'pic_desc_'.$GLOBALS['lang']}['value'],
							$myPIC->Draw_Photo($myPIC->pic_ext, "thumb", "", $vid, "lightwindow"),
							"pictures.php",more,true,$myPIC->NVNom['value'],$myPIC->NDate['value'],"", "PICTURES_MAN");
				}
				elseif($Row['tbl'] == 'vid')
				{
// if video object then show video card

					$ttl='pic_title_'.$GLOBALS['lang'];
					$desc='pic_desc_'.$GLOBALS['lang'];
					
					$myframe->card($Row['pic_id'],		$Row[$ttl],		$Row[$desc],
							
						'<iframe width="420" height="345" src="'.$Row['pic_ext'].'"> </iframe>',
						"videos.php",more,true,$myVID->NVNom['value'],$myVID->NDate['value'],"", "VIDEOS_MAN");					
				}		
			}
			//////END CARDS EXPLORER
			$nav->Draw_Navigator_Line();
		}else{

// if no results returned, display message

		?><div style="color:#999;width:90%;border:1px #ccc solid;padding:15px;margin:auto;text-align: center;"><?=No_Rows_Selected?></div><?
				}
// increase visits of the album
		$myAlbum->More_DVNom();	
		$myframe->close_box("panel");
		
	break;	
	//TABLE_____________________________________________________________________________
	case "t": 
// table of albums, box with tree, then SQL query, chech result not empty to show each object card
		
		$myframe->open_box("withTree", Albums,"panel", $adding);
	
		$wherestr="  ";	
		$sql="select * from {$myAlbum->tblname} {$wherestr} order by NDate desc";
		$album=new Navigator($sql, $_GET['cur_page'], 6, "select count(NID) from {$myAlbum->tblname} {$wherestr} ");
		if(@mysql_num_rows(mysql_query($sql)) != 0)
		{
			/////START CARDS EXPLORER
				echo '<div class="albums">';
			while ($albumRow=mysql_fetch_array($album->result)){
				echo '<div class="albm">';
				$myAlbum->FillIn($albumRow);
				$myframe->card($myAlbum->NID['value'],
								$myAlbum->{'album_title_'.$GLOBALS['lang']}['value'],
								$myAlbum->{'album_desc_'.$GLOBALS['lang']}['value'],
								$myframe->DisplayAlbumPic($myAlbum),
								"",more,true,$myAlbum->NVNom['value'],$myAlbum->NDate['value'],"", $pagePRIV);
				echo '</div>';
			}
			echo '</div>';
			//////END CARDS EXPLORER
			$album->Draw_Navigator_Line();
		}else{	
// No Rows Returned !
?><div style="color:#999;width:90%;border:1px #ccc solid;padding:15px;margin:auto;text-align: center;"><?=No_Rows_Selected?></div><?	}
			
		$myframe->close_box("panel");
	
	break;
	//DELETE ___________________________________________________________________________
	case "d":
// just previous page, security then delete message
		/***/IS_SECURE($pagePRIV);/***/
		if ($myAlbum->NID['IsNew'])  break;
		
		$myframe->open_box("withTree", Delete_Album,"panel", $pagePRIV, $adding);
		$myAlbum->DisplayDelMsg();
		$myframe->close_box("panel");
	
	break;	
}
/*************************   related pages  ******************************/
$myframe->Display_Related_Pages(Album,array("A", $pagePRIV));

$myframe->footer();
?> 