<?php
//إعدادات عامة General Settings

// TODO String path must end with trailing slash. يجب أن ينتهي المسار بسلاش
$wwwURL="";

// TODO Default Language. اللغة الإفتراضية
isset($_GET["lang"]) ? $GLOBALS["lang"]=$_GET["lang"] : $GLOBALS["lang"]="ar"; 

//معلومات الاتصال Connection Settings
class mysql_connection_A {
	private $myhost="localhost"; //Server Name
	private $myuser="root"; //Database User Name
	private $mypass="12345"; //Password
	private $mydb="generalization"; //Database Name
	
	//__________________________________________________________________DO NOT CHANGE
	function do_connect(){$mycon=mysql_connect($this->myhost,$this->myuser,$this->mypass);mysql_select_db($this->mydb,$mycon);mysql_query("SET NAMES 'utf8'",$mycon);mysql_query('SET CHARACTER SET utf8',$mycon);return $mycon;}
}
?>