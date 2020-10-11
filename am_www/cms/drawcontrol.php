<?php //Updated 13-11-2009
$GLOBALS['calloaded']=false;
function draw_control($DataRow, $actrl, $lblstyle="")
{
	if ($actrl['permission']!==null && !user_has_permission($actrl['permission'])) {return;}
	switch ($actrl['control']) {
		case 'hidden':
		echo '<div class="jfld'.$lblstyle.'"><input type="hidden" name="txt_'.$actrl['name'].'" id="txt_'.$actrl['name'].'" value="'.$actrl['value'].'" /></div>';
		break;
		case 'text':
		echo '<div class="jfld'.$lblstyle.'"><div class="field_label'.$lblstyle.'">'.constant($actrl['caption']).' '.addreq($actrl['required']).'</div><div class="txtfld'.$lblstyle.'"><input type="text" name="txt_'.$actrl['name'].'" id="txt_'.$actrl['name'].'" class="validate_'.$actrl['validate'].'" value="'.$actrl['value'].'" /></div><div class="field_note'.$lblstyle.'">'.$actrl['note'].'</div></div>';
		break;
		case 'password':
		echo '<div class="jfld'.$lblstyle.'"><div class="field_label'.$lblstyle.'">'.constant($actrl['caption']).' '.addreq($actrl['required']).'</div><div class="txtfld'.$lblstyle.'"><input  type="password" name="txt_'.$actrl['name'].'" id="txt_'.$actrl['name'].'" value="'.$actrl['value'].'" /></div><div class="field_note'.$lblstyle.'">'.$actrl['note'].'</div></div>';
		if (user_has_permission(array("A"))){$myrepass='value="'.$actrl['value'].'"';}
		echo '<div class="jfld'.$lblstyle.'"><div class="field_label'.$lblstyle.'">'.constant('Re-Type').' '.constant($actrl['caption']).' '.addreq($actrl['required']).'</div><div class="txtfld'.$lblstyle.'" ><input type="password" name="txt_'.$actrl['name'].'_r" id="txt_'.$actrl['name'].'_r" '.$myrepass.' /></div></div>';
		break;
		case 'textarea':
		echo '<div class="jfld'.$lblstyle.'"><div class="field_label'.$lblstyle.'">'.constant($actrl['caption']).' '.addreq($actrl['required']).'</div><div class="txtfld'.$lblstyle.'"><textarea   name="txt_'.$actrl['name'].'" id="txt_'.$actrl['name'].'" cols="40" rows="10">'.$actrl['value'].'</textarea></div><div class="field_note'.$lblstyle.'">'.$actrl['note'].'</div></div>';
		break;
		case 'editor':
		include("../fckeditor/fckeditor.php");
		echo '<div class="jfld'.$lblstyle.'"><div class="field_label'.$lblstyle.'">'.constant($actrl['caption']).' '.addreq($actrl['required']).'</div>';
		$sBasePath = '../fckeditor/' ;
		$oFCKeditor = new FCKeditor('txt_'.$actrl['name']) ;
		$oFCKeditor->BasePath = $sBasePath ;
		//$oFCKeditor->Config['SkinPath'] = $sBasePath . 'editor/skins/' . htmlspecialchars('office2007') . '/' ;
		$oFCKeditor->Height='400';
		$oFCKeditor->Width='520';
		$oFCKeditor->Value = stripslashes($actrl['value']) ;
		$oFCKeditor->Create();
		echo '</div>';
		break;
		case 'fkey':
		$mytbl=table("select {$actrl['fID']}, {$actrl['fTitle']} from {$actrl['ftbl']} {$actrl['fFltr']} order by {$actrl['fTitle']}");
		echo '<div class="jfld'.$lblstyle.'"><div class="field_label'.$lblstyle.'">'.constant($actrl['caption']).' '.addreq($actrl['required']).'</div><div class="txtfld'.$lblstyle.'"><select  name="txt_'.$actrl['name'].'" id="txt_'.$actrl['name'].'">';
		foreach ($actrl['options'] as $kopt => $vopt) {
			$actrl['value']==$kopt ? $selme="selected" : $selme="";
			echo '<option value="'.$kopt.'" '.$selme.' >'.constant($vopt).'</option>';
		}
		while ($fkrow=mysql_fetch_array($mytbl))
		{
			$actrl['value']==$fkrow[0] ? $selme="selected" : $selme="";
			echo '<option value="'.$fkrow[0].'" '.$selme.' >'.$fkrow[1].'</option>';
		}
		echo '</select></div><div class="field_note'.$lblstyle.'">'.$actrl['note'].'</div></div>';
		break;
		case 'autocomplete':
		if ($actrl['fURL']=="") $actrl['fURL']="../cms/fetch_auto_complete_res.php";
		$myurl="{$actrl['fURL']}?fID={$actrl['fID']}&fTitle={$actrl['fTitle']}&ftbl={$actrl['ftbl']}&fFltr={$actrl['fFltr']}";
		$txtval=get_data_in("select {$actrl['fTitle']} from {$actrl['ftbl']} where {$actrl['fID']} like '{$actrl['value']}'","{$actrl['fTitle']}");
		echo '<div><div class="field_label'.$lblstyle.'">'.constant($actrl['caption']).' '.addreq($actrl['required']).'</div><div class="txtfld'.$lblstyle.'"><input type="text"  name="auto_'.$actrl['name'].'" id="auto_'.$actrl['name'].'" value="'.$txtval.'" /><input type="hidden" name="txt_'.$actrl['name'].'" id="txt_'.$actrl['name'].'" value="'.$actrl['value'].'" />';
		?>
		<script type="text/javascript">
		<?php if ($actrl['unbind']!=true) { ?>
		$(document).ready(function () {
		$("<?='#auto_'.$actrl['name']?>").autocomplete("<?=$myurl?>",{mustMatch:true});
		$("<?='#auto_'.$actrl['name']?>").result(function (b, data, itfrm) { $("<?="#txt_".$actrl['name']?>").val(data[1]);	});
		});
		<?php } ?>
		</script>
		<?php 
		echo '</select></div><div class="field_note'.$lblstyle.'">'.$actrl['note'].'</div></div>';
		break;
		case 'FCBKcomplete':
		echo '<div class="jfld'.$lblstyle.'"><div class="field_label'.$lblstyle.'">'.constant($actrl['caption']).' '.addreq($actrl['required']).'</div><div class="txtfld'.$lblstyle.'"><select name="txt_'.$actrl['name'].'" id="txt_'.$actrl['name'].'" multiple="multiple">';
		
		if ($actrl['value']!=""){
		$arr_vals=explode("|",$actrl['value']);
		
		foreach ($arr_vals as $arr_item) {
			$myitem_txt=get_data_in("select {$actrl['fTitle']} from {$actrl['ftbl']} where {$actrl['fID']} like '{$arr_item}'","{$actrl['fTitle']}");
			echo '<option value="'.$arr_item.'" class="selected" >'.$myitem_txt.'</option>';
		}
		}
		echo '</select></div><div class="field_note'.$lblstyle.'">'.$actrl['note'].'</div></div>';
		if (!$GLOBALS['FCBKCOMPLoaded']){
		?>
		<script type="text/javascript" src="../cms/jquery.fcbkcomplete.min.js"></script>
		<?php
		$GLOBALS['FCBKCOMPLoaded']=true;
		}
		?>
		<script type="text/javascript">
		$(document).ready(function () {
		$("#txt_<?=$actrl['name']?>").fcbkcomplete({
            json_url: "<?="../cms/fetch_fcbkcomplete_res.php?fID={$actrl['fID']}&fTitle={$actrl['fTitle']}&ftbl={$actrl['ftbl']}&fFltr={$actrl['fFltr']}&"?>",
            cache: false,
            filter_case: true,
            filter_hide: true,
			firstselected: true,
            //onremove: "testme",
			//onselect: "testme",
            filter_selected: true,
            newel: <?=$actrl['newel']?>      
          });
        });
        </script>
		<?php
		break;
		case 'list':
		echo '<div class="jfld'.$lblstyle.'"><div class="field_label'.$lblstyle.'">'.constant($actrl['caption']).' '.addreq($actrl['required']).'</div><div class="txtfld'.$lblstyle.'"><select  name="txt_'.$actrl['name'].'" id="txt_'.$actrl['name'].'">';
		foreach ($actrl['options'] as $kopt => $vopt) {
			$actrl['value']==$kopt ? $selme="selected" : $selme="";
			echo '<option value="'.$kopt.'" '.$selme.' >'.constant($vopt).'</option>';
		}
		echo '</select></div><div class="field_note'.$lblstyle.'">'.$actrl['note'].'</div></div>';
		break;
		case 'countries':
		echo '<div class="jfld'.$lblstyle.'"><div class="field_label'.$lblstyle.'">'.constant($actrl['caption']).' '.addreq($actrl['required']).'</div><div class="txtfld'.$lblstyle.'"><select  name="txt_'.$actrl['name'].'" id="txt_'.$actrl['name'].'">';
		$tbl=table("select * from countries");
		while ($dd=mysql_fetch_row($tbl)) {
			$actrl['value']==$dd[1] ? $selme="selected" : $selme="";
			echo '<option value="'.$dd[1].'" '.$selme.' >'.$dd[1].'</option>';
		}
		echo '</select></div><div class="field_note'.$lblstyle.'">'.$actrl['note'].'</div></div>';
		break;
		case 'tree':
		echo '<div class="jfld'.$lblstyle.'"><div class="field_label'.$lblstyle.'">'.constant($actrl['caption']).' '.addreq($actrl['required']).'</div><div class="txtfld'.$lblstyle.'"><input  type="text" name="txt_'.$actrl['name'].'" id="txt_'.$actrl['name'].'" value="'.$actrl['value'].'" /></div></div>';
		break;
		case 'checkbox':
		(bool) $actrl['value'] == true? $hdnchecked = 'checked value="1"' : $hdnchecked = 'value="0"';
		echo '<div class="jfld'.$lblstyle.'"><div class="field_label'.$lblstyle.'">&nbsp;</div><div class="txtfld'.$lblstyle.'"><input  type="checkbox" name="txt_'.$actrl['name'].'" id="txt_'.$actrl['name'].'" '.$hdnchecked.' onclick="if(this.checked){this.value=\'1\';}else{this.value=\'0\';}" /> '.constant($actrl['caption']).'</div></div>';
		break;
		case 'date':
		if (!$GLOBALS['calloaded']){
		echo '<!-- import the calendar script -->
		<script src="../jscal2-1.9/src/js/jscal2.js"></script>
    	<script src="../jscal2-1.9/src/js/lang/en.js"></script>
    	<link rel="stylesheet" type="text/css" href="../jscal2-1.9/src/css/jscal2.css" />
    	<link rel="stylesheet" type="text/css" href="../jscal2-1.9/src/css/border-radius.css" />
    	<link rel="stylesheet" type="text/css" href="../jscal2-1.9/src/css/steel/steel.css" />';
		}
		$GLOBALS['calloaded']=true;
		echo '<div class="field_box'.$lblstyle.'"><div class="ttxtg field_label'.$lblstyle.'">'.constant($actrl['caption']).' '.addreq($actrl['required']).'</div><div class="txtfld'.$lblstyle.'"  ><input type="text"  name="txt_'.$actrl['name'].'" id="txt_'.$actrl['name'].'" value="'.$actrl['value'].'" /> <img id="img_'.$actrl['name'].'"  name="img_'.$actrl['name'].'" style="cursor:pointer;" src="../images/cal.gif" alt="'.Click_Here_to_Pick_up_the_date.'" class="datecalimg'.$lblstyle.'"   />
		<script type="text/javascript">
		var cal_'.$actrl['name'].' = Calendar.setup({
        	onSelect   : function() { this.hide() },
        	showTime   : '.$actrl['withtime'].'
      	});
      	cal_'.$actrl['name'].'.manageFields("img_'.$actrl['name'].'", "txt_'.$actrl['name'].'", "'.$actrl['format'].'");
		</script></div></div>';
		break;
		case 'datepicker':
		if (!$GLOBALS['calloaded']){
		echo '
		<script type="text/javascript" src="../cms/jquery-ui-1.7.2.custom.min.js"></script>
		<script type="text/javascript" src="../cms/timepicker.js"></script>';
		}
		$GLOBALS['calloaded']=true;
		echo '<div class="field_box'.$lblstyle.'"><div class="ttxtg field_label'.$lblstyle.'">'.constant($actrl['caption']).' '.addreq($actrl['required']).'</div><div class="txtfld'.$lblstyle.'" ><input type="text"  name="txt_'.$actrl['name'].'" id="txt_'.$actrl['name'].'" value="'.$actrl['value'].'" />
		<script type="text/javascript">
			$(function() {
    		$("#txt_'.$actrl['name'].'").datepicker({
    			duration: "",  
		        showTime: '.$actrl['showtime'].',  
		        constrainInput: false,  
		        stepMinutes: 1,  
		        stepHours: 1,  
		        altTimeField: "",  
		        dateFormat: "'.$actrl['format'].'",
	            time24h: true ,
	            firstDay: 6,
	            timeFormat: " hh:ii:ss" 
     		});});	
		</script></div></div>';
		break;
		case 'file':
			if ($actrl['view']=='image' && $DataRow->NID['IsNew']==false){
				if ($actrl['resize']==true)
				{
					echo '<div style="clear:both;overflow:hidden;"><div class="field_label'.$lblstyle.'">&nbsp;</div><img style="margin:5px" src="'.$DataRow->get_file_path($actrl,"thumb").'" /></div>';
				}else {
					echo '<div style="clear:both;overflow:hidden;"><div class="field_label'.$lblstyle.'">&nbsp;</div><img style="margin:5px" src="'.$DataRow->get_file_path($actrl).'" /></div>';
				}
			}
		echo '<div  class="jfld'.$lblstyle.'" style="clear:both;overflow:hidden;">	<input type="hidden" name="MAX_FILE_SIZE" value="100000000" />
				<div class="field_label'.$lblstyle.'">'.constant($actrl['caption']).' '.addreq($actrl['required']).'</div><div class="txtfld'.$lblstyle.'"><input  name="txt_'.$actrl['name'].'" id="txt_'.$actrl['name'].'" type="file" /></div><div class="field_note'.$lblstyle.'">'.$actrl['note'].'</div></div>';
		break;
		case 'tree':
		echo '<div class="jfld'.$lblstyle.'"><div class="field_label'.$lblstyle.'">'.constant($actrl['caption']).' '.addreq($actrl['required']).'</div><div><input type="text" name="txt_'.$actrl['name'].'" id="txt_'.$actrl['name'].'" value="'.$actrl['value'].'" /></div>';
		break;
		case 'map';
		echo '<div class="jfld'.$lblstyle.'"><div class="field_label'.$lblstyle.'">'.constant($actrl['caption']).' '.addreq($actrl['required']).'</div><div class="txtfld'.$lblstyle.'"><input type="text" readonly="readonly" name="txt_'.$actrl['name'].'" id="txt_'.$actrl['name'].'"  value="'.$actrl['value'].'" /> <img id="img_'.$actrl['name'].'"  name="img_'.$actrl['name'].'" style="cursor:pointer;" src="../images/map.png" alt="'.Click_Here_to_View_Map.'" class="mapimg" onclick="ShowMap()"  /><span class="Cancel_Map_Point" onclick="document.getElementById(\'txt_'.$actrl['name'].'\').value=\'\';ShowMap()"> '.Cancel_Map_Point.'</span></div><div class="field_note'.$lblstyle.'">'.$actrl['note'].'</div>';
		echo '<script type="text/javascript">
		function ShowMap()
		{
		if (document.getElementById("map_'.$actrl['name'].'").innerHTML==""){
		document.getElementById("map_'.$actrl['name'].'").style.display="";
		var maploc=document.getElementById("txt_'.$actrl['name'].'").value;
		var arrstr=maploc.split("-");
		if (arrstr[0]=="") {arrstr[0]='.$actrl['def_lat'].';arrstr[1]='.$actrl['def_lng'].';}   
		var msgstr="'.$actrl['msgstr'].'";
		load_map("map_'.$actrl['name'].'", "txt_'.$actrl['name'].'", arrstr[0],arrstr[1],'.$actrl['zoom'].',msgstr);
		}else{GUnload();document.getElementById("map_'.$actrl['name'].'").innerHTML="";document.getElementById("map_'.$actrl['name'].'").style.display="none";}
		}
		</script>';
		include_once '../cms/mapctrl.php';
		echo '<div class="field_label'.$lblstyle.'" id="map_'.$actrl['name'].'" style="display:none;width: '.$actrl['width'].'px; height: '.$actrl['height'].'px"></div>';
		break;
	}
}

function addreq($myreq)
{
	if ($myreq == true) {
	return  " *";
	}
}
?>