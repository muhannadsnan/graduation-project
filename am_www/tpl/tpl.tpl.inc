<?php 
session_start();
function tpl_header($title="",$mnu=false,$rel=array()) {
	
	//in some cases you need to be redirected to the previous page so we put it in a SESSION[prev]
	// don't change when refreshing = don't change when we are on same page

	if($_SESSION['cur_params'] != $_SERVER['QUERY_STRING'] || 
                                            $_SESSION['cur_self'] != $_SERVER['PHP_SELF']) 
	{
       $_SESSION['prev_self'] = $_SESSION['cur_self']; 
       $_SESSION['prev_params'] = $_SESSION['cur_params']; }
	$_SESSION['cur_params'] = $_SERVER['QUERY_STRING']; 
	$_SESSION['cur_self'] = $_SERVER['PHP_SELF'];
	
	// Has visitor been counted in this session?	If not, increase counter value by one
	if(!isset($_SESSION['hasVisited'])){
		$_SESSION['hasVisited']=true;
		@cmd("update tcount set countid=countid+1");
	}
	
	if(basename($_SERVER['PHP_SELF']) != 'search.php') unset($_SESSION['search_result']);

	include_once '../common/pframe.php';
	$myfrm =new pframe();
//if this IP address has too many failed login attempts 
// OR copys links he doesn't has access to, will be blocked
	if($myfrm->illegal_attempts('failed_login') > 10 ){	@header("location:../tpl/error.php?reason=failed_login");	}

	elseif($myfrm->illegal_attempts('link_copy') > 10){	@header("location:../tpl/error.php?reason=link_copy");	}
?>

// now the starting HTML TAGS
// with the including of the SCRIPT files and other kinds of files


<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">

<head>
	<meta http-equiv="content-type" content="text/html; charset=UTF-8" />
	<meta name="author" content="muhannad snan" />
	<meta name="viewport" content="width=device-width" />
	<link href="../common/main_<?=$GLOBALS['lang']?>.css" rel="stylesheet" type="text/css" media="screen" />
	<link rel="icon" href="../documents/thumbs/SSOCIAL_<? $a=$myfrm->SiteConfig("fav_ico");echo$a[0]['NID']?>.<?=$a[0]['pic_social']?>" type="image/x-icon" />	    
    
    <script type="text/javascript" src="../cms/jquery-1.5.1.min.js"></script>
    <script type="text/javascript" src="../cms/datagrid.js"></script>
    <script type="text/javascript" src="../cms/jquery.fancybox-1.3.1.pack.js"></script>	
    <script type="text/javascript" src="../cms/jquery-latest.min.js"></script>
    <script type="text/javascript" src="../cms/jquery-1.10.1.min.js"></script>
    <script type="text/javascript" src="../cms/jquery-1.9.0.js"></script>
    <script type="text/javascript" src="../cms/jquery-2.1.3.min.js"></script>
    <script type="text/javascript" src="../cms/sorttable.js"></script>
    <script type="text/javascript" src="../cms/CollapsibleLists.js"></script>    
    
	<title><?=$title?></title>
	<? Bring_Configs_From_DB();?>
</head>

<body>

<div id="header_bar"><!------------------ HEADER_BAR >>>--------------------------------------------> 

		<a href="#mypanel" class="hide_sidebar" id="hider"  onclick=""><img src="../images/hider-button.png" alt="" title="this will be displayed as a tooltip"/></a>
		<? 		
		/*************************   List Panel   **************************/
		MyPanel();
		
		/*************************   Header Bar BUTTONS   **************************/
		DisplayHeaderBarButtons();
		?>
</div><!------------------------------------- HEADER_BAR <<<------------------------------------------> 

<div id="content_sidebar"><!----------------- CONTENT_SIDEBAR >>>------------------------------------->
 
    <div class="side_bar">
   		<? 
   		/************************   SEARCHING   ***************************/
		SEARCHING();
    	
    	/************************   Side Bar BUTTONS   ***************************/
    	DisplaySideBarButtons(); 
    	?>
    	
    	<div class="btn_div" id="mail_list">
			<div class="side_bar_blk">
				<? /**************   Mail List   ***************/
				Mail_List();
				?>
			</div>
        </div>
    </div>
    
    <?
    /*************************   Advertisements   **************************/
    Display_Ads();    
    ?>    
  	<?if(!$_SESSION['GID']){?>
    	<a href="../common/signin.php?lang=<?=$GLOBALS['lang']?>" id="signin" <?=$style?>>
    	<img src="../images/signin.png" style=""/> <span><?=Sign_in?></span> </a><br/>
    <?}?>
	
</div><!------------------------ CONTENT_SIDEBAR <<<-------------------------------------------------->	
<div id="main_content"><?
} ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

