<?php
include_once '../db/mysqlcon.php';

//$mytbl=table("select {$_REQUEST['fID']}, {$_REQUEST['fTitle']} from {$_REQUEST['ftbl']} {$_REQUEST['fFltr']} order by {$_REQUEST['fTitle']} limit 10");

$q = addslashes(strip_tags(strtolower($_REQUEST["q"])));
if (!$q) return;

$myres=table("select {$_REQUEST['fID']}, {$_REQUEST['fTitle']} from {$_REQUEST['ftbl']} where {$_REQUEST['fTitle']} like '%$q%' ". stripslashes($_REQUEST['fFltr']) ."  order by {$_REQUEST['fTitle']} limit 10");
//echo "select {$_REQUEST['fID']}, {$_REQUEST['fTitle']} from {$_REQUEST['ftbl']} where {$_REQUEST['fTitle']} like '%$q%' ". stripslashes($_REQUEST['fFltr']) ."  order by {$_REQUEST['fTitle']} limit 10";
if (!$myres) return;

while ($myr=mysql_fetch_array($myres))
{
	echo $myr[$_REQUEST['fTitle']]."|".$myr[$_REQUEST['fID']] . "\n";
}
?>