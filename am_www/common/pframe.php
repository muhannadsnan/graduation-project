<?php
ob_start();
require_once '../db/mysqlcon.php';
require_once '../lang/lang_'.$GLOBALS['lang'].'.inc';
require_once '../cms/IPframe.php'; //-->

class pframe implements IPframe {
//________________________________________________________________________________________	
	public function header($title="",$pms=array()){ // function displays header HTML elements
		
		$title=="" ? $title=PROJECT_TITLE : $title=PROJECT_TITLE." - ".$title; //PROJECT TITLE 
		
		if ($_REQUEST['noframe']=="basic") {
			include_once '../tpl/basic.tpl.inc';
			tpl_header($title,$pms['mnu'],$pms['rel']);
			echo '<div id="all_bdy">';
		}else if ($_REQUEST['noframe']) {
			echo '<div id="all_bdy">';
		}else{
			include_once '../tpl/tpl.tpl.inc';
			//tpl_header($title,$pms['mnu'],$pms['rel']);
			tpl_header($title);
			echo '<div id="all_bdy">';
		}		
	}//_____________________________________________________________________________________

	public function footer($pms=array()) {  // function displays footer HTML elements
		
		if ($_REQUEST['noframe']=="basic") {			
			include_once '../tpl/basic.tpl.inc';
			echo '</div>';
			tpl_footer();
		}else if ($_REQUEST['noframe']) {			
			echo '</div>';
			include_once '../common/js.php';
		}else{
			include_once '../tpl/tpl.tpl.inc';
			echo '</div>';
			tpl_footer();
		}		
	}//_____________________________________________________________________________________

	public function open_box($withTree= "", $box_title="", $box_type="panel",$priv="" ,$adding="") {
		
// a box is a div element  to be a container, and this is the opening function

		$open=true;		//include_once '../tpl/' . $box_type . '.box.inc';
		include_once '../tpl/panel.box.inc';		
	}//______________________________________________________________________________________
	 
	public function close_box($box_type="panel") {
		
// a box is a div element  to be a container, and this is the closing function

		$close=true;		//include '../tpl/' . $box_type . '.box.inc';
		include_once '../tpl/panel.box.inc';
	}//___________________________________________________________________________________
	
	function card($NID, $card_title, $card_sum, $card_photo, $page_link="", $more_text="", $showfooter=true, $visits="", $NDate="", $NType="", $priv="")
	{
// a card is an html container, contains title description, long text, photo and so on..

		$NType !== "" ? $ntp="&NType=" . $NType : $ntp="";
		
		include_once '../tpl/news.card.inc';
		card($NID, $card_title, $card_sum, $card_photo,$page_link, $more_text, $showfooter, $visits, $NDate, $NType, $priv);	
	}//______________________________________________________________________________________

	function DrawPhotos($images,$AID) {
		
//draws HTML and javascript that display and preview the photo of the objects calls this

		include_once '../tpl/ext.tpl.inc';
		DrawPhotos($images,$AID);
		
	}/////////////////////////////////////////////////////////////////////////////////
	
	function VisitsCounter()
	{
// displays the number of our site visits

		$cnt = get_data_in("select countid from tcount", "countid");
		?><span class="Visits_to_Site"><?=Visits_to_Site?><br/><h3><?=$cnt?></h3></span><?
	}/////////////////////////////////////////////////////////////////////////////////
	
	function Get_Incremental_ID($table="",$col="") {

//some object need to have a serial number, it has to be incremental. So if the db table was empty it returns '1', else it returns the largest ID + 1 , to be an ID for te new object declared

		$res=get_data_in("select count(*) as 'count' from {$table}",'count');
		if($res != 0)
		{
			$id=get_data_in("select max({$col})+1 as 'max'  from {$table}",'max');
		}else{
			$id=1;
		}
		return $id;
	}/////////////////////////////////////////////////////////////////////////////////
	
	function onLogin($user)// We set a secure cookie whenever a user logs in with remember me option 
	{
		$token = GenerateRandomToken(); // generate a token, should be 128 - 256 bit
		storeTokenForUser($user, $token);
		$cookie = $user . ':' . $token;
		$mac = hash_hmac('sha256', $cookie, SECRET_KEY);
		$cookie .= ':' . $mac;
		setcookie('rememberme', $cookie);
	}/////////////////////////////////////////////////////////////////////////////////
	
