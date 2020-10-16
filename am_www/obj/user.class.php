<?php
include_once '../cms/dataset.php';
include_once '../lang/pages_'.$GLOBALS['lang'].'.inc';


class User extends JSDataSet
{
    public $user_id=array('name'=>'user_id', 'type'=>'ID', 'caption'=>'user_id', 'control'=>'none');

    public $user_name=array('name'=>'user_name', 'type'=>'varchar', 'caption'=>'user_name', 'control'=>'text','istitle'=>true, 'required'=>'required');

    public $user_password=array('name'=>'user_password', 'type'=>'varchar', 'caption'=>'user_password', 'control'=>'none', 'value'=>'202cb962ac59075b964b07152d234b70' /*this is the hash of "123" as default password*/, 'required'=>'required' );

    public $user_email=array('name'=>'user_email', 'type'=>'varchar', 'caption'=>'user_email', 'control'=>'text' );

    public $user_phone=array('name'=>'user_phone', 'type'=>'varchar', 'caption'=>'user_phone', 'control'=>'text' );

    public $user_address=array('name'=>'user_address', 'type'=>'varchar', 'caption'=>'user_address', 'control'=>'text' );

    public $user_country=array('name'=>'user_country', 'type'=>'varchar', 'caption'=>'user_country', 'control'=>'text' );

    public $user_city=array('name'=>'user_city', 'type'=>'varchar', 'caption'=>'user_city', 'control'=>'text' );

    public $user_birthyear=array('name'=>'user_birthyear', 'type'=>'int', 'caption'=>'user_birthyear', 'control'=>'text' , 'value'=>'0');
    
    public $in_home=array('name'=>'in_home', 'type'=>'bool', 'caption'=>'in_home', 'control'=>'checkbox' , 'value'=>0);

    public $user_pic=array('name'=>'user_pic', 'type'=>'file', 'filetypes'=>'jpg|png|gif', 'prefix'=>'USER_', 'caption'=>'user_pic', 'control'=>'file', 'view'=>'image', 'resize'=>true, 'sizes'=>array('thumb'=>array('p'=>'B', 'w'=>180, 'h'=>126)) , 'ext'=>'');
   
    public $user_cat=array('name'=>'user_cat', 'type'=>'varchar', 'caption'=>'user_cat', 'control'=>'list', 'options'=>array('employee'=>'employee','distributor'=>'distributor','customer'=>'customer', 'admin'=>'admin'));
    
    public $NDate=array('name'=>'NDate', 'type'=>'varchar', 'caption'=>'NDate', 'control'=>'none', 'value'=>nowandate, 'format'=>'%Y-%m-%d %H:%M:%S', 'withtime'=>'true', 'NTitle'=>'');//this one does not exist in db but we use it to show name when deleting
    
    //public 

    public $tblname="user";

    public function onStart(){
    	$this->user_pic['ext'] = get_data_in("select user_pic from {$this->tblname} where user_id='{$this->user_id["value"]}'", "user_pic");
    	$this->NDate['NTitle']=$this->user_name['value'];
    	
    }///////////////////////////////////////////////
    
    public function onInsert($res){
    	 
    	include_once '../common/pframe.php';
    	$myframe=new pframe();
    	
    	// to make distributor manage only his own customers
    	if($myframe->Is_Distributor()){ 
    		$sql="insert into customers_of_dist values('{$_SESSION['GID']}', '{$this->user_id['value']}')";//echo $sql;
    		@cmd($sql);
    	}
    	// each user is added to one of the default groups according to his category
    	if($this->user_cat['value'] == 'customer'){$group = 'CUSTOMERS';}
    	elseif($this->user_cat['value'] == 'employee'){$group = 'EMPLOYEES';}
    	elseif($this->user_cat['value'] == 'distributor'){$group = 'DISTRIBUTORS';}
    	elseif($this->user_cat['value'] == 'admin'){$group = 'ADMINISTRATORS';}
    	$group_id=get_data_in("select group_id from groups where group_name = '{$group}' ", "group_id");
    	$sql="insert into user_groups values('{$this->user_id['value']}', '{$group_id}')";//echo $sql;
    	@cmd($sql);
    }///////////////////////////////////////////////
    
    public function onRemove($res){
    
    	include_once '../common/pframe.php';
    	$myframe=new pframe();
    	 
    	// when a user is deleted, it will be deleted from distributor custs too.
    		$sql="delete from customers_of_dist where cust_id = '{$this->user_id['value']}' ";//echo $sql;
    		@cmd($sql);
    }///////////////////////////////////////////////
}