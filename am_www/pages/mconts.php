<?php
session_start();
include_once '../common/pframe.php';
include_once '../obj/maint_contract.class.php';
include_once '../cms/navigator.php'; 

$myframe=new pframe();
$myframe->header(View_mconts);
$myMcont=new Maint_contract($_REQUEST['NID']);
$pagePRIV = "MCONTS_MAN";
if(user_has_permission(array("A", $pagePRIV))){	$adding ="";/*if adding is about another OBJ*/	}else{$adding=""; $pagePRIV="";	}

switch ($_REQUEST['v'])
{
case "e": //EDITOR____________________________________________________________________________________________

		/***/IS_SECURE($pagePRIV);/***/
	
	//bcont_serial from get param.. or else we can choose
	if($_REQUEST['bcont'] != ''){	$myMcont->bcont_serial['control']="text";$myMcont->bcont_serial['value']=$_REQUEST['bcont'];		}
	//else{$myMcont->bcont_serial['value'] = $myframe->Get_Incremental_ID("buy_contract","NSerial");}

	
	//when creating/editing mconts it's not allowed to change dist/cust, they fill automatically from SESSION . . .
	//------------
	if ($myMcont->NID['IsNew']){	$ttl=Add_x;  $myMcont->NSerial['value'] = $myframe->Get_Incremental_ID($myMcont->tblname,"NSerial");}
	else { $ttl=Edit_x; }
	
	if($myframe->Is_Customer()){ 
		$myMcont->bcont_serial['fFltr']= " where bcont_cust = '{$_SESSION['GID']}'"; 
		$myMcont->mcont_cust['value']=$_SESSION['GID'];
		$bcont_dist = get_data_in("select bcont_dist from buy_contract where NSerial='{$_REQUEST['bcont']}' ", "bcont_dist");
		$myMcont->mcont_dist['control'] = 'hidden'; $myMcont->mcont_dist['value'] = $bcont_dist;
		$myMcont->mcont_cust['control'] = 'hidden'; $myMcont->mcont_cust['value'] = $_SESSION['GID'];
	}	
	
	//distributor creates mcont with his id and cust id of the bcont (( not other ))

	if($myframe->Is_Distributor()){
		$bcont_cust = get_data_in("select bcont_cust from buy_contract where NSerial='{$_REQUEST['bcont']}' ", "bcont_cust");
		$myMcont->mcont_dist['control'] = 'hidden'; $myMcont->mcont_dist['value'] = $_SESSION['GID'];
		if($_REQUEST['bcont'] !=''){$myMcont->mcont_cust['control'] = 'hidden'; $myMcont->mcont_cust['value'] = $bcont_cust;}
		else{$myMcont->bcont_serial['fFltr']= "where bcont_dist='".$_SESSION['GID']."'";
		$myMcont->mcont_cust['control'] = 'hidden';}
		
		//prevent distributor from changing parameters or copy links to access others bconts

		if($_SESSION['GID'] != get_data_in("select mcont_dist from maint_contract where NSerial='{$_REQUEST['NID']}'", "mcont_dist") && $_REQUEST['NID'] != 'IsNew') {
			IS_SECURE("", "not_secure");		
		}
	}	
	
	$myframe->open_box("withTree", $ttl,"panel", $pagePRIV, $adding);
	$myMcont->DisplayEditor("_n");
	$myframe->close_box("panel");
	
// LOCK some field for protection

		?><script>var selct=document.getElementById("txt_NSerial");		selct.disabled = true;	</script><?
		
	if($_REQUEST['bcont']){
		?><script>		
			var selct=document.getElementById("txt_bcont_serial");	selct.disabled = true;
		</script><?
	} 
	if($myframe->Is_Customer()){ 
		 ?><script>
				var selct=document.getElementById("txt_mcont_cust");			selct.disabled = true;
				var selct=document.getElementById("txt_mcont_status");			selct.disabled = true;		
			</script><?
	}
	$url = "mconts.php?lang={$GLOBALS['lang']}&v=e&NID=IsNew&bcont=";
	?><script>
	$("#txt_bcont_serial").val(<?=json_encode($_SESSION['GID'])?>);

			//redirect to modify NID parameter according to the selected option
			$( "#txt_bcont_serial" ).change(function() {
				location.href = <?=json_encode($url)?> + $(this).val();
				});
			// adding an empty option at the beginning of the list
			$("#txt_bcont_serial").prepend("<option value='' selected='selected'>...........</option>");
			$("#txt_bcont_serial").val(<?=json_encode($_REQUEST['bcont'])?>);
			</script><? 	
break;

case "c": //Card Viewer___________________________________________________________________________
	
/***/IS_SECURE($pagePRIV);/***/
	if($myMcont->seen['value']==0 && $myframe->Is_Distributor()){
		cmd("update maint_contract set mcont_seen=1 where NSerial='{$_REQUEST['NID']}'");
	}

	$myframe->open_box("withTree", $myMcont->NSerial['value'],"panel", $pagePRIV, $adding);
	$myframe->card(
			$myMcont->NSerial['value'],
			mcont_serial." : ".$myMcont->NSerial['value'].'<br/><br/>',
			Distributor_Name." : ".get_data_in("select user_name from user where user_id = '{$myMcont->mcont_dist['value']}'", 'user_name').'<br/><br/>'.
			Customer_Name." : ".get_data_in("select user_name from user where user_id = '{$myMcont->mcont_cust['value']}'", 'user_name').'<br/><br/>'.
				mcont_desc." : ".$myMcont->mcont_desc['value'] .'<br/><br/>'.			bcont_serial." : ".$myMcont->bcont_serial['value'].'<br/><br/>'.
				mcont_status." : ".$myMcont->mcont_status['value'] .'<br/><br/>'.		NDate." : ".$myMcont->NDate['value'] ,
			"",
			"","",false,
			$myMcont->mcont_status['value'],
			$myMcont->NDate['value'],"",$pagePRIV);
break;

case "t"://TABLE__________________________________________________________________________________
	
/***/IS_SECURE($pagePRIV);/***/
	$_SESSION['bcont'] = $_REQUEST['bcont'];
	
	if($myframe->Is_Distributor() && get_data_in("SELECT bcont_seen FROM buy_contract WHERE NSerial = '{$_REQUEST['bcont']}' ", "bcont_seen")==0){
		cmd("update buy_contract set bcont_seen=1 where NSerial='{$_REQUEST['bcont']}'");
	}
	$myframe->open_box("withTree", mconts,"panel",$pagePRIV , $adding);
	
	//if the user doesn't have any BConts, he can't create any MConts !!

	if($myframe->Is_Customer()){$me='bcont_cust';}
	elseif($myframe->Is_Distributor()){$me='bcont_dist';}
	$sql = "select * from buy_contract where {$me}='{$_SESSION['GID']}' ";
	if(@mysql_num_rows(mysql_query($sql)) == 0){		echo No_MConts;$Have_BConts = false;
	}else{
		$Have_BConts = true;
	$and="";
	$wherestr=" where ";
	if($myframe->Is_Customer()){$wherestr.=" mcont_cust='{$_SESSION['GID']}' "; $and=" and ";}
	if($myframe->Is_Distributor()){$wherestr.=" mcont_dist='{$_SESSION['GID']}' "; $and=" and ";}	
	if($_REQUEST['bcont']){	$wherestr .= " {$and} bcont_serial='{$_REQUEST['bcont']}'";	$and="2";}
	if($and ==''){$wherestr="";}
	
	$sql="select * from {$myMcont->tblname} {$wherestr} order by NDate desc";
	$serv=new Navigator($sql, $_GET['cur_page'], 20, "select count(NID) from {$myMcont->tblname} {$wherestr} ");
	//
	?><table class="global_tbl sortable">
	<? if(@mysql_num_rows(mysql_query($sql)) == 0){?><tr><td><?=No_Rows_Selected?> </td></tr><?}else
	{
		?><tr>
			<th><?=mcont_serial?></th>
			<th><?=BConts?></th>
			<th><?=Distributor_Name?></th>
			<th><?=Customer_Name?></th>
			<th><?=bcont_prod?></th>
			<th><?=mcont_status?></th>
			<th><?=NDate?></th>
		</tr>
		<?
		////START CARDS NAVIGATOR
		$i=0;
		$tr_class="";
		
		while ($Row=mysql_fetch_array($serv->result))
		{
			$myMcont->FillIn($Row);
			if($i%2==0){$tr_class="tr_1";}else{$tr_class="tr_2";}
			if(($myMcont->mcont_dist['value'] == $_SESSION['GID']) || user_has_permission(array("A", $pagePRIV))){
			?>
			
			<tr class="<?=$tr_class?> <? if($_REQUEST['NID'] == $myMcont->NSerial['value']){ echo "tr_highlighted";}?>" onclick="document.location = '../pages/mconts.php?lang=<?=$GLOBALS["lang"]?>&v=c&bcont=<?=$myMcont->bcont_serial['value']?>&prod=<?=$_REQUEST['prod']?>&NID=<?=$myMcont->NSerial['value']?>';">
				<td class="<?if($myMcont->mcont_seen['value']!=1){echo "unseen_td";}?>"><?=$myMcont->NSerial['value']?></td>
				<td class=""><a class="goto_bcont" href="../pages/bconts.php?lang=<?=$GLOBALS['lang']?>&NID=<?=$myMcont->bcont_serial['value']?>&v=t" ><?=$myMcont->bcont_serial['value']?></a></td>
				<td class=""><?=get_data_in("select user_name from user where user_id = '{$myMcont->mcont_dist['value']}'", 'user_name')?></td>
				<td class=""><?=get_data_in("select user_name from user where user_id = '{$myMcont->mcont_cust['value']}'", 'user_name')?></td>
				
				<? $bcont_prod = get_data_in("select bcont_prod from buy_contract where NSerial = '{$myMcont->bcont_serial['value']}'", 'bcont_prod'); ?>
				<td class=""><?=get_data_in("select ".prod_title_x." from product where prod_id = '{$bcont_prod}'", prod_title_x)?></td>
				<td class=""><?=$myMcont->mcont_status['value']?></td>
				<td class=""><?=$myMcont->NDate['value']?></td>
				<? if(user_has_permission(array("A", $pagePRIV))) {?><td class="admin_tools_td"><?=$myframe->DisplayAdminTools("mconts", $myMcont->bcont_serial['value']."/".$bcont_prod."/".$myMcont->NSerial['value'])?></td><? }
			?></tr><?
			}
			$i++;
		}  
	}
	?></table><?
	//////END CARDS NAVIGATOR
	$serv->Draw_Navigator_Line();
	$myframe->close_box("panel");
}
break;
//DELETE________________________________________________________________________________________
case "d":
/***/IS_SECURE($pagePRIV);/***/	
	if ($myMcont->NID['IsNew'])  break;	
	
	$myframe->open_box("withTree", Del_x,"panel", $pagePRIV, $adding);
	$myMcont->DisplayDelMsg();
	$myframe->close_box("panel");

break;
}
/*************************   related pages  ******************************/
	if($Have_BConts){
		$myframe->Display_Related_Pages(mconts, array("A", $pagePRIV));
	}
$myframe->footer();
?>