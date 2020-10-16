<?php 
include_once '../common/pframe.php';
include_once '../common/privileges.php';
include_once '../cms/navigator.php';
include_once "../obj/groups.class.php";

$myframe=new pframe();
$myframe->header(Groups);
$myGroup = new Groups($_REQUEST['NID']);
$pagePRIV = "GROUPS_MAN";
if(user_has_permission(array("A", $pagePRIV))){	$adding ="";/*if adding is about another OBJ*/	}else{$adding=""; $pagePRIV="";	}
if(isset($_SESSION['group_id'])) //if we were adding a user and came back here
	unset($_SESSION['group_id']);

switch ($_REQUEST['v'])
{
	case "members": /************** members editor **************/
		/***/IS_SECURE($pagePRIV);/***/
		if ($myGroup->NID['IsNew']){	$ttl=Group_members." : ".Add_x;	}else { $ttl=Group_members." : ".Edit_x;	}
	
		$myframe->open_box("withTree", $ttl, "panel" ,$pagePRIV  ,$adding);
	
		if($_REQUEST['group'] != ''){
			//echo '<div>'.Group_members.'</div></br>';
			MembersEditor($pagePRIV);
		}
		$myframe->close_box("panel");
		$_SESSION['back_page'] = "groups";	
	break;
		
	case "e": /************** edit group & Privileges Editor **************/
	/***/IS_SECURE($pagePRIV);/***/		
		if ($myGroup->NID['IsNew']){	$ttl=Add_groups;	}else { $ttl=Edit_groups;		}
		
		$myframe->open_box("withTree", $ttl, "panel", $pagePRIV, $adding);
		
		$myGroup->DisplayEditor("_n");
		
		if(!$myGroup->NID['IsNew']){
			echo '<div>'.Privileges_Message_Empty.'</div></br>';
			PrivilegesEditor($pagePRIV);}
		$myframe->close_box("panel");
		
	break;
	
	case "t": /*************** All groups with users inside each one **************/
	/***/IS_SECURE($pagePRIV);/***/
		$_SESSION['access_to_all_privs']=$pagePRIV;
		$myframe->open_box("withTree", View_groups,"panel",$pagePRIV , $adding);
		$wherestr="";
		$sql="select * from {$myGroup->tblname} {$wherestr} order by 2 desc";
		$grp=table($sql);
				
		if(@mysql_num_rows(mysql_query($sql)) != 0)
		{
			?><div class="groups"><? 
			?><ul class="collapsibleList"  id="ul_<?=$myGroup->NID['value']?>"><? 
			/////START CARDS EXPLORER				
			while ($GroupRow=mysql_fetch_array($grp)){
				
				$myGroup->FillIn($GroupRow);
				?>
				<li class="li_group"  id="li_<?=$myGroup->NID['value']?>">
					<?=$myGroup->group_name['value']?>
					<ul class="ul_user"  id="<?=$myGroup->NID['value']?>"><? 						
						
						$where=" where user_id in (select user_id from user_groups where user_group_id ='{$myGroup->group_id['value']}' )  ";
						$sql2="select * from user {$where} order by user_name";
						$usr=table($sql2);
						while ($UsrRow=mysql_fetch_array($usr)){
							
							$href="../pages/users.php?lang={$GLOBALS['lang']}&v=c&NID={$UsrRow['user_id']}&group={$myGroup->NID['value']}";
							?><a class="a_li_user" href="<?=$href?>"><li class="li_user"><?=$UsrRow['user_name']?></li></a><?  
						}
						?>
						<li class="li_gr_privs">[[[[[[[[ &nbsp;&nbsp;<?=Privileges?> &nbsp;&nbsp; ]]]]]]]] <div>&nbsp;</div><? 
				 			this_group_privs($myGroup->NID['value'], false);
				 		?></li><?					
							 		 
						?><li class="tools"><?
							?><div style=""><? 
								$myframe->DisplayAdminTools("groups",$myGroup->NID['value']);
								$myframe->DisplayAddingTool($pagePRIV, "groups", "&v=members&group={$myGroup->NID['value']}");
							?></div><? 
						?></li><?
					?></ul>
				</li><? 
				$_SESSION['back_page'] = "groups";
			}?>
			<script>
				// make the appropriate lists collapsible
				CollapsibleLists.apply();
				//when we come back from adding a user, it's group opens
				var v=document.getElementById("<?echo $_REQUEST['NID'];?>");
				v.style.display = "block";
				var v=document.getElementById("li_<?echo $_REQUEST['NID'];?>");
				v.className = 'li_group collapsibleListOpen';				
			</script>	
					
		</ul></div><? 
		}else{	?><div style="color:#999;width:90%;border:1px #ccc solid;padding:15px;margin:auto;text-align: center;"><?=No_Rows_Selected?></div><?	}			
		$myframe->close_box("panel");
	break;
	
	case "d": /************** delete **************/
		/***/IS_SECURE($pagePRIV);/***/
		$myframe->open_box("withTree", Del_groups,"panel",$pagePRIV ,$adding);
		$myGroup->DisplayDelMsg();
		$myframe->close_box("panel");
	break;
}

/*************************   related pages  ******************************/
$myframe->Display_Related_Pages(Services,array("A", $pagePRIV));

$myframe->footer();
?>