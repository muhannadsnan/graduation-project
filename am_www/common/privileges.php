<?php
session_start();
function DB_All_Privileges($GID, $login=true)
{
//brings all privileges for a given user (groups privs – user privs)

	$group_str = DB_Groups_AS_STR($GID, $login);
	 
	$privs_from_groups = DB_GROUPS_Privileges($group_str);
	 
	$user_privs = DB_USER_Privileges($GID);
	 
	$ALL=array_merge($user_privs, $privs_from_groups);
	
	return $ALL;	
}/////////////////////////////////////////////////////////////////////////////////

function DB_Groups_AS_STR($GID, $login=false)
{
//returns a string of the groups that the user is a member of..

	$groups=array();
		$i=0;
		$tbl=table("select group_id from groups where group_id in (select user_group_id from user_groups where user_id='$GID')");
		while($row=mysql_fetch_array($tbl)){
			$groups[$i]=$row['group_id'];
			$i++;
		}
		$group_str="";
		foreach($groups as $gid){
			$group_str .= $gid.",";
		}
	 	
	 	if($i>1){
	 		$group_str="'".$group_str;
	 		$group_str=str_replace(",", "','", $group_str);
	 		$group_str = substr($group_str,0,-1);
	 	}
	 	$group_str = substr($group_str,0,-1);
	 	return $group_str;	 	
}/////////////////////////////////////////////////////////////////////////////////
	
 function DB_GROUPS_Privileges($group_str)
 {
//returns an array of the privileges from groups that the user is member of

 	$privs=array();

 	$i=0;
 	if(strpos($group_str,',') !== false) // means many groups sent
 	{
 		$tbl=table("select priv from user_group_privs where group_id in ({$group_str})");
 		
 	}else{ //means only one group
 		$tbl=table("select priv from user_group_privs where group_id='{$group_str}' "); 
 	}
 	while($row=mysql_fetch_array($tbl)){
 		$privs[$i]=$row['priv'];
 		$i++;
 	}
 		return $privs;
 }/////////////////////////////////////////////////////////////////////////////////
 
 function DB_USER_Privileges($GID)
 {
//returns an array of the privileges granted to the user

 	$privs=array();
 	$i=0;
 	$tbl=table("select user_priv from user_privs where user_id = '{$GID}' ");
 	while($row=mysql_fetch_array($tbl)){ 		
 		$privs[$i]=$row['user_priv'];
 		$i++;
 	}
 	return $privs;
 }/////////////////////////////////////////////////////////////////////////////////

 function group_has_permission($gr_id, $per)
 {
//checks whether a group is granted a certain privilege

 	if (true) {
 		$gr_prv = DB_GROUPS_Privileges($gr_id);
 		foreach ($gr_prv as $priv){ //echo $priv . "<br/>";
 			if (in_array($priv, $per)) {
 			return true;
 		}}
 	}
 	return false;
 }/////////////////////////////////////////////////////////////////////////////////
  
 function PrivilegesEditor($priv="GROUPS_MAN")
 {

//Displays the editor which shows the privileges for a given group or user, and enables us to add/remove privs to user/group

 	$P=array("A", $priv);
 	$ALL=array_merge($P, $_SESSION['PRIVS']);
 	//print_r($A);
 	if(user_has_permission($ALL))
 	{
 		$_SESSION['back_uri']=$_SERVER['PHP_SELF'].'?'.$_SERVER['QUERY_STRING'];//echo $_SESSION['back_uri']."<br/>";
 		$_SESSION['access_to_all_privs']=$priv;
 		$ALL_PRIVS=Get_All_Privileges($_SESSION['access_to_all_privs']);

 		?><div class="all_privs"><?
 	 			?><div class="hd">
 	 				<?if($_SESSION['back_page'] == "groups"){ echo All_Group_Privs; }elseif($_SESSION['back_page'] == "users"){echo All_User_Privs;}?>
 	 			</div><?
 	 		 foreach($ALL_PRIVS as $key => $val)
 	 		 {
 	 		 	if($_SESSION['back_page'] == 'groups'){
	 	 		 	if(!group_has_permission($_REQUEST['NID'], array( $val))){
	 	 		 	?><div style="margin-left:10px;display:block;clear:both"><p>(<?=$key?>) :&nbsp;&nbsp;&nbsp;<?=$val?></p> 
	 	 		 	<a href="../common/manage_group_privs.php?lang=<?=$GLOBALS['lang']?>&v=add&PR=<?=$key?>&group=<?=$_REQUEST['NID']?>" class="add_grp_prv"> >> </a></div><?
	 	 		 	} 
 	 		 	}elseif($_SESSION['back_page'] == 'users'){
 	 		 		if(!any_user_has_permission($_REQUEST['NID'], array( $val))){
 	 		 			?><div style="margin-left:10px;display:block;clear:both"><p>(<?=$key?>) :&nbsp;&nbsp;&nbsp;<?=$val?></p>
 	 		 			<a href="../common/manage_group_privs.php?lang=<?=$GLOBALS['lang']?>&v=add&PR=<?=$key?>&user=<?=$_REQUEST['NID']?>" class="add_grp_prv"> >> </a></div><?
 	 		 		}
 	 		 	}
 	 		 }
 	 		 ?></div><?
 	 		 
 	 		 ?><div class="this_group_privs"><?
 	 		 	?><div class="hd"><?if($_SESSION['back_page'] == "groups"){ echo This_Group_Privs; }elseif($_SESSION['back_page'] == "users"){echo This_User_Privs;}?></div><?
 	 		 	if($_SESSION['back_page'] == "groups"){
 	 		 		this_group_privs($_REQUEST['NID'], false);
 	 		 	}elseif($_SESSION['back_page'] == "users"){
 	 		 		this_user_privs($_REQUEST['NID'], false);
 	 		 	}
 	 			 
 	 		 ?></div><?
 	 		 
 	 	}
 }/////////////////////////////////////////////////////////////////////////////////
 
 function Get_All_Privileges($priv="GROUPS_MAN")
 {
// we store the privileges we grant to users/groups in this array

 	$PRV_ARR=array(
 			"P01"=>"GROUPS_MAN",
 			"P02"=>"USERS_MAN",
 			"P03"=>"DISTRIBUTORS_MAN",
 			"P04"=>"CUSTOMERS_MAN",
 			"P05"=>"EMPLOYEES_MAN",
 				
 			"P06"=>"CATEGORY_MAN",
 			"P07"=>"PRODUCTS_MAN",
 			"P08"=>"DOWNLOADS_MAN",
 			"P09"=>"NEWS_MAN",
 				
 			"P10"=>"SERVICES_MAN",
 			"P11"=>"JOBS_MAN",
 			"P12"=>"FAQ_MAN",
 				
 			"P13"=>"CONTACTUS_MAN",
 			"P14"=>"ADS_MAN",
 				
 			"P15"=>"ALBUMS_MAN",
 			"P16"=>"PICTURES_MAN",
 			"P17"=>"VIDEOS_MAN",
 				
 			"P18"=>"BCONTS_MAN",
 			"P19"=>"MCONTS_MAN",
 				
 			"P21"=>"CONFIGS_MAN",
 			"P22"=>"BLOCKED_IPS_MAN",
 			"P23"=>"MAILLIST_MAN",
 			"P24"=>"SHARED_POOL_MAN"
 	);
 	$arr_allowed=array("A", "GROUPS_MAN", "USERS_MAN");
 	if(in_array($priv, $arr_allowed))
 	{
 		if(user_has_permission($arr_allowed))
 		{
 			return $PRV_ARR;
 		}
 	}
 }/////////////////////////////////////////////////////////////////////////////////
 function this_group_privs($group="", $login=true)
 { 
// displays the privs of the groups we are editing 

 	if($login){
 // it's used in login process also

 		$this_group_id = get_data_in("select user_group_id from user_groups where user_group_id='{$group}' and user_id='{$_SESSION['GID']}' ", "user_group_id"); //echo $this_group_id;
 		$group_str = DB_Groups_AS_STR($_SESSION['GID']);
 
 	}else{
 		$group_str = $group;
 	}
 	 
 	$privs_from_groups = DB_GROUPS_Privileges( $group_str);
 
 	foreach($privs_from_groups as $val)
 	{
 		$key = get_priv_key($val);
 		?><div style="margin-left:10px;display:block;clear:both"><p>(<?=get_priv_key($val)?>) :&nbsp;&nbsp;&nbsp;<?=$val?></p><?
  			?><a href="../common/manage_group_privs.php?lang=<?=$GLOBALS['lang']?>&v=del&PR=<?=$key?>&group=<?=$_REQUEST['NID']?>" >X</a></div><?
  	} 		
}/////////////////////////////////////////////////////////////////////////////////

