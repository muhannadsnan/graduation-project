<?php
session_start();
require_once("../db/mysqlcon.php");
$usr=$_REQUEST['txtusr'];
$pass=$_REQUEST['txtpass'];

/*SET COOKIES*/
$chkrm=$_POST['chkrm'];
if (intval($chkrm)==1) {
	setcookie("meusr", $_POST['txtusr']);
	setcookie("mepass", $_POST['txtpass']);
}

/*START SESSION*/
$NID=get_data_in("SELECT UID FROM users WHERE uname LIKE '$usr' AND upass LIKE '$pass'","UID");
if (!$NID) {
	session_destroy();
	@header("location:../common/signin.php?lang=".$GLOBALS['lang']."&usr=$usr&soro=x");
}else {
	session_register("NID");
	$_SESSION["NID"]=$NID;
	$_SESSION['UID']=$NID;
	$grow=row("select uname, utype from users where UID like '$NID'");
	$_SESSION["UNM"]=$grow['uname'];
	$_SESSION["UTP"]=$grow['utype'];
	@header("location:../common/signin.php?lang=".$GLOBALS['lang']."&v=c");
}
?>