<?php
include_once '../obj/book.class.php';
$t=table("select * from books");

$mybook=new Book();
while ($tr=mysql_fetch_array($t))
{
	$mybook->FillIn($tr);
	$mybook->Index_Record();
}

echo "done";
?>