function get_priv_key($pr)
{
// returns the KEY of a given privilege from the main array of privs

	$ALL_PRIVS=Get_All_Privileges($_SESSION['access_to_all_privs']);
	foreach ($ALL_PRIVS as $k => $v){
		if($pr == $v)
			return $k;
	}
}
/////////////////////////////////////////////////////////////////////////////////
//////////////////////////                            ///////////////////////////
/////////////////////////    manage user privileges   ///////////////////////////
/////////////////////////                            ////////////////////////////
/////////////////////////////////////////////////////////////////////////////////

function this_user_privs($user="", $login=true)
{	
//displays privs granted to a given user

	$privs_from_user = DB_USERS_Privileges ($user); 
	if(count($privs_from_user != 0))
	{
		foreach($privs_from_user as $val)
		{
			if($_SESSION['back_page']=='groups') $page="group";
			if($_SESSION['back_page']=='users') $page="user";
			$key = get_priv_key($val);
			?><div style="margin-left:10px;display:block;clear:both"><p>(<?=get_priv_key($val)?>) :&nbsp;&nbsp;&nbsp;<?=$val?></p><?
			?><a href="../common/manage_group_privs.php?lang=<?=$GLOBALS['lang']?>&v=del&PR=<?=$key?>&<?=$page?>=<?=$_REQUEST['NID']?>" >X</a></div><?
		}
	}else{echo No_Rows_Selected;}
}/////////////////////////////////////////////////////////////////////////////////