function tpl_footer(){//____________________________________________________________________________________________________________________
	include_once '../common/js.php';
	include_once '../common/pframe.php';
	$myfrm =new pframe();	
	$arr = $myfrm->SiteConfig("social_media");  $i=0;
	?>
	<div class="footer">
		<? /*********************   Visits Counter   *******************/?>
		<? $myfrm->VisitsCounter();?>
		
		<div class="social_media"> <? 
           /*********************   Social Media   *******************/?>
			<?
			foreach($arr as $key => $val){
				echo "";
				?><a href="<?=$arr[$i]['link']?>" >
   <img src="../documents/thumbs/BSOCIAL_<?=$arr[$i]['NID']?>.<?=$arr[$i]['pic_social']?>" /> </a><? 
				$i++;
			}
			?>
		</div>
		<div class="fcontent">
			<span><?=footer1?></span>
			<p><?=footer2?></p>
		</div>
	</div></div>
	</body></html><?
}////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

function MyPanel() 
// this is the panel that appears when we click on the panel button in the corner
{
	include_once '../common/pframe.php';
	$myfrm =new pframe();
	?>
	<div id="mypanel" class="hidden">

// link buttons of the panel

		<!-- <a href="../common/index.php?lang=<?=$GLOBALS['lang']?>" ><?=Home?></a> -->
    		<a href="../pages/products.php?v=t&lang=<?=$GLOBALS['lang']?>" ><?=Products?></a>
    		<a href="../pages/news.php?v=t&lang=<?=$GLOBALS['lang']?>" ><?=News?></a>
    		<a href="../pages/services.php?v=t&lang=<?=$GLOBALS['lang']?>"><?=Our_Services?></a>
		<a href="../pages/our_customers.php?v=t&lang=<?=$GLOBALS['lang']?>"><?=Our_Customers?></a>
		<a href="../pages/download.php?v=t&lang=<?=$GLOBALS['lang']?>"><?=Download?></a>
	<?

// logged in users have different buttons according to privs they have

	 if (session_is_registered("GID")) {
	 	?><a href="../pages/shared_pool.php?v=t&lang=<?=$GLOBALS['lang']?>&path="><img src="../images/icons/shared.png" style="float:left;margin-left:10px;margin-top:5px"/><?=Shared_Pool?></a><? 
	 	if($myfrm->Is_Customer()){
	 		?> <a class="" href="../pages/bconts.php?lang=<?=$GLOBALS['lang']?>&v=t"><?=BConts_Management?></a> <?
	 		?> <a class="" href="../pages/mconts.php?lang=<?=$GLOBALS['lang']?>&v=t"><?=MConts_Management?></a> <?
	 	}else{
	 		?><p class="sep">_________________</p><? 
			?><a href="../common/control_panel.php?lang=<?=$GLOBALS['lang']?>" id="signin"><img src="../images/cp.png" style="float:left;margin-left:10px;"/><?=Control_Panel?></a>
		<? }?>
		<a href="../cms/signout.php" id="signout" ><img src="../images/signout.png" style="float:left;margin-left:10px;"/><?=Sign_Out?></a>
		<?					
	 }else{ 
		?>		
		<a href="../pages/albums.php?v=t&lang=<?=$GLOBALS['lang']?>"><?=Albums?></a>
		<a href="../pages/jobs.php?v=t&lang=<?=$GLOBALS['lang']?>"><?=Jobs?></a>
		<a href="../pages/faq.php?v=t&lang=<?=$GLOBALS['lang']?>"><?=FAQ?></a>
		<a href="../pages/contactus.php?v=about&lang=<?=$GLOBALS['lang']?>"><?=About?></a>
		<? 				 
	}?>
	</div>

// script of SHOW\HIDE event when clicking the panel button

	<script type=text/javascript> //joggle hider button
	$('#hider').click(function(e){ 
	    e.stopPropagation();
	    //$('#mypanel').slideToggle();
	    $('#mypanel').attr('class', $('#mypanel').attr('class') == 'hidden' ? 'shown' : 'hidden');				    
	    $('body').animate({scrollTop: 0});
	    $('#header_bar').animate({scrollTop: 0});				    
	});
	$(document).click(function(){
	     //$('#mypanel').slideUp();
	     $('#mypanel').attr('class', 'hidden');
	});
	</script><? 
}////////////////////////////////////////////////////////////////////////////////////////

