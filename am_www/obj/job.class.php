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