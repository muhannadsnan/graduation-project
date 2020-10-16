<?php
//General Settings
 
// String path must end with trailing slash. 
$wwwURL="";
 
// Default Language 'ar'
isset($_GET["lang"])?$GLOBALS["lang"]=$_GET["lang"]:$GLOBALS["lang"]="ar";
 
//Connection Settings
class mysql_connection_A {
	private $myhost="localhost";//Server Name
	private $myuser="root";//Database User Name
	private $mypass="12345";//Password
	private $mydb="db1";//Database Name
	
//configure the mysql connection with those previously mentioned attributes
	function do_connect(){$mycon=mysql_connect($this->myhost,$this->myuser,$this->mypass);mysql_select_db($this->mydb,$mycon);mysql_query("SET NAMES 'utf8'",$mycon);mysql_query('SET CHARACTER SET utf8',$mycon);return$mycon;}
}
?>