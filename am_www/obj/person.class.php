<?php
include_once '../cms/dataset.php';
include_once '../lang/pages_'.$GLOBALS['lang'].'.inc';


class Person extends JSDataSet
{
    public $pid=array('name'=>'pid', 'type'=>'ID', 'caption'=>'pid', 'control'=>'fkey', 'ftbl'=>'', 'fTitle'=>'', 'fID'=>'' , 'required'=>true);
    
    public $gid=array('name'=>'gid', 'type'=>'ID', 'caption'=>'gid', 'control'=>'fkey', 'ftbl'=>'', 'fTitle'=>'', 'fID'=>'' , 'required'=>true);

    public $name=array('name'=>'name', 'type'=>'varchar', 'caption'=>'name', 'control'=>'fkey', 'ftbl'=>'', 'fTitle'=>'', 'fID'=>'' , 'required'=>true);

    public $father=array('name'=>'father', 'type'=>'varchar', 'caption'=>'father', 'control'=>'fkey', 'ftbl'=>'', 'fTitle'=>'', 'fID'=>'' );

    public $mother=array('name'=>'mother', 'type'=>'varchar', 'caption'=>'mother', 'control'=>'fkey', 'ftbl'=>'', 'fTitle'=>'', 'fID'=>'' );

    public $birth_date=array('name'=>'birth_date', 'type'=>'varchar', 'caption'=>'birth_date', 'control'=>'date', 'value'=>nowandate, 'format'=>'%Y-%m-%d %H:%M:%S', 'withtime'=>'true');

    public $birth_place=array('name'=>'birth_place', 'type'=>'varchar', 'caption'=>'birth_place', 'control'=>'fkey', 'ftbl'=>'', 'fTitle'=>'', 'fID'=>'' );

    public $address=array('name'=>'address', 'type'=>'text', 'caption'=>'address', 'control'=>'textarea' );

    public $rule=array('name'=>'rule', 'type'=>'varchar', 'caption'=>'rule', 'control'=>'text' );

    public $national=array('name'=>'national', 'type'=>'varchar', 'caption'=>'national', 'control'=>'fkey', 'ftbl'=>'', 'fTitle'=>'', 'fID'=>'' );

    public $ideal=array('name'=>'ideal', 'type'=>'varchar', 'caption'=>'ideal', 'control'=>'fkey', 'ftbl'=>'', 'fTitle'=>'', 'fID'=>'' );

    public $reserve=array('name'=>'reserve', 'type'=>'file', 'filetypes'=>'jpg', 'prefix'=>'PER', 'caption'=>'', 'control'=>'file', 'view'=>'image', 'resize'=>true, 'sizes'=>array('thumb'=>array('p'=>'B', 'w'=>0, 'h'=>126)));

    public $tblname="person";

}