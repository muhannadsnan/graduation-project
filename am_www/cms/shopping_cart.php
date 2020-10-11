<?php
require_once '../db/mysqlcon.php';
class ShoppingCart 
{	
	public $money=0;
	public $icont=0;

	////////////////////////////////////////////////////////////////////////////////////////////////
	//  Add new Item To Shopping Cart
	////////////////////////////////////////////////////////////////////////////////////////////////
	
	public function addItem($FID,$OID="")
	{
		if ($OID=="") $OID=$FID;
		global $_SESSION;
		
		if (!isset($_SESSION["gids"]))
		{
			$_SESSION["gids"]   = array();
			$_SESSION["counts"] = array();
			$_SESSION["options"] = array();
		}
		
		//check for current item in the current shopping cart content
		$i=0;
		while ($i<count($_SESSION["gids"]) && $_SESSION["gids"][$i] != $OID) 
			$i++;
		
		//increase current product's quantity
		if ($i < count($_SESSION["gids"])) 
		{	$_SESSION["counts"][$i]++;	}
		else //no item - add it to $gids array
		{
			$_SESSION["gids"]  [] = $OID;
			$_SESSION["counts"][] = 1;
			$_SESSION["options"][] = $FID;
		} 
	}
	
	////////////////////////////////////////////////////////////////////////////////////////////////
	// Remove Item From Shopping Cart
	////////////////////////////////////////////////////////////////////////////////////////////////
	
	public function removeItem($FID)
	{
		global $_SESSION;
		
		$i=0;
		while ($i<count($_SESSION["gids"]) && $_SESSION["gids"][$i] != $FID) 
			$i++;
		if ($i<count($_SESSION["gids"])) 
		{	$_SESSION["gids"][$i] = 0;	}
	}
	
	
	////////////////////////////////////////////////////////////////////////////////////////////////
	// Update Item Quantity in Shopping Cart
	////////////////////////////////////////////////////////////////////////////////////////////////
	
	public function updateItem()
	{
		global $_SESSION;
		global $_POST;
		
		// The Array Contains POST array
		foreach ($_POST as $key => $val)
			if (strstr($key, "count_"))
			{
				if ($val > 0)
				{
					for ($i=0; $i<count($_SESSION["gids"]); $i++)
					{
						if ($_SESSION["gids"][$i] == str_replace("count_","",$key))
						{	$_SESSION["counts"][$i] = floor($val);	}
					}
				}
				else //remove
				{
					$i=0;
					while ($_SESSION["gids"][$i] != str_replace("count_","",$key) && $i<count($_SESSION["gids"])) 
						$i++;
					$_SESSION["gids"][$i] = 0;
				}
			}
	}
	
	
	//////////////////////////////////////////////////////////////////////////////////////////////////
	// Make Shopping Cart Empty
	////////////////////////////////////////////////////////////////////////////////////////////////
	
	public function emptyBasket()
	{
		global $_SESSION;
		
		unset($_SESSION["gids"]);
		unset($_SESSION["counts"]);
		unset($_SESSION["options"]);
		
		$_SESSION["IMoney"] = 0;
		$_SESSION["ICount"] = 0;
	}
	
	////////////////////////////////////////////////////////////////////////////////////////////////
	//  Update Shopping Cart Info
	////////////////////////////////////////////////////////////////////////////////////////////////
	
	public function updateBasketInfo()
	{
		$_SESSION["IMoney"] = 0;
		$_SESSION["ICount"] = 0;
		for ($i=0; $i<count($_SESSION["gids"]); $i++)
		{
		  if ($_SESSION["gids"][$i])
		  {
			$t = table("SELECT op_price FROM t_options WHERE OPID='".$_SESSION["gids"][$i]."' and FID='".$_SESSION["options"][$i]."'");
			$row = mysql_fetch_array($t);
			$_SESSION["IMoney"] += $_SESSION["counts"][$i]*$row['op_price'];
			$_SESSION["ICount"] += $_SESSION["counts"][$i];
		  }
		}
	}
	
	////////////////////////////////////////////////////////////////////////////////////////////////
	//  Order Items
	////////////////////////////////////////////////////////////////////////////////////////////////
	
	public function addOrder()
	{
		global $_POST;
		global $_SESSION;
		
		$oid = uniqid();
		$sql = "insert into t_orders (OID,order_time, cust_firstname, cust_lastname, cust_zip, cust_address, cust_phone,order_done,cu_hname,cu_ccnom,cu_ctype,cu_expm,cu_expy,cu_scode) 
		values ('$oid','".$this->get_current_time()."','".$_POST["first_name"]."','".$_POST["last_name"]."','".$_POST["zip"]."','".$_POST["address"]."','".$_POST["phone"]."',0,'".$_POST["cu_hname"]."','".$_POST["cu_ccnom"]."','".$_POST["cu_ctype"]."','".$_POST["cu_expm"]."','".$_POST["cu_expy"]."','".$_POST["cu_scode"]."');";
		table($sql);

		for ($i=0; $i<count($_SESSION["gids"]); $i++)
		{
		  if ($_SESSION["gids"][$i])
		  {
			$sql = table("SELECT F.fo_name, O.op_price,O.op_name FROM t_foods F , t_options O WHERE O.OPID='".$_SESSION["gids"][$i]."' and O.FID = F.FID");
			$r = mysql_fetch_array($sql);
			// Put values
			$name  = $r['fo_name']."( ".$r['op_name']." )";
			$count = $_SESSION['counts'][$i];
			$price = $r['op_price'] * $count;
			$OPID  = $_SESSION["gids"][$i];
	
			cmd("insert into t_ordered_carts (OID,OPID,name,price,quantity) values ('$oid', '$OPID', '$name', '$price', '$count')");
		  }
		}
	}
	
	////////////////////////////////////////////////////////////////////////////////////////////////
	//  Get Current Time
	////////////////////////////////////////////////////////////////////////////////////////////////
	
	public function get_current_time() 
	{
		return strftime("%Y-%m-%d %H:%M:%S", time());
	}
}

?>
