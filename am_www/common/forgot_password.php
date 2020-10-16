<?php
ob_start ();
session_start();
include_once '../common/pframe.php';
$myframe = new pframe ();
$myframe->header ( Forgot_Password );

$myframe->open_box("", Forgot_Password,"panel");

// Page #1 of the wizard
if(!$_POST) Page1();

// Show Page #2 
if($_POST['next1']) Page2();

// Show Page #3
if($_POST['next2']) Page3();

$myframe->close_box("panel");
//////////////////////////////////////////////////////////////////////

function Page1()
{
// the first page of the wizard 
//the user enters his username & clicks next

	?>
	<form name="forgot_form1" method="post" action="<?=$_SERVER['PHP_SELF']?>?<?=$_SERVER['QUERY_STRING']?>">
	
		<table width="50%" style="width:500px;border:1px #ccc solid; border-radius:10px; margin:20px auto;padding:30px">
		
			<tr style="height:60px;" ><td><label><?=enter_your_username?> *</label></td></tr>
			
			<tr><td><input type="text" name="user"  style="width:300px;margin-right:65px" autocomplete="off"  /></td>	</tr>
	
	
			<tr><td colspan="2" style="text-align: center">
				<input type="submit" name="next1" value="(1) <?=Next?> > " style="height:35px;font-weight:bold;font-size:14pt;margin-top:20px;cursor:pointer">	</td>	</tr>
	
		</table>
	</form>
	<? 
}//////////////////////////////////////////////////////////////////////
 
function Page2()
{
// Page #2 in the wizard, shows the first 4 characters of the user according to the username he entered in the precious page

	include_once '../common/pframe.php';
	$myframe = new pframe ();
	?><form name="forgot_form2" method="post" action="<?=$_SERVER['PHP_SELF']?>?<?=$_SERVER['QUERY_STRING']?>">
	
		<table width="50%" style="width:500px;border:1px #ccc solid; border-radius:10px; margin:20px auto;padding:30px">
		
			<? 
			$email = $myframe->get_Email_regestered_in_DB($_POST['user']) ;
				if($email == "")
				{
					echo '<tr><td>'.email_NOT_registered.'</td></tr>';
					$email = false;
				}
				if($email){
					?>
					<tr style="height:60px;"><td><label><?=we_will_send_reset_lint_to_this_email?> </label><br/></td></tr>
					
					<tr><td> 
					
					<p style="direction:ltr"><? echo  $myframe-> unreadable_email($email);?></p>					
					 
					 <? $_SESSION['forgot_password']['email'] = $email; ?>
					 <? $_SESSION['forgot_password']['user'] = $_POST['user']; ?>	
		
					</td>	</tr>		
			
					<tr><td colspan="2" style="text-align: center">
						<input type="submit" name="next2" value="(2) <?=Next?> > " style="height:35px;font-weight:bold;font-size:14pt;margin-top:20px;cursor:pointer">	</td>	</tr>
			<? } ?>
		</table>
	</form><? 
}//////////////////////////////////////////////////////////////////////

function Page3()
{ 
// generating the OTID link (one-time usable link) and sending it to the email mentioned it previous page

	include_once '../common/pframe.php';
	$myframe = new pframe ();
	 
	$user = $_SESSION['forgot_password']['user']. ".OTID"; 
	$hash_user = md5($user); 
	$link = $myframe->Generat_OTID_Link($hash_user); 
	
	$email = $_SESSION['forgot_password']['email'];
	$subject = "A&M SYSTEMS : RESET PASSWORD";
	$message="Hello, <br/> Here is the reset password link :<br/> {$link}";
	// create email headers
	$headers = "From: A&M SYSTEMS\r\n"	. "Reply-To: msn-23@live.com \r\n"	."X-Mailer: PHP/"	. phpversion();
	
	
	if(mail($email, $subject, $message, $headers))
	{
		?><p style="width:500px;border:1px #ccc solid; border-radius:10px; margin:20px auto;padding:30px">
			<?=we_have_just_sent_the_link_to_email?><br/></br>
			<span style="direction:ltr;text-align:left;"><?=$link?></span>
			<p>لقد عرضنا الرابط تجاوزاً لعدم امكانية الارسال الى الايميل الان</p>
		</p><? 
		//unset($_SESSION['forgot_password']);
	}
	else{echo "Sending Failed !!"."<br/>";
	?><span style="direction:ltr;text-align:left;"><?=$link?></span>
				<p>لقد عرضنا الرابط تجاوزاً لعدم امكانية الارسال الى الايميل الان</p><? 
	}
}//////////////////////////////////////////////////////////////////////