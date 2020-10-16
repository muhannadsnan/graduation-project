<?php
include_once '../common/pframe.php';
include_once '../obj/shared_pool.class.php';

$myframe = new pframe ();
$myframe->header ( Shared_Pool );
$mySH = new Shared_pool ( $_REQUEST ['NID'] );
$adding = "";
$pagePRIV = "SHARED_POOL_MAN";

//*********** back & refresh buttons ************/
$cur = $_REQUEST['path'];
$ds = substr($_REQUEST['path'], 0, strrpos( $_REQUEST['path'], '/') );
$back = "../pages/shared_pool.php?lang={$GLOBALS['lang']}&v=t&path={$ds}";
$current = "../pages/shared_pool.php?lang={$GLOBALS['lang']}&v=t&path={$cur}";

if ($_SESSION['GID']) // only logged in users can access shared pool
{
	switch ($_REQUEST ['v'])
	{
		case "e": //******************** Add, Edit *********************/
			
			if($mySH->user['value'] != $_SESSION['GID'] && !user_has_permission(array("A", $pagePRIV)) && $_REQUEST['NID'] != 'IsNew') {
				/***/IS_SECURE("", "not_secure");/***/
			}
			
			if ($mySH->NID['IsNew']){	$ttl=Add_x;	} else{ $ttl=Edit_x ." ". $mySH->file_name['value'];	}			
			
			/*** Creating New Folder ***/ 
			if($_REQUEST['dir'] == '1'){					
				$mySH->file_type['type'] = 'hidden';
				$mySH->file_type['control'] = 'none';
				$mySH->file_type['value'] = '[dir]';				
				$mySH->file_name['control'] = 'text';
				$mySH->path['value'] = $_REQUEST['path']; }
				
			/*** uploading file ***/
			else{ 
				$mySH->file_type['type'] = 'file';
				$mySH->file_type['control'] = 'file';
			}			

			$myframe->open_box("withTree", $ttl,"panel", $adding);
			
			//fill data about uploaded file
			$mySH->user['value'] = $_SESSION['GID'];
			$mySH->path['value'] = $_REQUEST['path'];
			$mySH->file_size['value'] = $_FILES['txt_file_type']['size'];
			
			$mySH->DisplayEditor("_n");
			$myframe->close_box("panel");
			?><a href="<?=$back?>"><img src="../images/icons/Undo.png" style="float:right;"/></a><?
		break;
		
		case "t": //********************* All Elements ********************/
			
			$myframe->open_box("withTree", Shared_Pool ." > ". $_REQUEST['path'] , "panel", "" , $adding);			
		
				//*********** back & refresh buttons						
			?><a href="<?=$back?>"><img src="../images/icons/Undo.png"/></a>&nbsp;&nbsp;<a href="<?=$current?>"><img src="../images/icons/refresh.png"/></a><?
			 
				//************** NewFolder
			?><a href="../pages/shared_pool.php?lang=<?=$GLOBALS['lang']?>&v=e&NID=IsNew&dir=1&path=<?=$_REQUEST['path']?>"><img src="../images/icons/black-newfolder.png" style="margin:0px 10px;margin-bottom:10px;"/></a><? 
			
			if($_REQUEST['path'] == '')
				{$wherestr=" where path='{$_REQUEST['path']}' or path is null ";}
			else{$wherestr=" where path='{$_REQUEST['path']}'";}
			$sql="select * from {$mySH->tblname} {$wherestr} order by file_type desc,file_name asc "; //echo $sql;
			
// bring all files that the users have uploaded
// the are stored in DB to make it faster to show
// and to isolate our storage from direct access

			if(@mysql_num_rows(mysql_query($sql)) != 0)
			{
				?><table class="global_tbl sortable">
					<th><?=file_name?></th>
					<th><?=user_id?></th>					
					<th><?=file_size?></th>
					<th><?=extension?></th>
					<th><?=NDate?></th>
				<? 					
					$i=0;
					$tr_class="";					
					$tbl=table($sql);
					 
				while ($Row=mysql_fetch_array($tbl))
				{		
					$mySH->FillIn($Row);
					if($i%2==0){	$tr_class="tr_1";	}else{	$tr_class="tr_2";	}
						
// if the record was Drectory it will show in different way				

					if($Row['file_type'] == '[dir]')
					{$location = "../pages/shared_pool.php?lang={$GLOBALS["lang"]}&v=t&path={$_REQUEST['path']}/{$mySH->file_name['value']}"; $ico='<img src="../images/icons/folder0000.png" style="float:right;line-height:21px;vertival-align:middle;padding:0px 10px"/>';}
					else 
					{
//the record was a FILE
						$dir = get_folders();
						$location = "../documents/SHARED_POOL{$dir}/{$mySH->NID['value']}.{$mySH->file_type['ext']}"; $ico='';}
										
					?><tr class="<?=$tr_class?> tr0" onclick="document.location = '<?=$location?>'; " id="tr_<?=$mySH->NID['value']?>" target="_blank">
						<td><?=$ico?><?=$mySH->file_name['value']?></td>
						<td><?=get_data_in("select user_name from user where user_id = '{$mySH->user['value']}' ", "user_name")?></td>						
						<td><?=$myframe->format_size($mySH->file_size['value']) ?></td>
						<td><?=$Row['file_type']?></td>
						<td><?=$mySH->NDate['value']?></td>
						<td>
							<?if($mySH->user['value'] == $_SESSION['GID'] || user_has_permission(array("A", $pagePRIV))) {	
								$myframe->DisplayAdminTools("shared_pool", $mySH->NID['value']);}?>
						</td>
					</tr><?					
					$i++; 				
				}
				?></table><?
				
			}else{
				?><div style="color:#999;width:90%;border:1px #ccc solid;padding:15px;margin:auto;text-align: center;"><?=No_Rows_Selected?></div><?
			} 
			$myframe->close_box("panel");
			$mySH = new Shared_pool();
			
//fill data about uploaded file

			$mySH->user['value'] = $_SESSION['GID'];
			$mySH->path['value'] = $_REQUEST['path']; 
			$mySH->file_size['value'] = $_FILES['txt_file_type']['size'];
			
			?><div style="width:700px;margin:auto;border:0px #000 solid"><? $mySH->DisplayEditor("_n");?></div><? 
			
		break;
			
		case "d" : /****************************************/
			
// if the user was trying to delete a file he does not own AND he is not admin (security)

			if($mySH->user['value'] != $_SESSION['GID'] && !user_has_permission(array("A", $pagePRIV)) && $_REQUEST['NID'] != 'IsNew' ) {
				/***/IS_SECURE("", "not_secure");/***/
			}
			
			$myframe->open_box ( "withTree", Del_Ads, "panel", "", $adding );
			$sql = "select NID from shared_pool where path like '%/{$mySH->file_name['value']}' ";
// it's not allowed to delete a folder with contents

			if(@mysql_num_rows(mysql_query($sql)) == 0)
			{
				$mySH->DisplayDelMsg ();
			}
			else{echo cannot_delete_folder_with_content;}
			
			$myframe->close_box ( "panel" );
			?><a href="<?=$back?>"><img src="../images/icons/Undo.png"/></a><?
		break; 
	}
}
else{IS_SECURE("", "not_secure");}

 /* *********************** related pages ***************************** */

$myframe->footer ();

function get_folders() 
// converts the path from "folder-name-path" to "IDs-path"
{
	//bring the id for dir
	$arr = explode('/', $_REQUEST['path']);
	$dir = "";
	$i=0;
	$ids=array();
	foreach( $arr as $v){
		//echo $v;
		$ids[$i] = get_data_in("select NID from shared_pool where file_type='[dir]' and file_name='{$v}' ", "NID");
		$i++;
	}
	foreach( $ids as $v){
		$dir .= "/{$v}";
	}
	if($_REQUEST['path'] != '')
		$dir .='/';
	return $dir;
}
?>