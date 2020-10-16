<?php
include_once '../cms/dataset.php';
include_once '../lang/pages_'.$GLOBALS['lang'].'.inc';


class News extends JSDataSet
{
    public $news_id=array('name'=>'news_id', 'type'=>'ID', 'caption'=>'news_id', 'control'=>'none');

    public $news_title_ar=array('name'=>'news_title_ar', 'type'=>'varchar', 'caption'=>'news_title_ar', 'control'=>'text', 'required'=>'required' );

    public $news_title_en=array('name'=>'news_title_en', 'type'=>'varchar', 'caption'=>'news_title_en', 'control'=>'text', 'required'=>'required' );

    public $news_desc_ar=array('name'=>'news_desc_ar', 'type'=>'varchar', 'caption'=>'news_desc_ar', 'control'=>'text' );

    public $news_desc_en=array('name'=>'news_desc_en', 'type'=>'varchar', 'caption'=>'news_desc_en', 'control'=>'text' );

    public $news_text_ar=array('name'=>'news_text_ar', 'type'=>'text', 'caption'=>'news_text_ar', 'control'=>'textarea' );

    public $news_text_en=array('name'=>'news_text_en', 'type'=>'text', 'caption'=>'news_text_en', 'control'=>'textarea' );
    
    public $news_pic=array('name'=>'news_pic', 'type'=>'file', 'caption'=>'news_pic', 'control'=>'file', 'filetypes'=>'jpg|png|gif', 'resize'=>true, 'prefix'=>'NEWS_', 'view'=>'image', 'sizes'=>array('thumb'=>array('p'=>'B', 'w'=>300, 'h'=>195), 'small'=>array('p'=>'S', 'w'=>180, 'h'=>126)), 'ext'=>'');

    public $NVNom=array('name'=>'NVNom', 'type'=>'int', 'caption'=>'NVNom', 'control'=>'none' , 'value'=>0);

    public $NDate=array('name'=>'NDate', 'type'=>'varchar', 'caption'=>'NDate', 'control'=>'none', 'value'=>nowandate, 'format'=>'%Y-%m-%d %H:%M:%S', 'withtime'=>'true', 'NTitle'=>'');

    public $tblname="news";
	
    
	public function onStart()
	{
    	$this->news_pic['ext'] = get_data_in("select news_pic from {$this->tblname} where {$this->NID["name"]}='{$this->news_id["value"]}'", "news_pic");
    	$this->NDate['NTitle']=$this->{news_title_x}['value'];
    }}