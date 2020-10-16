<?php
ob_start ();
include_once '../common/pframe.php';
include_once '../common/privileges.php';
include_once '../obj/user.class.php';
include_once '../cms/navigator.php';

$myframe = new pframe ();
$myframe->header ( Users );
$myUser = new User ( $_REQUEST ['NID'] );
$pagePRIV = "USERS_MAN";
if(user_has_permission(array("A", $pagePRIV))){	$adding ="";/*if adding is about another OBJ*/	}else{$adding=""; /*$pagePRIV="";*/	}
$_SESSION['back_page'] = "users";

// for security, make sure that the user is logged in & (is Admin or has the page's permission)

if($_SESSION['GID'])
{
	if(user_has_permission(array("A", $pagePRIV))){	$USERS_MAN = true;}
	else{

// remember user type in all page life (admin, customer, distributor, employee)

		if(user_has_permission(array("A", "CUSTOMERS_MAN"))){	$CUSTOMERS_MAN = true; $pagePRIV = "CUSTOMERS_MAN";}
		if(user_has_permission(array("A", "DISTRIBUTORS_MAN"))){	$DISTRIBUTORS_MAN = true; $pagePRIV = "DISTRIBUTORS_MAN";}
		if(user_has_permission(array("A", "EMPLOYEES_MAN"))){	$EMPLOYEES_MAN = true; $pagePRIV = "EMPLOYEES_MAN";}		
	}
}else{ 
// not customer , not user_manager. then, what are you doing here :@ !!
// go to error page and record that as an illegal attempt for his IP

		/***/IS_SECURE("", "not_secure");/***/} 

switch ($_REQUEST ['v'])
{
	case "e":/*************   edit   ******************************************/
		
	/***/IS_SECURE("{$pagePRIV},CUSTOMERS_MAN,EMPLOYEE_MAN,DISTRIBUTOR_MAN");/***/

// check more privileges before starting

		if ($myUser->NID['IsNew']){	$ttl=Add_User; }else { $ttl=Edit_User; }
		
		if($myframe->Is_Distributor()){ //Distributors can only add 'customer users'
			$myUser->user_cat['control'] = 'text';  $myUser->user_cat['value'] = 'customer';
		}
				
		$myframe->open_box("withTree", $ttl,"panel", $pagePRIV, $adding);
		$myUser->DisplayEditor("_n");
		
//when adding a user to a group
		
		if($_REQUEST['group'] && $_REQUEST['NID'] !="IsNew"){
			$ins="insert into user_groups values ('{$myUser->NID['value']}' 
                                                       ,'{$_REQUEST['group']}')";//echo $ins;
			if(cmd($ins)){ header("location: ../pages/groups.php?lang={$GLOBALS['lang']}&v=t&NID={$_REQUEST['group']}"); }
		}

