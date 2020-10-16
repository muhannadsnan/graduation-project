<?php
function DisplayDelMsg($DataRow,$NID="", $delete_what)
{
if ($NID=="") {return;}
	foreach ($_GET as $pmk => $pmv) { //collect GET parameters
		if ($pmk!="v" && $pmk!="lang" && $pmk!="NID"){
		$strpms[]=$pmk."=".$pmv;	
		}
	}
	$strpms=@join("&",$strpms);
	if ($_GET['v']=='d')
	{
		if ($_POST['doit']==yes) 
		{
			if($delete_what=="download"){ //deleting a 'download' object db row + the file
				cmd("update product set prod_exe = '' where prod_id='{$_REQUEST['NID']}' ");echo "update product set prod_exe = '' where prod_id='{$_REQUEST['NID']}' ";
				$path="../documents/exe/EXE_{$_REQUEST['NID']}.exe";
				unlink($path); //delete the attached file
//redirect
				header("location: ../pages/download.php?lang={$GLOBALS['lang']}&v=t");
			}else{
				$res=$DataRow->RemoveRow();
				$DataRow->onUserRemovedRow($res);
			}		
		}else {
//display delete message
			echo '<form class="del_form" action="'.$_SERVER['PHP_SELF'].'?lang='.$GLOBALS['lang'].'&NID='.$NID.'&v=d&'.$strpms.'&delete_done" method="POST">';
			
			$mystr="<span style='color:red'>".$DataRow->NDate['NTitle']."</span>";
			printf(delete_question, $mystr);
			echo '<p class="del_form_btns"><input type="submit" value="'.yes.'" style="cursor:pointer;" name="doit" id="doit" />&nbsp;&nbsp;&nbsp
			<input type="button" style="cursor:pointer" value="'.no.'" name="doit" id="doit" onclick="window.location=\''.$_SESSION['prev_self'].'?'. $_SESSION['prev_params'].'\';" /></p>';
			echo '</form>';
		}
	}
}
?>