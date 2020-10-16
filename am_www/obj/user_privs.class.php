<?php
include_once '../cms/dataset.php';
include_once '../lang/pages_'.$GLOBALS['lang'].'.inc';


class User_privs extends JSDataSet
{
	public $user_id=array('name'=>'user_id', 'type'=>'ID', 'caption'=>'user_id', 'control'=>'fkey', 'ftbl'=>'', 'fTitle'=>'', 'fID'=>'' );

	public $user_priv=array('name'=>'user_priv', 'type'=>'varchar', 'caption'=>'user_priv', 'control'=>'text' );

	public $user_grant=array('name'=>'user_grant', 'type'=>'bool', 'caption'=>'user_grant', 'control'=>'checkbox' , 'value'=>0);

	public $tblname="user_privs";

}