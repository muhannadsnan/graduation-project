<?php
//Ver. 2.6 Last editing in DARALNAWADER Project
require_once '../db/mysqlcon.php';
class JSDataSet
{
	public $NID="NONE";
	public $NTitle="NONE";
	public $tblname="";
	public $documents_path="../documents/";
	public $thumbs_path="../documents/thumbs/";
	
	function __construct($DID="new")
	{	
		foreach (get_class_vars(get_class($this)) as $varn) {$varn=$this->$varn['name'];if (is_array($varn) && in_array($varn['type'], array('ID'))) {$this->NID=&$this->$varn['name'];}}
		foreach (get_class_vars(get_class($this)) as $varn) {$varn=$this->$varn['name'];if (is_array($varn) && $varn['istitle']==true) {$this->NTitle=&$this->$varn['name'];}} 
	
		if ($DID=="new") {
			$this->NID['value']=uniqid();
			$this->NID['IsNew']=true;
			$this->onStart($DID); 
			return;
		}
		
		$sql="select * from $this->tblname where ".$this->NID['name']." = '$DID'";
		$my_row=row($sql, "con");
		
		if ($my_row){
			$this->FillIn($my_row,false);
		}else {
			//Row not found
			$this->NID['value']=$DID;
			$this->NID['IsNew']=true; 
		}
		
		$this->onStart($DID);
	}
	
	function FillIn($arr,$chk_is_new=true)
	{
		//Fields
		foreach (get_class_vars(get_class($this)) as $varn) {
			$strtr=$varn['name'];
			$mano=&$this->$strtr;
			if (is_array($varn)) {
				if (in_array($varn['type'], array('varchar', 'char', 'text')))
				{
					$mano['value']=stripslashes($arr[$varn['name']]);
				}elseif (in_array($varn['type'], array('file'))) {
					$mano['ftype']=$arr[$varn['name']];
				}else {
					$mano['value']=$arr[$varn['name']];
				}
			}
		}
		if ($chk_is_new) {
			$chk=get_data_in("select count(".$this->NID['name'].") as chk from {$this->tblname} where {$this->NID['name']} = '{$this->NID['value']}' ","chk");
			if ($chk>0) {$this->NID['IsNew']=false;} else {$this->NID['IsNew']=true;}
		}
		$this->onStart($this->NID['value']);
	}
		
	function UpdateRow()
	{
		$this->onBeforeUpdate();
		
		$MyErrStr=new ErrStr();
		
		if (!$this->chk_required()) {return $MyErrStr->FillAllRequierd;}
		if (!$this->validate_values()) {return $MyErrStr->InvalidMail;}
		if (!$this->validate_password()) {return $MyErrStr->ReTypePassword;}
	 	if (!$this->chk_is_unique(true)) {return $MyErrStr->DataIsExist;}
	 	
	 	
		$upres=$this->do_uploads();
		if ($upres!=$MyErrStr->Uploded) {return $upres;}
		
		foreach (get_class_vars(get_class($this)) as $varn) {
			$varn=$this->$varn['name'];
			if (is_array($varn) && in_array($varn['type'], array('file', 'ID'))==false) {
				
				if (in_array($varn['type'], array('varchar', 'char', 'text')))
				{
					$parms[]=$varn['name']."='".addslashes($varn['value'])."'";
				}else {
					$parms[]=$varn['name']."='".$varn['value']."'";
				}
			}
			if ($varn['type']=='file') {
				$parms[]=$varn['name']."='".$varn['ftype']."'";
			}
			if ($varn['type']=='password') {
				$parms[]=$varn['name']."=md5('".$varn['value']."')";
			}	
		}
		$sql="update {$this->tblname} set ".join(",",$parms)." where {$this->NID['name']} = '{$this->NID['value']}'";
		//echo $sql;
		$res = cmd($sql, "con");
		if ($res==$MyErrStr->DBOK){$this->Index_Record();}
		$this->onUpdate($res);
		return $res;
	}
	
