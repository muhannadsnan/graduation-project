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