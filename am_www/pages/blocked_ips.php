<?php 

// as usual calls and declares ………..

include_once '../common/pframe.php';
include_once '../cms/navigator.php';
include_once "../obj/blocked_ip.class.php";

$myframe=new pframe();
$myframe->header(BlockedIPs);
$myBlock = new Blocked_ip($_REQUEST['NID']);
$pagePRIV = "BLOCKED_IPS_MAN";
if(user_has_permission(array("A", $pagePRIV))){	$adding ="";/*if adding is about another OBJ*/	}else{$adding=""; $pagePRIV="";	}

switch ($_REQUEST['v'])
{
	case "e":/*************   editor   ***********************/
		
// as usual, security tree header and editor

	/***/IS_SECURE($pagePRIV);/***/
		$myframe->open_box("withTree", $myBlock->ip['value'],"panel", $pagePRIV, $adding);
		$myBlock->DisplayEditor("_n");
		$myframe->close_box("panel");
		
		?>
		<button id="edit_protected" onclick="edit_protected()" style="font-size:14pt;font-weight:bold;padding:5px 40px"><?=Edit_x?></button>
		<script>

//disable fields for protection
			var selct=document.getElementById("txt_ip");				selct.disabled = true;
			var selct=document.getElementById("txt_user");			selct.disabled = true;
			var selct=document.getElementById("txt_attempts");			selct.disabled = true;
			var selct=document.getElementById("txt_reason");			selct.disabled = true;
			var selct=document.getElementById("txt_NDate");			selct.disabled = true;
			function edit_protected() {var selct=document.getElementById("txt_attempts");			selct.disabled = false;}		
		</script><?
	
	break;
		
	case "c":/*************   card   ***********************/
	/***/IS_SECURE($pagePRIV);/***/
		echo "no c parameter, no card here";
	break;
	
	case "t":/*************   Table of All elements   ***********************/

// as usual, security, tree header, sql query, table of results
	/***/IS_SECURE($pagePRIV);/***/
		$myframe->open_box("withTree", BlockedIPs,"panel", $pagePRIV, $adding);
		
		$wherestr="";
		$sql="select * from {$myBlock->tblname} {$wherestr} order by NDate";
		$nav=new Navigator($sql, $_GET['cur_page'], 10, "select count(NID) from {$myBlock->tblname} {$wherestr} ");
		//
		?><table class="global_tbl sortable">
// empty result or NOT ?
		<? if(@mysql_num_rows(mysql_query($sql)) == 0){?><tr><td><?=No_Rows_Selected?></td></tr><?}else{?>
			<tr>
				<th><?=blocked?></th>
				<th><?=ip?></th>
				<th><?=attempts?></th>
				<th><?=reason?></th>
				<th><?=NDate?></th>
			</tr><?
		////START CARDS NAVIGATOR
		$i=0;
		$tr_class="";
		while ($Row=mysql_fetch_array($nav->result)) //#
		{
			$myBlock->FillIn($Row);$id=$myBlock->user_id["value"];
			if($i%2==0){$tr_class="tr_1";}else{$tr_class="tr_2";}
			$before_7_days="";
			?>
// highlight the blocked IPs (over 10 illegal attempts) with red color

			<tr class="<?=$tr_class?> <?if($myBlock->attempts['value']>10){echo"blocked_ip";}?>" onclick="document.location = '../pages/our_customers.php?lang=<?=$GLOBALS["lang"]?>&v=c&NID=<?=$myBlock->NID['value']?>';">
				<td><?if($myBlock->attempts['value']>10){echo blocked;}else{echo NOT_blocked;}?></td>
				<td class=""><?=$myBlock->ip['value']?></td>
				<td class=""><?=$myBlock->attempts['value']?></td>
				<td class=""><?=constant($myBlock->reason['value'])?></td>

// Admin Tools if you have permission

				<? if(user_has_permission(array("A", $pagePRIV))) {?><td class="admin_tools_td"><?=$myframe->DisplayAdminTools("blocked_ips", $myBlock->NID['value'])?></td><? }
			?></tr><?
			$i++;
		}}
		?></table><script>   $('.DEL_admintool').hide();   </script><?
		//////END CARDS NAVIGATOR
		$nav->Draw_Navigator_Line();
		$myframe->close_box("panel");
	break;
	
	case "d":/*************   delete   ******************************************/
	/***/IS_SECURE($pagePRIV);/***/
		if ($myBlock->NID['IsNew'])  break;
		
		$myframe->open_box("withTree", Del_blocked_ip,"panel", $pagePRIV, $adding);
		$myBlock->DisplayDelMsg();
		$myframe->close_box("panel");
	break;
}

$myframe->footer();
?>