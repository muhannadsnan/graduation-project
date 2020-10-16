<?php
include_once '../cms/dataset.php';
include_once '../lang/pages_'.$GLOBALS['lang'].'.inc';


class User_groups extends JSDataSet
{
	public $user_id=array('name'=>'user_id', 'type'=>'ID', 'caption'=>'user_id', 'control'=>'fkey', 'ftbl'=>'', 'fTitle'=>'', 'fID'=>'' );

	public $user_group_id=array('name'=>'user_group_id', 'type'=>'ID', 'caption'=>'user_group_id', 'control'=>'fkey', 'ftbl'=>'', 'fTitle'=>'', 'fID'=>'' );

	public $tblname="user_groups";

}