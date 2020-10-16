<?php 
include_once '../db/mysqlcon.php';
include_once '../common/pframe.php';
include_once '../cms/navigator.php';
include_once "../obj/user.class.php";
$myframe=new pframe();
$myframe->header(Our_Customers);
$myCustomer = new User($_REQUEST['NID']);
$pagePRIV = "CUSTOMERS_MAN";
if(user_has_permission(array("A", $pagePRIV))){	$adding ="";/*if adding is about another OBJ*/	}else{$adding=""; $pagePRIV="";	}

switch ($_REQUEST['v']) {
	
	case "e":/*************   editor   ******************************************/
	/***/IS_SECURE($pagePRIV);/***/
		
		$myframe->open_box("", Our_Customers,"panel" ,$pagePRIV ,$adding);
		if ($myCustomer->NID['IsNew']){	$ttl=Add_Customer; }else { $ttl=Edit_Customer;	}
	
		$myCustomer->user_cat['control'] = 'text';  $myCustomer->user_cat['value'] = 'customer';
		$myframe->open_box("withTree", $ttl,"panel", $pagePRIV, $adding);
		$myCustomer->DisplayEditor("_n");
		$myframe->close_box("panel");
		
// lock user category, it must stay customer
		
?><script>	document.getElementById("txt_user_cat").disabled = true;</script><? 
		break;
	
	case "c":/*************   card   ******************************************/
		
		$myframe->open_box("withTree", $myCustomer->user_name['value'],"panel", $pagePRIV ,$adding);
	
		$myframe->user_card($myCustomer, "our_customers", "");//print_r($myCustomer);
	
		$myCustomer->More_DVNom();
	
		$myframe->close_box("panel");
	break;	
	
	case "t":/*************   table   ******************************************/
		
		$myframe->open_box("withTree", Our_Customers,"panel", $pagePRIV, $adding);
		$wherestr=" where user_cat = 'customer' and in_home=1 ";

//bring users with category "customer" from DB

		$sql="select * from {$myCustomer->tblname} {$wherestr} order by user_name desc";
		$cust=new Navigator($sql, $_GET['cur_page'], 10, "select count(NID) from {$myCustomer->tblname} {$wherestr} ");
		//
		?><table class="global_tbl sortable">
		<? if(@mysql_num_rows(mysql_query($sql)) == 0){?><tr><td><?=No_Rows_Selected?></td></tr><?}else{?>
			<tr>
				<th><?=Customer_Name?></th>
				<th><?=user_email?></th>
				<th><?=user_phone?></th>
				<th><?=user_address?></th>
				<th><?=user_country?></th>
				<th><?=user_city?></th>
			</tr>
		<?
		////START CARDS NAVIGATOR
		$i=0;
		$tr_class="";
		while ($Row=mysql_fetch_array($cust->result))
		{
			$myCustomer->FillIn($Row);$id=$myCustomer->user_id["value"];
			if($i%2==0){$tr_class="tr_1";}else{$tr_class="tr_2";}
			?>
			<tr class="<?=$tr_class?>" onclick="document.location = '../pages/our_customers.php?lang=<?=$GLOBALS["lang"]?>&v=c&NID=<?=$myCustomer->NID['value']?>';">
				<td class=""><?=$myCustomer->user_name['value']?></td>
				<td class=""><?=$myCustomer->user_email['value']?></td>
				<td class=""><?=$myCustomer->user_phone['value']?></td>
				<td class=""><?=$myCustomer->user_address['value']?></td>
				<td class=""><?=$myCustomer->user_country['value']?></td>
				<td class=""><?=$myCustomer->user_city['value']?></td>
				<? if(user_has_permission(array("A", $pagePRIV))) {?><td class="admin_tools_td"><?=$myframe->DisplayAdminTools("our_customers", $myCustomer->user_id['value'])?></td><? }
			?></tr><?
			$i++;
		}}
		?></table><?
		//////END CARDS NAVIGATOR
		$cust->Draw_Navigator_Line();
		$myframe->close_box("panel");
	break;
	
	case "d":/*************   delete   ******************************************/
		
	/***/IS_SECURE($pagePRIV);/***/
		if ($myCustomer->NID['IsNew'])  break;
		
		$myframe->open_box("withTree", Our_Customers,"panel", $pagePRIV, $adding);
		$myCustomer->DisplayDelMsg();
		$myframe->close_box("panel");
	break;
}
?><style>.global_tbl td{border:0px #ccc solid;padding:2px 8px;max-width:150px;overflow:hidden;white-space:normal;}</style><? 

/*************************   related pages  ******************************/
$myframe->Display_Related_Pages(Our_Customers,array("A", $pagePRIV));
?>