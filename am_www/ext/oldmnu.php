<?php 
include_once '../db/mysqlcon.php';
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
<meta content="text/html; charset=utf-8" http-equiv="Content-Type" />
<title><?=$title?></title>
<body style="direction:rtl;text-align:right;">

<?php

class mysql_connection_B {
	private $myhost="localhost"; //Server Name
	private $myuser="root"; //Database User Name
	private $mypass=""; //Password
	private $mydb="cabledb"; //Database Name
	
	//__________________________________________________________________DO NOT CHANGE
	function do_connect(){$mycon=mysql_connect($this->myhost,$this->myuser,$this->mypass);mysql_select_db($this->mydb,$mycon);mysql_query("SET NAMES 'utf8'",$mycon);mysql_query('SET CHARACTER SET utf8',$mycon);return $mycon;}
}

$conB=new mysql_connection_B();
$con=$conB->do_connect();


get_children(0,1);

function get_children($root, $level) {
	$tbl=table("select * from mos_menu where  parent = {$root} and menutype = 'mainmenu' order by `ordering`");

	while ($row = mysql_fetch_array($tbl)) {
		
		$pad=str_pad(" ",$level,"-");
		
		echo $pad." ".$row['name']."<br /><br />";
		
		get_children($row['id'],$level+1);
	}
}

?>

</body>

</html>