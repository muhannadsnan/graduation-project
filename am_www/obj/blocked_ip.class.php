<?php
include_once '../cms/dataset.php';
include_once '../lang/pages_'.$GLOBALS['lang'].'.inc';


class Blocked_ip extends JSDataSet
{
	public $NID=array('name'=>'NID', 'type'=>'ID', 'caption'=>'NID', 'control'=>'none');
	
	public $ip=array('name'=>'ip', 'type'=>'varchar', 'caption'=>'ip', 'control'=>'text' );
	
	public $user=array('name'=>'user', 'type'=>'varchar', 'caption'=>'user', 'control'=>'textarea' );

	public $attempts=array('name'=>'attempts', 'type'=>'int', 'caption'=>'attempts', 'control'=>'text' , 'value'=>0);

	public $reason=array('name'=>'reason', 'type'=>'varchar', 'caption'=>'reason', 'control'=>'text' );

	public $NDate=array('name'=>'NDate', 'type'=>'varchar', 'caption'=>'NDate', 'control'=>'none', 'value'=>nowandate, 'format'=>'%Y-%m-%d %H:%M:%S', 'withtime'=>'true', 'NTitle'=>'');

	public $tblname="blocked_ip";
	
	public function onStart(){
		
		$this->NDate['NTitle']=$this->ip['value'];
	}
}