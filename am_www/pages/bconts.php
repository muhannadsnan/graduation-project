<?php
// as usual, include class and function pages

include_once '../common/pframe.php';
include_once '../obj/buy_contract.class.php';
include_once '../cms/navigator.php'; 

//declare objects and page privilege

$myframe=new pframe();
$myframe->header(View_bconts);
$myBCont=new Buy_contract($_REQUEST['NID']); 
$pagePRIV = "BCONTS_MAN";
if(user_has_permission(array("A", $pagePRIV))){	$adding ="";/*if adding is about another OBJ*/	}else{$adding=""; $pagePRIV="";	}

switch ($_REQUEST['v'])
	{
	//EDITOR_______________________________________________________________________________
	case "e":
	
	/***/IS_SECURE($pagePRIV);/***/		
		if ($myBCont->NID['IsNew']){	$ttl=Add_x; }else { $ttl=Edit_x; }
		
		//distributor can't change dist value, it's him self and hidden
		if($myframe->Is_Distributor()){$myBCont->bcont_dist['control'] = 'hidden';        $myBCont->bcont_dist['value'] = $_SESSION['GID'];

//prevent distributor from changing parameters or copy links to access others buy contracts

			if($_SESSION['GID'] != get_data_in("select bcont_dist from buy_contract where NSerial='{$_REQUEST['NID']}'", "bcont_dist") && $_REQUEST['NID'] != 'IsNew') {IS_SECURE("", "not_secure");}
		}
		
// Show Editor
		$myframe->open_box("withTree", $ttl,"panel", $pagePRIV, $adding);
		$myBCont->DisplayEditor("_n");
		$myframe->close_box("panel");
		
		?><script>  // you can not edit Serial (auto increment) OR change distributor (LOCKED)
			var selct=document.getElementById("txt_NSerial");		selct.disabled = true;	
			var selct=document.getElementById("txt_bcont_dist");		selct.disabled = true;	
			$("#txt_bcont_dist").val(<?=json_encode($_SESSION['GID'])?>);
		</script><?
	
	break;
	//Viewer______________________________________________________________________________
	case "c": 
	
	/***/IS_SECURE($pagePRIV);/***/

// when clicking on the bcont, the card case will redirect you to the MConts inside this BCont

		header("location: ../pages/bconts.php?lang={$GLOBALS['lang']}&v=t&NID={$_REQUEST['NID']}");	
	break;
	//TABLE_______________________________________________________________________________
	case "t":	
		
		$myframe->open_box("withTree", BConts,"panel", $pagePRIV, $adding);
		$wherestr=" ";
// if you were a Customer/Distributor, you only see BConts that you are a part of
		if($myframe->Is_Customer()){$wherestr=" where bcont_cust='{$_SESSION['GID']}' ";}
		elseif($myframe->Is_Distributor()){$wherestr=" where bcont_dist='{$_SESSION['GID']}' ";}
			
// sql query to bring all BConts
		$sql="select * from {$myBCont->tblname} {$wherestr} order by NDate desc"; //echo $sql;
		$serv=new Navigator($sql, $_GET['cur_page'], 20, "select count(NID) from {$myBCont->tblname} {$wherestr} ");

		?><table class="global_tbl sortable">

// if no result display a message
		<? if(@mysql_num_rows(mysql_query($sql)) == 0){?><tr><td><?=No_Rows_Selected?></td></tr><?}else{?>

// if  result not empty show the table of results
			<tr>
				<th><?=NSerial?></th>
				<th><?=Distributor_Name?></th>
				<th><?=Customer_Name?></th>				
				<th><?=bcont_prod?></th>
				<th><?=bcont_discount?></th>
				<th><?=bcont_payment?></th>
				<th><?=bcont_license?></th>
				<th><?=NDate?></th>
			</tr>
		<?
		////START CARDS NAVIGATOR
		$i=0;
		$tr_class="";
		while ($Row=mysql_fetch_array($serv->result))
		{
			$myBCont->FillIn($Row);
			if($i%2==0){$tr_class="tr_1";}else{$tr_class="tr_2";}
// Display my BConts if I was Distributor / Customer
// Display all BConts for Admin
			if(($myframe->Is_Distributor() && $myBCont->bcont_dist['value'] == $_SESSION['GID']) || user_has_permission(array("A", $pagePRIV))  
					|| $myframe->Is_Customer() && $myBCont->bcont_cust['value'] == $_SESSION['GID']){
				
				//checking whther this bcont is licensed or not to change the column css
				if($myBCont->bcont_license['value']=="No License"){		$is_lice="no_license";		}
				elseif($myBCont->bcont_license['value']=="Licensed") {			$is_lice="licensed";			}
			?>

// Here we go, show all columns, with FK titles of course

			<tr class="<?=$tr_class?> <? if($_REQUEST['NID'] == $myBCont->NID['value']){ echo "tr_highlighted";}?>" onclick="document.location = '../pages/mconts.php?lang=<?=$GLOBALS["lang"]?>&v=t&bcont=<?=$myBCont->NID['value']?>&prod=<?=$myBCont->bcont_prod['value']?>';">
				
				<td class="<?if($myBCont->bcont_seen['value']!=1){echo "unseen_td";}?>"><?=$myBCont->NSerial['value']?></td>
				<td class=""><?=get_data_in("select user_name from user where user_id = '{$myBCont->bcont_dist['value']}'", 'user_name')?></td>
				<td class=""><?=get_data_in("select user_name from user where user_id = '{$myBCont->bcont_cust['value']}'", 'user_name')?></td>
				<td class=""><?=get_data_in("select ".prod_title_x." from product where prod_id = '{$myBCont->bcont_prod['value']}'", prod_title_x)?></td>
				<td class=""><?=$myBCont->bcont_discount['value']?></td>
				<td class=""><?=$myBCont->bcont_payment['value']?></td>
				<td class="license_td <?=$is_lice?>"><?=$myBCont->bcont_license['value']?></td>
				<td class=""><?=$myBCont->NDate['value']?></td>

//show admin tools (edit/delete) if I was admin or have the page's priv
				<? if(user_has_permission(array("A", $pagePRIV))) {?><td class="admin_tools_td"><?=$myframe->DisplayAdminTools("bconts", $myBCont->NSerial['value'])?></td><? }
			?></tr><?
			}
			$i++;
		} }
		?></table><?
		//////END CARDS NAVIGATOR
		$serv->Draw_Navigator_Line();
		$myframe->close_box("panel");
	break;
	// DELETE_______________________________________________________________________________
	case "d":
		/***/IS_SECURE($pagePRIV);/***/
		
		if ($myBCont->NID['IsNew'])  break;
		
		$myframe->open_box("withTree", Del_x,"panel", $pagePRIV, $adding);
		$myBCont->DisplayDelMsg();
		$myframe->close_box("panel");
	
	break;	
}
/*************************   related pages  ******************************/
$myframe->Display_Related_Pages(BConts, array("A", $pagePRIV, $myBCont->NSerial['value']));

$myframe->footer();
?> 