	function rememberMe() 
	{
//the "remember me" feature on login, if it was checked, then create cookies to get the job done

		$cookie = isset($_COOKIE['rememberme']) ? $_COOKIE['rememberme'] : '';
		if ($cookie) {
			list ($user, $token, $mac) = explode(':', $cookie);
			if ($mac !== hash_hmac('sha256', $user . ':' . $token, SECRET_KEY)) {
				return false;
			}
			$usertoken = fetchTokenByUserName($user);
			if (timingSafeCompare($usertoken, $token)) {
				logUserIn($user);
			}
		}else{return false;}
	}/////////////////////////////////////////////////////////////////////////////////
		
	function DisplayAdminTools($page = "", $ID="" /*we can set $e="e", $d="d" to check whther we need only edit or only del but we used JQuery to hide the unneeded one*/)
	{
//admin tools enable the user to edit or delete the object that they appear next to .

		$page ==''? basename($_SERVER ['PHP_SELF']) : $page;
		if($page == 'mconts'){	$arr=explode('/', $ID); $params="&bcont={$arr[0]}&prod={$arr[1]}"; $ID = $arr[2];}
		elseif($page == "shared_pool"){$params = "&path={$_REQUEST['path']}";}
		?>
		<div class="admin_tools">
			<a href="../pages/<?=$page?>.php?v=e&lang=<?=$GLOBALS['lang']?>&NID=<?=$ID?><?=$params?>" class="EDT_admintool"><img src="../images/pencil-512.png" /></a>
			<a href="../pages/<?=$page?>.php?v=d&lang=<?=$GLOBALS['lang']?>&NID=<?=$ID?><?=$params?>" class="DEL_admintool"><img src="../images/dltimg.png" /></a>
		</div>
		<? 
	}/////////////////////////////////////////////////////////////////////////////////
	
	function DisplayAddingTool($priv="", $page="", $NID="IsNew")
	{
//the adding tool appears for the admin user.
//it enables him to add a new object of a given class according to the current page

		if($page=="") $page=basename($_SERVER['PHP_SELF']);
		$NO_Pages = array("reset_password.php","maillist.php"); //pages that don't need the adding tool inside

		if($page=="download.php"){$NID = "add_existing";}
		if(!($this->Is_Customer() && $page == 'bconts.php') && !in_array($page, $NO_Pages)){ //customers not allowed to add bconts
			if(user_has_permission(array("A", $priv))){

//if user is an admin OR has the managing permission for this class

				?>
					<a href="../pages/<?=$page?>?lang=<?=$GLOBALS['lang']?>&v=e&NID=<?=$NID?>"><img src="../images/addimg.png" class="admin_tools_img"/></a>
				<?
			}
		}
	}/////////////////////////////////////////////////////////////////////////////////
	
