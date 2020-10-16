<?php

require_once '../db/mysqlcon.php'; //call pages of communicating with database
include_once '../common/pframe.php'; //call pages of user defined functions
$myfr=new pframe();
class JSDataSet
{

// Dataset attibutes
	public $NID="NONE";
	public $NTitle="NONE";
	public $tblname="";
	public $documents_path="../documents/";
	public $thumbs_path="../documents/thumbs/";
	

//class constructor, automatically called when a new object is defined $x=new classx()
	function __construct($DID="IsNew")
	{	//bringing class attributes from the x.class.php file
             // attribute name of the ID atrribute
		foreach (get_class_vars(get_class($this)) as $varn) {$varn=$this->$varn['name'];if (is_array($varn) && in_array($varn['type'], array('ID'))) {$this->NID=&$this->$varn['name'];}}
		foreach (get_class_vars(get_class($this)) as $varn) {$varn=$this->$varn['name'];if (is_array($varn) && $varn['istitle']==true) {$this->NTitle=&$this->$varn['name'];}} 
	
//if the GET parameter 'NID'was empty, then it takes the values IsNew automatically, and then:
		if ($DID=="IsNew") {
			$this->NID['value']=uniqid();//hex unique ID
			$this->NID['IsNew']=true;
			$this->onStart($DID); //trigger the event onStart()
			return;
		}

//if the GET parameter 'NID'=add_existing (this is in the 'download' page)		
		if ($DID=="add_existing") {
			$this->onStart($DID);
		}

//bring records related to that NID parameter		
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
/*****************************************  END OF FUNCTION   ***************************************/

	
	function FillIn($arr,$chk_is_new=true) //fills a query result into an x object 
	{
		//Fields
		foreach (get_class_vars(get_class($this)) as $varn) {
//bring class's attributes
			$strtr=$varn['name'];
			$mano=&$this->$strtr;
			if (is_array($varn)) { 
//and these attributes has many types	
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
 //if this id already exists in db
			$chk=get_data_in("select count(".$this->NID['name'].") as chk from {$this->tblname} where {$this->NID['name']} = '{$this->NID['value']}' ","chk");
			if ($chk>0) {$this->NID['IsNew']=false;           //Not new} 
                                else {$this->NID['IsNew']=true;             //is new}
		}
		$this->onStart($this->NID['value']);
	}
/*****************************************  END OF FUNCTION   ***************************************/
		
	function UpdateRow()    //data updating operation
	{
		$this->onBeforeUpdate();    //trigger the onBefore event
		
		$MyErrStr=new ErrStr();
		
//check validity according to the class VALIDATION attributes 
		if (!$this->chk_required()) {return $MyErrStr->FillAllRequierd;}
		if (!$this->validate_values()) {return $MyErrStr->InvalidMail;}
		if (!$this->validate_password()) {return $MyErrStr->ReTypePassword;}
	 	if (!$this->chk_is_unique(true)) {return $MyErrStr->DataIsExist;}
	 	
	 	
		$upres=$this->do_uploads();        //upload 'file' if exist
		if ($upres!=$MyErrStr->Uploded) {return $upres;}
		
		foreach (get_class_vars(get_class($this)) as $varn) {      //manipulating class attributes
			$varn=$this->$varn['name'];
			if (is_array($varn) && in_array($varn['type'], array('file', 'ID'))==false) {
				
//if type is one of these
				if (in_array($varn['type'], array('varchar', 'char', 'text')))
				{
					$parms[]=$varn['name']."='".addslashes($varn['value'])."'";
				}else {
					$parms[]=$varn['name']."='".$varn['value']."'";
				}
			}
			if ($varn['type']=='file') {     //if file 
				$parms[]=$varn['name']."='".$varn['ftype']."'";
			}
			if ($varn['type']=='password') {     //if password
				$parms[]=$varn['name']."=md5('".$varn['value']."')";
			}	
		}
		$sql="update {$this->tblname} set ".join(",",$parms)." where {$this->NID['name']} = '{$this->NID['value']}'";
//execute the update sql query according to this row
		$res = cmd($sql, "con");
		if ($res==$MyErrStr->DBOK){$this->Index_Record();}
		$this->onUpdate($res); //trigger onUpdate event
		return $res;
	}
/*****************************************  END OF FUNCTION   ***************************************/
	
	function InsertRow()      //data inserting operation 
	{
		$this->onBeforeInsert();     //trigger event previous to inserting
		
		if ($this->NID['value']=='' || $this->NID['value']==null || $this->NID['value'] == "IsNew"){
			$this->NID['value']=uniqid();
      //generate new hex unique id if the NID is empty|IsNew
		}

//check validity according to the class VALIDATION attributes		
		$MyErrStr=new ErrStr();
		if (!$this->chk_required()) {return $MyErrStr->FillAllRequierd;}
		if (!$this->validate_values()) {return $MyErrStr->InvalidMail;}
		if (!$this->validate_password()) {return $MyErrStr->ReTypePassword;}
		if (!$this->chk_is_unique()) {return $MyErrStr->DataIsExist;}
	
		$upres=$this->do_uploads();     //upload 'file' if exists
		if ($upres!=$MyErrStr->Uploded) {return $upres;}
		foreach (get_class_vars(get_class($this)) as $varn) {    //manipulating class attributes
			$strvar=$varn['name'];
			$varn=$this->$strvar;
			if (is_array($varn)) {	//if type is one of these
				if (in_array($varn['type'], array('varchar', 'char', 'text', 'ID')))
				{
					$parmsA[]=$varn['name'];
					$parmsB[]="'".addslashes($varn['value'])."'";
				}elseif (in_array($varn['type'], array('file'))){ //type=file
					$parmsA[]=$varn['name'];
					$parmsB[]="'".$varn['ftype']."'";
				}elseif (in_array($varn['type'], array('password'))){ //type=password
					$parmsA[]=$varn['name'];
					$parmsB[]="md5('".$varn['value']."')";
				}elseif (in_array($varn['type'], array('Auto_Nom'))) {//type=autonum
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

//execute the insert sql query according to this row
		$sql="insert into $this->tblname (".join(", ",$parmsA).") values (".join(", ",$parmsB).")";

		$res = cmd($sql, "con");
		if ($res==$MyErrStr->DBOK){$this->Index_Record();}
		$this->onInsert($res);//trigger event
		//echo $sql;
		return $res;
	}
/*****************************************  END OF FUNCTION   ***************************************/

	function RemoveRow()
	{
		$this->onBeforeRemove();//trigger event
		
		foreach (get_class_vars(get_class($this)) as $varn) {//bring class attribs
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

		$res = cmd($sql, "con");//execute the delete sql query according to this row
		if ($res=$GLOBALS['MyErrStr']->RowDeleted){$this->Remve_Row_Index();}
		$this->onRemove($res);
		return $res;
	}
/*****************************************  END OF FUNCTION   ***************************************/
	
	function chk_required()
	{
//check the attribute 'required' foreach field in the class to prevent add an empty value
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
/*****************************************  END OF FUNCTION   ***************************************/
	
	function chk_is_unique($old=false)
	{
//check the attribute 'unique' foreach field in the class to prevent add existing value
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
/*****************************************  END OF FUNCTION   ***************************************/
	
	function validate_values()
	{
//perform validation operations for each field in the class according to validation type of each
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
		}
		return true;
	}
/*****************************************  END OF FUNCTION   ***************************************/
	
	function validate_password()
	{
//Password Validation opration: not null or empty 
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
/*****************************************  END OF FUNCTION   ***************************************/
	
	function do_uploads()
	{
//files uploading operation with considering: path and resize(for photos). +triggering upload events
		foreach (get_class_vars(get_class($this)) as $varn) {
			$varf=&$this->$varn['name'];
			if (is_array($varf) && $varf['type']=='file') {
			if ($varf['value']['name']!=""){
			include_once("../cms/upload.php");//call file include uploads functions
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
/*****************************************  END OF FUNCTION   ***************************************/

	function More_DVNom()
	{
// increase the number of views or visits for the object has called this
		$this->NVNom['value']++;
		cmd("update {$this->tblname} set {$this->NVNom['name']} = '{$this->NVNom['value']}' where {$this->NID['name']} = '{$this->NID['value']}' ");
	}
/*****************************************  END OF FUNCTION   ***************************************/
	
	function Index_Record()
	{
//this function inserts a record in the indexing table to make search faster

		if ($this->IndexView==null || $this->IndexView=="") {return;}
		$SrchID=uniqid("SR");
		$TopicID=$this->NID['value'];
		
		foreach (get_class_vars(get_class($this)) as $varn) {
//cheching the class attribs: fkey, all types, istitle
			$strvar=$varn['name'];
			$varn=$this->$strvar;
			if (is_array($varn)) {	
				if (in_array($varn['type'], array('varchar', 'char', 'text')))
				{
					if ($varn['control']=='fkey'){$fldtext=get_data_in("select {$varn['fTitle']} from {$varn['ftbl']} where {$varn['fID']} like '{$varn['value']}'", "{$varn['fTitle']}");}
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

//insert/update after checking existance		
		$is_update=data_is_exists("select SrchID from indextbl where TopicID like '$TopicID' and tbl like '$tbl'");
		if ($is_update){
			$sql="update indextbl set Topic = '$Topic', TopicText = '$TopicText', NDate = '$NDate', IndexView='$this->IndexView' where TopicID like '$TopicID' and tbl like '$tbl'";
		}else {
			$sql="insert into indextbl (SrchID, TopicID, Topic, TopicText, NDate, tbl, IndexView) values ('$SrchID', '$TopicID', '$Topic', '$TopicText', '$NDate', '$tbl', '$this->IndexView')";			
		}		

		return cmd($sql, "con");
	}
/*****************************************  END OF FUNCTION   ***************************************/
	
	function Remve_Row_Index()
	{
//removing and index from index table
		$sql="delete from indextbl where TopicID like '$this->NID['value']' and tbl like '$this->tblname'";
	}
/*****************************************  END OF FUNCTION   ***************************************/
	
	function get_file_path($fld, $size="")
	{ 
//returns the file path according to the attribute prefix & sizes in the class
		$fext=$fld['ftype'];
		if ($size=="")
		{
			$fp=$this->documents_path.$fld['prefix'].$this->NID['value'].".".$fext;
		}else {
			$fp=$this->thumbs_path.$fld['sizes'][$size]['p'].$fld['prefix'].$this->NID['value'].".".$fext;
		} 
		return $fp;
	}
/*****************************************  END OF FUNCTION   ***************************************/
	
	function get_file_path_def($fld, $size="", $extension="jpg")
	{ 
//returns the default file path for a given extention
		$fext=$extension;
		if ($size=="")
		{
			$fp=$this->documents_path.$fld['prefix']."_default".".".$fext;
		}else {
			$fp=$this->thumbs_path.$fld['sizes'][$size]['p'].$fld['prefix']."_default".".".$fext;
		} 
		return $fp;
	}
/*****************************************  END OF FUNCTION   ***************************************/
	
	function Draw_Photo($fld, $size="", $def_ext="jpg", $href="", $target="")
	{
// an object calls this function, ex.$myProduct->Draw_Photo(…), so it draws HTML emelents + javascript including the object's photo according to it's ID
		if (file_exists($this->get_file_path($fld, $size)))
		{
			$news_title = $this->{'news_title_'.$GLOBALS['lang']}['value'];
			if ($href=="") {$_href=$this->get_file_path($fld, "");$class='class="lightwindow"';} else {$_href=$href;$class='';}
		$html_pic = <<<IMG
		<div  class="card_img_div"><a href="{$_href}" target="{$target}" {$class} rel="gallery-plants" title="{$news_title}"}><img class="card_img" src="{$this->get_file_path($fld, $size)}" /></a></div>
IMG;
		}elseif (file_exists($this->get_file_path_def($fld, $size))) {
		$html_pic = <<<IMG
		<div  class="card_img_div"><a href="{$_href}" target="{$target}" class="lightwindow" rel="gallery-plants" title="{$news_title}"><img class="card_img" src="{$this->get_file_path_def($fld, $size)}" /></a></div>
IMG;
		}
		return $html_pic;
	}
/*****************************************  END OF FUNCTION   ***************************************/
	
	function RemoveFile($fld="all")
	{
//unlinking + removing a file attached to an object ex. A photo of a product, so the field with a type=file becomes empty and the attached file be deletes
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
/*****************************************  END OF FUNCTION   ***************************************/
	
	function DisplayEditor($lblstyle="", $ShowVerificationCode=0, $btn_txt=Save, $action_filename="")
	{
//calls the function that draws Editor, this happens in 'Inserting' & 'Updating' Cases.
		include_once '../cms/editor.php';
		DisplayEditor($this, $this->NID['value'], $lblstyle, $ShowVerificationCode, $btn_txt, $action_filename);
	}
/*****************************************  END OF FUNCTION   ***************************************/
	
	function DisplayUploadDef($lblstyle="")
	{
// calls the function that draws Uploading Editor, this happens in 'Inserting' & 'Updating' Cases.
		include_once '../cms/editor.php';
		DisplayUploadDef($this, $this->NID['value'], $lblstyle);
	}
/*****************************************  END OF FUNCTION   ***************************************/

	function DisplayDelMsg($delete_what="")
	{
//when we choose to delete an object, this displays the delete message with the name of that object, you choose between YES/NO, it acts according to your choise with deleting it or not
		include_once '../cms/delrow.php';
		DisplayDelMsg($this, $this->NID['value'], $delete_what);
	}
/*****************************************  END OF FUNCTION   ***************************************/
		
	function RemoveRows($filter)
	{
//deletes rows according to a 'where' filter clause.
		$tbl=table("select * from {$this->tblname} where $filter");
		while ($row=mysql_fetch_array($tbl))
		{
			$this->FillIn($row);
			$this->RemoveRow();	
		}	
	}
/*****************************************  END OF FUNCTION   ***************************************/

/***EVENTS>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>***/
	
	function onUserInsertedRow($res, &$ShowForm, &$NID)
	{
//triggers on inserting a data row
		echo($GLOBALS['MyErrStr']->Show($res));
		$ShowForm=true;
		$NID="new";
	}
	
	function onUserUpdatedRow($res, &$ShowForm, &$NID)
	{
//triggers on updating a data row
		echo($GLOBALS['MyErrStr']->Show($res));
		$ShowForm=true;
	}

	function onUserRemovedRow($res)
	{
//triggers on deleting a data row
		if ($res==$GLOBALS['MyErrStr']->DBOK)  $res=$GLOBALS['MyErrStr']->RowDeleted; 
		echo $GLOBALS['MyErrStr']->show($res);
	}
}
?>