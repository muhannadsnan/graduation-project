<?php
error_reporting(E_ALL);
ini_set('display_errors', 'on');
session_start();
//declare global variables
global $con;
global $MyErrStr;
global $wwwURL;
global $lang;


require_once '../db/settings.php';
$conA=new mysqli_connection_A();
$con=$conA->do_connect();

//MYSQL DB Functions________________________________________________________________________

function cmd($sql, $db="con"){

//cmd means 'COMMAND'. This function executes an sql query that does not have results to return
//if it was a deleting statement, return an appropriate message of deleting or query succeeding  or else an error message
 
	//echo $sql;
	if (mysqli_query($sql, $GLOBALS[$db])) {
		if (eregi("^delete", $sql)){
			return $GLOBALS['MyErrStr']->RowDeleted;
		}else {
			return $GLOBALS['MyErrStr']->DBOK;	
		}
	}else {
		return $GLOBALS['MyErrStr']->DBERR;
	}
}

function table($sql, $db="con"){

// executes an sql query and returns result rows as table 
	//echo $sql;
	$res=mysqli_query($sql, $GLOBALS[$db]);
	if ($res) {
		return $res;
	}else {
		return false;
	}	
}

function row($sql, $db="con"){

//returns a single result row according to an sql statement
	$res=mysqli_query($sql, $GLOBALS[$db]);
	if ($res) {
		return mysqli_fetch_array($res);
	}else {
		return false;
	}	
}

function get_data_in($search_sqlstatement,$dfname, $db="con"){

//returns a single data cell according to an sql statement
	$xres=mysqli_query( "$search_sqlstatement", $GLOBALS[$db]);
	if ($xres) {
		$xrow=mysqli_fetch_array($xres);
		$requierd_data=$xrow["$dfname"];
		return $requierd_data;
	}else {
		return false;	
	}
}





function get_month_name($mid)
{
//returns the month's name by it's order
	$maaa=array('January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December');
	return $maaa[$mid-1];
}

//Admin Functions___________________________________________________________________________

function showedit($lnk, $showTXT=false, $tttxt="Edit", $users=array("A")){//print_r($users);

//displays a tool to enable the user do edit operation, if he had full control permission "A" or the class's managing permission.
	if (!$showTXT) {$tttxt="";}
	if (user_has_permission($users)) {
		return  '<a class="edit_tool_ico" href="'.$lnk.'"><img style="" src="../images/edtimg.png" alt="Edit" /> <span>'.$tttxt.'</span></a>';
	}
	return false;
}

function showdelet($lnk, $showTXT=false, $tttxt="Delete", $users=array("A")){//print_r($users);

//displays a tool to enable the user do delete operation, if he had full control permission "A" or the class's managing permission.
	if (!$showTXT) {$tttxt="";}
	if (user_has_permission($users)) {
		return  '<a  class="edit_tool_ico" href="'.$lnk.'"><img style="" src="../images/dltimg.png" alt="Delete" /> <span>'.$tttxt.'</span></a>';
	}
	return false;
}

function get_pms()
{
//collects the GET parameters and join them as a single string var
	foreach ($_GET as $pmk => $pmv) {
		if ($pmk!="v" && $pmk!="lang" && $pmk!="NID" && $pmk!="noframe"){
		$strpms[]=$pmk."=".$pmv;	
		}
	}
	$strpms=@join("&",$strpms);
	return $strpms;
}
////////USER PERMISSION//////////////////////////////////////
function user_has_permission($users_types){

//this is the most important function in terms of permissions.
//it checkes whether the user is logged in, and has one or more permissions in his session array variable of privileges

	if (in_array("N", $users_types)) {
			return true;
	}

	if (session_is_registered("GID") && $_SESSION["PRIVS"]) {
		foreach ($_SESSION["PRIVS"] as $priv)
			if (in_array($priv, $users_types)) {
				return true;
		}
	}
	return false;
}



function IS_SECURE($priv=""){

//and this is the second most important one. If makes sure that the user is logged in and has enough permissions to continue on this page, and priv "A" (full control) is checked also. However, if the user does not have and, we force to leave the page and redirect him to the signin page
	$p=array();
	$p[0]="A";
	$p[1]=$priv;
	if (!user_has_permission($p)){@header("location:../common/signin.php?soro=x"); exit();}
}

//Error Alerts__________________________________________________________________________________
$MyErrStr=new ErrStr();
class ErrStr
{
//these variables are very clear and can explain every thing about this class to know it's contents
//the error messages returnd by db and others, will find a match here and will be displayed as a formatted message div that has CSS class to modify
	public $CannotMasking=-10;
	public $NotVerified=-9;
	public $CannotResize=-8;
	public $ErrFileType=-7;
	public $CannotUpload=-6;
	public $ReRegitered=-5;
	public $CancelRegister=-4;
	public $RegisteredOK=-3;
	public $RowDeleted=-2;
	public $DBOK=-1;
	public $DBERR=0;
	public $FillAllRequierd=1;
	public $FillAll=2;
	public $DataIsExist=3;
	public $IsNotRegistered=4;	
	public $Uploded=5;
	public $Commented=6;
	public $InvalidTel=7;
	public $InvalidFax=8;
	public $InvalidMobile=9;
	public $InvalidMail=10;
	
	function Show($ErrStrHandle){
		//Use these two varibles to format message
		$strstyle='<div class="err_msg">';
		$strendstyle="</div>";
		$exc='<img class="err_msg_icon" src="../images/exs.gif" />';
		
		switch ($ErrStrHandle) {
			case -10:
				return $strstyle.$exc.CannotMasking.$strendstyle;
				break;
			case -9:
				return $strstyle.$exc.NotVerified.$strendstyle;
				break;
			case -8:
				return $strstyle.$exc.CannotResize.$strendstyle;
				break;
			case -7:
				return $strstyle.$exc.ErrFileType.$strendstyle;
				break;
			case -6:
				return $strstyle.$exc.CannotUpload.$strendstyle;
				break;
			case -5:
				return $strstyle.$exc.ReRegistered.$strendstyle;
				break;
			case -4:
				return $strstyle.$exc.CancelRegister.$strendstyle;
				break;
			case -3:
				return $strstyle.$exc.RegisteredOK.$strendstyle;
				break;
			case -2:
				return $strstyle.$exc.RowDeleted.$strendstyle;
				break;
			case -1:
				return $strstyle.$exc.DBOK.$strendstyle;
				break;
			case 0:
				return $strstyle.$exc.DBERR.$strendstyle;
				break;
			case 1:
				return $strstyle.$exc.FillAllRequierd.$strendstyle;
				break;
			case 2:
				return $strstyle.$exc.FillAll.$strendstyle;
				break;
			case 3:
				return $strstyle.$exc.DataIsExist.$strendstyle;
				break;
			case 4:
				return $strstyle.$exc.IsNotRegistered.$strendstyle;
				break;	
			case 5:
				return $strstyle.$exc.Uploded.$strendstyle;
				break;
			case 6:
				return $strstyle.$exc.Commented.$strendstyle;
				break;		
			case 7:
				return $strstyle.$exc.InvalidTel.$strendstyle;
				break;
			case 8:
				return $strstyle.$exc.InvalidMob.$strendstyle;
				break;
			case 9:
				return $strstyle.$exc.InvalidFax.$strendstyle;
				break;
			case 10:
				return $strstyle.$exc.InvalidMail.$strendstyle;
				break;		
			default:
				return false;
				break;
		}
	}
}


?>