	function Make_Tree($php_self)
	{
//the tree is a set of links displayed on the inner header of the contents.
//tree shows your position in a given page.. 
//eg. Home > Products > All products  - eg. Products > "xProduct" > edit
//echo $php_self;

		$col_name="";
		$col_id="";
		$tbl="a";
		$params="";

//determine the GET parameter 'v'

		if($_REQUEST['v']=='e') $e=true; //edit
		if($_REQUEST['v']=='c') $c=true; //card
		if($_REQUEST['v']=='t') $t=true; //table
		if($_REQUEST['v']=='d') $d=true; //delete
		
		switch($php_self)
		{

// every page needs a tree appears in a switch case

			case"products.php":
				$col_name = prod_title_x;
				$col_id = "prod_id";
				$tbl = 'product';
				if($_REQUEST['cat']){$all_page = Categories;}
				else{$all_page = Products;}
				
			break;
				
			case"download.php":
				$download = true;
				$col_name = prod_title_x;
				$col_id = "prod_id";
				$tbl = 'product';
				$all_page = Download;
				$params = "&NID={$_REQUEST['NID']}";
			break;
	
			case"news.php":
				$col_name = news_title_x;
				$col_id = "news_id";
				$tbl = 'news';
				$all_page = News;
			break;
	
			case"ads.php":
				$col_name = ads_title_x;
				$col_id = "ads_id";
				$tbl = 'ads';
				$all_page = Ads;
			break;
			
			case"bconts.php":
				$col_name = 'NSerial';
				$col_id = "NSerial";
				$tbl = 'buy_contract';
				$all_page = BConts;
				if($_REQUEST['NID'] != 'IsNew')	{$params = "&NID={$_REQUEST['NID']}";}
			break;
			
			case"services.php":
				$col_name = src_title_x;
				$col_id = "srv_id";
				$tbl = 'service';
				$all_page = Services;
			break;
			
			case"our_customers.php":
				$col_name = "user_name";
				$col_id = "user_id";
				$tbl = 'user';
				$all_page = Our_Customers;
			break;
			
			case"category.php":
				$col_name = cat_title_x;
				$col_id = "cat_id";
				$tbl = 'category';
				$all_page = Categories;
			break;
			
			case"jobs.php":
				$col_name = job_title_x;
				$col_id = "job_id";
				$tbl = 'jobs';
				$all_page = Jobs;
			break;
			
			case"albums.php":
				$col_name = album_title_x;
				$col_id = "album_id";
				$tbl = 'album';
				$all_page = Albums;
			break;
				
			case"pictures.php":
				$pictures=true;
				$col_name = pic_title_x;
				$col_id = "pic_id";
				$col_parent= album_title_x;
				$tbl = 'picture';
				$all_page = Albums;
			break;
			
			case"videos.php":
				$videos=true;
				$col_name = pic_title_x;
				$col_id = "vid_id";
				$col_parent= album_title_x;
				$tbl = 'video';
				$all_page = Albums;
			break;
			
			case"faq.php":
				$col_name = faq_title_x;
				$col_id = "faq_id";
				$tbl = 'faq';
				$all_page = FAQ;
			break;
			
			case"groups.php":
				$groups=true;
				$col_name = group_name;
				$col_id = "group_id";
				$tbl = 'groups';
				$all_page = Groups;
				if($_REQUEST['v'] == 'members'){
					$members_to_group=true;
					$e = true;
					$parentTitle = Groups;					
					$params="&NID={$_REQUEST['NID']}";
				}
				else{$params="&NID={$_REQUEST['NID']}";}
				$params="&NID={$_REQUEST['NID']}";
			break;
			
			case"mconts.php":
				$mconts=true;
				$col_name = 'NSerial';
				$col_id = "NSerial";
				$tbl = 'maint_contract';
				$all_page = mconts;
				$params="&bcont={$_REQUEST['bcont']}&prod={$_REQUEST['prod']}&NID={$_REQUEST['NID']}";
			break;
			
			case"users.php":			
				$col_name = 'user_name';
				$col_id = "user_id";
				$tbl = 'user';
				if($_SESSION['back_page']=='groups')	{
					$group_to_users=true;
					$all_page = Groups;
					$params="&NID={$_REQUEST['group']}";//to open/collapse the users groups
					if($_REQUEST['group']){
						$php_self="groups.php";
					}
					$parentTitle=Groups;
				}
				if($_SESSION['back_page']=='users'){
					$user=true;
					$all_page = Users_Management;
					if($_REQUEST['v']=="priv_editor"){$e=true; $v="priv_editor";$params="&NID={$_REQUEST['NID']}";}					
				}
				if($_REQUEST['v'] == 'm'){$user=true;$t=true;}
			break; 
			
			case"license.php":
				$license=true;
				$col_name = bcont_serial;
				$col_id = "bcont_serial";
				$tbl = 'buy_contract';
				$all_page = BConts;
				
			break;
			
			case"blocked_ips.php":
				$col_name = ip;
				$col_id = "NID";
				$tbl = 'blocked_ip';
				$all_page = BlockedIPs;
			break;
			
			case"maillist.php":
				$col_name = email;
				$col_id = "NID";
				$tbl = 'mail_list';
				$all_page = MailList;
			break;
			
			case"site_configs.php":
				$site_configs = true;
				$col_name = "title";
				$col_id = "NID";
				$tbl = 'site_configs';
				$all_page = Site_Configs;
			break;
			
			case"shared_pool.php":
				$col_name = "title";
				$col_id = "NID";
				$tbl = 'shared_pool';
				$all_page = Shared_pool;
				$params = "&path=";
			break;
			
		}
		$pageName="../pages/albums.php";

//now some pages need to have their tree manipulated
//the tree of these pages looks like this : Home > Albums > xAlbum > xPicture
//parent title is the album of a picture

		if($pictures || $videos){
				$parentTitle=get_data_in("select {$col_parent} from album where album_id='{$_SESSION["album"]}'",$col_parent);
				$title=get_data_in("select {$col_name} from {$tbl} where {$col_id}='{$_REQUEST["NID"]}'",$col_name);
		
		}elseif($license && $_REQUEST['NID'] != 'IsNew'){$title= bcont_serial." ".$_REQUEST['NID'];
		}elseif($download){ //nothing to show
		}else 
/********   defualt   *********/
			$title=get_data_in("select {$col_name} from {$tbl} where {$col_id}='{$_REQUEST["NID"]}'",$col_name);
		

//if the GET param v=t, ie. The current page displays table of all objects
		if($t){
//exceptions has more links to show
			if($user || $groups || $site_configs){
				
				$tree .= '<a href="../common/control_panel.php?lang='.$GLOBALS['lang'].'">'.Control_Panel.'</a> > ';
				if($_REQUEST['v'] == 'members'){ } 
			}
			
			elseif($pictures || $videos){ // Home > Albums > xAlbum
				$tree .= '<a href="../pages/albums.php?lang='.$GLOBALS['lang'].'&v=t">'.$all_page."</a> > ";	}

//exceptions has more links to show
			elseif($mconts){
				$tree .= '<a href="../pages/bconts.php?lang='.$GLOBALS['lang'].'&v=t&NID='.$_REQUEST['bcont'].'">'.BConts.'</a> >';		}
			
//HOME LINK
			else{		$tree .= '<a href="../common"><img src="../images/icons/home.png"/></a> > ';			}			
			
		}

//if the GET param v=c, ie. The current page displays a card of a specific object

		if($c){ //HOME >
			$tree .= '<a href="../common"><img src="../images/icons/home.png"/></a> >';
			
//exceptions has more links to show
			if($pictures || $videos){ // HOME > Albums > xAlbum > xPic

				$tree .= '<a href="../pages/albums.php?lang='.$GLOBALS['lang'].'&v=t">'.$all_page."</a> > ";
				$tree .= '<a href="../pages/albums.php?NID='.$_SESSION["album"].'&lang='.$GLOBALS['lang'].'&v=c">'.$parentTitle."</a> >";
				$params="&album=".$_REQUEST['album'];
			}elseif($users){$tree .= '<a href="../pages/groups.php?NID='.$_REQUEST["group"].'&lang='.$GLOBALS['lang'].'&v=t">'.$parentTitle."</a> >";
			
			}else{

// Home > All objects of this table

				$tree .= '<a href="'.$php_self.'?lang='.$GLOBALS['lang'].'&v=t'.$params.'">'.$all_page."</a> > ";
			}
		}

//if the GET param v=e || v=d, ie. The current page displays Editor (add | edit) or deleting page

		if($e || $d){
			$v_t="v=t";

//exceptions has different links to show
			if($pictures || $videos){ // Albums > xAlbum > xPic > [edit|delete] Pic

				$tree .= '<a href="../pages/albums.php?lang='.$GLOBALS['lang'].'&v=t">'.$all_page.'</a> > ';
				$tree .= '<a href="../pages/albums.php?NID='.$_SESSION["album"].'&lang='.$GLOBALS['lang'].'&v=c'.$params.'">'.$parentTitle."</a> >";
			}elseif($group_to_users || $members_to_group){ 
				$tree .= '<a href="../pages/groups.php?NID='.$_REQUEST["group"].'&lang='.$GLOBALS['lang'].'&v=t">'.$parentTitle."</a> >";	
			}elseif($user && $v=="priv_editor"){$tree .= '<a href="'.$php_self.'?lang='.$GLOBALS['lang'].'&v=m'.$params.'">'.$all_page.'</a> > ';
			
			}else{

// Products > xProduct > [edit|delete] Product

				$tree .= '<a href="'.$php_self.'?lang='.$GLOBALS['lang'].'&v=t'.$params.'">'.$all_page.'</a> > ';
			}
			$tree .= ' <a href="'.$php_self.'?NID='.$_REQUEST["NID"].'&lang='.$GLOBALS['lang'].'&v=c'.$params.'">'.$title."</a> >";
		}
		return $tree;
	}/////////////////////////////////////////////////////////////////////////////////
	
