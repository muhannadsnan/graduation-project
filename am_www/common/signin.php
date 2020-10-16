<?php
ob_start();
include_once '../common/pframe.php';
$myframe=new pframe();
$myframe->header(admin_controls);
$user= $_SESSION['GID']==''?$_SESSION['failed_username']:$_SESSION['UNM'];

if (! $_SESSION["GID"]) 
{
	//if user had a remember me cookie then do the login process automatically
	if($_COOKIE['remember_user'] && $_REQUEST['soro'] != 'x') { @header("location:../cms/secreg.php");}else{

// if this IP has performed more than 10 illegal actions in the last 3 days, don't let it access out site 
		if($myframe->illegal_attempts() > 10){ @header("location:../tpl/error.php?reason=failed_login"); }
		else{ //echo "illegal attempts  number : ".$myframe->illegal_attempts()."</br>user:".$user;
		?>
		<table align="center" style="border:1px #E5E5E5 solid;padding:10px;padding-top:20px;padding-left:20px;margin:20px auto;">
		<tr>
			<td>
				<form id="loginfrm" action="../cms/secreg.php?lang=<?=$GLOBALS['lang']?>" method="POST" style="margin:0px; padding:0px;" target="_parent">
					<div class="field_label_n"><?=user_name?></div>
					<div class="txtfld_n">
						<input id="txtusr" name="txtusr" type="text" style="font-size:8pt; width:140px;" value="<?=$_COOKIE['remember_user']; ?>"/></div>
					<div class="field_label_n"><?=password?></div>
					<div class="txtfld_n">
						<input id="txtpass" name="txtpass" type="password" style="font-size:8pt; width:140px;" /><br/>
						
					</div>


// the "Remember Me" feature that signs in user automatically if he closed the session without signing out


					<div id="div_rememberme">
						<input id="rememberme" name="rememberme" type="checkbox" value="remember" <?php if(isset($_COOKIE['remember_user'])) {echo 'checked="checked"';}else{echo '';}?>/>
						<span class="span_rememberme"><?=Remember_Me?></span><br/>
						<a href="../common/forgot_password.php" style=""><?=Forgot_Password?>?</a> 
					</div>
					<div style="text-align:center; margin:10px; margin-left:10px;margin-bottom:0px;clear:both;">
						<input id="doit" style="cursor:pointer;" type="submit" value="<?=Sign_In?>" /></div>
				</form>
			</td>
		</tr>
		</table>
		
	<? }
	}
}else{
	//print_r($_SESSION);
	header("location: ../common/");
}

?>