	function InsertRow()
	{
		$this->onBeforeInsert();
		
		if ($this->NID['value']=='' || $this->NID['value']==null){
			$this->NID['value']=uniqid();
		}
		
		$MyErrStr=new ErrStr();
		if (!$this->chk_required()) {return $MyErrStr->FillAllRequierd;}
		if (!$this->validate_values()) {return $MyErrStr->InvalidMail;}
		if (!$this->validate_password()) {return $MyErrStr->ReTypePassword;}
		if (!$this->chk_is_unique()) {return $MyErrStr->DataIsExist;}
	
		$upres=$this->do_uploads();
		if ($upres!=$MyErrStr->Uploded) {return $upres;}
		foreach (get_class_vars(get_class($this)) as $varn) {
			$strvar=$varn['name'];
			$varn=$this->$strvar;
			if (is_array($varn)) {	
				if (in_array($varn['type'], array('varchar', 'char', 'text', 'ID')))
				{
					$parmsA[]=$varn['name'];
					$parmsB[]="'".addslashes($varn['value'])."'";
				}elseif (in_array($varn['type'], array('file'))){
					$parmsA[]=$varn['name'];
					$parmsB[]="'".$varn['ftype']."'";
				}elseif (in_array($varn['type'], array('password'))){
					$parmsA[]=$varn['name'];
					$parmsB[]="md5('".$varn['value']."')";
				}elseif (in_array($varn['type'], array('Auto_Nom'))) {
					if ($varn['value']=="") {
					$lastone=get_data_in("select ifnull(max( cast({$varn['name']} as decimal) ),0) as nn from {$this->tblname}","nn");
					$lastone+=1;
					$varn['value']=$lastone;
					}
					$parmsA[]=$varn['name'];
					$parmsB[]="'".$varn['value']."'";
				}elseif (in_array($varn['save'], array('no'))) {
						
				}else {
					$parmsA[]=$varn['name'];
					$parmsB[]="'".$varn['value']."'";
				}
			}
		}

		$sql="insert into $this->tblname (".join(", ",$parmsA).") values (".join(", ",$parmsB).")";

		$res = cmd($sql, "con");
		if ($res==$MyErrStr->DBOK){$this->Index_Record();}
		$this->onInsert($res);
		//echo $sql;
		return $res;
	}
	
	function RemoveRow()
	{
		$this->onBeforeRemove();
		
		foreach (get_class_vars(get_class($this)) as $varn) {
			$varn=$this->$varn['name'];
			if (is_array($varn) && in_array($varn['type'], array('file'))) {
				@unlink("{$this->documents_path}{$varn['prefix']}{$this->NID['value']}.{$varn['ftype']}");
					if ($varn['resize']){
						foreach ($varn['sizes'] as $size){
						@unlink("{$this->thumbs_path}{$size['p']}{$varn['prefix']}{$this->NID['value']}.{$varn['ftype']}");
						}
					}
			}	
		}
		$sql="DELETE FROM $this->tblname WHERE ".$this->NID['name']." like '".$this->NID['value']."'";

		$res = cmd($sql, "con");
		if ($res=$GLOBALS['MyErrStr']->RowDeleted){$this->Remve_Row_Index();}
		$this->onRemove($res);
		return $res;
	}
	
	function chk_required()
	{
		foreach (get_class_vars(get_class($this)) as $varn) {
			$varn=$this->$varn['name'];
			if (is_array($varn) && in_array($varn['type'], array('file'))==false) {
				if ($varn['required']==true)
				{
					if ($varn['value']===null){return false;}
					if (in_array($varn['type'], array('text', 'char', 'varchar')) && trim($varn['value'])==""){return false;}
				}
			}	
		}
		return true;
	}
	
	function chk_is_unique($old=false)
	{
		foreach (get_class_vars(get_class($this)) as $varn) {
			$varn=$this->$varn['name'];
			if (is_array($varn) && in_array($varn['type'], array('file'))==false) {
				if ($varn['unique']==true)
				{
					if ($old) {if (data_is_exists("select {$varn['name']} from {$this->tblname} where {$varn['name']} like '{$varn['value']}' and {$this->NID['name']} not like '{$this->NID['value']}'")){return false;}}
					if (!$old)if (data_is_exists("select {$varn['name']} from {$this->tblname} where {$varn['name']} like '{$varn['value']}'")){return false;}
				}
			}	
		}
		return true;
	}
	
