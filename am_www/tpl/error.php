<? 
// this is the error page where the user is redirected if he has done an illegal access, like copying a // link that he does not have permission to, or change a parameter (buy contract) he's not a part of..



include_once '../common/pframe.php';
$myfrm =new pframe();
$ttl= ($myfrm->illegal_attempts() > 10)? YOU_ARE_BLOCKED : ERROR_PAGE;
?>
<html>
<header>
	<meta http-equiv="content-type" content="text/html; charset=UTF-8" />
	<link href="../common/main_<?=$GLOBALS['lang']?>.css" rel="stylesheet" type="text/css" media="screen" />
	<link rel="icon" href="../images/error_ico.png" type="image/x-icon" />
	<title><?=$ttl?></title>
</header>
<body class="error_page">

	<div class="message">
		<? echo PROJECT_TITLE ."<br/><br/>".ERROR_PAGE."<br/><br/>";?>
		<? echo constant($_REQUEST['reason']) ."</br>";?>
	</div>

// then the user clicks the back button to go back

// but if he reaches a limit of illegal actions, the back button disappears and the will be redirected // every time he access the site (this means the client ip is blocked)

//illegal actions are two kinds : copy link & failed login over 10 times in 3 day window

	<? if($myfrm->illegal_attempts() > 10 || $myfrm->illegal_attempts('link_copy') > 10){}else{?>
		<a class="back_button"  onClick="history.back();return false;"><?=Back?></a>
	<? }?>
</body>
</html>