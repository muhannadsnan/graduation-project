<?php
//General Settings
 
// String path must end with trailing slash. 
$wwwURL="";
 
// Default Language 'ar'
isset($_GET["lang"])?$GLOBALS["lang"]=$_GET["lang"]:$GLOBALS["lang"]="ar";
 
//Connection Settings
class mysqli_connection_A {
	private $myhost="localhost";//Server Name
	private $myuser="root";//Database User Name
	private $mypass="12345";//Password
	private $mydb="db1";//Database Name
	
//configure the mysql connection with those previously mentioned attributes
	function do_connect(){
		$mycon=mysqli_connect($this->myhost,$this->myuser,$this->mypass);
		mysqli_select_db($mycon,$this->mydb);
		mysqli_query($mycon,"SET NAMES 'utf8'");
		mysqli_query($mycon,'SET CHARACTER SET utf8');
		return $mycon;
	}
}
?>