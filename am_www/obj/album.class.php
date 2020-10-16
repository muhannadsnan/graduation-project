<?php
include_once '../cms/dataset.php';
include_once '../lang/pages_'.$GLOBALS['lang'].'.inc';


class Album extends JSDataSet
{
    public $album_id=array('name'=>'album_id', 'type'=>'ID', 'caption'=>'album_id', 'control'=>'none', 'ftbl'=>'', 'fTitle'=>'', 'fID'=>'' );

    public $album_title_ar=array('name'=>'album_title_ar', 'type'=>'varchar', 'caption'=>'album_title_ar', 'control'=>'text', 'required'=>'required');

    public $album_title_en=array('name'=>'album_title_en', 'type'=>'varchar', 'caption'=>'album_title_en', 'control'=>'text', 'required'=>'required' );

    public $NVNom=array('name'=>'NVNom', 'type'=>'int', 'caption'=>'NVNom', 'control'=>'none' , 'value'=>0);

    public $NDate=array('name'=>'NDate', 'type'=>'varchar', 'caption'=>'NDate', 'control'=>'none', 'value'=>nowandate, 'format'=>'%Y-%m-%d %H:%M:%S', 'withtime'=>'true', 'NTitle'=>'');

    public $tblname="album";


	
	public function onStart()
	{
		$this->lang['value'] = $GLOBALS['lang'];
		$this->NDate['NTitle']=$this->{albums_title_x}['value'];
	}
	
	public function onRemove($res)
	{

//on delete album dascade for it's child pictures

		if ($res==$GLOBALS['MyErrStr']->RowDeleted){
			include_once '../obj/picture.class.php';
			$pic=new Picture();
			$pic->RemoveRows(" pic_album = '{$this->NID['value']}'");
		}
	}}
 