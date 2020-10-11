<?php
include_once '../common/pframe.php';
include_once '../cms/dataset.php';
include_once '../lang/lang_'.$GLOBALS['lang'].'.inc';
include_once '../cms/navigator.php';

class VComment extends JSDataSet  
{
	public $VID=array('name'=>'VID', 'type'=>'ID', 'caption'=>'NID', 'control'=>'none');
	public $vNID=array('name'=>'vNID', 'type'=>'varchar', 'caption'=>'vNID', 'control'=>'none');
	public $Name=array('name'=>'Name', 'type'=>'varchar', 'caption'=>'Visitor_Name', 'control'=>'text', 'required'=>true);
	public $EMail=array('name'=>'EMail', 'type'=>'varchar', 'caption'=>'Visitor_EMail', 'control'=>'text', 'required'=>true, 'validate'=>'Email');
	public $Comment=array('name'=>'Comment', 'type'=>'varchar', 'caption'=>'VComment_Text', 'control'=>'textarea', 'required'=>true);
	public $FromIP=array('name'=>'FromIP', 'type'=>'varchar', 'caption'=>'FromIP', 'control'=>'none');
	public $NTable=array('name'=>'NTable', 'type'=>'varchar', 'caption'=>'NTable', 'control'=>'none');
	
	public $NHidden=array('name'=>'NHidden', 'type'=>'bool', 'caption'=>'Hidden', 'control'=>'none', 'required'=>true, 'value'=>1);
	public $NDate=array('name'=>'NDate', 'type'=>'datetime', 'caption'=>'Last_Update', 'control'=>'none', 'value'=>nowandate, 'format'=>'%Y-%m-%d %H:%M:%S', 'withtime'=>'true');
		
	public $tblname="vcomments";
	
	function onUserInsertedRow($res, &$ShowForm, &$NID)
	{
		if ($res==$GLOBALS['MyErrStr']->DBOK){
			echo $GLOBALS['MyErrStr']->Show($GLOBALS['MyErrStr']->Commented);
			$ShowForm=false;
			$NID="new";
		}else {
			echo $GLOBALS['MyErrStr']->Show($res);
			$ShowForm=true;
		}
		
		$name=$this->Name['value'];
		$subj="تنبيه قام احد الزوار بالتعليق على الموقع";
		
		$email=$this->EMail['value'];
		$sendTo = "info@aqar21.com";
		$subject = strip_tags(trim($subj));
		$headers = "From: <" . trim($email).">\r\n";
		$headers .= "Reply-To: " . trim($email) . "\r\n";
		$headers .= "Return-path: " . trim($email);
		$headers .= "\r\n";
  		$headers .= "MIME-Version: 1.0\r\n";
  		$headers .= "Content-type: text/plain; charset=utf-8\r\n";
  		//$headers .= "Content-Transfer-Encoding: quoted-printable\r\n";
		$message = "الاسم : ".$name."\r\n";
		$message .= "Email: ".$email."\r\n";		
		$message .= "http://daralfath.com/pages/{$this->NTable['value']}.php?NID={$this->vNID['value']}&v=c";
		if (@mail($sendTo, $subject, $message, $headers)) {
			$name="";
			$email="";
			$message="";
			$ErrorMsg = Thanks_Your_email_was_sent;			
		}else {
			$ErrorMsg = Sorry_Please_try_again_later;
		}
	}
	
}//إبراهيم خليل
	foreach ($_GET as $pmk => $pmv) {
		if ($pmk!="lang" && $pmk!="NID"){
		$strpms[]=$pmk."=".$pmv;	
		}
	}
	$strpms=@join("&",$strpms);
$myframe=new pframe();
if ($_REQUEST['v']!="e"){$myframe->header("تعليقات الزوار");}

$myVComment=new VComment($_GET['NID']);

switch ($_GET['v'])
{
case "e":
	echo '<html class="comment_editor">
			<head>
			<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
			<link href="../common/main_'.$GLOBALS['lang'].'.css" rel="stylesheet" type="text/css" />
			</head>
			<body  class="comment_editor">';
	//if ($myVComment->NID['IsNew']) {
	$myVComment->vNID['value']=$_GET['vnid'];
	$myVComment->FromIP['value']=$_SERVER['SERVER_ADDR'];
	$myVComment->NTable['value']=$_GET['ntbl'];
	$myVComment->NDate['value']=nowandate;
	$myVComment->NHidden['value']=1;
	
	$myVComment->DisplayEditor("_n", 3, Send);

	echo '</body>
		</html>';
break;
case "t":

break;
case "d":
if ($_GET['NID']=="new")  break;
$myframe->open_box(Delete,"panel");
if (user_has_permission(array("A"))) {//Must be :A
	if ($_GET['v']=='d'){
		if ($_POST['delit']==yes) {
			echo $GLOBALS['MyErrStr']->show($myVComment->RemoveRow());	
			echo '<p>'.showview_details(urldecode($_GET['prev']), true, Back, array("N")).'</p>';	
		}else {
			echo '<form class="del_form" action="'.$_SERVER['PHP_SELF'].'?lang='.$GLOBALS['lang'].'&NID='.$myVComment->VID['value'].'&v=d&prev='.urlencode($_GET['prev']).'" method="POST">';
			
			$mystr="<span style='color:red'>".$myVComment->Comment['value']."</span>";
			printf(v_delete_question, $mystr);
			echo '<p style="text-align:'.r_align.'"><input type="submit" value="'.yes.'" style="cursor:pointer;width:50px" name="delit" id="doit" />&nbsp;&nbsp;&nbsp<input type="button" style="width:50px; cursor:pointer" value="'.no.'" name="doit" id="doit" onclick="window.location=\''.urldecode($_GET["prev"]).'\';" /></p>';
			echo '</form>';
		}
	}
}$myframe->close_box("panel");
break;
case "h":
if ($_GET['NID']=="new")  break;
$myframe->open_box(Hide,"panel");
if (user_has_permission(array("A"))) {//Must be :A
	if ($_GET['v']=='d'){
		if ($_POST['delit']==yes) {
			echo $GLOBALS['MyErrStr']->show($myVComment->RemoveRow());	
			echo '<p>'.showview_details(urldecode($_GET['prev']), true, Back, array("N")).'</p>';	
		}else {
			echo '<form class="del_form" action="'.$_SERVER['PHP_SELF'].'?lang='.$GLOBALS['lang'].'&NID='.$myVComment->VID['value'].'&v=d&prev='.urlencode($_GET['prev']).'" method="POST">';
			
			$mystr="<span style='color:red'>".$myVComment->Comment['value']."</span>";
			printf(v_delete_question, $mystr);
			echo '<p style="text-align:'.r_align.'"><input type="submit" value="'.yes.'" style="cursor:pointer;width:50px" name="delit" id="doit" />&nbsp;&nbsp;&nbsp<input type="button" style="width:50px; cursor:pointer" value="'.no.'" name="doit" id="doit" onclick="window.location=\''.urldecode($_GET["prev"]).'\';" /></p>';
			echo '</form>';
		}
	}
}$myframe->close_box("panel");
break;
}

if ($_REQUEST['v']!="e"){$myframe->footer();}

?>