	function DisplaySlider()
	{
// calling javascript libraries
		?>
		<link rel="stylesheet" href="../cms/slider/slider_<?=$GLOBALS['lang']?>.css">	
		<script src="../cms/slider/jquery.min.js"></script>
		<script src="../cms/slider/slides.min.jquery.js"></script>
		
		<script>
		$(function(){

//jacascript of the slider, which consists of many pictures sliding with their titles

			$('#slides').slides({
				preload: true,
				preloadImage: '../cms/slider/img/loading.gif',
				play: 2700,
				pause: 5000,
				hoverPause: true,
				animationStart: function(current){
					$('.caption').animate({
						bottom:-35
					},100);
					if (window.console && console.log) {

						// example return of current slide number
						console.log('animationStart on slide: ', current);
					};
				},
				animationComplete: function(current){
					$('.caption').animate({
						bottom:0
					},200);
					if (window.console && console.log) {

						// example return of current slide number
						console.log('animationComplete on slide: ', current);
					};
				},
				slidesLoaded: function() {
					$('.caption').animate({
						bottom:0
					},200);
				}
			});
		});
	</script>

// HTML elements of the slider: images, labels, links

	<div id="container">
		<div id="example">
			<img src="../cms/slider/img/new-ribbon.png" width="112" height="112" alt="New Ribbon" id="ribbon">
			<div id="slides">
				<div class="slides_container">
				<?
				$tbl=table("select * from site_config where config = 'slider' order by NDate desc ");
				while($row=mysql_fetch_array($tbl)){					
					?>
					<div class="slide">
						<a href="<?=$row['link']?>" title="<?=$row['title']?>" target="_blank">
						<img src="../documents/SLIDER_<?=$row['NID']?>.<?=$row['pic_slider']?>" width="570" height="270" alt="Slide 1"></a>
						<div class="caption" style="bottom:0">
							<p><?=$row['title']?></p>
						</div>
					</div>
					<? 					
				}
				?></div>
				<a href="#" class="prev"><img src="../cms/slider/img/arrow-<?=$GLOBALS['lang']=='en'?'prev':'next'?>.png" width="24" height="43" alt="Arrow Prev"></a>
				<a href="#" class="next"><img src="../cms/slider/img/arrow-<?=$GLOBALS['lang']=='en'?'next':'prev'?>.png" width="24" height="43" alt="Arrow Next"></a>
			</div>
			<img src="../cms/slider/img/example-frame.png" width="739" height="341" alt="Example Frame" id="frame">
		</div>

	</div><? 
	}/////////////////////////////////////////////////////////////////////////////////
				 
