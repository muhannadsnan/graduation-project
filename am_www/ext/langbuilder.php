<?php
include_once '../obj/products.class.php';
$mycls="Products";

if ($_POST['trans'])
{
	foreach ($_POST as $defk=>$defv) {
		if ($defk!="trans"){
		?>
		define("<?=$defk ?>","<?=$defv ?>");<br />
		<?
		}
	}
}

echo "<br /><br /><form method='post'><table>";
?>
<tr>
<td>Edit_<?=$mycls ?></td>
<td><input name="Edit_<?=$mycls ?>" value="<?=$_POST['Edit_'.$mycls] ?>" /></td>
</tr>
<tr>
<td>Add_<?=$mycls ?></td>
<td><input name="Add_<?=$mycls ?>" value="<?=$_POST['Add_'.$mycls] ?>" /></td>
</tr>
<tr>
<td>View_<?=$mycls ?></td>
<td><input name="View_<?=$mycls ?>" value="<?=$_POST['View_'.$mycls] ?>" /></td>
</tr>
<tr>
<td>Del_<?=$mycls ?></td>
<td><input name="Del_<?=$mycls ?>" value="<?=$_POST['Del_'.$mycls] ?>" /></td>
</tr>
<?
foreach (get_class_vars($mycls) as $mc_fld) {
	if (is_array($mc_fld)){
	if ($mc_fld['control']!="none"){
	?>
	<tr>
	<td><?=$mc_fld['caption'] ?></td>
	<td><input type="text" name="<?=$mc_fld['caption'] ?>" value="<?=$_POST[$mc_fld['name']] ?>" /></td>
	</tr>
	<?
	if (is_array($mc_fld['options']))
	{
		foreach ($mc_fld['options'] as $opk=>$opv) {
	?>
	<tr>
	<td><?=$opv ?></td>
	<td><input type="text" name="<?=$opv ?>"  value="<?=$_POST[$opv] ?>" /></td>
	</tr>
	<?
		}
	}
	}
	}
}
?>
<tr>
<td colspan="2"><input type='submit' value="trans" name="trans" /></td>
</tr>
<?
echo "</table></form>";
?>