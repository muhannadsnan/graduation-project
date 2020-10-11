<?php
include_once '../cms/drawcontrol.php';
function DisplayEditor($DataRow, $NID="", $lblstyle="", $ShowVerficationCode=0, $btn_txt=Save, $action_filename=""){
	
if ($NID=="") {
	$NID="new";
	$mynewid=&$DataRow->NID;
	$mynewid['value']=uniqid();
	$mynewid['IsNew']=true;
}

if ($action_filename=="") $action_filename=$_SERVER['PHP_SELF'];

$ShowForm=true;
//Update Data
if (isset($_POST['doit'])) {
	foreach (get_class_vars(get_class($DataRow)) as $varn) {
		$strtr=$varn['name'];
		$myar=&$DataRow->$strtr;
		if (is_array($varn) && in_array($varn['type'], array('file'))==false) {
			if (isset($_POST['txt_'.$varn['name']])) {
			$myar['value']=stripslashes($_POST['txt_'.$varn['name']]);
			}
		}
		if ($varn['type']=='bool' && $varn['control']!='none'){
			$myar['value']=(int) $_POST['txt_'.$varn['name']];			
		}
		if ($varn['type']=='file'){
			$myar['value']=$_FILES['txt_'.$varn['name']];
		}
		if ($varn['control']=='password'){
			$myar['revalue']=$_POST['txt_'.$varn['name'].'_r'];
		}
		if ($varn['control']=='autocomplete'){
			$myar['value']=$_POST['txt_'.$varn['name']];
		}
		if ($varn['control']=='FCBKcomplete'){
			//echo "HHH";
			//var_dump($_POST['txt_'.$varn['name']]);
			if (is_array($_POST['txt_'.$varn['name']]))
			$myar['value']=implode("|",$_POST['txt_'.$varn['name']]);
		}
		if ($varn['control']=='xmlarea'){
			if (isset($_POST["txt_fld_".$varn['name']])) {
				//var_dump($_POST["txt_fld_".$varn['name']]);
				
				$myar['value']=$DataRow->xml_fields_composer($_POST["txt_fld_".$varn['name']], $myar['default']);
			
			//var_dump($myar['value']);
			}
		}
	}
	
	if ($ShowVerficationCode>0){
		/////////////////////////VERF
		$key=$_SESSION['image_value'];
		$imag = $_POST['txtpic'];
		$user = md5(strtoupper($imag));
		if ($user==$key) {
 			$verf="true";
		}
		//////////////////////////////
	}
	
	if ($ShowVerficationCode>0 && $verf!="true") {
		echo $GLOBALS['MyErrStr']->Show($GLOBALS['MyErrStr']->NotVerified);
	}else {
		
	if ($DataRow->NID['IsNew']) {
		$res=$DataRow->InsertRow();
		$DataRow->onUserInsertedRow($res, $ShowForm, $NID);
	}else {
		$res=$DataRow->UpdateRow();
		$DataRow->onUserUpdatedRow($res, $ShowForm, $NID);
	}
	
	}
}
	foreach ($_GET as $pmk => $pmv) {
		if ($pmk!="v" && $pmk!="lang" && $pmk!="NID"){
		$strpms[]=$pmk."=".$pmv;	
		}
	}
	$strpms=@join("&",$strpms);
	if ($ShowForm){
	
	$DataRow->onRenderStart();
?>
<form method="POST" action="<?= $action_filename ?>?NID=<? echo $NID; ?>&v=e&lang=<?=$GLOBALS['lang']?>&<?=$strpms?>" enctype="multipart/form-data">
<table class="form_table">
<tr>
	<td>
	<?php
	foreach (get_class_vars(get_class($DataRow)) as $varn) {
		if (is_array($varn) && $varn['control']!='none') {
			$strtr=$varn['name'];
			$strdtr=$DataRow->$strtr;
			if ($varn['control']=='xmlarea'){
				$def_arr=$DataRow->xml_fields_parser($strdtr['default']);
				$val_arr=$DataRow->xml_fields_parser($strdtr['value']);
				if (is_array($def_arr)){
				foreach ($def_arr as $def_ctrl) {
					$val_ctrl=$val_arr[$def_ctrl['name']];
					$def_ctrl['name']="fld_".$strdtr['name']."[".$def_ctrl['name']."]";
					$def_ctrl['value']=$val_ctrl['value'];
					draw_control($DataRow, $def_ctrl, $lblstyle);
				}
				}
			}else {
				draw_control($DataRow ,$DataRow->$strtr, $lblstyle);
			}
		}	
	}
	?>
	</td>
</tr>
	<?
	$showverf=false;
	if ($ShowVerficationCode==1 && $_GET['NID']=="new"){$showverf=true;}
	if ($ShowVerficationCode==2 && $_GET['NID']!="new"){$showverf=true;}
	if ($ShowVerficationCode==3) {$showverf=true;}
	if ($showverf){
	?>
<tr>
	<td>
<!--Verification AREA/////////////////////////////////////////////////////////--> 
										<table border="0" cellpadding="0" cellspacing="0">
										<tr>
											<td><div class="VerfLabel"><strong class="bigcomment" ><?=Verification?></strong>: <span style="font-family:Arial" class="ttxtg"><?=This_helps_us_to_prevent_automatic_registration?></span></div></td>
											<td><div id="vrimg" style="width:127px; height:70px"><img src="../cms/verf_img.php" /></div><!--&nbsp;[<a onclick="redrawimg('vrimg');">Change Image</a>]<br /><br />--></td>
										</tr>
										<tr>
											<td></td>
											<td valign="top"><input name="txtpic" id="txtpic" type="text" /></td>
											<td valign="top">
											<table cellpadding="0" cellspacing="0"><tr><td class="msgtd"><div id="ivmsg" class="MsgDiv" style="display:none"></div></td></tr></table></td>
										</tr>
										</table>
										<input type="hidden" id="verfication_is_ok" name="verfication_is_ok" value="false" />
<!--Verification AREA/////////////////////////////////////////////////////////-->
	</td>
</tr>
	<?
	}
	?>
<tr>
	<td style="text-align:center"><input type="submit" name="doit" id="doit" value="&nbsp;&nbsp;&nbsp;<?=$btn_txt?>&nbsp;&nbsp;&nbsp;" style="cursor:pointer;" /></td>
</tr>
</table>
</form>
<script type="text/javascript">
$(".validate_float").keydown(function (e) {
	var key = e.charCode || e.keyCode || 0;
    // allow backspace, tab, delete, arrows, numbers and keypad numbers ONLY
    return (
    	key ==190 || 
        key == 8 || 
        key == 9 ||
        key == 46 ||
        (key >= 37 && key <= 40) ||
        (key >= 48 && key <= 57) ||
        (key >= 96 && key <= 105));
});
$(".validate_float").blur(function () {
	if (($(this).val()).toString() != (parseFloat($(this).val())).toString()) {
		$(this).val('');
	}
});
</script>
<?php
	
	$DataRow->onRenderComplete();
	
	}
}

