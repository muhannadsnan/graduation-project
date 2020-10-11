<?php
session_start();
global $con;
global $MyErrStr;
global $wwwURL;
global $lang;
 
require_once '../db/settings.php';
$conA=new mysql_connection_A();
$con=$conA->do_connect();

$cc_arr=array('','#005ba0','#2ba25b','#da251c');
$cc_arrC=array('blue','blue','green','red');
		
	
//MYSQL DB Functions___________________________________________________________________________________________________________________________________________________

function cmd($sql, $db="con"){
	//echo $sql;
	if (mysql_query($sql, $GLOBALS[$db])) {
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
	//echo $sql;
	$res=mysql_query($sql, $GLOBALS[$db]);
	if ($res) {
		return $res;
	}else {
		return false;
	}	
}

function row($sql, $db="con"){
	$res=mysql_query($sql, $GLOBALS[$db]);
	if ($res) {
		return mysql_fetch_array($res);
	}else {
		return false;
	}	
}

function get_data_in($search_sqlstatement,$dfname, $db="con"){
	$xres=mysql_query( "$search_sqlstatement", $GLOBALS[$db]);
	if ($xres) {
		$xrow=mysql_fetch_array($xres);
		$requierd_data=$xrow["$dfname"];
		return $requierd_data;
	}else {
		return false;	
	}
}

function data_is_exists($search_sqlstatement, $db="con"){
	//echo $search_sqlstatement;
	$inquireres=mysql_query("$search_sqlstatement", $GLOBALS[$db]);

	/*if (mysql_num_rows($inquireres)>0) { return true;}
    else {	return false;}*/
}

function get_month_name($mid)
{
	$maaa=array('January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December');
	return $maaa[$mid-1];
}

function Reverse_date($date, $speartor="-"){
	$dt=explode($speartor, strval($date));
	$dt=array_reverse($dt);
	$dt=join("-", $dt);
	return $dt;
	//return $date;
}

//CP Functions_________________________________________________________________________________________________________________________________________________________

function showedit($lnk, $showTXT=false, $tttxt="Edit", $users=array("A")){
	if (!$showTXT) {$tttxt="";}
	if (user_has_permission($users)) {
		return  '<a class="edit_tool" href="'.$lnk.'"><img style="border:0px" src="../images/edtimg.png" alt="Edit" /> <span>'.$tttxt.'</span></a>';
	}
	return false;
}

function showdelet($lnk, $showTXT=false, $tttxt="Delete", $users=array("A")){
	if (!$showTXT) {$tttxt="";}
	if (user_has_permission($users)) {
		return  '<a  class="edit_tool" href="'.$lnk.'"><img style="border:0px" src="../images/dltimg.png" alt="Delete" /> <span>'.$tttxt.'</span></a>';
	}
	return false;
}

function showview_details($lnk, $showTXT=false, $tttxt="View", $users=array("A"), $showicon=true, $icon="", $lnkTarget="", $extra_class=""){
	if (!$showTXT) {$tttxt="";}
	if($icon =="") 
		$icon="../images/vewdt.gif";
	if ($showicon) {	
		$micon='<img style="border:0px" src="'.$icon.'" alt="- " />';
	}
	if (user_has_permission($users)) {
		return  '<a  class="edit_tool '.$extra_class.'" href="'.$lnk.'" target="'.$lnkTarget.'">'.$micon.' <span>'.$tttxt.'</span></a>';
	}
	return false;
}

function showview_admin_details($lnk, $editLink, $deleteLink, $showTXT=false, $tttxt="View", $users=array("A"), $showicon=true, $icon="", $lnkTarget=""){
	if (!$showTXT) {$tttxt="";}
	if($icon =="") 
		$icon="../images/vewdt.gif";
	if ($showicon) {	
		$micon='<img style="border:0px" src="'.$icon.'" alt="View Details" />';
	}
	if (user_has_permission($users)) {
		return  '<a  class="edit_tool" href="'.$lnk.'" target="'.$lnkTarget.'">'.$micon.' '.'</a>'.$deleteLink.' '.$editLink.'<a  class="edit_tool" href="'.$lnk.'" target="'.$lnkTarget.'">'.$tttxt.'</a>';
	}
	return false;
}

function showview_list($lnk, $showTXT=false, $tttxt="View", $users=array("A"), $showicon=true){
	if (!$showTXT) {$tttxt="";}
	if ($showicon) {
		$micon='<img style="border:0px" src="../images/vewlst.png" alt="View List" />';
	}
	if (user_has_permission($users)) {
		return  '<a  class="edit_tool" href="'.$lnk.'">'.$micon.' '.$tttxt.'</a>';
	}
	return false;
}

function show_views_nom($Nom=0, $users=array("N")){
	if (user_has_permission($users)) {
		return '<span>Views: </span><span>'.$Nom.'</span>'; 
	}
	return false;
}

function gen_str($str, $arr)
{
	//if (!is_array($arr)) return;
	foreach ($arr as $k=>$v) {
		$str=str_ireplace('{'.$k.'}', $v, $str);
	}
	return $str;
}

function get_pms()
{
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
	if (in_array("N", $users_types)) {
			return true;
	}
	if (session_is_registered("UID")) {
		if (in_array($_SESSION["UTP"], $users_types)) {
			return true;
		}
	}
	return false;
}

function IS_LOGGED_IN(){
	if (!user_has_permission(array("A","B"))){@header("location:../common/signin.php?soro=x"); exit();}
}

function RemCrLf($subject,$delimiter="")
{
	$newstring = preg_replace("/[\n\r]/",$delimiter,$subject);
	$newstring = str_ireplace("{$delimiter}{$delimiter}","{$delimiter}",$newstring);
	return $newstring;
}

function get_script_file_name(){
	$str=$_SERVER['PHP_SELF'];
	$arr=array_reverse(explode('/',$str));
	return $arr[0];
}

function AXFlash($id, $src, $width, $height, $alt="", $attr="", $stretch=false, $bg="transparent", $wmode="transparent"){
	if(!$GLOBALS['$AXFLASHCHECER_LOADED']){
		?><script src="../cms/flashchecker.js" language="javascript"></script><?php
		$GLOBALS['AXFLASHCHECKR_LOADED']=true;
	}
	
	?>
	<script language="JavaScript" type="text/javascript">
	var requiredMajorVersion = 10;
	var requiredMinorVersion = 0;
	var requiredRevision = 0;
	
	var hasReqestedVersion = DetectFlashVer(requiredMajorVersion, requiredMinorVersion, requiredRevision);
	if (hasReqestedVersion) {
		document.getElementById("<?=$id?>").innerHTML = '<embed src="<?=$src?>" wmode="<?=$wmode?>" quality="high" bgcolor="<?=$bg?>" width="<?=$width?>" height="<?=$height?>" name="ad-banner1" align="middle" allowScriptAccess="always" type="application/x-shockwave-flash" pluginspage="http://www.macromedia.com/go/getflashplayer" style="float: middle" <?php if ($stretch) echo 'scale="ExactFit"' ?> <?=$attr?> />';
	}
	else {
		<?php if ($alt!=""){ ?>
		document.getElementById("<?=$id?>").innerHTML = '<?=$alt?>';
		<?php } ?>
	}
	</script>
	<?php	
}
//NDT_______________________________________________________________________________________________________________

class NDT
{
	static function BlogName($id)
	{
		$res=get_data_in("select BName from Btbl where BID = '{$id}'","BName");
		return $res;
	}
}

//Error Alerts_______________________________________________________________________________________________________________________________________

$MyErrStr=new ErrStr();
class ErrStr
{
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
		//استخدم هذين المتغيرين لتحديد تنسيق الرسالة
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

//AJAX__________________________________________________________________________________________________________________________________________________________________

function decode_unicode_url($str)
{
  $res = '';

  $i = 0;
  $max = strlen($str) - 6;
  while ($i <= $max)
  {
    $character = $str[$i];
    if ($character == '%' && $str[$i + 1] == 'u')
    {
      $value = hexdec(substr($str, $i + 2, 4));
      $i += 6;

      if ($value < 0x0080) // 1 byte: 0xxxxxxx
      {$character = chr($value);}
      else if ($value < 0x0800) // 2 bytes: 110xxxxx 10xxxxxx
      {$character =
            chr((($value & 0x07c0) >> 6) | 0xc0)
          . chr(($value & 0x3f) | 0x80);}
      else // 3 bytes: 1110xxxx 10xxxxxx 10xxxxxx
      {$character =
            chr((($value & 0xf000) >> 12) | 0xe0)
          . chr((($value & 0x0fc0) >> 6) | 0x80)
          . chr(($value & 0x3f) | 0x80);}
    }
    else
      $i++;

    $res .= $character;
  }

  return $res . substr($str, $i);
}

function url_pms($exclude_arr){
	foreach ($_GET as $pmk => $pmv) {
		if (!in_array($pmk, $exclude_arr)){
		$strpms[]=$pmk."=".urlencode($pmv);	
		}
	}
	$strpms=@join("&",$strpms);
	return ($strpms);
}

class MT
{
	public static function get_BCountry($BCID)
	{
		$rval=get_data_in("select CName from bcountries where BCID = '{$BCID}'","CName");
		return $rval;
	}
	
	public static function get_BCountry_BID($BID)
	{
		$rval=get_data_in("select bcountries.CName as CName from brands inner join bcountries on brands.BCID=bcountries.BCID where brands.BID = '{$BID}'","CName");
		return $rval;
	}
	
	public static function get_Flag($BCID)
	{
		include_once '../obj/bcountry.class.php';
		$myFlag=new BCountry($BCID);
		return $myFlag->Draw_Photo($myFlag->BCPic,"thumb");
	}
	
	public static function get_Flag_Path($BCID)
	{
		include_once '../obj/bcountry.class.php';
		$myFlag=new BCountry($BCID);
		return $myFlag->get_file_path($myFlag->BCPic,"thumb");
	}
	
}
?>