	 function SiteConfig($config="")
	 {
// Brings the configurations stored in DB and collect them in an array. Usually called at the beginning of the page and overwrites CSS value
	 	include_once '../obj/site_config.class.php';
	 	$mySC = new Site_config(); 
	 	
	 	$tbl=table("select * from site_config where config = '{$config}' ");
	 	$arr = array();
	 	while($Row=mysql_fetch_array($tbl)){
	 		array_push($arr,array("NID"=>$Row['NID'], "config"=>$Row['config'], "title"=>$Row['title'], "link"=>$Row['link'], "value"=>$Row['value'], "pic_social"=>$Row['pic_social'], "pic_slider"=>$Row['pic_slider'], "NDate"=>$Row['NDate'])); 
	 	}
	 	return $arr;
	 }/////////////////////////////////////////////////////////////////////////////////
	 
	 function is_current_page($this_page, $href, $v='', $v2='')
	 {

//helps us determine which link to highlight to tell this is the currents page

	 	$current_page = false;
	 	$current_v = false;
	 	if($this_page == $href)
	 	{//echo"1";
	 		$current_page = true;
	 	}
	 	if($v == '') 
	 	{//echo"v=''";
	 		$current_v=true;
	 	}
	 	else
	 	{//echo"v!=''";
	 		if($v ==$v2)
	 		{
	 			$current_v = true;
	 		}
	 	}
	 	if($current_page && $current_v){
	 		echo 'is_current_page';//echo"true+true";
	 	}//echo"this=$this_page,href=$href,v=$v,v2=$v2";
	 }/////////////////////////////////////////////////////////////////////////////////
	 	 
