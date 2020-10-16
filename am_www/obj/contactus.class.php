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