	function validate_values()
	{
		foreach (get_class_vars(get_class($this)) as $varn) {
			$varn=$this->$varn['name'];
			if (is_array($varn) && in_array($varn['type'], array('file'))==false) {
				
				if ($varn['required']==true) {if ($varn['value']===null){return false;}}
				if ($varn['required']==true) {if (trim($varn['value'])==""){return false;}}
				
				if (!trim($varn['value'])==""){

				if ($varn['validate']=="Email")
				{
					if (!eregi("^[_a-z0-9-]+(\\.[_a-z0-9-]+)*@[a-z0-9-]+(\\.[a-z0-9-]+)*(\\.[a-z]{2,10})$",$varn['value'])){return false;}
				}
				
				if ($varn['validate']=="int")
				{
					if (!is_int($varn['value'])) return false;
				}
				
				if ($varn['validate']=="float")
				{
					//if (!is_double($varn['value'])) return false;
				}
				
				if ($varn['validate']=="reg")
				{
					if (!eregi($varn['regexp'],$varn['value'])){return false;}
				}
				
				}
			}
/*
			if ($varn['control']=='xmlarea'){
				$def_arr=$this->xml_fields_parser($varn['default']);
				$val_arr=$this->xml_fields_parser($varn['value']);
				if (is_array($def_arr)){
				foreach ($def_arr as $def_ctrl) {
					$val_ctrl=$val_arr[$def_ctrl['name']];
					$def_ctrl['name']="fld_".$strdtr['name']."[".$def_ctrl['name']."]";
					$def_ctrl['value']=$val_ctrl['value'];
					
				if ($def_ctrl['validate']=="float")
				{
					
					//if (!is_float($def_ctrl['value'])) return false;
				}
					
				}
				}
			} */
		}
		
		
		return true;
	}
	
	function validate_password()
	{
		foreach (get_class_vars(get_class($this)) as $varn) {
			$varn=$this->$varn['name'];
			if (is_array($varn) && in_array($varn['control'], array('password'))) {
				
				if ($varn['required']==true) {if ($varn['value']===null){return false;}}
				if ($varn['required']==true) {if (trim($varn['value'])==""){return false;}}
				
				if ($varn['value']!=$varn['revalue']){return false;}
			}	
		}
		return true;
	}
	
	function do_uploads()
	{
		foreach (get_class_vars(get_class($this)) as $varn) {
			$varf=&$this->$varn['name'];
			if (is_array($varf) && $varf['type']=='file') {
			if ($varf['value']['name']!=""){
			include_once("../cms/upload.php");
			$ures=upload_my_file($this->documents_path, $varf['value'], $varf['prefix'].$this->NID['value'], $varf['filetypes']);
				if ($ures==$GLOBALS['MyErrStr']->Uploded) {
					$varf['ftype']=Get_File_type($varf['value']['name']);
					
					//TRIGGER ONUPLOAD EVENT
					$this->onUpload("{$this->documents_path}{$varf['prefix']}{$this->NID['value']}.{$varf['ftype']}");
					
					if ($varf['resize']){
						foreach ($varf['sizes'] as $size){
						if (!createThumbnail("{$this->documents_path}{$varf['prefix']}{$this->NID['value']}.{$varf['ftype']}", $this->thumbs_path, "{$size['p']}{$varf['prefix']}{$this->NID['value']}", $size['w'], false, $size['h'])) {return $GLOBALS['MyErrStr']->CannotResize;}
						if ($size['mask']){
							if (!merg_two_pics("{$this->thumbs_path}/{$size['p']}{$varf['prefix']}{$this->NID['value']}.{$varf['ftype']}", $size['mask'], "{$this->thumbs_path}/{$size['p']}{$varf['prefix']}{$this->NID['value']}.{$varf['ftype']}")) {return $GLOBALS['MyErrStr']->CannotResize;}
						}
						//TRIGGER ONRESIZE
						$this->onResize($size,"{$this->thumbs_path}/{$size['p']}{$varf['prefix']}{$this->NID['value']}.{$varf['ftype']}");
						}
					}
				}else {
					return $ures;
				}
			}
			}	
		}
		return $GLOBALS['MyErrStr']->Uploded;
	}