	 function Display_Related_Pages($title="empty", $arr_priv=array(), $Clicker_ID="")
	 {
//displaying the related page section, editing, adding or viewing all objects

//first, this requires the user to have the management permissions of the current class

	 	if (user_has_permission($arr_priv))
	 	{
//what is the value of parameter 'v'

	 		if($_REQUEST['v']=='c') $c=true;
	 		if($_REQUEST['v']=='e') $e=true; 
	 		if($_REQUEST['v']=='d') $d=true;
	 		if($_REQUEST['v']=='t') $t=true;
	 		$NID="IsNew";
	 		$ptitle=Edit_x;	 
	 			 		
	 		?><div id="related_pages"><? 
	 		?><div class="related_pages_hd"><?=Related_Pages?></div><? 
	 				switch (basename($_SERVER['PHP_SELF']))
	 				{
//exceptions have to go here to have different attributes…

	 					case 'download.php':
	 						$download = true;
	 						$NID="add_existing";
	 						$ttl = Download;
	 					break;
	 					case 'albums.php':
	 						$albums=true;
	 						$ttl=Albums;
	 					break;
 						case 'pictures.php':
 							$pictures=true;
 							$ttl=Pictures;
 						break;
 						case 'mconts.php':
 							$mconts=true;
 							//if($_REQUEST['NID']=='IsNew') 
 								$NID="IsNew&bcont={$_REQUEST['bcont']}";
 							$ttl=mconts;
 						break;
	 					
	 				}

	 				if($e || $c)  // then show [view all objects] button
	 				{

//when the parameter 'v'=e (editing|adding) or v=c (card)

	 					$ptitle=View_x ." ". $ttl;
	 					if($albums){
	 						if($_REQUEST['NID']=='IsNew'){
	 								echo '<p>'.showview_details("../pages/albums.php?lang={$GLOBALS['lang']}&v=t", true, $ttl).'</p>';
	 							}else{
	 								echo '<p>'.showview_details("../pages/albums.php?lang={$GLOBALS['lang']}&v=d&NID={$_REQUEST['NID']}", true, Delete_Album).'</p>';
	 								echo '<p>'.showview_details("../pages/pictures.php?lang={$GLOBALS['lang']}&v=e&NID=IsNew&album={$_REQUEST['NID']}", true, Add_x." ".Pic).'</p>';
	 								echo '<p>'.showview_details("../pages/videos.php?lang={$GLOBALS['lang']}&v=e&NID=IsNew&album={$_REQUEST['NID']}", true, Add_x." ".Video).'</p>';
	 							}
	 						}
	 					else{
	 						echo '<p>'.showview_details($_SERVER['PHP_SELF']."?lang={$GLOBALS['lang']}&v=t&NID={$_REQUEST['NID']}", true, $ptitle).'</p>';
	 						$t=true; // also go as if the v=t too }
		 			}

// show [adding new item] button
		 			if($t || $d)
					 {
//when the parameter v=t (all obj) or v=d (delete)

				 		if($download){ echo '<p>'.showview_details($_SERVER['PHP_SELF']."?lang={$GLOBALS['lang']}&v=e&NID={$NID}", true, Add_x).'</p>';}
				 	
				 		else{
				 			$ptitle = Add_x." ".$ttl;
				 			echo '<p>'.showview_details($_SERVER['PHP_SELF']."?lang={$GLOBALS['lang']}&v=e&NID={$NID}", true, $ptitle).'</p>';
				 		}
					 }
					 	
		 	?></div><?
	 	}
	 }/////////////////////////////////////////////////////////////////////////////////
	 
	 function DisplayAlbumPic($myAlb)
	 {
//makes the last picture inserted to this album is the preview picture
//if the album was empty, a specific image is shown

	 	include_once '../obj/picture.class.php';
	 	$myPic=new Picture(); 
	 	$tbl=table("select * from picture where pic_album='".$myAlb->album_id['value']."'  order by NDate desc Limit 1"); 
	 	
	 	if(($row=mysql_fetch_array($tbl)) !== false)
	 	{ // album has pics
 			$myPic->FillIn($row);
 			echo $myPic->Draw_Photo($myPic->pic_ext, "thumb");
	 	}else{//album is empty from pics
	 		echo '<img src="../images/no-image.png" />';
	 	}
	 	
	 	
	 }/////////////////////////////////////////////////////////////////////////////////
	 
	 function User_Card($myUser, $user="users" /* is this page a user or group management page*/ ,
$priv /*must have this param to get admin tools*/)
	 {

//displays the personal information for a given user

	 	//if(user_has_permission(array("A", $priv))){
		 ?><table class="user_card"><? 
		 	?><tr> <td><b><?=$myUser->user_name['value']?> </b><td> 

//show admin tools if the user has the permission of the class 

<?if(user_has_permission(array("A", $priv))){$this->DisplayAdminTools($user, $myUser->user_id['value'] );}?></td></tr><? 
		 		?><tr><td><?=$myUser->Draw_Photo($myUser->user_pic, "thumb")?></td></tr><?
		 		?><tr> <td><?=user_email?></td> <td><?=$myUser->user_email['value']?></td></tr><?
		 		?><tr> <td><?=user_phone?></td> <td><?=$myUser->user_phone['value']?></td></tr><?
		 		?><tr> <td><?=user_country?></td> <td><?=$myUser->user_country['value']?></td></tr><?
		 		?><tr> <td><?=user_city?></td> <td><?=$myUser->user_city['value']?></td></tr><?
		 		?><tr> <td><?=user_address?></td> <td><?=$myUser->user_address['value']?></td></tr><?
	
		 	?></table><?
	 	//} 
	 }/////////////////////////////////////////////////////////////////////////////////	 
	 
	 function Is_Customer()
	 {

// enables us to know whether this user who called it, is a customer or not

	 	$customer=get_data_in("select user_cat from user where user_id='{$_SESSION['GID']}' ", "user_cat");
	 	if($customer == "customer"){
			return true;
	 	}else{
	 		return false;	
	 	}
	 	
	 }/////////////////////////////////////////////////////////////////////////////////
	 
	 function Is_Distributor()
	 {

// enables us to know whether this user who called it, is a distributor or not

	 	$customer=get_data_in("select user_cat from user where user_id='{$_SESSION['GID']}' ", "user_cat");
	 	if($customer == "distributor"){
	 		return true;
	 	}else{
	 		return false;
	 	}
	 	 
	 }/////////////////////////////////////////////////////////////////////////////////

	 function get_client_ip() { 

//bringing the ip address of the client

	 	$ipaddress = '';
	 	if (getenv('HTTP_CLIENT_IP'))
	 		$ipaddress = getenv('HTTP_CLIENT_IP');
	 	else if(getenv('HTTP_X_FORWARDED_FOR'))
	 		$ipaddress = getenv('HTTP_X_FORWARDED_FOR');//can be behind a proxy server
	 	else if(getenv('HTTP_X_FORWARDED'))
	 		$ipaddress = getenv('HTTP_X_FORWARDED');
	 	else if(getenv('HTTP_FORWARDED_FOR'))
	 		$ipaddress = getenv('HTTP_FORWARDED_FOR');
	 	else if(getenv('HTTP_FORWARDED'))
	 		$ipaddress = getenv('HTTP_FORWARDED');
	 	else if(getenv('REMOTE_ADDR'))
	 		$ipaddress = getenv('REMOTE_ADDR');//for clients connected through a proxy
	 	else
	 		$ipaddress = 'UNKNOWN';
	 	return $ipaddress;
	 }/////////////////////////////////////////////////////////////////////////////////
	 
	 function illegal_attempts($reason="failed_login", $ip=""){

//brings from DB the number of illegal attempts that the client ip has done in the las three days..
	
	 	$in_days=" and NDate >= NOW()-INTERVAL 3 DAY";//if this is enabled, the ip will blocked for 3 days
	 	$in_days="";
	 	$ip= $ip=="" ? $this->get_client_ip() : $ip;
	 	$sql = "select attempts from blocked_ip where ip='{$ip}' and reason='{$reason}' {$in_days}  order by NDate desc limit 1";
	 		return get_data_in($sql, "attempts");
	 	
	 }/////////////////////////////////////////////////////////////////////////////////
	 	 
	 function record_row_in_blockedips($type){

// reports/records the illegal attempt for the client IP in the db.

	 	$NID=uniqid();
	 	$NDate=nowandate;
	 	$ip=$this->get_client_ip();
	 	$user = $_SESSION['GID']=='' ? $_SESSION['failed_username']:$_SESSION['UNM'];
	 	
// has the ip done illegal attempt in the last 3 days ?

	 	$in_days=" and NDate >= NOW()-INTERVAL 3 DAY";
	 	$q="select * from blocked_ip where ip='{$ip}' and reason='{$type}' {$in_days} order by NDate desc limit 1";
	 	echo "<br/>".$q."<br/>";
	 	if(@mysql_num_rows(mysql_query($q)) == 0) // if the ip is new insert a new row
	 	{
	 		$sql="insert into blocked_ip values('{$NID}','{$ip}', '{$user}_', 1, '{$type}', '{$NDate}')"; echo $sql;
	 		@cmd($sql);
	 	}else{ //if the ip already exists update its row by increasing the attempts
	 		echo "<br/>select attempts from blocked_ip where ip='{$ip}' and reason='{$type}' <br/>";
	 		$num=get_data_in("select attempts from blocked_ip where ip='{$ip}' and reason='{$type}' {$in_days} order by NDate desc limit 1", "attempts") + 1;
	 		$sql="update blocked_ip set attempts = '{$num}', user=concat(user, '{$user}_') where ip='{$ip}' and reason='{$type}' {$in_days} order by NDate desc limit 1";	echo $sql;
	 		@cmd($sql);
	 	}
	 }/////////////////////////////////////////////////////////////////////////////////
	 
	 function Generat_OTID_Link($hash_user)
	 {

// genetates One-Time-ID LINK for a given user hash

	 	$otid = uniqid();
	 	$now = nowandate;
	 	$sql = "insert into sys_otid values('{$otid}', '{$hash_user}', '{$now}')"; //echo "<br/>". $sql;
	 	if(!@cmd($sql)) return false;
	 	
	 	$domain = "localhost/Hadara";
	 	$link = "{$domain}/common/reset_password.php?otid={$otid}&x={$hash_user}";
	 	return $link;
	 }/////////////////////////////////////////////////////////////////////////////////
	 
	 function get_Email_regestered_in_DB($user)
	 {
// checks whether an email is registered in our DB and return 

	 	$res = get_data_in("select user_email from user where user_name='{$user}' ", "user_email");
	 	
	 	return $res;
	 }/////////////////////////////////////////////////////////////////////////////////
	 
	 function unreadable_email($email)
	 {
// returns a part of the user's email (first 4 letters)

	 	if($email != "") 
	 	{
	 		$a="";
	 		
	 		for( $i=0 ; $i < strlen($email); $i++)
	 		{
	 			if($i > 4 && $email[$i] != '@')
	 				$a .= "*";
	 			else 
	 				$a .= $email[$i];
	 		}
	 		return $a;
	 	}
	 }/////////////////////////////////////////////////////////////////////////////////
	 
	function Valid_OTID($otid)
	{

// is the OTID (one-time-id) valid? If it has been created before 7 days then NO

		$sql = "select * from sys_otid where otid = '{$otid}' and NDate >= NOW() - INTERVAL 7 DAY";//echo $sql;
		$row=table($sql);
		if(@mysql_fetch_array($row)){return true;}
		else return false; 
		
	}/////////////////////////////////////////////////////////////////////////////////
	
	function Delete_OTID($otid, $hash_user)
	{
//delete the OTID from the DB

		$sql = "delete from sys_otid where otid = '{$otid}' ";//echo $sql;
		if(!@cmd($sql))
			return false;
		$sql = "delete from sys_otid where user_hash = '{$hash_user}' ";//echo $sql;
		if(!@cmd($sql))
			return false;
		
		return true;
	
	}/////////////////////////////////////////////////////////////////////////////////
	
	function bring_USER_by_hash($user_hash)
	{
// I give you user_hash then search between  all hashes of user to find this hash, and return me the user id

		$sql = "select user_id, user_name from user";//echo $sql;
		$tbl=table($sql);
		$user_found = "";
		while($row=mysql_fetch_array($tbl))
		{
			$user=$row['user_name'].".OTID";
			$md5=md5($user);
			if($md5 == $user_hash)
				$user_found = $row['user_id'];
		}
		return $user_found;
	
	}/////////////////////////////////////////////////////////////////////////////////
	
	function format_size($size) {

//formatting the file size
// return humanly readable number with the unit

		$sizes = array(" Bytes", " KB", " MB", " GB", " TB", " PB", " EB", " ZB", " YB");
		if ($size == 0) {
			return('n/a');
		} else {
			return (round($size/pow(1024, ($i = floor(log($size, 1024)))), 2) . $sizes[$i]);
		}
	}/////////////////////////////////////////////////////////////////////////////////
	
}?>