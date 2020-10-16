<?php
include_once '../common/pframe.php';
$myframe=new pframe();
$myframe->header("Control_Panel");

?>
<center>
<div id="control_panel">
	<p class="content_hd"><?=Control_Panel?></p>
	
/*****************************   all management buttons   *****************************/
       /*******************   Groups & Users Management buttons   *******************/	

	<div class="button_container">
		<? if(user_has_permission(array("A" ,"GROUPS_MAN"))){?> <a class="button color1" href="../pages/groups.php?lang=<?=$GLOBALS['lang']?>&v=t"><?=Groups_Management?></a> <? } ?>

		<? if(user_has_permission(array("A" ,"USERS_MAN"))){ ?> <a class="button color1" href="../pages/users.php?lang=<?=$GLOBALS['lang']?>&v=t"><?=Users_Management?></a> <? } ?>

		<? if(user_has_permission(array("A" ,"CUSTOMERS_MAN"))){?> <a class="button color1" href="../pages/users.php?lang=<?=$GLOBALS['lang']?>&v=t&ucat=customer"><?=Customer_Management?></a> <? } ?>
    /********** Categories, Products, Downloads and News Management buttons  ***********/
			
		<? if(user_has_permission(array("A" ,"CATEGORY_MAN"))){?> <a class="button color2" href="../pages/category.php?lang=<?=$GLOBALS['lang']?>&v=t"><?=Cats_Management?></a> <? } ?>

		<? if(user_has_permission(array("A" ,"PRODUCTS_MAN"))){?> <a class="button color2" href="../pages/products.php?lang=<?=$GLOBALS['lang']?>&v=t"><?=Prods_Management?></a> <? } ?>

		<? if(user_has_permission(array("A" ,"DOWNLOADS_MAN"))){?> <a class="button color2" href="../pages/download.php?lang=<?=$GLOBALS['lang']?>&v=t"><?=Downloads_Management?></a> <? } ?>

		<? if(user_has_permission(array("A" ,"NEWS_MAN"))){?> <a class="button color2" href="../pages/news.php?lang=<?=$GLOBALS['lang']?>&v=t"><?=News_Management?></a> <? } ?>

/************** Services, Jobs, FAQ, Contactus, Advertisements Management buttons **************/

		<? if(user_has_permission(array("A" ,"SERVICES_MAN"))){?> <a class="button color1" href="../pages/services.php?lang=<?=$GLOBALS['lang']?>&v=t"><?=Serices_Management?></a> <? } ?>

		<? if(user_has_permission(array("A" ,"JOBS_MAN"))){?> <a class="button color1" href="../pages/jobs.php?lang=<?=$GLOBALS['lang']?>&v=t"><?=Jobs_Management?></a> <? } ?>

		<? if(user_has_permission(array("A" ,"FAQ_MAN"))){?> <a class="button color1" href="../pages/faq.php?lang=<?=$GLOBALS['lang']?>&v=t"><?=FAQ_Management?></a> <? } ?>

		<? if(user_has_permission(array("A" ,"CONTACTUS_MAN"))){?> <a class="button color1" href="../pages/contactus.php?lang=<?=$GLOBALS['lang']?>&v=about"><?=Contactus_Management?></a> <? } ?>

		<? if(user_has_permission(array("A" ,"ADS_MAN"))){?> <a class="button color1" href="../pages/ads.php?lang=<?=$GLOBALS['lang']?>&v=t"><?=ADS_Management?></a> <? } ?>

/**************** Gallery albums, pictures and videos Management buttons ******************/

		<? if(user_has_permission(array("A" ,"ALBUMS_MAN","PICTURES_MAN", "VIDEOS_MAN"))){?> <a class="button color1" href="../pages/albums.php?lang=<?=$GLOBALS['lang']?>&v=t"><?=Albums_Pics_Videos_Management?></a> <? } ?>
		
/**************** Buy contracts & Maintenance contract Management buttons ***************/

		<? if(user_has_permission(array("A" ,"BCONTS_MAN"))){?> <a class="button color2" href="../pages/bconts.php?lang=<?=$GLOBALS['lang']?>&v=t"><?=BConts_Management?></a> <? } ?>
		<? if(user_has_permission(array("A" ,"MCONTS_MAN"))){?> <a class="button color2" href="../pages/mconts.php?lang=<?=$GLOBALS['lang']?>&v=t"><?=MConts_Management?></a> <? } ?>
		
/************** Site Configurations, Blocked IPs, Maillist Management buttons **************/
			
		<? if(user_has_permission(array("A" ,"CONFIGS_MAN"))){?> <a class="button color1" href="../pages/site_configs.php?lang=<?=$GLOBALS['lang']?>&v=t"><?=Configs_Management?></a> <? } ?>
		<? if(user_has_permission(array("A" ,"BLOCKED_IPS_MAN"))){?> <a class="button color1" href="../pages/blocked_ips.php?lang=<?=$GLOBALS['lang']?>&v=t"><?=Blocked_IPs_Management?></a> <? } ?>
		<? if(user_has_permission(array("A" ,"MAILLIST_MAN"))){?> <a class="button color1" href="../pages/maillist.php?lang=<?=$GLOBALS['lang']?>&v=t"><?=Maillist_Management?></a> <? } ?>
	</div>
	
</div>
</center>
<?php 

@$myframe->footer();
?>