	function More_DVNom()
	{
		$this->NVNom['value']++;
		cmd("update {$this->tblname} set {$this->NVNom['name']} = '{$this->NVNom['value']}' where {$this->NID['name']} = '{$this->NID['value']}'");
	}

	function xml_fields_parser($xmlstr, $constant_cap=true)
	{
		$ccc=new XMLReader();
		if (trim($xmlstr)=="") return "";
		$xmlstr = RemCrLf($xmlstr,"|||||");
		$ccc->XML($xmlstr);
		while ($ccc->read()){
			$ccc_name=$ccc->getAttribute("name");
			if (!$ccc_name==""){
			$arr[$ccc_name]['name']=$ccc->getAttribute("name");
			if ($constant_cap){
				$arr[$ccc_name]['caption']="xml_{$ccc_name}_cap";
				if (!defined("xml_{$ccc_name}_cap")){
				define("xml_{$ccc_name}_cap", $ccc->getAttribute("caption"));}
			}else{
				$arr[$ccc_name]['caption']=$ccc->getAttribute("caption");
			}
			$arr[$ccc_name]['value']=str_ireplace("|||||", "\n\r", $ccc->getAttribute("value"));
			$arr[$ccc_name]['type']=$ccc->getAttribute("type");
			$arr[$ccc_name]['control']=$ccc->getAttribute("control");
			$arr[$ccc_name]['required']=$ccc->getAttribute("required");
			$arr[$ccc_name]['permission']=$ccc->getAttribute("permission");
			$arr[$ccc_name]['sumview']=$ccc->getAttribute("sumview");
			$arr[$ccc_name]['datatype']=$ccc->getAttribute("datatype");
			if ($arr[$ccc_name]['datatype']==1) {
				$arr[$ccc_name]['validate']="float";
			}
			$arr[$ccc_name]['note']=str_ireplace("|||||", "\n\r", $ccc->getAttribute("note"));
			$arr[$ccc_name]['grp']=str_ireplace("|||||", "\n\r", $ccc->getAttribute("grp"));
			$arr[$ccc_name]['vtype']=$ccc->getAttribute("vtype");
			if ($arr[$ccc_name]['control']=="list"){
				$full_options=explode("|||||",$ccc->getAttribute("options"));
				if (isset($options)) {unset($options);}
				$n=0;
				foreach ($full_options as $opt) {
					if ($arr[$ccc_name]['vtype'] == "i") $cindex=$n; else $cindex=$opt; 
					if ($constant_cap){
						if (!defined("xml_{$ccc_name}_opt_{$n}")) {define("xml_{$ccc_name}_opt_{$n}",$opt);} 
						$options[$cindex]="xml_{$ccc_name}_opt_{$n}";
					}else {
						$options[$cindex]=$opt;
					}
					$n++;
				}
				$arr[$ccc_name]['options']=$options;
			}
			}
		}
		return $arr;
	}
	
	function xml_fields_composer($values_arr, $xmldef)
	{
		$def_arr=$this->xml_fields_parser($xmldef, false);
		if (!is_array($def_arr)) return "";
		$xmlstr="<root>";
		foreach ($def_arr as $defrow) {
			$defrow['value']=$values_arr[$defrow['name']];
			$xmlstr.="<fld";
			foreach ($defrow as $kname=>$vval)
			{
				if (in_array($kname,array('name','value'))) {
				if (is_array($vval)) {$vval=implode(PHP_EOL,$vval);}
				$xmlstr.=" {$kname}=\"{$vval}\"";
				}
			}
			$xmlstr.=" />";
		}
		$xmlstr.="</root>";
		return $xmlstr;
	}
	
