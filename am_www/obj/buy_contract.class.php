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