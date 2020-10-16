<?php

// by now every thing seems to be routine, we are going to explain non routine thing by now

include_once '../common/pframe.php';
include_once '../obj/contactus.class.php';

$myframe = new pframe ();
$myframe->header ( ContactUs );
$myContactus = new Contactus ( $_REQUEST ['lang'] );
$pagePRIV = "CONTACTUS_MAN";
if(user_has_permission(array("A", $pagePRIV))){	$adding ="";/*if adding is about another OBJ*/	}else{$adding=""; $pagePRIV="";	}

switch ($_REQUEST ['v'])
{
	case "e": //**************  edit  **************************/ 
	/***/IS_SECURE($pagePRIV);/***/
		
		$myframe->open_box("withTree", ContactUs.">".Edit_x,"panel", $pagePRIV, $adding);
		$myContactus->DisplayEditor("_n");
		$myframe->close_box("panel");
	break;
	
	case "about" : //**************  about us  **************************/
		
		$myframe->open_box ("", About, "panel" ,"");
		
		$tbl = table ( "select * from {$myContactus->tblname} where cu_id='{$GLOBALS['lang']}'" );		
		$row = mysql_fetch_array ( $tbl );
		$myContactus->FillIn ( $row );
		
		$logo_img = "../images/logo_contactus.png";
		?>
		<table class="contactus_tbl">
			<tr>
				<td class="img"><img src="<?=$logo_img?>" /></td>

// the Location.php shows location on map

				<td rowspan="9" style="width:50%;"><?=include_once '../pages/location.php'; ?></td>
			</tr>
			<tr><td><?=$row['cu_name']?></td></tr>
			<tr><td><?=$row['cu_job']?></td></tr>
			<tr><td><?=$row['cu_company']?></td></tr><!-- our companies -->
			<tr><td><?=$row['cu_mobile']?></td></tr>
			<tr><td><?=$row['cu_tel']?></td></tr>
			<tr><td><?=$row['cu_email']?></td></tr>
			<tr><td><?=$row['cu_name']?></td>	</tr>
		</table>
		<?
		$myframe->close_box ( "panel" );
		
	break;
	
	case "contactus" : //**************  Contact Us  **************************/
		
		$myframe->open_box ( "", ContactUs, "panel", "" );

// if submitted then use this page to manipulate data before sending

		if(isset($_POST['email'])) {include_once '../pages/send_form_email.php';}
		else{		
// HTML form to contact us 
		?><div style="padding: 15px 0px; width: ;"><?=You_can_contact_us_directly_by_filling_this_application?></div>

			<form name="contactform" method="post" action="<?=$_SERVER['PHP_SELF']?>?<?=$_SERVER['QUERY_STRING']?>">			
				<table width="50%">
					<tr><td valign="top"><label for="first_name"><?=FullName?> *</label></td>
						<td valign="top"><input type="text" name="full_name" maxlength="50"size="30"></td>
					</tr>
						
					<tr><td valign="top"><label for="email"><?=email?> *</label></td>			
						<td valign="top"><input type="text" name="email" maxlength="80"	size="30"></td>			
					</tr>
			
					<tr><td valign="top"><label for="telephone"><?=TelephoneNum?></label></td>			
						<td valign="top"><input type="text" name="telephone" maxlength="30"	size="30"></td>			
					</tr>
			
					<tr><td valign="top"><label for="comments"><?=Message?> *</label></td>			
						<td valign="top"><textarea name="comments" maxlength="1000" cols="25"rows="6"></textarea></td>			
					</tr>
			
					<tr><td colspan="2" style="text-align: center">
					<input type="submit"	value="<?=Send?>" style="height:35px;font-weight:bold;font-size:14pt;margin-top:20px">	</td>
					</tr>			
				</table>			
			</form>
		<?
		}
		$myframe->close_box ( "panel" );
	break;
}

$myframe->footer ();
?>