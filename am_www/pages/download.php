<?php
ob_start();
include_once '../common/pframe.php';
include_once '../obj/product.class.php';
include_once '../cms/navigator.php';

$myframe=new pframe();
$myframe->header(View_Downloads);
$myDwn=new Product($_REQUEST['NID']);
$pagePRIV = "DOWNLOADS_MAN";
if(user_has_permission(array("A", $pagePRIV))){	$adding ="";/*if adding is about another OBJ*/	}else{$adding=""; $pagePRIV="";	}

$increase_nvnom=$_REQUEST['increase_nvnom'];   // increase number of downloads

switch ($_REQUEST['v'])
{
	case "e": /***********************   add, edit   ***************************/
	/***/IS_SECURE($pagePRIV);/***/		
		if ($_REQUEST['NID'] == "add_existing"){	$ttl=Add_x;	}else { $ttl=Edit_x;  }
		
		$sql="select * from product where prod_exe ='' or prod_exe is null";
		$tbl=table($sql);
	
		if($GLOBALS['lang']=='ar'){		$lang='ar';	$nolang='en';	}
		else{	$lang='en';  	$nolang='ar';		}
		
		// changing the way fields are shown (Not appearing as usual)

		$myDwn->{prod_title_x}['control'] = 'none';		
		$myDwn->prod_id['control'] = 'fkey';
		$myDwn->prod_id['ftbl'] = 'product';
		$myDwn->prod_id['fTitle'] = prod_title_x;
		$myDwn->prod_id['fID'] = 'prod_id';
		$myDwn->prod_id['fFltr'] = " where prod_exe ='' or prod_exe is null ";		
		/***********************   hide other fields   ***************************/
		$myDwn->{'prod_title_'.$nolang}['control'] = 'none';
		$myDwn->{'prod_desc_'.$nolang}['control'] = 'none';
		$myDwn->{'prod_text_'.$nolang}['control'] = 'none';
		$myDwn->prod_cat['control'] = 'none';
		$myDwn->{'prod_desc_'.$lang}['control'] = 'none';
		$myDwn->{'prod_text_'.$lang}['control'] = 'none';
		$myDwn->prod_pic['control'] = 'none';
		$myDwn->prod_price['control'] = 'none';
		/***********************   ability to upload file   ***************************/
		$myDwn->prod_exe['control'] = 'file';
		$myDwn->prod_exe['prefix'] = 'EXE_';
		
		//when editing fill the ID (hidden) value from GET
		if($_REQUEST['NID'] != 'add_existing' && $_REQUEST['v'] == 'e'){
			$myDwn->prod_id['control'] = 'hidden';
			$myDwn->prod_id['value'] = $_REQUEST['NID'];
		}
		
		$myframe->open_box("withTree", $ttl,"panel", $pagePRIV, $adding);
		$myDwn->DisplayEditor("_n");
		$myframe->close_box("panel");
		
		//to update GET parameters when selected changes
		$url = "download.php?lang={$GLOBALS['lang']}&v=e&NID=";		
		?>
		<script>
			//redirect to modify NID parameter according to the selected option
			$( "#txt_prod_id" ).change(function() {
				location.href = <?=json_encode($url)?> + $(this).val();
				});
			// adding an empty option at the beginning of the list
			$("#txt_prod_id").prepend("<option value='add_existing' selected='selected'>...........</option>");
			$("#txt_prod_id").val(<?=json_encode($_REQUEST['NID'])?>);
		</script>
		<?
	break;
		
	case "t"; /***********************   All elements   ***************************/
	
	$myframe->open_box("withTree", View_Downloads,"panel", $adding);
	
	$wherestr=" where prod_exe <> ''";
	$sql="select * from {$myDwn->tblname} {$wherestr} order by prod_only_dw desc";
	$serv=new Navigator($sql, $_GET['cur_page'], 10, "select count(NID) from {$myDwn->tblname} {$wherestr} ");
	//
	?><table class="global_tbl  sortable">
	<? if(@mysql_num_rows(mysql_query($sql)) == 0){?><tr><td><?=No_Rows_Selected?></td></tr><?}else{?>
		<tr>
			<th><?=prod_title_ar?></th>
			<th><?=prod_desc_ar?></th>
			<th><?=prod_cat?></th>
			<th><?=prod_price?></th>
			<th><?=NVnom?></th>
			<th><?=Download?></th>
		</tr><?
	////START CARDS NAVIGATOR
	$i=0;
	$tr_class="";
	while ($Row=mysql_fetch_array($serv->result))
	{
		$myDwn->FillIn($Row);

// until now every thing is routine

		$exe_path="../documents/exe/{$myDwn->prod_id['value']}.{$myDwn->prod_exe['value']}";
		if($i%2==0){$tr_class="tr_1";}else{$tr_class="tr_2";}
		?><tr class="<?=$tr_class?> <? if($_REQUEST['NID'] == $myDwn->prod_id['value']){ echo "tr_highlighted";}?>">
			<td class=""><?=$myDwn->{'prod_title_'.$GLOBALS['lang']}['value']?></td>
			<td class=""><?=$myDwn->{'prod_desc_'.$GLOBALS['lang']}['value']?></td>
			<td class=""><?=get_data_in("select ".cat_title_x." from category where cat_id='".$myDwn->prod_cat['value']."'",cat_title_x)?></td>
			
			<td class=""><?=$myDwn->prod_price['value']?></td>
			<td class=""><?=$myDwn->NVNom['value']?></td>
			<td class="download_td"><a href="<?=$exe_path?>" target="_blank"><img  onclick="on_click_increase_nvnom('<?=$myDwn->prod_id['value']?>')" src="../images/dwimg.png"/></a></td>

// Admin Tools
			<? if(user_has_permission(array("A"))) {?>
			<td class="admin_tools_td"><?$myframe->DisplayAdminTools("download", $myDwn->prod_id['value']);  }?></td>
		</tr><?
		$i++;
	} }
	?></table><?
	$serv->Draw_Navigator_Line();
	$myframe->close_box("panel");
	break;
	
	case "increase_nvnom"; /***** increase number of downloads when clicking download button ****/

		$upd="update product set NVNom=NVNom+1 where prod_id = '{$_REQUEST["NID"]}' ";
		cmd($upd);echo $upd;
		header("location: ../pages/download.php?lang={$GLOBALS['lang']}&v=t");
	break;
	
	case "d": /***********************   delete   ***************************/
		/***/IS_SECURE($pagePRIV);/***/
				
		$myframe->open_box("withTree", Del_x,"panel", $pagePRIV, $adding);
		$myDwn->DisplayDelMsg("download");
		$myframe->close_box("panel");
		break;
}

/*************************   increase number of downloads  ******************************/
?><script type="text/javascript">
	function on_click_increase_nvnom(pid) 
	{
		window.location = "../pages/download.php?lang=<?=$GLOBALS['lang']?>&v=increase_nvnom&NID="+pid;
	}
</script><?
/*************************   related pages  ******************************/
$myframe->Display_Related_Pages(Download,array("A", $pagePRIV));

$myframe->footer();
?>