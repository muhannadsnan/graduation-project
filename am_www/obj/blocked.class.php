<?php
include_once '../cms/dataset.php';
include_once '../lang/pages_'.$GLOBALS['lang'].'.inc';


class Blocked extends JSDataSet
{
	public $ip=array('name'=>'ip', 'type'=>'varchar', 'caption'=>'ip', 'control'=>'text' , 'required'=>true);

	public $UID=array('name'=>'UID', 'type'=>'ID', 'caption'=>'UID', 'control'=>'fkey', 'ftbl'=>'', 'fTitle'=>'', 'fID'=>'' , 'required'=>true);

	public $date=array('name'=>'date', 'type'=>'varchar', 'caption'=>'date', 'control'=>'date', 'value'=>nowandate, 'format'=>'%Y-%m-%d %H:%M:%S', 'withtime'=>'true');

	public $tnum=array('name'=>'tnum', 'type'=>'int', 'caption'=>'tnum', 'control'=>'text' , 'required'=>true, 'value'=>);

	public $tblname="blocked";

}