<?php
include_once '../cms/dataset.php';
include_once '../lang/pages_'.$GLOBALS['lang'].'.inc';


class Users extends JSDataSet
{
	public $UID=array('name'=>'UID', 'type'=>'ID', 'caption'=>'UID', 'control'=>'fkey', 'ftbl'=>'', 'fTitle'=>'', 'fID'=>'' , 'required'=>true);

	public $uname=array('name'=>'uname', 'type'=>'varchar', 'caption'=>'uname', 'control'=>'fkey', 'ftbl'=>'', 'fTitle'=>'', 'fID'=>'' , 'required'=>true);

	public $upass=array('name'=>'upass', 'type'=>'varchar', 'caption'=>'upass', 'control'=>'fkey', 'ftbl'=>'', 'fTitle'=>'', 'fID'=>'' , 'required'=>true);

	public $utype=array('name'=>'utype', 'type'=>'varchar', 'caption'=>'utype', 'control'=>'text' , 'required'=>true);

	public $tblname="users";
	
	function onUserInsertedRow($res, &$ShowForm, &$NID)
	{
		if ($res==$GLOBALS['MyErrStr']->DBOK){
			$ShowForm=false;
			header("location:../cms/secreg.php?lang={$GLOBALS['lang']}&txtusr={$this->UserName['value']}&txtpass={$this->upass['value']}");
			//echo $GLOBALS['MyErrStr']->Show($GLOBALS['MyErrStr']->Commented);
			$NID="new";
		}else {
			echo $GLOBALS['MyErrStr']->Show($res);
			$ShowForm=true;
		}
	
	}

}

