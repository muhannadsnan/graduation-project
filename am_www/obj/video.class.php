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