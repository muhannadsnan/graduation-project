<?php
include_once '../cms/dataset.php';
include_once '../lang/pages_'.$GLOBALS['lang'].'.inc';


class General extends JSDataSet
{
	public $gid=array('name'=>'gid', 'type'=>'ID', 'caption'=>'gid', 'control'=>'fkey', 'ftbl'=>'', 'fTitle'=>'', 'fID'=>'' , 'required'=>true);

	public $gdate=array('name'=>'gdate', 'type'=>'varchar', 'caption'=>'gdate', 'control'=>'date', 'value'=>nowandate, 'format'=>'%Y-%m-%d %H:%M:%S', 'withtime'=>'true');

	public $tblname="general";

}