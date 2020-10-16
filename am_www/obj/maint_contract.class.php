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