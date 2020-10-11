<?php 
include_once '../db/mysqlcon.php';
?>
[
<?php
$mytbl=table("select {$_REQUEST['fID']}, {$_REQUEST['fTitle']} from {$_REQUEST['ftbl']} {$_REQUEST['fFltr']} order by {$_REQUEST['fTitle']} limit 10");

$q = addslashes(strip_tags(strtolower(decode_unicode_url($_REQUEST["?tag"]))));
if (!$q) return;

$myres=table("select {$_REQUEST['fID']}, {$_REQUEST['fTitle']} from {$_REQUEST['ftbl']} where {$_REQUEST['fTitle']} like '%$q%' {$_REQUEST['fFltr']}  order by {$_REQUEST['fTitle']} limit 10");

if (!$myres) return;

while ($myr=mysql_fetch_array($myres))
{
	$str[] = '{"caption":"'.$myr[$_REQUEST['fTitle']].'","value":"'.$myr[$_REQUEST['fID']].'"}'	;
}
if (is_array($str)) echo implode(",",$str);
?>
]