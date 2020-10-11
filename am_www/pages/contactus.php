<?php
include_once '../common/pframe.php';
include_once '../obj/contactus.class.php';
$GLOBALS['mnuf']="contacts";
$btn_img='<img src="../images/5_'.$GLOBALS['lang'].'.png" jw="34" jh="26" class="zoom hd_ico" />';
//______________________________________________________________________________________________________________________________
$myframe=new pframe();
$myframe->header(ContactUs);

$myMSG=new MSG($_REQUEST['NID']);

switch ($_REQUEST['v'])
{
//EDITOR________________________________________________________________________________________________________________________
case "e":
	
if ($myMSG->NID['IsNew']){
	$ttl=ContactUs;
	$myMSG->MText['value']=$_REQUEST['pre']."\r\n";
}else {
	/***/IS_SECURE();/***/
	$ttl=Edit_MSG;
}

$myframe->open_box($btn_img . $ttl,"panel");

$NText=get_data_in("select NText from news where NID='contactus_{$GLOBALS['lang']}'", "NText");

//Add one more view
echo '<div class="info_div card_sum" style="background:none;">'.nl2br($NText).'</div>';
echo '<div style="text-align:'.r_align.'">'.showedit("../pages/news.php?v=e&lang=".$GLOBALS['lang']."&NID=contactus_{$GLOBALS['lang']}&NType=1", true,Edit)."</div>";
?>
<div><img src='../images/www.png' style="vertical-align:middle;" /> <a class="edit_tool wbx" href="../pages/location.php?lang=<?=$GLOBALS['lang']?>"><?=Location?></a></div>
<div style="border-bottom:1px #CFCFCF solid;">&nbsp;</div>
<p style="padding:15px 0px;"><?=You_can_contact_us_directly_by_filling_this_application?></p>
<?php

$myMSG->DisplayEditor("_n",3,Send,"../pages/contactus.php");

$myframe->close_box("panel");
break;
//TABLE________________________________________________________________________________________________________________________
case "t":
/***/IS_SECURE();/***/
$myframe->open_box($btn_img . View_MSG,"panel");
$wherestr="where lang like '{$GLOBALS['lang']}' order by NDate DESC";
$showfields=array($myMSG->NTitle['name']);
$myMSG->DisplayTable($showfields, "select * from {$myMSG->tblname} {$wherestr}", true, $wherestr, array("../pages/contactus.php?lang={$GLOBALS['lang']}&v=c"), array("NID"), array("@Name"));
$myframe->close_box("panel");
break;
//DELETE________________________________________________________________________________________________________________________
case "d":
	/***/IS_SECURE();/***/
if ($myMSG->NID['IsNew'])  break;
$myframe->open_box($btn_img . Del_MSG,"panel");
$myMSG->DisplayDelMsg();
$myframe->close_box("panel");
break;	
//Viewer________________________________________________________________________________________________________________________
case "c":
if ($myMSG->NID['IsNew'])  break;
$myframe->open_box($btn_img . $myMSG->NTitle['value'],"panel");
$myMSG->ListView();
echo '<p align="left">'.showview_details($_SERVER['PHP_SELF']."?lang={$GLOBALS['lang']}&v=e&NID={$myMSG->NID['value']}", true, Edit_MSG, array("A")).'</p>';
$myframe->close_box("panel");
break;
}
//RELATED PAGES__________________________________________________________________________________________________________________
if (user_has_permission(array("A"))){
$myframe->open_box($btn_img . Related_Pages,"panel");
echo "<div>";
echo '<p>'.showview_details($_SERVER['PHP_SELF']."?lang={$GLOBALS['lang']}&v=e&NID=new", true, Add_MSG, array("A")).'</p>';
//echo '<p>'.showview_details("../pages/depts.php?lang={$GLOBALS['lang']}&v=t&NID=new", true, View_Dept, array("A")).'</p>';
echo '<p>'.showview_details($_SERVER['PHP_SELF']."?lang={$GLOBALS['lang']}&v=t&NID=new", true, View_MSG, array("A")).'</p>';
echo "</div>";
$myframe->close_box("panel");
}
//______________________________________________________________________________________________________________________________
$myframe->footer();

?>