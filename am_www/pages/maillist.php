<?php
include_once '../common/pframe.php';
include_once '../obj/mail_list.class.php';
include_once '../cms/navigator.php';

$myframe=new pframe();
$myframe->header(MailList);
$myML = new Mail_list($_REQUEST['NID']);
$pagePRIV = "MAILLIST_MAN";
if(user_has_permission(array("A", $pagePRIV))){	$adding ="";/*if adding is about another OBJ*/	}else{$adding=""; $pagePRIV="";	}

switch ($_REQUEST['v'])
{		
	case "t": /**************  Show all emails subscribed in table  ***************/
		
//all routine …..

	/***/IS_SECURE($pagePRIV);/***/
		$myframe->open_box("withTree", MailList,"panel", $adding);
		?> <div style="width:400px;display:block;clear:both;margin:auto;"><a class="group_by_button" href="<?=$_SERVER['PHP_SELF']?>?v=send_email&lang=<?=$GLOBALS['lang']?>">  <?=send_to_maillist?>   </a> </div><div style="width:300px;display:block;clear:both;margin:auto"></div><?
		?><table class="global_tbl sortable" style="width:;margin:auto"><? 
		$i=0;
		$wherestr="";
		$sql="select * from {$myML->tblname} {$wherestr} order by NDate desc ";
		if(@mysql_num_rows(mysql_query($sql)) == 0){?><tr><td><?=No_Rows_Selected?></td></tr><?}else{?>
			<tr>
				<th><?=Email?></th>
				<th><?=NDate?></th>
			</tr>
		<?
		if($i%2==0){$tr_class="tr_1";}else{$tr_class="tr_2";}	
		
		$nav=new Navigator($sql, $_GET['cur_page'], 30, "select count(NID) from mail_list {$wherestr}");
		if(@mysql_num_rows(mysql_query($sql)) != 0)
		{
			/////START CARDS EXPLORER
			while ($Row=mysql_fetch_array($nav->result)){
				
				$myML->FillIn($Row);
				if($i%2==0){$tr_class="tr_1";}else{$tr_class="tr_2";}
				
				?><tr class="<?=$tr_class?>" >	
					<td class="" style="max-width:;"><?=$myML->email['value']?></td>
					<td class="NDate"><?=$myML->NDate['value']?></td>
					<? if(user_has_permission(array("A", $pagePRIV))) {?><td class="admin_tools_td"><?=$myframe->DisplayAdminTools("maillist", $myML->NID['value'])?></td><? }?>
				</tr><?
				$i++;
			}
			//////END CARDS EXPLORER
			
		}else{
			?><div style="color:#999;width:90%;border:1px #ccc solid;padding:15px;margin:auto;text-align: center;"><?=No_Rows_Selected?></div><?
			} 
		}

// finally the edit tools will be hidden (no editing in mail list)

		?></table><script>   $('.EDT_admintool').hide();   </script><? 
		$nav->Draw_Navigator_Line("jbtn");
		$myframe->close_box("panel");
	break;
	
	case "d": /**************  delete from mail list  ***************/
		/***/IS_SECURE($pagePRIV);/***/		
		if ($myML->NID['IsNew'])  break;
		
		$myframe->open_box("withTree", Del_x ." ".email,"panel", $pagePRIV, $adding);
		$myML->DisplayDelMsg();
		$myframe->close_box("panel");
	break;
	
	case "unsub":  /**************  Unsubscribe from mail list  ***************/
		
	// NO need to IS_SECURE because the customer does it
		
		if($_REQUEST['doit']){

// when submitted, delete with showing an appropriate message

			if(@cmd("delete from mail_list where email='{$_REQUEST["txtunsub"]}' "))
			{?><div style="margin:10px"> <?=unsubscribed_successfully?></div><? }
			else
			{?><div style="margin:10px"> <?=unsubscribed_failed?></div><? }
		}else{
//HTML form for unsubscribing (enter your email to unsubscribe)
		?>
		<div style="border:2px #ccc solid; width:50%; margin:50px auto;padding:20px 30px">
		<div style="margin:10px"> <?=enter_your_email_to_unsubscribe?></div>
	 	<form method="POST" action="<?=$_SERVER['PHP_SELF']?>?<?=$_SERVER['QUERY_STRING']?>"  >
		<input type="text" name="txtunsub" id="txtunsub" value="" style="color:red" autocomplete="off"/>
		<input type="submit" name="doit" id="unsubscribe" value="unsubscribe" style="font-size:14pt; height:35px; padding:0px;"/>
		 </form></div><? }
	break;
	
	case "send_email": /*********** send email to mail list participants (Admin) **********/
		
	/***/IS_SECURE($pagePRIV);/***/
		$myframe->open_box("withTree", MailList ." > ". Send,"panel", $adding);
		
		$email_to2=""; // filled with all maillist emails in the included page
		if(isset($_POST['email'])) {
			include_once '../pages/send_form_email.php';
		}
		?><div style="padding: 15px 0px; width: ;"><?=send_to_maillist?></div>
		
// HTML FORM ...
			<form name="maillist_form" method="post" action="<?=$_SERVER['PHP_SELF']?>?<?=$_SERVER['QUERY_STRING']?>">
			
			<table width="50%">
				<tr style="display:;"><td valign="top"><label for="full_name"><?=FullName?> *</label></td>
					<td valign="top"><input type="text" name="full_name" maxlength="50"size="30" value="A and M SYSTEMS"></td>	</tr>
		
				<tr><td valign="top"><label for="email"><?=email?> *</label></td>			
					<td valign="top"><input type="text" name="email" maxlength="80"	size="30"  value="msn-23@live.com"></td>	</tr>
		
				<tr><td valign="top"><label for="telephone"><?=TelephoneNum?></label></td>			
					<td valign="top"><input type="text" name="telephone" maxlength="30"	size="30"  value="+963949130380"></td>	</tr>
		
				<tr><td valign="top"><label for="comments"><?=Message?> *</label></td>			
					<td valign="top"><textarea name="comments" maxlength="1000" cols="25"rows="6"></textarea></td>	</tr>
		
				<tr><td colspan="2" style="text-align: center">
					<input type="submit"	value="<?=Send?>" style="height:35px;font-weight:bold;font-size:14pt;margin-top:20px">	</td>	</tr>
			
				</table>
			</form> <br/><?

// after sending, show emails that we sent the message to

				echo "sent to these emails : ".$email_to2."<br/>";
		$myframe->close_box("panel");		
	break;
}
$myframe->footer();
?>