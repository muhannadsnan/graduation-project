<?php 
function tpl_header($title="",$mnu=false,$rel=array()) 
{  ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">

<head>
	<meta http-equiv="content-type" content="text/html; charset=utf8" />
	<meta name="author" content="muhannad snan" />
	<link href="../common/main_ar.css" rel="stylesheet" type="text/css" media="screen" />
    
    <script type="text/javascript" src="../cms/jquery-1.5.1.min.js"></script>
    <script type="text/javascript" src="../cms/datagrid.js"></script>
    <script type="text/javascript" src="../cms/jquery.fancybox-1.3.1.pack.js"></script>	
    
	
	<title><?=$title?></title>
</head>

<body>

	<div id="header"><!------------------------ HEADER >>>--------------------------------------------------------------------> 
	    <div class="header_img">
	    	<img src="../images/header.jpg" />
	    </div>
	    
	    
	    <?php 
	    if(basename($_SERVER['PHP_SELF']) != "signin.php"){
 
	    }
	     
		if(user_has_permission(array("A","B"))){
		 ?>	
			<div class="header_menu_bar">
		    	<a href="../common/signin.php?lang=<?=$GLOBALS['lang']?>&v=c"><?=Control_panel?></a>
			    <a href="../pages/data.php?lang=<?=$GLOBALS['lang']?>&v=d"><?=Show_data?></a>
			    <a href="../pages/search.php?lang=<?=$GLOBALS['lang']?>&v=s"><?=Search?></a>
			    <div class="user_info">
		    		<?php 
		    		echo "user: {$_SESSION['UNM']} |";
		    		include_once '../common/pframe.php';
		    		$myframe=new pframe();
		    		echo $myframe->Get_Client_IP();
		    		?>
			    </div>
			    
		    	<div class="signout_btn">
		    		<a href="../cms/signout.php" ><img src="../images/pwr.png" /></a>
		    	</div>
	    	</div>
	   <?php } ?>
	   
	</div><!------------------------ HEADER <<<--------------------------------------------------------------------> 
	

<div id="content"><!------------------------ CONTENT >>>--------------------------------------------------------------------> 
	
<?php  } 

function tpl_footer(){//__________________________________________________________________________________________________________________________________________________________
	
?>
</div><!------------------------ CONTENT <<<-------------------------------------------------------------------->

	<div id="footer"><!------------------------ FOOTER >>>--------------------------------------------------------------------> 

	</div><!------------------------ FOOTER <<<--------------------------------------------------------------------> 

</body>

</html>
<?php
}