	function Index_Record()
	{
		if ($this->IndexView==null || $this->IndexView=="") {return;}
		$SrchID=uniqid("SR");
		$TopicID=$this->NID['value'];
		
		foreach (get_class_vars(get_class($this)) as $varn) {
			$strvar=$varn['name'];
			$varn=$this->$strvar;
			if (is_array($varn)) {	
				if (in_array($varn['type'], array('varchar', 'char', 'text')))
				{
					if ($varn['control']=='fkey'){$fldtext=get_data_in("select {$varn['fTitle']} from {$varn['ftbl']} where {$varn['fID']} like '{$varn['value']}'", "{$varn['fTitle']}");}
					elseif ($varn['control']=='xmlarea') {
						$xml_arr=$this->xml_fields_parser($varn['value'],false);
						$fldtext="";
						foreach ($xml_arr as $xml_fld){
							$fldtext.=$xml_fld['caption'].": ".$xml_fld['value']." - ";
						}
					}
					else{$fldtext=$varn['value'];}
					$fldtext=str_ireplace("&nbsp;", " ", $fldtext);
					$fldtext=str_ireplace("\r\n", " ", ((addslashes(htmlspecialchars_decode(strip_tags($fldtext))))));
					$fldtext=eregi_replace("[ ]+", " ", $fldtext);
					
					if ($varn['istitle']){$Topic=$fldtext;}
					if ($varn['control']=="xmlarea"){$txt_cap=" ";}
					else{$txt_cap=constant($varn['caption']) . ": ";}
					if ($varn['indexfld']){$TopicText.= $txt_cap . $fldtext . " &nbsp;&nbsp;";}
				}
			}
		}
		$NDate=date("Y-m-d H:i:s");
		$tbl=$this->tblname;
		
		$is_update=data_is_exists("select SrchID from indextbl where TopicID like '$TopicID' and tbl like '$tbl'");
		if ($is_update){
			$sql="update indextbl set Topic = '$Topic', TopicText = '$TopicText', NDate = '$NDate', IndexView='$this->IndexView' where TopicID like '$TopicID' and tbl like '$tbl'";
		}else {
			$sql="insert into indextbl (SrchID, TopicID, Topic, TopicText, NDate, tbl, IndexView) values ('$SrchID', '$TopicID', '$Topic', '$TopicText', '$NDate', '$tbl', '$this->IndexView')";			
		}		

		return cmd($sql, "con");
	}
	
	function Remve_Row_Index()
	{
		$sql="delete from indextbl where TopicID like '$this->NID['value']' and tbl like '$this->tblname'";
	}
	
	function get_file_path($fld, $size="")
	{ 
		$fext=$fld['ftype'];
		if ($size=="")
		{
			$fp=$this->documents_path.$fld['prefix'].$this->NID['value'].".".$fext;
		}else {
			$fp=$this->thumbs_path.$fld['sizes'][$size]['p'].$fld['prefix'].$this->NID['value'].".".$fext;
		} 
		return $fp;
	}
	
	function get_file_path_def($fld, $size="", $extension="jpg")
	{ 
		$fext=$extension;
		if ($size=="")
		{
			$fp=$this->documents_path.$fld['prefix']."_default".".".$fext;
		}else {
			$fp=$this->thumbs_path.$fld['sizes'][$size]['p'].$fld['prefix']."_default".".".$fext;
		} 
		return $fp;
	}
	
	function Draw_Photo($fld, $size="", $def_ext="jpg", $href="", $target="")
	{
		if (file_exists($this->get_file_path($fld, $size)))
		{
			if ($href=="") {$_href=$this->get_file_path($fld, "");$class='class="lightwindow"';} else {$_href=$href;$class='';}
		$html_pic = <<<IMG
		<div class="card_img_div"><a href="{$_href}" target="{$target}" {$class} rel="gallery-plants" title="{$this->NTitle['value']}"><img class="card_img" src="{$this->get_file_path($fld, $size)}" /></a></div>
IMG;
		}elseif (file_exists($this->get_file_path_def($fld, $size))) {
		$html_pic = <<<IMG
		<div class="card_img_div"><a href="{$_href}" target="{$target}" class="lightwindow" rel="gallery-plants" title="{$this->NTitle['value']}"><img class="card_img" src="{$this->get_file_path_def($fld, $size)}" /></a></div>
IMG;
		}
		return $html_pic;
	}
	
