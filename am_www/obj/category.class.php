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