function DisplayHeaderBarButtons()

// HEADER BAR
{	
	include_once '../common/pframe.php';
	$myfrm =new pframe();
	
	$elem_loggedin="";
	if(session_is_registered("GID")){		$elem_loggedin="button_loggedin";
	} else{ $elem_loggedin="button";	}
	
	$self=basename($_SERVER['PHP_SELF']);
	$v=$_REQUEST['v'];
	?>
		<div  class="padding_before_home_button">&nbsp;</div>
    	<a href="../common/index.php?lang=<?=$GLOBALS['lang']?>" class="<?=$elem_loggedin?> <?=$myfrm->is_current_page($self, "index.php")?>"><?=Home?></a>
    	<a href="../pages/products.php?v=t&lang=<?=$GLOBALS['lang']?>" class="<?=$elem_loggedin?> <?=$myfrm->is_current_page($self,"products.php")?>" ><?=Products?></a>
    	<a href="../pages/news.php?v=t&lang=<?=$GLOBALS['lang']?>" class="<?=$elem_loggedin?> <?=$myfrm->is_current_page($self,"news.php")?>" ><?=News?></a>
    	<a href="../pages/contactus.php?lang=<?=$GLOBALS['lang']?>&v=contactus" class="<?=$elem_loggedin?> <?=$myfrm->is_current_page($self, "contactus.php", $v, "contactus")?>"><?=ContactUs?></a>
    	<a href="../pages/contactus.php?lang=<?=$GLOBALS['lang']?>&v=about" class="<?=$elem_loggedin?> <?=$myfrm->is_current_page($self, "contactus.php", $v, "about")?>"><?=About?></a>
    	
    	<? $opos1=""; $opos2="";?>
    	<? if($GLOBALS['lang']=='ar'){$opos1='en';$opos2=EN;}else{$opos1='ar';$opos2=AR;}?>
    	<a href="../common/index.php?lang=<?=$opos1?>" class="<?=$elem_loggedin?>"><?=$opos2?></a>
    	
    	<? $arr = $myfrm->SiteConfig("header_logo");?>
    	<div class="logo"><a href="../common?lang=<?=$GLOBALS['lang']?>"><img src="../documents/SOCIAL_<?=$arr[0]['NID']?>.<?=$arr[0]['pic_social']?>" /></a></div>
    	<? if(session_is_registered("GID")){?>
    		<a href="../pages/users.php?lang=<?=$GLOBALS['lang']?>&v=p&NID=<?=$_SESSION['GID']?>" class="user_profile <?=$elem_loggedin?> <?=$myfrm->is_current_page($self, "user", "p")?>">
    			<span class="unm"><?=$_SESSION['UNM']?></span>
    			<? 
    			$src = "../documents/thumbs/BUSER_{$_SESSION["GID"]}.{$_SESSION["UPIC"]}";
    			//echo $src;
    			if(!file_exists($src))
    				//$src = "../images/xuser.png";
    			?>
    			<img src="<?=$src?>" class="profile_image"/>
    		</a>
    	<? }?>
    	<!-- ------------------------------------ -->
    	<a href="#" class="<?=$elem_loggedin?> <?=$myfrm->is_current_page($self,".php")?>" ><?	//if($_SESSION['GID']) {foreach($_SESSION['PRIVS'] as $p) {echo "$p + ";}}?></a>
    <? 	
}////////////////////////////////////////////////////////////////////////////////////////

