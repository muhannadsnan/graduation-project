<?php
include_once '../cms/dataset.php';
include_once '../lang/pages_'.$GLOBALS['lang'].'.inc';


class User_group_privs extends JSDataSet
{
	public $group_id=array('name'=>'group_id', 'type'=>'ID', 'caption'=>'group_id', 'control'=>'fkey', 'ftbl'=>'', 'fTitle'=>'', 'fID'=>'' );

	public $priv=array('name'=>'priv', 'type'=>'varchar', 'caption'=>'priv', 'control'=>'text' );

	public $tblname="user_group_privs";

}