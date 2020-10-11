<?php
function DisplayDelMsg($DataRow,$NID="")
{
if ($NID=="") {return;}
	foreach ($_GET as $pmk => $pmv) {
		if ($pmk!="v" && $pmk!="lang" && $pmk!="NID"){
		$strpms[]=$pmk."=".$pmv;	
		}
	}
	$strpms=@join("&",$strpms);
if (user_has_permission(array("A"))) {//Must be :A
	if ($_GET['v']=='d'){
		if ($_POST['doit']==yes) {
			$res=$DataRow->RemoveRow();
			$DataRow->onUserRemovedRow($res);		
		}else {
			echo '<form class="del_form" action="'.$_SERVER['PHP_SELF'].'?lang='.$GLOBALS['lang'].'&NID='.$NID.'&v=d&'.$strpms.'" method="POST">';
			
			$mystr="<span style='color:red'>".$DataRow->NTitle['value']."</span>";
			printf(delete_question, $mystr);
			echo '<p class="del_form_btns"><input type="submit" value="'.yes.'" style="cursor:pointer;" name="doit" id="doit" />&nbsp;&nbsp;&nbsp<input type="button" style="cursor:pointer" value="'.no.'" name="doit" id="doit" onclick="window.location=\''.$_SERVER['PHP_SELF'].'?lang='.$GLOBALS['lang'].'&NID='.$DataRow->NID['value'].'&v=t&'.$strpms.'\';" /></p>';
			echo '</form>';
		}
	}
}
}
?>