function SEARCHING()
//it's obviously clear, the search textbox with scripts calling
{
	include_once '../common/pframe.php';
	$myfrm =new pframe();
	
	?><script src="../cms/jquery.min.js"></script>
	<script src="../cms/bootstrap.min.js"></script>
	<script src="../cms/typeahead.min.js"></script>
	
	<script>

// jquery script to operate search query in other page 

	$(document).ready(function(){
		$('input.typeahead').typeahead({
			name: 'typeahead',
			remote:'search_sql.php?key=%QUERY',
			limit : 10
		});
	});
	
	document.getElementById('txtsrch').addEventListener('keypress', function(event) {
		if (event.keyCode == 13) {
			event.preventDefault();
			document.getElementById('send').click();
		}
	});
	</script>
	
	 <? ///////////  Searching textbox   ////////// ?>
	 <div class="search_div">	
	 	<form action="../common/search_sql.php" method="get" id="srch_form"> 
			<input type="text" id="txtsrch" name="typeahead" class="typeahead tt-query " autocomplete="off" spellcheck="false" placeholder="<?=Search_all_over_thesite?>">
			<input type="submit" id="send" value="send" style="display:none"/>
		</form>
	 </div>
	 <? 
}////////////////////////////////////////////////////////////////////////////////////////

function check_email_address($email) {
	// First, we check that there's one '@' symbol, and that the lengths are right
	if (!ereg("^[^@]{1,64}@[^@]{1,255}$", $email)) {
		// Email invalid because wrong number of characters in one section, or wrong number of @ symbols.
		return false;
	}

	// Split it into sections to make life easier

	$email_array = explode("@", $email);
	$local_array = explode(".", $email_array[0]);
	for ($i = 0; $i < sizeof($local_array); $i++) {
		if (!ereg("^(([A-Za-z0-9!#$%&'*+/=?^_`{|}~-][A-Za-z0-9!#$%&'*+/=?^_`{|}~\.-]{0,63})|(\"[^(\\|\")]{0,62}\"))$", $local_array[$i])) {
			return false;
		}
	}
	if (!ereg("^\[?[0-9\.]+\]?$", $email_array[1])) { 
// Check if domain is IP. If not, it should be valid domain name

		$domain_array = explode(".", $email_array[1]);
		if (sizeof($domain_array) < 2) {
			return false; // Not enough parts to domain
		}
		for ($i = 0; $i < sizeof($domain_array); $i++) {
			if (!ereg("^(([A-Za-z0-9][A-Za-z0-9-]{0,61}[A-Za-z0-9])|([A-Za-z0-9]+))$", $domain_array[$i])) {
				return false;
			}
		}
	}
	return true;
}////////////////////////////////////////////////////////////////////////////////////////