function any_user_has_permission($us_id, $per)
{
//checks whether a user is granted a given priv (non group priv)

	if (true) {
		$us_prv = DB_USERS_Privileges($us_id);
		
		foreach ($us_prv as $priv){ //echo $priv . "<br/>";
			if (in_array($priv, $per)) {
				return true;
			}
		}
	}
	return false;
}/////////////////////////////////////////////////////////////////////////////////

function DB_USERS_Privileges($user)
{
	return DB_USER_Privileges($user);
}/////////////////////////////////////////////////////////////////////////////////

function MembersEditor($priv)
{	
//when we explore groups, we can add/remove members to 'em. This function enables us do that

	$P=array("A", $priv);
	$ALL=array_merge($P, $_SESSION['PRIVS']);
	if(user_has_permission($ALL))
	{
		$_SESSION['back_uri']=$_SERVER['PHP_SELF'].'?'.$_SERVER['QUERY_STRING'];
		$_SESSION['access_to_all_privs']=$priv;
		$MEMBERS = group_non_members();
		
	?><div class="all_privs"><? // Non-member Users
	 	  ?><div class="hd">
	 	 	<?=All_Users?>
	 	   </div><?
 	 		 foreach($MEMBERS as $key => $val)
 	 		 {
 	 		 	if($_SESSION['back_page'] == 'groups'){
	 	 		   ?><div style="margin-left:40px;display:block;clear:both"><p><?=$val?></p> 
	 	 		   <a href="../common/manage_group_privs.php?lang=<?=$GLOBALS['lang']?>&v=add&members=1&user=<?=$key?>&group=<?=$_REQUEST['group']?>" class="add_grp_prv" > >> </a></div><? 
 	 		 	}
 	 		 }
 	 		 ?></div><?
 	 		 
 	 		 ?><div class="this_group_privs"><? // Group Members
 	 		 	?><div class="hd">
 	 		 		<?=Group_members?>
 	 		 	</div><?
 	 			 
 	 		 	$members = group_members();
 	 		 	foreach($members as $key => $val)
 	 		 	{
 	 		 		?><div style="margin-left:40px;display:block;clear:both"><p><?=$val?></p> 
	 	 		   <a href="../common/manage_group_privs.php?lang=<?=$GLOBALS['lang']?>&v=del&members=1&user=<?=$key?>&group=<?=$_REQUEST['group']?>" class="add_grp_prv" > X </a></div><?
 	 		 	} 
 	 		 ?></div><?	 	 		 
	 }	
}/////////////////////////////////////////////////////////////////////////////////

function group_members()
{
//returns members/users of the groups

	$sql = "select user_id, user_name from user where user_id in (select user_id from user_groups where user_group_id = '{$_REQUEST['group']}' ) order by user_name ";
	$tbl = table($sql);
	$arr = array();
	while($Row = mysql_fetch_array($tbl)){
		$arr[$Row['user_id']] = $Row['user_name'];
	}
	//print_r($arr);
	return $arr;
}/////////////////////////////////////////////////////////////////////////////////

function group_non_members()
{
// returns users that are not members of the group we are editing

	$sql = "select user_id, user_name from user where user_id not in (select user_id from user_groups where user_group_id = '{$_REQUEST['group']}' ) order by user_name";
	$tbl = table($sql);
	$arr = array();
	while($Row = mysql_fetch_array($tbl)){
		$arr[$Row['user_id']] = $Row['user_name'];
	}
	//print_r($arr);
	return $arr;
}/////////////////////////////////////////////////////////////////////////////////