// if the user is distributordon't allow him create users in different type but customer

		if($myframe->Is_Distributor())
		{	?><script>	var selct=document.getElementById("txt_user_cat");	selct.disabled = true;	</script><? }
		
		$myframe->close_box("panel");
		break;
		
	case "c":/*************   card   ******************************************/
		
	/***/IS_SECURE("{$pagePRIV},CUSTOMERS_MAN,EMPLOYEE_MAN,DISTRIBUTOR_MAN");/***/
		
		$myframe->open_box("withTree", $myUser->user_name['value'],"panel", $pagePRIV, $adding);
		
		$myframe->User_Card($myUser, "users",$pagePRIV);//print_r($myUser);
		
		$myframe->close_box("panel");
		break;
		
	case "t":/*************   table   ******************************************/
		
		//panel title
		if($_REQUEST['ucat'] == 'customer'){$ttl = Customer_Management;}
		elseif($_REQUEST['ucat'] == 'employee'){$ttl = Emps_Management;	}
		elseif($_REQUEST['ucat'] == 'distributor'){$ttl = Dists_Management;	}
		else {$ttl = Users_Management;}
		$myframe->open_box("withTree", $ttl,"panel", $pagePRIV, $adding);
		
		// "Group-by" buttons filter user by user category

		?><div class="group_by_div"><? ?></div><? 
			if($USERS_MAN){?> <a class="group_by_button" href="../pages/users.php?lang=<?=$GLOBALS['lang']?>&v=t"><?=All_Users?></a> <?}
			if($USERS_MAN||$DISTRIBUTORS_MAN){?> <a class="group_by_button" href="../pages/users.php?lang=<?=$GLOBALS['lang']?>&v=t&ucat=distributor"><?=Dists_Management?></a> <?  }
			if($USERS_MAN||$CUSTOMERS_MAN){?> <a class="group_by_button" href="../pages/users.php?lang=<?=$GLOBALS['lang']?>&v=t&ucat=customer"><?=Custs_Management?></a> <?  }
			if($USERS_MAN||$EMPLOYEES_MAN){?> <a class="group_by_button" href="../pages/users.php?lang=<?=$GLOBALS['lang']?>&v=t&ucat=employee"><?=Emps_Management?></a> <?}
			if($USERS_MAN){?> <a class="group_by_button" href="../pages/users.php?lang=<?=$GLOBALS['lang']?>&v=m"><?=User_Privileges?></a> <?}
		$wherestr="";
			
		// distributors are only allowed to manage cutomers, not other user categories
		if($myframe->Is_Distributor()){ 
			if($_REQUEST['ucat']=='employee') /***/IS_SECURE("", "not_secure");/***/
			if($_REQUEST['ucat']=='distributor') /***/IS_SECURE("", "not_secure");/***/
			if($_REQUEST['ucat']=='admin') /***/IS_SECURE("", "not_secure");/***/
		}
		if($_REQUEST['ucat']){ $wherestr=" where user_cat='{$_REQUEST['ucat']}' ";}

		// a distributor can only manage his own customers

		if($myframe->Is_Distributor()){ $wherestr.=" and user_id in (select cust_id from customers_of_dist where dist_id='{$_SESSION['GID']}' )";}
		$sql="select * from {$myUser->tblname} {$wherestr} order by NDate desc";
		//echo $sql;
		$usr=new Navigator($sql, $_GET['cur_page'], 30, "select count(NID) from {$myUser->tblname} {$wherestr} ");
		//
		?><table class="global_tbl sortable">
				<? if(@mysql_num_rows(mysql_query($sql)) == 0){?><tr><td><?=No_Rows_Selected?></td></tr><?}else{?>
					<tr>
						<th><?=user_name?></th>
						<th><?=user_cat?></th>
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
				while ($Row=mysql_fetch_array($usr->result))
				{
					$myUser->FillIn($Row);//$id=$myUser->user_id["value"];
					if($i%2==0){$tr_class="tr_1";}else{$tr_class="tr_2";}
					?>
					<tr class="<?=$tr_class?> <? if($_REQUEST['NID'] == $myUser->NID['value']){ echo "tr_highlighted";}?>" onclick="document.location = '../pages/users.php?lang=<?=$GLOBALS["lang"]?>&v=c&NID=<?=$myUser->NID['value']?>';">
						<td class=""><?=$myUser->user_name['value']?></td>
						<td class=""><?=$myUser->user_cat['value']?></td>
						<td class=""><?=$myUser->user_email['value']?></td>
						<td class="" style="max-width:130px;"><?=$myUser->user_phone['value']?></td>
						<td class="" style="max-width:130px;"><?=$myUser->user_address['value']?></td>
						<td class="" style="max-width:70px"><?=$myUser->user_country['value']?></td>
						<td class=""style="max-width:70px"><?=$myUser->user_city['value']?></td>
						<? if(user_has_permission(array("A", $pagePRIV, "CUSTOMERS_MAN", "DISTRIBUTORS_MAN", "EMPLOYEES_MAN"))) {
							?><td class="admin_tools_td"><div style="display:inline-block"><?=$myframe->DisplayAdminTools("users", $myUser->user_id['value'])?></div>
							  <a href="../pages/users.php?lang=<?=$GLOBALS['lang']?>&v=priv_editor&NID=<?=$myUser->NID['value']?>">
							  <img src="../images/priv_tool.png"  style="width:32px;display:inline-block" alt="<?=Privileges?>"/></a></td><? }
					?></tr><?
					$i++;
				}}
				?></table><?
				//////END CARDS NAVIGATOR
				$usr->Draw_Navigator_Line();
				$myframe->close_box("panel");
				$_SESSION['back_php'] = "users"; // this is for the tree
		break;
		
	case "d":/*************   del   ******************************************/
		
	/***/IS_SECURE("{$pagePRIV},CUSTOMERS_MAN,EMPLOYEE_MAN,DISTRIBUTOR_MAN");/***/	

		$myframe->open_box("withTree", Del_User,"panel", $pagePRIV, $adding);
		$myUser->DisplayDelMsg();
		$myframe->close_box("panel");
	break;
	
	case "m":/*************   Users Management   **********************************/
		
	/***/IS_SECURE("{$pagePRIV},CUSTOMERS_MAN,EMPLOYEE_MAN,DISTRIBUTOR_MAN");/***/
		
		$myframe->open_box("withTree", Users_Management,"panel", $pagePRIV, $adding);
		?><div class="group_by_div"><? ?></div><?

// group-by buttons vary according to the privs that user has

			if($USERS_MAN){?> <a class="group_by_button" href="../pages/users.php?lang=<?=$GLOBALS['lang']?>&v=t"><?=All_Users?></a> <?}
			if($USERS_MAN||$DISTRIBUTORS_MAN){?> <a class="group_by_button" href="../pages/users.php?lang=<?=$GLOBALS['lang']?>&v=t&ucat=distributor"><?=Dists_Management?></a> <?  }
			if($USERS_MAN||$CUSTOMERS_MAN){?> <a class="group_by_button" href="../pages/users.php?lang=<?=$GLOBALS['lang']?>&v=t&ucat=customer"><?=Custs_Management?></a> <?  }
			if($USERS_MAN||$EMPLOYEES_MAN){?> <a class="group_by_button" href="../pages/users.php?lang=<?=$GLOBALS['lang']?>&v=t&ucat=employee"><?=Emps_Management?></a> <?}
			if($USERS_MAN){?> <a class="group_by_button" href="../pages/users.php?lang=<?=$GLOBALS['lang']?>&v=m"><?=User_Privileges?></a> <?}
		$wherestr="";
		if($_REQUEST['ucat']){ $wherestr=" where user_cat='{$_REQUEST['ucat']}' ";}
		$sql="select * from {$myUser->tblname} {$wherestr} order by user_name ";
		$usr=new Navigator($sql, $_GET['cur_page'], 30, "select count(NID) from {$myUser->tblname} {$wherestr} ");
		//
