<?php
ob_start();
session_start();
require_once("../db/mysqlcon.php");
require_once("../common/pframe.php");
$myframe=new pframe();
$usr=$_REQUEST['txtusr'];
$pass=$_REQUEST['txtpass'];

/*IS REMEMBER ME? THEN LOGIN AUTOMATICALLY*/
if($_COOKIE['remember_user'])
{
	$usr=$_COOKIE['remember_user'];		echo "gooood";
	$hashed_pass=get_data_in("select user_password from user where user_name = '".$_COOKIE["remember_user"]."'","user_password");
}
else
{
	/*****************   SET COOKIES   *****************/
	if ($_POST['rememberme']) 
	{
		if(!setcookie("remember_user", $usr, time()+( 3 * 365 * 24 * 60 * 60), '/')){	echo "cookie user NO";}else{echo "cookie user YES";	}
	}print_r($_COOKIE) ;
}

if($_COOKIE['remember_user']) {$pass = $hashed_pass;} else{$pass = md5($pass);}

/*START SESSION*/
$GID=get_data_in("select user_id from user where user_name = '$usr' and user_password = '$pass'","user_id");

if (!$GID) { //username + password don’t match, go back to sign in page
	session_destroy();
	session_start();
	$_SESSION['failed_username']=$usr;
	$myframe->record_row_in_blockedips('failed_login'); // record a failed login attempt for this ip
	@header("location:../common/signin.php?lang=".$GLOBALS['lang']."&usr=$usr&soro=x");
}else {
//username + password match, create session variables
	session_register("GID");
	$_SESSION["GID"]=$GID;
	$_SESSION['uid']=$GID;
	$grow=row("select user_name,user_cat from user where user_id = '$GID'");
	$_SESSION["UNM"]=$grow['user_name'];
	$_SESSION["UCAT"]=$grow['user_cat'];
	/*****************   get user privs and groups privs   ******************/
	include_once '../common/privileges.php';
//fill all privs in one array & put it in a session variable
	$_SESSION["PRIVS"]= DB_All_Privileges($GID,true);
	print_r($_SESSION["PRIVS"]);echo "<br/><br/>";
	@header("location:../common/"); //redirect to home page having your privs in a session var
}
?>