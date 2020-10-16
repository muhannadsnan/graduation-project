<?php 
include_once '../cms/dataset.php';
include_once '../lang/pages_'.$GLOBALS['lang'].'.inc';


class Picture extends JSDataSet
{
    public $pic_id=array('name'=>'pic_id', 'type'=>'ID', 'caption'=>'pic_id', 'control'=>'none' );

    public $pic_album=array('name'=>'pic_album', 'type'=>'varchar', 'caption'=>'pic_album', 'control'=>'fkey', 'ftbl'=>'album', 'fTitle'=>album_title_x, 'fID'=>'album_id');

    public $pic_title_ar=array('name'=>'pic_title_ar', 'type'=>'varchar', 'caption'=>'pic_title_ar', 'control'=>'text', 'required'=>'required' );

    public $pic_title_en=array('name'=>'pic_title_en', 'type'=>'varchar', 'caption'=>'pic_title_en', 'control'=>'text', 'required'=>'required' );
    
    public $pic_desc_ar=array('name'=>'pic_desc_ar', 'type'=>'text', 'caption'=>'pic_desc_ar', 'control'=>'textarea' );
    
    public $pic_desc_en=array('name'=>'pic_desc_en', 'type'=>'text', 'caption'=>'pic_desc_en', 'control'=>'textarea' );

    public $pic_ext	= array('name'=>'pic_ext', 'type'=>'file', 'caption'=>'pic_ext', 'control'=>'file', 'filetypes'=>'jpg|gif|png|bmp', 'resize'=>true, 'prefix'=>'Pic_', 'view'=>'image',  'sizes'=>array('thumb'=>array('p'=>'B', 'w'=>199, 'h'=>124)), 'defimg'=>'../images/def.png', 'ext'=>'');

    public $NVNom=array('name'=>'NVNom', 'type'=>'int', 'caption'=>'NVNom', 'control'=>'none' , 'value'=>0);

    public $NDate=array('name'=>'NDate', 'type'=>'varchar', 'caption'=>'NDate', 'control'=>'none', 'value'=>nowandate, 'format'=>'%Y-%m-%d %H:%M:%S', 'withtime'=>'true', 'NTitle'=>'');
    
    public $tblname="picture";

	////////////////////////////////////////////////////////////////////////
	
	public function onStart()
	{
		$this->lang['value'] = $GLOBALS['lang'];
		$this->NDate['NTitle']=$this->{pic_title_x}['value'];
		$this->pic_ext['ext'] = get_data_in("select pic_ext from {$this->tblname} where pic_id='{$this->pic_id["value"]}'", "pic_id");
	}
	
	public function onInsert() {$this->CheckIsMain();}
	public function onUpdate() {$this->CheckIsMain();}
	
	public function CheckIsMain() {	}
}