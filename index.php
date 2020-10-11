Index
First     ../Cms folder	3
1)../cms/dataset.php	3
2)../cms/editor.php	15
3)../cms/table.php	19
4)../cms/delrow.php	21
5) ../cms/secreg.php	22
6) ../cms/navigator.php	23
Second  ../common Folder	25
7) ../common/control_panel.php	25
8) ../common/index.php	26
9) ../common/main_ar.css + ../common/main_en.css	28
10) ../common/pframe.php	40
11) ../common/signin.php	51
12) ../common/signup.php	53
13)../db/mysqlcon.php	54
14)../db/settings.php	61
Fourth ../lang Folder	61
15)  ../lang/lang_ar.inc	61
16)  ../lang/pages_ar.inc	64
Fifth  ../obj   Folder	67
17) ../obj/ads.class.php	67
18) ../obj/album.class.php	68
19) ../obj/buy_contract.class.php	69
20) ../obj/category.class.php	69
21) ../obj/contactus.class.php	70
22) ../obj/faq.class.php	71
23) ../obj/groups.class.php	71
24) ../obj/job.class.php	72
25) ../obj/maint_contract.class.php	72
26) ../obj/news.class.php	73
27) ../obj/picture.class.php	74
28) ../obj/product.class.php	75
29) ../obj/service.class.php	77
30) ../obj/ucomment.class.php	77
31) ../obj/user.class.php	78
32) ../obj/user_config.class.php	79
33) ../obj/user_group_privs.class.php	79
34) ../obj/user_groups.class.php	79
35) ../obj/user_privs.class.php	80
36) ../obj/video.class.php	80
37) ../obj/blocked_ips.class.php	81
Sixth ../pages  folder	81
38) ../pages/ads.php    (ADVERTISEMENT Page)	81
39) ../pages/albums.php         (ALBUMS page)	83
40) ../pages/bconts.php          (BUY CONTRACT Page)	85
41) ../pages/category.php           (CATEGORIES Page)	86
42) ../pages/contactus.php               (CONTACT US & ABOUT Pages)	88
43) ../pages/download.php              (SOFTWARE DOWNLOADS Page)	90
44) ../pages/faq.php                   (Frequently Asked Questions Pages)	92
45) ../pages/jobs.php                    (JOBS we are offering)	93
46) ../pages/mconts.php                      (MAINTENANCE CONTRACTS)	95
47) ../pages/news.php                    (NEWS Page)	97
48) ../pages/our_customers.php                       (OUR CUSTOMERS Page)	99
49) ../pages/pictures.php	101
50) ../pages/products.php	103
51) ../pages/send_form_email.php	105
52) ../pages/services.php                  (OUR SERVICES Page)	107
53) ../pages/users.php	108

First     ../Cms folder


1)../cms/dataset.php



Dataset is the class brings data from database and fill it into the new object we have declared.
It also does the operations of insert, update, delete and contains the events and calls editors  related to those operations.
In addition, It contains functions of dealing with files (paths/extensions…)


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
2)../cms/editor.php



Draws a specific html control foreach database field , making a whole form to submit data to the database. It also makes validation for this form.


<?php
include_once '../cms/drawcontrol.php';
function DisplayEditor($DataRow, $NID="", $lblstyle="", $ShowVerficationCode=0, $btn_txt=Save, $action_filename=""){

//if no NID parameter in the GET, make it's value IsNew and fill it with a unique ID 
if ($NID=="") {
	$NID="IsNew";
	$mynewid=&$DataRow->NID;
	$mynewid['value']=uniqid();
	$mynewid['IsNew']=true;
}

if ($action_filename=="") $action_filename=$_SERVER['PHP_SELF'];

$ShowForm=true;

//Update Data
if (isset($_POST['doit'])) 
{
	foreach (get_class_vars(get_class($DataRow)) as $varn) {
//manipulate the class's fields and fill them into an array
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
///////////////////////// VERFICATION
		$key=$_SESSION['image_value'];
		$imag = $_POST['txtpic'];
		$user = md5(strtoupper($imag));
		if ($user==$key) {
 			$verf="true";
		}
//////////////////////////////
	}
	
//displays error messages 
	if ($ShowVerficationCode>0 && $verf!="true") {
		echo $GLOBALS['MyErrStr']->Show($GLOBALS['MyErrStr']->NotVerified);
	}else {
		
//if a new ID then Do Inserting
	if ($DataRow->NID['IsNew']) {
		$res=$DataRow->InsertRow();
		$DataRow->onUserInsertedRow($res, $ShowForm, $NID);
		
	}else {
//not new ID means already exists then do updating
		$res=$DataRow->UpdateRow();
		$DataRow->onUserUpdatedRow($res, $ShowForm, $NID);
	}
	
	}
}
//collect the GET parameters into an array
	foreach ($_GET as $pmk => $pmv) {
		if ($pmk!="v" && $pmk!="lang" && $pmk!="NID"){
		$strpms[]=$pmk."=".$pmv;	
		}
	}
	$strpms=@join("&",$strpms);
	if ($ShowForm){
	
	$DataRow->onRenderStart();

//Display the form of inserting | updating fields
?><form id="FRM" method="POST" action="<?= $action_filename ?>?NID=<? echo $NID; ?>&v=e&lang=<?=$GLOBALS['lang']?>&<?=$strpms?>" enctype="multipart/form-data">
<table class="form_table">
<tr>
	<td>
	<?php
//check all class fields if it has a control attribute to displays it's control
	foreach (get_class_vars(get_class($DataRow)) as $varn) {
		if (is_array($varn) && $varn['control']!='none') {
			$strtr=$varn['name'];
			$strdtr=$DataRow->$strtr;

			}else {
//draw html control
				draw_control($DataRow ,$DataRow->$strtr, $lblstyle);
			}
		}	
	}
	?>
	</td>
</tr>
	<?

//display verification messages for the fileds 
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
			<td><div  class="VerfLabel"><strong class="bigcomment" ><?=Verification?></strong>: <span style="font-family:Arial" class="ttxtg"><?=This_helps_us_to_prevent_automatic_registration?></span></div></td>
			<td><div  id="vrimg" style="width:127px; height:70px"><img src="../cms/verf_img.php" /></div><!--&nbsp;[<a onclick="redrawimg('vrimg');">Change Image</a>]<br /><br />--></td>
		</tr>
		<tr>
			<td></td>
			<td valign="top"><input name="txtpic" id="txtpic" type="text" /></td>
			<td valign="top">
			<table cellpadding="0" cellspacing="0"><tr><td class="msgtd"><div  id="ivmsg" class="MsgDiv" style="display:none"></div></td></tr></table></td>
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
//this javascript block is to prevent adding other characters 
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
	});	///////////////////////////////////////////

</script>
<?php

	$DataRow->onRenderComplete();
	
	}
}

function DisplayUploadDef($DataRow, $lblstyle=""){ //upload form
	
//if the user doesn't have permission to do this, then send him to login page|error page
	/***/IS_SECURE();/***/

	$NID="IsNew";
	$mynewid=&$DataRow->NID;
	$mynewid['value']="_default";
	$mynewid['IsNew']=false;
	
$ShowForm=true;

//Update Data
if (isset($_POST['doit'])) {
	foreach (get_class_vars(get_class($DataRow)) as $varn) {   //class fields
		$strtr=$varn['name'];
		$myar=&$DataRow->$strtr;
		if ($varn['type']=='file' && $varn['view']=='image'){
			$myar['value']=$_FILES['txt_'.$varn['name']];
		}
	}
	
	$res=$DataRow->do_uploads();
	echo $GLOBALS['MyErrStr']->Show($res);
}
	foreach ($_GET as $pmk => $pmv) { //fill GET parameters
		if ($pmk!="v" && $pmk!="lang" && $pmk!="NID"){
		$strpms[]=$pmk."=".$pmv;	
		}
	}
	$strpms=@join("&",$strpms);
	if ($ShowForm){

//display form
?>
<form id="FRM" method="POST" action="<?=$_SERVER['PHP_SELF']?>?NID=<?=$NID?>&v=u&lang=<?=$GLOBALS['lang']?>&<?=$strpms?>" enctype="multipart/form-data">
<table class="form_table">
<tr>
	<td>
	<?php
	foreach (get_class_vars(get_class($DataRow)) as $varn) {
		if (is_array($varn) && $varn['control']=='file' && $varn['view']=='image') {
			$strtr=$varn['name'];
			$strdtr=$DataRow->$strtr;
// draw html Conrtol
			draw_control($DataRow ,$DataRow->$strtr, $lblstyle);
		}	
	}
	?>
	</td>
</tr>
<tr>
	<td style="text-align:center"><input type="submit" name="doit" id="doit" onclick="checkform(document.getElementById('#FRM'))" value="&nbsp;&nbsp;&nbsp;<?=Save?>&nbsp;&nbsp;&nbsp;" style="cursor:pointer;" /></td>	
</tr>
</table>
</form>
<?php
	}
}
?>
3)../cms/drawcontrol.php‬


Displays HTML control for each field in the php class (for each field in the db), according to the attribute 'control' of each field.



<?php 
$GLOBALS ['calloaded'] = false;
function draw_control($DataRow, $actrl, $lblstyle = "")
{
	if ($actrl ['permission'] !== null && ! user_has_permission ( $actrl ['permission'] ))
	{return;}

// CHeching the value of the attribute 'control' with the 'Switch' for the given object
	switch ($actrl ['control'])
	{
		case 'hidden' : // control = hidden . input type = hidden

			echo '<div class="jfld' . $lblstyle . '"><input type="hidden" name="txt_' . $actrl ['name'] . '" id="txt_' . $actrl ['name'] . '" value="' . $actrl ['value'] . '" /></div>';
			break;

		case 'text' : // control = text . input type = text

			echo '<div class="jfld' . $lblstyle . '"><div class="field_label' . $lblstyle . '">' .addreq ( $actrl ['required'] ). constant ( $actrl ['caption'] )  . '</div><div class="txtfld' . $lblstyle . '"><input type="text" name="txt_' . $actrl ['name'] . '" id="txt_' . $actrl ['name'] . '" class="validate_' . $actrl ['validate'] . '" value="' . $actrl ['value'] . '" /></div><div class="field_note' . $lblstyle . '">' . $actrl ['note'] . '</div></div>';
			break;

		case 'password' : // control = password . input type = password

			echo '<div class="jfld' . $lblstyle . '"><div class="field_label' . $lblstyle . '">'.addreq ( $actrl ['required'] ) . constant ( $actrl ['caption'] ) . '</div><div class="txtfld' . $lblstyle . '"><input  type="password" name="txt_' . $actrl ['name'] . '" id="txt_' . $actrl ['name'] . '" value="' . $actrl ['value'] . '" /></div><div class="field_note' . $lblstyle . '">' . $actrl ['note'] . '</div></div>';
			if (user_has_permission ( array ("A" ) ))
			{
				$myrepass = 'value="' . $actrl ['value'] . '"';
			}
			echo '<div class="jfld' . $lblstyle . '"><div class="field_label' . $lblstyle . '">'.addreq ( $actrl ['required'] ) . constant ( 'Re-Type' ) . ' ' . constant ( $actrl ['caption'] )  . '</div><div class="txtfld' . $lblstyle . '" ><input type="password" name="txt_' . $actrl ['name'] . '_r" id="txt_' . $actrl ['name'] . '_r" ' . $myrepass . ' /></div></div>';
			break;

		case 'textarea' : // control = text area . html <textarea>

			echo '<div class="jfld' . $lblstyle . '"><div class="field_label' . $lblstyle . '">'.addreq ( $actrl ['required'] ) . constant ( $actrl ['caption'] )  . '</div><div class="txtfld' . $lblstyle . '"><textarea   name="txt_' . $actrl ['name'] . '" id="txt_' . $actrl ['name'] . '" cols="40" rows="10">' . $actrl ['value'] . '</textarea></div><div class="field_note' . $lblstyle . '">' . $actrl ['note'] . '</div></div>';
			break;

		case 'fkey' : // control = fkey, (html select), Foreign value brought from another table

			$mytbl = table ( "select {$actrl['fID']}, {$actrl['fTitle']} from {$actrl['ftbl']} {$actrl['fFltr']} order by {$actrl['fTitle']}" );
			echo '<div class="jfld' . $lblstyle . '"><div class="field_label' . $lblstyle . '">'.addreq ( $actrl ['required'] ) . constant ( $actrl ['caption'] )  . '</div>
			<div class="txtfld' . $lblstyle . '">
			<select  name="txt_' . $actrl ['name'] . '" id="txt_' . $actrl ['name'] . '">';

//each foreign key control has 'options' attribute, that must match constant variable to display
// these options can also brought by performing sql query in attributes such as:
// ftbl, fTitle, fID, fFltr
			foreach ( $actrl ['options'] as $kopt => $vopt )
			{
				$actrl ['value'] == $kopt ? $selme = "selected" : $selme = "";
				echo '<option value="' . $kopt . '" ' . $selme . ' >' . constant ( $vopt ) . '</option>';
			}
			while ( $fkrow = mysql_fetch_array ( $mytbl ) )
			{
				$actrl ['value'] == $fkrow [0] ? $selme = "selected" : $selme = "";
				echo '<option value="' . $fkrow [0] . '" ' . $selme . ' >' . $fkrow [1] . '</option>';
			}
			echo '</select></div><div class="field_note' . $lblstyle . '">' . $actrl ['note'] . '</div></div>';
			break;

// control = list, specific values that we want to give to a field, we can define the options by the attribute 'options' that take a value of array('op1'=>'val1', 'op2'=>'val2'….)
		case 'list' :
			echo '<div class="jfld' . $lblstyle . '"><div class="field_label' . $lblstyle . '">'.addreq ( $actrl ['required'] ) . constant ( $actrl ['caption'] ) . '</div><div class="txtfld' . $lblstyle . '"><select  name="txt_' . $actrl ['name'] . '" id="txt_' . $actrl ['name'] . '">';
			foreach ( $actrl ['options'] as $kopt => $vopt )
			{
				$actrl ['value'] == $kopt ? $selme = "selected" : $selme = "";
				echo '<option value="' . $kopt . '" ' . $selme . ' >' . constant ( $vopt ) . '</option>';
			}
			echo '</select></div><div class="field_note' . $lblstyle . '">' . $actrl ['note'] . '</div></div>';
			break;
// control = countries, list of countries
		case 'countries' :
			echo '<div class="jfld' . $lblstyle . '"><div class="field_label' . $lblstyle . '">' .addreq ( $actrl ['required'] ). constant ( $actrl ['caption'] ) . '</div><div class="txtfld' . $lblstyle . '"><select  name="txt_' . $actrl ['name'] . '" id="txt_' . $actrl ['name'] . '">';
			$tbl = table ( "select * from countries" );
			while ( $dd = mysql_fetch_row ( $tbl ) )
			{
				$actrl ['value'] == $dd [1] ? $selme = "selected" : $selme = "";
				echo '<option value="' . $dd [1] . '" ' . $selme . ' >' . $dd [1] . '</option>';
			}
			echo '</select></div><div class="field_note' . $lblstyle . '">' . $actrl ['note'] . '</div></div>';
			break;
// control = date, when a db field has DATETIME type, it takes this case.
		case 'date' :
			if (! $GLOBALS ['calloaded'])
			{
				echo '<!-- import the calendar script -->
					<script src="../jscal2-1.9/src/js/jscal2.js"></script>
			    	<script src="../jscal2-1.9/src/js/lang/en.js"></script>
			    	<link rel="stylesheet" type="text/css" href="../jscal2-1.9/src/css/jscal2.css" />
			    	<link rel="stylesheet" type="text/css" href="../jscal2-1.9/src/css/border-radius.css" />
			    	<link rel="stylesheet" type="text/css" href="../jscal2-1.9/src/css/steel/steel.css" />';
			}
// calling javascript files

			$GLOBALS ['calloaded'] = true;

// now display html input an other html elements for collecting date value
			echo '<div class="field_box' . $lblstyle . '"><div class="ttxtg field_label' . $lblstyle . '">'.addreq ( $actrl ['required'] ) . constant ( $actrl ['caption'] )  . '</div><div class="txtfld' . $lblstyle . '"  ><input type="text"  name="txt_' . $actrl ['name'] . '" id="txt_' . $actrl ['name'] . '" value="' . $actrl ['value'] . '" /> <img id="img_' . $actrl ['name'] . '"  name="img_' . $actrl ['name'] . '" style="cursor:pointer;" src="../images/cal.gif" alt="' . Click_Here_to_Pick_up_the_date . '" class="datecalimg' . $lblstyle . '"   />
		<script type="text/javascript">
		var cal_' . $actrl ['name'] . ' = Calendar.setup({
        	onSelect   : function() { this.hide() },
        	showTime   : ' . $actrl ['withtime'] . '
      	});
      	cal_' . $actrl ['name'] . '.manageFields("img_' . $actrl ['name'] . '", "txt_' . $actrl ['name'] . '", "' . $actrl ['format'] . '");
		</script></div></div>';
			break;

// control = file, displays the button to browse for the file you want to upload… input type = file
		case 'file' :
			echo '<div  class="jfld' . $lblstyle . '" style="clear:both;overflow:hidden;">	<input type="hidden" name="MAX_FILE_SIZE" value="100000000" />'
			.'<div class="field_label' . $lblstyle . '">'.addreq ( $actrl ['required'] ) . constant ( $actrl ['caption'] )  . '</div><div class="txtfld' . $lblstyle . '"><input  name="txt_' . $actrl ['name'] . '" id="txt_' . $actrl ['name'] . '" type="file" /></div><div class="field_note' . $lblstyle . '">' . $actrl ['note'] . '</div></div>';

//if you upload an image file, it may have attribute 'resize' which makes a thumb copy of the image
			if ($actrl ['view'] == 'image' && $DataRow->NID ['IsNew'] == false)
			{
				if ($actrl ['resize'] == true)
				{
					echo '<div style="clear:both;overflow:hidden;width:200px;margin:auto"><img style="margin:15px" src="' . $DataRow->get_file_path ( $actrl, "thumb" ) . '" /></div>';
				} else
				{
					echo '<div style="clear:both;overflow:hidden;width:200px;margin:auto"><img style="margin:15px" src="' . $DataRow->get_file_path ( $actrl ) . '" /></div>';
				}
			}			
			break;		
	} 
}

// some fields in php class files has attribule 'required'='required', to validate the form and prevent empty values. Those field appear with '*' sign.
function addreq($myreq) 
{
	if ($myreq == true)
	{?><span class="requied_field">*</span><? }
}
?>
3)../cms/table.php



Displays a table of data according to an sql query.


<?php
require_once '../cms/navigator.php';   //table with pagination
function DisplayTable($cols, $DataSet, $sql, $showediting=false, $wherestmnt="", $joinlink="", $joinparam="",$jointtls=""){

//fill GET params into an array
	foreach ($_GET as $pmk => $pmv) {
		if ($pmk!="v" && $pmk!="lang" && $pmk!="NID"){
		$strpms[]=$pmk."=".$pmv;	
		}
	}
	$strpms=@join("&",$strpms);
	
	$cur_page=$_REQUEST['cur_page']; // parameter of GET
	$old_first=false;
	
//Bring data of the sql query result and fill it inside a paginated table with 20 row a page
	$nav1=new Navigator($sql, $cur_page, 20, "select count({$cols[0]}) from {$DataSetTmp->tblname} {$wherestmnt}");
	while ($tblrow=mysql_fetch_array($nav1->result)) {
//manipulate data for every row brought
//manipulate fields of the class
		echo "<tr>";
		foreach ($cols as $col) {
				$thecol=$DataSetTmp->$col;
			
				if (in_array($thecol['type'], array('varchar', 'char', 'text', 'ID', 'int', 'double', 'datetime')))
				{
					if (in_array($thecol['control'], array('fkey'))){
						if ($thecol['showkey']){$thefkey=$tblrow[$col];}



//draw an HTML input in a row <td> for each row according to the 'type' attribute of each field
// type is one of these : textbox, textarea, ID, INT, double, datatime

//brings data related to the fkey value & fTitle & fID
						echo "<td> {$thefkey} - ".get_data_in("select {$thecol['fTitle']} from {$thecol['ftbl']} where {$thecol['fID']} like '{$tblrow[$col]}' ", $thecol['fTitle'])."</td>";

//if the file type is 'list' then:

					}elseif (in_array($thecol['control'], array('list'))){
						if (is_array($jointtls)) {$a_links=array_keys($jointtls,"@".$thecol['name']);}
						if ($a_links[0]!==null){
							$link_id=$a_links[0];
							echo "<td><a href='{$joinlink[$link_id]}&{$joinparam[$link_id]}={$tblrow[$DataSetTmp->NID['name']]}'>".constant($thecol['options'][$tblrow[$col]])."</a></td>";
						}else{
							echo "<td>".constant($thecol['options'][$tblrow[$col]])."</td>";
						}
					}else{
						if (is_array($jointtls)) {$a_links=array_keys($jointtls,"@".$thecol['name']);}
						if ($a_links[0]!==null){
							$link_id=$a_links[0];
							echo "<td><a href='{$joinlink[$link_id]}&{$joinparam[$link_id]}={$tblrow[$DataSetTmp->NID['name']]}'>{$tblrow[$col]}</a></td>";
						}else{
							echo "<td>{$tblrow[$col]}</td>";
						}
					}

//if the file type is 'file' then show image of it:

				}elseif (in_array($thecol['type'], array('file')) && in_array($thecol['view'], array('image'))){
					if ($thecol['resize']==true)
					{
						echo '<td><img src="'.$DataSetTmp->thumbs_path.$thecol['sizes']['thumb']['p'].$thecol['prefix'].$tblrow[$DataSetTmp->NID['name']].".".$tblrow[$col].'" /></td>';
					}else {
						echo "<td><img src=\"{$DataSetTmp->documents_path}{$thecol['prefix']}{$tblrow[$DataSetTmp->NID['name']]}.{$tblrow[$col]}\" /></td>";
					}

//If the file has a 'link' show it
				}elseif (in_array($thecol['type'], array('file')) && $thecol['view']=='link'){
					echo "<td><a href=\"{$DataSetTmp->documents_folder}{$thecol['prefix']}{$tblrow[$DataSetTmp->NID['name']]}.{$tblrow[$col]}\">".View."</a></td>";
				}
		}

//to show 'related page' section
		if ($showediting){
			if ($joinlink!="") {
			if (is_array($joinlink)){
				for ($i=0; $i < count($joinlink); $i++){
					if (substr($jointtls[$i],0,1)=="#"){
						echo "<td><a href='{$joinlink[$i]}&{$joinparam[$i]}={$tblrow[$DataSetTmp->NID['name']]}'>".substr($jointtls[$i],1)."</a></td>";
					}elseif(substr($jointtls[$i],0,1)=="@"){
					}else{
						echo "<td>".showview_details($joinlink[$i]."&".$joinparam[$i]."=".$tblrow[$DataSetTmp->NID['name']], true, "", array("N"))."</td>";
					}	
				}
			}else {
				echo "<td>".showview_details($joinlink."&".$joinparam."=".$tblrow[$DataSetTmp->NID['name']], true, "", array("N"))."</td>";	
			}
			}

//user must be admin with full control to display 'related page' section 
			if (user_has_permission(array("A"))) {
			echo "<td>".showedit($_SERVER['PHP_SELF']."?lang={$GLOBALS['lang']}&NID={$tblrow[$DataSetTmp->NID['name']]}&v=e&{$strpms}", false, "", array("A"))."</td>";
			echo "<td>".showdelet($_SERVER['PHP_SELF']."?lang={$GLOBALS['lang']}&NID={$tblrow[$DataSetTmp->NID['name']]}&v=d&{$strpms}", false, "", array("A"))."</td>";
			}
		}
		echo "</tr>";
	}
	
	echo "</table>";
	
	//4.Draw navigator line
	echo '<div  class="page_nav_div" style="margin-top:20px; padding-bottom:20px;margin-left:20px;">';
	$nav1->Draw_Navigator_Line("v=t&".$strpms);
	echo "</div>";
}
?>
4)../cms/delrow.php



Displays delete message of an element, if user clicks yes it calls functions and triggers events that delete that row in the DB.


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
5) ../cms/secreg.php



Signs the user in (Authentication), makes cookies to remember user when login, creates a session that contains user's information including his privileges of the groups he is in, and privs of his own. These session data and privs are checked for every single operation in our project.


<?php
ob_start();
session_start();
require_once("../db/mysqlcon.php");
require_once("../common/pframe.php");
$myframe=new pframe();
$usr=$_REQUEST['txtusr'];
$pass=$_REQUEST['txtpass'];

/*IS REMEMBER ME? THEN LOGIN AUTOMATICALLY*/
if($_COOKIE['remember_user'])
{
	$usr=$_COOKIE['remember_user'];		echo "gooood";
	$hashed_pass=get_data_in("select user_password from user where user_name = '".$_COOKIE["remember_user"]."'","user_password");
}
else
{
	/*****************   SET COOKIES   *****************/
	if ($_POST['rememberme']) 
	{
		if(!setcookie("remember_user", $usr, time()+( 3 * 365 * 24 * 60 * 60), '/')){	echo "cookie user NO";}else{echo "cookie user YES";	}
	}print_r($_COOKIE) ;
}

if($_COOKIE['remember_user']) {$pass = $hashed_pass;} else{$pass = md5($pass);}

/*START SESSION*/
$GID=get_data_in("select user_id from user where user_name = '$usr' and user_password = '$pass'","user_id");

if (!$GID) { //username + password don’t match, go back to sign in page
	session_destroy();
	session_start();
	$_SESSION['failed_username']=$usr;
	$myframe->record_row_in_blockedips('failed_login'); // record a failed login attempt for this ip
	@header("location:../common/signin.php?lang=".$GLOBALS['lang']."&usr=$usr&soro=x");
}else {
//username + password match, create session variables
	session_register("GID");
	$_SESSION["GID"]=$GID;
	$_SESSION['uid']=$GID;
	$grow=row("select user_name,user_cat from user where user_id = '$GID'");
	$_SESSION["UNM"]=$grow['user_name'];
	$_SESSION["UCAT"]=$grow['user_cat'];
	/*****************   get user privs and groups privs   ******************/
	include_once '../common/privileges.php';
//fill all privs in one array & put it in a session variable
	$_SESSION["PRIVS"]= DB_All_Privileges($GID,true);
	print_r($_SESSION["PRIVS"]);echo "<br/><br/>";
	@header("location:../common/"); //redirect to home page having your privs in a session var
}
?>
6) ../cms/navigator.php



Creates a paginated table for the sql statement sent to it. Contains opening and closing functions.


<?php
require_once ('../lang/lang_'.$GLOBALS["lang"].'.inc');
class Navigator
{
var $result;
var $ful_result;
	
var $records_per_page=5;
var $cur_page=0;
var $page_num=0;

var $total_num_rows=0;
var $total_num_pages=0;

var $old_first=false;

function Navigator($sql, $cur_page=0, $records_per_page=5, $countsqlstr=""){

//if the query returns result rows
	if(@mysql_num_rows(mysql_query($sql)) != 0)
	{ 
		//full result
		$this->ful_result=mysql_query("$sql",$GLOBALS['con']);
		
		// fill Navigator Properties from parameters
		$this->records_per_page=$records_per_page;
		$this->cur_page=$cur_page;
		$this->page_num=$cur_page + 1;
	
		if ($countsqlstr==""){
			$this->total_num_rows=mysql_num_rows($this->ful_result);
		}else {
			$counttb=table($countsqlstr);
			if ($counttb){
				$this->total_num_rows=mysql_result($counttb,0,0);
			}else {
				$this->total_num_rows=mysql_num_rows($this->ful_result);
			}
		}
		
		$this->total_num_pages=ceil($this->total_num_rows/$records_per_page);
	
		$this->old_first=$old_first;
		
		//current page result
		$limit_str="LIMIT ". $cur_page * $records_per_page .", $records_per_page";
//do the query and fill the result local var
	 	$this->result=mysql_query("$sql $limit_str",$GLOBALS['con']);
	 	return true;
	}
}

//////////DEFAULT NAVIGATOR LINE///////////////////////////////////////////
function Draw_Navigator_Line($class="",$frm="",$pms=""){

//draw HTML elements containing the navigated table

$main_fields=url_pms(array("lang", "cur_page"));
echo '<div class="page_nav_div">';
echo('<div class="navbar">');
echo '<a id="txt_a">'.$this->total_num_rows.' '.rows_found.' / '.$this->total_num_pages.' '.Page.'</a>';
if($this->page_num > 1) {
	//all pages except page number 1
	$prev_page = $this->cur_page - 1;
	echo '<a frm="'.$frm.'" href="'.$_SERVER['PHP_SELF'].'?lang='.$GLOBALS['lang'].'&'.$main_fields.'&cur_page=0&'.$pms.'" class="'.$class.'" >1</a>';
	echo '<a frm="'.$frm.'" class="do_go '.$class.'" href="'.$_SERVER['PHP_SELF'].'?lang='.$GLOBALS['lang'].'&'.$main_fields.'&cur_page='.$prev_page.'&'.$pms.'"><img style="border-width:0px; margin-top:5px;" src="../images/go_'.align.'.gif" alt="..." /></a>';
}
//===================  paginating >>  =================
	$num_page_1=$this->page_num-2;$cur_page_1=$this->cur_page-2;
	if ($cur_page_1>=1) {echo '<a frm="'.$frm.'"  class="'.$class.'" href="'.$_SERVER['PHP_SELF'].'?lang='.$GLOBALS['lang'].'&'.$main_fields.'&cur_page='.$cur_page_1.'&'.$pms.'">'.$num_page_1.'</a>';}
	$num_page_2=$this->page_num-1;$cur_page_2=$this->cur_page-1;
	if ($cur_page_2>=1) {echo '<a frm="'.$frm.'"  class="'.$class.'" href="'.$_SERVER['PHP_SELF'].'?lang='.$GLOBALS['lang'].'&'.$main_fields.'&cur_page='.$cur_page_2.'&'.$pms.'">'.$num_page_2.'</a>';}
	echo '<a id="txt_a"><strong>'.$this->page_num.'</strong></a>';
	$num_page_4=$this->page_num+1;$cur_page_4=$this->cur_page+1;
	if ($cur_page_4<$this->total_num_pages-1) {echo '<a  frm="'.$frm.'" class="'.$class.'" href="'.$_SERVER['PHP_SELF'].'?lang='.$GLOBALS['lang'].'&'.$main_fields.'&cur_page='.$cur_page_4.'&'.$pms.'">'.$num_page_4.'</a>';}
	$num_page_5=$this->page_num+2;$cur_page_5=$this->cur_page+2;
	if ($cur_page_5<$this->total_num_pages-1) {echo '<a  frm="'.$frm.'" class="'.$class.'" href="'.$_SERVER['PHP_SELF'].'?lang='.$GLOBALS['lang'].'&'.$main_fields.'&cur_page='.$cur_page_5.'&'.$pms.'">'.$num_page_5.'</a>';}
//===================  paginating << =================
if($this->page_num < $this->total_num_pages) {
//we are not on the last page yet
	$next_page = $this->cur_page + 1;
	$last_page = $this->total_num_pages - 1;
//draw navigating buttons to the next and prev page
	echo '<a frm="'.$frm.'" class="do_go '.$class.'" href="'.$_SERVER['PHP_SELF'].'?lang='.$GLOBALS['lang'].'&'.$main_fields.'&cur_page='.$next_page.'&'.$pms.'"><img style="border-width:0px; margin-top:5px;" src="../images/go_'.r_align.'.gif" alt="..." /></a>';
	echo '<a frm="'.$frm.'" class="'.$class.'" href="'.$_SERVER['PHP_SELF'].'?lang='.$GLOBALS['lang'].'&'.$main_fields.'&cur_page='.$last_page.'&'.$pms.'">'.$this->total_num_pages.'</a>';
}
echo("</div>");
echo '</div>';
}
//////////////END OF NAVIGATOR LINE/////////////////////////////////
//////////WORDS NAVIGATOR LINE///////////////////////////////////////////
function Draw_Navigator_Line_W(){
$main_fields=url_pms(array("lang", "cur_page"));
echo('<div class="navbar">');
echo '<a id="txt_a">'.$this->total_num_rows.' '.rows_found.' / '.$this->total_num_pages.' '.Page.'</a>';
if($this->page_num > 1) {
	//all pages except page number 1
	$prev_page = $this->cur_page - 1;
	echo '<a href="?lang='.$GLOBALS['lang'].'&'.$main_fields.'&cur_page=0">'.First.'</a>';
	echo '<a class="do_go" href="?lang='.$GLOBALS['lang'].'&'.$main_fields.'&cur_page='.$prev_page.'">'.Previous.'</a>';
}
//===================  paginating >>  =================
	$num_page_1=$this->page_num-2;$cur_page_1=$this->cur_page-2;
	if ($cur_page_1>=1) {echo '<a href="?lang='.$GLOBALS['lang'].'&'.$main_fields.'&cur_page='.$cur_page_1.'">'.$num_page_1.'</a>';}
	$num_page_2=$this->page_num-1;$cur_page_2=$this->cur_page-1;
	if ($cur_page_2>=1) {echo '<a href="?lang='.$GLOBALS['lang'].'&'.$main_fields.'&cur_page='.$cur_page_2.'">'.$num_page_2.'</a>';}
	echo '<a id="txt_a"><strong>'.$this->page_num.'</strong></a>';
	$num_page_4=$this->page_num+1;$cur_page_4=$this->cur_page+1;
	if ($cur_page_4<$this->total_num_pages-1) {echo '<a href="?lang='.$GLOBALS['lang'].'&'.$main_fields.'&cur_page='.$cur_page_4.'">'.$num_page_4.'</a>';}
	$num_page_5=$this->page_num+2;$cur_page_5=$this->cur_page+2;
	if ($cur_page_5<$this->total_num_pages-1) {echo '<a href="?lang='.$GLOBALS['lang'].'&'.$main_fields.'&cur_page='.$cur_page_5.'">'.$num_page_5.'</a>';}
//===================  paginating << =================

if($this->page_num < $this->total_num_pages) {
	$next_page = $this->cur_page + 1;
	$last_page = $this->total_num_pages - 1;
//draw navigating buttons to the next and prev page as WORDS
	echo '<a class="do_go" href="?lang='.$GLOBALS['lang'].'&'.$main_fields.'&cur_page='.$next_page.'">'.Next.'</a>';
	echo '<a href="?lang='.$GLOBALS['lang'].'&'.$main_fields.'&cur_page='.$last_page.'">'.Last.'</a>';
}
echo("</div>");
}
//////////////END OF NAVIGATOR LINE/////////////////////////////////
}
?>
Second  ../common Folder


7) ../common/control_panel.php



Displays buttons that goes to each management page: groups, users, customers, categories, products, blocked IPs, buy contracts, maintenance contracts, FAQ, downloads, gallery albums, videos, advertisements, site configurations .. according tto the PRVILEGES the user has.


<?php
include_once '../common/pframe.php';
$myframe=new pframe();
$myframe->header("Control_Panel");

?>
<center>
<div id="control_panel">
	<p class="content_hd"><?=Control_Panel?></p>
	
/*****************************   all management buttons   *****************************/
       /*******************   Groups & Users Management buttons   *******************/	

	<div class="button_container">
		<? if(user_has_permission(array("A" ,"GROUPS_MAN"))){?> <a class="button color1" href="../pages/groups.php?lang=<?=$GLOBALS['lang']?>&v=t"><?=Groups_Management?></a> <? } ?>

		<? if(user_has_permission(array("A" ,"USERS_MAN"))){ ?> <a class="button color1" href="../pages/users.php?lang=<?=$GLOBALS['lang']?>&v=t"><?=Users_Management?></a> <? } ?>

		<? if(user_has_permission(array("A" ,"CUSTOMERS_MAN"))){?> <a class="button color1" href="../pages/users.php?lang=<?=$GLOBALS['lang']?>&v=t&ucat=customer"><?=Customer_Management?></a> <? } ?>
    /********** Categories, Products, Downloads and News Management buttons  ***********/
			
		<? if(user_has_permission(array("A" ,"CATEGORY_MAN"))){?> <a class="button color2" href="../pages/category.php?lang=<?=$GLOBALS['lang']?>&v=t"><?=Cats_Management?></a> <? } ?>

		<? if(user_has_permission(array("A" ,"PRODUCTS_MAN"))){?> <a class="button color2" href="../pages/products.php?lang=<?=$GLOBALS['lang']?>&v=t"><?=Prods_Management?></a> <? } ?>

		<? if(user_has_permission(array("A" ,"DOWNLOADS_MAN"))){?> <a class="button color2" href="../pages/download.php?lang=<?=$GLOBALS['lang']?>&v=t"><?=Downloads_Management?></a> <? } ?>

		<? if(user_has_permission(array("A" ,"NEWS_MAN"))){?> <a class="button color2" href="../pages/news.php?lang=<?=$GLOBALS['lang']?>&v=t"><?=News_Management?></a> <? } ?>

/************** Services, Jobs, FAQ, Contactus, Advertisements Management buttons **************/

		<? if(user_has_permission(array("A" ,"SERVICES_MAN"))){?> <a class="button color1" href="../pages/services.php?lang=<?=$GLOBALS['lang']?>&v=t"><?=Serices_Management?></a> <? } ?>

		<? if(user_has_permission(array("A" ,"JOBS_MAN"))){?> <a class="button color1" href="../pages/jobs.php?lang=<?=$GLOBALS['lang']?>&v=t"><?=Jobs_Management?></a> <? } ?>

		<? if(user_has_permission(array("A" ,"FAQ_MAN"))){?> <a class="button color1" href="../pages/faq.php?lang=<?=$GLOBALS['lang']?>&v=t"><?=FAQ_Management?></a> <? } ?>

		<? if(user_has_permission(array("A" ,"CONTACTUS_MAN"))){?> <a class="button color1" href="../pages/contactus.php?lang=<?=$GLOBALS['lang']?>&v=about"><?=Contactus_Management?></a> <? } ?>

		<? if(user_has_permission(array("A" ,"ADS_MAN"))){?> <a class="button color1" href="../pages/ads.php?lang=<?=$GLOBALS['lang']?>&v=t"><?=ADS_Management?></a> <? } ?>

/**************** Gallery albums, pictures and videos Management buttons ******************/

		<? if(user_has_permission(array("A" ,"ALBUMS_MAN","PICTURES_MAN", "VIDEOS_MAN"))){?> <a class="button color1" href="../pages/albums.php?lang=<?=$GLOBALS['lang']?>&v=t"><?=Albums_Pics_Videos_Management?></a> <? } ?>
		
/**************** Buy contracts & Maintenance contract Management buttons ***************/

		<? if(user_has_permission(array("A" ,"BCONTS_MAN"))){?> <a class="button color2" href="../pages/bconts.php?lang=<?=$GLOBALS['lang']?>&v=t"><?=BConts_Management?></a> <? } ?>
		<? if(user_has_permission(array("A" ,"MCONTS_MAN"))){?> <a class="button color2" href="../pages/mconts.php?lang=<?=$GLOBALS['lang']?>&v=t"><?=MConts_Management?></a> <? } ?>
		
/************** Site Configurations, Blocked IPs, Maillist Management buttons **************/
			
		<? if(user_has_permission(array("A" ,"CONFIGS_MAN"))){?> <a class="button color1" href="../pages/site_configs.php?lang=<?=$GLOBALS['lang']?>&v=t"><?=Configs_Management?></a> <? } ?>
		<? if(user_has_permission(array("A" ,"BLOCKED_IPS_MAN"))){?> <a class="button color1" href="../pages/blocked_ips.php?lang=<?=$GLOBALS['lang']?>&v=t"><?=Blocked_IPs_Management?></a> <? } ?>
		<? if(user_has_permission(array("A" ,"MAILLIST_MAN"))){?> <a class="button color1" href="../pages/maillist.php?lang=<?=$GLOBALS['lang']?>&v=t"><?=Maillist_Management?></a> <? } ?>
	</div>
	
</div>
</center>
<?php 

@$myframe->footer();
?>
8) ../common/index.php



Is the content of the home page. It includes latest products and latest news all brought from DB.


<?php 
include_once '../db/mysqlcon.php';
include_once '../common/pframe.php';

$myframe=new pframe();
$myframe->header();     //calling the header template

?><div id="content"><!------------------------ CONTENT >>>-------------------------------------------------------------------->

<? /*********************   Visits Counter   *******************/?>
<? $myframe->VisitsCounter();?>

<? /*********************   Slider   *******************/?>
<? $myframe->DisplaySlider();?> 

<p class="content_hd"><?=Latest_Products?></p>
    <div class="products">
        
    	<?php 

//bringing latest three products from db and display them in boxes with photos

			include_once "../obj/product.class.php";
			
			$myProd = new Product();
			$tbl=table("select * from {$myProd->tblname} where prod_exe <>'' order by NDate Desc limit 3");
			$i=0;
			$float_lft="";

			while ($row=mysql_fetch_array($tbl))
			{
			 $myProd->FillIn($row);
		     ?>
			 <div class="prod_blk <?echo $float_lft;?>">
				<div class="prod_title"><?=$myProd->{'prod_title_'.$GLOBALS['lang']}['value']?></div>
				<div class="prod_img">
                <?php 
              		$src = "../documents/thumbs/BPRO_{$myProd->prod_id['value']}.{$myProd->prod_pic['ext']}";

// photo path >> display the photo

                    if(!file_exists($src)){$src = "../images/test0.png";}
                 ?>
                <img src="<?=$src?>" /></div>
				<p class="prod_desc"><?=$myProd->{'prod_desc_'.$GLOBALS['lang']}['value']?></p>
				<a class="prod_more" href="../pages/products.php?lang=<?=$GLOBALS['lang']?>&v=c&NID=<?=$myProd->NID['value']?>" target="_blank"><?=more?></a>
			 </div>
			
		     <?php	$i++;
			}?>
        <div >  <a class="prod_more" href="../pages/products.php?lang=<?=$GLOBALS['lang']?>&v=t" target="_blank"><?=All_Products?></a> </div>
    </div>
    <?php //////////////////////////////////////////////////////////////////////////////////////////////////////////////?>
    <div class="latest_news">
        <p class="content_hd"><?=Latest_News?></p>
   	    <?php
			include_once "../obj/news.class.php";
			
			$myNews = new News();
			$tbl=table("select * from {$myNews->tblname} order by NDate Desc limit 3");
			$i=0;
			?><div class="content"><? 

//bringing latest three news from db and display them in boxes with photos

			while ($row=mysql_fetch_array($tbl))
			{
			 $myNews->FillIn($row);
				?>
				<div class="news_blk blk_<?=$i+1?>" style="<? if($i>1){echo "margin-left:0px";} ?>">
					<div class="news_title"><?=$myNews->{'news_title_'.$GLOBALS['lang']}['value']?></div>
					<div class="news_img">
	                <?php 
	                    $src = "../documents/thumbs/SNEWS_{$myNews->news_id['value']}.{$myNews->news_pic['ext']}";
	                    if(!file_exists($src)){$src = "../images/test1.png";}
	                 ?>
	                <img src="<?=$src?>" /></div>
					<div class="news_desc"><?=$myNews->{'news_desc_'.$GLOBALS['lang']}['value']?></div>
					<div >  <a class="news_more" href="../pages/news.php?lang=<?=$GLOBALS['lang']?>&v=c&NID=<?=$myNews->NID['value']?>" target="_blank"><?=more?></a> </div>
				 </div>
		     <?php $i++;
			}?>
			</div><!-- content -->
        <div >  <a class="news_more" href="../pages/news.php?lang=<?=$GLOBALS['lang']?>&v=t" target="_blank"><?=All_News?></a> </div>
    </div>
<?php

$myframe->footer();   //calling the footer template
 
9) ../common/main_ar.css + ../common/main_en.css



Main_<X>.css is the css file that contains all css style classes and IDs with attributes and values…
it changes to main_en when the language change.


/*MAIN*/
html,body{margin:0;padding:0;direction:rtl;text-align:center;font-family:Tahoma;font-size:12pt;color:#233D53;}
div,form,p,img{margin:0px;padding:0}
img{/*border:1px #ccc solid;*/}
a,a:active,a:visited{color:#000046 ;text-decoration:none;outline:0px;font-size:12pt;}
a:hover{text-decoration:none;;}
input[type="radio"] {border:0px;}
input[type="check"] {}
input[type="search"] {border:1px #ccc solid;margin:10px;line-height:30px;height:30px;font-size:14pt;color:#0072C6}
input[type="text"] {font-size:14pt;height:25px;}
#FRM{display:inline-block;}
.form_table{width:100%;font-family:Tahoma; margin-right:20%;}
.form_table div{font-size:12pt;}
.form_table input{font-size:12pt;;padding:3px 6px}
.form_table textarea{font-size:11pt;line-height:22px;padding:3px 8px;height:100px} 
.form_table select{font-size:11pt;line-height:22px;height:33px;font-weight:bold;}

body{width:100%;border:0px gray dotted;margin:auto;margin-top:0px;background-image:url('');background-repeat:;color:#000046;}

/**** HEADER ****/
#header{}
#header_bar{ white-space: nowrap; margin-right:5;overflow:hidden;background-color:#0072C6 /*DC2229*/;width:100%;position:;height:50px;padding:0 0px;clear:both;display:block;/*margin-bottom:41px;*/z-index:1000;position:fixed;;top:0px;border-bottom:1px #0072C6 solid;}
#header_bar .button{ white-space: nowrap; color:#ccc;font-size:14pt;font-family:;line-height:50px;padding:0px 28px;float:right;border:1px #0072C6 solid;border-left:1px #3c8bc4 solid; }
#header_bar .button:hover{color:#fff; background-color:#005798/*#c11000*/}
#header_bar .button_loggedin{ white-space: nowrap; color:#fff;font-size:14pt;font-family:;line-height:50px;padding:0px 22px;float:right;margin:auto;border:0px #0072C6 solid;border-left:1px #3c8bc4 solid;text-shadow: 2px 2px #000;}
#header_bar .button_loggedin:hover{background-color:#005798;text-shadow: 2px 2px #000;}
#header_bar .padding_before_home_button{width:36px;display:inline-block;float:right;	}
#header_bar .hide_sidebar{float:right;padding:17px 10px;position:absolute;top:0;right:00px;border-left:1px #aaa solid;}
#header_bar .hide_sidebar:hover{ background-color:#005798;}
#header_bar  #mypanel{position:fixed;top:52px;right:10px;border:1px #000 solid;width:200px;height:390px;background-color:#fff;}
#header_bar  #mypanel a,#header_bar  #mypanel a:active,#header_bar  #mypanel a:visited{display:block;line-height:40px;border:1px #fff solid;}
#header_bar  #mypanel a:hover{border:1px #999 solid;background-color:#efefef;font-weight:bold;}
#header_bar  #mypanel img{padding:0px;margin:0px}
#hider{}
.hidden{height:0px;}
.shown{height:320px;	transition: 0.2s;    -webkit-transition: 0.2s;    -moz-transition: 0.0s;    -o-transition: 0.0s;    -khtml-transition: 0.0s;    -ms-transition: 0.0s; padding:5px;font-size:12pt; }

#header_bar .user_loggedin{}
#header_bar .user_profile{height:50px;float:left;padding:0px 2px;padding-right:10px}
#header_bar .user_profile .unm{float:right;height:41px;}
#header_bar .user_profile img{height:50px;float:right;}
#header_bar .logo img{height:49px;float:left;border:0px #ccc solid}
#header_bar .profile_image{height:50px;margin-right:10px;}
#header_bar .is_current_page{border:0px #000 solid;background-color:#fff;color:#0072C6;font-weight:bold;;text-shadow: 2px 2px #ddd;}
#header_bar .is_current_page:hover{color:#fff;}

/**** Side Bar ****/

#content_sidebar{background-color:#ccc;width:208px;position:fixed;top:41px;bottom:0px;;right:0px;font-size:14pt;}
#content_sidebar #signin{width:140px;line-height:30px;border:2px #999 solid;position:absolute;bottom:0;margin:5px 10px;width:180px;padding:10px 0px;height:30px;font-size:12pt;}
#content_sidebar #signin:hover{border:2px #0072C6 solid;}
.side_bar{float:right;width:200px;margin:13px 3px;;background-color:;border-left:0px solid #ccc;}
.side_bar .txtmaillist{width:90%;margin-bottom:10px;}
.side_bar input[type="button"]{width:90%;}
.side_bar ul  li{text-align:right;padding:2px 0px;}
.side_bar_hd{border:2px #fff solid;border-radius:10px;height:30px;background-color:#0072C6;color:#fff;vertical-align:middle;line-height:30px;font-weight:;font}
.side_bar_blk{padding:5px 0px;}
.side_bar .button{display:block;border:2px #ccc solid;border-bottom:1px #aaa solid;padding:9px 0px;margin-bottom:0px;font-weight:bold;color:#000;text-shadow: 2px 2px #ccc;}
.side_bar .button:hover{background-color:#0072C6;border:2px #004F8B solid;color:#fff;text-shadow: 0px 0px #222;}
/**Overwritten in block style**/.side_bar .is_current_page{border:2px #000 solid;background-color:#fff;font-weight:bold;color:;font-size:13pt;text-shadow:0px 0px #ccc;}
.side_bar .is_current_page:hover{ }

/*****   mail list   *****/
.side_bar #mail_list{}
.side_bar .txtmaillist{color:#999;display:inline-block;width:65%;font-size:10pt;height:20px;}
.side_bar input[type="submit"]{width:25%;height:26px;border-radius:5px;background-color:#0072c6;color:white;border:2px #0072c6 solid;}
.side_bar input[type="submit"]:hover{color:#fff;border:2px #fff solid;background-color:#0072c6;}

/**** CONTENT ****/
#main_content{overflow:hidden;position:absolute;right:211px;left:0;top:51px;padding:0px 0.5%;border:0px blue solid;}
#content{overflow:hidden;border:0px red solid;}

.content_hd{text-align:center;overflow:hidden;font-size:16pt;font-weight:bold;padding:5px 10px;margin:5px 5%;border:2px #000046 solid;border-radius:5px;background-color:#ccc;z-index:-999;box-shadow: 0px 0px 8px #666;}

/**** latest products ****/

.products{overflow:hidden;}
.products .prod_blk{overflow:hidden;width:47%;height:290px;float:right;border-radius:0px;margin:10px 1%;background-color:#F7F7F7;border:1px #d4d4d4 solid;min-width:100px;}
.products .prod_title{overflow:hidden;;font-size:12pt;font-weight:bold;width:;padding:8px 20px;}
.products .prod_img{width:70%;height:160px;margin:auto;margin-bottom:10px;}
.products .prod_img img{border:1px #d4d4d4 solid;border-radius:10px;width:100%;height:100%;box-shadow: 1px 1px 25px #000;border:0}
.products .prod_desc{overflow:hidden;padding:0px 7px;width:90%;white-space: pre-line;  white-space: -moz-pre-line;}
.products .prod_more,.products .prod_more:active,.products .prod_more:visited{border-radius:5px;background-color:#000046;
color:white;padding:8px 20px;clear:both;float:left;margin:5px 12px;;}
.products .prod_more:hover{background-color:#f63a35;color:white;text-decoration:none;cursor:pointer;}
.products .prod_sep{overflow:hidden;width:4%;height:275px;border:1px #000 solid;background-color:#ccc;float:right;}
.products .float_lft{float:left;}

/**** latest news ****/

.latest_news{margin:auto;overflow:hidden;width:;display:block;}
.latest_news .content{width:100%;margin:auto;border:0px #000 solid;padding:2% 0px;height:;padding: 0px 2%;}
.latest_news .news_blk{overflow:hidden;width:28%;height:250px;float:right;border:1px #000046 solid;border-radius:10px;margin:10px 0px;margin-left:4%;padding:15px 5px;background-color:#F7F7F7;}
.latest_news .news_title{overflow:hidden;;font-size:12pt;font-weight:bold;width:;height:20px;width:100%;}
.latest_news .news_img{margin:auto;padding:8px 0px;width:100%;height:120px;}
.latest_news .news_img img{border:1px #ccc solid;border-radius:10px;height:100%;box-shadow: 1px 1px 25px #000;}
.latest_news .news_desc{height:48px;overflow:hidden;white-space: pre-line;  white-space: -moz-pre-line;}
.latest_news .news_more,.latest_news .news_more:active,.latest_news .news_more:visited{border-radius:5px;background-color:#000046;color:white;padding:8px 25px;position:relative;left:20px;float:left;margin:15px 0px;clear:both;}
.latest_news .news_more:hover{background-color:#f63a35;color:white;text-decoration:none;cursor:pointer;}

.signin_div{border:0px #000 solid;position:absolute;bottom:0;margin:5px 0px;width:210px;padding:10px 0px;height:40px;font-size:12pt;}
.signin_div a, .signin_div a:active, .signin_div a:visited{margin:0px 18px;}
.signin_div a:hover{}
.signin_div img{width:50px;height:50px;}
#signin, #signin:active, #signin:visited{padding:0px;padding-right:30px;text-align:right;width:170px;display:block;height:50px;border:3px #fff solid;BORDER-RADIUS:5PX;}
#signin:hover{background-color:#fff;border:3px #000046 solid;}
#signout, #signout:active, #signout:visited{padding-right:30px;text-align:right;width:170px;display:block;height:50px;border:3px #fff solid;BORDER-RADIUS:5PX;}
#signout:hover{border:3px RED solid;}
#signup, #signup:ative, #signup:visited{}
#signup:hover{font-weight:bold;}

.withTree{float:right;padding-left:10px;}
.withTree a,.withTree a:active,.withTree a:visited{color:#005798;font-size:16pt;font-weight:bold;}
.withTree a:hover{}
/******   ADS   *****/
#ads_div{position:absolute;bottom:60px;width:208px;height:320px;border:0px #000 solid;}
.ads_bar_hd_a,.ads_bar_hd_a:ACTIVE,.ads_bar_hd_a:VISITED{height:30px;color:#fff;line-height:30px;vertical-align:middle;float:right;}
.ads_bar_hd_a .edt{float:right;margin:0px 25px}
.ads_bar_hd{margin:0px 5px;;border:2px #fff solid;border-radius:10px;height:30px;background-color:#0072C6;color:#fff;vertical-align:middle;line-height:30px;font-weight:;font}
#ads_img{width:200px;max-height:275px;margin-top:6px;border:2px #fff solid}
/****************   albums   ***************/
.albums{border:0px #888 solid;width:100%;}
.albums .albm{float:right;width:300px;margin-right:20px;}
.albums .card_table{float:right;border:0px #888 solid;}

.albums .item_pan{border:0px #888 solid;}
.albums .item_title{clear:right;border:0px #888 solid;}
.albums .card_sep{display:none;}
.albums .more_btn{display:none;}
.albums .card_auth{width:80%;border:0px #888 solid;}

/*******   CONTROL PANEL   *****/
#control_panel{width:700px;border:1px #ccc solid;border-radius:10px;padding:20px;margin:20px;top:20px;font-weight:bold;font-size:14pt;}
#control_panel .content_hd{margin:0;border:2px #000046 solid;}
#control_panel .button_container{width:600px;margin:auto;padding:10px 0px;}
#control_panel .button,#control_panel .button:active,#control_panel .button:visited{
	display:block;border:2px #225675 solid;border-radius:5px;padding:10px 30px;margin:10px 30px;color:#225675;}
#control_panel .button:hover{border:2px #f00 solid;color:#DC2229;background-image:url("../images/fancy_left.png");background-repeat:no-repeat;background-position:right;background-color:#c5e4f9}
#control_panel .color1{background-color:#fff}
#control_panel .color2{background-color:#eee}

/********   RELATED PAGES   ******/
#related_pages{width:50%;margin:auto;padding:50px;}
.related_pages_hd{text-align:center;overflow:hidden;font-size:16pt;font-weight:bold;padding:5px 10px;margin:5px 0px;border:2px #000046 solid;border-radius:5px;background-color:#ccc;box-shadow: 0px 0px 8px #666;}
#related_pages .edit_tool{margin:10px auto;font-size:14pt ;}
/****** global table *****/
.global_tbl{border:2px #ccc solid; border-radius:5px;width:;padding:2px;text-align:center;clear:both;margin:auto;box-shadow: 0px 0px 6px #ccc;}
.global_tbl tr{}
.global_tbl tr:hover{background-color:#b7e1ff;color:#004F8B;cursor:pointer;border:1px #0072C6 solid;}	
.global_tbl .tr_1{background-color:#fff;}
.global_tbl .tr_2{background-color:#ededed;}
.global_tbl th{background-color:#004F8B;height:27px;color:#fff; border-radius:5px;padding:2px 2px;}
.global_tbl td{border:0px #ccc solid;padding:2px 8px;max-width:150px;overflow:hidden;white-space:nowrap;}
.global_tbl .NDate{width:100px;padding:0px}
.global_tbl .admin_tools_td{width:70px;padding:0px}

.downloads_tbl th{background-color:#009124}
.downloads_tbl .tr_1{background-color:#fff;}
.downloads_tbl .tr_2{background-color:#d6ffdf;}

.download_td{}
.download_td img{width:32px;}
/********   License MAN   ********/
.license_td{background-color:#b9e0ea;font-size:14pt;width:100px}
.no_license{color:#c11000}
.licensed{color:green}

/******   Sortable tables   ******/
table.sortable thead {   background-color:#eee;    color:#666666;    font-weight: bold;    cursor: default;}
/********   user card   ********/
.user_card{border:1px #ccc solid;border-radius:10px;padding:20px;margin:auto;}
.user_card tr{}
.user_card td{border-bottom:1px #ccc solid;padding:10px;}
.user_card .card_img{border-bottom:0px;border:1px #ccc solid;padding:10px;}

.contactus_tbl{font-size:14pt;}
.contactus_tbl .img{width:50%;margin: 0px auto;border:1px #000 solid}
.contactus_tbl tr{padding:10px;}
.contactus_tbl td{padding:10px;}

.admin_tools{width:70px;float:left}
.admin_tools a, .admin_tools a:active, .admin_tools a:visited{}
.admin_tools a:hover{}
.admin_tools_img{width:32px;float:left;padding-left:13px;}
.admin_tools{}

.goto_bcont,.goto_bcont:active,.goto_bcont:visited{color:#87cdff;padding:3px 15px;font-weight:bold;background-color:#777;border:1px;}
.goto_bcont:hover{padding:3px 15px;font-weight:bold;;border:2px red solid;color:#fff	;}
.unseen_td{background-color:#ffe3c4} 
.tr_highlighted td{border:2px #960a00 solid;font-weight:;text-decoration:;}
/***********   Groups   ***********/
.groups{border:2px #ccc solid;border-radius:10px;padding:20px;margin:auto;width:400px;}
.li_group{border:2px #ccc solid;padding:10px 50px;width:;margin:5px;background-color:#bfe3fc}
.li_group:hover{background-color: #0176c4; color:#fff}
.ul_user{margin-top:15px;}
.ul_user:hover{}
.li_user{border:2px #ccc solid;padding:8px;width:220px;background-color:#fff;font-weight:normal; color:#fff;text-align:center;margin:5px;display:block;color:#005798}
.li_user:hover{border:2px #004f8b solid;background-color:#e5f4ff;}
.a_li_user{}
.a_li_user:hover{}
.groups .tools{height:35px;width:115px;border:1px #000 solid;background-color:#fff;margin-top:20px;padding:0px 10px;margin-right:180px;}

/***********   expandable lists (tree)   ***********/
.collapsibleList{padding:0px}
.collapsibleList li{  list-style-image:url('../images/button.png');  cursor:auto; list-style:none}

li.collapsibleListOpen{  list-style-image:url('../images/button-open.png');  cursor:pointer; font-weight:bold;background-color:#005798; color:#fff;}

li.collapsibleListClosed{  list-style-image:url('../images/button-closed.png');  cursor:pointer;}
/****************************************************/
.li_gr_privs{direction:ltr;width:83%;border:1px #ccc solid;padding:6px;padding-left:20px;text-align:left;}
.li_gr_privs a{display:none}

.all_privs{width:320px;border:2px #ccc solid;direction:ltr;display:inline-block;;border-radius:10px;margin-left:20px;padding-bottom:20px;}
.all_privs .hd{border:2px #ccc solid;text-align:center;line-height:50px;margin-bottom:11px;border-radius:5px 5px 0px 0px;background-color:#ccc;color:#000;}
.all_privs p{float:left;margin-left:20px;line-height:30px;}
.all_privs a,.all_privs a:VISITED,.all_privs a:ACTIVE{float:left;padding:0px 20px;line-height:30px;color:red;clear:right;}
.all_privs a:HOVER{font-weight:bold;;}
.this_group_privs{width:300px;border:2px #0176c4 solid;direction:ltr;display:inline-block;float:right;border-radius:10px;margin-left:20px;padding-bottom:20px;}
.this_group_privs .hd{border:2px #0176c4 solid;text-align:center;line-height:50px;margin-bottom:11px;border-radius:5px 5px 0px 0px;background-color:#0176c4;color:#fff;}
.this_group_privs p{float:left;margin-left:20px;line-height:30px;}
.this_group_privs a,.this_group_privs a:VISITED,.this_group_privs a:ACTIVE{float:left;padding:0px 20px;line-height:30px;color:red;clear:right;}
.this_group_privs a:HOVER{font-weight:bold;}

/********************   user management   *********************/
.group_by_div{}
.group_by_button{padding:10px 20px; border:2px #004F8B solid;float:right;border-radius:10px;margin:0px 10px;margin-bottom:6px;color:#004F8B;margin-bottom:20px}
.group_by_button:HOVER{border:2px #3fadfc solid;color:#3fadfc;}

/*******************   blocked ips   ************************/
.blocked_ip{color:#bf0000; font-weight:bold;}

/*******************   blocked ips   ************************/
.site_configs{}
.site_configs .slider{background-color:#009ed8;color:#fff}
.site_configs .social_media{background-color:#3A5795;color:#fff}
.site_configs .color_main,.site_configs .color_second,.site_configs .color_third{background-color:#75ce00;color:#fff}
.site_configs .fav_ico{background-color:#ceac00;color:#fff}
.site_configs .side_bar_button{background-color:#ba033a;color:#fff}
.site_configs .header_logo{background-color:#ff3f00;color:#fff}
.site_configs .value_color{color: #f00;    -webkit-filter: invert(100%);    filter: invert(100%); }
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////


.pan_title{margin-bottom:10px;}
.item_pan{overflow:hidden;}
.item_pan_img{float:right;border:1px #000046 solid;overflow:hidden;margin-left:14px;position:relative;}
.visits_pan{position:absolute;top:0px;left:0px;width:100%;background-image:url(../images/date_bg.png);font-size:8pt;text-align:center;display:none;}
.g_edit_tool,.g_edit_tool:visited,.g_edit_tool:active{margin:0px 0px; border:0px; color:#fff; font-weight:normal; text-decoration:none;line-height:18px;font-family:tahoma,GE Dinar Two, Arial;font-size:14pt;}
.g_edit_tool:hover{margin:0px 0px; border:0px; text-decoration:none;}

.item_title,.item_title a,.item_title a:active,.item_title a:visited{color:#000046;margin-bottom:5px;font-size:14pt;text-decoration:none;text-align:right;font-family:tahoma,GE Dinar Two, Arial;vertical-align: middle;cursor:pointer;}
.item_title a:hover{text-decoration:none;color:#0072C6;font-weight:bold;}
.item_txt{text-align:justify;font-size:14px;line-height:21px;}
.ajax_content{margin-top:25px;float:right;}
.more_btn{text-align:left;vertical-align:bottom;margin-top:10px;}
.more_btn a,.more_btn a:active, .more_btn a:visited{color:#949494;width:84px;background:url(../images/bg_more.png) no-repeat;display:inline-block;position:relative;text-decoration:none;}
.more_circle{position:absolute;top:11px;right:11px;}
.more_arrow{position:absolute;top:18px;right:19px;}
.more_txt{}
.data_cover{overflow:hidden;float:right;width:100%;}
.item_author a,.item_author a:active,.item_author a:visited,.item_author span{font-size:8pt;font-family:Tahoma;color:#a7bd41;}
.item_author a:hover{text-decoration:underline;}
.item_author_body{padding-top:30px;overflow:hidden;}
.auth_pic{float:right;margin-left:20px;margin-bottom:10px;}
a.auth_name{padding:0px;font-family:tahoma;font-size:14pt;color:#000;vertical-align:middle;text-align:right;padding-bottom:35px;}
a.auth_name:hover{text-decoration:none;}
.auth_sum{}

.al_div{padding-top:5px;margin:5px;}
.galery_table{}
.card_img_div_title{clear:both;text-align:center;}
.rmnu_flashAd{margin-top:5px;margin-bottom:5px;}

#pdf_icon{position: absolute; z-index: 100 ;left:328px;}
#rit_body_in{float: right;width: 199px;}
#lft_body_in{float: left;width: 737px;}
#in_title{color: #000046; font-size:20pt; font-family:tahoma,GE Dinar Two, Arial; }
#in_text{color: #000; font-size:12pt; font-family:tahoma,GE Dinar Two, Arial;margin:15px 0px; text-align: justify;}

/*PANEL*/
.panel{overflow:hidden;margin:0px 0px;padding: 10px;}
.panel_title{padding:0px;margin-right:0px;margin-top:0px;background-image:url(../images/bdy_ticket_ar.png);background-repeat:no-repeat;margin-bottom:10px;}
.panel_title_icon{width:18px;height:13px;padding:7px 0px;margin-left:14px;float:right;}
.panel_title_text{font-family:tahoma,GE Dinar Two, Arial;font-size:24px;color:#000046;vertical-align:middle;text-align:right;box-shadow: 0px 0px 8px #666;direction:rtl}
.panel_title_text{overflow:hidden;font-size:16pt;font-weight:bold;padding:5px 10px;padding-right:20px;border:2px #000046 solid;border-radius:5px;background-color:#ccc;}
.panel_title_text img{line-height:32px;vertical-align:middle;}
.panel_title_text a,.panel_title_text a:active,.panel_title_text a:visited{color:;text-decoration:none;font-weight:normal;}
.panel_title_text a:hover{text-decoration:underline;}
.panel_bdy{overflow:hidden;margin:0px;text-align:right;}
.panel_bdy p{}

/*CARDS*/
.card_table{border-collapse:collapse;width:100%;}
.card_title{margin-bottom:7px;color:#fff;font-weight:bold;}
.card_title a,.card_title a:visited,.card_title a:active{text-decoration:none;font-weight:bold;margin:0px;padding:0px;font-size:9pt;padding-right:7px;}
.card_title a:hover{text-decoration:underline;font-weight:bold;margin:0px;padding:0px;padding-right:7px;}
.card_sum{text-align:justify;padding-right:0px;line-height:16px;padding-top:3px;padding-right:7px;}
.home_boxes .card_sum{font-family:Tahoma;font-weight:normal;font-size:8pt;}
.card_links{text-align:left;margin:10px 5px;font-weight:bold;padding:5px;}
.card_links a{text-decoration:none;color:#a6a6a6;}
.card_links a:hover{color:#c31516;}
.card_links img{border-width:0px;padding-left:5px;}
.card_sep{margin-top:10px;line-height:5px;}
.card_auth{text-align:left;font-size:7pt;color:#888;box-shadow: 0px 3px 5px #ddd;border-bottom:1px #aeaeae dashed;padding-bottom:5px;margin-bottom:10px;}
.card_img{border:0px #d8d8d1 solid;margin-left:0px;}
.more_link{text-decoration:none;}
.more_link:hover{text-decoration:underline;}

.card_img_mask{overflow:hidden;border:4px #d8d8d8 solid;margin-top:6px;margin-left:5px;}
.card_img_div{ }
.card_pa .card_img{border-width:0px;}
.nimags_card_img{border-width:0px;padding:10px;}
.nimgs{border:1px #f2e9cc solid;overflow:hidden;background-color:whitesmoke;margin-bottom:12px;padding:12px;}

/*LIST VIEW*/
.lv{}
.lv td{vertical-align:top;padding:5px 2px;line-height:21px;}
.lv_cap{width:125px;font-family:Tahoma; font-size:8pt; color:#689341; font-weight:bold; padding:5px;text-align:left;line-height:21px;vertical-align:top;}

/*Forms Styles*/
input{border:1px #000046 solid; font-family:Tahoma; font-size:8pt; padding:3px;width:260px;height:20px;behavior: url(../common/border-radius.htc)\9;-moz-border-radius: 10px; -webkit-border-radius: 10px;-khtml-border-radius: 10px; border-radius: 10px;}
input[type=checkbox]{width:10px;vertical-align:middle;}
textarea{border:1px #000046 solid; font-family:Tahoma; font-size:8pt; padding:3px;width:260px;behavior: url(../common/border-radius.htc)\9;-moz-border-radius: 10px; -webkit-border-radius: 10px;-khtml-border-radius: 10px; border-radius: 10px;}
.long_hkl_name{font-family:Tahoma; font-size:8pt; color:#d69831; font-weight:bold; padding:3px;}
.jfld_n{overflow:hidden;}
.field_label{font-family:Tahoma; font-size:8pt; color:#002060; font-weight:bold; padding:5px;}
.field_label_n{float:right;clear:right; width:150px;font-family:Tahoma; font-size:9pt; color:#292929; font-weight:bold; padding:7px;line-height:22px;text-align:right;vertical-align:middle;}
.field_note_n{float:right;margin-right:5px;overflow:hidden;line-height:22px;vertical-align:middle;margin-top:8px;color:#ff0000;font-size:9pt;}
.req_astric{color:#ff0000;}
.field_box{float:right;width:80%;clear:right;}
.txtfld_n{margin:5px;clear:left;float:right;overflow:hidden;}
.requied_field{color:red;font-size:16pt;float:left;padding-top:10px;z-index:999}

.form_table{ }
.chk{margin:0px; padding:0px; border-width: 0px;}field_label
select{behavior: url(../common/border-radius.htc)\9;-moz-border-radius: 10px; -webkit-border-radius: 10px;-khtml-border-radius: 10px; border-radius: 10px;width:268px; border:1px #000046 solid; font-family:Tahoma; font-size:9pt; height:27px;#margin-right:4px;padding:3px;}
.edit_tool_ico, .edit_tool_ico:active, .edit_tool_ico:visited{border:0px #efefef solid;}
.edit_tool_ico:hover{border:0px #000 solid;}
.edit_tool_ico img{border:2px #efefef solid;margin-bottom:-8px;padding:2px;}
.edit_tool_ico img:hover{border:2px #0072c6 solid}
.edit_tool,.edit_tool:visited,.edit_tool:active{border:2px #ccc solid;padding:10px 50px;width:250px;display:block;margin:5px;}
.edit_tool:hover{border:2px #aaa solid; text-decoration:none;background-color: #efefef}
.edit_tool:hover span{}
.edit_tool img{margin-left:30px;vertical-align:middle;}
.ttxtg{vertical-align:middle;}
#doit{behavior: url(../common/border-radius.htc)\9;-moz-border-radius: 10px; -webkit-border-radius: 10px;-khtml-border-radius: 10px; border-radius: 10px;border:1px #fff solid; color:#fff; font-size:9pt; width:99px; margin:10px; cursor:pointer; background-color:#000046; text-align:center;#height:25px;font-weight:bold;height:30px;}
.del_form{padding:10px 20px;}
.datecalimg_n{position:absolute;border-width:0px;margin-top:-20px;margin-right:-23px;}
#txtDate{text-align:left; direction:ltr;}
.IVComments{height:450px;width:450px;}
.comment_editor{background-color:#fafcf5;text-align:left;}
#vrimg{border:1px #c7c7c7 solid;margin:5px auto;}
.VerfLabel{font-family:Arial;font-size:10pt;text-align:justify;margin-top:20px;}
.plz_fill{margin-bottom:25px;font-family:GE Dinar Two, Arial;margin-right:10px;}
.verf_tbl{margin:auto;}

/*Pages Navigator*/
.page_nav_div{margin-bottom:20px;clear:both;}
.navbar{padding:5px 0px;text-align:left;}
.navbar a, .navbar a:visited{background-color:#fff;color:#0072c6; text-align:left; display:block; display:inline-block; border:1px #0072c6 solid;	text-decoration:none;padding-bottom:3px; margin:2px; height:15px; line-height:16px; vertical-align:middle;font-size:14pt;padding:5px 10px}
.navbar a:hover{background-color:#0072c6; color:#fff; border:1px #000046 solid;}
#do_go:hover{color:#000046; background-color:#ffffff; border:1px #000046 solid;}
#txt_a:hover{color:#000046; background-color:#ffffff; border:1px #000046 solid;}

/*Err Msg*/
.err_msg{text-align:right; color:#d17002 ;border:2px #ff8a00 solid; background-color: #ffebd6;margin:10px 20px;padding:10px 20px;font-weight:bold;height:19px;line-height:19px;vertical-align:middle;}
.err_msg_icon{float:left;margin:0px 7px;}

/*OTHETRS*/
.ubaloon{margin-left:75px; margin-top:15px;position:absolute; background-color:#859451; color:#FFFFFF; padding:5px; border:1px gray solid;z-index:150;}
.ulink_link{color:#4d4e53;}
.ulink_link:hover{text-decoration:underline; color:#01763a;}

/*DialogBox*/
.page_lid{position:absolute;top:0;left:0;width:100%;overflow:visible;height:1000px;background-color:black;z-index:101;text-align:center;opacity:0.5;filter:alpha(opacity=50);}
.msgbox{text-align:center;overflow:hidden;width:100%;left:0px;top:230px;position:absolute;z-index:110;} 
.msgbox .msgbox_container{width:465px;margin:auto;}
.msgbox .msgbox_title{border:1px #3b5998 solid;padding:5px;background-color:#6d84b4;overflow:hidden;}
.msgbox .msgbox_title_icon{float:right;}
.msgbox .msgbox_title_text{float:right;padding-right:6px;color:white;font-weight:bold;}
.msgbox .msgbox_body{background-color:white;border:1px #555555 solid;border-width:0px 1px;padding:10px;text-align:right;}
.msgbox .msgbox_btns{overflow:hidden;background-color:#f2f2f2;border:1px #555555 solid;border-width:0px 1px;border-bottom-width:1px;}
.msgbox .msgbox_line_sep{line-height:1px;height:1px;border-top:1px #cccccc solid;margin:0px;padding:0px;}
.msgbox .msgbox_btns_container{float:left;margin:0px;padding:0px;padding:7px 15px;#padding-top:4px;}
.msgbox .okbtn {background-color:#3B5998;border-color:#D9DFEA #0E1F5B #0E1F5B #D9DFEA;border-style:solid;border-width:1px;color:#FFFFFF;font-family:"lucida grande",tahoma,verdana,arial,sans-serif;font-size:11px;padding:2px 15px 3px;text-align:center;cursor:pointer;}
.msgbox .cancelbtn {border-style:solid;border-width:1px;color:#FFFFFF;font-family:"lucida grande",tahoma,verdana,arial,sans-serif;font-size:11px;padding:2px 15px 3px;text-align:center;background:#F0F0F0 none repeat scroll 0 0;border-color:#E7E7E7 #666666 #666666 #E7E7E7;color:#000000;cursor:pointer;}

/*AutoComplete*/
.ac_results {padding: 0px;border: 1px solid black;background-color: white;overflow: hidden;z-index: 99999;direction:ltr;}
.ac_results ul {width: 100%;list-style-position: outside;list-style: none;padding: 0;margin: 0;direction:ltr;text-align:right;}
.ac_results li {margin: 0px;padding: 2px 5px;cursor: default;display: block;font: menu;font-size: 12px;line-height: 16px;overflow: hidden;}
.ac_loading {background: white url('indicator.gif') right center no-repeat;}
.ac_odd {background-color: #eee;}
.ac_over {background-color: #0A246A;color: white;}

/*AutoComplete*/
.ac_results {padding: 0px;border: 1px solid black;background-color: white;overflow: hidden;z-index: 99999;}
.ac_results ul {width: 100%;list-style-position: outside;list-style: none;padding: 0;margin: 0;direction:ltr;text-align:right;}
.ac_results li {margin: 0px;padding: 2px 5px;cursor: default;display: block;font: menu;font-size: 12px;line-height: 16px;overflow: hidden;}
.ac_loading {background: white url('indicator.gif') right center no-repeat;}
.ac_odd {background-color: #eee;}
.ac_over {background-color: #0A246A;color: white;}

/*FANCY BOX*/
#fancybox-loading {	position: fixed;top: 50%;left: 50%;height: 40px;width: 40px;margin-top: -20px;margin-left: -20px;cursor: pointer;overflow: hidden;z-index: 1104;display: none;}
* html #fancybox-loading {	/* IE6 */ position: absolute;margin-top: 0;}
#fancybox-loading div {position: absolute;top: 0;left: 0;width: 40px;height: 480px;background-image: url('../images/fancybox.png');}
#fancybox-overlay {position: fixed;top: 0;left: 0;bottom: 0;right: 0;background: #000;z-index: 1100;display: none;}
* html #fancybox-overlay {	/* IE6 */ position: absolute;width: 100%;}
#fancybox-tmp {padding: 0;margin: 0;border: 0;overflow: auto;display: none;}
#fancybox-wrap {position: absolute;top: 0;left: 0;margin: 0;padding: 20px;z-index: 1101;display: none;}
#fancybox-outer {position: relative;width: 100%;height: 100%;background: #FFF;}
#fancybox-inner {position: absolute;top: 0;left: 0;width: 1px;height: 1px;padding: 0;margin: 0;outline: none;overflow: hidden;}
#fancybox-hide-sel-frame {position: absolute;top: 0;left: 0;width: 100%;height: 100%;background: transparent;}
#fancybox-close {position: absolute;top: -15px;right: -15px;width: 30px;height: 30px;background-image: url('../images/fancybox.png');background-position: -40px 0px;cursor: pointer;z-index: 1103;display: none;}
#fancybox_error {color: #444;font: normal 12px/20px Arial;padding: 7px;margin: 0;}
#fancybox-content {height: auto;width: auto;padding: 0;margin: 0;}
#fancybox-img {width: 100%;height: 100%;padding: 0;margin: 0;border: none;outline: none;line-height: 0;vertical-align: top;	-ms-interpolation-mode: bicubic;}
#fancybox-frame {position: relative;width: 100%;height: 100%;border: none;display: block;}
#fancybox-title {position: absolute;bottom: 0;left: 0;font-size: 12px;z-index: 1102;text-align: right;}
.fancybox-title-inside {padding: 10px 0;text-align: center;color: #333;text-align: right;}
.fancybox-title-outside {padding-top: 5px;color: #FFF;text-align: center;font-weight: bold;}
.fancybox-title-over {color: #FFF;text-align: left;}
#fancybox-title-over {padding: 10px;background-image: url('../images/fancy_title_over.png');display: block;}
#fancybox-title-wrap {display: inline-block;}
#fancybox-title-wrap span {height: 32px;float: left;}
#fancybox-title-left {padding-left: 15px;background-image: url('../images/fancybox.png');background-position: -40px -90px;background-repeat: no-repeat;}
#fancybox-title-main {font-weight: bold;line-height: 29px;background-image: url('../images/fancybox-x.png');background-position: 0px -40px;color: #FFF;}
#fancybox-title-right {padding-left: 15px;background-image: url('../images/fancybox.png');background-position: -55px -90px;background-repeat: no-repeat;}
#fancybox-left, #fancybox-right {position: absolute;bottom: 0px;height: 100%;width: 35%;cursor: pointer;outline: none;background-image: url('../images/blank.gif');z-index: 1102;display: none;}
#fancybox-left {left: 0px;}
#fancybox-right {right: 0px;}
#fancybox-left-ico, #fancybox-right-ico {position: absolute;top: 50%;left: -9999px;width: 30px;height: 30px;margin-top: -15px;cursor: pointer;z-index: 1102;display: block;}
#fancybox-left-ico {background-image: url('../images/fancybox.png');background-position: -40px -30px;}
#fancybox-right-ico {background-image: url('../images/fancybox.png');background-position: -40px -60px;}
#fancybox-left:hover, #fancybox-right:hover {visibility: visible;    /* IE6 */}
#fancybox-left:hover span {left: 20px;right: auto;}
#fancybox-right:hover span {left: auto;right: 20px;}
.fancy-bg {position: absolute;padding: 0;margin: 0;border: 0;width: 20px;height: 20px;z-index: 1001;}
#fancy-bg-n {top: -20px;left: 0;width: 100%;background-image: url('../images/fancybox-x.png');}
#fancy-bg-ne {top: -20px;right: -20px;background-image: url('../images/fancybox.png');background-position: -40px -162px;}
#fancy-bg-e {top: 0;right: -20px;height: 100%;background-image: url('../images/fancybox-y.png');background-position: -20px 0px;}
#fancy-bg-se {bottom: -20px;right: -20px;background-image: url('../images/fancybox.png');background-position: -40px -182px;}
#fancy-bg-s {bottom: -20px;left: 0;width: 100%;background-image: url('../images/fancybox-x.png');background-position: 0px -20px;}
#fancy-bg-sw {bottom: -20px;left: -20px;background-image: url('../images/fancybox.png');background-position: -40px -142px;}
#fancy-bg-w {top: 0;left: -20px;height: 100%;background-image: url('../images/fancybox-y.png');}
#fancy-bg-nw {top: -20px;left: -20px;background-image: url('../images/fancybox.png');background-position: -40px -122px;}

/* IE */

#fancybox-loading.fancybox-ie div	{ background: transparent; filter: progid:DXImageTransform.Microsoft.AlphaImageLoader(src='../images/fancy_loading.png', sizingMethod='scale'); }
.fancybox-ie #fancybox-close		{ background: transparent; filter: progid:DXImageTransform.Microsoft.AlphaImageLoader(src='../images/fancy_close.png', sizingMethod='scale'); }

.fancybox-ie #fancybox-title-over	{ background: transparent; filter: progid:DXImageTransform.Microsoft.AlphaImageLoader(src='../images/fancy_title_over.png', sizingMethod='scale'); zoom: 1; }
.fancybox-ie #fancybox-title-left	{ background: transparent; filter: progid:DXImageTransform.Microsoft.AlphaImageLoader(src='../images/fancy_title_left.png', sizingMethod='scale'); }
.fancybox-ie #fancybox-title-main	{ background: transparent; filter: progid:DXImageTransform.Microsoft.AlphaImageLoader(src='../images/fancy_title_main.png', sizingMethod='scale'); }
.fancybox-ie #fancybox-title-right	{ background: transparent; filter: progid:DXImageTransform.Microsoft.AlphaImageLoader(src='../images/fancy_title_right.png', sizingMethod='scale'); }

.fancybox-ie #fancybox-left-ico		{ background: transparent; filter: progid:DXImageTransform.Microsoft.AlphaImageLoader(src='../images/fancy_nav_left.png', sizingMethod='scale'); }
.fancybox-ie #fancybox-right-ico	{ background: transparent; filter: progid:DXImageTransform.Microsoft.AlphaImageLoader(src='../images/fancy_nav_right.png', sizingMethod='scale'); }

.fancybox-ie .fancy-bg { background: transparent !important; }

.fancybox-ie #fancy-bg-n	{ filter: progid:DXImageTransform.Microsoft.AlphaImageLoader(src='../images/fancy_shadow_n.png', sizingMethod='scale'); }
.fancybox-ie #fancy-bg-ne	{ filter: progid:DXImageTransform.Microsoft.AlphaImageLoader(src='../images/fancy_shadow_ne.png', sizingMethod='scale'); }
.fancybox-ie #fancy-bg-e	{ filter: progid:DXImageTransform.Microsoft.AlphaImageLoader(src='../images/fancy_shadow_e.png', sizingMethod='scale'); }
.fancybox-ie #fancy-bg-se	{ filter: progid:DXImageTransform.Microsoft.AlphaImageLoader(src='../images/fancy_shadow_se.png', sizingMethod='scale'); }
.fancybox-ie #fancy-bg-s	{ filter: progid:DXImageTransform.Microsoft.AlphaImageLoader(src='../images/fancy_shadow_s.png', sizingMethod='scale'); }
.fancybox-ie #fancy-bg-sw	{ filter: progid:DXImageTransform.Microsoft.AlphaImageLoader(src='../images/fancy_shadow_sw.png', sizingMethod='scale'); }
.fancybox-ie #fancy-bg-w	{ filter: progid:DXImageTransform.Microsoft.AlphaImageLoader(src='../images/fancy_shadow_w.png', sizingMethod='scale'); }
.fancybox-ie #fancy-bg-nw	{ filter: progid:DXImageTransform.Microsoft.AlphaImageLoader(src='../images/fancy_shadow_nw.png', sizingMethod='scale'); }


/* TextboxList sample CSS */
ul.holder {width:260px !important; margin: 0; border: 1px solid #000046; overflow: hidden; height: auto !important; height: 1%; padding: 4px 5px 0; behavior: url(../common/border-radius.htc)\9;-moz-border-radius: 10px; -webkit-border-radius: 10px;-khtml-border-radius: 10px; border-radius: 10px;}
*:first-child+html ul.holder { padding-bottom: 2px; } * html ul.holder { padding-bottom: 2px; } /* ie7 and below */
ul.holder li { float: right; list-style-type: none; margin: 0 5px 4px 0; white-space:nowrap;}
ul.holder li.bit-box, ul.holder li.bit-input input { font: 11px "Lucida Grande", "Verdana"; }
ul.holder li.bit-box { -moz-border-radius: 6px; -webkit-border-radius: 6px; border-radius: 6px; border: 1px solid #CAD8F3; background: #DEE7F8; padding: 1px 5px 2px; }
ul.holder li.bit-box-focus { border-color: #598BEC; background: #598BEC; color: #fff; }
ul.holder li.bit-input input { width: auto; overflow:visible; margin: 0; border: 0px; outline: 0; padding: 3px 0px 2px; } /* no left/right padding here please */
ul.holder li.bit-input input.smallinput { width: 20px; }
ul.holder input{height:15px;}

/* Facebook demo CSS */      
#add { border: 1px solid #c7c7c7; width: 350px; margin: 50px; padding: 20px 30px 10px; }
form ol li { list-style-type: none; }
form ol { font: 11px "Lucida Grande", "Verdana"; margin: 0; padding: 0; }
form ol li.input-text { margin-bottom: 10px; list-style-type: none; padding-bottom: 10px; }
form ol li.input-text label { font-weight: bold; cursor: pointer; display: block; font-size: 13px; margin-bottom: 10px; }
form ol li.input-text input { width: 300px; padding: 5px 5px 6px; font: 11px "Lucida Grande", "Verdana"; border: 1px solid #c7c7c7; }
form ul.holder { width: 300px; }
form ul { margin: 0 !important }
ul.holder li.bit-box, #apple-list ul.holder li.bit-box { padding-left: 15px; position: relative; z-index:1000;}
#apple-list ul.holder li.bit-input { margin: 0; }
#apple-list ul.holder li.bit-input input.smallinput { width: 5px; }
ul.holder li.bit-hover { background: #BBCEF1; border: 1px solid #6D95E0; }
ul.holder li.bit-box-focus { border-color: #598BEC; background: #598BEC; color: #fff; }
ul.holder li.bit-box a.closebutton { position: absolute; left: 4px; top: 5px; display: block; width: 7px; height: 7px; font-size: 1px; background: url('../images/close.gif'); }
ul.holder li.bit-box a.closebutton:hover { background-position: 7px; }
ul.holder li.bit-box-focus a.closebutton, ul.holder li.bit-box-focus a.closebutton:hover { background-position: bottom; }

/* Autocompleter */

.facebook-auto { display: none; position: absolute; width: 260px; background: #eee;margin-right:5px; }
.facebook-auto .default {  padding: 5px 7px; border: 1px solid #ccc; border-width: 0 1px 1px;font-family:"Lucida Grande","Verdana"; font-size:11px; }
.facebook-auto ul { display: none; margin: 0; padding: 0; overflow: auto; position:absolute; z-index:9999}
.facebook-auto ul li { padding: 5px 12px; z-index: 1000; cursor: pointer; margin: 0; list-style-type: none; border: 1px solid #ccc; border-width: 0 1px 1px; font: 11px "Lucida Grande", "Verdana"; background-color: #eee }
.facebook-auto ul li em { font-weight: bold; font-style: normal; background: #ccc; }
.facebook-auto ul li.auto-focus { background: #4173CC; color: #fff; }
.facebook-auto ul li.auto-focus em { background: none; }
.deleted { background-color:#4173CC !important; color:#ffffff !important;}
.hidden { display:none;}

#demo ul.holder li.bit-input input { padding: 2px 0 1px; border: 1px solid #c7c7c7; }
.ie6fix {height:1px;width:1px; position:absolute;top:0px;left:0px;z-index:1;}

/*VOTE*/
.vote{padding:15px 0px;overflow:hidden;padding-bottom:10px;width:162px;}
.vote .vote_q{margin-bottom:10px;color:#689341;font-weight:bold;}
.vote .vote_a_div{overflow:hidden;}
.vote .vote_a{}
.vote .vote_opt{border-width:0px;}
.vote .vote_a_txt{font-weight:bold;}
.vote .vote_btns{overflow:hidden;margin-top:20px;margin-bottom:0px;text-align:center;}
.vote .do_vote{background-color:#FFFFFF;cursor:pointer;}
.vote .Pre_Polls{cursor:pointer;width:80px;}

/*VOTE RES*/
.vote_res{padding:10px 0px;overflow:hidden;padding-bottom:0px;}
.vote_res .vote_res_q{margin-bottom:10px;color:#689341;font-weight:bold;}
.vote_res .vote_res_a_div{overflow:hidden;}
.vote_res .vote_res_a{font-weight:bold;}
.vote_res .vote_res_r{overflow:hidden;clear:both;}
.vote_res .vote_res_r_d{float:right;width:90px;background-color:white;margin:5px 0px;border:1px #e3e4de solid;}
.vote_res .vote_res_r_p{float:right;background-color:green;}
.vote_res .vote_res_r_txt{float:right;margin:5px;}
.vote_res .vote_btns{overflow:hidden;margin-top:20px;margin-bottom:0px;text-align:center;}
.vote_res .vote_btns input{cursor:pointer;}


/*VOTE RES INNER*/
.vote_res_inner{padding:15px;overflow:hidden;padding-bottom:0px;border-bottom:1px #b5b7b7 dotted;}
.vote_res_inner .vote_res_q{margin-bottom:10px;color:#689341;font-weight:bold;}
.vote_res_inner .vote_res_a_div{overflow:hidden;}
.vote_res_inner .vote_res_a{font-weight:bold;}
.vote_res_inner .vote_res_r{overflow:hidden;clear:both;}
.vote_res_inner .vote_res_r_d{float:right;width:100px;background-color:white;margin:5px 0px;border:1px #e3e4de solid;}
.vote_res_inner .vote_res_r_p{float:right;background-color:green;}
.vote_res_inner .vote_res_r_txt{float:right;margin:5px;}
.vote_res_inner .vote_btns{overflow:hidden;margin-top:20px;margin-bottom:0px;text-align:center;}
.vote_res_inner .vote_btns input{cursor:pointer;}

/*jScrollPane*/
.jScrollPaneContainer {position: relative;overflow: hidden;z-index: 1;}
.jScrollPaneContainer:focus{outline-width:0px;}

.jScrollPaneTrack {position: absolute;cursor: pointer;left: 0;top: 0;height: 100%;background: Transparent;}
.jScrollPaneDrag {position: absolute;background: #fff;cursor: pointer;overflow: hidden;}
.jScrollPaneDragTop {position: absolute;top: 0;right: 0;overflow: hidden;}
.jScrollPaneDragBottom {position: absolute;bottom: 0;right: 0;overflow: hidden;}
a.jScrollArrowUp {display: block;position: absolute;z-index: 1;top: 0;left: 0;text-indent: -2000px;overflow: hidden;/*background-color: #666;*/height: 9px;}
a.jScrollArrowUp:hover {/*background-color: #f60;*/}

a.jScrollArrowDown {display: block;position: absolute;z-index: 1;bottom: 0;left: 0;text-indent: -2000px;overflow: hidden;/*background-color: #666;*/height: 9px;}
a.jScrollArrowDown:hover {/*background-color: #f60;*/}
a.jScrollActiveArrowButton, a.jScrollActiveArrowButton:hover {/*background-color: #f00;*/}


/* TOOLTIP* */
#tooltip {position: absolute;z-index: 3000;border:2px solid #fff;background-color: #eee;padding: 5px;opacity: 0.85; width:200px;}
#tooltip h3, #tooltip div { margin: 0; font-weight:normal;font-size:10pt;text-shadow: 0 0 0.2em #8F7 }
#tooltip h3 {color:#01763A;}

/**************   ERROR Page   **************/
.error_page{width:40%;border:3px #d63104 solid;border-radius:10px;margin:50px auto;;padding:0px 20px;padding-top:100px;}
.error_page .message{color:#d63104;font-size:20pt;margin-bottom:100px;}
.error_page .back_button{padding:20px 50px;background-color:#d63104;color:#fff;cursor:pointer;}

/**************   SEARCHING   **************/
.search_div{width:100%x;height:36px;margin:0px auto;margin-bottom:2px;text-align:right;}
.search_div input{padding:3px 1px;margin:1px;width:95%;text-align:right;}/* the txtbox */
.p_res{padding:5px 20px}
.search_result_a{font-size:14pt; color:#005798;}
.tt-dropdown-menu{width:300px;font-size:12pt}
.twitter-typeahead{height:35px;width:20	0px}
.bs-example{	font-family: sans-serif;	position: relative;	margin: 50px;}
.typeahead, .tt-query, .tt-hint {	border: 2px solid #CCCCCC;	border-radius: 8px;	font-size: 24px;	height: 25px;	line-height: 25px;	outline: medium none;	padding: 3px 3px;	width: 200px;}
.typeahead {	background-color: #FFFFFF;}
.typeahead:focus {	border: 2px solid #0097CF;}
.tt-query {	box-shadow: 0 1px 1px rgba(0, 0, 0, 0.075) inset;}
.tt-hint {	color: #999999;}
.tt-dropdown-menu {	background-color: #FFFFFF;	border: 1px solid rgba(0, 0, 0, 0.2);	border-radius: 8px;	box-shadow: 0 5px 10px rgba(0, 0, 0, 0.2);	margin-top: 7px;	padding: 3px 0;	width: 210px;	overflow:hidden;}
.tt-suggestion p{	font-size: 10pt;	line-height: 24px;	padding: 3px 3px;}
.tt-suggestion.tt-is-under-cursor {	background-color: #0097CF;	color: #FFFFFF;}
.tt-suggestion p {	width:250px}

.Visits_to_Site{padding:10px 10px;color:#fff;font-size:12pt;font-weight:bold;border:0px #ccc solid; background-color:#ccc;display:block;clear:both;width:100px;position:absolute;left:5px;top:5px}
.Visits_to_Site h3 {font-size:18pt; padding:0px;margin:0px}

/**************   FOOTER   **************/
.footer{background-color:#eee;padding:20px 20px;margin-top:20px;border-top:1px #ccc solid;bottom:0px;width:100%;display:block}
.footer .Visits_to_Site{padding:10px 10px;color:#fff;font-size:12pt;font-weight:bold;border:0px #ccc solid;
background-color:#ccc;position:relative;margin-left:25px;float:left;display:none}
.footer .Visits_to_Site h3 {font-size:18pt; padding:0px;margin:0px}
.footer .social_media{margin-bottom:20px;display:inline-block;}
.footer .social_media img{border:2px #ccc solid;box-shadow: 0px 0px 20px #444;}
.footer .social_media a{padding:0px 10px;}
.footer .social_media img:HOVER{border:2px #fff solid;box-shadow: 3px 3px 20px #555;}
.footer .fcontent{display:block; margin-top:10px}
.footer .fcontent span{padding:10px;color:#777;border-top:1px #aaa solid;border-bottom:1px #aaa solid;box-shadow: 0px 0px 20px #bbb;text-shadow: 1px 1px #ccc;}
.footer .fcontent p{padding:5px;color:#005fd3;font-size:14pt;margin-top:20px;text-shadow: 2px 2px #ccc;}


/*************   WIDTHS   **************/
@media all and (min-width: 1140px) {
	.products .prod_blk {width:31%;} 
}
@media all and (max-width: 1060px) {	
	#content .Visits_to_Site{display:none}
	.footer .Visits_to_Site{display:block;}
}
@media all and (max-width: 940px) { .footer .Visits_to_Site{display:block;}
	#header_bar .button_loggedin{font-size:12pt;padding:0px 17px}
    #content_sidebar, .side_bar, .side_bar_blk{display:none;}
	#main_content{right:0px;left:0px;}
	.products .prod_blk {width:95%;}
	.products .prod_img{width:230px;height:200px;float:right;margin-right:30px}
	.products .prod_desc{overflow:hidden;float:right;width:45%;}
	.latest_news .news_blk{width:40%;margin-right:3%; }
}
@media all and (max-width: 600px) {
	.products .prod_blk {width:95%;height:auto;}
	.products .prod_img{padding:0px;margin:0px;width:40%;height:130px;margin-right:7%}
	.products .prod_desc{overflow:hidden;float:right;width:47%;}
	.latest_news .news_blk{width:85%;margin-right:4%;}
	#container{display:none}
}
@media all and (max-width: 350px) {
	.products .prod_blk {width:95%;}
	.products .prod_img{height:auto;float:right;}
	.products .prod_img img{border:1px #d4d4d4 solid;border-radius:10px;width:100%;height:100%;}
	.products .prod_desc{overflow:hidden;float:right;width:45%;}
	.products .prod_more{padding:6px 15px;}
	.latest_news .news_blk{width:85%;}
	.latest_news .news_img{height:auto;float:right;}
	.latest_news .news_img img{border:1px #d4d4d4 solid;border-radius:10px;width:80%;}
	.latest_news .news_desc{overflow:hidden;float:right;width:;}
	.latest_news .news_more{padding:6px 15px;margin:0}
}

/************   Heights   **************/
@media all and (max-height: 790px) {
.signin_div{margin:0px}
#ads_div{position:absolute;bottom:60px;height:200px;}
.ads_bar_hd{display:none;}
#ads_img{width:170px;max-height:180px;margin-top:2px;border:2px #fff solid;margin-top:10px;}
}
@media all and (max-height: 660px) {
#ads_div{display:none;}
} 
10) ../common/pframe.php



Contains the templet functions (header, footer, side bar ) and a wide variation of functions related to display controls (html) and manipulate data (php)…..


<?php
ob_start();
require_once '../db/mysqlcon.php';
require_once '../lang/lang_'.$GLOBALS['lang'].'.inc';
require_once '../cms/IPframe.php'; //-->

class pframe implements IPframe {
//________________________________________________________________________________________	
	public function header($title="",$pms=array()){ // function displays header HTML elements
		
		$title=="" ? $title=PROJECT_TITLE : $title=PROJECT_TITLE." - ".$title; //PROJECT TITLE 
		
		if ($_REQUEST['noframe']=="basic") {
			include_once '../tpl/basic.tpl.inc';
			tpl_header($title,$pms['mnu'],$pms['rel']);
			echo '<div id="all_bdy">';
		}else if ($_REQUEST['noframe']) {
			echo '<div id="all_bdy">';
		}else{
			include_once '../tpl/tpl.tpl.inc';
			//tpl_header($title,$pms['mnu'],$pms['rel']);
			tpl_header($title);
			echo '<div id="all_bdy">';
		}		
	}//_____________________________________________________________________________________

	public function footer($pms=array()) {  // function displays footer HTML elements
		
		if ($_REQUEST['noframe']=="basic") {			
			include_once '../tpl/basic.tpl.inc';
			echo '</div>';
			tpl_footer();
		}else if ($_REQUEST['noframe']) {			
			echo '</div>';
			include_once '../common/js.php';
		}else{
			include_once '../tpl/tpl.tpl.inc';
			echo '</div>';
			tpl_footer();
		}		
	}//_____________________________________________________________________________________

	public function open_box($withTree= "", $box_title="", $box_type="panel",$priv="" ,$adding="") {
		
// a box is a div element  to be a container, and this is the opening function

		$open=true;		//include_once '../tpl/' . $box_type . '.box.inc';
		include_once '../tpl/panel.box.inc';		
	}//______________________________________________________________________________________
	 
	public function close_box($box_type="panel") {
		
// a box is a div element  to be a container, and this is the closing function

		$close=true;		//include '../tpl/' . $box_type . '.box.inc';
		include_once '../tpl/panel.box.inc';
	}//___________________________________________________________________________________
	
	function card($NID, $card_title, $card_sum, $card_photo, $page_link="", $more_text="", $showfooter=true, $visits="", $NDate="", $NType="", $priv="")
	{
// a card is an html container, contains title description, long text, photo and so on..

		$NType !== "" ? $ntp="&NType=" . $NType : $ntp="";
		
		include_once '../tpl/news.card.inc';
		card($NID, $card_title, $card_sum, $card_photo,$page_link, $more_text, $showfooter, $visits, $NDate, $NType, $priv);	
	}//______________________________________________________________________________________

	function DrawPhotos($images,$AID) {
		
//draws HTML and javascript that display and preview the photo of the objects calls this

		include_once '../tpl/ext.tpl.inc';
		DrawPhotos($images,$AID);
		
	}/////////////////////////////////////////////////////////////////////////////////
	
	function VisitsCounter()
	{
// displays the number of our site visits

		$cnt = get_data_in("select countid from tcount", "countid");
		?><span class="Visits_to_Site"><?=Visits_to_Site?><br/><h3><?=$cnt?></h3></span><?
	}/////////////////////////////////////////////////////////////////////////////////
	
	function Get_Incremental_ID($table="",$col="") {

//some object need to have a serial number, it has to be incremental. So if the db table was empty it returns '1', else it returns the largest ID + 1 , to be an ID for te new object declared

		$res=get_data_in("select count(*) as 'count' from {$table}",'count');
		if($res != 0)
		{
			$id=get_data_in("select max({$col})+1 as 'max'  from {$table}",'max');
		}else{
			$id=1;
		}
		return $id;
	}/////////////////////////////////////////////////////////////////////////////////
	
	function onLogin($user)// We set a secure cookie whenever a user logs in with remember me option 
	{
		$token = GenerateRandomToken(); // generate a token, should be 128 - 256 bit
		storeTokenForUser($user, $token);
		$cookie = $user . ':' . $token;
		$mac = hash_hmac('sha256', $cookie, SECRET_KEY);
		$cookie .= ':' . $mac;
		setcookie('rememberme', $cookie);
	}/////////////////////////////////////////////////////////////////////////////////
	
	function rememberMe() 
	{
//the "remember me" feature on login, if it was checked, then create cookies to get the job done

		$cookie = isset($_COOKIE['rememberme']) ? $_COOKIE['rememberme'] : '';
		if ($cookie) {
			list ($user, $token, $mac) = explode(':', $cookie);
			if ($mac !== hash_hmac('sha256', $user . ':' . $token, SECRET_KEY)) {
				return false;
			}
			$usertoken = fetchTokenByUserName($user);
			if (timingSafeCompare($usertoken, $token)) {
				logUserIn($user);
			}
		}else{return false;}
	}/////////////////////////////////////////////////////////////////////////////////
		
	function DisplayAdminTools($page = "", $ID="" /*we can set $e="e", $d="d" to check whther we need only edit or only del but we used JQuery to hide the unneeded one*/)
	{
//admin tools enable the user to edit or delete the object that they appear next to .

		$page ==''? basename($_SERVER ['PHP_SELF']) : $page;
		if($page == 'mconts'){	$arr=explode('/', $ID); $params="&bcont={$arr[0]}&prod={$arr[1]}"; $ID = $arr[2];}
		elseif($page == "shared_pool"){$params = "&path={$_REQUEST['path']}";}
		?>
		<div class="admin_tools">
			<a href="../pages/<?=$page?>.php?v=e&lang=<?=$GLOBALS['lang']?>&NID=<?=$ID?><?=$params?>" class="EDT_admintool"><img src="../images/pencil-512.png" /></a>
			<a href="../pages/<?=$page?>.php?v=d&lang=<?=$GLOBALS['lang']?>&NID=<?=$ID?><?=$params?>" class="DEL_admintool"><img src="../images/dltimg.png" /></a>
		</div>
		<? 
	}/////////////////////////////////////////////////////////////////////////////////
	
	function DisplayAddingTool($priv="", $page="", $NID="IsNew")
	{
//the adding tool appears for the admin user.
//it enables him to add a new object of a given class according to the current page

		if($page=="") $page=basename($_SERVER['PHP_SELF']);
		$NO_Pages = array("reset_password.php","maillist.php"); //pages that don't need the adding tool inside

		if($page=="download.php"){$NID = "add_existing";}
		if(!($this->Is_Customer() && $page == 'bconts.php') && !in_array($page, $NO_Pages)){ //customers not allowed to add bconts
			if(user_has_permission(array("A", $priv))){

//if user is an admin OR has the managing permission for this class

				?>
					<a href="../pages/<?=$page?>?lang=<?=$GLOBALS['lang']?>&v=e&NID=<?=$NID?>"><img src="../images/addimg.png" class="admin_tools_img"/></a>
				<?
			}
		}
	}/////////////////////////////////////////////////////////////////////////////////
	
	function Make_Tree($php_self)
	{
//the tree is a set of links displayed on the inner header of the contents.
//tree shows your position in a given page.. 
//eg. Home > Products > All products  - eg. Products > "xProduct" > edit
//echo $php_self;

		$col_name="";
		$col_id="";
		$tbl="a";
		$params="";

//determine the GET parameter 'v'

		if($_REQUEST['v']=='e') $e=true; //edit
		if($_REQUEST['v']=='c') $c=true; //card
		if($_REQUEST['v']=='t') $t=true; //table
		if($_REQUEST['v']=='d') $d=true; //delete
		
		switch($php_self)
		{

// every page needs a tree appears in a switch case

			case"products.php":
				$col_name = prod_title_x;
				$col_id = "prod_id";
				$tbl = 'product';
				if($_REQUEST['cat']){$all_page = Categories;}
				else{$all_page = Products;}
				
			break;
				
			case"download.php":
				$download = true;
				$col_name = prod_title_x;
				$col_id = "prod_id";
				$tbl = 'product';
				$all_page = Download;
				$params = "&NID={$_REQUEST['NID']}";
			break;
	
			case"news.php":
				$col_name = news_title_x;
				$col_id = "news_id";
				$tbl = 'news';
				$all_page = News;
			break;
	
			case"ads.php":
				$col_name = ads_title_x;
				$col_id = "ads_id";
				$tbl = 'ads';
				$all_page = Ads;
			break;
			
			case"bconts.php":
				$col_name = 'NSerial';
				$col_id = "NSerial";
				$tbl = 'buy_contract';
				$all_page = BConts;
				if($_REQUEST['NID'] != 'IsNew')	{$params = "&NID={$_REQUEST['NID']}";}
			break;
			
			case"services.php":
				$col_name = src_title_x;
				$col_id = "srv_id";
				$tbl = 'service';
				$all_page = Services;
			break;
			
			case"our_customers.php":
				$col_name = "user_name";
				$col_id = "user_id";
				$tbl = 'user';
				$all_page = Our_Customers;
			break;
			
			case"category.php":
				$col_name = cat_title_x;
				$col_id = "cat_id";
				$tbl = 'category';
				$all_page = Categories;
			break;
			
			case"jobs.php":
				$col_name = job_title_x;
				$col_id = "job_id";
				$tbl = 'jobs';
				$all_page = Jobs;
			break;
			
			case"albums.php":
				$col_name = album_title_x;
				$col_id = "album_id";
				$tbl = 'album';
				$all_page = Albums;
			break;
				
			case"pictures.php":
				$pictures=true;
				$col_name = pic_title_x;
				$col_id = "pic_id";
				$col_parent= album_title_x;
				$tbl = 'picture';
				$all_page = Albums;
			break;
			
			case"videos.php":
				$videos=true;
				$col_name = pic_title_x;
				$col_id = "vid_id";
				$col_parent= album_title_x;
				$tbl = 'video';
				$all_page = Albums;
			break;
			
			case"faq.php":
				$col_name = faq_title_x;
				$col_id = "faq_id";
				$tbl = 'faq';
				$all_page = FAQ;
			break;
			
			case"groups.php":
				$groups=true;
				$col_name = group_name;
				$col_id = "group_id";
				$tbl = 'groups';
				$all_page = Groups;
				if($_REQUEST['v'] == 'members'){
					$members_to_group=true;
					$e = true;
					$parentTitle = Groups;					
					$params="&NID={$_REQUEST['NID']}";
				}
				else{$params="&NID={$_REQUEST['NID']}";}
				$params="&NID={$_REQUEST['NID']}";
			break;
			
			case"mconts.php":
				$mconts=true;
				$col_name = 'NSerial';
				$col_id = "NSerial";
				$tbl = 'maint_contract';
				$all_page = mconts;
				$params="&bcont={$_REQUEST['bcont']}&prod={$_REQUEST['prod']}&NID={$_REQUEST['NID']}";
			break;
			
			case"users.php":			
				$col_name = 'user_name';
				$col_id = "user_id";
				$tbl = 'user';
				if($_SESSION['back_page']=='groups')	{
					$group_to_users=true;
					$all_page = Groups;
					$params="&NID={$_REQUEST['group']}";//to open/collapse the users groups
					if($_REQUEST['group']){
						$php_self="groups.php";
					}
					$parentTitle=Groups;
				}
				if($_SESSION['back_page']=='users'){
					$user=true;
					$all_page = Users_Management;
					if($_REQUEST['v']=="priv_editor"){$e=true; $v="priv_editor";$params="&NID={$_REQUEST['NID']}";}					
				}
				if($_REQUEST['v'] == 'm'){$user=true;$t=true;}
			break; 
			
			case"license.php":
				$license=true;
				$col_name = bcont_serial;
				$col_id = "bcont_serial";
				$tbl = 'buy_contract';
				$all_page = BConts;
				
			break;
			
			case"blocked_ips.php":
				$col_name = ip;
				$col_id = "NID";
				$tbl = 'blocked_ip';
				$all_page = BlockedIPs;
			break;
			
			case"maillist.php":
				$col_name = email;
				$col_id = "NID";
				$tbl = 'mail_list';
				$all_page = MailList;
			break;
			
			case"site_configs.php":
				$site_configs = true;
				$col_name = "title";
				$col_id = "NID";
				$tbl = 'site_configs';
				$all_page = Site_Configs;
			break;
			
			case"shared_pool.php":
				$col_name = "title";
				$col_id = "NID";
				$tbl = 'shared_pool';
				$all_page = Shared_pool;
				$params = "&path=";
			break;
			
		}
		$pageName="../pages/albums.php";

//now some pages need to have their tree manipulated
//the tree of these pages looks like this : Home > Albums > xAlbum > xPicture
//parent title is the album of a picture

		if($pictures || $videos){
				$parentTitle=get_data_in("select {$col_parent} from album where album_id='{$_SESSION["album"]}'",$col_parent);
				$title=get_data_in("select {$col_name} from {$tbl} where {$col_id}='{$_REQUEST["NID"]}'",$col_name);
		
		}elseif($license && $_REQUEST['NID'] != 'IsNew'){$title= bcont_serial." ".$_REQUEST['NID'];
		}elseif($download){ //nothing to show
		}else 
/********   defualt   *********/
			$title=get_data_in("select {$col_name} from {$tbl} where {$col_id}='{$_REQUEST["NID"]}'",$col_name);
		

//if the GET param v=t, ie. The current page displays table of all objects
		if($t){
//exceptions has more links to show
			if($user || $groups || $site_configs){
				
				$tree .= '<a href="../common/control_panel.php?lang='.$GLOBALS['lang'].'">'.Control_Panel.'</a> > ';
				if($_REQUEST['v'] == 'members'){ } 
			}
			
			elseif($pictures || $videos){ // Home > Albums > xAlbum
				$tree .= '<a href="../pages/albums.php?lang='.$GLOBALS['lang'].'&v=t">'.$all_page."</a> > ";	}

//exceptions has more links to show
			elseif($mconts){
				$tree .= '<a href="../pages/bconts.php?lang='.$GLOBALS['lang'].'&v=t&NID='.$_REQUEST['bcont'].'">'.BConts.'</a> >';		}
			
//HOME LINK
			else{		$tree .= '<a href="../common"><img src="../images/icons/home.png"/></a> > ';			}			
			
		}

//if the GET param v=c, ie. The current page displays a card of a specific object

		if($c){ //HOME >
			$tree .= '<a href="../common"><img src="../images/icons/home.png"/></a> >';
			
//exceptions has more links to show
			if($pictures || $videos){ // HOME > Albums > xAlbum > xPic

				$tree .= '<a href="../pages/albums.php?lang='.$GLOBALS['lang'].'&v=t">'.$all_page."</a> > ";
				$tree .= '<a href="../pages/albums.php?NID='.$_SESSION["album"].'&lang='.$GLOBALS['lang'].'&v=c">'.$parentTitle."</a> >";
				$params="&album=".$_REQUEST['album'];
			}elseif($users){$tree .= '<a href="../pages/groups.php?NID='.$_REQUEST["group"].'&lang='.$GLOBALS['lang'].'&v=t">'.$parentTitle."</a> >";
			
			}else{

// Home > All objects of this table

				$tree .= '<a href="'.$php_self.'?lang='.$GLOBALS['lang'].'&v=t'.$params.'">'.$all_page."</a> > ";
			}
		}

//if the GET param v=e || v=d, ie. The current page displays Editor (add | edit) or deleting page

		if($e || $d){
			$v_t="v=t";

//exceptions has different links to show
			if($pictures || $videos){ // Albums > xAlbum > xPic > [edit|delete] Pic

				$tree .= '<a href="../pages/albums.php?lang='.$GLOBALS['lang'].'&v=t">'.$all_page.'</a> > ';
				$tree .= '<a href="../pages/albums.php?NID='.$_SESSION["album"].'&lang='.$GLOBALS['lang'].'&v=c'.$params.'">'.$parentTitle."</a> >";
			}elseif($group_to_users || $members_to_group){ 
				$tree .= '<a href="../pages/groups.php?NID='.$_REQUEST["group"].'&lang='.$GLOBALS['lang'].'&v=t">'.$parentTitle."</a> >";	
			}elseif($user && $v=="priv_editor"){$tree .= '<a href="'.$php_self.'?lang='.$GLOBALS['lang'].'&v=m'.$params.'">'.$all_page.'</a> > ';
			
			}else{

// Products > xProduct > [edit|delete] Product

				$tree .= '<a href="'.$php_self.'?lang='.$GLOBALS['lang'].'&v=t'.$params.'">'.$all_page.'</a> > ';
			}
			$tree .= ' <a href="'.$php_self.'?NID='.$_REQUEST["NID"].'&lang='.$GLOBALS['lang'].'&v=c'.$params.'">'.$title."</a> >";
		}
		return $tree;
	}/////////////////////////////////////////////////////////////////////////////////
	
	function DisplaySlider()
	{
// calling javascript libraries
		?>
		<link rel="stylesheet" href="../cms/slider/slider_<?=$GLOBALS['lang']?>.css">	
		<script src="../cms/slider/jquery.min.js"></script>
		<script src="../cms/slider/slides.min.jquery.js"></script>
		
		<script>
		$(function(){

//jacascript of the slider, which consists of many pictures sliding with their titles

			$('#slides').slides({
				preload: true,
				preloadImage: '../cms/slider/img/loading.gif',
				play: 2700,
				pause: 5000,
				hoverPause: true,
				animationStart: function(current){
					$('.caption').animate({
						bottom:-35
					},100);
					if (window.console && console.log) {

						// example return of current slide number
						console.log('animationStart on slide: ', current);
					};
				},
				animationComplete: function(current){
					$('.caption').animate({
						bottom:0
					},200);
					if (window.console && console.log) {

						// example return of current slide number
						console.log('animationComplete on slide: ', current);
					};
				},
				slidesLoaded: function() {
					$('.caption').animate({
						bottom:0
					},200);
				}
			});
		});
	</script>

// HTML elements of the slider: images, labels, links

	<div id="container">
		<div id="example">
			<img src="../cms/slider/img/new-ribbon.png" width="112" height="112" alt="New Ribbon" id="ribbon">
			<div id="slides">
				<div class="slides_container">
				<?
				$tbl=table("select * from site_config where config = 'slider' order by NDate desc ");
				while($row=mysql_fetch_array($tbl)){					
					?>
					<div class="slide">
						<a href="<?=$row['link']?>" title="<?=$row['title']?>" target="_blank">
						<img src="../documents/SLIDER_<?=$row['NID']?>.<?=$row['pic_slider']?>" width="570" height="270" alt="Slide 1"></a>
						<div class="caption" style="bottom:0">
							<p><?=$row['title']?></p>
						</div>
					</div>
					<? 					
				}
				?></div>
				<a href="#" class="prev"><img src="../cms/slider/img/arrow-<?=$GLOBALS['lang']=='en'?'prev':'next'?>.png" width="24" height="43" alt="Arrow Prev"></a>
				<a href="#" class="next"><img src="../cms/slider/img/arrow-<?=$GLOBALS['lang']=='en'?'next':'prev'?>.png" width="24" height="43" alt="Arrow Next"></a>
			</div>
			<img src="../cms/slider/img/example-frame.png" width="739" height="341" alt="Example Frame" id="frame">
		</div>

	</div><? 
	}/////////////////////////////////////////////////////////////////////////////////
				 
	 function SiteConfig($config="")
	 {
// Brings the configurations stored in DB and collect them in an array. Usually called at the beginning of the page and overwrites CSS value
	 	include_once '../obj/site_config.class.php';
	 	$mySC = new Site_config(); 
	 	
	 	$tbl=table("select * from site_config where config = '{$config}' ");
	 	$arr = array();
	 	while($Row=mysql_fetch_array($tbl)){
	 		array_push($arr,array("NID"=>$Row['NID'], "config"=>$Row['config'], "title"=>$Row['title'], "link"=>$Row['link'], "value"=>$Row['value'], "pic_social"=>$Row['pic_social'], "pic_slider"=>$Row['pic_slider'], "NDate"=>$Row['NDate'])); 
	 	}
	 	return $arr;
	 }/////////////////////////////////////////////////////////////////////////////////
	 
	 function is_current_page($this_page, $href, $v='', $v2='')
	 {

//helps us determine which link to highlight to tell this is the currents page

	 	$current_page = false;
	 	$current_v = false;
	 	if($this_page == $href)
	 	{//echo"1";
	 		$current_page = true;
	 	}
	 	if($v == '') 
	 	{//echo"v=''";
	 		$current_v=true;
	 	}
	 	else
	 	{//echo"v!=''";
	 		if($v ==$v2)
	 		{
	 			$current_v = true;
	 		}
	 	}
	 	if($current_page && $current_v){
	 		echo 'is_current_page';//echo"true+true";
	 	}//echo"this=$this_page,href=$href,v=$v,v2=$v2";
	 }/////////////////////////////////////////////////////////////////////////////////
	 	 
	 function Display_Related_Pages($title="empty", $arr_priv=array(), $Clicker_ID="")
	 {
//displaying the related page section, editing, adding or viewing all objects

//first, this requires the user to have the management permissions of the current class

	 	if (user_has_permission($arr_priv))
	 	{
//what is the value of parameter 'v'

	 		if($_REQUEST['v']=='c') $c=true;
	 		if($_REQUEST['v']=='e') $e=true; 
	 		if($_REQUEST['v']=='d') $d=true;
	 		if($_REQUEST['v']=='t') $t=true;
	 		$NID="IsNew";
	 		$ptitle=Edit_x;	 
	 			 		
	 		?><div id="related_pages"><? 
	 		?><div class="related_pages_hd"><?=Related_Pages?></div><? 
	 				switch (basename($_SERVER['PHP_SELF']))
	 				{
//exceptions have to go here to have different attributes…

	 					case 'download.php':
	 						$download = true;
	 						$NID="add_existing";
	 						$ttl = Download;
	 					break;
	 					case 'albums.php':
	 						$albums=true;
	 						$ttl=Albums;
	 					break;
 						case 'pictures.php':
 							$pictures=true;
 							$ttl=Pictures;
 						break;
 						case 'mconts.php':
 							$mconts=true;
 							//if($_REQUEST['NID']=='IsNew') 
 								$NID="IsNew&bcont={$_REQUEST['bcont']}";
 							$ttl=mconts;
 						break;
	 					
	 				}

	 				if($e || $c)  // then show [view all objects] button
	 				{

//when the parameter 'v'=e (editing|adding) or v=c (card)

	 					$ptitle=View_x ." ". $ttl;
	 					if($albums){
	 						if($_REQUEST['NID']=='IsNew'){
	 								echo '<p>'.showview_details("../pages/albums.php?lang={$GLOBALS['lang']}&v=t", true, $ttl).'</p>';
	 							}else{
	 								echo '<p>'.showview_details("../pages/albums.php?lang={$GLOBALS['lang']}&v=d&NID={$_REQUEST['NID']}", true, Delete_Album).'</p>';
	 								echo '<p>'.showview_details("../pages/pictures.php?lang={$GLOBALS['lang']}&v=e&NID=IsNew&album={$_REQUEST['NID']}", true, Add_x." ".Pic).'</p>';
	 								echo '<p>'.showview_details("../pages/videos.php?lang={$GLOBALS['lang']}&v=e&NID=IsNew&album={$_REQUEST['NID']}", true, Add_x." ".Video).'</p>';
	 							}
	 						}
	 					else{
	 						echo '<p>'.showview_details($_SERVER['PHP_SELF']."?lang={$GLOBALS['lang']}&v=t&NID={$_REQUEST['NID']}", true, $ptitle).'</p>';
	 						$t=true; // also go as if the v=t too }
		 			}

// show [adding new item] button
		 			if($t || $d)
					 {
//when the parameter v=t (all obj) or v=d (delete)

				 		if($download){ echo '<p>'.showview_details($_SERVER['PHP_SELF']."?lang={$GLOBALS['lang']}&v=e&NID={$NID}", true, Add_x).'</p>';}
				 	
				 		else{
				 			$ptitle = Add_x." ".$ttl;
				 			echo '<p>'.showview_details($_SERVER['PHP_SELF']."?lang={$GLOBALS['lang']}&v=e&NID={$NID}", true, $ptitle).'</p>';
				 		}
					 }
					 	
		 	?></div><?
	 	}
	 }/////////////////////////////////////////////////////////////////////////////////
	 
	 function DisplayAlbumPic($myAlb)
	 {
//makes the last picture inserted to this album is the preview picture
//if the album was empty, a specific image is shown

	 	include_once '../obj/picture.class.php';
	 	$myPic=new Picture(); 
	 	$tbl=table("select * from picture where pic_album='".$myAlb->album_id['value']."'  order by NDate desc Limit 1"); 
	 	
	 	if(($row=mysql_fetch_array($tbl)) !== false)
	 	{ // album has pics
 			$myPic->FillIn($row);
 			echo $myPic->Draw_Photo($myPic->pic_ext, "thumb");
	 	}else{//album is empty from pics
	 		echo '<img src="../images/no-image.png" />';
	 	}
	 	
	 	
	 }/////////////////////////////////////////////////////////////////////////////////
	 
	 function User_Card($myUser, $user="users" /* is this page a user or group management page*/ ,
$priv /*must have this param to get admin tools*/)
	 {

//displays the personal information for a given user

	 	//if(user_has_permission(array("A", $priv))){
		 ?><table class="user_card"><? 
		 	?><tr> <td><b><?=$myUser->user_name['value']?> </b><td> 

//show admin tools if the user has the permission of the class 

<?if(user_has_permission(array("A", $priv))){$this->DisplayAdminTools($user, $myUser->user_id['value'] );}?></td></tr><? 
		 		?><tr><td><?=$myUser->Draw_Photo($myUser->user_pic, "thumb")?></td></tr><?
		 		?><tr> <td><?=user_email?></td> <td><?=$myUser->user_email['value']?></td></tr><?
		 		?><tr> <td><?=user_phone?></td> <td><?=$myUser->user_phone['value']?></td></tr><?
		 		?><tr> <td><?=user_country?></td> <td><?=$myUser->user_country['value']?></td></tr><?
		 		?><tr> <td><?=user_city?></td> <td><?=$myUser->user_city['value']?></td></tr><?
		 		?><tr> <td><?=user_address?></td> <td><?=$myUser->user_address['value']?></td></tr><?
	
		 	?></table><?
	 	//} 
	 }/////////////////////////////////////////////////////////////////////////////////	 
	 
	 function Is_Customer()
	 {

// enables us to know whether this user who called it, is a customer or not

	 	$customer=get_data_in("select user_cat from user where user_id='{$_SESSION['GID']}' ", "user_cat");
	 	if($customer == "customer"){
			return true;
	 	}else{
	 		return false;	
	 	}
	 	
	 }/////////////////////////////////////////////////////////////////////////////////
	 
	 function Is_Distributor()
	 {

// enables us to know whether this user who called it, is a distributor or not

	 	$customer=get_data_in("select user_cat from user where user_id='{$_SESSION['GID']}' ", "user_cat");
	 	if($customer == "distributor"){
	 		return true;
	 	}else{
	 		return false;
	 	}
	 	 
	 }/////////////////////////////////////////////////////////////////////////////////

	 function get_client_ip() { 

//bringing the ip address of the client

	 	$ipaddress = '';
	 	if (getenv('HTTP_CLIENT_IP'))
	 		$ipaddress = getenv('HTTP_CLIENT_IP');
	 	else if(getenv('HTTP_X_FORWARDED_FOR'))
	 		$ipaddress = getenv('HTTP_X_FORWARDED_FOR');//can be behind a proxy server
	 	else if(getenv('HTTP_X_FORWARDED'))
	 		$ipaddress = getenv('HTTP_X_FORWARDED');
	 	else if(getenv('HTTP_FORWARDED_FOR'))
	 		$ipaddress = getenv('HTTP_FORWARDED_FOR');
	 	else if(getenv('HTTP_FORWARDED'))
	 		$ipaddress = getenv('HTTP_FORWARDED');
	 	else if(getenv('REMOTE_ADDR'))
	 		$ipaddress = getenv('REMOTE_ADDR');//for clients connected through a proxy
	 	else
	 		$ipaddress = 'UNKNOWN';
	 	return $ipaddress;
	 }/////////////////////////////////////////////////////////////////////////////////
	 
	 function illegal_attempts($reason="failed_login", $ip=""){

//brings from DB the number of illegal attempts that the client ip has done in the las three days..
	
	 	$in_days=" and NDate >= NOW()-INTERVAL 3 DAY";//if this is enabled, the ip will blocked for 3 days
	 	$in_days="";
	 	$ip= $ip=="" ? $this->get_client_ip() : $ip;
	 	$sql = "select attempts from blocked_ip where ip='{$ip}' and reason='{$reason}' {$in_days}  order by NDate desc limit 1";
	 		return get_data_in($sql, "attempts");
	 	
	 }/////////////////////////////////////////////////////////////////////////////////
	 	 
	 function record_row_in_blockedips($type){

// reports/records the illegal attempt for the client IP in the db.

	 	$NID=uniqid();
	 	$NDate=nowandate;
	 	$ip=$this->get_client_ip();
	 	$user = $_SESSION['GID']=='' ? $_SESSION['failed_username']:$_SESSION['UNM'];
	 	
// has the ip done illegal attempt in the last 3 days ?

	 	$in_days=" and NDate >= NOW()-INTERVAL 3 DAY";
	 	$q="select * from blocked_ip where ip='{$ip}' and reason='{$type}' {$in_days} order by NDate desc limit 1";
	 	echo "<br/>".$q."<br/>";
	 	if(@mysql_num_rows(mysql_query($q)) == 0) // if the ip is new insert a new row
	 	{
	 		$sql="insert into blocked_ip values('{$NID}','{$ip}', '{$user}_', 1, '{$type}', '{$NDate}')"; echo $sql;
	 		@cmd($sql);
	 	}else{ //if the ip already exists update its row by increasing the attempts
	 		echo "<br/>select attempts from blocked_ip where ip='{$ip}' and reason='{$type}' <br/>";
	 		$num=get_data_in("select attempts from blocked_ip where ip='{$ip}' and reason='{$type}' {$in_days} order by NDate desc limit 1", "attempts") + 1;
	 		$sql="update blocked_ip set attempts = '{$num}', user=concat(user, '{$user}_') where ip='{$ip}' and reason='{$type}' {$in_days} order by NDate desc limit 1";	echo $sql;
	 		@cmd($sql);
	 	}
	 }/////////////////////////////////////////////////////////////////////////////////
	 
	 function Generat_OTID_Link($hash_user)
	 {

// genetates One-Time-ID LINK for a given user hash

	 	$otid = uniqid();
	 	$now = nowandate;
	 	$sql = "insert into sys_otid values('{$otid}', '{$hash_user}', '{$now}')"; //echo "<br/>". $sql;
	 	if(!@cmd($sql)) return false;
	 	
	 	$domain = "localhost/Hadara";
	 	$link = "{$domain}/common/reset_password.php?otid={$otid}&x={$hash_user}";
	 	return $link;
	 }/////////////////////////////////////////////////////////////////////////////////
	 
	 function get_Email_regestered_in_DB($user)
	 {
// checks whether an email is registered in our DB and return 

	 	$res = get_data_in("select user_email from user where user_name='{$user}' ", "user_email");
	 	
	 	return $res;
	 }/////////////////////////////////////////////////////////////////////////////////
	 
	 function unreadable_email($email)
	 {
// returns a part of the user's email (first 4 letters)

	 	if($email != "") 
	 	{
	 		$a="";
	 		
	 		for( $i=0 ; $i < strlen($email); $i++)
	 		{
	 			if($i > 4 && $email[$i] != '@')
	 				$a .= "*";
	 			else 
	 				$a .= $email[$i];
	 		}
	 		return $a;
	 	}
	 }/////////////////////////////////////////////////////////////////////////////////
	 
	function Valid_OTID($otid)
	{

// is the OTID (one-time-id) valid? If it has been created before 7 days then NO

		$sql = "select * from sys_otid where otid = '{$otid}' and NDate >= NOW() - INTERVAL 7 DAY";//echo $sql;
		$row=table($sql);
		if(@mysql_fetch_array($row)){return true;}
		else return false; 
		
	}/////////////////////////////////////////////////////////////////////////////////
	
	function Delete_OTID($otid, $hash_user)
	{
//delete the OTID from the DB

		$sql = "delete from sys_otid where otid = '{$otid}' ";//echo $sql;
		if(!@cmd($sql))
			return false;
		$sql = "delete from sys_otid where user_hash = '{$hash_user}' ";//echo $sql;
		if(!@cmd($sql))
			return false;
		
		return true;
	
	}/////////////////////////////////////////////////////////////////////////////////
	
	function bring_USER_by_hash($user_hash)
	{
// I give you user_hash then search between  all hashes of user to find this hash, and return me the user id

		$sql = "select user_id, user_name from user";//echo $sql;
		$tbl=table($sql);
		$user_found = "";
		while($row=mysql_fetch_array($tbl))
		{
			$user=$row['user_name'].".OTID";
			$md5=md5($user);
			if($md5 == $user_hash)
				$user_found = $row['user_id'];
		}
		return $user_found;
	
	}/////////////////////////////////////////////////////////////////////////////////
	
	function format_size($size) {

//formatting the file size
// return humanly readable number with the unit

		$sizes = array(" Bytes", " KB", " MB", " GB", " TB", " PB", " EB", " ZB", " YB");
		if ($size == 0) {
			return('n/a');
		} else {
			return (round($size/pow(1024, ($i = floor(log($size, 1024)))), 2) . $sizes[$i]);
		}
	}/////////////////////////////////////////////////////////////////////////////////
	
}?>








 
11) ../common/privileges.php



Managing all operations in term of privileges.


<?php
session_start();
function DB_All_Privileges($GID, $login=true)
{
//brings all privileges for a given user (groups privs – user privs)

	$group_str = DB_Groups_AS_STR($GID, $login);
	 
	$privs_from_groups = DB_GROUPS_Privileges($group_str);
	 
	$user_privs = DB_USER_Privileges($GID);
	 
	$ALL=array_merge($user_privs, $privs_from_groups);
	
	return $ALL;	
}/////////////////////////////////////////////////////////////////////////////////

function DB_Groups_AS_STR($GID, $login=false)
{
//returns a string of the groups that the user is a member of..

	$groups=array();
		$i=0;
		$tbl=table("select group_id from groups where group_id in (select user_group_id from user_groups where user_id='$GID')");
		while($row=mysql_fetch_array($tbl)){
			$groups[$i]=$row['group_id'];
			$i++;
		}
		$group_str="";
		foreach($groups as $gid){
			$group_str .= $gid.",";
		}
	 	
	 	if($i>1){
	 		$group_str="'".$group_str;
	 		$group_str=str_replace(",", "','", $group_str);
	 		$group_str = substr($group_str,0,-1);
	 	}
	 	$group_str = substr($group_str,0,-1);
	 	return $group_str;	 	
}/////////////////////////////////////////////////////////////////////////////////
	
 function DB_GROUPS_Privileges($group_str)
 {
//returns an array of the privileges from groups that the user is member of

 	$privs=array();

 	$i=0;
 	if(strpos($group_str,',') !== false) // means many groups sent
 	{
 		$tbl=table("select priv from user_group_privs where group_id in ({$group_str})");
 		
 	}else{ //means only one group
 		$tbl=table("select priv from user_group_privs where group_id='{$group_str}' "); 
 	}
 	while($row=mysql_fetch_array($tbl)){
 		$privs[$i]=$row['priv'];
 		$i++;
 	}
 		return $privs;
 }/////////////////////////////////////////////////////////////////////////////////
 
 function DB_USER_Privileges($GID)
 {
//returns an array of the privileges granted to the user

 	$privs=array();
 	$i=0;
 	$tbl=table("select user_priv from user_privs where user_id = '{$GID}' ");
 	while($row=mysql_fetch_array($tbl)){ 		
 		$privs[$i]=$row['user_priv'];
 		$i++;
 	}
 	return $privs;
 }/////////////////////////////////////////////////////////////////////////////////

 function group_has_permission($gr_id, $per)
 {
//checks whether a group is granted a certain privilege

 	if (true) {
 		$gr_prv = DB_GROUPS_Privileges($gr_id);
 		foreach ($gr_prv as $priv){ //echo $priv . "<br/>";
 			if (in_array($priv, $per)) {
 			return true;
 		}}
 	}
 	return false;
 }/////////////////////////////////////////////////////////////////////////////////
  
 function PrivilegesEditor($priv="GROUPS_MAN")
 {

//Displays the editor which shows the privileges for a given group or user, and enables us to add/remove privs to user/group

 	$P=array("A", $priv);
 	$ALL=array_merge($P, $_SESSION['PRIVS']);
 	//print_r($A);
 	if(user_has_permission($ALL))
 	{
 		$_SESSION['back_uri']=$_SERVER['PHP_SELF'].'?'.$_SERVER['QUERY_STRING'];//echo $_SESSION['back_uri']."<br/>";
 		$_SESSION['access_to_all_privs']=$priv;
 		$ALL_PRIVS=Get_All_Privileges($_SESSION['access_to_all_privs']);

 		?><div class="all_privs"><?
 	 			?><div class="hd">
 	 				<?if($_SESSION['back_page'] == "groups"){ echo All_Group_Privs; }elseif($_SESSION['back_page'] == "users"){echo All_User_Privs;}?>
 	 			</div><?
 	 		 foreach($ALL_PRIVS as $key => $val)
 	 		 {
 	 		 	if($_SESSION['back_page'] == 'groups'){
	 	 		 	if(!group_has_permission($_REQUEST['NID'], array( $val))){
	 	 		 	?><div style="margin-left:10px;display:block;clear:both"><p>(<?=$key?>) :&nbsp;&nbsp;&nbsp;<?=$val?></p> 
	 	 		 	<a href="../common/manage_group_privs.php?lang=<?=$GLOBALS['lang']?>&v=add&PR=<?=$key?>&group=<?=$_REQUEST['NID']?>" class="add_grp_prv"> >> </a></div><?
	 	 		 	} 
 	 		 	}elseif($_SESSION['back_page'] == 'users'){
 	 		 		if(!any_user_has_permission($_REQUEST['NID'], array( $val))){
 	 		 			?><div style="margin-left:10px;display:block;clear:both"><p>(<?=$key?>) :&nbsp;&nbsp;&nbsp;<?=$val?></p>
 	 		 			<a href="../common/manage_group_privs.php?lang=<?=$GLOBALS['lang']?>&v=add&PR=<?=$key?>&user=<?=$_REQUEST['NID']?>" class="add_grp_prv"> >> </a></div><?
 	 		 		}
 	 		 	}
 	 		 }
 	 		 ?></div><?
 	 		 
 	 		 ?><div class="this_group_privs"><?
 	 		 	?><div class="hd"><?if($_SESSION['back_page'] == "groups"){ echo This_Group_Privs; }elseif($_SESSION['back_page'] == "users"){echo This_User_Privs;}?></div><?
 	 		 	if($_SESSION['back_page'] == "groups"){
 	 		 		this_group_privs($_REQUEST['NID'], false);
 	 		 	}elseif($_SESSION['back_page'] == "users"){
 	 		 		this_user_privs($_REQUEST['NID'], false);
 	 		 	}
 	 			 
 	 		 ?></div><?
 	 		 
 	 	}
 }/////////////////////////////////////////////////////////////////////////////////
 
 function Get_All_Privileges($priv="GROUPS_MAN")
 {
// we store the privileges we grant to users/groups in this array

 	$PRV_ARR=array(
 			"P01"=>"GROUPS_MAN",
 			"P02"=>"USERS_MAN",
 			"P03"=>"DISTRIBUTORS_MAN",
 			"P04"=>"CUSTOMERS_MAN",
 			"P05"=>"EMPLOYEES_MAN",
 				
 			"P06"=>"CATEGORY_MAN",
 			"P07"=>"PRODUCTS_MAN",
 			"P08"=>"DOWNLOADS_MAN",
 			"P09"=>"NEWS_MAN",
 				
 			"P10"=>"SERVICES_MAN",
 			"P11"=>"JOBS_MAN",
 			"P12"=>"FAQ_MAN",
 				
 			"P13"=>"CONTACTUS_MAN",
 			"P14"=>"ADS_MAN",
 				
 			"P15"=>"ALBUMS_MAN",
 			"P16"=>"PICTURES_MAN",
 			"P17"=>"VIDEOS_MAN",
 				
 			"P18"=>"BCONTS_MAN",
 			"P19"=>"MCONTS_MAN",
 				
 			"P21"=>"CONFIGS_MAN",
 			"P22"=>"BLOCKED_IPS_MAN",
 			"P23"=>"MAILLIST_MAN",
 			"P24"=>"SHARED_POOL_MAN"
 	);
 	$arr_allowed=array("A", "GROUPS_MAN", "USERS_MAN");
 	if(in_array($priv, $arr_allowed))
 	{
 		if(user_has_permission($arr_allowed))
 		{
 			return $PRV_ARR;
 		}
 	}
 }/////////////////////////////////////////////////////////////////////////////////
 function this_group_privs($group="", $login=true)
 { 
// displays the privs of the groups we are editing 

 	if($login){
 // it's used in login process also

 		$this_group_id = get_data_in("select user_group_id from user_groups where user_group_id='{$group}' and user_id='{$_SESSION['GID']}' ", "user_group_id"); //echo $this_group_id;
 		$group_str = DB_Groups_AS_STR($_SESSION['GID']);
 
 	}else{
 		$group_str = $group;
 	}
 	 
 	$privs_from_groups = DB_GROUPS_Privileges( $group_str);
 
 	foreach($privs_from_groups as $val)
 	{
 		$key = get_priv_key($val);
 		?><div style="margin-left:10px;display:block;clear:both"><p>(<?=get_priv_key($val)?>) :&nbsp;&nbsp;&nbsp;<?=$val?></p><?
  			?><a href="../common/manage_group_privs.php?lang=<?=$GLOBALS['lang']?>&v=del&PR=<?=$key?>&group=<?=$_REQUEST['NID']?>" >X</a></div><?
  	} 		
}/////////////////////////////////////////////////////////////////////////////////

function get_priv_key($pr)
{
// returns the KEY of a given privilege from the main array of privs

	$ALL_PRIVS=Get_All_Privileges($_SESSION['access_to_all_privs']);
	foreach ($ALL_PRIVS as $k => $v){
		if($pr == $v)
			return $k;
	}
}
/////////////////////////////////////////////////////////////////////////////////
//////////////////////////                            ///////////////////////////
/////////////////////////    manage user privileges   ///////////////////////////
/////////////////////////                            ////////////////////////////
/////////////////////////////////////////////////////////////////////////////////

function this_user_privs($user="", $login=true)
{	
//displays privs granted to a given user

	$privs_from_user = DB_USERS_Privileges ($user); 
	if(count($privs_from_user != 0))
	{
		foreach($privs_from_user as $val)
		{
			if($_SESSION['back_page']=='groups') $page="group";
			if($_SESSION['back_page']=='users') $page="user";
			$key = get_priv_key($val);
			?><div style="margin-left:10px;display:block;clear:both"><p>(<?=get_priv_key($val)?>) :&nbsp;&nbsp;&nbsp;<?=$val?></p><?
			?><a href="../common/manage_group_privs.php?lang=<?=$GLOBALS['lang']?>&v=del&PR=<?=$key?>&<?=$page?>=<?=$_REQUEST['NID']?>" >X</a></div><?
		}
	}else{echo No_Rows_Selected;}
}/////////////////////////////////////////////////////////////////////////////////

function any_user_has_permission($us_id, $per)
{
//checks whether a user is granted a given priv (non group priv)

	if (true) {
		$us_prv = DB_USERS_Privileges($us_id);
		
		foreach ($us_prv as $priv){ //echo $priv . "<br/>";
			if (in_array($priv, $per)) {
				return true;
			}
		}
	}
	return false;
}/////////////////////////////////////////////////////////////////////////////////

function DB_USERS_Privileges($user)
{
	return DB_USER_Privileges($user);
}/////////////////////////////////////////////////////////////////////////////////

function MembersEditor($priv)
{	
//when we explore groups, we can add/remove members to 'em. This function enables us do that

	$P=array("A", $priv);
	$ALL=array_merge($P, $_SESSION['PRIVS']);
	if(user_has_permission($ALL))
	{
		$_SESSION['back_uri']=$_SERVER['PHP_SELF'].'?'.$_SERVER['QUERY_STRING'];
		$_SESSION['access_to_all_privs']=$priv;
		$MEMBERS = group_non_members();
		
	?><div class="all_privs"><? // Non-member Users
	 	  ?><div class="hd">
	 	 	<?=All_Users?>
	 	   </div><?
 	 		 foreach($MEMBERS as $key => $val)
 	 		 {
 	 		 	if($_SESSION['back_page'] == 'groups'){
	 	 		   ?><div style="margin-left:40px;display:block;clear:both"><p><?=$val?></p> 
	 	 		   <a href="../common/manage_group_privs.php?lang=<?=$GLOBALS['lang']?>&v=add&members=1&user=<?=$key?>&group=<?=$_REQUEST['group']?>" class="add_grp_prv" > >> </a></div><? 
 	 		 	}
 	 		 }
 	 		 ?></div><?
 	 		 
 	 		 ?><div class="this_group_privs"><? // Group Members
 	 		 	?><div class="hd">
 	 		 		<?=Group_members?>
 	 		 	</div><?
 	 			 
 	 		 	$members = group_members();
 	 		 	foreach($members as $key => $val)
 	 		 	{
 	 		 		?><div style="margin-left:40px;display:block;clear:both"><p><?=$val?></p> 
	 	 		   <a href="../common/manage_group_privs.php?lang=<?=$GLOBALS['lang']?>&v=del&members=1&user=<?=$key?>&group=<?=$_REQUEST['group']?>" class="add_grp_prv" > X </a></div><?
 	 		 	} 
 	 		 ?></div><?	 	 		 
	 }	
}/////////////////////////////////////////////////////////////////////////////////

function group_members()
{
//returns members/users of the groups

	$sql = "select user_id, user_name from user where user_id in (select user_id from user_groups where user_group_id = '{$_REQUEST['group']}' ) order by user_name ";
	$tbl = table($sql);
	$arr = array();
	while($Row = mysql_fetch_array($tbl)){
		$arr[$Row['user_id']] = $Row['user_name'];
	}
	//print_r($arr);
	return $arr;
}/////////////////////////////////////////////////////////////////////////////////

function group_non_members()
{
// returns users that are not members of the group we are editing

	$sql = "select user_id, user_name from user where user_id not in (select user_id from user_groups where user_group_id = '{$_REQUEST['group']}' ) order by user_name";
	$tbl = table($sql);
	$arr = array();
	while($Row = mysql_fetch_array($tbl)){
		$arr[$Row['user_id']] = $Row['user_name'];
	}
	//print_r($arr);
	return $arr;
}/////////////////////////////////////////////////////////////////////////////////


 	
11) ../common/manage_group_privs.php



Managing issues related to grant/revoke privileges from users/groups. Also add/delete members from groups


<?php
session_start();
include_once '../common/pframe.php';
include_once '../common/privileges.php';
$myf=new pframe();
$pagePRIV = array("GROUPS_MAN","USERS_MAN"); 
/***/IS_SECURE($pagePRIV);/***/ // "A" is also checked inside

switch ($_REQUEST['v'])
{
	case "add": 
/*******************  adding privs to groups, users OR add users to group  *******************/
		
		$all_privs = Get_All_Privileges($_SESSION['access_to_all_privs']);
		
		$arr = $_SESSION['PRIVS'];
		if($_REQUEST['PR'] != "" || $_REQUEST['members'] != "") 
		{
			if(! in_array($_REQUEST['PR'], $arr))
			{
				$add_this_priv = $all_privs[$_REQUEST['PR']];
				 
				if($_SESSION['back_page']=="groups")
					add_priv_to_group($_REQUEST['group'], $add_this_priv);
				
				elseif($_SESSION['back_page']=="users")
					add_priv_to_user($_REQUEST['user'], $add_this_priv);
			}			
			
			if( $_REQUEST['members'] != ""){
				add_user_to_group($_REQUEST['user'], $_REQUEST['group']);
			}			
			
			echo $_SESSION['back_uri'];		
			header("location: {$_SESSION['back_uri']}");
		}
	break;
	
	case "del":	
/*******************  deleting  privs to groups, users OR add users to group  *******************/
		
		if( !$_REQUEST['members']){
			$all_privs = Get_All_Privileges($_SESSION['access_to_all_privs']);
			$del_this_priv = $all_privs[$_REQUEST['PR']];echo "<br/>del_this_priv=$del_this_priv";
			
			if($_SESSION['back_page']=="groups")
				del_priv_from_group($_REQUEST['group'], $del_this_priv);
			
			elseif($_SESSION['back_page']=="users")
				del_priv_from_user($_REQUEST['user'], $del_this_priv);
		}
		
		elseif( $_REQUEST['members'] != ""){
			del_user_from_GROUP($_REQUEST['user'], $_REQUEST['group']);
		}
		
		header("location: {$_SESSION['back_uri']}");
	break;
}

function add_priv_to_group($group, $add_this_priv)
{
//grant priv to group

	$sql= "select group_id from user_group_privs where priv = '{$add_this_priv}' and group_id='{$group}' ";
	echo " $sql <br/>";
	if(!@mysql_num_rows(mysql_query($sql)) != 0){ 
		$sql1="insert into user_group_privs values('{$group}','{$add_this_priv}')"; echo "<br/>$sql1<br/>";
		cmd($sql1);
	}echo "</br>insert into user_group_privs values('{$group}','{$add_this_priv}')</br>";;
}/////////////////////////////////////////////////////////////////////////////////

function del_priv_from_group($group, $del_this_priv)
{
//revoke priv from group

	$sql3="delete from user_group_privs where group_id='{$group}' and priv='{$del_this_priv}' ";	cmd($sql3);
}/////////////////////////////////////////////////////////////////////////////////

function add_priv_to_user($user, $add_this_priv)
{
// grant priv to user

	$sql= "select group_id from user_privs where priv = '{$add_this_priv}'  and group_id='{$user}' ";
	if(!@mysql_num_rows(mysql_query($sql)) != 0){
		$sql4="insert into user_privs values('{$user}','{$add_this_priv}',0)"; echo "<br/>".$sql4."<br/>";
		cmd($sql4);
	}
}/////////////////////////////////////////////////////////////////////////////////

function del_priv_from_user($user, $del_this_priv)
{
//revoke priv from user

	$sql5="delete from user_privs where user_id='{$user}' and user_priv='{$del_this_priv}' "; echo "<br/>$sql5<br/>";
	cmd($sql5);
}/////////////////////////////////////////////////////////////////////////////////

function add_user_to_group($userid, $groupid)
{
// add a certain user/member to a certain group

	$sql= "select user_id from user_groups where user_id = '{$userid}' and user_group_id='{$groupid}' ";
	echo " $sql <br/>";
	if(!@mysql_num_rows(mysql_query($sql)) != 0){
		$sql1="insert into user_groups values('{$userid}','{$groupid}')"; echo "<br/>$sql1<br/>";
		cmd($sql1);
	}
}/////////////////////////////////////////////////////////////////////////////////

function del_user_from_GROUP($userid, $groupid)
{
// eject user from group

	$sql5="delete from user_groups where user_id='{$userid}' and user_group_id='{$groupid}' "; echo "<br/>$sql5<br/>";
	cmd($sql5);
}/////////////////////////////////////////////////////////////////////////////////



11) ../common/reset_password.php



When a user wants tp reset his password from the profile page. In addition, this handles the "forgot-password operations" within a very secure  fabric…


<?php
ob_start ();
session_start();
include_once '../common/pframe.php';
$myframe = new pframe ();
$myframe->header ( Reset_Password );
$myframe->open_box("", Reset_Password,"panel");
// calling libraries as usual + declaring objects + header boxes

if_POST();

if($_SESSION["GID"]) //came from user profile
{
	Password_Reset();	// show html form of the Reset Password
}
else{ // came here from reset pass link 
	//otid exists and within this week ??
	//echo $_SESSION['reset']['otid']."</br>".$_SESSION['reset']['user_hash']."<br/>";
	
	if( /*first access*/$myframe->Valid_OTID($_REQUEST['otid']) || 
            /*or has session*/($_SESSION['reset'] && $_SESSION['reset']['otid'] != ""))
	{
		echo '<p style="color:red">'.one_time_use_link.'</p>';
		if($myframe->Valid_OTID($_REQUEST['otid']))
//is this OTID valid or has been used before
		{
			$_SESSION['reset']['otid'] = $_REQUEST['otid'];
			$_SESSION['reset']['user_hash'] = $_REQUEST['x'];
			
			$myframe->Delete_OTID($_SESSION['reset']['otid'], $_SESSION['reset']['user_hash']); // at first access the otid is deleted so the link will not be used again				
		}
		//else{echo '<p style="color:red">'.this_link_is_expired_now.'</p>';}
		
		Password_Reset();
	}
	else{echo Expired_Link."<br/>";	}
}

$myframe->close_box("panel");
///////////////////////////////////////////////////////////////////////////////////////////
function if_POST()
{
//if submit performed, then manipulate

	include_once '../common/pframe.php';
	$myframe = new pframe ();
	
	if($_POST['doit'])
	{
		if($_POST['pass1'] == $_POST['pass2'] ) //passwords matching and not empty
		{
			if($_POST['pass1'] != "") // password not empty
			{
				$ID="";
// using session for maximum protection
				if($_SESSION["GID"]){$ID = $_SESSION["GID"];}

//the user is sent as a hash code so the link can not be modified by hackers
				else{$ID = $myframe->bring_USER_by_hash($_REQUEST['x']);} 
// bring the user_id by searching in the hashes of users one by one until it's found		
				
				$md5 = md5($_POST['pass1']);
				$sql = "update user set user_password='{$md5}' where user_id='{$ID}' ";
//perform update password process for this user
				if(@cmd($sql))
				{echo Password_Reset_OK."<br/>"; unset($_SESSION['reset']);}
			}
		}
		else{
			echo passwords_dont_match; // message says the passwords don't match
		}
	}
}

function Password_Reset()
{
//HTML form including appropriate elements [2 text boxes + submit button]
	?>
	<form name="resetpassword_form" method="post" action="<?=$_SERVER['PHP_SELF']?>?<?=$_SERVER['QUERY_STRING']?>">
	
		<table width="50%" style="width:500px;border:1px #ccc solid; border-radius:10px; margin:20px auto;padding:30px">
		
			<tr style="height:60px;"><td><label><?=New_Password?> *</label></td>
				<td><input type="password" name="pass1"  style="width:200px;" autocomplete="off"></td>	</tr>
	
			<tr><td><label><?=Confirm_New_Password?> *</label></td>
				<td><input type="password" name="pass2"  style="width:200px;" autocomplete="off"></td>	</tr>

	
			<tr><td colspan="2" style="text-align: center">
				<input type="submit" name="doit" value="<?=Reset_Password?>" style="height:35px;font-weight:bold;font-size:14pt;margin-top:20px;cursor:pointer">	</td>	</tr>
	
		</table>
	</form>
	<?
}
11) ../common/search.php



Displays search results. Highlights the found result between suggested ones


<?php
session_start();
require_once '../db/mysqlcon.php';
include_once '../common/pframe.php';
require_once '../lang/lang_'.$GLOBALS['lang'].'.inc';
$myframe=new pframe();
$myframe->header(View_Ads);


?><script type="text/javascript" src="../cms/jquery.highlight.js"></script><? 
    
    if($_REQUEST['q']){
	    $myframe->open_box("withTree", Search_Results,"panel", $adding);

	    $i=0;
	    $res = $_SESSION['search_result']['val']; //print_r($_SESSION['search_result']['val']);//echo "<br/><br/>".$_SESSION['search_result']['val'][0]. $_SESSION['search_result']['tbl'][0].$_SESSION['search_result']['span'][0];

	    $arr_tbl = $_SESSION['search_result']['tbl'];//Table of each result. came from the page search_sql.php
	    $span_tbl = $_SESSION['search_result']['span'];
	    $ttl= $GLOBALS['lang'] == 'ar'?'title_ar':'title_en';
	    
	    foreach($res as $val){
	    	$v='c';
	    	//SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = 'hadara' AND TABLE_NAME = 'product'  limit 3

	    	// bringing NID of the element accoding to its TITLE

	    	// + building the link which the result take you to.

	    	$id_col=get_data_in("SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = '".DB_NAME."' AND TABLE_NAME = '".$arr_tbl[$i]."'   limit 1 ", "COLUMN_NAME");
	    	$ttl_col=get_data_in("SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = '".DB_NAME."' AND TABLE_NAME = '".$arr_tbl[$i]."' and COLUMN_NAME like '%{$ttl}%'  limit 1 ", "COLUMN_NAME");
	    	$NID=get_data_in("SELECT {$id_col} FROM {$arr_tbl[$i]} WHERE {$ttl_col}='{$val}' ", $id_col);
	    	
	    	$page=$arr_tbl[$i]=='category'?$arr_tbl[$i].".php":$arr_tbl[$i]."s.php";
	    	
	    	?><p class="p_res"> > <a class="search_result_a"  style="<? if($_REQUEST['q'] == $val){echo "color:#7b03bc;font-weight:bold;border:1px #000 solid;padding:4px 10px";}?>"
	    	 href="../pages/<?=$page?>?v=<?=$v?>&NID=<?=$NID?>"><?=$val?></a><?=$span_tbl[$i]?></p>	<?
	    	$i++;
	    }
	    
	    $myframe->close_box("panel");
	    
	    ?>
	    <script>
	    </script>
	    <? 
    }
    
?>
11) ../common/search_sql.php



Performs the sql query in nine tables in our DB (Category, Product, News, Album, Picture, Video, Service, Job, FAQ)
in 4 fields : Title (AR + EN) & Description (AR + EN)
And returns the result as session arrays


<?php
ob_start();
session_start();
require_once '../db/mysqlcon.php';
include_once '../common/pframe.php';
$myf=new pframe();

if($_GET['key']) $key=$_GET['key'];
elseif($_GET['typeahead']) $key=$_GET['typeahead'];

//collect many keywords to search
$pieces = explode(" ", $key);
$key="";
foreach($pieces as $val){
	$key .= $val."|";
}
$key=rtrim($key, "|"); //echo $key;

// search keywords through these tables : product, news, album, picture, videos, service, job, faq, category
$sql = "
(Select prod_title_ar as ttl_ar,prod_title_en as ttl_en,prod_desc_ar as desc_ar,prod_desc_en as desc_ar ,'product' as tbl from product where lower(Concat(prod_title_ar, '', prod_title_en, '', prod_desc_ar, '', prod_desc_en, '' )) REGEXP '$key')
UNION
(Select news_title_ar,news_title_en,news_desc_ar,news_desc_ar ,'news' as tbl from news where lower(Concat(news_title_ar, '', news_title_en, '', news_desc_ar, '', news_desc_en, '' )) REGEXP '$key')
UNION
(Select album_title_ar,album_title_en,'-','-' ,'album' as tbl from album where lower(Concat(album_title_ar, '', album_title_en, '')) REGEXP '$key')
UNION
(Select vid_title_ar,vid_title_en,vid_desc_en,vid_desc_en ,'video' as tbl from video where lower(Concat(vid_title_ar, '', vid_title_en, '', vid_desc_ar, '', vid_desc_en, '')) REGEXP '$key')
UNION
(Select srv_title_ar,srv_title_en,srv_desc_ar,srv_desc_en ,'service' as tbl from service where lower(Concat(srv_title_ar, '',srv_title_en, '', srv_desc_ar, '', srv_desc_en, '')) REGEXP '$key')
UNION
(Select pic_title_ar,pic_title_en,'-','-' ,'picture' as tbl from picture where lower(Concat(pic_title_ar, '',pic_title_en, '')) REGEXP '$key')
UNION
(Select job_title_ar,job_title_en,job_desc_ar,job_desc_en ,'job' as tbl from job where lower(Concat(job_title_ar, '', job_title_en, '', job_desc_ar, '', job_desc_en, '')) REGEXP '$key')
UNION
(Select faq_title_ar,faq_title_en,faq_desc_ar,faq_desc_en ,'faq' as tbl from faq where lower(Concat(faq_title_ar, '',faq_title_en, '', faq_desc_ar, '', faq_desc_en, '')) REGEXP '$key')
UNION
(Select cat_title_ar,cat_title_en,'-','-' ,'category' as tbl from category where lower(Concat(cat_title_ar, '',cat_title_en, '')) REGEXP '$key') order by 1";

 //echo "<br/>".$sql."<br/>";

$lang = $GLOBALS['lang'] == 'ar' ? 'ar' : 'en';
$ttl_lang = 'ttl_'.$lang;
$query=table($sql);
$res = array();
$span_tbl = array();
$arr_tbl = array();
$i=0;

//collect results In arrays

while($row=mysql_fetch_assoc($query))
{
	$res[$i] = $row[$ttl_lang];
	$span_tbl[$i] = ' <span style="color:#ccc">('.$row['tbl'].')</span>';
	$arr_tbl[$i] = $row['tbl'];
	$i++;
}

if($_REQUEST['typeahead']){

// using session vars to pass results to view page

	$_SESSION['search_result']['val'] = $res;
	$_SESSION['search_result']['tbl'] = $arr_tbl; //print_r($_SESSION['search_result']);
	$_SESSION['search_result']['span'] = $span_tbl;
	header("location: ../common/search.php?q={$_REQUEST['typeahead']}");
}
echo json_encode($res);

?>
11) ../common/forgot_password.php



The password resetting wizard, which enables the user to get  a "secure one-time link" by email, this link allows him to securely access the reset password form.


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


11) ../common/signin.php



Displays the login form and redirects the data that the user entered to the secreg.php page which validates and authenticates users …


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
 
Third     ../dbfolder


 
13)../db/mysqlcon.php
 
 
Contains all functions required to connect with MYSQL database, and functions that bring data (multi rows/one row). In addition, contains some security  functions.
 
 
<?php
session_start();
//declare global variables
global $con;
global $MyErrStr;
global $wwwURL;
global $lang;


require_once '../db/settings.php';
$conA=new mysql_connection_A();
$con=$conA->do_connect();

//MYSQL DB Functions________________________________________________________________________

function cmd($sql, $db="con"){

//cmd means 'COMMAND'. This function executes an sql query that does not have results to return
//if it was a deleting statement, return an appropriate message of deleting or query succeeding  or else an error message
 
	//echo $sql;
	if (mysql_query($sql, $GLOBALS[$db])) {
		if (eregi("^delete", $sql)){
			return $GLOBALS['MyErrStr']->RowDeleted;
		}else {
			return $GLOBALS['MyErrStr']->DBOK;	
		}
	}else {
		return $GLOBALS['MyErrStr']->DBERR;
	}
}

function table($sql, $db="con"){

// executes an sql query and returns result rows as table 
	//echo $sql;
	$res=mysql_query($sql, $GLOBALS[$db]);
	if ($res) {
		return $res;
	}else {
		return false;
	}	
}

function row($sql, $db="con"){

//returns a single result row according to an sql statement
	$res=mysql_query($sql, $GLOBALS[$db]);
	if ($res) {
		return mysql_fetch_array($res);
	}else {
		return false;
	}	
}

function get_data_in($search_sqlstatement,$dfname, $db="con"){

//returns a single data cell according to an sql statement
	$xres=mysql_query( "$search_sqlstatement", $GLOBALS[$db]);
	if ($xres) {
		$xrow=mysql_fetch_array($xres);
		$requierd_data=$xrow["$dfname"];
		return $requierd_data;
	}else {
		return false;	
	}
}





function get_month_name($mid)
{
//returns the month's name by it's order
	$maaa=array('January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December');
	return $maaa[$mid-1];
}

//Admin Functions___________________________________________________________________________

function showedit($lnk, $showTXT=false, $tttxt="Edit", $users=array("A")){//print_r($users);

//displays a tool to enable the user do edit operation, if he had full control permission "A" or the class's managing permission.
	if (!$showTXT) {$tttxt="";}
	if (user_has_permission($users)) {
		return  '<a class="edit_tool_ico" href="'.$lnk.'"><img style="" src="../images/edtimg.png" alt="Edit" /> <span>'.$tttxt.'</span></a>';
	}
	return false;
}

function showdelet($lnk, $showTXT=false, $tttxt="Delete", $users=array("A")){//print_r($users);

//displays a tool to enable the user do delete operation, if he had full control permission "A" or the class's managing permission.
	if (!$showTXT) {$tttxt="";}
	if (user_has_permission($users)) {
		return  '<a  class="edit_tool_ico" href="'.$lnk.'"><img style="" src="../images/dltimg.png" alt="Delete" /> <span>'.$tttxt.'</span></a>';
	}
	return false;
}

function get_pms()
{
//collects the GET parameters and join them as a single string var
	foreach ($_GET as $pmk => $pmv) {
		if ($pmk!="v" && $pmk!="lang" && $pmk!="NID" && $pmk!="noframe"){
		$strpms[]=$pmk."=".$pmv;	
		}
	}
	$strpms=@join("&",$strpms);
	return $strpms;
}
////////USER PERMISSION//////////////////////////////////////
function user_has_permission($users_types){

//this is the most important function in terms of permissions.
//it checkes whether the user is logged in, and has one or more permissions in his session array variable of privileges

	if (in_array("N", $users_types)) {
			return true;
	}

	if (session_is_registered("GID") && $_SESSION["PRIVS"]) {
		foreach ($_SESSION["PRIVS"] as $priv)
			if (in_array($priv, $users_types)) {
				return true;
		}
	}
	return false;
}



function IS_SECURE($priv=""){

//and this is the second most important one. If makes sure that the user is logged in and has enough permissions to continue on this page, and priv "A" (full control) is checked also. However, if the user does not have and, we force to leave the page and redirect him to the signin page
	$p=array();
	$p[0]="A";
	$p[1]=$priv;
	if (!user_has_permission($p)){@header("location:../common/signin.php?soro=x"); exit();}
}

//Error Alerts__________________________________________________________________________________
$MyErrStr=new ErrStr();
class ErrStr
{
//these variables are very clear and can explain every thing about this class to know it's contents
//the error messages returnd by db and others, will find a match here and will be displayed as a formatted message div that has CSS class to modify
	public $CannotMasking=-10;
	public $NotVerified=-9;
	public $CannotResize=-8;
	public $ErrFileType=-7;
	public $CannotUpload=-6;
	public $ReRegitered=-5;
	public $CancelRegister=-4;
	public $RegisteredOK=-3;
	public $RowDeleted=-2;
	public $DBOK=-1;
	public $DBERR=0;
	public $FillAllRequierd=1;
	public $FillAll=2;
	public $DataIsExist=3;
	public $IsNotRegistered=4;	
	public $Uploded=5;
	public $Commented=6;
	public $InvalidTel=7;
	public $InvalidFax=8;
	public $InvalidMobile=9;
	public $InvalidMail=10;
	
	function Show($ErrStrHandle){
		//Use these two varibles to format message
		$strstyle='<div class="err_msg">';
		$strendstyle="</div>";
		$exc='<img class="err_msg_icon" src="../images/exs.gif" />';
		
		switch ($ErrStrHandle) {
			case -10:
				return $strstyle.$exc.CannotMasking.$strendstyle;
				break;
			case -9:
				return $strstyle.$exc.NotVerified.$strendstyle;
				break;
			case -8:
				return $strstyle.$exc.CannotResize.$strendstyle;
				break;
			case -7:
				return $strstyle.$exc.ErrFileType.$strendstyle;
				break;
			case -6:
				return $strstyle.$exc.CannotUpload.$strendstyle;
				break;
			case -5:
				return $strstyle.$exc.ReRegistered.$strendstyle;
				break;
			case -4:
				return $strstyle.$exc.CancelRegister.$strendstyle;
				break;
			case -3:
				return $strstyle.$exc.RegisteredOK.$strendstyle;
				break;
			case -2:
				return $strstyle.$exc.RowDeleted.$strendstyle;
				break;
			case -1:
				return $strstyle.$exc.DBOK.$strendstyle;
				break;
			case 0:
				return $strstyle.$exc.DBERR.$strendstyle;
				break;
			case 1:
				return $strstyle.$exc.FillAllRequierd.$strendstyle;
				break;
			case 2:
				return $strstyle.$exc.FillAll.$strendstyle;
				break;
			case 3:
				return $strstyle.$exc.DataIsExist.$strendstyle;
				break;
			case 4:
				return $strstyle.$exc.IsNotRegistered.$strendstyle;
				break;	
			case 5:
				return $strstyle.$exc.Uploded.$strendstyle;
				break;
			case 6:
				return $strstyle.$exc.Commented.$strendstyle;
				break;		
			case 7:
				return $strstyle.$exc.InvalidTel.$strendstyle;
				break;
			case 8:
				return $strstyle.$exc.InvalidMob.$strendstyle;
				break;
			case 9:
				return $strstyle.$exc.InvalidFax.$strendstyle;
				break;
			case 10:
				return $strstyle.$exc.InvalidMail.$strendstyle;
				break;		
			default:
				return false;
				break;
		}
	}
}

}
?>
14)../db/settings.php
Containts Database connection parameters
<?php
//General Settings
 
// String path must end with trailing slash. 
$wwwURL="";
 
// Default Language 'ar'
isset($_GET["lang"])?$GLOBALS["lang"]=$_GET["lang"]:$GLOBALS["lang"]="ar";
 
//Connection Settings
class mysql_connection_A {
	private $myhost="localhost";//Server Name
	private $myuser="root";//Database User Name
	private $mypass="12345";//Password
	private $mydb="db1";//Database Name
	
//configure the mysql connection with those previously mentioned attributes
	function do_connect(){$mycon=mysql_connect($this->myhost,$this->myuser,$this->mypass);mysql_select_db($this->mydb,$mycon);mysql_query("SET NAMES 'utf8'",$mycon);mysql_query('SET CHARACTER SET utf8',$mycon);return$mycon;}
}
?>
Fourth ../lang Folder


15)  ../lang/lang_ar.inc



Lang_X.inc  is a php file contains all constant variables for all labels in the project. The X letter changes when the $GLOBALS['lang'] var changes, so the site become in EN|AR. 
<?php
define("PROJECT_TITLE","أم آند أي للبرمجيات");
define("Main","البداية");
define("News","الأخبار");
define("About","عن الشركة");
define("OurAccounts","حساباتنا");
define("Locations","الموقع الجغرافي");
define("JoinUs","ساهم معنا");
define("Gallery","معرض الصور");
define("ContactUs","اتصل بنا");
define("MailList","القائمة البريدية");
define("Members_Login","دخول الأعضاء");
define("Products","المنتجات");
define("Categories","التصنيفات");
define("Adv","الإعلانات");
define("Links","روابط مفيدة");
define("Maintenance","طلب صيانة");
 
define("Back","عودة");
define("Back_to_home","عودة إلى الرئيسية");
define("user_name","اسم المستخدم");
define("password","كلمة المرور");
define("Re-Type","أعد كتابة");
define("Sign_in","تسجيل دخول");
define("Edit","تحرير");
define("Delete","حذف");
define("nowandate", date("Y-m-d H:i:s"));
define("curdatetime", date("d-m-Y H:i:s"));
define("date", date("Y-m-d"));
define("delete_question", "هل تريد بالتأكيد حذف العنصر المسمى: %s ؟");
define("Save", "حفظ");
define("Send","إرسال");
define("more","المزيد");
define("NID","ID");
define("Hidden","إخفاء (لا يمكن للزوار معاينة هذا السجل)");
define("LastUpdate","آخر تحديث");
define("NDate","آخر تحديث");
define("Visits","عدد الزيارات");
define("lang","اللغة");
define("Related_Pages","صفحات ذات صلة");
define("Visitors","Visits");
define("View","تحميل الملف");
define("View_Orig_Page","عرض الصفحة الأصلية");
define("Home","الرئيسية");
define("Articles","المقالات");
define("Read_More","المزيد...");
define("Remove_Pic","حذف الصورة");
define("Comments","التعليقات");
define("aLinks","مراجع وروابط");
define("aPhotos","لقطات وصور");
define("admin_controls","لوحة التحكم");
define("Downlods","قسم التحميل");
define("Yes","نعم");
define("No","لا");
define("insrt_ur_eml","اشترك بالقائمة البريدية");
 
 
define("Visitor_EMail","البريد الإلكتروني");
 
define("CannotMasking", "فشل في إنشاء القناع!");
define("NotVerified", "يرجى إدخال الرمز بشكل صحيح أدناه!");
define("CannotResize", "فشل في تغيير حجم الصورة!");
define("ErrFileType", "Invalid File Type!");
define("CannotUpload", "خطأ أثناء رفع الملف!");
define("ReRegistered", "شكراً لك! تم إعادة تفعيل إشتراكك بنجاح..");
define("CancelRegister", "تم إلغاء إشتراكك بنجاح! شكراً لك.");
define("RegisteredOK", "شكراً لك! تم تسجيلك بنجاح...");
define("RowDeleted", "تم الحذف بنجاح!");
define("DBOK", "تم حفظ البيانات بنجاح!");
define("DBERR", "نأسف لحصول خطأ غيرمتوقع أثناء الكتابة إلى قاعدة البيانات! يرجى إعادة المحاولة لاحقاً...");
define("FillAllRequierd", "يجب إدخال جميع الحقول المطلوبة (*) !");
define("FillAll", "يجب إدخال جميع الحقول!");
define("DataIsExist", "البيانات موجودة.. يرجى إدخال بيانات جديدة!");
define("IsNotRegistered", "المعذرة! أنت غير مسجل لدينا سابقاً.");
define("Uploded", "تم رفع الملف بنجاح!");
define("Commented", "تم استقبال تعليقكم وشكرا لتواصلكم!");
define("InvalidTel", "الرجاء إدخال رقم<u>هاتف</u>صحيح! فقط الأرقام مسموحة..");
define("InvalidMob", "الرجاء إدخال رقم<u>فاكس</u>صحيح! فقط الأرقام مسموحة.");
define("InvalidFax", "الرجاء إدخال رقم<u>موبايل</u>صحيح! فقط الأرقام مسموحة.");
define("InvalidMail", "الرجاء إدخال<u>بريد إلكتروني</u>صحيح!");
define("You_can_contact_us_directly_by_filling_this_application", "للتواصل معنا مباشرة, الرجاء ملىء الاستمارة التالية!");
 
 
define("This_helps_us_to_prevent_automatic_registration","الرجاء إدخال المحارف الظاهرة أدناه في الصورة (غير حساس لحالة الأحرف)");
define("No_Rows_Selected","لايوجد نتائج");
 
define("Set_Def_Pic","تعيين الصور الإفتراضية");
//VCOMMENTS >>>
define("Search","بحث");
define("VComments","تعليقات الزوار");
define("VComment_Text","نص التعليق");
define("v_delete_question", "هل تريد بالتأكيد حذف التعليق: %s ؟");
define("Visitor_Name","الاسم");
//VCOMMENTS <<<
 
//NAVIGATOR >>>>>>>>>>>>>>>
define("dir", "rtl");
define("align","right");
define("r_align","left");
define("Next", "التالي");
define("Last", "الأخير");
define("First", "الأول");
define("Previous", "السابق");
define("rows_found", "نتيجة");
define("Page", "صفحة");
define("of", "من");
//END NAVIGATOR <<<<<<<<<<<
 
/////////////////////////////////////////////////////////////////////////////
 
define("Remember_Me","ابق متصلا على هذا الكمبيوتر");
define("Thanks_Your_email_was_sent","تم ارسال رسالتك بنجاح !!");
define("Sorry_Please_try_again_later","نأسف لحدوث خطأ .. الرجاء المحاولة لاحقاً");
/*****   mail list   *****/
define("Email_already_exists","ايميلك مسجل مسبقا");
define("Added_successfully","اضيف ايميلك بنجاح");
define("Adding_error","لقد حدث خطأ,حاول لاحقا");
 
define("Change_Password","تغيير كلمة السر");
define("Sign_UP","تسجيل حساب جديد");
define("Sign_In","تسجيل دخول");
define("Sign_Out","تسجيل خروج");
 
//Side Bar >>>
define("Our_Services","خدماتنا");
define("Our_Customers","زبائننا");
define("Download","التحميل");
define("FAQ","الأسئلة الشائعة");
define("Albums","ألبومات الصور");
define("Jobs","فرص عمل");
define("Pictures","صور الالبوم");
//Side Bar <<<
 
define("Latest_Products","آخر المنتجات");
define("Latest_News","آخر الأخبار");
define("All_Products","جميع المنتجات");
define("All_News","جميع الأخبار");
 
//Control_Panel >>>
define("Control_Panel","لوحة التحكم");
define("Users_Management","إدارة المستخدمين");
//Control_Panel <<<
 
16)  ../lang/pages_ar.inc



Contains constant variables of database columns. These are used by dataset and editor displayers. It also contains the labels (edit-add-delete-view) foreach database class/table.


<?php
define("Edit_x","تعديل");
define("Add_x","إضافة جديد");
define("View_x","جميع ");
define("Del_x","حذف");
define("ID_x","المعرف");
 
//ADS>>>
define("Ads","الاعلانات");
define("ads_id","معرف الإعلان");
define("ads_title_x","ads_title_ar");
define("ads_title_ar","الاعلان-عربي");
define("ads_title_en","الإعلان-EN");
define("ads_start","تاريخ البدء");
define("ads_end","تاريخ الانتهاء"); 
define("ads_link","الرابط");
define("ads_pic","صورة");
define("NDate","تاريخ الاضافة");
//END ADS<<<<<<<<<<<<<<<<<<<<<<<
 
//ALBUM>>>
define("Add_Album","إضافة ألبوم");
define("Edit_Album","تحرير الالبوم");
define("Delete_Album","حذف الالبوم");
define("View_Album","عرض الألبوم");
 
define("Album","الالبومات");
define("album_id","معرف الالبوم");
define("album_title_x","album_title_ar");
define("album_title_ar","عنوان الالبوم-عربي");
define("album_title_en","عنوان الالبوم-EN");
define("album_pic","album_title_ar");
define("NVnom","عدد الزيارات");
//END ALBUM<<<<<<<<<<<<<<<<<<<<<<<
 
//BUY_CONTRACT>>>
define("BConts","عقود البيع");
define("bcont_dist","الموزع");
define("bcont_cust","الزبون");
define("NSerial","التسلسلي");
define("bcont_prod","المنتج/المنتجات");
define("bcont_discount","التخفيض");
define("bcont_payment","المدفوع");
define("bcont_seen","تمت قراءته");
define("bcont_license","الترخيص");
//END BUY_CONTRACT<<<<<<<<<<<<<<<<<<<<<<<
 
//CATEGORY>>>
define("cat_id","معرف التصنيف");
define("cat_title_ar","التصنيف-عربي");
define("cat_title_en","التصنيف-EN");
define("cat_title_x","cat_title_ar");
//END CATEGORY<<<<<<<<<<<<<<<<<<<<<<<
 
//CONTACTUS>>>
define("cu_id","المعرف");
define("cu_name","شركتنا");
define("cu_mobile","جوال");
define("cu_job","عمل شركتنا");
define("cu_tel","هاتف");
define("cu_company","الرسالة");
define("cu_email","أيميل"); 
define("cu_pic","صورة");
//END CONTACTUS<<<<<<<<<<<<<<<<<<<<<<<
 
//FAQ>>>
define("FAQ","الاسئلة الشائعة");
define("faq_title_x","faq_title_ar");
define("faq_title_ar","السؤال");
define("faq_title_en","السؤال-EN");
define("faq_desc_ar","شرح-عربي");
define("faq_desc_en","شرح-EN");
//END FAQ<<<<<<<<<<<<<<<<<<<<<<<
 
//JOB>>>
define("job_title_x","job_title_ar");
define("job_title_ar","فرصة العمل-عربي");
define("job_title_en","فرصة العمل-EN");
define("job_desc_ar","الوصف");
define("job_desc_en","الوصف-EN");
//END JOB<<<<<<<<<<<<<<<<<<<<<<<
 
//MAINTENANCE_CONTRACT>>>
define("mconts","عقود الصيانة");
define("mcont_serial","تسلسلي صيانة");
define("mcont_dist","الموزع");
define("mcont_cust","الزبون");
define("bcont_serial","رقم عقد الشراء");
define("mcont_desc","وصف");
define("mcont_type","نوع العقد");
define("mcont_seen","تمت قراءته");
define("mcont_status","الحالة");
//END MAINTENANCE_CONTRACT<<<<<<<<<<<<<<<<<<<<<<<
 
//NEWS>>>
define("Add_News","إضافة خبر");
define("Edit_News","تعديل خبر");
define("Delete_News","حذف خبر");
define("View_News","الأخبار");
 
 
define("news_title_x","news_title_ar");
define("news_title_ar","الخبر");
define("news_title_en","الخبر-EN");
 
define("news_desc_x","news_desc_ar");
define("news_desc_ar","الوصف");
define("news_desc_en","الوصف-EN");
 
define("news_text_x","news_text_ar");
define("news_text_ar","نص مطول");
define("news_text_en","نص مطول-EN");
define("news_pic","صورة");
//END NEWS<<<<<<<<<<<<<<<<<<<<<<<
 
//PICTURE>>>Add_Pic
define("Add_Pic","اضافة صورة");
define("Edit_Pic","تحرير الصورة");
define("Delete_Pic","حذف االصورة");
define("View_Pic","عرض");
 
define("pic_album","الالبوم");
define("pic_title_x","pic_title_ar");
define("pic_title_ar","عنوان الصورة");
define("pic_title_en","عنوان الصورة-EN");
define("pic_ext","صورة");
define("pic_video","رابط فديو");
define("pic_is_main","صورة الالبوم؟");
//END MSG<<<<<<<<<<<<<<<<<<<<<<<
 
//PRODUCT>>>
define("Add_Products","إضافة منتج");
define("Edit_Products","تعديل منتج");
define("Delete_Products","حذف منتج");
define("View_Products","المنتجات");
define("View_Downloads","التحميل");
 
define("prod_title_x","prod_title_ar");
define("prod_title_ar","المنتج");
define("prod_title_en","المنتج-EN");
define("prod_cat","التصنيف");
 
define("prod_desc_x","prod_desc_ar");
define("prod_desc_ar","وصف");
define("prod_desc_en","وصف-EN");
 
define("prod_text_x","prod_text_ar");
define("prod_text_ar","شرح");
define("prod_text_en","شرح-EN");
 
define("prod_pic","الصورة");
define("prod_only_dw","برنامج مساعد؟");
define("prod_exe","برنامج تنفيذي");
define("prod_price","السعر");
 
//END PRODUCT<<<<<<<<<<<<<<<<<<<<<<<
 
//UCOMMENT>>>
define("ucom_user","المستخدم");
define("ucom_text","التعليق");
define("ucom_from_ip","عنوان آي بي");
define("ucom_reported","عدد الشكاوي");
//END UCOMMENT<<<<<<<<<<<<<<<<<<<<<<<
 
//USER>>>
 
define("Add_Users","إضافة");
define("Edit_Users","تعديل ");
define("Delete_Users","حذف ");
define("View_Users","جميع المستخدمين");
 
define("user_name","اسم المستخدم");
define("user_password","كلمة السر");
define("user_email","الايميل");
define("user_phone","هاتف");
define("user_address","عنوان");
define("user_country","الدولة");
define("user_city","المدينة");
define("user_birthyear","سنة الميلاد");
define("user_pic","صورة");
define("user_cat","نمط المستخدم");
define("Customer_Name","الزبون");
define("Distributor_Name","الموزع");
define("Employee_Name","الموزع");
define("Add_Customer","اضافة زبون");
define("View_Customer","الزبون");
 
define("employee","employee");
define("customer","customer");
define("distributor","distributor");
define("admin","admin");
//END USER<<<<<<<<<<<<<<<<<<<<<<<
 
//USER_CONFIG>>>
define("Configs","الاعدادات");
define("usrconf_user","المستخدم");
define("usrconf_color","اللون");
//END USER_CONFIG<<<<<<<<<<<<<<<<<<<<<<<
 
//VIDEO>>>
define("vid_title_ar","عنوان");
define("vid_title_en","عنوان-EN");
define("vid_desc_ar","وصف");
define("vid_desc_en","وصف-EN");
define("vid_link","الرابط");
//END VIDEO<<<<<<<<<<<<<<<<<<<<<<<
 
//SERVICE>>>
define("Services","الخدمات");
define("Edit_service","تعديل");
define("Add_service","اضافة خدمات");
define("View_service","خدماتنا");
define("Del_service","حذف خدمة");
define("srv_id","المعرف");
define("srv_title_ar","العنوان");
define("srv_title_en","العنوان-EN");
define("srv_desc_ar","الوصف");
define("srv_desc_en","الوصف-EN");
define("NVNom","عدد الزيارات");
define("NDate","تاريخ الاضافة");
//END SERVICE<<<<<<<<<<<<<<<<<<<<<<<
 
//groups>>>
define("Edit_groups","تعديل المجموعات");
define("Add_groups","اضافة مجموعة");
define("View_groups","مجموعات المستخدمين");
define("Del_groups","حذف مجموعة");
define("group_id","معرف");
define("group_name","اسم المجموعة");
//END groups<<<<<<<<<<<<<<<<<<<<<<<
 
//user_groups>>>
define("Edit_user_groups","تعديل مجموعات المستخدم");
define("Add_user_groups","اضافة مجموعة للمستخدم");
define("View_user_groups","عرض مجموعات المستخدم");
define("Del_user_groups","حذف مجموعة");
define("user_id","المستخدم");
define("user_group_id","مجموعة");
//END user_groups<<<<<<<<<<<<<<<<<<<<<<<
 
//user_privs>>>
define("Edit_user_privs","تعديل مجموعات المستخدم");
define("Add_user_privs","اضافة مجموعة للمستخدم");
define("View_user_privs","عرض مجموعات المستخدم");
define("Del_user_privs","حذف مجموعة");
define("user_id","المستخدم");
define("user_priv","مجموعة");
define("user_grant","يستطيع منحا لمستخدم آخر؟");
//END user_privs<<<<<<<<<<<<<<<<<<<<<<<
 
//user_group_privs>>>
define("Edit_user_group_privs","تعديل صلاحيات المجموعة");
define("Add_user_group_privs","اضافة صلاحية للمجموعة");
define("View_user_group_privs","صلاحيات المجموعة");
define("Del_user_group_privs","حذف صلاحية من المجموعة");
define("group_id","المجموعة");
define("priv","صلاحية");
//END user_group_privs<<<<<<<<<<<<<<<<<<<<<<<
 
//MSG>>>
define("BlockedIPs","العناوين المحجوبة");
//END MSG<<<<<<<<<<<<<<<<<<<<<<<
 
Fifth  ../obj   Folder

NOTE: We are going to mention this information ONLY ONCE, because it goes on all < .class> files .

This folder contains all php classes (<dbTable>.class.php) for all database tables. 

Class files can beconsidered as a php programattic joint between DB and PHP

Class files let the dataset understand the structure for every database table, to fill its data when making (insert, update, delete, select) operations .

Class files can also contain events triggered by dataset (onStart, onRemoveRow…). 

Each db column goes here as a two dimentional array, and each key is an attribute for that column. 

Attributes that can go inside a given column (if necessary):

'name' : name of the column to search for
'type' : field type. Takes values: varchar, ID, text, file, int
'caption' : another name for the field
'control' : this one determines the control displayed be the editor when adding or editing the object. Values: none, text, fkey, list, date
'options': options of the 'control'=>'list'
wich is displayed as a <select> HTML element. These options can go like this:
'options'=>array('ID1'=>'value1', 'key2'=>'val2', 'emp'=>'employee')
'ftbl' :FK table  + 'fTitle' : FK field + 'fID' : FK ID + 'fFilter':the where clause to filter FKs
'required' : means does not accept empty values. It takes the value of required
'filetypes' : extensions that are only allowed to upload as an attachement. Eg. 'jpg|gif|png' or 'pdf|exe|lrec'
'resize' :  whether to resize an uploaded picture or not 
 'prefix' : file prefix like: 'PROD_' 
 'sizes' : a 2D array like this: 
array('thumb'=>array('p'=>'B', 'w'=>215, 'h'=>125))
'value' : the default value if we insert an empty one
'format' : if 'control'=>'date' then we can format the date
 'withtime' : true|false

So, all of this goes for every and each single class file, and no need to explain those foreach class.

And we will let you with our .class files for now:



17) ../obj/ads.class.php


<?php
include_once '../cms/dataset.php';
include_once '../lang/pages_'.$GLOBALS['lang'].'.inc';


class Ads extends JSDataSet
{
    public $ads_id=array('name'=>'ads_id', 'type'=>'ID', 'caption'=>'ads_id', 'control'=>'none');

    public $ads_title_ar=array('name'=>'ads_title_ar', 'type'=>'varchar', 'caption'=>'ads_title_ar', 'control'=>'text', 'required'=>'required' );

    public $ads_title_en=array('name'=>'ads_title_en', 'type'=>'varchar', 'caption'=>'ads_title_en', 'control'=>'text', 'required'=>'required' );

    public $ads_pic=array('name'=>'ads_pic', 'type'=>'file', 'filetypes'=>'jpg|png|bmp|gif', 'prefix'=>'ADS_', 'caption'=>'ads_pic', 'control'=>'file', 'view'=>'image', 'resize'=>true, 'sizes'=>array('thumb'=>array('p'=>'B', 'w'=>200)));

    public $ads_link=array('name'=>'ads_link', 'type'=>'text', 'caption'=>'ads_link', 'control'=>'textarea' );
    
    public $ads_start=array('name'=>'ads_start', 'type'=>'varchar', 'caption'=>'ads_start', 'control'=>'date', 'value'=>nowandate, 'format'=>'%Y-%m-%d %H:%M:%S', 'withtime'=>'true');

    public $ads_end=array('name'=>'ads_end', 'type'=>'varchar', 'caption'=>'ads_end', 'control'=>'date', 'value'=>nowandate, 'format'=>'%Y-%m-%d %H:%M:%S', 'withtime'=>'true');

    public $NDate=array('name'=>'NDate', 'type'=>'varchar', 'caption'=>'NDate', 'control'=>'none', 'value'=>nowandate, 'format'=>'%Y-%m-%d %H:%M:%S', 'withtime'=>'true', 'NTitle'=>'');

    public $tblname="ads";

    public function onStart(){
    	 
    	$this->ads_pic['ext'] = get_data_in("select ads_pic from {$this->tblname} where ads_id='{$this->ads_id["value"]}'", "ads_pic");
    	$this->NDate['NTitle']=$this->{ads_title_x}['value'];
    }

}
18) ../obj/album.class.php


<?php
include_once '../cms/dataset.php';
include_once '../lang/pages_'.$GLOBALS['lang'].'.inc';


class Album extends JSDataSet
{
    public $album_id=array('name'=>'album_id', 'type'=>'ID', 'caption'=>'album_id', 'control'=>'none', 'ftbl'=>'', 'fTitle'=>'', 'fID'=>'' );

    public $album_title_ar=array('name'=>'album_title_ar', 'type'=>'varchar', 'caption'=>'album_title_ar', 'control'=>'text', 'required'=>'required');

    public $album_title_en=array('name'=>'album_title_en', 'type'=>'varchar', 'caption'=>'album_title_en', 'control'=>'text', 'required'=>'required' );

    public $NVNom=array('name'=>'NVNom', 'type'=>'int', 'caption'=>'NVNom', 'control'=>'none' , 'value'=>0);

    public $NDate=array('name'=>'NDate', 'type'=>'varchar', 'caption'=>'NDate', 'control'=>'none', 'value'=>nowandate, 'format'=>'%Y-%m-%d %H:%M:%S', 'withtime'=>'true', 'NTitle'=>'');

    public $tblname="album";


	
	public function onStart()
	{
		$this->lang['value'] = $GLOBALS['lang'];
		$this->NDate['NTitle']=$this->{albums_title_x}['value'];
	}
	
	public function onRemove($res)
	{

//on delete album dascade for it's child pictures

		if ($res==$GLOBALS['MyErrStr']->RowDeleted){
			include_once '../obj/picture.class.php';
			$pic=new Picture();
			$pic->RemoveRows(" pic_album = '{$this->NID['value']}'");
		}
	}}
 
19) ../obj/blocked_ip.class.php


<?php
include_once '../cms/dataset.php';
include_once '../lang/pages_'.$GLOBALS['lang'].'.inc';


class Blocked_ip extends JSDataSet
{
	public $NID=array('name'=>'NID', 'type'=>'ID', 'caption'=>'NID', 'control'=>'none');
	
	public $ip=array('name'=>'ip', 'type'=>'varchar', 'caption'=>'ip', 'control'=>'text' );
	
	public $user=array('name'=>'user', 'type'=>'varchar', 'caption'=>'user', 'control'=>'textarea' );

	public $attempts=array('name'=>'attempts', 'type'=>'int', 'caption'=>'attempts', 'control'=>'text' , 'value'=>0);

	public $reason=array('name'=>'reason', 'type'=>'varchar', 'caption'=>'reason', 'control'=>'text' );

	public $NDate=array('name'=>'NDate', 'type'=>'varchar', 'caption'=>'NDate', 'control'=>'none', 'value'=>nowandate, 'format'=>'%Y-%m-%d %H:%M:%S', 'withtime'=>'true', 'NTitle'=>'');

	public $tblname="blocked_ip";
	
	public function onStart(){
		
		$this->NDate['NTitle']=$this->ip['value'];
	}
}
19) ../obj/buy_contract.class.php


<?php
include_once '../cms/dataset.php';
include_once '../lang/pages_'.$GLOBALS['lang'].'.inc';


class Buy_contract extends JSDataSet
{
    public $bcont_dist=array('name'=>'bcont_dist', 'type'=>'varchar', 'caption'=>'bcont_dist', 'control'=>'fkey', 'ftbl'=>'user', 'fTitle'=>'user_name', 'fID'=>'user_id' ,'fFltr'=>"where user_cat = 'distributor'");

    public $bcont_cust=array('name'=>'bcont_cust', 'type'=>'varchar', 'caption'=>'bcont_cust', 'control'=>'fkey', 'ftbl'=>'user', 'fTitle'=>'user_name', 'fID'=>'user_id' ,'fFltr'=>"where user_cat = 'customer'");

    public $NSerial=array('name'=>'NSerial', 'type'=>'ID', 'caption'=>'NSerial', 'control'=>'text');

    public $bcont_prod=array('name'=>'bcont_prod', 'type'=>'int', 'caption'=>'bcont_prod', 'control'=>'fkey', 'ftbl'=>'product', 'fTitle'=>prod_title_x, 'fID'=>'prod_id' );

    public $bcont_discount=array('name'=>'bcont_discount', 'type'=>'float', 'caption'=>'bcont_discount', 'control'=>'text' , 'value'=>0);

    public $bcont_payment=array('name'=>'bcont_payment', 'type'=>'float', 'caption'=>'bcont_payment', 'control'=>'text' , 'value'=>0);

    public $bcont_seen=array('name'=>'bcont_seen', 'type'=>'bool', 'caption'=>'bcont_seen', 'control'=>'none' , 'value'=>0);

    public $bcont_license=array('name'=>'bcont_license', 'type'=>'varchar', 'caption'=>'bcont_license', 'control'=>'list', 'options'=>array('No License'=>'No License','Licensed'=>'Licensed'), 'value'=>'No License');

    public $NDate=array('name'=>'NDate', 'type'=>'varchar', 'caption'=>'NDate', 'control'=>'none', 'value'=>nowandate, 'format'=>'%Y-%m-%d %H:%M:%S', 'withtime'=>'true', 'NTitle'=>'');

    public $tblname="buy_contract";
    
    public function onStart($DID=""){

    	if($DID=="IsNew"){
	    	include_once '../common/pframe.php';
	    	$myF=new pframe();
	    	$this->NSerial['value'] = $myF->Get_Incremental_ID($this->tblname,$this->NSerial['name']);
    	}
    	
    	$this->NDate['NTitle']=$this->NSerial['value'];
    }
    public function onRemove($res)
    {
    	if ($res==$GLOBALS['MyErrStr']->RowDeleted){
    		include_once '../obj/maint_contract.class.php';
    		$mc=new Maint_contract();
    		$mc->RemoveRows(" bcont_serial = '{$this->NSerial['value']}'");
    	}
    }

}
20) ../obj/category.class.php


<?php
include_once '../cms/dataset.php';
include_once '../lang/pages_'.$GLOBALS['lang'].'.inc';


class Category extends JSDataSet
{
    public $cat_id=array('name'=>'cat_id', 'type'=>'ID', 'caption'=>'cat_id', 'control'=>'none', 'ftbl'=>'', 'fTitle'=>'', 'fID'=>'' );

    public $cat_title_ar=array('name'=>'cat_title_ar', 'type'=>'varchar', 'caption'=>'cat_title_ar', 'control'=>'text', 'required'=>'required' );

    public $cat_title_en=array('name'=>'cat_title_en', 'type'=>'varchar', 'caption'=>'cat_title_en', 'control'=>'text', 'required'=>'required' );

    public $NDate=array('name'=>'NDate', 'type'=>'varchar', 'caption'=>'NDate', 'control'=>'none'/*'date'*/, 'value'=>nowandate, 'format'=>'%Y-%m-%d %H:%M:%S', 'withtime'=>'true', 'NTitle'=>'');

    public $tblname="category";
    
    public function onStart(){

//we use this in most classes to show the name of the object in the deleting message

    	$this->NDate['NTitle']=$this->{cat_title_x}['value'];
    }

} 
21) ../obj/contactus.class.php


<?php
include_once '../cms/dataset.php';
include_once '../lang/pages_'.$GLOBALS['lang'].'.inc';


class Contactus extends JSDataSet
{
    public $cu_id=array('name'=>'cu_id', 'type'=>'ID', 'caption'=>'cu_id', 'control'=>'none', 'ftbl'=>'', 'fTitle'=>'', 'fID'=>'' );

    public $cu_name=array('name'=>'cu_name', 'type'=>'varchar', 'caption'=>'cu_name', 'control'=>'text', 'required'=>'required' );

    public $cu_mobile=array('name'=>'cu_mobile', 'type'=>'varchar', 'caption'=>'cu_mobile', 'control'=>'text' );

    public $cu_job=array('name'=>'cu_job', 'type'=>'text', 'caption'=>'cu_job', 'control'=>'textarea' );

    public $cu_tel=array('name'=>'cu_tel', 'type'=>'varchar', 'caption'=>'cu_tel', 'control'=>'text' );

    public $cu_company=array('name'=>'cu_company', 'type'=>'text', 'caption'=>'cu_company', 'control'=>'textarea' );

    public $cu_email=array('name'=>'cu_email', 'type'=>'varchar', 'caption'=>'cu_email', 'control'=>'text' );
    
    public $cu_pic=array('name'=>'cu_pic', 'type'=>'file', 'filetypes'=>'jpg|png|gif', 'prefix'=>'CONTACT_', 'caption'=>'cu_pic', 'control'=>'file', 'view'=>'image', 'resize'=>true, 'sizes'=>array('thumb'=>array('p'=>'B', 'w'=>180, 'h'=>126)) , 'ext'=>'');

    public $tblname="contactus";
	
 	public function onStart()
 	{
    	$this->cu_pic['ext'] = get_data_in("select cu_pic from {$this->tblname} where cu_id='{$this->cu_id["value"]}'", "cu_pic");
    }
	
}
22) ../obj/faq.class.php


<?php
include_once '../cms/dataset.php';
include_once '../lang/pages_'.$GLOBALS['lang'].'.inc';


class Faq extends JSDataSet
{
    public $faq_id=array('name'=>'faq_id', 'type'=>'ID', 'caption'=>'faq_id', 'control'=>'none');

    public $faq_title_ar=array('name'=>'faq_title_ar', 'type'=>'varchar', 'caption'=>'faq_title_ar', 'control'=>'text', 'required'=>'required' );

    public $faq_title_en=array('name'=>'faq_title_en', 'type'=>'varchar', 'caption'=>'faq_title_en', 'control'=>'text', 'required'=>'required' );

    public $faq_desc_ar=array('name'=>'faq_desc_ar', 'type'=>'text', 'caption'=>'faq_desc_ar', 'control'=>'textarea' );

    public $faq_desc_en=array('name'=>'faq_desc_en', 'type'=>'text', 'caption'=>'faq_desc_en', 'control'=>'textarea' );
    
    public $NVNom=array('name'=>'NVNom', 'type'=>'int', 'caption'=>'NVNom', 'control'=>'none' , 'value'=>0);
    
    public $NDate=array('name'=>'NDate', 'type'=>'varchar', 'caption'=>'NDate', 'control'=>'none', 'value'=>nowandate, 'format'=>'%Y-%m-%d %H:%M:%S', 'withtime'=>'true', 'NTitle'=>'');
    
    public $tblname="faq";
    
    public function onStart($DID){
    	
    	$this->NDate['NTitle']=$this->{faq_title_x}['value'];
    }

}
23) ../obj/groups.class.php


<?php
include_once '../cms/dataset.php';
include_once '../lang/pages_'.$GLOBALS['lang'].'.inc';


class Groups extends JSDataSet
{
	public $group_id=array('name'=>'group_id', 'type'=>'ID', 'caption'=>'group_id', 'control'=>'none');

	public $group_name=array('name'=>'group_name', 'type'=>'varchar', 'caption'=>'group_name', 'control'=>'text', 'required'=>'required' );
	
	public $NDate=array('name'=>'NDate', 'type'=>'varchar', 'caption'=>'NDate', 'control'=>'none', 'value'=>nowandate, 'format'=>'%Y-%m-%d %H:%M:%S', 'withtime'=>'true', 'NTitle'=>'');

	public $tblname="groups";

	public function onStart($DID){
		 
		$this->NDate['NTitle']=$this->group_name['value'];
	}
}
24) ../obj/job.class.php


<?php
include_once '../cms/dataset.php';
include_once '../lang/pages_'.$GLOBALS['lang'].'.inc';


class Job extends JSDataSet
{
    public $job_id=array('name'=>'job_id', 'type'=>'ID', 'caption'=>'job_id', 'control'=>'none', 'ftbl'=>'', 'fTitle'=>'', 'fID'=>'' );

    public $job_title_ar=array('name'=>'job_title_ar', 'type'=>'varchar', 'caption'=>'job_title_ar', 'control'=>'text', 'required'=>'required' );

    public $job_title_en=array('name'=>'job_title_en', 'type'=>'varchar', 'caption'=>'job_title_en', 'control'=>'text', 'required'=>'required' );

    public $job_desc_ar=array('name'=>'job_desc_ar', 'type'=>'text', 'caption'=>'job_desc_ar', 'control'=>'textarea' );

    public $job_desc_en=array('name'=>'job_desc_en', 'type'=>'text', 'caption'=>'job_desc_en', 'control'=>'textarea' );

    public $NDate=array('name'=>'NDate', 'type'=>'varchar', 'caption'=>'NDate', 'control'=>'none', 'value'=>nowandate, 'format'=>'%Y-%m-%d %H:%M:%S', 'withtime'=>'true');

    public $tblname="job";
    
    public function onStart(){
    
    	$this->NDate['NTitle']=$this->{job_title_x}['value'];
    }

}
25) ../obj/maint_contract.class.php


<?php
include_once '../cms/dataset.php';
include_once '../lang/pages_'.$GLOBALS['lang'].'.inc';


class Maint_contract extends JSDataSet
{
    public $mcont_dist=array('name'=>'mcont_dist', 'type'=>'varchar', 'caption'=>'mcont_dist', 'control'=>'fkey', 'ftbl'=>'user', 'fTitle'=>'user_name', 'fID'=>'user_id' ,'fFltr'=>"where user_cat = 'distributor'");

    public $mcont_cust=array('name'=>'mcont_cust', 'type'=>'varchar', 'caption'=>'mcont_cust', 'control'=>'fkey', 'ftbl'=>'user', 'fTitle'=>'user_name', 'fID'=>'user_id' ,'fFltr'=>"where user_cat = 'customer'");
    
    public $NSerial=array('name'=>'NSerial', 'type'=>'ID', 'caption'=>'NSerial', 'control'=>'text');
	
    public $bcont_serial=array('name'=>'bcont_serial', 'type'=>'varchar', 'caption'=>'bcont_serial', 'control'=>'fkey', 'ftbl'=>'buy_contract', 'fTitle'=>'NSerial', 'fID'=>'NSerial' ,'fFltr'=>"");

    public $mcont_desc=array('name'=>'mcont_desc', 'type'=>'text', 'caption'=>'mcont_desc', 'control'=>'textarea' );

    public $mcont_seen=array('name'=>'mcont_seen', 'type'=>'bool', 'caption'=>'mcont_seen', 'control'=>'none' , 'value'=>0);

    public $mcont_status=array('name'=>'mcont_status', 'type'=>'varchar', 'caption'=>'mcont_status', 'control'=>'text' , 'value'=>'pending');

    public $NDate=array('name'=>'NDate', 'type'=>'varchar', 'caption'=>'NDate', 'control'=>'none', 'value'=>nowandate, 'format'=>'%Y-%m-%d %H:%M:%S', 'withtime'=>'true', 'NTitle'=>'');

    public $tblname="maint_contract";

    public function onStart($DID){
    	if($DID=="IsNew"){
    		include_once '../common/pframe.php';
	    	$myF=new pframe();
	    	$this->NSerial['value'] = $myF->Get_Incremental_ID($this->tblname,$this->NSerial['name']);	
    	}
    	$this->NDate['NTitle']=$this->NSerial['value'];
    }
}
26) ../obj/news.class.php


<?php
include_once '../cms/dataset.php';
include_once '../lang/pages_'.$GLOBALS['lang'].'.inc';


class News extends JSDataSet
{
    public $news_id=array('name'=>'news_id', 'type'=>'ID', 'caption'=>'news_id', 'control'=>'none');

    public $news_title_ar=array('name'=>'news_title_ar', 'type'=>'varchar', 'caption'=>'news_title_ar', 'control'=>'text', 'required'=>'required' );

    public $news_title_en=array('name'=>'news_title_en', 'type'=>'varchar', 'caption'=>'news_title_en', 'control'=>'text', 'required'=>'required' );

    public $news_desc_ar=array('name'=>'news_desc_ar', 'type'=>'varchar', 'caption'=>'news_desc_ar', 'control'=>'text' );

    public $news_desc_en=array('name'=>'news_desc_en', 'type'=>'varchar', 'caption'=>'news_desc_en', 'control'=>'text' );

    public $news_text_ar=array('name'=>'news_text_ar', 'type'=>'text', 'caption'=>'news_text_ar', 'control'=>'textarea' );

    public $news_text_en=array('name'=>'news_text_en', 'type'=>'text', 'caption'=>'news_text_en', 'control'=>'textarea' );
    
    public $news_pic=array('name'=>'news_pic', 'type'=>'file', 'caption'=>'news_pic', 'control'=>'file', 'filetypes'=>'jpg|png|gif', 'resize'=>true, 'prefix'=>'NEWS_', 'view'=>'image', 'sizes'=>array('thumb'=>array('p'=>'B', 'w'=>300, 'h'=>195), 'small'=>array('p'=>'S', 'w'=>180, 'h'=>126)), 'ext'=>'');

    public $NVNom=array('name'=>'NVNom', 'type'=>'int', 'caption'=>'NVNom', 'control'=>'none' , 'value'=>0);

    public $NDate=array('name'=>'NDate', 'type'=>'varchar', 'caption'=>'NDate', 'control'=>'none', 'value'=>nowandate, 'format'=>'%Y-%m-%d %H:%M:%S', 'withtime'=>'true', 'NTitle'=>'');

    public $tblname="news";
	
    
	public function onStart()
	{
    	$this->news_pic['ext'] = get_data_in("select news_pic from {$this->tblname} where {$this->NID["name"]}='{$this->news_id["value"]}'", "news_pic");
    	$this->NDate['NTitle']=$this->{news_title_x}['value'];
    }}
27) ../obj/picture.class.php


<?php 
include_once '../cms/dataset.php';
include_once '../lang/pages_'.$GLOBALS['lang'].'.inc';


class Picture extends JSDataSet
{
    public $pic_id=array('name'=>'pic_id', 'type'=>'ID', 'caption'=>'pic_id', 'control'=>'none' );

    public $pic_album=array('name'=>'pic_album', 'type'=>'varchar', 'caption'=>'pic_album', 'control'=>'fkey', 'ftbl'=>'album', 'fTitle'=>album_title_x, 'fID'=>'album_id');

    public $pic_title_ar=array('name'=>'pic_title_ar', 'type'=>'varchar', 'caption'=>'pic_title_ar', 'control'=>'text', 'required'=>'required' );

    public $pic_title_en=array('name'=>'pic_title_en', 'type'=>'varchar', 'caption'=>'pic_title_en', 'control'=>'text', 'required'=>'required' );
    
    public $pic_desc_ar=array('name'=>'pic_desc_ar', 'type'=>'text', 'caption'=>'pic_desc_ar', 'control'=>'textarea' );
    
    public $pic_desc_en=array('name'=>'pic_desc_en', 'type'=>'text', 'caption'=>'pic_desc_en', 'control'=>'textarea' );

    public $pic_ext	= array('name'=>'pic_ext', 'type'=>'file', 'caption'=>'pic_ext', 'control'=>'file', 'filetypes'=>'jpg|gif|png|bmp', 'resize'=>true, 'prefix'=>'Pic_', 'view'=>'image',  'sizes'=>array('thumb'=>array('p'=>'B', 'w'=>199, 'h'=>124)), 'defimg'=>'../images/def.png', 'ext'=>'');

    public $NVNom=array('name'=>'NVNom', 'type'=>'int', 'caption'=>'NVNom', 'control'=>'none' , 'value'=>0);

    public $NDate=array('name'=>'NDate', 'type'=>'varchar', 'caption'=>'NDate', 'control'=>'none', 'value'=>nowandate, 'format'=>'%Y-%m-%d %H:%M:%S', 'withtime'=>'true', 'NTitle'=>'');
    
    public $tblname="picture";

	////////////////////////////////////////////////////////////////////////
	
	public function onStart()
	{
		$this->lang['value'] = $GLOBALS['lang'];
		$this->NDate['NTitle']=$this->{pic_title_x}['value'];
		$this->pic_ext['ext'] = get_data_in("select pic_ext from {$this->tblname} where pic_id='{$this->pic_id["value"]}'", "pic_id");
	}
	
	public function onInsert() {$this->CheckIsMain();}
	public function onUpdate() {$this->CheckIsMain();}
	
	public function CheckIsMain() {	}
}
28) ../obj/product.class.php


<?php
include_once '../cms/dataset.php';
include_once '../lang/pages_'.$GLOBALS['lang'].'.inc';

class Product extends JSDataSet
{
    public $prod_id=array('name'=>'prod_id', 'type'=>'ID', 'caption'=>'prod_id', 'control'=>'text');

    public $prod_title_ar=array('name'=>'prod_title_ar', 'type'=>'varchar', 'caption'=>'prod_title_ar', 'control'=>'text', 'required'=>'required' );//, 'istitle'=>true 

    public $prod_title_en=array('name'=>'prod_title_en', 'type'=>'varchar', 'caption'=>'prod_title_en', 'control'=>'text', 'required'=>'required' );

    public $prod_cat=array('name'=>'prod_cat', 'type'=>'varchar', 'caption'=>'prod_cat', 'control'=>'fkey', 'ftbl'=>'category', 'fTitle'=>cat_title_x, 'fID'=>'cat_id' );

    public $prod_desc_ar=array('name'=>'prod_desc_ar', 'type'=>'varchar', 'caption'=>'prod_desc_ar', 'control'=>'text' );

    public $prod_desc_en=array('name'=>'prod_desc_en', 'type'=>'varchar', 'caption'=>'prod_desc_en', 'control'=>'text' );

    public $prod_text_ar=array('name'=>'prod_text_ar', 'type'=>'text', 'caption'=>'prod_text_ar', 'control'=>'textarea' );

    public $prod_text_en=array('name'=>'prod_text_en', 'type'=>'text', 'caption'=>'prod_text_en', 'control'=>'textarea' );

    public $prod_pic=array('name'=>'prod_pic', 'type'=>'file', 'caption'=>'prod_pic', 'control'=>'file', 'filetypes'=>'jpg|png|gif', 'prefix'=>'PRO_', 'view'=>'image', 'resize'=>true, 'sizes'=>array('thumb'=>array('p'=>'B', 'w'=>180, 'h'=>126), 'small'=>array('p'=>'S', 'w'=>300, 'h'=>0)) , 'ext'=>'');

    public $NVNom=array('name'=>'NVNom', 'type'=>'int', 'caption'=>'NVNom', 'control'=>'none' , 'value'=>0);

    public $NDate=array('name'=>'NDate', 'type'=>'varchar', 'caption'=>'NDate', 'control'=>'none', 'value'=>nowandate, 'format'=>'%Y-%m-%d %H:%M:%S', 'withtime'=>'true', 'NTitle'=>'');
    
    public $prod_exe=array('name'=>'prod_exe', 'type'=>'file', 'filetypes'=>'exe|msi|zip|rar|7z', 'prefix'=>'exe\EXE_', 'caption'=>'prod_exe', 'control'=>'file' );

    public $prod_price=array('name'=>'prod_price', 'type'=>'float', 'caption'=>'prod_price', 'control'=>'text' , 'value'=>0);
    
    public $prod_only_dw=array('name'=>'prod_only_dw', 'type'=>'bool', 'caption'=>'prod_only_dw', 'control'=>'checkbox' , 'value'=>0);

    public $tblname="product";	
	   
    public function onStart($DID){
    	
    	$this->prod_lang['value'] = $GLOBALS['lang'];
    	$this->prod_pic['ext'] = get_data_in("select prod_pic from {$this->tblname} where prod_id='{$this->prod_id["value"]}'", "prod_pic");
    	$this->NDate['NTitle']=$this->{prod_title_x}['value'];
    	
    	if($GLOBALS['lang'] == 'ar') {$this->prod_title_en['required']='';}
    	else{$this->prod_title_ar['required']='';}
    	
    }

}
29) ../obj/service.class.php


<?php
include_once '../cms/dataset.php';
include_once '../lang/pages_'.$GLOBALS['lang'].'.inc';


class Service extends JSDataSet
{
    public $srv_id=array('name'=>'srv_id', 'type'=>'ID', 'caption'=>'srv_id', 'control'=>'none', 'ftbl'=>'', 'fTitle'=>'', 'fID'=>'' );

    public $srv_title_ar=array('name'=>'srv_title_ar', 'type'=>'varchar', 'caption'=>'srv_title_ar', 'control'=>'text', 'required'=>'required' );

    public $srv_title_en=array('name'=>'srv_title_en', 'type'=>'varchar', 'caption'=>'srv_title_en', 'control'=>'text', 'required'=>'required' );

    public $srv_desc_ar=array('name'=>'srv_desc_ar', 'type'=>'varchar', 'caption'=>'srv_desc_ar', 'control'=>'text' );

    public $srv_desc_en=array('name'=>'srv_desc_en', 'type'=>'varchar', 'caption'=>'srv_desc_en', 'control'=>'text' );

    public $NVNom=array('name'=>'NVNom', 'type'=>'int', 'caption'=>'NVNom', 'control'=>'text' , 'value'=>0);

    public $NDate=array('name'=>'NDate', 'type'=>'varchar', 'caption'=>'NDate', 'control'=>'none', 'value'=>nowandate, 'format'=>'%Y-%m-%d %H:%M:%S', 'withtime'=>'true', 'NTitle'=>'');

    public $tblname="service";

    public function onStart(){
    	$this->NDate['NTitle']=$this->{srv_title_x}['value'];
    }
}
31) ../obj/site_config.class.php


<?php
include_once '../cms/dataset.php';
include_once '../lang/pages_'.$GLOBALS['lang'].'.inc';


class Site_config extends JSDataSet
{
    public $NID=array('name'=>'NID', 'type'=>'ID', 'caption'=>'NID', 'control'=>'none');

    public $config=array('name'=>'config', 'type'=>'varchar', 'caption'=>'config', 'control'=>'list' ,'options'=>array('slider'=>'slider','social_media'=>'social_media'));

    public $title=array('name'=>'title', 'type'=>'varchar', 'caption'=>'title', 'control'=>'text' );

    public $link=array('name'=>'link', 'type'=>'text', 'caption'=>'link', 'control'=>'textarea' );

    public $value=array('name'=>'value', 'type'=>'varchar', 'caption'=>'value', 'control'=>'text');

    public $pic_social=array('name'=>'pic_social', 'type'=>'file', 'caption'=>'pic_social', 'control'=>'file', 'filetypes'=>'jpg|png|gif', 'prefix'=>'SOCIAL_', 'view'=>'image', 'resize'=>true, 'sizes'=>array('thumb'=>array('p'=>'B', 'w'=>64, 'h'=>64), 'small'=>array('p'=>'S', 'w'=>32, 'h'=>32)) , 'ext'=>'');

    public $pic_slider=array('name'=>'pic_slider', 'type'=>'file', 'caption'=>'pic_slider', 'control'=>'file', 'filetypes'=>'jpg|png|gif', 'prefix'=>'SLIDER_', 'view'=>'image', 'resize'=>true, 'sizes'=>array('thumb'=>array('p'=>'B', 'w'=>570, 'h'=>270), 'small'=>array('p'=>'S', 'w'=>180, 'h'=>126)) , 'ext'=>'');
    
    // in this class we chose two pictures although there is only single picture foreach config,
    //we did that, because we want to give every king of pictures different attributes and dimentions
    
    public $NDate=array('name'=>'NDate', 'type'=>'varchar', 'caption'=>'NDate', 'control'=>'none', 'value'=>nowandate, 'format'=>'%Y-%m-%d %H:%M:%S', 'withtime'=>'true', 'NTitle'=>'');

    public $tblname="site_config";
    
    public function onStart($DID){
    	$this->pic_social['ext'] = get_data_in("select pic_social from {$this->tblname} where NID='{$this->NID["value"]}'", "pic_social");
    	$this->pic_slider['ext'] = get_data_in("select pic_slider from {$this->tblname} where NID='{$this->NID["value"]}'", "pic_slider");
    	$this->NDate['NTitle']=$this->config['value']." : ".$this->title['value'];
    }
    /*
     *  define("slider","slider");
		define("social_media","social_media");
		define("color_main","color_main");
		define("color_second","color_second");
		define("color_third","color_third");
		define("fav_ico","fav_ico");
		define("side_bar_button","side_bar_button");
		define("header_logo","header_logo");
     */

}
31) ../obj/shared_pool.class.php


<?php
include_once '../cms/dataset.php';
include_once '../lang/pages_'.$GLOBALS['lang'].'.inc';


class Shared_pool extends JSDataSet
{
	public $NID=array('name'=>'NID', 'type'=>'ID', 'caption'=>'NID', 'control'=>'none' );

	public $user=array('name'=>'user', 'type'=>'varchar', 'caption'=>'user', 'control'=>'none', 'ftbl'=>'', 'fTitle'=>'', 'fID'=>'' );

	public $file_name=array('name'=>'file_name', 'type'=>'varchar', 'caption'=>'file_name', 'control'=>'text' );

	public $file_size=array('name'=>'file_size', 'type'=>'varchar', 'caption'=>'file_size', 'control'=>'none' );

	public $file_type=array('name'=>'file_type', 'type'=>'file', 'caption'=>'file_type', 'control'=>'file' ,'filetypes'=>'', 'prefix'=>'', 'control'=>'file', 'ext'=>'');

	public $path=array('name'=>'path', 'type'=>'text', 'caption'=>'path', 'control'=>'none', 'value'=>'' );

	public $NDate=array('name'=>'NDate', 'type'=>'varchar', 'caption'=>'NDate', 'control'=>'none', 'value'=>nowandate, 'format'=>'%Y-%m-%d %H:%M:%S', 'withtime'=>'true', ''=>'');

	public $tblname="shared_pool";
	
	
	public function onStart($DID){
		 
// load some values from DB

		$this->file_type['ext'] = get_data_in("select file_type from {$this->tblname} where NID='{$this->NID["value"]}'", "file_type");
		$this->NDate['NTitle']=$this->file_name['value'];
		
		$dir = $this->get_folders();
		$this->documents_path = "../documents/SHARED_POOL{$dir}"; //echo $this->documents_path ;
		
	}/////////////////////
	
	public function onInsert($res)
	{
//when inserting a directory, we are not using the user defined names but we use unique IDs
//so, get these IDs and create the dir 
		if($_REQUEST['dir'] == '1')
		{
			$dir = $this->get_folders();
			mkdir("{$this->documents_path}{$this->NID['value']}"); 
		}
	}/////////////////////
	
	public function onRemove($res)
	{
		$dir = $this->get_folders();
		
			if($this->file_type['ext'] == '[dir]'){
				$dir_path = "{$this->documents_path}{$this->NID['value']}";	
				@rmdir($dir_path);
			} 
			else {
				@unlink("{$this->documents_path}{$this->NID['value']}.{$this->file_type['ext']}"); 
			}
		
	}/////////////////////
	
	function get_folders()
	 {
	 	//bring the id for dir
	 	if($_REQUEST['path'] == ''){ return '/';}
	 	else{
		 	$arr = explode('/', $_REQUEST['path']);
		 	$dir = "";
		 	$i=0;
		 	$ids=array();
		 	foreach( $arr as $v){
		 		$ids[$i] = get_data_in("select NID from shared_pool where file_type='[dir]' and file_name='{$v}' ", "NID");
		 		$i++;
		 	}
		 	foreach( $ids as $v){
		 		if($v != '') { $dir .= "/{$v}"; }
		 	}
		 	if($_REQUEST['path'] != '')
		 		$dir .='/';
		 	return $dir;
	 	}
	 }/////////////////////	 
}


31) ../obj/user.class.php


<?php
include_once '../cms/dataset.php';
include_once '../lang/pages_'.$GLOBALS['lang'].'.inc';


class User extends JSDataSet
{
    public $user_id=array('name'=>'user_id', 'type'=>'ID', 'caption'=>'user_id', 'control'=>'none');

    public $user_name=array('name'=>'user_name', 'type'=>'varchar', 'caption'=>'user_name', 'control'=>'text','istitle'=>true, 'required'=>'required');

    public $user_password=array('name'=>'user_password', 'type'=>'varchar', 'caption'=>'user_password', 'control'=>'none', 'value'=>'202cb962ac59075b964b07152d234b70' /*this is the hash of "123" as default password*/, 'required'=>'required' );

    public $user_email=array('name'=>'user_email', 'type'=>'varchar', 'caption'=>'user_email', 'control'=>'text' );

    public $user_phone=array('name'=>'user_phone', 'type'=>'varchar', 'caption'=>'user_phone', 'control'=>'text' );

    public $user_address=array('name'=>'user_address', 'type'=>'varchar', 'caption'=>'user_address', 'control'=>'text' );

    public $user_country=array('name'=>'user_country', 'type'=>'varchar', 'caption'=>'user_country', 'control'=>'text' );

    public $user_city=array('name'=>'user_city', 'type'=>'varchar', 'caption'=>'user_city', 'control'=>'text' );

    public $user_birthyear=array('name'=>'user_birthyear', 'type'=>'int', 'caption'=>'user_birthyear', 'control'=>'text' , 'value'=>'0');
    
    public $in_home=array('name'=>'in_home', 'type'=>'bool', 'caption'=>'in_home', 'control'=>'checkbox' , 'value'=>0);

    public $user_pic=array('name'=>'user_pic', 'type'=>'file', 'filetypes'=>'jpg|png|gif', 'prefix'=>'USER_', 'caption'=>'user_pic', 'control'=>'file', 'view'=>'image', 'resize'=>true, 'sizes'=>array('thumb'=>array('p'=>'B', 'w'=>180, 'h'=>126)) , 'ext'=>'');
   
    public $user_cat=array('name'=>'user_cat', 'type'=>'varchar', 'caption'=>'user_cat', 'control'=>'list', 'options'=>array('employee'=>'employee','distributor'=>'distributor','customer'=>'customer', 'admin'=>'admin'));
    
    public $NDate=array('name'=>'NDate', 'type'=>'varchar', 'caption'=>'NDate', 'control'=>'none', 'value'=>nowandate, 'format'=>'%Y-%m-%d %H:%M:%S', 'withtime'=>'true', 'NTitle'=>'');//this one does not exist in db but we use it to show name when deleting
    
    //public 

    public $tblname="user";

    public function onStart(){
    	$this->user_pic['ext'] = get_data_in("select user_pic from {$this->tblname} where user_id='{$this->user_id["value"]}'", "user_pic");
    	$this->NDate['NTitle']=$this->user_name['value'];
    	
    }///////////////////////////////////////////////
    
    public function onInsert($res){
    	 
    	include_once '../common/pframe.php';
    	$myframe=new pframe();
    	
    	// to make distributor manage only his own customers
    	if($myframe->Is_Distributor()){ 
    		$sql="insert into customers_of_dist values('{$_SESSION['GID']}', '{$this->user_id['value']}')";//echo $sql;
    		@cmd($sql);
    	}
    	// each user is added to one of the default groups according to his category
    	if($this->user_cat['value'] == 'customer'){$group = 'CUSTOMERS';}
    	elseif($this->user_cat['value'] == 'employee'){$group = 'EMPLOYEES';}
    	elseif($this->user_cat['value'] == 'distributor'){$group = 'DISTRIBUTORS';}
    	elseif($this->user_cat['value'] == 'admin'){$group = 'ADMINISTRATORS';}
    	$group_id=get_data_in("select group_id from groups where group_name = '{$group}' ", "group_id");
    	$sql="insert into user_groups values('{$this->user_id['value']}', '{$group_id}')";//echo $sql;
    	@cmd($sql);
    }///////////////////////////////////////////////
    
    public function onRemove($res){
    
    	include_once '../common/pframe.php';
    	$myframe=new pframe();
    	 
    	// when a user is deleted, it will be deleted from distributor custs too.
    		$sql="delete from customers_of_dist where cust_id = '{$this->user_id['value']}' ";//echo $sql;
    		@cmd($sql);
    }///////////////////////////////////////////////
}
33) ../obj/user_group_privs.class.php


<?php
include_once '../cms/dataset.php';
include_once '../lang/pages_'.$GLOBALS['lang'].'.inc';


class User_group_privs extends JSDataSet
{
	public $group_id=array('name'=>'group_id', 'type'=>'ID', 'caption'=>'group_id', 'control'=>'fkey', 'ftbl'=>'', 'fTitle'=>'', 'fID'=>'' );

	public $priv=array('name'=>'priv', 'type'=>'varchar', 'caption'=>'priv', 'control'=>'text' );

	public $tblname="user_group_privs";

}
34) ../obj/user_groups.class.php


<?php
include_once '../cms/dataset.php';
include_once '../lang/pages_'.$GLOBALS['lang'].'.inc';


class User_groups extends JSDataSet
{
	public $user_id=array('name'=>'user_id', 'type'=>'ID', 'caption'=>'user_id', 'control'=>'fkey', 'ftbl'=>'', 'fTitle'=>'', 'fID'=>'' );

	public $user_group_id=array('name'=>'user_group_id', 'type'=>'ID', 'caption'=>'user_group_id', 'control'=>'fkey', 'ftbl'=>'', 'fTitle'=>'', 'fID'=>'' );

	public $tblname="user_groups";

}
35) ../obj/user_privs.class.php


<?php
include_once '../cms/dataset.php';
include_once '../lang/pages_'.$GLOBALS['lang'].'.inc';


class User_privs extends JSDataSet
{
	public $user_id=array('name'=>'user_id', 'type'=>'ID', 'caption'=>'user_id', 'control'=>'fkey', 'ftbl'=>'', 'fTitle'=>'', 'fID'=>'' );

	public $user_priv=array('name'=>'user_priv', 'type'=>'varchar', 'caption'=>'user_priv', 'control'=>'text' );

	public $user_grant=array('name'=>'user_grant', 'type'=>'bool', 'caption'=>'user_grant', 'control'=>'checkbox' , 'value'=>0);

	public $tblname="user_privs";

}
36) ../obj/video.class.php


<?php
include_once '../cms/dataset.php';
include_once '../lang/pages_'.$GLOBALS['lang'].'.inc';


class Video extends JSDataSet
{
    public $vid_id=array('name'=>'vid_id', 'type'=>'ID', 'caption'=>'vid_id', 'control'=>'none');
    
    public $vid_album=array('name'=>'vid_album', 'type'=>'varchar', 'caption'=>'vid_album', 'control'=>'fkey', 'ftbl'=>'album', 'fTitle'=>album_title_x, 'fID'=>'album_id');

    public $vid_title_ar=array('name'=>'vid_title_ar', 'type'=>'varchar', 'caption'=>'vid_title_ar', 'control'=>'text', 'required'=>'required' );

    public $vid_title_en=array('name'=>'vid_title_en', 'type'=>'varchar', 'caption'=>'vid_title_en', 'control'=>'text', 'required'=>'required' );

    public $vid_desc_ar=array('name'=>'vid_desc_ar', 'type'=>'text', 'caption'=>'vid_desc_ar', 'control'=>'textarea' );

    public $vid_desc_en=array('name'=>'vid_desc_en', 'type'=>'text', 'caption'=>'vid_desc_en', 'control'=>'textarea' );

    public $vid_link=array('name'=>'vid_link', 'type'=>'varchar', 'caption'=>'vid_link', 'control'=>'text' );
    
    public $NVNom=array('name'=>'NVNom', 'type'=>'int', 'caption'=>'NVNom', 'control'=>'none' , 'value'=>0);

    public $NDate=array('name'=>'NDate', 'type'=>'varchar', 'caption'=>'NDate', 'control'=>'none', 'value'=>nowandate, 'format'=>'%Y-%m-%d %H:%M:%S', 'withtime'=>'true', 'NTitle'=>'');

    public $tblname="video";
    
    public function onStart()
    {
    	$this->lang['value'] = $GLOBALS['lang'];
    	$this->NDate['NTitle']=$this->{vid_title_x}['value'];
    }

}
Sixth ../pages  folder


The general content for most pages:

Calling the template functions, "header"at first and "footer" at the end.

Each page has a specific Privelige, which with out it the user can not do any of the administrative operations.

It has many cases that vary accourding to the parameter 'v':
case "e": editor. That is Editing & Adding
case "c":card. That is a one element (a product, a service…) (displays the element's picture ,title, description, text, latest update, number of visits …)
case "t": table. That is all rows result of a query (usually all rows of a table).
case "d": delete. Displaying deleting form for a given element.

At the end, displaying the "Related Pages" section foreach page.


38) ../pages/ads.php    (ADVERTISEMENT Page)
<?php

// OK, let's show you how we bahave in all of our pages
// we use this general behavior in each page.
// so we will mention more details this time but not in every page ☺
// we are one the 100th page already, only in report 7 !!

//calling class page and function pages
include_once '../common/pframe.php';
include_once '../obj/ads.class.php';
include_once '../cms/navigator.php';

//declare objects 
$myframe=new pframe();
$myframe->header(View_Ads);
$myAds=new Ads($_REQUEST['NID']);
$pagePRIV = "ADS_MAN"; //declare the privilege of this page
//show adding tool if this user is admin (A) or has the page's privilege
if(user_has_permission(array("A", $pagePRIV))){	$adding ="";/*if adding is about another OBJ*/	}else{$adding=""; $pagePRIV="";	}

switch ($_REQUEST['v'])
{		
	case "e":/*******************  Add/Edit Editor  ********************/
	/***/IS_SECURE($pagePRIV);/***/ 
//in most switch cases we use this to make sure that the user accessing the page DOES has the permission to access and operate
		if ($myAds->NID['IsNew']){		$ttl=Add_Ads;	} else{ $ttl=Edit_Ads;	}
		
//open box with tree header and adding tool (if permitted)
		$myframe->open_box("withTree", $ttl,"panel", $adding);
//display Editor that draws html add/edit from 
		$myAds->DisplayEditor("_n");

		$myframe->close_box("panel");
	break;//____________________________________________
	
	case "c":/*******************  Card (one object info)  ********************/
	/***/IS_SECURE($pagePRIV);/***/
		$myframe->open_box("withTree", $myAds->{'ads_title_'.$GLOBALS['lang']}['value'],"panel", $adding);

// calling the previously explained "card" 
//that shows a single object (photo, title, description, text, NumOfVisits, last update Date)
		$myframe->card($myAds->NID['value'],$myAds->{'ads_title_'.$GLOBALS['lang']}['value'],$myAds->{'ads_text_'.$GLOBALS['lang']}['value'],$myAds->Draw_Photo($myAds->ads_pic, "thumb", "", $vid, "lightwindow"),"",more,true,$myAds->NVNom['value'],$myAds->NDate['value'],"");

		$myframe->close_box("panel");
	break;//____________________________________________
	
	case "t":/*******************  Table of objects  ********************/
	/***/IS_SECURE($pagePRIV);/***/
		$myframe->open_box("withTree", View_Ads. $_REQUEST['NID'],"panel", $adding);
		
// SQL query with where clause
		$wherestr="";
		$sql="select * from {$myAds->tblname} {$wherestr} order by NDate desc ";

// declare a Navigator object from this sql query (Pagination), with 10 result in each page
		$adstable=new Navigator($sql, $_GET['cur_page'], 10, "select count(NID) from {$myAds->tblname} {$wherestr}");
		if(@mysql_num_rows(mysql_query($sql)) != 0)
// if the SQL query returns results
		{
			$color="";			
			/////START CARDS EXPLORER
			while ($AdsRow=mysql_fetch_array($adstable->result)){
//Fetch every single result of the query
				
//Fill the "Fetched row result" into the object of the current class (Ads)
				$myAds->FillIn($AdsRow);
//when an advertisement expires, highlight the date with color red, and don't show it in side bar
				/** check the expired ADS **/if( strtotime($myAds->ads_end['value']) < strtotime(nowandate)) $color='red'; else $color='green';
				$myframe->card($myAds->NID['value'],$myAds->{'ads_title_'.$GLOBALS['lang']}['value'],$myAds->{'ads_desc_'.$GLOBALS['lang']}['value'].'</br>'.ads_end.' : <p style="color:'.$color.'">'.$myAds->ads_end['value']."</p>",$myAds->Draw_Photo($myAds->ads_pic, "thumb"),"",more,/*$_REQUEST['NType']=="1" ? false : true*/true,$myAds->NVNom['value'],$myAds->NDate['value'],/*$myAds->NType['value']*/"");
			}
			//////END CARDS EXPLORER
			$adstable->Draw_Navigator_Line("jbtn");
		}else{
// when the SQL query returns no result, show a message
			?><div style="color:#999;width:90%;border:1px #ccc solid;padding:15px;margin:auto;text-align: center;"><?=No_Rows_Selected?></div><?
			}
		$myframe->close_box("panel");
	break;//____________________________________________
	
	case "d":/*******************  Delete an object  ********************/

//security ..
	/***/IS_SECURE($pagePRIV);/***/
		if ($myAds->NID['IsNew'])  break; //security ..
	
		$myframe->open_box("withTree", Del_Ads,"panel", $pagePRIV, $adding);
             //Delete form (message, Yes/No)
		$myAds->DisplayDelMsg();
		$myframe->close_box("panel");
	break;//____________________________________________
}	
/*************************   related pages  ******************************/
$myframe->Display_Related_Pages(Ads,array("A", $pagePRIV));

// Footer foreach page
	$myframe->footer();
	?>
39) ../pages/albums.php         (ALBUMS page)
<?php
ob_start();
session_start();
include_once '../common/pframe.php';
include_once '../obj/album.class.php';
include_once '../cms/navigator.php'; 

$myframe=new pframe();
$myframe->header(Albums);
$myAlbum=new Album($_REQUEST['NID']);
$pagePRIV = "ALBUMS_MAN";
if(user_has_permission(array("A", $pagePRIV))){	$adding ="";/*if adding is about another OBJ*/	}else{$adding=""; $pagePRIV="";	}

switch ($_REQUEST['v'])
	{
	//EDITOR_____________________________________________________________________________
	
//as we mentioned previously, most editors goes like this, Secure access to page, then EDITOR
	/***/IS_SECURE($pagePRIV);/***/		
		if ($myAlbum->NID['IsNew']){	$ttl=Add_Album; }else { $ttl=Edit_Album; }
		
		$myframe->open_box("withTree", $ttl,"panel", $pagePRIV, $adding);
		$myAlbum->DisplayEditor("_n");
		$myframe->close_box("panel");
	
	break;
	//Card Viewer_____________________________________________________________________________
	case "c":

// the "Album" page has special behavior, it's an object that contains two sub-object. So, we declare them at the beginning (Picture, Video)
		$_SESSION["album"] = $_REQUEST['NID'];
		
		include_once '../obj/picture.class.php';
		include_once '../obj/video.class.php';
		$myPIC=new Picture();
		$myVID=new Video();
		
// Open Box with tree & adding tool
		$myframe->open_box("withTree", $myAlbum->{album_title_x}['value'],"panel", "");
		
// SQL query with where clause (Pictures + Videos in the sqme Query !)
		$wherestr1=" where pic_album='{$_REQUEST['NID']}' ";
		$sql="(select 'pic' as tbl , pic_id ,pic_album ,pic_title_ar ,pic_title_en ,pic_desc_ar ,pic_desc_en ,pic_ext ,NVNom ,NDate from {$myPIC->tblname} {$wherestr1} order by NDate desc)";
// separator result
		$sql.=" UNION(select 'x','x','x','x','x','x','x','x','x','x' from video)"; 
		$wherestr2=" where vid_album='{$_REQUEST['NID']}' ";
		$sql.=" UNION (select 'vid' as tbl , vid_id ,vid_album ,vid_title_ar ,vid_title_en ,vid_desc_ar ,vid_desc_en ,vid_link ,NVNom ,NDate from {$myVID->tblname} {$wherestr2} order by NDate desc) ";
		$num1=get_data_in("select count(pic_id) as v from {$myPIC->tblname} {$wherestr1} ", "v");
		$num2=get_data_in("select count(vid_id) as v from {$myVID->tblname} {$wherestr2} ", "v");
		$nav=new Navigator($sql, $_GET['cur_page'], 6,0 );
		
		if(@mysql_num_rows(mysql_query($sql)) != 0)
// result not empty
		{
			/////START CARDS EXPLORER
			while ($Row=mysql_fetch_array($nav->result))
			{
// if picture object then show picture card
				if($Row['tbl'] == 'pic')
				{
					$myPIC->FillIn($Row);
					
					$myframe->card($myPIC->NID['value'],
							$myPIC->{'pic_title_'.$GLOBALS['lang']}['value'],
							$myPIC->{'pic_desc_'.$GLOBALS['lang']}['value'],
							$myPIC->Draw_Photo($myPIC->pic_ext, "thumb", "", $vid, "lightwindow"),
							"pictures.php",more,true,$myPIC->NVNom['value'],$myPIC->NDate['value'],"", "PICTURES_MAN");
				}
				elseif($Row['tbl'] == 'vid')
				{
// if video object then show video card

					$ttl='pic_title_'.$GLOBALS['lang'];
					$desc='pic_desc_'.$GLOBALS['lang'];
					
					$myframe->card($Row['pic_id'],		$Row[$ttl],		$Row[$desc],
							
						'<iframe width="420" height="345" src="'.$Row['pic_ext'].'"> </iframe>',
						"videos.php",more,true,$myVID->NVNom['value'],$myVID->NDate['value'],"", "VIDEOS_MAN");					
				}		
			}
			//////END CARDS EXPLORER
			$nav->Draw_Navigator_Line();
		}else{

// if no results returned, display message

		?><div style="color:#999;width:90%;border:1px #ccc solid;padding:15px;margin:auto;text-align: center;"><?=No_Rows_Selected?></div><?
				}
// increase visits of the album
		$myAlbum->More_DVNom();	
		$myframe->close_box("panel");
		
	break;	
	//TABLE_____________________________________________________________________________
	case "t": 
// table of albums, box with tree, then SQL query, chech result not empty to show each object card
		
		$myframe->open_box("withTree", Albums,"panel", $adding);
	
		$wherestr="  ";	
		$sql="select * from {$myAlbum->tblname} {$wherestr} order by NDate desc";
		$album=new Navigator($sql, $_GET['cur_page'], 6, "select count(NID) from {$myAlbum->tblname} {$wherestr} ");
		if(@mysql_num_rows(mysql_query($sql)) != 0)
		{
			/////START CARDS EXPLORER
				echo '<div class="albums">';
			while ($albumRow=mysql_fetch_array($album->result)){
				echo '<div class="albm">';
				$myAlbum->FillIn($albumRow);
				$myframe->card($myAlbum->NID['value'],
								$myAlbum->{'album_title_'.$GLOBALS['lang']}['value'],
								$myAlbum->{'album_desc_'.$GLOBALS['lang']}['value'],
								$myframe->DisplayAlbumPic($myAlbum),
								"",more,true,$myAlbum->NVNom['value'],$myAlbum->NDate['value'],"", $pagePRIV);
				echo '</div>';
			}
			echo '</div>';
			//////END CARDS EXPLORER
			$album->Draw_Navigator_Line();
		}else{	
// No Rows Returned !
?><div style="color:#999;width:90%;border:1px #ccc solid;padding:15px;margin:auto;text-align: center;"><?=No_Rows_Selected?></div><?	}
			
		$myframe->close_box("panel");
	
	break;
	//DELETE ___________________________________________________________________________
	case "d":
// just previous page, security then delete message
		/***/IS_SECURE($pagePRIV);/***/
		if ($myAlbum->NID['IsNew'])  break;
		
		$myframe->open_box("withTree", Delete_Album,"panel", $pagePRIV, $adding);
		$myAlbum->DisplayDelMsg();
		$myframe->close_box("panel");
	
	break;	
}
/*************************   related pages  ******************************/
$myframe->Display_Related_Pages(Album,array("A", $pagePRIV));

$myframe->footer();
?> 
40) ../pages/bconts.php          (BUY CONTRACT Page)
<?php
// as usual, include class and function pages

include_once '../common/pframe.php';
include_once '../obj/buy_contract.class.php';
include_once '../cms/navigator.php'; 

//declare objects and page privilege

$myframe=new pframe();
$myframe->header(View_bconts);
$myBCont=new Buy_contract($_REQUEST['NID']); 
$pagePRIV = "BCONTS_MAN";
if(user_has_permission(array("A", $pagePRIV))){	$adding ="";/*if adding is about another OBJ*/	}else{$adding=""; $pagePRIV="";	}

switch ($_REQUEST['v'])
	{
	//EDITOR_______________________________________________________________________________
	case "e":
	
	/***/IS_SECURE($pagePRIV);/***/		
		if ($myBCont->NID['IsNew']){	$ttl=Add_x; }else { $ttl=Edit_x; }
		
		//distributor can't change dist value, it's him self and hidden
		if($myframe->Is_Distributor()){$myBCont->bcont_dist['control'] = 'hidden';        $myBCont->bcont_dist['value'] = $_SESSION['GID'];

//prevent distributor from changing parameters or copy links to access others buy contracts

			if($_SESSION['GID'] != get_data_in("select bcont_dist from buy_contract where NSerial='{$_REQUEST['NID']}'", "bcont_dist") && $_REQUEST['NID'] != 'IsNew') {IS_SECURE("", "not_secure");}
		}
		
// Show Editor
		$myframe->open_box("withTree", $ttl,"panel", $pagePRIV, $adding);
		$myBCont->DisplayEditor("_n");
		$myframe->close_box("panel");
		
		?><script>  // you can not edit Serial (auto increment) OR change distributor (LOCKED)
			var selct=document.getElementById("txt_NSerial");		selct.disabled = true;	
			var selct=document.getElementById("txt_bcont_dist");		selct.disabled = true;	
			$("#txt_bcont_dist").val(<?=json_encode($_SESSION['GID'])?>);
		</script><?
	
	break;
	//Viewer______________________________________________________________________________
	case "c": 
	
	/***/IS_SECURE($pagePRIV);/***/

// when clicking on the bcont, the card case will redirect you to the MConts inside this BCont

		header("location: ../pages/bconts.php?lang={$GLOBALS['lang']}&v=t&NID={$_REQUEST['NID']}");	
	break;
	//TABLE_______________________________________________________________________________
	case "t":	
		
		$myframe->open_box("withTree", BConts,"panel", $pagePRIV, $adding);
		$wherestr=" ";
// if you were a Customer/Distributor, you only see BConts that you are a part of
		if($myframe->Is_Customer()){$wherestr=" where bcont_cust='{$_SESSION['GID']}' ";}
		elseif($myframe->Is_Distributor()){$wherestr=" where bcont_dist='{$_SESSION['GID']}' ";}
			
// sql query to bring all BConts
		$sql="select * from {$myBCont->tblname} {$wherestr} order by NDate desc"; //echo $sql;
		$serv=new Navigator($sql, $_GET['cur_page'], 20, "select count(NID) from {$myBCont->tblname} {$wherestr} ");

		?><table class="global_tbl sortable">

// if no result display a message
		<? if(@mysql_num_rows(mysql_query($sql)) == 0){?><tr><td><?=No_Rows_Selected?></td></tr><?}else{?>

// if  result not empty show the table of results
			<tr>
				<th><?=NSerial?></th>
				<th><?=Distributor_Name?></th>
				<th><?=Customer_Name?></th>				
				<th><?=bcont_prod?></th>
				<th><?=bcont_discount?></th>
				<th><?=bcont_payment?></th>
				<th><?=bcont_license?></th>
				<th><?=NDate?></th>
			</tr>
		<?
		////START CARDS NAVIGATOR
		$i=0;
		$tr_class="";
		while ($Row=mysql_fetch_array($serv->result))
		{
			$myBCont->FillIn($Row);
			if($i%2==0){$tr_class="tr_1";}else{$tr_class="tr_2";}
// Display my BConts if I was Distributor / Customer
// Display all BConts for Admin
			if(($myframe->Is_Distributor() && $myBCont->bcont_dist['value'] == $_SESSION['GID']) || user_has_permission(array("A", $pagePRIV))  
					|| $myframe->Is_Customer() && $myBCont->bcont_cust['value'] == $_SESSION['GID']){
				
				//checking whther this bcont is licensed or not to change the column css
				if($myBCont->bcont_license['value']=="No License"){		$is_lice="no_license";		}
				elseif($myBCont->bcont_license['value']=="Licensed") {			$is_lice="licensed";			}
			?>

// Here we go, show all columns, with FK titles of course

			<tr class="<?=$tr_class?> <? if($_REQUEST['NID'] == $myBCont->NID['value']){ echo "tr_highlighted";}?>" onclick="document.location = '../pages/mconts.php?lang=<?=$GLOBALS["lang"]?>&v=t&bcont=<?=$myBCont->NID['value']?>&prod=<?=$myBCont->bcont_prod['value']?>';">
				
				<td class="<?if($myBCont->bcont_seen['value']!=1){echo "unseen_td";}?>"><?=$myBCont->NSerial['value']?></td>
				<td class=""><?=get_data_in("select user_name from user where user_id = '{$myBCont->bcont_dist['value']}'", 'user_name')?></td>
				<td class=""><?=get_data_in("select user_name from user where user_id = '{$myBCont->bcont_cust['value']}'", 'user_name')?></td>
				<td class=""><?=get_data_in("select ".prod_title_x." from product where prod_id = '{$myBCont->bcont_prod['value']}'", prod_title_x)?></td>
				<td class=""><?=$myBCont->bcont_discount['value']?></td>
				<td class=""><?=$myBCont->bcont_payment['value']?></td>
				<td class="license_td <?=$is_lice?>"><?=$myBCont->bcont_license['value']?></td>
				<td class=""><?=$myBCont->NDate['value']?></td>

//show admin tools (edit/delete) if I was admin or have the page's priv
				<? if(user_has_permission(array("A", $pagePRIV))) {?><td class="admin_tools_td"><?=$myframe->DisplayAdminTools("bconts", $myBCont->NSerial['value'])?></td><? }
			?></tr><?
			}
			$i++;
		} }
		?></table><?
		//////END CARDS NAVIGATOR
		$serv->Draw_Navigator_Line();
		$myframe->close_box("panel");
	break;
	// DELETE_______________________________________________________________________________
	case "d":
		/***/IS_SECURE($pagePRIV);/***/
		
		if ($myBCont->NID['IsNew'])  break;
		
		$myframe->open_box("withTree", Del_x,"panel", $pagePRIV, $adding);
		$myBCont->DisplayDelMsg();
		$myframe->close_box("panel");
	
	break;	
}
/*************************   related pages  ******************************/
$myframe->Display_Related_Pages(BConts, array("A", $pagePRIV, $myBCont->NSerial['value']));

$myframe->footer();
?> 
41) ../pages/blocked_ips.php           (BLOCKED IPs Page)
<?php 

// as usual calls and declares ………..

include_once '../common/pframe.php';
include_once '../cms/navigator.php';
include_once "../obj/blocked_ip.class.php";

$myframe=new pframe();
$myframe->header(BlockedIPs);
$myBlock = new Blocked_ip($_REQUEST['NID']);
$pagePRIV = "BLOCKED_IPS_MAN";
if(user_has_permission(array("A", $pagePRIV))){	$adding ="";/*if adding is about another OBJ*/	}else{$adding=""; $pagePRIV="";	}

switch ($_REQUEST['v'])
{
	case "e":/*************   editor   ***********************/
		
// as usual, security tree header and editor

	/***/IS_SECURE($pagePRIV);/***/
		$myframe->open_box("withTree", $myBlock->ip['value'],"panel", $pagePRIV, $adding);
		$myBlock->DisplayEditor("_n");
		$myframe->close_box("panel");
		
		?>
		<button id="edit_protected" onclick="edit_protected()" style="font-size:14pt;font-weight:bold;padding:5px 40px"><?=Edit_x?></button>
		<script>

//disable fields for protection
			var selct=document.getElementById("txt_ip");				selct.disabled = true;
			var selct=document.getElementById("txt_user");			selct.disabled = true;
			var selct=document.getElementById("txt_attempts");			selct.disabled = true;
			var selct=document.getElementById("txt_reason");			selct.disabled = true;
			var selct=document.getElementById("txt_NDate");			selct.disabled = true;
			function edit_protected() {var selct=document.getElementById("txt_attempts");			selct.disabled = false;}		
		</script><?
	
	break;
		
	case "c":/*************   card   ***********************/
	/***/IS_SECURE($pagePRIV);/***/
		echo "no c parameter, no card here";
	break;
	
	case "t":/*************   Table of All elements   ***********************/

// as usual, security, tree header, sql query, table of results
	/***/IS_SECURE($pagePRIV);/***/
		$myframe->open_box("withTree", BlockedIPs,"panel", $pagePRIV, $adding);
		
		$wherestr="";
		$sql="select * from {$myBlock->tblname} {$wherestr} order by NDate";
		$nav=new Navigator($sql, $_GET['cur_page'], 10, "select count(NID) from {$myBlock->tblname} {$wherestr} ");
		//
		?><table class="global_tbl sortable">
// empty result or NOT ?
		<? if(@mysql_num_rows(mysql_query($sql)) == 0){?><tr><td><?=No_Rows_Selected?></td></tr><?}else{?>
			<tr>
				<th><?=blocked?></th>
				<th><?=ip?></th>
				<th><?=attempts?></th>
				<th><?=reason?></th>
				<th><?=NDate?></th>
			</tr><?
		////START CARDS NAVIGATOR
		$i=0;
		$tr_class="";
		while ($Row=mysql_fetch_array($nav->result)) //#
		{
			$myBlock->FillIn($Row);$id=$myBlock->user_id["value"];
			if($i%2==0){$tr_class="tr_1";}else{$tr_class="tr_2";}
			$before_7_days="";
			?>
// highlight the blocked IPs (over 10 illegal attempts) with red color

			<tr class="<?=$tr_class?> <?if($myBlock->attempts['value']>10){echo"blocked_ip";}?>" onclick="document.location = '../pages/our_customers.php?lang=<?=$GLOBALS["lang"]?>&v=c&NID=<?=$myBlock->NID['value']?>';">
				<td><?if($myBlock->attempts['value']>10){echo blocked;}else{echo NOT_blocked;}?></td>
				<td class=""><?=$myBlock->ip['value']?></td>
				<td class=""><?=$myBlock->attempts['value']?></td>
				<td class=""><?=constant($myBlock->reason['value'])?></td>

// Admin Tools if you have permission

				<? if(user_has_permission(array("A", $pagePRIV))) {?><td class="admin_tools_td"><?=$myframe->DisplayAdminTools("blocked_ips", $myBlock->NID['value'])?></td><? }
			?></tr><?
			$i++;
		}}
		?></table><script>   $('.DEL_admintool').hide();   </script><?
		//////END CARDS NAVIGATOR
		$nav->Draw_Navigator_Line();
		$myframe->close_box("panel");
	break;
	
	case "d":/*************   delete   ******************************************/
	/***/IS_SECURE($pagePRIV);/***/
		if ($myBlock->NID['IsNew'])  break;
		
		$myframe->open_box("withTree", Del_blocked_ip,"panel", $pagePRIV, $adding);
		$myBlock->DisplayDelMsg();
		$myframe->close_box("panel");
	break;
}

$myframe->footer();
?>
41) ../pages/category.php           (CATEGORIES Page)
<?php

// by now every thing seems to be routine, we are going to explain non routine thing by now

include_once '../common/pframe.php';
include_once '../obj/category.class.php';
include_once '../cms/navigator.php'; 

$myframe=new pframe();
$myframe->header(View_x);
$myP=new Category($_REQUEST['NID']);
$pagePRIV = "CATEGORY_MAN";
if(user_has_permission(array("A", $pagePRIV))){	$adding ="";/*if adding is about another OBJ*/	}else{$adding=""; $pagePRIV="";	}

switch ($_REQUEST['v'])
	{
	//EDITOR_______________________________________________________________________________
	case "e":
	
	/***/IS_SECURE($pagePRIV);/***/	
		if ($myP->NID['IsNew']){	$ttl=Add_x; }else { $ttl=Edit_x; }
		
		$myframe->open_box("withTree", $ttl,"panel", $pagePRIV, $adding);
		$myP->DisplayEditor("_n");
		$myframe->close_box("panel");	
	break;
	//Viewer_______________________________________________________________________________
	case "c":
	
// when clicking on a category, the card case will redirect you to the products of that category

		header("location: ../pages/products?lang={$GLOBALS['lang']}&v=t&cat={$_REQUEST['NID']}");
	
	break;	
	//TABLE_________________________________________________________________________________
	case "t":
		$myframe->open_box("withTree", Categories,"panel", $adding);
		$wherestr=" ";	
		$sql="select * from {$myP->tblname} {$wherestr} order by NDate desc";
		$cat=new Navigator($sql, $_GET['cur_page'], 6, "select count(NID) from {$myP->tblname} {$wherestr} ");
		
		if(@mysql_num_rows(mysql_query($sql)) != 0)
		{
			/////START CARDS EXPLORER				
			while ($catRow=mysql_fetch_array($cat->result)){
				
				$myP->FillIn($catRow);
				$myframe->card($myP->NID['value'],
								$myP->{'cat_title_'.$GLOBALS['lang']}['value'],
								"",
								"",
								"",more,true,""/*$myCat->NVNom['value']*/,
                                                     $myP->NDate['value'],"", $pagePRIV);
			}
			//////END CARDS EXPLORER
			$cat->Draw_Navigator_Line();
		}else{	?><div style="color:#999;width:90%;border:1px #ccc solid;padding:15px;margin:auto;text-align: center;"><?=No_Rows_Selected?></div><?	}
// Or emprty result, then display message as usual
			
		$myframe->close_box("panel");	
	break;
	//DELETE_______________________________________________________________________________
	case "d":
		/***/IS_SECURE($pagePRIV);/***/		
		if ($myP->NID['IsNew'])  break;
		
		$myframe->open_box("withTree", Del_x,"panel", $pagePRIV, $adding);
		$myP->DisplayDelMsg();
		$myframe->close_box("panel");
	
	break;	
}
/*************************   related pages  ******************************/
$myframe->Display_Related_Pages(Categories, array("A", $pagePRIV));

$myframe->footer();
?>
42) ../pages/contactus.php               (CONTACT US & ABOUT Pages)
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
43) ../pages/download.php              (SOFTWARE DOWNLOADS Page)

// by now every thing seems to be routine, we are going to explain non routine thing by now

<?php
ob_start();
include_once '../common/pframe.php';
include_once '../obj/product.class.php';
include_once '../cms/navigator.php';

$myframe=new pframe();
$myframe->header(View_Downloads);
$myDwn=new Product($_REQUEST['NID']);
$pagePRIV = "DOWNLOADS_MAN";
if(user_has_permission(array("A", $pagePRIV))){	$adding ="";/*if adding is about another OBJ*/	}else{$adding=""; $pagePRIV="";	}

$increase_nvnom=$_REQUEST['increase_nvnom'];   // increase number of downloads

switch ($_REQUEST['v'])
{
	case "e": /***********************   add, edit   ***************************/
	/***/IS_SECURE($pagePRIV);/***/		
		if ($_REQUEST['NID'] == "add_existing"){	$ttl=Add_x;	}else { $ttl=Edit_x;  }
		
		$sql="select * from product where prod_exe ='' or prod_exe is null";
		$tbl=table($sql);
	
		if($GLOBALS['lang']=='ar'){		$lang='ar';	$nolang='en';	}
		else{	$lang='en';  	$nolang='ar';		}
		
		// changing the way fields are shown (Not appearing as usual)

		$myDwn->{prod_title_x}['control'] = 'none';		
		$myDwn->prod_id['control'] = 'fkey';
		$myDwn->prod_id['ftbl'] = 'product';
		$myDwn->prod_id['fTitle'] = prod_title_x;
		$myDwn->prod_id['fID'] = 'prod_id';
		$myDwn->prod_id['fFltr'] = " where prod_exe ='' or prod_exe is null ";		
		/***********************   hide other fields   ***************************/
		$myDwn->{'prod_title_'.$nolang}['control'] = 'none';
		$myDwn->{'prod_desc_'.$nolang}['control'] = 'none';
		$myDwn->{'prod_text_'.$nolang}['control'] = 'none';
		$myDwn->prod_cat['control'] = 'none';
		$myDwn->{'prod_desc_'.$lang}['control'] = 'none';
		$myDwn->{'prod_text_'.$lang}['control'] = 'none';
		$myDwn->prod_pic['control'] = 'none';
		$myDwn->prod_price['control'] = 'none';
		/***********************   ability to upload file   ***************************/
		$myDwn->prod_exe['control'] = 'file';
		$myDwn->prod_exe['prefix'] = 'EXE_';
		
		//when editing fill the ID (hidden) value from GET
		if($_REQUEST['NID'] != 'add_existing' && $_REQUEST['v'] == 'e'){
			$myDwn->prod_id['control'] = 'hidden';
			$myDwn->prod_id['value'] = $_REQUEST['NID'];
		}
		
		$myframe->open_box("withTree", $ttl,"panel", $pagePRIV, $adding);
		$myDwn->DisplayEditor("_n");
		$myframe->close_box("panel");
		
		//to update GET parameters when selected changes
		$url = "download.php?lang={$GLOBALS['lang']}&v=e&NID=";		
		?>
		<script>
			//redirect to modify NID parameter according to the selected option
			$( "#txt_prod_id" ).change(function() {
				location.href = <?=json_encode($url)?> + $(this).val();
				});
			// adding an empty option at the beginning of the list
			$("#txt_prod_id").prepend("<option value='add_existing' selected='selected'>...........</option>");
			$("#txt_prod_id").val(<?=json_encode($_REQUEST['NID'])?>);
		</script>
		<?
	break;
		
	case "t"; /***********************   All elements   ***************************/
	
	$myframe->open_box("withTree", View_Downloads,"panel", $adding);
	
	$wherestr=" where prod_exe <> ''";
	$sql="select * from {$myDwn->tblname} {$wherestr} order by prod_only_dw desc";
	$serv=new Navigator($sql, $_GET['cur_page'], 10, "select count(NID) from {$myDwn->tblname} {$wherestr} ");
	//
	?><table class="global_tbl  sortable">
	<? if(@mysql_num_rows(mysql_query($sql)) == 0){?><tr><td><?=No_Rows_Selected?></td></tr><?}else{?>
		<tr>
			<th><?=prod_title_ar?></th>
			<th><?=prod_desc_ar?></th>
			<th><?=prod_cat?></th>
			<th><?=prod_price?></th>
			<th><?=NVnom?></th>
			<th><?=Download?></th>
		</tr><?
	////START CARDS NAVIGATOR
	$i=0;
	$tr_class="";
	while ($Row=mysql_fetch_array($serv->result))
	{
		$myDwn->FillIn($Row);

// until now every thing is routine

		$exe_path="../documents/exe/{$myDwn->prod_id['value']}.{$myDwn->prod_exe['value']}";
		if($i%2==0){$tr_class="tr_1";}else{$tr_class="tr_2";}
		?><tr class="<?=$tr_class?> <? if($_REQUEST['NID'] == $myDwn->prod_id['value']){ echo "tr_highlighted";}?>">
			<td class=""><?=$myDwn->{'prod_title_'.$GLOBALS['lang']}['value']?></td>
			<td class=""><?=$myDwn->{'prod_desc_'.$GLOBALS['lang']}['value']?></td>
			<td class=""><?=get_data_in("select ".cat_title_x." from category where cat_id='".$myDwn->prod_cat['value']."'",cat_title_x)?></td>
			
			<td class=""><?=$myDwn->prod_price['value']?></td>
			<td class=""><?=$myDwn->NVNom['value']?></td>
			<td class="download_td"><a href="<?=$exe_path?>" target="_blank"><img  onclick="on_click_increase_nvnom('<?=$myDwn->prod_id['value']?>')" src="../images/dwimg.png"/></a></td>

// Admin Tools
			<? if(user_has_permission(array("A"))) {?>
			<td class="admin_tools_td"><?$myframe->DisplayAdminTools("download", $myDwn->prod_id['value']);  }?></td>
		</tr><?
		$i++;
	} }
	?></table><?
	$serv->Draw_Navigator_Line();
	$myframe->close_box("panel");
	break;
	
	case "increase_nvnom"; /***** increase number of downloads when clicking download button ****/

		$upd="update product set NVNom=NVNom+1 where prod_id = '{$_REQUEST["NID"]}' ";
		cmd($upd);echo $upd;
		header("location: ../pages/download.php?lang={$GLOBALS['lang']}&v=t");
	break;
	
	case "d": /***********************   delete   ***************************/
		/***/IS_SECURE($pagePRIV);/***/
				
		$myframe->open_box("withTree", Del_x,"panel", $pagePRIV, $adding);
		$myDwn->DisplayDelMsg("download");
		$myframe->close_box("panel");
		break;
}

/*************************   increase number of downloads  ******************************/
?><script type="text/javascript">
	function on_click_increase_nvnom(pid) 
	{
		window.location = "../pages/download.php?lang=<?=$GLOBALS['lang']?>&v=increase_nvnom&NID="+pid;
	}
</script><?
/*************************   related pages  ******************************/
$myframe->Display_Related_Pages(Download,array("A", $pagePRIV));

$myframe->footer();
?>
 
44) ../pages/faq.php                   (Frequently Asked Questions Pages)
 
// we are going to explain ONLY non-routine thing by now
 
<?php
include_once '../common/pframe.php';
include_once '../obj/faq.class.php';
include_once '../cms/navigator.php'; 

$myframe=new pframe();
$myframe->header(FAQ);
$myFAQ=new faq($_REQUEST['NID']);
$pagePRIV = "FAQ_MAN";
if(user_has_permission(array("A", $pagePRIV))){	$adding ="";/*if adding is about another OBJ*/	}else{$adding=""; $pagePRIV="";	}

switch ($_REQUEST['v'])
	{
	//EDITOR________________________________________________________________________________
	case "e":

	// routine as usual for editor
	/***/IS_SECURE($pagePRIV);/***/		
		if ($myFAQ->NID['IsNew']){	$ttl=Add_x; }else { $ttl=Edit_x; }
		
		$myframe->open_box("withTree", $ttl,"panel", $pagePRIV, $adding);
		$myFAQ->DisplayEditor("_n");
		$myframe->close_box("panel");
	
	break;
	//Viewer________________________________________________________________________________
	case "c":
	
// also routine for card
		$myframe->open_box("withTree", $myFAQ->{'faq_title_'.$GLOBALS['lang']}['value'],"panel", $pagePRIV, $adding);
		$myframe->card(
				$myFAQ->faq_id['value'],
				$myFAQ->{'faq_title_'.$GLOBALS['lang']}['value'],
				$myFAQ->{'faq_desc_'.$GLOBALS['lang']}['value'],
				"",
				"","no_more",true,$myFAQ->NVNom['value'],
				$myFAQ->NDate['value'],"",$pagePRIV);
	
		$myFAQ->More_DVNom();	
		$myframe->close_box("panel");
	
	break;	
	//TABLE________________________________________________________________________________
	case "t":
		
		$myframe->open_box("withTree", FAQ,"panel", $adding);
		$wherestr="  ";
		$sql="select * from {$myFAQ->tblname} {$wherestr} order by NDate desc";
		$faq=new Navigator($sql, $_GET['cur_page'], 6, "select count(NID) from {$myFAQ->tblname} {$wherestr} ");
		if(@mysql_num_rows(mysql_query($sql)) != 0)
		{
			/////START CARDS EXPLORER		
			while ($faqRow=mysql_fetch_array($faq->result)){
				
				$myFAQ->FillIn($faqRow);
				
				$myframe->card($myFAQ->NID['value'],
								$myFAQ->{'faq_title_'.$GLOBALS['lang']}['value'],
								$myFAQ->{'faq_desc_'.$GLOBALS['lang']}['value'],
								"",
								"","no_more",true,"",""
                                     /*$myFAQ->NVNom['value'],$myFAQ->NDate['value']*/,"", $pagePRIV);
			}
			//////END CARDS EXPLORER
			$faq->Draw_Navigator_Line();
		}else{	?><div style="color:#999;width:90%;border:1px #ccc solid;padding:15px;margin:auto;text-align: center;"><?=No_Rows_Selected?></div><?	}
		$myframe->close_box("panel");
		?>

		<script>
//Here is the Javascript for the "show details when title clicked" feature
// Auto height animation	
		jQuery.fn.animateAuto = function(prop, speed, callback){
		    var elem, height, width;
		    return this.each(function(i, el){
		        el = jQuery(el), elem = el.clone().css({"height":"auto","width":"auto"}).appendTo("body");
		        height = elem.css("height"),
		        width = elem.css("width"),
		        elem.remove();
		        
		        if(prop === "height")
		            el.animate({"height":height}, speed, callback);
		        else if(prop === "width")
		            el.animate({"width":width}, speed, callback);  
		        else if(prop === "both")
		            el.animate({"width":width,"height":height}, speed, callback);
		    });  
		}

		$( ".item_txt" ).height(0) ;

			//////// when clicking on a title the description appears under it
	
			$( ".item_title" ).click(function() { 
		    if ( $(this).next( ".item_txt" ).height() != 0)
		    	$(this).next( ".item_txt" ).animate({ height: 0 }, 400 );
		    else{
		    	$(this).next( ".item_txt" ).animateAuto("height", 1000);
		    }
		});
		</script><? 	
	break;
	//DELETE_______________________________________________________________________________
	case "d":
		/***/IS_SECURE($pagePRIV);/***/			
		if ($myFAQ->NID['IsNew'])  break;
		
		$myframe->open_box("withTree", Del_faqs,"panel", $pagePRIV, $adding);
		$myFAQ->DisplayDelMsg();
		$myframe->close_box("panel");
	
	break;
}
/*************************   related pages  ******************************/
$myframe->Display_Related_Pages(FAQ,array("A", $pagePRIV));

$myframe->footer();
?>
45) ../pages/jobs.php                    (JOBS we are offering)

// we are going to explain ONLY non-routine thing by now

<?php
include_once '../common/pframe.php';
include_once '../obj/job.class.php';
include_once '../cms/navigator.php'; 

$myframe=new pframe();
$myframe->header(View_Jobs);
$myP=new Job($_REQUEST['NID']);
$pagePRIV = "JOBS_MAN";
if(user_has_permission(array("A", $pagePRIV))){	$adding ="";/*if adding is about another OBJ*/	}else{$adding=""; $pagePRIV="";	}

switch ($_REQUEST['v'])
	{
	//EDITOR________________________________________________________________________________
	case "e":	
		
	/***/IS_SECURE($pagePRIV);/***/	
		if ($myP->NID['IsNew']){	$ttl=Add_x; }else { $ttl=Edit_x; }
		
		$myframe->open_box("withTree", $ttl,"panel", $pagePRIV, $adding);
		$myP->DisplayEditor("_n");
		$myframe->close_box("panel");
	
	break;
	//Viewer________________________________________________________________________________
	case "c":
	
		$myframe->open_box("withTree", $myP->{'job_title_'.$GLOBALS['lang']}['value'],"panel", $pagePRIV, $adding);
		$myframe->card(
				$myP->job_id['value'],
				$myP->{'job_title_'.$GLOBALS['lang']}['value'],
				$myP->{'job_desc_'.$GLOBALS['lang']}['value'],
				"",
				"",more,true,""/*$myJob->NVNom['value']*/,
				$myP->NDate['value'],"",$pagePRIV);
	
		$myP->More_DVNom();	
		$myframe->close_box("panel");
	
	break;	
	//TABLE_________________________________________________________________________________
	case "t":
		
		$myframe->open_box("withTree", Jobs, "panel", $adding);
		$wherestr="  ";
		$sql="select * from {$myP->tblname} {$wherestr} order by NDate desc";
		$job=new Navigator($sql, $_GET['cur_page'], 6, "select count(NID) from {$myP->tblname} {$wherestr} ");
		
		if(@mysql_num_rows(mysql_query($sql)) != 0)
		{
			/////START CARDS EXPLORER
			while ($jobRow=mysql_fetch_array($job->result)){
				
				$myP->FillIn($jobRow);
				$myframe->card($myP->NID['value'],
								$myP->{'job_title_'.$GLOBALS['lang']}['value'],
								$myP->{'job_desc_'.$GLOBALS['lang']}['value'],
								"",
								"",/*more*/"no_more",true,""
                                      /*$myJob->NVNom['value']*/,$myP->NDate['value'],"", $pagePRIV);
			}
			//////END CARDS EXPLORER
			$job->Draw_Navigator_Line();
		}else{
			?><div style="color:#999;width:90%;border:1px #ccc solid;padding:15px;margin:auto;text-align: center;"><?=No_Rows_Selected?></div><?
			}
		$myframe->close_box("panel");
		?>
				<script>
				// Auto height animation	
				jQuery.fn.animateAuto = function(prop, speed, callback){
				    var elem, height, width;
				    return this.each(function(i, el){
				        el = jQuery(el), elem = el.clone().css({"height":"auto","width":"auto"}).appendTo("body");
				        height = elem.css("height"),
				        width = elem.css("width"),
				        elem.remove();
				        
				        if(prop === "height")
				            el.animate({"height":height}, speed, callback);
				        else if(prop === "width")
				            el.animate({"width":width}, speed, callback);  
				        else if(prop === "both")
				            el.animate({"width":width,"height":height}, speed, callback);
				    });  
				}
		
				$( ".item_txt" ).height(0) ;
					
//////// when clicking on a title the description appears under it

					$( ".item_title" ).click(function() { 
				    if ( $(this).next( ".item_txt" ).height() != 0)
				    	$(this).next( ".item_txt" ).animate({ height: 0 }, 400 );
				    else{
				    	$(this).next( ".item_txt" ).animateAuto("height", 1000);
				    }
				});
				</script><? 
		
	break;
	//DELETE_______________________________________________________________________________
	case "d":
	/***/IS_SECURE($pagePRIV);/***/	
		if ($myP->NID['IsNew'])  break;
		
		$myframe->open_box("withTree", Del_x, "panel", $pagePRIV, $adding);
		$myP->DisplayDelMsg();
		$myframe->close_box("panel");
	
	break;	

}
/*************************   related pages  ******************************/
$myframe->Display_Related_Pages(Jobs,array("A", $pagePRIV));

$myframe->footer();
?> 
46) ../pages/groups.php                      (GROUPS MANAGEMENT)

// we are going to explain ONLY non-routine thing by now

<?php 
include_once '../common/pframe.php';
include_once '../common/privileges.php';
include_once '../cms/navigator.php';
include_once "../obj/groups.class.php";

$myframe=new pframe();
$myframe->header(Groups);
$myGroup = new Groups($_REQUEST['NID']);
$pagePRIV = "GROUPS_MAN";
if(user_has_permission(array("A", $pagePRIV))){	$adding ="";/*if adding is about another OBJ*/	}else{$adding=""; $pagePRIV="";	}
if(isset($_SESSION['group_id'])) //if we were adding a user and came back here
	unset($_SESSION['group_id']);

switch ($_REQUEST['v'])
{
	case "members": /************** members editor **************/
		/***/IS_SECURE($pagePRIV);/***/
		if ($myGroup->NID['IsNew']){	$ttl=Group_members." : ".Add_x;	}else { $ttl=Group_members." : ".Edit_x;	}
	
		$myframe->open_box("withTree", $ttl, "panel" ,$pagePRIV  ,$adding);
	
		if($_REQUEST['group'] != ''){
			//echo '<div>'.Group_members.'</div></br>';
			MembersEditor($pagePRIV);
		}
		$myframe->close_box("panel");
		$_SESSION['back_page'] = "groups";	
	break;
		
	case "e": /************** edit group & Privileges Editor **************/
	/***/IS_SECURE($pagePRIV);/***/		
		if ($myGroup->NID['IsNew']){	$ttl=Add_groups;	}else { $ttl=Edit_groups;		}
		
		$myframe->open_box("withTree", $ttl, "panel", $pagePRIV, $adding);
		
		$myGroup->DisplayEditor("_n");
		
		if(!$myGroup->NID['IsNew']){
			echo '<div>'.Privileges_Message_Empty.'</div></br>';
			PrivilegesEditor($pagePRIV);}
		$myframe->close_box("panel");
		
	break;
	
	case "t": /*************** All groups with users inside each one **************/
	/***/IS_SECURE($pagePRIV);/***/
		$_SESSION['access_to_all_privs']=$pagePRIV;
		$myframe->open_box("withTree", View_groups,"panel",$pagePRIV , $adding);
		$wherestr="";
		$sql="select * from {$myGroup->tblname} {$wherestr} order by 2 desc";
		$grp=table($sql);
				
		if(@mysql_num_rows(mysql_query($sql)) != 0)
		{
			?><div class="groups"><? 
			?><ul class="collapsibleList"  id="ul_<?=$myGroup->NID['value']?>"><? 
			/////START CARDS EXPLORER				
			while ($GroupRow=mysql_fetch_array($grp)){
				
				$myGroup->FillIn($GroupRow);
				?>
				<li class="li_group"  id="li_<?=$myGroup->NID['value']?>">
					<?=$myGroup->group_name['value']?>
					<ul class="ul_user"  id="<?=$myGroup->NID['value']?>"><? 						
						
						$where=" where user_id in (select user_id from user_groups where user_group_id ='{$myGroup->group_id['value']}' )  ";
						$sql2="select * from user {$where} order by user_name";
						$usr=table($sql2);
						while ($UsrRow=mysql_fetch_array($usr)){
							
							$href="../pages/users.php?lang={$GLOBALS['lang']}&v=c&NID={$UsrRow['user_id']}&group={$myGroup->NID['value']}";
							?><a class="a_li_user" href="<?=$href?>"><li class="li_user"><?=$UsrRow['user_name']?></li></a><?  
						}
						?>
						<li class="li_gr_privs">[[[[[[[[ &nbsp;&nbsp;<?=Privileges?> &nbsp;&nbsp; ]]]]]]]] <div>&nbsp;</div><? 
				 			this_group_privs($myGroup->NID['value'], false);
				 		?></li><?					
							 		 
						?><li class="tools"><?
							?><div style=""><? 
								$myframe->DisplayAdminTools("groups",$myGroup->NID['value']);
								$myframe->DisplayAddingTool($pagePRIV, "groups", "&v=members&group={$myGroup->NID['value']}");
							?></div><? 
						?></li><?
					?></ul>
				</li><? 
				$_SESSION['back_page'] = "groups";
			}?>
			<script>
				// make the appropriate lists collapsible
				CollapsibleLists.apply();
				//when we come back from adding a user, it's group opens
				var v=document.getElementById("<?echo $_REQUEST['NID'];?>");
				v.style.display = "block";
				var v=document.getElementById("li_<?echo $_REQUEST['NID'];?>");
				v.className = 'li_group collapsibleListOpen';				
			</script>	
					
		</ul></div><? 
		}else{	?><div style="color:#999;width:90%;border:1px #ccc solid;padding:15px;margin:auto;text-align: center;"><?=No_Rows_Selected?></div><?	}			
		$myframe->close_box("panel");
	break;
	
	case "d": /************** delete **************/
		/***/IS_SECURE($pagePRIV);/***/
		$myframe->open_box("withTree", Del_groups,"panel",$pagePRIV ,$adding);
		$myGroup->DisplayDelMsg();
		$myframe->close_box("panel");
	break;
}

/*************************   related pages  ******************************/
$myframe->Display_Related_Pages(Services,array("A", $pagePRIV));

$myframe->footer();
?>
46) ../pages/location.php                      (MAP)

<!DOCTYPE html>
<html>
<head>
<script src="http://maps.googleapis.com/maps/api/js"></script>
<script>
function initialize() { // Draws map for our location
  var mapProp = {
    center:new google.maps.LatLng(33.5074755,36.2828954),
    zoom:10,
    mapTypeId:google.maps.MapTypeId.HYBRID
  };
  var map=new google.maps.Map(document.getElementById("googleMap"),mapProp);
}
google.maps.event.addDomListener(window, 'load', initialize);
</script>
</head>

<body>
<div id="googleMap" style="width:600px;height:600px;margin:auto;"></div>
</body>

</html>
46) ../pages/maillist.php                      (MAIL LIST MANAGEMTENT)

// we are going to explain ONLY non-routine thing by now

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
46) ../pages/mconts.php                      (MAINTENANCE CONTRACTS)
 
// we are going to explain ONLY non-routine thing by now
 
<?php
session_start();
include_once '../common/pframe.php';
include_once '../obj/maint_contract.class.php';
include_once '../cms/navigator.php'; 

$myframe=new pframe();
$myframe->header(View_mconts);
$myMcont=new Maint_contract($_REQUEST['NID']);
$pagePRIV = "MCONTS_MAN";
if(user_has_permission(array("A", $pagePRIV))){	$adding ="";/*if adding is about another OBJ*/	}else{$adding=""; $pagePRIV="";	}

switch ($_REQUEST['v'])
{
case "e": //EDITOR____________________________________________________________________________________________

		/***/IS_SECURE($pagePRIV);/***/
	
	//bcont_serial from get param.. or else we can choose
	if($_REQUEST['bcont'] != ''){	$myMcont->bcont_serial['control']="text";$myMcont->bcont_serial['value']=$_REQUEST['bcont'];		}
	//else{$myMcont->bcont_serial['value'] = $myframe->Get_Incremental_ID("buy_contract","NSerial");}

	
	//when creating/editing mconts it's not allowed to change dist/cust, they fill automatically from SESSION . . .
	//------------
	if ($myMcont->NID['IsNew']){	$ttl=Add_x;  $myMcont->NSerial['value'] = $myframe->Get_Incremental_ID($myMcont->tblname,"NSerial");}
	else { $ttl=Edit_x; }
	
	if($myframe->Is_Customer()){ 
		$myMcont->bcont_serial['fFltr']= " where bcont_cust = '{$_SESSION['GID']}'"; 
		$myMcont->mcont_cust['value']=$_SESSION['GID'];
		$bcont_dist = get_data_in("select bcont_dist from buy_contract where NSerial='{$_REQUEST['bcont']}' ", "bcont_dist");
		$myMcont->mcont_dist['control'] = 'hidden'; $myMcont->mcont_dist['value'] = $bcont_dist;
		$myMcont->mcont_cust['control'] = 'hidden'; $myMcont->mcont_cust['value'] = $_SESSION['GID'];
	}	
	
	//distributor creates mcont with his id and cust id of the bcont (( not other ))

	if($myframe->Is_Distributor()){
		$bcont_cust = get_data_in("select bcont_cust from buy_contract where NSerial='{$_REQUEST['bcont']}' ", "bcont_cust");
		$myMcont->mcont_dist['control'] = 'hidden'; $myMcont->mcont_dist['value'] = $_SESSION['GID'];
		if($_REQUEST['bcont'] !=''){$myMcont->mcont_cust['control'] = 'hidden'; $myMcont->mcont_cust['value'] = $bcont_cust;}
		else{$myMcont->bcont_serial['fFltr']= "where bcont_dist='".$_SESSION['GID']."'";
		$myMcont->mcont_cust['control'] = 'hidden';}
		
		//prevent distributor from changing parameters or copy links to access others bconts

		if($_SESSION['GID'] != get_data_in("select mcont_dist from maint_contract where NSerial='{$_REQUEST['NID']}'", "mcont_dist") && $_REQUEST['NID'] != 'IsNew') {
			IS_SECURE("", "not_secure");		
		}
	}	
	
	$myframe->open_box("withTree", $ttl,"panel", $pagePRIV, $adding);
	$myMcont->DisplayEditor("_n");
	$myframe->close_box("panel");
	
// LOCK some field for protection

		?><script>var selct=document.getElementById("txt_NSerial");		selct.disabled = true;	</script><?
		
	if($_REQUEST['bcont']){
		?><script>		
			var selct=document.getElementById("txt_bcont_serial");	selct.disabled = true;
		</script><?
	} 
	if($myframe->Is_Customer()){ 
		 ?><script>
				var selct=document.getElementById("txt_mcont_cust");			selct.disabled = true;
				var selct=document.getElementById("txt_mcont_status");			selct.disabled = true;		
			</script><?
	}
	$url = "mconts.php?lang={$GLOBALS['lang']}&v=e&NID=IsNew&bcont=";
	?><script>
	$("#txt_bcont_serial").val(<?=json_encode($_SESSION['GID'])?>);

			//redirect to modify NID parameter according to the selected option
			$( "#txt_bcont_serial" ).change(function() {
				location.href = <?=json_encode($url)?> + $(this).val();
				});
			// adding an empty option at the beginning of the list
			$("#txt_bcont_serial").prepend("<option value='' selected='selected'>...........</option>");
			$("#txt_bcont_serial").val(<?=json_encode($_REQUEST['bcont'])?>);
			</script><? 	
break;

case "c": //Card Viewer___________________________________________________________________________
	
/***/IS_SECURE($pagePRIV);/***/
	if($myMcont->seen['value']==0 && $myframe->Is_Distributor()){
		cmd("update maint_contract set mcont_seen=1 where NSerial='{$_REQUEST['NID']}'");
	}

	$myframe->open_box("withTree", $myMcont->NSerial['value'],"panel", $pagePRIV, $adding);
	$myframe->card(
			$myMcont->NSerial['value'],
			mcont_serial." : ".$myMcont->NSerial['value'].'<br/><br/>',
			Distributor_Name." : ".get_data_in("select user_name from user where user_id = '{$myMcont->mcont_dist['value']}'", 'user_name').'<br/><br/>'.
			Customer_Name." : ".get_data_in("select user_name from user where user_id = '{$myMcont->mcont_cust['value']}'", 'user_name').'<br/><br/>'.
				mcont_desc." : ".$myMcont->mcont_desc['value'] .'<br/><br/>'.			bcont_serial." : ".$myMcont->bcont_serial['value'].'<br/><br/>'.
				mcont_status." : ".$myMcont->mcont_status['value'] .'<br/><br/>'.		NDate." : ".$myMcont->NDate['value'] ,
			"",
			"","",false,
			$myMcont->mcont_status['value'],
			$myMcont->NDate['value'],"",$pagePRIV);
break;

case "t"://TABLE__________________________________________________________________________________
	
/***/IS_SECURE($pagePRIV);/***/
	$_SESSION['bcont'] = $_REQUEST['bcont'];
	
	if($myframe->Is_Distributor() && get_data_in("SELECT bcont_seen FROM buy_contract WHERE NSerial = '{$_REQUEST['bcont']}' ", "bcont_seen")==0){
		cmd("update buy_contract set bcont_seen=1 where NSerial='{$_REQUEST['bcont']}'");
	}
	$myframe->open_box("withTree", mconts,"panel",$pagePRIV , $adding);
	
	//if the user doesn't have any BConts, he can't create any MConts !!

	if($myframe->Is_Customer()){$me='bcont_cust';}
	elseif($myframe->Is_Distributor()){$me='bcont_dist';}
	$sql = "select * from buy_contract where {$me}='{$_SESSION['GID']}' ";
	if(@mysql_num_rows(mysql_query($sql)) == 0){		echo No_MConts;$Have_BConts = false;
	}else{
		$Have_BConts = true;
	$and="";
	$wherestr=" where ";
	if($myframe->Is_Customer()){$wherestr.=" mcont_cust='{$_SESSION['GID']}' "; $and=" and ";}
	if($myframe->Is_Distributor()){$wherestr.=" mcont_dist='{$_SESSION['GID']}' "; $and=" and ";}	
	if($_REQUEST['bcont']){	$wherestr .= " {$and} bcont_serial='{$_REQUEST['bcont']}'";	$and="2";}
	if($and ==''){$wherestr="";}
	
	$sql="select * from {$myMcont->tblname} {$wherestr} order by NDate desc";
	$serv=new Navigator($sql, $_GET['cur_page'], 20, "select count(NID) from {$myMcont->tblname} {$wherestr} ");
	//
	?><table class="global_tbl sortable">
	<? if(@mysql_num_rows(mysql_query($sql)) == 0){?><tr><td><?=No_Rows_Selected?> </td></tr><?}else
	{
		?><tr>
			<th><?=mcont_serial?></th>
			<th><?=BConts?></th>
			<th><?=Distributor_Name?></th>
			<th><?=Customer_Name?></th>
			<th><?=bcont_prod?></th>
			<th><?=mcont_status?></th>
			<th><?=NDate?></th>
		</tr>
		<?
		////START CARDS NAVIGATOR
		$i=0;
		$tr_class="";
		
		while ($Row=mysql_fetch_array($serv->result))
		{
			$myMcont->FillIn($Row);
			if($i%2==0){$tr_class="tr_1";}else{$tr_class="tr_2";}
			if(($myMcont->mcont_dist['value'] == $_SESSION['GID']) || user_has_permission(array("A", $pagePRIV))){
			?>
			
			<tr class="<?=$tr_class?> <? if($_REQUEST['NID'] == $myMcont->NSerial['value']){ echo "tr_highlighted";}?>" onclick="document.location = '../pages/mconts.php?lang=<?=$GLOBALS["lang"]?>&v=c&bcont=<?=$myMcont->bcont_serial['value']?>&prod=<?=$_REQUEST['prod']?>&NID=<?=$myMcont->NSerial['value']?>';">
				<td class="<?if($myMcont->mcont_seen['value']!=1){echo "unseen_td";}?>"><?=$myMcont->NSerial['value']?></td>
				<td class=""><a class="goto_bcont" href="../pages/bconts.php?lang=<?=$GLOBALS['lang']?>&NID=<?=$myMcont->bcont_serial['value']?>&v=t" ><?=$myMcont->bcont_serial['value']?></a></td>
				<td class=""><?=get_data_in("select user_name from user where user_id = '{$myMcont->mcont_dist['value']}'", 'user_name')?></td>
				<td class=""><?=get_data_in("select user_name from user where user_id = '{$myMcont->mcont_cust['value']}'", 'user_name')?></td>
				
				<? $bcont_prod = get_data_in("select bcont_prod from buy_contract where NSerial = '{$myMcont->bcont_serial['value']}'", 'bcont_prod'); ?>
				<td class=""><?=get_data_in("select ".prod_title_x." from product where prod_id = '{$bcont_prod}'", prod_title_x)?></td>
				<td class=""><?=$myMcont->mcont_status['value']?></td>
				<td class=""><?=$myMcont->NDate['value']?></td>
				<? if(user_has_permission(array("A", $pagePRIV))) {?><td class="admin_tools_td"><?=$myframe->DisplayAdminTools("mconts", $myMcont->bcont_serial['value']."/".$bcont_prod."/".$myMcont->NSerial['value'])?></td><? }
			?></tr><?
			}
			$i++;
		}  
	}
	?></table><?
	//////END CARDS NAVIGATOR
	$serv->Draw_Navigator_Line();
	$myframe->close_box("panel");
}
break;
//DELETE________________________________________________________________________________________
case "d":
/***/IS_SECURE($pagePRIV);/***/	
	if ($myMcont->NID['IsNew'])  break;	
	
	$myframe->open_box("withTree", Del_x,"panel", $pagePRIV, $adding);
	$myMcont->DisplayDelMsg();
	$myframe->close_box("panel");

break;
}
/*************************   related pages  ******************************/
	if($Have_BConts){
		$myframe->Display_Related_Pages(mconts, array("A", $pagePRIV));
	}
$myframe->footer();
?>
47) ../pages/news.php                    (NEWS Page)
<?php

include_once '../common/pframe.php';
include_once '../obj/news.class.php';
include_once '../cms/navigator.php';

$myframe=new pframe();
$myframe->header(View_News);
$myNews=new News($_REQUEST['NID']);
$pagePRIV="NEWS_MAN";
if(user_has_permission(array("A", $pagePRIV))){	$adding ="";/*if adding is about another OBJ*/	}else{$adding=""; $pagePRIV="";	}

switch ($_REQUEST['v'])
	{
	//EDITOR_________________________________________________________________________________
	case "e":	
	/***/IS_SECURE($pagePRIV);/***/		
		if ($myNews->NID['IsNew']){	$ttl=Add_News; } else{ $ttl=Edit_News; }
		
		$myframe->open_box("withTree", $ttl,"panel", $adding);
		$myNews->DisplayEditor("_n");
		$myframe->close_box("panel");
	
	break;
	//Card____________________________________________________________________________________
	case "c":
	
		$myframe->open_box("withTree", $myNews->{'news_title_'.$GLOBALS['lang']}['value'],
                                                                                 "panel", $adding);
		$myframe->card($myNews->NID['value'],
                            $myNews->{'news_title_'.$GLOBALS['lang']}['value'],
                            $myNews->{'news_text_'.$GLOBALS['lang']}['value'],
                            $myNews->Draw_Photo($myNews->news_pic,
                            "thumb", "", $vid, "lightwindow"),"",more,true,
                            $myNews->NVNom['value'], $myNews->NDate['value'],"",$pagePRIV);
	
		$myNews->More_DVNom();	
		$myframe->close_box("panel");
	break;
	//TABLE___________________________________________________________________________________
	case "t":
		$myframe->open_box("withTree", View_News. $_REQUEST['NID'],"panel", $adding);
		
		$wherestr="";
		$sql="select * from {$myNews->tblname} {$wherestr} order by NDate desc ";
		$newstable=new Navigator($sql, $_GET['cur_page'], 6, "select count(NID) from {$myNews->tblname} {$wherestr}");
		if(@mysql_num_rows(mysql_query($sql)) != 0)
		{
			/////START CARDS EXPLORER
			while ($NewsRow=mysql_fetch_array($newstable->result)){
				$myNews->FillIn($NewsRow);
				$myframe->card($myNews->NID['value'],$myNews->{'news_title_'.$GLOBALS['lang']}['value'],$myNews->{'news_desc_'.$GLOBALS['lang']}['value'],$myNews->Draw_Photo($myNews->news_pic, "small", "", $vid , "lightwindow"),"",more,true,$myNews->NVNom['value'],$myNews->NDate['value'],"",$pagePRIV);	
			}
			//////END CARDS EXPLORER
			$newstable->Draw_Navigator_Line("jbtn");
		}else{
			?><div style="color:#999;width:90%;border:1px #ccc solid;padding:15px;margin:auto;text-align: center;"><?=No_Rows_Selected?></div><?
		}
		$myframe->close_box("panel");
	
	break;
	//DELETE__________________________________________________________________________________
	case "d":
	
	/***/IS_SECURE($pagePRIV);/***/
		if ($myNews->NID['IsNew'])  break;
		
		$myframe->open_box("withTree", Del_News,"panel", $pagePRIV, $adding);
		$myNews->DisplayDelMsg();
		$myframe->close_box("panel");
	
	break;	
}

/*************************   related pages  ******************************/
$myframe->Display_Related_Pages(News,array("A", $pagePRIV));

$myframe->footer();
?>
48) ../pages/our_customers.php                       (OUR CUSTOMERS Page)
<?php 
include_once '../db/mysqlcon.php';
include_once '../common/pframe.php';
include_once '../cms/navigator.php';
include_once "../obj/user.class.php";
$myframe=new pframe();
$myframe->header(Our_Customers);
$myCustomer = new User($_REQUEST['NID']);
$pagePRIV = "CUSTOMERS_MAN";
if(user_has_permission(array("A", $pagePRIV))){	$adding ="";/*if adding is about another OBJ*/	}else{$adding=""; $pagePRIV="";	}

switch ($_REQUEST['v']) {
	
	case "e":/*************   editor   ******************************************/
	/***/IS_SECURE($pagePRIV);/***/
		
		$myframe->open_box("", Our_Customers,"panel" ,$pagePRIV ,$adding);
		if ($myCustomer->NID['IsNew']){	$ttl=Add_Customer; }else { $ttl=Edit_Customer;	}
	
		$myCustomer->user_cat['control'] = 'text';  $myCustomer->user_cat['value'] = 'customer';
		$myframe->open_box("withTree", $ttl,"panel", $pagePRIV, $adding);
		$myCustomer->DisplayEditor("_n");
		$myframe->close_box("panel");
		
// lock user category, it must stay customer
		
?><script>	document.getElementById("txt_user_cat").disabled = true;</script><? 
		break;
	
	case "c":/*************   card   ******************************************/
		
		$myframe->open_box("withTree", $myCustomer->user_name['value'],"panel", $pagePRIV ,$adding);
	
		$myframe->user_card($myCustomer, "our_customers", "");//print_r($myCustomer);
	
		$myCustomer->More_DVNom();
	
		$myframe->close_box("panel");
	break;	
	
	case "t":/*************   table   ******************************************/
		
		$myframe->open_box("withTree", Our_Customers,"panel", $pagePRIV, $adding);
		$wherestr=" where user_cat = 'customer' and in_home=1 ";

//bring users with category "customer" from DB

		$sql="select * from {$myCustomer->tblname} {$wherestr} order by user_name desc";
		$cust=new Navigator($sql, $_GET['cur_page'], 10, "select count(NID) from {$myCustomer->tblname} {$wherestr} ");
		//
		?><table class="global_tbl sortable">
		<? if(@mysql_num_rows(mysql_query($sql)) == 0){?><tr><td><?=No_Rows_Selected?></td></tr><?}else{?>
			<tr>
				<th><?=Customer_Name?></th>
				<th><?=user_email?></th>
				<th><?=user_phone?></th>
				<th><?=user_address?></th>
				<th><?=user_country?></th>
				<th><?=user_city?></th>
			</tr>
		<?
		////START CARDS NAVIGATOR
		$i=0;
		$tr_class="";
		while ($Row=mysql_fetch_array($cust->result))
		{
			$myCustomer->FillIn($Row);$id=$myCustomer->user_id["value"];
			if($i%2==0){$tr_class="tr_1";}else{$tr_class="tr_2";}
			?>
			<tr class="<?=$tr_class?>" onclick="document.location = '../pages/our_customers.php?lang=<?=$GLOBALS["lang"]?>&v=c&NID=<?=$myCustomer->NID['value']?>';">
				<td class=""><?=$myCustomer->user_name['value']?></td>
				<td class=""><?=$myCustomer->user_email['value']?></td>
				<td class=""><?=$myCustomer->user_phone['value']?></td>
				<td class=""><?=$myCustomer->user_address['value']?></td>
				<td class=""><?=$myCustomer->user_country['value']?></td>
				<td class=""><?=$myCustomer->user_city['value']?></td>
				<? if(user_has_permission(array("A", $pagePRIV))) {?><td class="admin_tools_td"><?=$myframe->DisplayAdminTools("our_customers", $myCustomer->user_id['value'])?></td><? }
			?></tr><?
			$i++;
		}}
		?></table><?
		//////END CARDS NAVIGATOR
		$cust->Draw_Navigator_Line();
		$myframe->close_box("panel");
	break;
	
	case "d":/*************   delete   ******************************************/
		
	/***/IS_SECURE($pagePRIV);/***/
		if ($myCustomer->NID['IsNew'])  break;
		
		$myframe->open_box("withTree", Our_Customers,"panel", $pagePRIV, $adding);
		$myCustomer->DisplayDelMsg();
		$myframe->close_box("panel");
	break;
}
?><style>.global_tbl td{border:0px #ccc solid;padding:2px 8px;max-width:150px;overflow:hidden;white-space:normal;}</style><? 

/*************************   related pages  ******************************/
$myframe->Display_Related_Pages(Our_Customers,array("A", $pagePRIV));
?>
49) ../pages/pictures.php
<?php
include_once '../common/pframe.php';
include_once '../obj/picture.class.php';
include_once '../cms/navigator.php'; 

$myframe=new pframe();
$myframe->header(View_pictures);
$myPIC=new picture($_REQUEST['NID']);
$pagePRIV = "PICTURES_MAN";
if(user_has_permission(array("A", $pagePRIV))){	$adding ="";/*if adding is about another OBJ*/	}else{$adding=""; $pagePRIV="";	}

switch ($_REQUEST['v'])
{
//EDITOR_______________________________________________________________________________________
case "e":

/***/IS_SECURE($pagePRIV);/***/

	if ($myPIC->NID['IsNew']){	$ttl=Add_Pic; }else { $ttl=Edit_Pic; }
	
	$myframe->open_box("withTree", $ttl,"panel", $pagePRIV, $adding);
	$myPIC->DisplayEditor("_n");
	$myframe->close_box("panel");
	?>
	<script>

// the text box of the album is locked with value of session var

		$("#txt_pic_album").val(<?=json_encode($_SESSION['album'])?>);
	</script>
	<?
break;
//Viewer________________________________________________________________________________________
case "c":

	$myframe->open_box("withTree", $myPIC->{'pic_title_'.$GLOBALS['lang']}['value'],"panel", $pagePRIV, $adding);

	$myframe->card(
			$myPIC->pic_id['value'],
			$myPIC->{'pic_title_'.$GLOBALS['lang']}['value'],
			$myPIC->{'pic_text_'.$GLOBALS['lang']}['value'],
			$myPIC->Draw_Photo($myPIC->pic_ext, "thumb"),
			"",more,true,$myPIC->NVNom['value'],
			$myPIC->NDate['value'],"",$pagePRIV);

	$myPIC->More_DVNom();

	$myframe->close_box("panel");

break;
//TABLE_________________________________________________________________________________________
case "t":
	
	$myframe->open_box("withTree", get_data_in("select ".album_title_x." from album where album_id='{$_REQUEST["album"]}'", album_title_x),"panel", $adding);

// bring pictures of the album

	$wherestr=" where pic_album='{$_REQUEST['album']}' ";
	$sql="select * from {$myPIC->tblname} {$wherestr} order by NDate desc";	
	$pic=new Navigator($sql, $_GET['cur_page'], 6, "select count(NID) from {$myPIC->tblname} {$wherestr} ");
	
	if(@mysql_num_rows(mysql_query($sql)) != 0)
	{
		/////START CARDS EXPLORER
		while ($picRow=mysql_fetch_array($pic->result)){
			
			$myPIC->FillIn($picRow);
			
			$myframe->card($myPIC->NID['value'],
							$myPIC->{'pic_title_'.$GLOBALS['lang']}['value'],
							$myPIC->{'pic_desc_'.$GLOBALS['lang']}['value'],
							$myPIC->Draw_Photo($myPIC->pic_ext, "thumb"),
							"",more,true,$myPIC->NVNom['value'],
                                              $myPIC->NDate['value'],"", $pagePRIV);
		}
		//////END CARDS EXPLORER
		$pic->Draw_Navigator_Line();
	}else{
		?><div style="color:#999;width:90%;border:1px #ccc solid;padding:15px;margin:auto;text-align: center;"><?=No_Rows_Selected?></div><?
		}
	$myframe->close_box("panel");

break;
//DELETE________________________________________________________________________________________
case "d":
	/***/IS_SECURE($pagePRIV);/***/
	
	$name = pic_title_x;
	
	if ($myPIC->NID['IsNew'])  break;
	
	$myframe->open_box("withTree", Delete_Pic,"panel", $pagePRIV, $adding);
	$myPIC->DisplayDelMsg();
	$myframe->close_box("panel");

break;	
}
/*************************   related pages  ******************************/
$myframe->Display_Related_Pages(Picture, array("A", $pagePRIV));

$myframe->footer();
?>
50) ../pages/products.php
<?php
include_once '../common/pframe.php';
include_once '../obj/product.class.php';
include_once '../cms/navigator.php'; 

$myframe=new pframe();
$myframe->header(View_Products);
$myP=new Product($_REQUEST['NID']);
$pagePRIV = "PRODUCTS_MAN";
if(user_has_permission(array("A", $pagePRIV))){	$adding ="";/*if adding is about another OBJ*/	}else{$adding=""; $pagePRIV="";	}

switch ($_REQUEST['v'])
{
//EDITOR_____________________________________________________________________________________
case "e":

/***/IS_SECURE($pagePRIV);/***/
	
	if ($myP->NID['IsNew']){	$ttl=Add_Products; }else { $ttl=Edit_Products; }
	
	$myframe->open_box("withTree", $ttl,"panel", $pagePRIV ,$adding);
	$myP->DisplayEditor("_n");
	$myframe->close_box("panel");

break;
//Viewer_____________________________________________________________________________________
case "c":

	$myframe->open_box("withTree", $myP->{'prod_title_'.$GLOBALS['lang']}['value'],"panel", $pagePRIV ,$adding);
	$myframe->card(
			$myP->prod_id['value'],
			$myP->{'prod_title_'.$GLOBALS['lang']}['value'],
			$myP->{'prod_text_'.$GLOBALS['lang']}['value'],
			$myP->Draw_Photo($myP->prod_pic, "thumb"),
			"",more,true,$myP->NVNom['value'],
			$myP->NDate['value'],"",$pagePRIV);

	$myP->More_DVNom();
	$myframe->close_box("panel");

break;
//TABLE______________________________________________________________________________________
case "t":
	
	$myframe->open_box("withTree", View_Products,"panel", $pagePRIV, $adding);
	
	if ($_REQUEST['NID']!="" && !$_REQUEST['cat_id']) {$c=" where NID = '{$_REQUEST['NID']}' ";} else {$c = "";}
	if ($_REQUEST['cat_id'] != "" && !$_REQUEST['NID']){$c=" where prod_cat='{$_REQUEST['cat_id']}' ";} else{$c="";}
	
	$wherestr=" where prod_only_dw=0 "; 

//NOT a prod_only_dw which is (supplying program that is not our product and we provide a download for it)
	if($_REQUEST['cat']){	$wherestr.=" and prod_cat='{$_REQUEST['cat']}' ";}
	
	$sql="select * from {$myP->tblname} {$wherestr} order by NDate desc";
	$prod=new Navigator($sql, $_GET['cur_page'], 6, "select count(NID) from {$myP->tblname} {$wherestr} ");
	
	if(@mysql_num_rows(mysql_query($sql)) != 0)
	{
		/////START CARDS EXPLORER			
		while ($ProdRow=mysql_fetch_array($prod->result)){
			
			$myP->FillIn($ProdRow);
			$myframe->card($myP->NID['value'],
							$myP->{'prod_title_'.$GLOBALS['lang']}['value'],
							$myP->{'prod_desc_'.$GLOBALS['lang']}['value'],
							$myP->Draw_Photo($myP->prod_pic, "thumb"),
							"",more,true,$myP->NVNom['value'],$myP->NDate['value'],"", $pagePRIV);
		}
		//////END CARDS EXPLORER
		$prod->Draw_Navigator_Line();
	}else{
		?><div style="color:#999;width:90%;border:1px #ccc solid;padding:15px;margin:auto;text-align: center;"><?=No_Rows_Selected?></div><? 
	}
	$myframe->close_box("panel");

break;
//DELETE______________________________________________________________________________________
case "d":

/***/IS_SECURE($pagePRIV);/***/	
	if ($myP->NID['IsNew'])  break;
	
	$myframe->open_box("withTree", Del_Products,"panel", $pagePRIV ,$adding);
	$myP->DisplayDelMsg();
	$myframe->close_box("panel");

break;	

}
/*************************   related pages  ******************************/
$myframe->Display_Related_Pages(Products,array("A", $pagePRIV));

$myframe->footer();
?>
51) ../pages/send_form_email.php (manipulation for sending emails [Contactus OR Maillist] )
<?php
if(basename($_SERVER['PHP_SELF'])  == 'maillist.php') $maillist=true;
 
if(isset($_POST['email'])) { 

/****************** manipulation for maillist email sending *****************/ 
	
// collect emails subrcribed in our maillist

	if($maillist){  
		$tbl=table("select email from mail_list");
		while($row=mysql_fetch_array($tbl)){
			$email_to .= "{$row['email']}, ";
		}
	}
	else{    $email_to = "msn-23@live.com";   }
 
    $email_subject = "Contact Us Message:";
 
    function died($error) {// error code goes here 
        echo errors_found; 
        echo These_errors_appear_below; 
        echo $error."<br /><br />"; 
        echo go_back_fix_errors; 
        die(); 
    }
 
    // validation expected data exists 

    if(!isset($_POST['full_name']) || /*!isset($_POST['last_name']) ||*/ !isset($_POST['email']) || 
        !isset($_POST['telephone']) || !isset($_POST['comments'])) 
    { died(problem_with_form); }
 
// collect information that user entered
 
    $full_name = $_POST['full_name']; // required 
    //$last_name = $_POST['last_name']; // required 
    $email_from = $_POST['email']; // required 
    $telephone = $_POST['telephone']; // not required 
    $comments = $_POST['comments']; // required    
 
    $error_message = ""; 
    $email_exp = '/^[A-Za-z0-9._%-]+@[A-Za-z0-9.-]+\.[A-Za-z]{2,4}$/';
 
/**************** Validation ****************/

// validate email format

  if(!preg_match($email_exp,$email_from)) { 
    $error_message .= Invalid_Email; 
  }
 
    $string_exp = "/^[A-Za-z .'-]+$/";
 
  if(!preg_match($string_exp,$full_name)) { 
    $error_message .= Invalid_FullName; 
  }
 
  /*if(!preg_match($string_exp,$last_name)) { 
    $error_message .= Invalid_LastName; 
  }*/
 
  if(strlen($comments) < 2) { 
    $error_message .= Invalid_Comments; 
  }
 
  if(strlen($error_message) > 0) { 
    died($error_message); 
  }
 
    $email_message = "Form details below.\n\n";    
 
    function clean_string($string) { 
      $bad = array("content-type","bcc:","to:","cc:","href"); 
      return str_replace($bad,"",$string); 
    }
      
// removing unwanted text from message before sending

    $email_message .= "Full Name: ".clean_string($full_name)."\n"; 
    //$email_message .= "Last Name: ".clean_string($last_name)."\n"; 
    $email_message .= "Email: ".clean_string($email_from)."\n"; 
    $email_message .= "Telephone: ".clean_string($telephone)."\n"; 
    $email_message .= "Comments: ".clean_string($comments)."\n";    
 
// create email headers 
$headers = 'From: '.$email_from."\r\n"
. 'Reply-To: '.$email_from."\r\n" 
.'X-Mailer: PHP/' 
. phpversion();

//send the email with all this information
 
@mail($email_to, $email_subject, $email_message, $headers);  

// Show messages

if($maillist){  ?><div style="width:500px;margin:50px auto;color:green"><?=Successfully_Sent?></div><?  }
else{ ?><div style="width:500px;margin:50px auto;"><?=Thank_you_for_contacting_us?></div><? } 

}

// collect emails that we sent to, to display after sending

$email_to2 = "<br/> > " . $email_to;
$email_to2 = str_replace(",","<br/> > ",$email_to2);
$email_to2 .= ">>>>>>>>>>>>>>>>>end";
?>
52) ../pages/services.php                  (OUR SERVICES Page)
<?php 
include_once '../common/pframe.php';
include_once '../cms/navigator.php';
include_once "../obj/service.class.php";

$myframe=new pframe();
$myframe->header(Our_Services);
$myService = new Service($_REQUEST['NID']);
$pagePRIV = "SERVICES_MAN";
if(user_has_permission(array("A", $pagePRIV))){	$adding ="";/*if adding is about another OBJ*/	}else{$adding=""; $pagePRIV="";	}

switch ($_REQUEST['v'])
{
	case "e": /********************* add, edit ************************/
	/***/IS_SECURE($pagePRIV);/***/
		
		if ($myService->NID['IsNew']){	$ttl=Add_x; }else { $ttl=Edit_x; }
		
		$myframe->open_box("withTree", $ttl,"panel", $pagePRIV, $adding);
		$myService->DisplayEditor("_n");
		$myframe->close_box("panel");
	break;
	
	case "t":  /****************** All elements **************************/
		$myframe->open_box("withTree", Our_Services,"panel",$adding);
		
		$wherestr="";
		$sql="select * from {$myService->tblname} {$wherestr} order by NDate desc";
		$serv=new Navigator($sql, $_GET['cur_page'], 10, "select count(NID) from {$myService->tblname} {$wherestr} ");
		
		if(@mysql_num_rows(mysql_query($sql)) != 0)
		{
			?><table id="Our_Services_tbl sortable"><?
					////START CARDS NAVIGATOR
					$i=0;
					$tr_class="";
					while ($ServRow=mysql_fetch_array($serv->result))
					{
						$myService->FillIn($ServRow);
						$myframe->card(
							$myService->srv_id['value'],
							$myService->{'srv_title_'.$GLOBALS['lang']}['value'],
							$myService->{'srv_desc_'.$GLOBALS['lang']}['value'],"",
							"","no_more",true,
							"",
							$myService->NDate['value'],$pagePRIV);
						$i++;
					}
					?></table><?
					//////END CARDS NAVIGATOR
			$serv->Draw_Navigator_Line();
		}else{
			?><div style="color:#999;width:90%;border:1px #ccc solid;padding:15px;margin:auto;text-align: center;"><?=No_Rows_Selected?></div><?
			}
		$myframe->close_box("panel");
		?>
						<script>
// the "click title to show details" feature
						// Auto height animation	
						jQuery.fn.animateAuto = function(prop, speed, callback){
						    var elem, height, width;
						    return this.each(function(i, el){
						        el = jQuery(el), elem = el.clone().css({"height":"auto","width":"auto"}).appendTo("body");
						        height = elem.css("height"),
						        width = elem.css("width"),
						        elem.remove();
						        
						        if(prop === "height")
						            el.animate({"height":height}, speed, callback);
						        else if(prop === "width")
						            el.animate({"width":width}, speed, callback);  
						        else if(prop === "both")
						            el.animate({"width":width,"height":height}, speed, callback);
						    });  
						}
				
						$( ".item_txt" ).height(0) ;
							//////// usage	
							$( ".item_title" ).click(function() { 
						    if ( $(this).next( ".item_txt" ).height() != 0)
						    	$(this).next( ".item_txt" ).animate({ height: 0 }, 400 );
						    else{
						    	$(this).next( ".item_txt" ).animateAuto("height", 1000);
						    }
						});
						</script><? 
	break;
	
	case "d": /********************* delete **********************/
	/***/IS_SECURE($pagePRIV);/***/	
	
		if ($myP->NID['IsNew'])  break;
	
		$myframe->open_box("withTree", Del_x,"panel", $pagePRIV, $adding);
		$myService->DisplayDelMsg();
		$myframe->close_box("panel");
	
	break;
}
/*************************   related pages  ******************************/
$myframe->Display_Related_Pages(Services,array("A", $pagePRIV));

$myframe->footer();
?>
53) ../pages/shared_pool.php
<?php
include_once '../common/pframe.php';
include_once '../obj/shared_pool.class.php';

$myframe = new pframe ();
$myframe->header ( Shared_Pool );
$mySH = new Shared_pool ( $_REQUEST ['NID'] );
$adding = "";
$pagePRIV = "SHARED_POOL_MAN";

//*********** back & refresh buttons ************/
$cur = $_REQUEST['path'];
$ds = substr($_REQUEST['path'], 0, strrpos( $_REQUEST['path'], '/') );
$back = "../pages/shared_pool.php?lang={$GLOBALS['lang']}&v=t&path={$ds}";
$current = "../pages/shared_pool.php?lang={$GLOBALS['lang']}&v=t&path={$cur}";

if ($_SESSION['GID']) // only logged in users can access shared pool
{
	switch ($_REQUEST ['v'])
	{
		case "e": //******************** Add, Edit *********************/
			
			if($mySH->user['value'] != $_SESSION['GID'] && !user_has_permission(array("A", $pagePRIV)) && $_REQUEST['NID'] != 'IsNew') {
				/***/IS_SECURE("", "not_secure");/***/
			}
			
			if ($mySH->NID['IsNew']){	$ttl=Add_x;	} else{ $ttl=Edit_x ." ". $mySH->file_name['value'];	}			
			
			/*** Creating New Folder ***/ 
			if($_REQUEST['dir'] == '1'){					
				$mySH->file_type['type'] = 'hidden';
				$mySH->file_type['control'] = 'none';
				$mySH->file_type['value'] = '[dir]';				
				$mySH->file_name['control'] = 'text';
				$mySH->path['value'] = $_REQUEST['path']; }
				
			/*** uploading file ***/
			else{ 
				$mySH->file_type['type'] = 'file';
				$mySH->file_type['control'] = 'file';
			}			

			$myframe->open_box("withTree", $ttl,"panel", $adding);
			
			//fill data about uploaded file
			$mySH->user['value'] = $_SESSION['GID'];
			$mySH->path['value'] = $_REQUEST['path'];
			$mySH->file_size['value'] = $_FILES['txt_file_type']['size'];
			
			$mySH->DisplayEditor("_n");
			$myframe->close_box("panel");
			?><a href="<?=$back?>"><img src="../images/icons/Undo.png" style="float:right;"/></a><?
		break;
		
		case "t": //********************* All Elements ********************/
			
			$myframe->open_box("withTree", Shared_Pool ." > ". $_REQUEST['path'] , "panel", "" , $adding);			
		
				//*********** back & refresh buttons						
			?><a href="<?=$back?>"><img src="../images/icons/Undo.png"/></a>&nbsp;&nbsp;<a href="<?=$current?>"><img src="../images/icons/refresh.png"/></a><?
			 
				//************** NewFolder
			?><a href="../pages/shared_pool.php?lang=<?=$GLOBALS['lang']?>&v=e&NID=IsNew&dir=1&path=<?=$_REQUEST['path']?>"><img src="../images/icons/black-newfolder.png" style="margin:0px 10px;margin-bottom:10px;"/></a><? 
			
			if($_REQUEST['path'] == '')
				{$wherestr=" where path='{$_REQUEST['path']}' or path is null ";}
			else{$wherestr=" where path='{$_REQUEST['path']}'";}
			$sql="select * from {$mySH->tblname} {$wherestr} order by file_type desc,file_name asc "; //echo $sql;
			
// bring all files that the users have uploaded
// the are stored in DB to make it faster to show
// and to isolate our storage from direct access

			if(@mysql_num_rows(mysql_query($sql)) != 0)
			{
				?><table class="global_tbl sortable">
					<th><?=file_name?></th>
					<th><?=user_id?></th>					
					<th><?=file_size?></th>
					<th><?=extension?></th>
					<th><?=NDate?></th>
				<? 					
					$i=0;
					$tr_class="";					
					$tbl=table($sql);
					 
				while ($Row=mysql_fetch_array($tbl))
				{		
					$mySH->FillIn($Row);
					if($i%2==0){	$tr_class="tr_1";	}else{	$tr_class="tr_2";	}
						
// if the record was Drectory it will show in different way				

					if($Row['file_type'] == '[dir]')
					{$location = "../pages/shared_pool.php?lang={$GLOBALS["lang"]}&v=t&path={$_REQUEST['path']}/{$mySH->file_name['value']}"; $ico='<img src="../images/icons/folder0000.png" style="float:right;line-height:21px;vertival-align:middle;padding:0px 10px"/>';}
					else 
					{
//the record was a FILE
						$dir = get_folders();
						$location = "../documents/SHARED_POOL{$dir}/{$mySH->NID['value']}.{$mySH->file_type['ext']}"; $ico='';}
										
					?><tr class="<?=$tr_class?> tr0" onclick="document.location = '<?=$location?>'; " id="tr_<?=$mySH->NID['value']?>" target="_blank">
						<td><?=$ico?><?=$mySH->file_name['value']?></td>
						<td><?=get_data_in("select user_name from user where user_id = '{$mySH->user['value']}' ", "user_name")?></td>						
						<td><?=$myframe->format_size($mySH->file_size['value']) ?></td>
						<td><?=$Row['file_type']?></td>
						<td><?=$mySH->NDate['value']?></td>
						<td>
							<?if($mySH->user['value'] == $_SESSION['GID'] || user_has_permission(array("A", $pagePRIV))) {	
								$myframe->DisplayAdminTools("shared_pool", $mySH->NID['value']);}?>
						</td>
					</tr><?					
					$i++; 				
				}
				?></table><?
				
			}else{
				?><div style="color:#999;width:90%;border:1px #ccc solid;padding:15px;margin:auto;text-align: center;"><?=No_Rows_Selected?></div><?
			} 
			$myframe->close_box("panel");
			$mySH = new Shared_pool();
			
//fill data about uploaded file

			$mySH->user['value'] = $_SESSION['GID'];
			$mySH->path['value'] = $_REQUEST['path']; 
			$mySH->file_size['value'] = $_FILES['txt_file_type']['size'];
			
			?><div style="width:700px;margin:auto;border:0px #000 solid"><? $mySH->DisplayEditor("_n");?></div><? 
			
		break;
			
		case "d" : /****************************************/
			
// if the user was trying to delete a file he does not own AND he is not admin (security)

			if($mySH->user['value'] != $_SESSION['GID'] && !user_has_permission(array("A", $pagePRIV)) && $_REQUEST['NID'] != 'IsNew' ) {
				/***/IS_SECURE("", "not_secure");/***/
			}
			
			$myframe->open_box ( "withTree", Del_Ads, "panel", "", $adding );
			$sql = "select NID from shared_pool where path like '%/{$mySH->file_name['value']}' ";
// it's not allowed to delete a folder with contents

			if(@mysql_num_rows(mysql_query($sql)) == 0)
			{
				$mySH->DisplayDelMsg ();
			}
			else{echo cannot_delete_folder_with_content;}
			
			$myframe->close_box ( "panel" );
			?><a href="<?=$back?>"><img src="../images/icons/Undo.png"/></a><?
		break; 
	}
}
else{IS_SECURE("", "not_secure");}

 /* *********************** related pages ***************************** */

$myframe->footer ();

function get_folders() 
// converts the path from "folder-name-path" to "IDs-path"
{
	//bring the id for dir
	$arr = explode('/', $_REQUEST['path']);
	$dir = "";
	$i=0;
	$ids=array();
	foreach( $arr as $v){
		//echo $v;
		$ids[$i] = get_data_in("select NID from shared_pool where file_type='[dir]' and file_name='{$v}' ", "NID");
		$i++;
	}
	foreach( $ids as $v){
		$dir .= "/{$v}";
	}
	if($_REQUEST['path'] != '')
		$dir .='/';
	return $dir;
}
?>
53) ../pages/site_configs.php
<?php 
include_once '../common/pframe.php';
include_once "../obj/site_config.class.php";

$myframe=new pframe();
$myframe->header(Configs);
$mySC = new Site_config($_REQUEST['NID']);
$pagePRIV = "CONFIGS_MAN";
if(user_has_permission(array("A", $pagePRIV))){	$adding ="";/*if adding is about another OBJ*/	}else{$adding=""; $pagePRIV="";	}

switch ($_REQUEST['v'])
{
	case "e"://____________________________________________
		
	/***/IS_SECURE($pagePRIV);/***/
		echo '<script type="text/javascript" src="../cms/jscolor.js"></script>';
		
		if ($_REQUEST['NID'] =='IsNew'){	$ttl=Add_x." ".Site_Configs;} 	
		else{ 
//specify editing fields of each configuration

			$ttl=Edit_x." ".Site_Configs;
			$conf = get_data_in("select config from site_config where NID='{$_REQUEST['NID']}' ", "config");			
			if($conf == 'slider' ){$mySC->value['control'] = 'none'; $mySC->pic_social['control'] = 'none';	}
			elseif($conf == 'social_media'){$mySC->value['control'] = 'none';$mySC->pic_slider['control'] = 'none';	}
			elseif($conf == 'header_logo' || $conf == 'fav_ico'){	$mySC->link['control'] = 'none';$mySC->value['control'] = 'none';$mySC->pic_slider['control'] = 'none';$mySC->title['control'] = 'none';$mySC->config['control'] = 'text';	}
			
			else{$mySC->config['control'] = 'text';	$mySC->link['control'] = 'none';$mySC->pic_slider['control'] = 'none';$mySC->pic_social['control'] = 'none';$mySC->title['control'] = 'none';	}
		}
 // EDITOR		
		
		$myframe->open_box("withTree", $ttl,"panel", $adding);
		$mySC->DisplayEditor("_n");
		$myframe->close_box("panel");
		
		if ($_REQUEST['NID'] !='IsNew'){
//not possible to edit a type of a config 
//also not possible to add types other than slider or social media 
		?><script>
			var selct=document.getElementById('txt_config');
			selct.disabled = true;
			document.getElementById("txt_value").setAttribute('type','color');	
		</script><? 
		}
	break;//____________________________________________
		
	case"t": /******************* Show Site Configurations *******************/
		
	/***/IS_SECURE($pagePRIV);/***/
		$myframe->open_box("withTree", Site_Configs,"panel", $pagePRIV, $adding);
		$wherestr="";
		if($_REQUEST['groupby'])	{$wherestr=" where config='{$_REQUEST['groupby']}' ";}
		
		$sql="select * from {$mySC->tblname} {$wherestr} order by config"; //echo $sql;
		$tbl=table($sql);
		//
		?><div class="site_configs"><table class="global_tbl sortable">
				<? if(@mysql_num_rows(mysql_query($sql)) == 0){?><tr><td><?=No_Rows_Selected?></td></tr><?}else{?>
					<tr>
						<th><?=config?></th>
						<th><?=title?></th>
						<th><?=link?></th>
						<th><?=value?></th>
						<th><?=pic_social?></th>
						<th><?=pic_slider?></th>
					</tr>
				<?
				$i=0;
				$tr_class="";
				while ($Row=mysql_fetch_array($tbl))
				{
					$mySC->FillIn($Row);$id=$mySC->user_id["value"];
					if($i%2==0){$tr_class="tr_1";}else{$tr_class="tr_2";}
					$css = "background-color:{$mySC->value['value']}; ";
					?>
					<tr class="<?=$tr_class?>" style="cursor:auto;">
						<td class="<?=$mySC->config['value']?>">	<?=$mySC->config['value']?>	</td>
						<td class="">	<?=$mySC->title['value']?>	</td>
						<td class="">	<?=$mySC->link['value']?>	</td>
						<td style="<? if($mySC->config['value']!='slider' && $mySC->config['value']!='social') echo $css;?>">	
						<?=$mySC->value['value']?>	</td>
						<td class="">	<?=$mySC->pic_social['ext']?>	</td>
						<td class="">	<?=$mySC->pic_slider['ext']?>	</td>
						<? if(user_has_permission(array("A", $pagePRIV))) {?><td class="admin_tools_td"><?=$myframe->DisplayAdminTools("site_configs", $mySC->NID['value'])?></td><? }
					?></tr><?
					?><script>   $(".DEL_admintool").attr('class', 'DEL_admintool_'+ <? echo json_encode($mySC->config['value']);?>); </script><?
						
					$i++;
				}}
				?></table></div><?
				
//hide delete tool for configurations not are (slider or social media)

				?><script> $(".DEL_admintool_color_main ,.DEL_admintool_color_second,.DEL_admintool_color_third,.DEL_admintool_fav_ico,.DEL_admintool_side_bar_button,.DEL_admintool_header_logo").hide(); </script><?
				$myframe->close_box("panel");
	break;
	
	case "d"://____________________________________________
		
	/***/IS_SECURE($pagePRIV);/***/
		if ($mySC->NID['IsNew'])  break;
	
		$myframe->open_box("withTree", Del_x." ".Site_Configs ,"panel", $pagePRIV, $adding);
		$mySC->DisplayDelMsg();
		$myframe->close_box("panel");
		
	break;//____________________________________________
}

/*************************   related pages  ******************************/
$myframe->Display_Related_Pages(Services,array("A", $pagePRIV));

$myframe->footer();
?>
53) ../pages/users.php
<?php
ob_start ();
include_once '../common/pframe.php';
include_once '../common/privileges.php';
include_once '../obj/user.class.php';
include_once '../cms/navigator.php';

$myframe = new pframe ();
$myframe->header ( Users );
$myUser = new User ( $_REQUEST ['NID'] );
$pagePRIV = "USERS_MAN";
if(user_has_permission(array("A", $pagePRIV))){	$adding ="";/*if adding is about another OBJ*/	}else{$adding=""; /*$pagePRIV="";*/	}
$_SESSION['back_page'] = "users";

// for security, make sure that the user is logged in & (is Admin or has the page's permission)

if($_SESSION['GID'])
{
	if(user_has_permission(array("A", $pagePRIV))){	$USERS_MAN = true;}
	else{

// remember user type in all page life (admin, customer, distributor, employee)

		if(user_has_permission(array("A", "CUSTOMERS_MAN"))){	$CUSTOMERS_MAN = true; $pagePRIV = "CUSTOMERS_MAN";}
		if(user_has_permission(array("A", "DISTRIBUTORS_MAN"))){	$DISTRIBUTORS_MAN = true; $pagePRIV = "DISTRIBUTORS_MAN";}
		if(user_has_permission(array("A", "EMPLOYEES_MAN"))){	$EMPLOYEES_MAN = true; $pagePRIV = "EMPLOYEES_MAN";}		
	}
}else{ 
// not customer , not user_manager. then, what are you doing here :@ !!
// go to error page and record that as an illegal attempt for his IP

		/***/IS_SECURE("", "not_secure");/***/} 

switch ($_REQUEST ['v'])
{
	case "e":/*************   edit   ******************************************/
		
	/***/IS_SECURE("{$pagePRIV},CUSTOMERS_MAN,EMPLOYEE_MAN,DISTRIBUTOR_MAN");/***/

// check more privileges before starting

		if ($myUser->NID['IsNew']){	$ttl=Add_User; }else { $ttl=Edit_User; }
		
		if($myframe->Is_Distributor()){ //Distributors can only add 'customer users'
			$myUser->user_cat['control'] = 'text';  $myUser->user_cat['value'] = 'customer';
		}
				
		$myframe->open_box("withTree", $ttl,"panel", $pagePRIV, $adding);
		$myUser->DisplayEditor("_n");
		
//when adding a user to a group
		
		if($_REQUEST['group'] && $_REQUEST['NID'] !="IsNew"){
			$ins="insert into user_groups values ('{$myUser->NID['value']}' 
                                                       ,'{$_REQUEST['group']}')";//echo $ins;
			if(cmd($ins)){ header("location: ../pages/groups.php?lang={$GLOBALS['lang']}&v=t&NID={$_REQUEST['group']}"); }
		}

// if the user is distributordon't allow him create users in different type but customer

		if($myframe->Is_Distributor())
		{	?><script>	var selct=document.getElementById("txt_user_cat");	selct.disabled = true;	</script><? }
		
		$myframe->close_box("panel");
		break;
		
	case "c":/*************   card   ******************************************/
		
	/***/IS_SECURE("{$pagePRIV},CUSTOMERS_MAN,EMPLOYEE_MAN,DISTRIBUTOR_MAN");/***/
		
		$myframe->open_box("withTree", $myUser->user_name['value'],"panel", $pagePRIV, $adding);
		
		$myframe->User_Card($myUser, "users",$pagePRIV);//print_r($myUser);
		
		$myframe->close_box("panel");
		break;
		
	case "t":/*************   table   ******************************************/
		
		//panel title
		if($_REQUEST['ucat'] == 'customer'){$ttl = Customer_Management;}
		elseif($_REQUEST['ucat'] == 'employee'){$ttl = Emps_Management;	}
		elseif($_REQUEST['ucat'] == 'distributor'){$ttl = Dists_Management;	}
		else {$ttl = Users_Management;}
		$myframe->open_box("withTree", $ttl,"panel", $pagePRIV, $adding);
		
		// "Group-by" buttons filter user by user category

		?><div class="group_by_div"><? ?></div><? 
			if($USERS_MAN){?> <a class="group_by_button" href="../pages/users.php?lang=<?=$GLOBALS['lang']?>&v=t"><?=All_Users?></a> <?}
			if($USERS_MAN||$DISTRIBUTORS_MAN){?> <a class="group_by_button" href="../pages/users.php?lang=<?=$GLOBALS['lang']?>&v=t&ucat=distributor"><?=Dists_Management?></a> <?  }
			if($USERS_MAN||$CUSTOMERS_MAN){?> <a class="group_by_button" href="../pages/users.php?lang=<?=$GLOBALS['lang']?>&v=t&ucat=customer"><?=Custs_Management?></a> <?  }
			if($USERS_MAN||$EMPLOYEES_MAN){?> <a class="group_by_button" href="../pages/users.php?lang=<?=$GLOBALS['lang']?>&v=t&ucat=employee"><?=Emps_Management?></a> <?}
			if($USERS_MAN){?> <a class="group_by_button" href="../pages/users.php?lang=<?=$GLOBALS['lang']?>&v=m"><?=User_Privileges?></a> <?}
		$wherestr="";
			
		// distributors are only allowed to manage cutomers, not other user categories
		if($myframe->Is_Distributor()){ 
			if($_REQUEST['ucat']=='employee') /***/IS_SECURE("", "not_secure");/***/
			if($_REQUEST['ucat']=='distributor') /***/IS_SECURE("", "not_secure");/***/
			if($_REQUEST['ucat']=='admin') /***/IS_SECURE("", "not_secure");/***/
		}
		if($_REQUEST['ucat']){ $wherestr=" where user_cat='{$_REQUEST['ucat']}' ";}

		// a distributor can only manage his own customers

		if($myframe->Is_Distributor()){ $wherestr.=" and user_id in (select cust_id from customers_of_dist where dist_id='{$_SESSION['GID']}' )";}
		$sql="select * from {$myUser->tblname} {$wherestr} order by NDate desc";
		//echo $sql;
		$usr=new Navigator($sql, $_GET['cur_page'], 30, "select count(NID) from {$myUser->tblname} {$wherestr} ");
		//
		?><table class="global_tbl sortable">
				<? if(@mysql_num_rows(mysql_query($sql)) == 0){?><tr><td><?=No_Rows_Selected?></td></tr><?}else{?>
					<tr>
						<th><?=user_name?></th>
						<th><?=user_cat?></th>
						<th><?=user_email?></th>
						<th><?=user_phone?></th>
						<th><?=user_address?></th>
						<th><?=user_country?></th>
						<th><?=user_city?></th>
					</tr>
				<?
				////START CARDS NAVIGATOR
				$i=0;
				$tr_class="";
				while ($Row=mysql_fetch_array($usr->result))
				{
					$myUser->FillIn($Row);//$id=$myUser->user_id["value"];
					if($i%2==0){$tr_class="tr_1";}else{$tr_class="tr_2";}
					?>
					<tr class="<?=$tr_class?> <? if($_REQUEST['NID'] == $myUser->NID['value']){ echo "tr_highlighted";}?>" onclick="document.location = '../pages/users.php?lang=<?=$GLOBALS["lang"]?>&v=c&NID=<?=$myUser->NID['value']?>';">
						<td class=""><?=$myUser->user_name['value']?></td>
						<td class=""><?=$myUser->user_cat['value']?></td>
						<td class=""><?=$myUser->user_email['value']?></td>
						<td class="" style="max-width:130px;"><?=$myUser->user_phone['value']?></td>
						<td class="" style="max-width:130px;"><?=$myUser->user_address['value']?></td>
						<td class="" style="max-width:70px"><?=$myUser->user_country['value']?></td>
						<td class=""style="max-width:70px"><?=$myUser->user_city['value']?></td>
						<? if(user_has_permission(array("A", $pagePRIV, "CUSTOMERS_MAN", "DISTRIBUTORS_MAN", "EMPLOYEES_MAN"))) {
							?><td class="admin_tools_td"><div style="display:inline-block"><?=$myframe->DisplayAdminTools("users", $myUser->user_id['value'])?></div>
							  <a href="../pages/users.php?lang=<?=$GLOBALS['lang']?>&v=priv_editor&NID=<?=$myUser->NID['value']?>">
							  <img src="../images/priv_tool.png"  style="width:32px;display:inline-block" alt="<?=Privileges?>"/></a></td><? }
					?></tr><?
					$i++;
				}}
				?></table><?
				//////END CARDS NAVIGATOR
				$usr->Draw_Navigator_Line();
				$myframe->close_box("panel");
				$_SESSION['back_php'] = "users"; // this is for the tree
		break;
		
	case "d":/*************   del   ******************************************/
		
	/***/IS_SECURE("{$pagePRIV},CUSTOMERS_MAN,EMPLOYEE_MAN,DISTRIBUTOR_MAN");/***/	

		$myframe->open_box("withTree", Del_User,"panel", $pagePRIV, $adding);
		$myUser->DisplayDelMsg();
		$myframe->close_box("panel");
	break;
	
	case "m":/*************   Users Management   **********************************/
		
	/***/IS_SECURE("{$pagePRIV},CUSTOMERS_MAN,EMPLOYEE_MAN,DISTRIBUTOR_MAN");/***/
		
		$myframe->open_box("withTree", Users_Management,"panel", $pagePRIV, $adding);
		?><div class="group_by_div"><? ?></div><?

// group-by buttons vary according to the privs that user has

			if($USERS_MAN){?> <a class="group_by_button" href="../pages/users.php?lang=<?=$GLOBALS['lang']?>&v=t"><?=All_Users?></a> <?}
			if($USERS_MAN||$DISTRIBUTORS_MAN){?> <a class="group_by_button" href="../pages/users.php?lang=<?=$GLOBALS['lang']?>&v=t&ucat=distributor"><?=Dists_Management?></a> <?  }
			if($USERS_MAN||$CUSTOMERS_MAN){?> <a class="group_by_button" href="../pages/users.php?lang=<?=$GLOBALS['lang']?>&v=t&ucat=customer"><?=Custs_Management?></a> <?  }
			if($USERS_MAN||$EMPLOYEES_MAN){?> <a class="group_by_button" href="../pages/users.php?lang=<?=$GLOBALS['lang']?>&v=t&ucat=employee"><?=Emps_Management?></a> <?}
			if($USERS_MAN){?> <a class="group_by_button" href="../pages/users.php?lang=<?=$GLOBALS['lang']?>&v=m"><?=User_Privileges?></a> <?}
		$wherestr="";
		if($_REQUEST['ucat']){ $wherestr=" where user_cat='{$_REQUEST['ucat']}' ";}
		$sql="select * from {$myUser->tblname} {$wherestr} order by user_name ";
		$usr=new Navigator($sql, $_GET['cur_page'], 30, "select count(NID) from {$myUser->tblname} {$wherestr} ");
		//
//show users  (with the user type filtering)

		?><table class="global_tbl sortable">
		<? if(@mysql_num_rows(mysql_query($sql)) == 0){?><tr><td><?=No_Rows_Selected?></td></tr><?}else{?>

// show users with privs for each one

			<tr>
				<th><?=user_name?></th>
				<th><?=user_cat?></th>
				<th style="width:50%"><?=user_priv?></th>
			</tr>
		<?
		////START CARDS NAVIGATOR
		$i=0;
		$tr_class="";
		while ($Row=mysql_fetch_array($usr->result))
		{
			$myUser->FillIn($Row); //echo $myUser->NID['value'];
			if($i%2==0){$tr_class="tr_1";}else{$tr_class="tr_2";}
			?>
			<tr class="<?=$tr_class?> <? if($_REQUEST['NID'] == $myUser->NID['value']){ echo "tr_highlighted";}?>" onclick="document.location = '../pages/users.php?lang=<?=$GLOBALS["lang"]?>&v=c&NID=<?=$myUser->NID['value']?>';">
				<td class=""><?=$myUser->user_name['value']?></td>
				<td class=""><?=$myUser->user_cat['value']?></td>
				<td class=""><?foreach(DB_USERS_Privileges($myUser->NID['value']) as $v) echo "$v</br>";?></td>

// admin tools to grant / revoke privs from user

				 <? if(user_has_permission(array("A", $pagePRIV))) {?> <td class="admin_tools_td"><div class="admin_tools"><a href="../pages/users.php?v=priv_editor&lang=<?=$GLOBALS['lang']?>&NID=<?=$myUser->NID['value']?>" class="EDT_admintool"><img src="../images/edtimg.png" /></a></div></td><? }?>
			</tr><?
			$i++;  
		}}
		
		?></table><script>   $('.DEL_admintool').hide();   </script><? 
		//////END CARDS NAVIGATOR
		$usr->Draw_Navigator_Line();
		$myframe->close_box("panel");
	break;
	
	case "priv_editor":/*************   privileges   ******************************************/
		
	/***/IS_SECURE($pagePRIV);/***/
		
		if ($myUser->NID['IsNew']){$ttl=Add_x." ".priv;}else { $ttl=Edit_x." ".priv;}
		
		$myframe->open_box("withTree", $ttl,"panel",$pagePRIV  ,$adding);
		echo '<div>'.Privileges_Message_Empty.'</div></br>';

//***************   Privileges Editor   ***************/
		PrivilegesEditor($pagePRIV);
		$myframe->close_box("panel");
	break;
	
	case "p":/*************   profile   ******************************************/
		
//check security first

		if($_SESSION['GID'] == $myUser->NID['value'])
		{
// Initialize user attributes and show specific ones

			$myframe->open_box("withTree", MyProfile, "panel" ,"" ,$adding);
			$myUser->user_name['required'] ='';
			$myUser->user_password['required'] ='';
			$myUser->in_home['required'] ='';
			$myUser->user_cat['required'] ='';
			$myUser->user_name['control'] ='none';
			$myUser->user_password['control'] ='none';
			$myUser->in_home['control'] ='none';
			$myUser->user_cat['control'] ='none';
			$myUser->DisplayEditor();			
			
//show user's privs (not for customers)

			 if( !$myframe->Is_Customer()){?>
			<div style="display:inline-block;margin-right:90px;vertical-align:top;">
				<p style="padding:20px;font-weight:bold;border:2px #999 solid;width:200px;margin:auto;text-align:center;"><?=MY_Privs?></p>
				<div style="width:200px;margin:auto;border:1px #999 solid;text-align:center;padding:20px 0px;border-top:0px">
					<? foreach($_SESSION['PRIVS'] as $v) echo "$v</br>";  ?></div>
			</div><? 
			} 
			
			?><br/><br/><br/><a href="../common/reset_password.php" style="color:red;font-weight:bold;border:1px red solid;padding:10px 20px;clear:both;margin:20px 30px;"><?=Reset_Password?></a><br/><br/><? 
			
			$myframe->close_box("panel");
		}
		else{/***/IS_SECURE("", "not_secure"); /** will record this as an illegal action for this ip **/	}
	break;
}

/*************************   related pages  ******************************/
$myframe->Display_Related_Pages(User,array("A", $pagePRIV));

// =========================================================================================================================================
$myframe->footer ();
ob_flush ();
53) ../pages/videos.php
<?php
include_once '../common/pframe.php';
include_once '../obj/video.class.php';
include_once '../cms/navigator.php'; 

$myframe=new pframe();
$myframe->header( Video );
$myVID=new Video($_REQUEST['NID']);
$pagePRIV = "VIDEOS_MAN";
if(user_has_permission(array("A", $pagePRIV))){	$adding ="";/*if adding is about another OBJ*/	}else{$adding=""; $pagePRIV="";	}

switch ($_REQUEST['v'])
{
//EDITOR_______________________________________________________________________________________
case "e":

/***/IS_SECURE($pagePRIV);/***/

	if ($myVID->NID['IsNew']){	$ttl=Add_x." ".Video; }else { $ttl=Edit_x." ".Video; }
	
	$myframe->open_box("withTree", $ttl,"panel", $pagePRIV, $adding);
	$myVID->DisplayEditor("_n");
	$myframe->close_box("panel");
	?>
	<script>
		$("#txt_vid_album").val(<?=json_encode($_SESSION['album'])?>);
	</script>
	<?
break;
//Viewer______________________________________________________________________________________
case "c":

/***/IS_SECURE($pagePRIV);/***/
	
	$myframe->open_box("withTree", $myVID->{'pic_title_'.$GLOBALS['lang']}['value'],"panel", $pagePRIV, $adding);

	$myframe->card(
			$myVID->vid_id['value'],
			$myVID->{'vid_title_'.$GLOBALS['lang']}['value'],
			$myVID->{'vid_text_'.$GLOBALS['lang']}['value'],
			$myVID->Draw_Photo($myVID->pic_ext, "thumb"),
			"",more,true,$myVID->NVNom['value'],
			$myVID->NDate['value'],"",$pagePRIV);

	$myVID->More_DVNom();

	$myframe->close_box("panel");

break;

//TABLE_______________________________________________________________________________________
case "t":
	
	$myframe->open_box("withTree", get_data_in("select ".album_title_x." from album where album_id='{$_REQUEST["album"]}'", album_title_x),"panel", $adding);

	$wherestr=" where pic_album='{$_REQUEST['album']}' ";
	$sql="select * from {$myVID->tblname} {$wherestr} order by NDate desc";	
	$pic=new Navigator($sql, $_GET['cur_page'], 6, "select count(NID) from {$myVID->tblname} {$wherestr} ");
	
	
	if(@mysql_num_rows(mysql_query($sql)) != 0)
	{
		/////START CARDS EXPLORER
		while ($picRow=mysql_fetch_array($pic->result)){
			
			$myVID->FillIn($picRow);
			
			$myframe->card($myVID->NID['value'],
							$myVID->{'vid_title_'.$GLOBALS['lang']}['value'],
							$myVID->{'vid_desc_'.$GLOBALS['lang']}['value'],
							$myVID->Draw_Photo($myVID->pic_ext, "thumb"),
							"",more,true,$myVID->NVNom['value'],
                                              $myVID->NDate['value'],"", $pagePRIV);
		}
		//////END CARDS EXPLORER
		$pic->Draw_Navigator_Line();
	}else{
		?><div style="color:#999;width:90%;border:1px #ccc solid;padding:15px;margin:auto;text-align: center;"><?=No_Rows_Selected?></div><?
		}
	$myframe->close_box("panel");

break;
//DELETE____________________________________________________________________________________
case "d":
	
/***/IS_SECURE($pagePRIV);/***/
	
	$name = pic_title_x;
	
	if ($myVID->NID['IsNew'])  break;
	
	$myframe->open_box("withTree", Del_Pic,"panel", $pagePRIV, $adding);
	$myVID->DisplayDelMsg();
	$myframe->close_box("panel");

break;	

}
/*************************   related pages  ******************************/
$myframe->Display_Related_Pages(Picture, array("A", $pagePRIV));

$myframe->footer();
?>
 
Folder : /tpl
 
53) ../tpl/error.php
<? 
// this is the error page where the user is redirected if he has done an illegal access, like copying a // link that he does not have permission to, or change a parameter (buy contract) he's not a part of..



include_once '../common/pframe.php';
$myfrm =new pframe();
$ttl= ($myfrm->illegal_attempts() > 10)? YOU_ARE_BLOCKED : ERROR_PAGE;
?>
<html>
<header>
	<meta http-equiv="content-type" content="text/html; charset=UTF-8" />
	<link href="../common/main_<?=$GLOBALS['lang']?>.css" rel="stylesheet" type="text/css" media="screen" />
	<link rel="icon" href="../images/error_ico.png" type="image/x-icon" />
	<title><?=$ttl?></title>
</header>
<body class="error_page">

	<div class="message">
		<? echo PROJECT_TITLE ."<br/><br/>".ERROR_PAGE."<br/><br/>";?>
		<? echo constant($_REQUEST['reason']) ."</br>";?>
	</div>

// then the user clicks the back button to go back

// but if he reaches a limit of illegal actions, the back button disappears and the will be redirected // every time he access the site (this means the client ip is blocked)

//illegal actions are two kinds : copy link & failed login over 10 times in 3 day window

	<? if($myfrm->illegal_attempts() > 10 || $myfrm->illegal_attempts('link_copy') > 10){}else{?>
		<a class="back_button"  onClick="history.back();return false;"><?=Back?></a>
	<? }?>
</body>
</html>
53) ../tpl/panel.box.php
<?php

// this is a box that can contain page content. 
// it includes a header-image banner at the top, which varies in each page dynamically
// then a header title, that can come with tree (optional), and the "adding tool" (new object) 
    (also optional)
// at last, the closing of the box has to be called


if ($open) {
?>
<div class="panel">
	<? Page_Header();?>
	<div class="panel_title">
	<?php if (stristr($box_title, "ttxtg") == false) { ?>
		<div class="panel_title_text ttxtg">
			<div class="withTree"><? 
				if($withTree=="withTree")
				{
					$tree= $this->Make_Tree(basename($_SERVER['PHP_SELF'])); 
				}
				echo $tree;
			?></div>
		<?=$box_title?>
		<?$this->DisplayAddingTool( $priv, $adding);?>
		</div>
	<?php } else {?>
		<div class="panel_title_text"><?=$box_title?></div>
	<?php } ?>
	</div>
	<div class="panel_bdy" id="<?=$box_id?>">
	<?php
	}
	
	if ($close) {
	?>
	</div>
</div>
<?php
}

function Page_Header(){
	$name = explode('.', $_SERVER['PHP_SELF']);
	$page = basename($name[0]);
	$src = "../images/pages/{$page}.jpg";
	if(file_exists($src)){
		?><img src="<?=$src?>" class="pages_header"/><? 
	}else{
		$src = "../images/pages/{$page}.png";
		if(file_exists($src)){
			?><img src="<?=$src?>" class="pages_header"/><?
		}
	}
}
 
53) ../tpl/tpl.tpl.inc
<?php 
session_start();
function tpl_header($title="",$mnu=false,$rel=array()) {
	
	//in some cases you need to be redirected to the previous page so we put it in a SESSION[prev]
	// don't change when refreshing = don't change when we are on same page

	if($_SESSION['cur_params'] != $_SERVER['QUERY_STRING'] || 
                                            $_SESSION['cur_self'] != $_SERVER['PHP_SELF']) 
	{
       $_SESSION['prev_self'] = $_SESSION['cur_self']; 
       $_SESSION['prev_params'] = $_SESSION['cur_params']; }
	$_SESSION['cur_params'] = $_SERVER['QUERY_STRING']; 
	$_SESSION['cur_self'] = $_SERVER['PHP_SELF'];
	
	// Has visitor been counted in this session?	If not, increase counter value by one
	if(!isset($_SESSION['hasVisited'])){
		$_SESSION['hasVisited']=true;
		@cmd("update tcount set countid=countid+1");
	}
	
	if(basename($_SERVER['PHP_SELF']) != 'search.php') unset($_SESSION['search_result']);

	include_once '../common/pframe.php';
	$myfrm =new pframe();
//if this IP address has too many failed login attempts 
// OR copys links he doesn't has access to, will be blocked
	if($myfrm->illegal_attempts('failed_login') > 10 ){	@header("location:../tpl/error.php?reason=failed_login");	}

	elseif($myfrm->illegal_attempts('link_copy') > 10){	@header("location:../tpl/error.php?reason=link_copy");	}
?>

// now the starting HTML TAGS
// with the including of the SCRIPT files and other kinds of files


<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">

<head>
	<meta http-equiv="content-type" content="text/html; charset=UTF-8" />
	<meta name="author" content="muhannad snan" />
	<meta name="viewport" content="width=device-width" />
	<link href="../common/main_<?=$GLOBALS['lang']?>.css" rel="stylesheet" type="text/css" media="screen" />
	<link rel="icon" href="../documents/thumbs/SSOCIAL_<? $a=$myfrm->SiteConfig("fav_ico");echo$a[0]['NID']?>.<?=$a[0]['pic_social']?>" type="image/x-icon" />	    
    
    <script type="text/javascript" src="../cms/jquery-1.5.1.min.js"></script>
    <script type="text/javascript" src="../cms/datagrid.js"></script>
    <script type="text/javascript" src="../cms/jquery.fancybox-1.3.1.pack.js"></script>	
    <script type="text/javascript" src="../cms/jquery-latest.min.js"></script>
    <script type="text/javascript" src="../cms/jquery-1.10.1.min.js"></script>
    <script type="text/javascript" src="../cms/jquery-1.9.0.js"></script>
    <script type="text/javascript" src="../cms/jquery-2.1.3.min.js"></script>
    <script type="text/javascript" src="../cms/sorttable.js"></script>
    <script type="text/javascript" src="../cms/CollapsibleLists.js"></script>    
    
	<title><?=$title?></title>
	<? Bring_Configs_From_DB();?>
</head>

<body>

<div id="header_bar"><!------------------ HEADER_BAR >>>--------------------------------------------> 

		<a href="#mypanel" class="hide_sidebar" id="hider"  onclick=""><img src="../images/hider-button.png" alt="" title="this will be displayed as a tooltip"/></a>
		<? 		
		/*************************   List Panel   **************************/
		MyPanel();
		
		/*************************   Header Bar BUTTONS   **************************/
		DisplayHeaderBarButtons();
		?>
</div><!------------------------------------- HEADER_BAR <<<------------------------------------------> 

<div id="content_sidebar"><!----------------- CONTENT_SIDEBAR >>>------------------------------------->
 
    <div class="side_bar">
   		<? 
   		/************************   SEARCHING   ***************************/
		SEARCHING();
    	
    	/************************   Side Bar BUTTONS   ***************************/
    	DisplaySideBarButtons(); 
    	?>
    	
    	<div class="btn_div" id="mail_list">
			<div class="side_bar_blk">
				<? /**************   Mail List   ***************/
				Mail_List();
				?>
			</div>
        </div>
    </div>
    
    <?
    /*************************   Advertisements   **************************/
    Display_Ads();    
    ?>    
  	<?if(!$_SESSION['GID']){?>
    	<a href="../common/signin.php?lang=<?=$GLOBALS['lang']?>" id="signin" <?=$style?>>
    	<img src="../images/signin.png" style=""/> <span><?=Sign_in?></span> </a><br/>
    <?}?>
	
</div><!------------------------ CONTENT_SIDEBAR <<<-------------------------------------------------->	
<div id="main_content"><?
} ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

function tpl_footer(){//____________________________________________________________________________________________________________________
	include_once '../common/js.php';
	include_once '../common/pframe.php';
	$myfrm =new pframe();	
	$arr = $myfrm->SiteConfig("social_media");  $i=0;
	?>
	<div class="footer">
		<? /*********************   Visits Counter   *******************/?>
		<? $myfrm->VisitsCounter();?>
		
		<div class="social_media"> <? 
           /*********************   Social Media   *******************/?>
			<?
			foreach($arr as $key => $val){
				echo "";
				?><a href="<?=$arr[$i]['link']?>" >
   <img src="../documents/thumbs/BSOCIAL_<?=$arr[$i]['NID']?>.<?=$arr[$i]['pic_social']?>" /> </a><? 
				$i++;
			}
			?>
		</div>
		<div class="fcontent">
			<span><?=footer1?></span>
			<p><?=footer2?></p>
		</div>
	</div></div>
	</body></html><?
}////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

function MyPanel() 
// this is the panel that appears when we click on the panel button in the corner
{
	include_once '../common/pframe.php';
	$myfrm =new pframe();
	?>
	<div id="mypanel" class="hidden">

// link buttons of the panel

		<!-- <a href="../common/index.php?lang=<?=$GLOBALS['lang']?>" ><?=Home?></a> -->
    		<a href="../pages/products.php?v=t&lang=<?=$GLOBALS['lang']?>" ><?=Products?></a>
    		<a href="../pages/news.php?v=t&lang=<?=$GLOBALS['lang']?>" ><?=News?></a>
    		<a href="../pages/services.php?v=t&lang=<?=$GLOBALS['lang']?>"><?=Our_Services?></a>
		<a href="../pages/our_customers.php?v=t&lang=<?=$GLOBALS['lang']?>"><?=Our_Customers?></a>
		<a href="../pages/download.php?v=t&lang=<?=$GLOBALS['lang']?>"><?=Download?></a>
	<?

// logged in users have different buttons according to privs they have

	 if (session_is_registered("GID")) {
	 	?><a href="../pages/shared_pool.php?v=t&lang=<?=$GLOBALS['lang']?>&path="><img src="../images/icons/shared.png" style="float:left;margin-left:10px;margin-top:5px"/><?=Shared_Pool?></a><? 
	 	if($myfrm->Is_Customer()){
	 		?> <a class="" href="../pages/bconts.php?lang=<?=$GLOBALS['lang']?>&v=t"><?=BConts_Management?></a> <?
	 		?> <a class="" href="../pages/mconts.php?lang=<?=$GLOBALS['lang']?>&v=t"><?=MConts_Management?></a> <?
	 	}else{
	 		?><p class="sep">_________________</p><? 
			?><a href="../common/control_panel.php?lang=<?=$GLOBALS['lang']?>" id="signin"><img src="../images/cp.png" style="float:left;margin-left:10px;"/><?=Control_Panel?></a>
		<? }?>
		<a href="../cms/signout.php" id="signout" ><img src="../images/signout.png" style="float:left;margin-left:10px;"/><?=Sign_Out?></a>
		<?					
	 }else{ 
		?>		
		<a href="../pages/albums.php?v=t&lang=<?=$GLOBALS['lang']?>"><?=Albums?></a>
		<a href="../pages/jobs.php?v=t&lang=<?=$GLOBALS['lang']?>"><?=Jobs?></a>
		<a href="../pages/faq.php?v=t&lang=<?=$GLOBALS['lang']?>"><?=FAQ?></a>
		<a href="../pages/contactus.php?v=about&lang=<?=$GLOBALS['lang']?>"><?=About?></a>
		<? 				 
	}?>
	</div>

// script of SHOW\HIDE event when clicking the panel button

	<script type=text/javascript> //joggle hider button
	$('#hider').click(function(e){ 
	    e.stopPropagation();
	    //$('#mypanel').slideToggle();
	    $('#mypanel').attr('class', $('#mypanel').attr('class') == 'hidden' ? 'shown' : 'hidden');				    
	    $('body').animate({scrollTop: 0});
	    $('#header_bar').animate({scrollTop: 0});				    
	});
	$(document).click(function(){
	     //$('#mypanel').slideUp();
	     $('#mypanel').attr('class', 'hidden');
	});
	</script><? 
}////////////////////////////////////////////////////////////////////////////////////////

function DisplayHeaderBarButtons()

// HEADER BAR
{	
	include_once '../common/pframe.php';
	$myfrm =new pframe();
	
	$elem_loggedin="";
	if(session_is_registered("GID")){		$elem_loggedin="button_loggedin";
	} else{ $elem_loggedin="button";	}
	
	$self=basename($_SERVER['PHP_SELF']);
	$v=$_REQUEST['v'];
	?>
		<div  class="padding_before_home_button">&nbsp;</div>
    	<a href="../common/index.php?lang=<?=$GLOBALS['lang']?>" class="<?=$elem_loggedin?> <?=$myfrm->is_current_page($self, "index.php")?>"><?=Home?></a>
    	<a href="../pages/products.php?v=t&lang=<?=$GLOBALS['lang']?>" class="<?=$elem_loggedin?> <?=$myfrm->is_current_page($self,"products.php")?>" ><?=Products?></a>
    	<a href="../pages/news.php?v=t&lang=<?=$GLOBALS['lang']?>" class="<?=$elem_loggedin?> <?=$myfrm->is_current_page($self,"news.php")?>" ><?=News?></a>
    	<a href="../pages/contactus.php?lang=<?=$GLOBALS['lang']?>&v=contactus" class="<?=$elem_loggedin?> <?=$myfrm->is_current_page($self, "contactus.php", $v, "contactus")?>"><?=ContactUs?></a>
    	<a href="../pages/contactus.php?lang=<?=$GLOBALS['lang']?>&v=about" class="<?=$elem_loggedin?> <?=$myfrm->is_current_page($self, "contactus.php", $v, "about")?>"><?=About?></a>
    	
    	<? $opos1=""; $opos2="";?>
    	<? if($GLOBALS['lang']=='ar'){$opos1='en';$opos2=EN;}else{$opos1='ar';$opos2=AR;}?>
    	<a href="../common/index.php?lang=<?=$opos1?>" class="<?=$elem_loggedin?>"><?=$opos2?></a>
    	
    	<? $arr = $myfrm->SiteConfig("header_logo");?>
    	<div class="logo"><a href="../common?lang=<?=$GLOBALS['lang']?>"><img src="../documents/SOCIAL_<?=$arr[0]['NID']?>.<?=$arr[0]['pic_social']?>" /></a></div>
    	<? if(session_is_registered("GID")){?>
    		<a href="../pages/users.php?lang=<?=$GLOBALS['lang']?>&v=p&NID=<?=$_SESSION['GID']?>" class="user_profile <?=$elem_loggedin?> <?=$myfrm->is_current_page($self, "user", "p")?>">
    			<span class="unm"><?=$_SESSION['UNM']?></span>
    			<? 
    			$src = "../documents/thumbs/BUSER_{$_SESSION["GID"]}.{$_SESSION["UPIC"]}";
    			//echo $src;
    			if(!file_exists($src))
    				//$src = "../images/xuser.png";
    			?>
    			<img src="<?=$src?>" class="profile_image"/>
    		</a>
    	<? }?>
    	<!-- ------------------------------------ -->
    	<a href="#" class="<?=$elem_loggedin?> <?=$myfrm->is_current_page($self,".php")?>" ><?	//if($_SESSION['GID']) {foreach($_SESSION['PRIVS'] as $p) {echo "$p + ";}}?></a>
    <? 	
}////////////////////////////////////////////////////////////////////////////////////////

function SEARCHING()
//it's obviously clear, the search textbox with scripts calling
{
	include_once '../common/pframe.php';
	$myfrm =new pframe();
	
	?><script src="../cms/jquery.min.js"></script>
	<script src="../cms/bootstrap.min.js"></script>
	<script src="../cms/typeahead.min.js"></script>
	
	<script>

// jquery script to operate search query in other page 

	$(document).ready(function(){
		$('input.typeahead').typeahead({
			name: 'typeahead',
			remote:'search_sql.php?key=%QUERY',
			limit : 10
		});
	});
	
	document.getElementById('txtsrch').addEventListener('keypress', function(event) {
		if (event.keyCode == 13) {
			event.preventDefault();
			document.getElementById('send').click();
		}
	});
	</script>
	
	 <? ///////////  Searching textbox   ////////// ?>
	 <div class="search_div">	
	 	<form action="../common/search_sql.php" method="get" id="srch_form"> 
			<input type="text" id="txtsrch" name="typeahead" class="typeahead tt-query " autocomplete="off" spellcheck="false" placeholder="<?=Search_all_over_thesite?>">
			<input type="submit" id="send" value="send" style="display:none"/>
		</form>
	 </div>
	 <? 
}////////////////////////////////////////////////////////////////////////////////////////

function check_email_address($email) {
	// First, we check that there's one '@' symbol, and that the lengths are right
	if (!ereg("^[^@]{1,64}@[^@]{1,255}$", $email)) {
		// Email invalid because wrong number of characters in one section, or wrong number of @ symbols.
		return false;
	}

	// Split it into sections to make life easier

	$email_array = explode("@", $email);
	$local_array = explode(".", $email_array[0]);
	for ($i = 0; $i < sizeof($local_array); $i++) {
		if (!ereg("^(([A-Za-z0-9!#$%&'*+/=?^_`{|}~-][A-Za-z0-9!#$%&'*+/=?^_`{|}~\.-]{0,63})|(\"[^(\\|\")]{0,62}\"))$", $local_array[$i])) {
			return false;
		}
	}
	if (!ereg("^\[?[0-9\.]+\]?$", $email_array[1])) { 
// Check if domain is IP. If not, it should be valid domain name

		$domain_array = explode(".", $email_array[1]);
		if (sizeof($domain_array) < 2) {
			return false; // Not enough parts to domain
		}
		for ($i = 0; $i < sizeof($domain_array); $i++) {
			if (!ereg("^(([A-Za-z0-9][A-Za-z0-9-]{0,61}[A-Za-z0-9])|([A-Za-z0-9]+))$", $domain_array[$i])) {
				return false;
			}
		}
	}
	return true;
}////////////////////////////////////////////////////////////////////////////////////////

function Mail_List()
// all operations related to mail list : 
// validation, redirection, parameters, show messages, inserting to DB
{
	if($_POST['mail_list'])
	{
		$email_invalid_format = false;
		$email_already_exists = false;
		$email_added_successfully = false;
		$email_insert_error = false;	
		
		$everything_is_ok = true;
		
// validate empty values

		if($_REQUEST['txtmail'] == "" || $_REQUEST['txtmail'] == insrt_ur_eml || $_REQUEST['txtmail'] == Email_already_exists || $_REQUEST['txtmail'] == Added_successfully || $_REQUEST['txtmail'] == Adding_error ){
			$everything_is_ok = false;
		}

// validate email format
		
		if ( !check_email_address( $_REQUEST['txtmail'] ) ) {
			$email_invalid_format = true;
			$_REQUEST['txtmail'] = Invalid_Email;
			$everything_is_ok = false;
		}

//your email already exists in our mail list

		$result = get_data_in("select email from mail_list where email = '{$_REQUEST['txtmail']}' ", "email");
		if( $result != "") {  		$email_already_exists = true;			
		}else{
			if( $everything_is_ok )
			{
				if(cmd("insert into mail_list values('".uniqid()."', '{$_REQUEST['txtmail']}','".nowandate."');"))	{
					$email_added_successfully = true;}
				else{$email_insert_error = true; }
				
			}
		}

// removing the maillist parameter from the URI
 		
		$arr = explode("&", $_SERVER['QUERY_STRING']);
		foreach ($arr as $key=>$val) 
		{
			if(($pos=strpos($val, "mail_list=")) !== false)		
				unset($arr[$key]);
		}
		$queryString="";
		foreach ($arr as $key=>$val)
		{
			$queryString .="&{$val}";
		}
		$queryString = ltrim($queryString, '&');
		echo $queryString;
		
//redirection to page now after all that manipulation

		if($email_already_exists)
			header("location: {$_SERVER['PHP_SELF']}?{$queryString}&mail_list=Email_already_exists");
		elseif($email_added_successfully)
				header("location: {$_SERVER['PHP_SELF']}?{$queryString}&mail_list=Added_successfully");
		elseif($email_insert_error)
			header("location: {$_SERVER['PHP_SELF']}?{$queryString}&mail_list=Adding_error");
		elseif($email_invalid_format)
			header("location: {$_SERVER['PHP_SELF']}?{$queryString}&mail_list=Invalid_Email");
		else
			$mail_list_message="";	
		 
	}// END of if($_POST['mail_list'])
	$mail_list_message="";
	$style="";
	switch ($_REQUEST['mail_list'])
 
// show result message

	{
		case "Email_already_exists":
			$mail_list_message=Email_already_exists;
			$style='color:red;border:2px red solid;';
			break;
		case "Added_successfully":
			$mail_list_message=Added_successfully;
			$style='color:green;border:2px green solid;';
			break;
		case "Adding_error":
			$mail_list_message=Adding_error;
			$style='color:red;border:2px red solid;';
			break;
		case "Invalid_Email":
			$mail_list_message=Invalid_Email;
			$style='color:red;border:2px red solid;';
			break;
	}
	if($mail_list_message == '')
		$mail_list_message = insrt_ur_eml;
	?>

// HTML form for subscribing

 	<div style="text-align:center;">
		<form action="<?=$_SERVER['PHP_SELF'].'?'.$_SERVER['QUERY_STRING']?>" method="post" id="form_maillist">
			<input type="text" name="txtmail" hint="<?=insrt_ur_eml?>" value="<?=$mail_list_message?>"  class="txtmaillist" id="mailbox_txt" style="<?=$style?>" onclick="this.value='';" onblur="if(this.value=='') this.value='<?=$mail_list_message?>';" autocomplete="off"/>
			<input type="submit" name="mail_list" value="<?=Send?>" class="submit" id="mail_list" onclick="checktxtbx()" />
			<input type="hidden" name="REQUEST_URI" value="<?=$_SERVER['REQUEST_URI']?>" />
		</form>
	</div>
 	<?
}////////////////////////////////////////////////////////////////////////////////////////

function DisplaySideBarButtons()

// SIDE BAR 

{
	include_once '../common/pframe.php';
	$myfrm =new pframe();
	$self = basename($_SERVER['PHP_SELF']) ;

// Buttons also vary according to the logged in user and his user category

	?><a class="button <?=$myfrm->is_current_page($self, "category.php")?>" href="../pages/category.php?v=t&lang=<?=$GLOBALS['lang']?>"><?=Categories?></a>
		<a class="button <?=$myfrm->is_current_page($self, "services.php")?>" href="../pages/services.php?v=t&lang=<?=$GLOBALS['lang']?>"><?=Our_Services?></a>
		<a class="button <?=$myfrm->is_current_page($self, "our_customers.php")?>" href="../pages/our_customers.php?lang=<?=$GLOBALS['lang']?>&v=t"><?=Our_Customers?></a>
		<a class="button <?=$myfrm->is_current_page($self, "download.php")?>" href="../pages/download.php?v=t&lang=<?=$GLOBALS['lang']?>"><?=Download?></a>
		<? if($myfrm->Is_Distributor() || $myfrm->Is_Customer()){?>
			<a class="button <?=$myfrm->is_current_page($self, "bconts.php")?>" href="../pages/bconts.php?lang=<?=$GLOBALS['lang']?>&v=t"><?=BConts_Management?></a>
			<a class="button <?=$myfrm->is_current_page($self, "mconts.php")?>" href="../pages/mconts.php?lang=<?=$GLOBALS['lang']?>&v=t"><?=MConts_Management?></a>
						
		<? }else{?>
			<a class="button <?=$myfrm->is_current_page($self, "jobs.php")?>" href="../pages/jobs.php?v=t&lang=<?=$GLOBALS['lang']?>"><?=Jobs?></a>
			<a class="button <?=$myfrm->is_current_page($self, "faq.php")?>" href="../pages/faq.php?v=t&lang=<?=$GLOBALS['lang']?>"><?=FAQ?></a>
			<a class="button <?=$myfrm->is_current_page($self, "albums.php")?>" href="../pages/albums.php?v=t&lang=<?=$GLOBALS['lang']?>"><?=Albums?></a>
		<? }
}////////////////////////////////////////////////////////////////////////////////////////

function Display_Ads()
{
// bringing random 5 Advertisements that are still valid from DB and show'em rotately

	include_once '../obj/ads.class.php';
	$myAds=new Ads();
	
	$sql = "SELECT * FROM {$myAds->tblname} WHERE (now() BETWEEN ads_start AND ads_end) ORDER BY RAND() LIMIT 5";
	$tbl=table($sql);
	$id_arr=array();
	$url_arr=array();
	$i=0;
	
	while($row=mysql_fetch_array($tbl))
	{
		$myAds->FillIn($row);
		$id_arr[$i] = "../documents/ADS_{$myAds->ads_id['value']}.{$myAds->ads_pic['ext']}";
		$url_arr[$i] = $myAds->ads_link['value'];
		$i++;
	}
	
	$interval=3000;
	?>
	<div id="ads_div"> 
	
	<p class="ads_bar_hd">
	<? if(user_has_permission(array("A", "ADS_MAN"))){?>
		<a class="ads_bar_hd_a" href="../pages/ads.php?lang=<?=$GLOBALS['lang']?>&v=t"><img class="edt" src="../images/pencil-512.png" /><?=Ads?></a>
	<? }else{ echo Ads;}?>
	</p>
	<a id="ads_link" href="" TARGET="_BLANK" ><img id='ads_img'/></a>
	</div>
	
	<script>
	(function(){
		
	///// Rotation Interval(msec)
	var interval = <?=$interval?>;
	///// pic ID
	var pic_id = "ads_img";
	var ads_link = "ads_link";
	///// pic URL list
	var pics = <?php echo json_encode($id_arr ); ?>;
	var urls = <?php echo json_encode($url_arr ); ?>;
	var pic_idx = 0;
	var pic_img = document.getElementById(pic_id);
	var a_img = document.getElementById(ads_link); 
	function rotate_pic(){
		if ( pic_idx == pics.length ) { pic_idx = 0; }
		var src = pics[pic_idx];
		pic_img.setAttribute("src", src);
		var url = urls[pic_idx];
		a_img.setAttribute ("href", url);
		pic_idx++;
		setTimeout( rotate_pic, interval );
	}
	rotate_pic();		}());
	</script>	<? 
}////////////////////////////////////////////////////////////////////////////////////////

function Bring_Configs_From_DB()
{
//  styles stored in DB as site configs, overwrite them in-line

	include_once '../common/pframe.php';
	$myfrm =new pframe();
	$color_main = $myfrm->SiteConfig("color_main");
	$color_second = $myfrm->SiteConfig("color_second");
	$color_third = $myfrm->SiteConfig("color_third");	
	$side_bar_button = $myfrm->SiteConfig("side_bar_button");
	
	echo $color_third[0]['value'];
?><style>

	/***** replace these with the mail color from DB *****/
	/************   color_main   ***********/
	a,a:active,a:visited{color:<?=$color_main[0]['value']?>}
	input[type="search"] {color:<?=$color_main[0]['value']?>}
	.Visits_to_Site{color:<?=$color_main[0]['value']?>}
	.footer .Visits_to_Site{color:<?=$color_main[0]['value']?>}
	#header_bar{background-color:<?=$color_main[0]['value']?>;border-bottom:1px <?=$color_main[0]['value']?> solid;}
	#header_bar .button{border:1px <?=$color_main[0]['value']?> solid;}
	#header_bar .button_loggedin{border:0px <?=$color_main[0]['value']?> solid;}
	#header_bar .is_current_page{color:<?=$color_main[0]['value']?>;}
	#content_sidebar #signin:hover{border:2px <?=$color_main[0]['value']?> solid;}
	.products .prod_more:hover{background-color:<?=$color_main[0]['value']?>;}
	.latest_news .news_more:hover{background-color:<?=$color_main[0]['value']?>;}
	.side_bar .button{color:<?=$color_main[0]['value']?>}
	.withTree a,.withTree a:active,.withTree a:visited{color:<?=$color_main[0]['value']?>}
	.side_bar_hd{background-color:<?=$color_main[0]['value']?>;}
	.side_bar .button:hover{border-color:<?=$color_main[0]['value']?>;
	.side_bar .is_current_page{color:<?=$color_main[0]['value']?>}
	.side_bar input[type="submit"]{background-color:<?=$color_main[0]['value']?>;border:2px <?=$color_main[0]['value']?> solid;}
	.side_bar input[type="submit"]:hover{background-color:<?=$color_main[0]['value']?>;}
	.ads_bar_hd{background-color:<?=$color_main[0]['value']?>;}
	.global_tbl tr:hover{border:1px <?=$color_main[0]['value']?> solid;}	
	.item_title a:hover{color:<?=$color_main[0]['value']?>;}
	.edit_tool_ico img:hover{border:2px <?=$color_main[0]['value']?> solid}
	.navbar a, .navbar a:visited{color:<?=$color_main[0]['value']?>;border:1px <?=$color_main[0]['value']?> solid;}
	.navbar a:hover{background-color:<?=$color_main[0]['value']?>;}
	
	/************   color_second   ***********/
	#header_bar .button:hover{background-color:<?=$color_second[0]['value']?>;}
	#header_bar .button_loggedin:hover{background-color:<?=$color_second[0]['value']?>}
	#header_bar .hide_sidebar:hover{ background-color:<?=$color_second[0]['value']?>;}
	.li_user{color:<?=$color_second[0]['value']?>}
	li.collapsibleListOpen{background-color:<?=$color_second[0]['value']?>;}
	.global_tbl th{background-color:<?=$color_second[0]['value']?>;}
	
	/************   color_third   ***********/
	input[type="search"] {border:1px <?=$color_third[0]['value']?> solid;}
	#header_bar .button{color:<?=$color_third[0]['value']?>;}
	#header_bar .logo img{border:1px <?=$color_third[0]['value']?> solid}
	#content_sidebar{background-color:<?=$color_third[0]['value']?>;}
	.side_bar{border-left:0px solid <?=$color_third[0]['value']?>;}
	.side_bar .button{border:2px <?=$color_third[0]['value']?> solid;}
	.content_hd{background-color:<?=$color_third[0]['value']?>;}
	.products .prod_sep{background-color:<?=$color_third[0]['value']?>;}
	.latest_news .news_img img{border:1px <?=$color_third[0]['value']?> solid;}
	#control_panel{width:700px;border:1px <?=$color_third[0]['value']?> solid;}
	#control_panel .color2{background-color:<?=$color_third[0]['value']?>}
	.related_pages_hd{background-color:<?=$color_third[0]['value']?>;}
	.global_tbl{border:2px <?=$color_third[0]['value']?> solid;}
	.user_card{border:1px <?=$color_third[0]['value']?> solid;}
	.user_card td{border-bottom:1px <?=$color_third[0]['value']?> solid;}
	.user_card .card_img{border:1px <?=$color_third[0]['value']?> solid;}
	.groups{border:2px <?=$color_third[0]['value']?> solid;}
	.li_group{border:2px <?=$color_third[0]['value']?> solid;}
	.li_user{border:2px <?=$color_third[0]['value']?> solid;}
	.li_gr_privs{border:1px <?=$color_third[0]['value']?> solid;}
	.all_privs{border:2px <?=$color_third[0]['value']?> solid;}
	.all_privs .hd{border:1px <?=$color_third[0]['value']?> solid;background-color:<?=$color_third[0]['value']?>;}
	.panel_title_text{background-color:<?=$color_third[0]['value']?>;}
	.msgbox .msgbox_line_sep{border-top:1px <?=$color_third[0]['value']?> solid;}
	
	
	/************   side_bar_button   ***********/
	.side_bar .button:hover{background-color:<?=$side_bar_button[0]['value']?>;border-color:;}
	.side_bar .is_current_page{border:2px #666 solid;}

</style>

<? 
}////////////////////////////////////////////////////////////////////////////////////////

 

