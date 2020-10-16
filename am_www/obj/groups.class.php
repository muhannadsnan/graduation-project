<?php
include_once '../cms/dataset.php';
include_once '../lang/pages_'.$GLOBALS['lang'].'.inc';


class Groups extends JSDataSet
{
	public $group_id=array('name'=>'group_id', 'type'=>'ID', 'caption'=>'group_id', 'control'=>'none');

	public $group_name=array('name'=>'group_name', 'type'=>'varchar', 'caption'=>'group_name', 'control'=>'text', 'required'=>'required' );
	
	public $NDate=array('name'=>'NDate', 'type'=>'varchar', 'caption'=>'NDate', 'control'=>'none', 'value'=>nowandate, 'format'=>'%Y-%m-%d %H:%M:%S', 'withtime'=>'true', 'NTitle'=>'');

	public $tblname="groups";

	public function onStart($DID){
		 
		$this->NDate['NTitle']=$this->group_name['value'];
	}
}