function Mail_List()
// all operations related to mail list : 
// validation, redirection, parameters, show messages, inserting to DB
{
	if($_POST['mail_list'])
	{
		$email_invalid_format = false;
		$email_already_exists = false;
		$email_added_successfully = false;
		$email_insert_error = false;	
		
		$everything_is_ok = true;
		
// validate empty values

		if($_REQUEST['txtmail'] == "" || $_REQUEST['txtmail'] == insrt_ur_eml || $_REQUEST['txtmail'] == Email_already_exists || $_REQUEST['txtmail'] == Added_successfully || $_REQUEST['txtmail'] == Adding_error ){
			$everything_is_ok = false;
		}

// validate email format
		
		if ( !check_email_address( $_REQUEST['txtmail'] ) ) {
			$email_invalid_format = true;
			$_REQUEST['txtmail'] = Invalid_Email;
			$everything_is_ok = false;
		}

//your email already exists in our mail list

		$result = get_data_in("select email from mail_list where email = '{$_REQUEST['txtmail']}' ", "email");
		if( $result != "") {  		$email_already_exists = true;			
		}else{
			if( $everything_is_ok )
			{
				if(cmd("insert into mail_list values('".uniqid()."', '{$_REQUEST['txtmail']}','".nowandate."');"))	{
					$email_added_successfully = true;}
				else{$email_insert_error = true; }
				
			}
		}

// removing the maillist parameter from the URI
 		
		$arr = explode("&", $_SERVER['QUERY_STRING']);
		foreach ($arr as $key=>$val) 
		{
			if(($pos=strpos($val, "mail_list=")) !== false)		
				unset($arr[$key]);
		}
		$queryString="";
		foreach ($arr as $key=>$val)
		{
			$queryString .="&{$val}";
		}
		$queryString = ltrim($queryString, '&');
		echo $queryString;
		
//redirection to page now after all that manipulation

		if($email_already_exists)
			header("location: {$_SERVER['PHP_SELF']}?{$queryString}&mail_list=Email_already_exists");
		elseif($email_added_successfully)
				header("location: {$_SERVER['PHP_SELF']}?{$queryString}&mail_list=Added_successfully");
		elseif($email_insert_error)
			header("location: {$_SERVER['PHP_SELF']}?{$queryString}&mail_list=Adding_error");
		elseif($email_invalid_format)
			header("location: {$_SERVER['PHP_SELF']}?{$queryString}&mail_list=Invalid_Email");
		else
			$mail_list_message="";	
		 
	}// END of if($_POST['mail_list'])
	$mail_list_message="";
	$style="";
	switch ($_REQUEST['mail_list'])
 
// show result message

	{
		case "Email_already_exists":
			$mail_list_message=Email_already_exists;
			$style='color:red;border:2px red solid;';
			break;
		case "Added_successfully":
			$mail_list_message=Added_successfully;
			$style='color:green;border:2px green solid;';
			break;
		case "Adding_error":
			$mail_list_message=Adding_error;
			$style='color:red;border:2px red solid;';
			break;
		case "Invalid_Email":
			$mail_list_message=Invalid_Email;
			$style='color:red;border:2px red solid;';
			break;
	}
	if($mail_list_message == '')
		$mail_list_message = insrt_ur_eml;
	?>

// HTML form for subscribing

 	<div style="text-align:center;">
		<form action="<?=$_SERVER['PHP_SELF'].'?'.$_SERVER['QUERY_STRING']?>" method="post" id="form_maillist">
			<input type="text" name="txtmail" hint="<?=insrt_ur_eml?>" value="<?=$mail_list_message?>"  class="txtmaillist" id="mailbox_txt" style="<?=$style?>" onclick="this.value='';" onblur="if(this.value=='') this.value='<?=$mail_list_message?>';" autocomplete="off"/>
			<input type="submit" name="mail_list" value="<?=Send?>" class="submit" id="mail_list" onclick="checktxtbx()" />
			<input type="hidden" name="REQUEST_URI" value="<?=$_SERVER['REQUEST_URI']?>" />
		</form>
	</div>
 	<?
}////////////////////////////////////////////////////////////////////////////////////////

function DisplaySideBarButtons()

// SIDE BAR 

{
	include_once '../common/pframe.php';
	$myfrm =new pframe();
	$self = basename($_SERVER['PHP_SELF']) ;

// Buttons also vary according to the logged in user and his user category

	?><a class="button <?=$myfrm->is_current_page($self, "category.php")?>" href="../pages/category.php?v=t&lang=<?=$GLOBALS['lang']?>"><?=Categories?></a>
		<a class="button <?=$myfrm->is_current_page($self, "services.php")?>" href="../pages/services.php?v=t&lang=<?=$GLOBALS['lang']?>"><?=Our_Services?></a>
		<a class="button <?=$myfrm->is_current_page($self, "our_customers.php")?>" href="../pages/our_customers.php?lang=<?=$GLOBALS['lang']?>&v=t"><?=Our_Customers?></a>
		<a class="button <?=$myfrm->is_current_page($self, "download.php")?>" href="../pages/download.php?v=t&lang=<?=$GLOBALS['lang']?>"><?=Download?></a>
		<? if($myfrm->Is_Distributor() || $myfrm->Is_Customer()){?>
			<a class="button <?=$myfrm->is_current_page($self, "bconts.php")?>" href="../pages/bconts.php?lang=<?=$GLOBALS['lang']?>&v=t"><?=BConts_Management?></a>
			<a class="button <?=$myfrm->is_current_page($self, "mconts.php")?>" href="../pages/mconts.php?lang=<?=$GLOBALS['lang']?>&v=t"><?=MConts_Management?></a>
						
		<? }else{?>
			<a class="button <?=$myfrm->is_current_page($self, "jobs.php")?>" href="../pages/jobs.php?v=t&lang=<?=$GLOBALS['lang']?>"><?=Jobs?></a>
			<a class="button <?=$myfrm->is_current_page($self, "faq.php")?>" href="../pages/faq.php?v=t&lang=<?=$GLOBALS['lang']?>"><?=FAQ?></a>
			<a class="button <?=$myfrm->is_current_page($self, "albums.php")?>" href="../pages/albums.php?v=t&lang=<?=$GLOBALS['lang']?>"><?=Albums?></a>
		<? }
}////////////////////////////////////////////////////////////////////////////////////////

