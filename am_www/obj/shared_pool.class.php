<?php
include_once '../cms/dataset.php';
include_once '../lang/pages_'.$GLOBALS['lang'].'.inc';


class Shared_pool extends JSDataSet
{
	public $NID=array('name'=>'NID', 'type'=>'ID', 'caption'=>'NID', 'control'=>'none' );

	public $user=array('name'=>'user', 'type'=>'varchar', 'caption'=>'user', 'control'=>'none', 'ftbl'=>'', 'fTitle'=>'', 'fID'=>'' );

	public $file_name=array('name'=>'file_name', 'type'=>'varchar', 'caption'=>'file_name', 'control'=>'text' );

	public $file_size=array('name'=>'file_size', 'type'=>'varchar', 'caption'=>'file_size', 'control'=>'none' );

	public $file_type=array('name'=>'file_type', 'type'=>'file', 'caption'=>'file_type', 'control'=>'file' ,'filetypes'=>'', 'prefix'=>'', 'control'=>'file', 'ext'=>'');

	public $path=array('name'=>'path', 'type'=>'text', 'caption'=>'path', 'control'=>'none', 'value'=>'' );

	public $NDate=array('name'=>'NDate', 'type'=>'varchar', 'caption'=>'NDate', 'control'=>'none', 'value'=>nowandate, 'format'=>'%Y-%m-%d %H:%M:%S', 'withtime'=>'true', ''=>'');

	public $tblname="shared_pool";
	
	
	public function onStart($DID){
		 
// load some values from DB

		$this->file_type['ext'] = get_data_in("select file_type from {$this->tblname} where NID='{$this->NID["value"]}'", "file_type");
		$this->NDate['NTitle']=$this->file_name['value'];
		
		$dir = $this->get_folders();
		$this->documents_path = "../documents/SHARED_POOL{$dir}"; //echo $this->documents_path ;
		
	}/////////////////////
	
	public function onInsert($res)
	{
//when inserting a directory, we are not using the user defined names but we use unique IDs
//so, get these IDs and create the dir 
		if($_REQUEST['dir'] == '1')
		{
			$dir = $this->get_folders();
			mkdir("{$this->documents_path}{$this->NID['value']}"); 
		}
	}/////////////////////
	
	public function onRemove($res)
	{
		$dir = $this->get_folders();
		
			if($this->file_type['ext'] == '[dir]'){
				$dir_path = "{$this->documents_path}{$this->NID['value']}";	
				@rmdir($dir_path);
			} 
			else {
				@unlink("{$this->documents_path}{$this->NID['value']}.{$this->file_type['ext']}"); 
			}
		
	}/////////////////////
	
	function get_folders()
	 {
	 	//bring the id for dir
	 	if($_REQUEST['path'] == ''){ return '/';}
	 	else{
		 	$arr = explode('/', $_REQUEST['path']);
		 	$dir = "";
		 	$i=0;
		 	$ids=array();
		 	foreach( $arr as $v){
		 		$ids[$i] = get_data_in("select NID from shared_pool where file_type='[dir]' and file_name='{$v}' ", "NID");
		 		$i++;
		 	}
		 	foreach( $ids as $v){
		 		if($v != '') { $dir .= "/{$v}"; }
		 	}
		 	if($_REQUEST['path'] != '')
		 		$dir .='/';
		 	return $dir;
	 	}
	 }/////////////////////	 
}