	function RemoveFile($fld="all")
	{
		if ($this->NID['IsNew']) return;
		//Find File Fields
		if ($fld=="all")
		{
			foreach (get_class_vars(get_class($this)) as $varn) {
			if (is_array($varn)) {
				$strtr=$varn['name'];
				$mano=&$this->$strtr;
				if (in_array($varn['type'], array('file')))
				{
					$flds_arr[]=$mano;
				}
			}
			}
		}else {
			$flds_arr[]=$this->$fld;
		}
		
		foreach ($flds_arr as $fld) {
		
		//Unlink Orginal
		@unlink($this->get_file_path($fld));
		
		//Find All Sizes and Unlink
		if (is_array($fld['sizes']))
		foreach ($fld['sizes'] as $size=>$sized) {
			@unlink($this->get_file_path($fld,$size));
		}
		
		//Update Data
		$fldn=$fld['name'];
		$this->{$fldn}['value']="";
		
		}
		
		//Apply Changes
		$this->UpdateRow();	
	}
	
	function DisplayEditor($lblstyle="", $ShowVerificationCode=0, $btn_txt=Save, $action_filename="")
	{
		include_once '../cms/editor.php';
		DisplayEditor($this, $this->NID['value'], $lblstyle, $ShowVerificationCode, $btn_txt, $action_filename);
	}
	
	function DisplayUploadDef($lblstyle="")
	{
		include_once '../cms/editor.php';
		DisplayUploadDef($this, $this->NID['value'], $lblstyle);
	}
	
	function DisplayTable($cols, $sql, $showediting=false, $wherest, $joinlink="", $joinparam="", $jointtls="", $con="con")
	{
		include_once '../cms/table.php';
		DisplayTable($cols, $this, $sql, $showediting,$wherest,$joinlink,$joinparam,$jointtls, $con);
	}
	
	function DisplayDelMsg()
	{
		include_once '../cms/delrow.php';
		DisplayDelMsg($this, $this->NID['value']);
	}
	
	function listview()
	{
		echo "<table class='lv'>";
		foreach (get_class_vars(get_class($this)) as $varn) {
				if (!is_array($varn)) {continue;}
				$thecol=$this->{$varn['name']};
				if ($thecol['control']=='none') {continue;}
				if ($thecol['view']=='none') continue;
				
				echo "<tr><td class='lv_cap'>".@constant($thecol['caption']).": </td><td>";
				
				if (in_array($thecol['type'], array('varchar', 'char', 'text', 'ID', 'int', 'datetime', 'bool', 'float')))
				{
					if (in_array($thecol['control'], array('fkey'))){
						if ($thecol['showkey']){$thefkey=$thecol['value'];}
						echo "<td> {$thefkey} - ".get_data_in("select {$thecol['fTitle']} from {$thecol['ftbl']} where {$thecol['fID']} like '{$thecol['value']}' ", $thecol['fTitle'])."</td>";
					}elseif (in_array($thecol['control'], array('list'))){
							echo "<td>".constant($thecol['options'][$thecol['value']])."</td>";
					}elseif (in_array($thecol['control'], array('checkbox'))){
							if ($thecol['value']=="1"){$chkme="checked='checked'"; }else {$chkme = '';}
							echo "<td><input type='checkbox' $chkme /></td>";
					}else{
							echo "<td>".nl2br($thecol['value'])."</td>";
					}
				}elseif (in_array($thecol['type'], array('file')) && in_array($thecol['view'], array('image'))){
					if ($thecol['resize']==true)
					{
						echo '<td><img src="'.$this->thumbs_path.$thecol['sizes']['thumb']['p'].$thecol['prefix'].$this->NID['value'].".".$thecol['ftype'].'" /></td>';
					}else {
						echo "<td><img src=\"{$this->documents_path}{$thecol['prefix']}{$this->NID['value']}.{$thecol['ftype']}\" /></td>";
					}
				}elseif (in_array($thecol['type'], array('file')) && $thecol['view']=='link'){
					echo "<td><a href=\"../cms/download.php?fpath={$this->documents_path}{$thecol['prefix']}{$this->NID['value']}.{$thecol['ftype']}&fname=".urlencode($this->NTitle['value'])."\">".View."</a></td>";
				}
				
				echo "</td></tr>";
		}
		echo "</table>";
	}
	