function Display_Ads()
{
// bringing random 5 Advertisements that are still valid from DB and show'em rotately

	include_once '../obj/ads.class.php';
	$myAds=new Ads();
	
	$sql = "SELECT * FROM {$myAds->tblname} WHERE (now() BETWEEN ads_start AND ads_end) ORDER BY RAND() LIMIT 5";
	$tbl=table($sql);
	$id_arr=array();
	$url_arr=array();
	$i=0;
	
	while($row=mysql_fetch_array($tbl))
	{
		$myAds->FillIn($row);
		$id_arr[$i] = "../documents/ADS_{$myAds->ads_id['value']}.{$myAds->ads_pic['ext']}";
		$url_arr[$i] = $myAds->ads_link['value'];
		$i++;
	}
	
	$interval=3000;
	?>
	<div id="ads_div"> 
	
	<p class="ads_bar_hd">
	<? if(user_has_permission(array("A", "ADS_MAN"))){?>
		<a class="ads_bar_hd_a" href="../pages/ads.php?lang=<?=$GLOBALS['lang']?>&v=t"><img class="edt" src="../images/pencil-512.png" /><?=Ads?></a>
	<? }else{ echo Ads;}?>
	</p>
	<a id="ads_link" href="" TARGET="_BLANK" ><img id='ads_img'/></a>
	</div>
	
	<script>
	(function(){
		
	///// Rotation Interval(msec)
	var interval = <?=$interval?>;
	///// pic ID
	var pic_id = "ads_img";
	var ads_link = "ads_link";
	///// pic URL list
	var pics = <?php echo json_encode($id_arr ); ?>;
	var urls = <?php echo json_encode($url_arr ); ?>;
	var pic_idx = 0;
	var pic_img = document.getElementById(pic_id);
	var a_img = document.getElementById(ads_link); 
	function rotate_pic(){
		if ( pic_idx == pics.length ) { pic_idx = 0; }
		var src = pics[pic_idx];
		pic_img.setAttribute("src", src);
		var url = urls[pic_idx];
		a_img.setAttribute ("href", url);
		pic_idx++;
		setTimeout( rotate_pic, interval );
	}
	rotate_pic();		}());
	</script>	<? 
}////////////////////////////////////////////////////////////////////////////////////////

