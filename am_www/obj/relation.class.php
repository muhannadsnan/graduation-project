<?php
include_once '../cms/dataset.php';
include_once '../lang/pages_'.$GLOBALS['lang'].'.inc';


class Relation extends JSDataSet
{
	public $rid=array('name'=>'rid', 'type'=>'ID', 'caption'=>'rid', 'control'=>'fkey', 'ftbl'=>'', 'fTitle'=>'', 'fID'=>'' , 'required'=>true);

	public $pid=array('name'=>'pid', 'type'=>'ID', 'caption'=>'pid', 'control'=>'fkey', 'ftbl'=>'', 'fTitle'=>'', 'fID'=>'' , 'required'=>true);

	public $pid2=array('name'=>'pid2', 'type'=>'ID', 'caption'=>'pid2', 'control'=>'fkey', 'ftbl'=>'', 'fTitle'=>'', 'fID'=>'' , 'required'=>true);

	public $type=array('name'=>'type', 'type'=>'file', 'filetypes'=>'jpg', 'prefix'=>'REL', 'caption'=>'', 'control'=>'file', 'view'=>'image', 'resize'=>true, 'sizes'=>array('thumb'=>array('p'=>'B', 'w'=>0, 'h'=>126)));

	public $tblname="relation";

}