function DisplayUploadDef($DataRow, $lblstyle=""){
	
	/***/IS_SECURE();/***/
	$NID="new";
	$mynewid=&$DataRow->NID;
	$mynewid['value']="_default";
	$mynewid['IsNew']=false;
	
$ShowForm=true;
//Update Data
if (isset($_POST['doit'])) {
	foreach (get_class_vars(get_class($DataRow)) as $varn) {
		$strtr=$varn['name'];
		$myar=&$DataRow->$strtr;
		if ($varn['type']=='file' && $varn['view']=='image'){
			$myar['value']=$_FILES['txt_'.$varn['name']];
		}
	}
	
	$res=$DataRow->do_uploads();
	echo $GLOBALS['MyErrStr']->Show($res);
}
	foreach ($_GET as $pmk => $pmv) {
		if ($pmk!="v" && $pmk!="lang" && $pmk!="NID"){
		$strpms[]=$pmk."=".$pmv;	
		}
	}
	$strpms=@join("&",$strpms);
	if ($ShowForm){
?>
<form method="POST" action="<?$_SERVER['PHP_SELF']?>?NID=<? echo $NID; ?>&v=u&lang=<?=$GLOBALS['lang']?>&<?=$strpms?>" enctype="multipart/form-data">
<table class="form_table">
<tr>
	<td>
	<?php
	foreach (get_class_vars(get_class($DataRow)) as $varn) {
		if (is_array($varn) && $varn['control']=='file' && $varn['view']=='image') {
			$strtr=$varn['name'];
			$strdtr=$DataRow->$strtr;
			draw_control($DataRow ,$DataRow->$strtr, $lblstyle);
		}	
	}
	?>
	</td>
</tr>
<tr>
	<td style="text-align:center"><input type="submit" name="doit" id="doit" value="&nbsp;&nbsp;&nbsp;<?=Save?>&nbsp;&nbsp;&nbsp;" style="cursor:pointer;" /></td>
</tr>
</table>
</form>
<?php
	}
}
//إبراهيم خليل
?>