function Bring_Configs_From_DB()
{
//  styles stored in DB as site configs, overwrite them in-line

	include_once '../common/pframe.php';
	$myfrm =new pframe();
	$color_main = $myfrm->SiteConfig("color_main");
	$color_second = $myfrm->SiteConfig("color_second");
	$color_third = $myfrm->SiteConfig("color_third");	
	$side_bar_button = $myfrm->SiteConfig("side_bar_button");
	
	echo $color_third[0]['value'];
?><style>

	/***** replace these with the mail color from DB *****/
	/************   color_main   ***********/
	a,a:active,a:visited{color:<?=$color_main[0]['value']?>}
	input[type="search"] {color:<?=$color_main[0]['value']?>}
	.Visits_to_Site{color:<?=$color_main[0]['value']?>}
	.footer .Visits_to_Site{color:<?=$color_main[0]['value']?>}
	#header_bar{background-color:<?=$color_main[0]['value']?>;border-bottom:1px <?=$color_main[0]['value']?> solid;}
	#header_bar .button{border:1px <?=$color_main[0]['value']?> solid;}
	#header_bar .button_loggedin{border:0px <?=$color_main[0]['value']?> solid;}
	#header_bar .is_current_page{color:<?=$color_main[0]['value']?>;}
	#content_sidebar #signin:hover{border:2px <?=$color_main[0]['value']?> solid;}
	.products .prod_more:hover{background-color:<?=$color_main[0]['value']?>;}
	.latest_news .news_more:hover{background-color:<?=$color_main[0]['value']?>;}
	.side_bar .button{color:<?=$color_main[0]['value']?>}
	.withTree a,.withTree a:active,.withTree a:visited{color:<?=$color_main[0]['value']?>}
	.side_bar_hd{background-color:<?=$color_main[0]['value']?>;}
	.side_bar .button:hover{border-color:<?=$color_main[0]['value']?>;
	.side_bar .is_current_page{color:<?=$color_main[0]['value']?>}
	.side_bar input[type="submit"]{background-color:<?=$color_main[0]['value']?>;border:2px <?=$color_main[0]['value']?> solid;}
	.side_bar input[type="submit"]:hover{background-color:<?=$color_main[0]['value']?>;}
	.ads_bar_hd{background-color:<?=$color_main[0]['value']?>;}
	.global_tbl tr:hover{border:1px <?=$color_main[0]['value']?> solid;}	
	.item_title a:hover{color:<?=$color_main[0]['value']?>;}
	.edit_tool_ico img:hover{border:2px <?=$color_main[0]['value']?> solid}
	.navbar a, .navbar a:visited{color:<?=$color_main[0]['value']?>;border:1px <?=$color_main[0]['value']?> solid;}
	.navbar a:hover{background-color:<?=$color_main[0]['value']?>;}
	
	/************   color_second   ***********/
	#header_bar .button:hover{background-color:<?=$color_second[0]['value']?>;}
	#header_bar .button_loggedin:hover{background-color:<?=$color_second[0]['value']?>}
	#header_bar .hide_sidebar:hover{ background-color:<?=$color_second[0]['value']?>;}
	.li_user{color:<?=$color_second[0]['value']?>}
	li.collapsibleListOpen{background-color:<?=$color_second[0]['value']?>;}
	.global_tbl th{background-color:<?=$color_second[0]['value']?>;}
	
	/************   color_third   ***********/
	input[type="search"] {border:1px <?=$color_third[0]['value']?> solid;}
	#header_bar .button{color:<?=$color_third[0]['value']?>;}
	#header_bar .logo img{border:1px <?=$color_third[0]['value']?> solid}
	#content_sidebar{background-color:<?=$color_third[0]['value']?>;}
	.side_bar{border-left:0px solid <?=$color_third[0]['value']?>;}
	.side_bar .button{border:2px <?=$color_third[0]['value']?> solid;}
	.content_hd{background-color:<?=$color_third[0]['value']?>;}
	.products .prod_sep{background-color:<?=$color_third[0]['value']?>;}
	.latest_news .news_img img{border:1px <?=$color_third[0]['value']?> solid;}
	#control_panel{width:700px;border:1px <?=$color_third[0]['value']?> solid;}
	#control_panel .color2{background-color:<?=$color_third[0]['value']?>}
	.related_pages_hd{background-color:<?=$color_third[0]['value']?>;}
	.global_tbl{border:2px <?=$color_third[0]['value']?> solid;}
	.user_card{border:1px <?=$color_third[0]['value']?> solid;}
	.user_card td{border-bottom:1px <?=$color_third[0]['value']?> solid;}
	.user_card .card_img{border:1px <?=$color_third[0]['value']?> solid;}
	.groups{border:2px <?=$color_third[0]['value']?> solid;}
	.li_group{border:2px <?=$color_third[0]['value']?> solid;}
	.li_user{border:2px <?=$color_third[0]['value']?> solid;}
	.li_gr_privs{border:1px <?=$color_third[0]['value']?> solid;}
	.all_privs{border:2px <?=$color_third[0]['value']?> solid;}
	.all_privs .hd{border:1px <?=$color_third[0]['value']?> solid;background-color:<?=$color_third[0]['value']?>;}
	.panel_title_text{background-color:<?=$color_third[0]['value']?>;}
	.msgbox .msgbox_line_sep{border-top:1px <?=$color_third[0]['value']?> solid;}
	
	
	/************   side_bar_button   ***********/
	.side_bar .button:hover{background-color:<?=$side_bar_button[0]['value']?>;border-color:;}
	.side_bar .is_current_page{border:2px #666 solid;}

</style>

<? 
}////////////////////////////////////////////////////////////////////////////////////////