	function fetch_comments()
	{
		include_once '../cms/navigator.php';
		if (!user_has_permission(array("A")))
		{
			$showhidden="and NHidden = false";
		}else {
			if ($_GET['hidcomnt']){cmd("update vcomments set NHidden=true where VID like '{$_GET['hidcomnt']}'");}
			if ($_GET['showcomnt']){cmd("update vcomments set NHidden=false where VID like '{$_GET['showcomnt']}'");}
		}
		$vcur=$_GET['cur_page'];
		$cnav=new Navigator("select * from vcomments where vNID like '{$this->NID['value']}' and NTable like '{$this->tblname}' {$showhidden} order by NDate desc", $vcur, 10);
		while ($crow=mysql_fetch_array($cnav->result))
		{
			if (user_has_permission(array("A"))) $vem='<span> | '.$crow['EMail'].'</span>';
			$HTMLROW='<div class="vcomment"><span class="vcomment_name">'.stripslashes($crow['Name']).':</span> '.stripslashes(nl2br($crow['Comment'])).'<div class="vcomment_date">'.$crow['NDate'].$vem.'</div>';
			if (user_has_permission(array("A"))){
				if ($crow['NHidden']){$toHide=Show;$HidVar="showcomnt";}else {$toHide=Hide;$HidVar="hidcomnt";}
				$HTMLROW.='<div class="vcomment_controls">'.
			showview_details("{$_SERVER['PHP_SELF']}?{$HidVar}={$crow['VID']}&lang={$GLOBALS['lang']}&NID={$_GET['NID']}&v={$_GET['v']}&cur_page={$_GET['cur_page']}",true,$toHide,array("N"))." | ".
			showdelet("../cms/vcomment.php?NID={$crow['VID']}&lang={$GLOBALS['lang']}&v=d&prev=".urlencode($_SERVER['PHP_SELF']."?NID={$this->NID['value']}&lang={$GLOBALS['lang']}&v={$_GET['v']}"),true,Delete,array("N"))
			.'</div>';
			}
			$HTMLROW.='</div>';
			echo $HTMLROW;
		}
		//4.Draw navigator line
		echo '<div class="page_nav_div" style="margin-bottom:30px;">';
		$cnav->Draw_Navigator_Line("v={$_GET['v']}&NID={$_GET['NID']}".$strpms);
		echo "</div>";
		?>
		<iframe id="IVComments" class="IVComments" name="IVComments" frameborder="0" scrolling="no" src="../cms/vcomment.php?v=e&NID=new&lang=<?=$GLOBALS['lang']?>&ntbl=<?=$this->tblname?>&vnid=<?=$this->NID['value']?>">Your browser does not support inline frames or is currently configured not to display inline frames.</iframe> 
		<?
	}
	
	function RemoveRows($filter)
	{
		$tbl=table("select * from {$this->tblname} where $filter");
		while ($row=mysql_fetch_array($tbl))
		{
			$this->FillIn($row);
			$this->RemoveRow();	
		}	
	}
	
	function DisplayCards()
	{
		
	}
/***EVENTS>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>***/
	function onStart()
	{
		return;
	}
	
	function onBeforeInsert()
	{
		
	}
	
	function onInsert($res)
	{
		return;
	}
	
	function onBeforeUpdate()
	{
		
	}
	
	function onUpdate($res)
	{
		return;
	}
	
	function onBeforeRemove()
	{
		
	}
	
	function onRemove($res)
	{
		return;
	}
	
	function onUserInsertedRow($res, &$ShowForm, &$NID)
	{
		echo($GLOBALS['MyErrStr']->Show($res));
		$ShowForm=true;
		$NID="new";
	}
	
	function onUserUpdatedRow($res, &$ShowForm, &$NID)
	{
		echo($GLOBALS['MyErrStr']->Show($res));
		$ShowForm=true;
	}

	function onUserRemovedRow($res)
	{
		if ($res==$GLOBALS['MyErrStr']->DBOK)  $res=$GLOBALS['MyErrStr']->RowDeleted; 
		echo $GLOBALS['MyErrStr']->show($res);
	}
	
	function onUpload($path)
	{
		return;
	}
	
	function onResize($size,$path)
	{
		return;
	}
	
	function onRenderStart()
	{
		return;
	}
	
	function onRenderComplete()
	{
		return;
	}
}
?>