//show users  (with the user type filtering)

		?><table class="global_tbl sortable">
		<? if(@mysql_num_rows(mysql_query($sql)) == 0){?><tr><td><?=No_Rows_Selected?></td></tr><?}else{?>

// show users with privs for each one

			<tr>
				<th><?=user_name?></th>
				<th><?=user_cat?></th>
				<th style="width:50%"><?=user_priv?></th>
			</tr>
		<?
		////START CARDS NAVIGATOR
		$i=0;
		$tr_class="";
		while ($Row=mysql_fetch_array($usr->result))
		{
			$myUser->FillIn($Row); //echo $myUser->NID['value'];
			if($i%2==0){$tr_class="tr_1";}else{$tr_class="tr_2";}
			?>
			<tr class="<?=$tr_class?> <? if($_REQUEST['NID'] == $myUser->NID['value']){ echo "tr_highlighted";}?>" onclick="document.location = '../pages/users.php?lang=<?=$GLOBALS["lang"]?>&v=c&NID=<?=$myUser->NID['value']?>';">
				<td class=""><?=$myUser->user_name['value']?></td>
				<td class=""><?=$myUser->user_cat['value']?></td>
				<td class=""><?foreach(DB_USERS_Privileges($myUser->NID['value']) as $v) echo "$v</br>";?></td>

// admin tools to grant / revoke privs from user

				 <? if(user_has_permission(array("A", $pagePRIV))) {?> <td class="admin_tools_td"><div class="admin_tools"><a href="../pages/users.php?v=priv_editor&lang=<?=$GLOBALS['lang']?>&NID=<?=$myUser->NID['value']?>" class="EDT_admintool"><img src="../images/edtimg.png" /></a></div></td><? }?>
			</tr><?
			$i++;  
		}}
		
		?></table><script>   $('.DEL_admintool').hide();   </script><? 
		//////END CARDS NAVIGATOR
		$usr->Draw_Navigator_Line();
		$myframe->close_box("panel");
	break;
	
	case "priv_editor":/*************   privileges   ******************************************/
		
	/***/IS_SECURE($pagePRIV);/***/
		
		if ($myUser->NID['IsNew']){$ttl=Add_x." ".priv;}else { $ttl=Edit_x." ".priv;}
		
		$myframe->open_box("withTree", $ttl,"panel",$pagePRIV  ,$adding);
		echo '<div>'.Privileges_Message_Empty.'</div></br>';

//***************   Privileges Editor   ***************/
		PrivilegesEditor($pagePRIV);
		$myframe->close_box("panel");
	break;
	
	case "p":/*************   profile   ******************************************/
		
//check security first

		if($_SESSION['GID'] == $myUser->NID['value'])
		{
// Initialize user attributes and show specific ones

			$myframe->open_box("withTree", MyProfile, "panel" ,"" ,$adding);
			$myUser->user_name['required'] ='';
			$myUser->user_password['required'] ='';
			$myUser->in_home['required'] ='';
			$myUser->user_cat['required'] ='';
			$myUser->user_name['control'] ='none';
			$myUser->user_password['control'] ='none';
			$myUser->in_home['control'] ='none';
			$myUser->user_cat['control'] ='none';
			$myUser->DisplayEditor();			
			
//show user's privs (not for customers)

			 if( !$myframe->Is_Customer()){?>
			<div style="display:inline-block;margin-right:90px;vertical-align:top;">
				<p style="padding:20px;font-weight:bold;border:2px #999 solid;width:200px;margin:auto;text-align:center;"><?=MY_Privs?></p>
				<div style="width:200px;margin:auto;border:1px #999 solid;text-align:center;padding:20px 0px;border-top:0px">
					<? foreach($_SESSION['PRIVS'] as $v) echo "$v</br>";  ?></div>
			</div><? 
			} 
			
			?><br/><br/><br/><a href="../common/reset_password.php" style="color:red;font-weight:bold;border:1px red solid;padding:10px 20px;clear:both;margin:20px 30px;"><?=Reset_Password?></a><br/><br/><? 
			
			$myframe->close_box("panel");
		}
		else{/***/IS_SECURE("", "not_secure"); /** will record this as an illegal action for this ip **/	}
	break;
}

/*************************   related pages  ******************************/
$myframe->Display_Related_Pages(User,array("A", $pagePRIV));

// =========================================================================================================================================
$myframe->footer ();
ob_flush ();