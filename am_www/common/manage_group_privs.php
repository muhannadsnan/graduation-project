<?php
session_start();
include_once '../common/pframe.php';
include_once '../common/privileges.php';
$myf=new pframe();
$pagePRIV = array("GROUPS_MAN","USERS_MAN"); 
/***/IS_SECURE($pagePRIV);/***/ // "A" is also checked inside

switch ($_REQUEST['v'])
{
	case "add": 
/*******************  adding privs to groups, users OR add users to group  *******************/
		
		$all_privs = Get_All_Privileges($_SESSION['access_to_all_privs']);
		
		$arr = $_SESSION['PRIVS'];
		if($_REQUEST['PR'] != "" || $_REQUEST['members'] != "") 
		{
			if(! in_array($_REQUEST['PR'], $arr))
			{
				$add_this_priv = $all_privs[$_REQUEST['PR']];
				 
				if($_SESSION['back_page']=="groups")
					add_priv_to_group($_REQUEST['group'], $add_this_priv);
				
				elseif($_SESSION['back_page']=="users")
					add_priv_to_user($_REQUEST['user'], $add_this_priv);
			}			
			
			if( $_REQUEST['members'] != ""){
				add_user_to_group($_REQUEST['user'], $_REQUEST['group']);
			}			
			
			echo $_SESSION['back_uri'];		
			header("location: {$_SESSION['back_uri']}");
		}
	break;
	
	case "del":	
/*******************  deleting  privs to groups, users OR add users to group  *******************/
		
		if( !$_REQUEST['members']){
			$all_privs = Get_All_Privileges($_SESSION['access_to_all_privs']);
			$del_this_priv = $all_privs[$_REQUEST['PR']];echo "<br/>del_this_priv=$del_this_priv";
			
			if($_SESSION['back_page']=="groups")
				del_priv_from_group($_REQUEST['group'], $del_this_priv);
			
			elseif($_SESSION['back_page']=="users")
				del_priv_from_user($_REQUEST['user'], $del_this_priv);
		}
		
		elseif( $_REQUEST['members'] != ""){
			del_user_from_GROUP($_REQUEST['user'], $_REQUEST['group']);
		}
		
		header("location: {$_SESSION['back_uri']}");
	break;
}

function add_priv_to_group($group, $add_this_priv)
{
//grant priv to group

	$sql= "select group_id from user_group_privs where priv = '{$add_this_priv}' and group_id='{$group}' ";
	echo " $sql <br/>";
	if(!@mysql_num_rows(mysql_query($sql)) != 0){ 
		$sql1="insert into user_group_privs values('{$group}','{$add_this_priv}')"; echo "<br/>$sql1<br/>";
		cmd($sql1);
	}echo "</br>insert into user_group_privs values('{$group}','{$add_this_priv}')</br>";;
}/////////////////////////////////////////////////////////////////////////////////

function del_priv_from_group($group, $del_this_priv)
{
//revoke priv from group

	$sql3="delete from user_group_privs where group_id='{$group}' and priv='{$del_this_priv}' ";	cmd($sql3);
}/////////////////////////////////////////////////////////////////////////////////

function add_priv_to_user($user, $add_this_priv)
{
// grant priv to user

	$sql= "select group_id from user_privs where priv = '{$add_this_priv}'  and group_id='{$user}' ";
	if(!@mysql_num_rows(mysql_query($sql)) != 0){
		$sql4="insert into user_privs values('{$user}','{$add_this_priv}',0)"; echo "<br/>".$sql4."<br/>";
		cmd($sql4);
	}
}/////////////////////////////////////////////////////////////////////////////////

function del_priv_from_user($user, $del_this_priv)
{
//revoke priv from user

	$sql5="delete from user_privs where user_id='{$user}' and user_priv='{$del_this_priv}' "; echo "<br/>$sql5<br/>";
	cmd($sql5);
}/////////////////////////////////////////////////////////////////////////////////

function add_user_to_group($userid, $groupid)
{
// add a certain user/member to a certain group

	$sql= "select user_id from user_groups where user_id = '{$userid}' and user_group_id='{$groupid}' ";
	echo " $sql <br/>";
	if(!@mysql_num_rows(mysql_query($sql)) != 0){
		$sql1="insert into user_groups values('{$userid}','{$groupid}')"; echo "<br/>$sql1<br/>";
		cmd($sql1);
	}
}/////////////////////////////////////////////////////////////////////////////////

