<?php 
include_once '../common/pframe.php';
include_once "../obj/site_config.class.php";

$myframe=new pframe();
$myframe->header(Configs);
$mySC = new Site_config($_REQUEST['NID']);
$pagePRIV = "CONFIGS_MAN";
if(user_has_permission(array("A", $pagePRIV))){	$adding ="";/*if adding is about another OBJ*/	}else{$adding=""; $pagePRIV="";	}

switch ($_REQUEST['v'])
{
	case "e"://____________________________________________
		
	/***/IS_SECURE($pagePRIV);/***/
		echo '<script type="text/javascript" src="../cms/jscolor.js"></script>';
		
		if ($_REQUEST['NID'] =='IsNew'){	$ttl=Add_x." ".Site_Configs;} 	
		else{ 
//specify editing fields of each configuration

			$ttl=Edit_x." ".Site_Configs;
			$conf = get_data_in("select config from site_config where NID='{$_REQUEST['NID']}' ", "config");			
			if($conf == 'slider' ){$mySC->value['control'] = 'none'; $mySC->pic_social['control'] = 'none';	}
			elseif($conf == 'social_media'){$mySC->value['control'] = 'none';$mySC->pic_slider['control'] = 'none';	}
			elseif($conf == 'header_logo' || $conf == 'fav_ico'){	$mySC->link['control'] = 'none';$mySC->value['control'] = 'none';$mySC->pic_slider['control'] = 'none';$mySC->title['control'] = 'none';$mySC->config['control'] = 'text';	}
			
			else{$mySC->config['control'] = 'text';	$mySC->link['control'] = 'none';$mySC->pic_slider['control'] = 'none';$mySC->pic_social['control'] = 'none';$mySC->title['control'] = 'none';	}
		}
 // EDITOR		
		
		$myframe->open_box("withTree", $ttl,"panel", $adding);
		$mySC->DisplayEditor("_n");
		$myframe->close_box("panel");
		
		if ($_REQUEST['NID'] !='IsNew'){
//not possible to edit a type of a config 
//also not possible to add types other than slider or social media 
		?><script>
			var selct=document.getElementById('txt_config');
			selct.disabled = true;
			document.getElementById("txt_value").setAttribute('type','color');	
		</script><? 
		}
	break;//____________________________________________
		
	case"t": /******************* Show Site Configurations *******************/
		
	/***/IS_SECURE($pagePRIV);/***/
		$myframe->open_box("withTree", Site_Configs,"panel", $pagePRIV, $adding);
		$wherestr="";
		if($_REQUEST['groupby'])	{$wherestr=" where config='{$_REQUEST['groupby']}' ";}
		
		$sql="select * from {$mySC->tblname} {$wherestr} order by config"; //echo $sql;
		$tbl=table($sql);
		//
		?><div class="site_configs"><table class="global_tbl sortable">
				<? if(@mysql_num_rows(mysql_query($sql)) == 0){?><tr><td><?=No_Rows_Selected?></td></tr><?}else{?>
					<tr>
						<th><?=config?></th>
						<th><?=title?></th>
						<th><?=link?></th>
						<th><?=value?></th>
						<th><?=pic_social?></th>
						<th><?=pic_slider?></th>
					</tr>
				<?
				$i=0;
				$tr_class="";
				while ($Row=mysql_fetch_array($tbl))
				{
					$mySC->FillIn($Row);$id=$mySC->user_id["value"];
					if($i%2==0){$tr_class="tr_1";}else{$tr_class="tr_2";}
					$css = "background-color:{$mySC->value['value']}; ";
					?>
					<tr class="<?=$tr_class?>" style="cursor:auto;">
						<td class="<?=$mySC->config['value']?>">	<?=$mySC->config['value']?>	</td>
						<td class="">	<?=$mySC->title['value']?>	</td>
						<td class="">	<?=$mySC->link['value']?>	</td>
						<td style="<? if($mySC->config['value']!='slider' && $mySC->config['value']!='social') echo $css;?>">	
						<?=$mySC->value['value']?>	</td>
						<td class="">	<?=$mySC->pic_social['ext']?>	</td>
						<td class="">	<?=$mySC->pic_slider['ext']?>	</td>
						<? if(user_has_permission(array("A", $pagePRIV))) {?><td class="admin_tools_td"><?=$myframe->DisplayAdminTools("site_configs", $mySC->NID['value'])?></td><? }
					?></tr><?
					?><script>   $(".DEL_admintool").attr('class', 'DEL_admintool_'+ <? echo json_encode($mySC->config['value']);?>); </script><?
						
					$i++;
				}}
				?></table></div><?
				
//hide delete tool for configurations not are (slider or social media)

				?><script> $(".DEL_admintool_color_main ,.DEL_admintool_color_second,.DEL_admintool_color_third,.DEL_admintool_fav_ico,.DEL_admintool_side_bar_button,.DEL_admintool_header_logo").hide(); </script><?
				$myframe->close_box("panel");
	break;
	
	case "d"://____________________________________________
		
	/***/IS_SECURE($pagePRIV);/***/
		if ($mySC->NID['IsNew'])  break;
	
		$myframe->open_box("withTree", Del_x." ".Site_Configs ,"panel", $pagePRIV, $adding);
		$mySC->DisplayDelMsg();
		$myframe->close_box("panel");
		
	break;//____________________________________________
}

/*************************   related pages  ******************************/
$myframe->Display_Related_Pages(Services,array("A", $pagePRIV));

$myframe->footer();
?>