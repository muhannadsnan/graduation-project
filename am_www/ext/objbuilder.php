<?php
include_once '../db/mysqlcon.php';

$mytable=$_GET['tbl'];

echo '
include_once \'../cms/dataset.php\';<br />
include_once \'../lang/pages_\'.$GLOBALS[\'lang\'].\'.inc\';<br />
<br /><br />
class '.ucfirst($mytable).' extends JSDataSet<br />  
{<br />';

$tbl_desc=table("describe $mytable");

while ($fld=mysql_fetch_array($tbl_desc))
{
	//REQUIRED
	if ($fld['Null']=="NO") {$required=", 'required'=>true";} else {$required="";}
	
	
	//VARCHAR ID
	if ($fld['Type']=="varchar(32)" && $fld['Key']=="PRI")
	{
		echo "&nbsp;&nbsp;&nbsp; public \${$fld['Field']}=array('name'=>'{$fld['Field']}', 'type'=>'ID', 'caption'=>'{$fld['Field']}', 'control'=>'none' {$required});<br /><br />";
	}
	
	//VARCHAR ID
	elseif ($fld['Type']=="varchar(32)")
	{
		echo "&nbsp;&nbsp;&nbsp; public \${$fld['Field']}=array('name'=>'{$fld['Field']}', 'type'=>'ID', 'caption'=>'{$fld['Field']}', 'control'=>'fkey', 'ftbl'=>'', 'fTitle'=>'', 'fID'=>'' {$required});<br /><br />";
	}
	
	
	//VARCHAR PIC
	else if ($fld['Type']=="varchar(10)")
	{
		echo "&nbsp;&nbsp;&nbsp; public \${$fld['Field']}=array('name'=>'{$fld['Field']}', 'type'=>'file', 'filetypes'=>'jpg', 'prefix'=>'".strtoupper(substr($mytable,0,3))."', 'caption'=>'${$fld['Field']}', 'control'=>'file', 'view'=>'image', 'resize'=>true, 'sizes'=>array('thumb'=>array('p'=>'B', 'w'=>0, 'h'=>126)));<br /><br />";
	}
	
	//VARCHAR TEXT
	else if (substr($fld['Type'],0,7)=="varchar")
	{
		echo "&nbsp;&nbsp;&nbsp; public \${$fld['Field']}=array('name'=>'{$fld['Field']}', 'type'=>'varchar', 'caption'=>'{$fld['Field']}', 'control'=>'text' {$required});<br /><br />";
	}
	
	//CHAR
	else if (substr($fld['Type'],0,4)=="char")
	{
		echo "&nbsp;&nbsp;&nbsp; public \${$fld['Field']}=array('name'=>'{$fld['Field']}', 'type'=>'varchar', 'caption'=>'{$fld['Field']}', 'control'=>'text' {$required});<br /><br />";
	}
	
	//TEXT TEXTAREA
	else if ($fld['Type']=="text")
	{
		echo "&nbsp;&nbsp;&nbsp; public \${$fld['Field']}=array('name'=>'{$fld['Field']}', 'type'=>'text', 'caption'=>'{$fld['Field']}', 'control'=>'textarea' {$required});<br /><br />";
	}
	
	
	//DATETIME
	else if ($fld['Type']=="datetime")
	{
		echo "&nbsp;&nbsp;&nbsp; public \${$fld['Field']}=array('name'=>'{$fld['Field']}', 'type'=>'varchar', 'caption'=>'{$fld['Field']}', 'control'=>'date', 'value'=>nowandate, 'format'=>'%Y-%m-%d %H:%M:%S', 'withtime'=>'true');<br /><br />";
	}
	
	//DATETIME JUI
	/*else if ($fld['Type']=="datetime")
	{
		echo "&nbsp;&nbsp;&nbsp; public \${$fld['Field']}=array('name'=>'{$fld['Field']}', 'type'=>'datetime', 'caption'=>'{$fld['Field']}', 'control'=>'datepicker', 'value'=>curdatetime, 'format'=>'dd-mm-yy', 'showtime'=>'true');<br /><br />";
	}*/
	
	//FLOAT TEXT
	else if ($fld['Type']=="float")
	{
		echo "&nbsp;&nbsp;&nbsp; public \${$fld['Field']}=array('name'=>'{$fld['Field']}', 'type'=>'float', 'caption'=>'{$fld['Field']}', 'control'=>'text' {$required}, 'value'=>{$fld['Default']});<br /><br />";
	}
	
	//FLOAT TEXT
	else if (substr($fld['Type'],0,3)=="int")
	{
		echo "&nbsp;&nbsp;&nbsp; public \${$fld['Field']}=array('name'=>'{$fld['Field']}', 'type'=>'int', 'caption'=>'{$fld['Field']}', 'control'=>'text' {$required}, 'value'=>{$fld['Default']});<br /><br />";
	}
	
	//BOOL CHECKBOX
	else if ($fld['Type']=="tinyint(1)")
	{
		echo "&nbsp;&nbsp;&nbsp; public \${$fld['Field']}=array('name'=>'{$fld['Field']}', 'type'=>'bool', 'caption'=>'{$fld['Field']}', 'control'=>'checkbox' {$required}, 'value'=>{$fld['Default']});<br /><br />";
	}
	
}

echo 	'&nbsp;&nbsp;&nbsp; public $tblname="'.$mytable.'";<br /><br />
}<br />';
?>