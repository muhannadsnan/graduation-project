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