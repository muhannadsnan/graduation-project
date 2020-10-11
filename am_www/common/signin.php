<?php
ob_start();
include_once '../common/pframe.php';

$myframe=new pframe();
$myframe->header(admin_controls);

if (!session_is_registered("UID") || !user_has_permission(array("A","B"))) 
{	//echo "user:{$_SESSION['UNM']}<br/>NOT Logged in";
	?>
	<!-------------- START Menu Spot Area ---------------->
	<table align="center" style="border:1px #E5E5E5 solid;padding:10px;padding-top:20px;padding-left:20px;margin:20px auto;">
	<tr>
		<td>
			<form id="loginfrm" action="../cms/secreg.php?lang=<?=$GLOBALS['lang']?>" method="POST" style="margin:0px; padding:0px;" target="_parent">
			<div class="field_label_n"><?=user_name?></div>
			<div class="txtfld_n"><input id="txtusr" name="txtusr" type="text" style="font-size:8pt; width:140px;" /></div>
			<div class="field_label_n"><?=password?></div>
			<div class="txtfld_n"><input id="txtpass" name="txtpass" type="password" style="font-size:8pt; width:140px;" /></div>
			<div style="text-align:center; margin:10px; margin-left:10px;margin-bottom:0px;clear:both;"><input id="doit" style="cursor:pointer;" type="submit" value="<?=sign_in?>" /></div>
			</form>
		</td>
	</tr>
	</table>
	<!-------------- END Menu Spot Area ---------------->
	<?php
}
else{//_______________________ Logged in _________________________
	//$myframe->Display_Side_Menu();
	//echo "user:{$_SESSION['UNM']}<br/>";
	
	if(user_has_permission(array("A")))
	{
		/*switch ($_REQUEST['v'])
		{
			case 'c':
				$myframe->Display_Control_Panel();
			break;
		}*/
		$myframe->Display_Control_Panel();
	}
	else
	{ //echo "user:{$_SESSION['UNM']}<br/>";
		header("location: ../pages/data.php?lang={$GLOBALS['lang']}&v=d");
	}
}

@$myframe->footer();
?>