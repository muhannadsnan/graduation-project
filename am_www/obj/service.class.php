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