function del_user_from_GROUP($userid, $groupid)
{
// eject user from group

	$sql5="delete from user_groups where user_id='{$userid}' and user_group_id='{$groupid}' "; echo "<br/>$sql5<br/>";
	cmd($sql5);
}/////////////////////////////////////////////////////////////////////////////////



11) ../common/reset_password.php



When a user wants tp reset his password from the profile page. In addition, this handles the "forgot-password operations" within a very secure  fabric…


<?php
ob_start ();
session_start();
include_once '../common/pframe.php';
$myframe = new pframe ();
$myframe->header ( Reset_Password );
$myframe->open_box("", Reset_Password,"panel");
// calling libraries as usual + declaring objects + header boxes

if_POST();

if($_SESSION["GID"]) //came from user profile
{
	Password_Reset();	// show html form of the Reset Password
}
else{ // came here from reset pass link 
	//otid exists and within this week ??
	//echo $_SESSION['reset']['otid']."</br>".$_SESSION['reset']['user_hash']."<br/>";
	
	if( /*first access*/$myframe->Valid_OTID($_REQUEST['otid']) || 
            /*or has session*/($_SESSION['reset'] && $_SESSION['reset']['otid'] != ""))
	{
		echo '<p style="color:red">'.one_time_use_link.'</p>';
		if($myframe->Valid_OTID($_REQUEST['otid']))
//is this OTID valid or has been used before
		{
			$_SESSION['reset']['otid'] = $_REQUEST['otid'];
			$_SESSION['reset']['user_hash'] = $_REQUEST['x'];
			
			$myframe->Delete_OTID($_SESSION['reset']['otid'], $_SESSION['reset']['user_hash']); // at first access the otid is deleted so the link will not be used again				
		}
		//else{echo '<p style="color:red">'.this_link_is_expired_now.'</p>';}
		
		Password_Reset();
	}
	else{echo Expired_Link."<br/>";	}
}

$myframe->close_box("panel");
///////////////////////////////////////////////////////////////////////////////////////////
function if_POST()
{
//if submit performed, then manipulate

	include_once '../common/pframe.php';
	$myframe = new pframe ();
	
	if($_POST['doit'])
	{
		if($_POST['pass1'] == $_POST['pass2'] ) //passwords matching and not empty
		{
			if($_POST['pass1'] != "") // password not empty
			{
				$ID="";
// using session for maximum protection
				if($_SESSION["GID"]){$ID = $_SESSION["GID"];}

//the user is sent as a hash code so the link can not be modified by hackers
				else{$ID = $myframe->bring_USER_by_hash($_REQUEST['x']);} 
// bring the user_id by searching in the hashes of users one by one until it's found		
				
				$md5 = md5($_POST['pass1']);
				$sql = "update user set user_password='{$md5}' where user_id='{$ID}' ";
//perform update password process for this user
				if(@cmd($sql))
				{echo Password_Reset_OK."<br/>"; unset($_SESSION['reset']);}
			}
		}
		else{
			echo passwords_dont_match; // message says the passwords don't match
		}
	}
}

function Password_Reset()
{
//HTML form including appropriate elements [2 text boxes + submit button]
	?>
	<form name="resetpassword_form" method="post" action="<?=$_SERVER['PHP_SELF']?>?<?=$_SERVER['QUERY_STRING']?>">
	
		<table width="50%" style="width:500px;border:1px #ccc solid; border-radius:10px; margin:20px auto;padding:30px">
		
			<tr style="height:60px;"><td><label><?=New_Password?> *</label></td>
				<td><input type="password" name="pass1"  style="width:200px;" autocomplete="off"></td>	</tr>
	
			<tr><td><label><?=Confirm_New_Password?> *</label></td>
				<td><input type="password" name="pass2"  style="width:200px;" autocomplete="off"></td>	</tr>

	
			<tr><td colspan="2" style="text-align: center">
				<input type="submit" name="doit" value="<?=Reset_Password?>" style="height:35px;font-weight:bold;font-size:14pt;margin-top:20px;cursor:pointer">	</td>	</tr>
	
		</table>
	</form>
	<?
}