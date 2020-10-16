<?php
include_once '../cms/dataset.php';
include_once '../lang/pages_'.$GLOBALS['lang'].'.inc';


class Site_config extends JSDataSet
{
    public $NID=array('name'=>'NID', 'type'=>'ID', 'caption'=>'NID', 'control'=>'none');

    public $config=array('name'=>'config', 'type'=>'varchar', 'caption'=>'config', 'control'=>'list' ,'options'=>array('slider'=>'slider','social_media'=>'social_media'));

    public $title=array('name'=>'title', 'type'=>'varchar', 'caption'=>'title', 'control'=>'text' );

    public $link=array('name'=>'link', 'type'=>'text', 'caption'=>'link', 'control'=>'textarea' );

    public $value=array('name'=>'value', 'type'=>'varchar', 'caption'=>'value', 'control'=>'text');

    public $pic_social=array('name'=>'pic_social', 'type'=>'file', 'caption'=>'pic_social', 'control'=>'file', 'filetypes'=>'jpg|png|gif', 'prefix'=>'SOCIAL_', 'view'=>'image', 'resize'=>true, 'sizes'=>array('thumb'=>array('p'=>'B', 'w'=>64, 'h'=>64), 'small'=>array('p'=>'S', 'w'=>32, 'h'=>32)) , 'ext'=>'');

    public $pic_slider=array('name'=>'pic_slider', 'type'=>'file', 'caption'=>'pic_slider', 'control'=>'file', 'filetypes'=>'jpg|png|gif', 'prefix'=>'SLIDER_', 'view'=>'image', 'resize'=>true, 'sizes'=>array('thumb'=>array('p'=>'B', 'w'=>570, 'h'=>270), 'small'=>array('p'=>'S', 'w'=>180, 'h'=>126)) , 'ext'=>'');
    
    // in this class we chose two pictures although there is only single picture foreach config,
    //we did that, because we want to give every king of pictures different attributes and dimentions
    
    public $NDate=array('name'=>'NDate', 'type'=>'varchar', 'caption'=>'NDate', 'control'=>'none', 'value'=>nowandate, 'format'=>'%Y-%m-%d %H:%M:%S', 'withtime'=>'true', 'NTitle'=>'');

    public $tblname="site_config";
    
    public function onStart($DID){
    	$this->pic_social['ext'] = get_data_in("select pic_social from {$this->tblname} where NID='{$this->NID["value"]}'", "pic_social");
    	$this->pic_slider['ext'] = get_data_in("select pic_slider from {$this->tblname} where NID='{$this->NID["value"]}'", "pic_slider");
    	$this->NDate['NTitle']=$this->config['value']." : ".$this->title['value'];
    }
    /*
     *  define("slider","slider");
		define("social_media","social_media");
		define("color_main","color_main");
		define("color_second","color_second");
		define("color_third","color_third");
		define("fav_ico","fav_ico");
		define("side_bar_button","side_bar_button");
		define("header_logo","header_logo");
     */

}