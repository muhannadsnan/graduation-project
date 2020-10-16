<?php 
include_once '../db/mysqlcon.php';
include_once '../common/pframe.php';

$myframe=new pframe();
$myframe->header();     //calling the header template

?><div id="content"><!------------------------ CONTENT >>>-------------------------------------------------------------------->

<? /*********************   Visits Counter   *******************/?>
<? $myframe->VisitsCounter();?>

<? /*********************   Slider   *******************/?>
<? $myframe->DisplaySlider();?> 

<p class="content_hd"><?=Latest_Products?></p>
    <div class="products">
        
    	<?php 

//bringing latest three products from db and display them in boxes with photos

			include_once "../obj/product.class.php";
			
			$myProd = new Product();
			$tbl=table("select * from {$myProd->tblname} where prod_exe <>'' order by NDate Desc limit 3");
			$i=0;
			$float_lft="";

			while ($row=mysql_fetch_array($tbl))
			{
			 $myProd->FillIn($row);
		     ?>
			 <div class="prod_blk <?echo $float_lft;?>">
				<div class="prod_title"><?=$myProd->{'prod_title_'.$GLOBALS['lang']}['value']?></div>
				<div class="prod_img">
                <?php 
              		$src = "../documents/thumbs/BPRO_{$myProd->prod_id['value']}.{$myProd->prod_pic['ext']}";

// photo path >> display the photo

                    if(!file_exists($src)){$src = "../images/test0.png";}
                 ?>
                <img src="<?=$src?>" /></div>
				<p class="prod_desc"><?=$myProd->{'prod_desc_'.$GLOBALS['lang']}['value']?></p>
				<a class="prod_more" href="../pages/products.php?lang=<?=$GLOBALS['lang']?>&v=c&NID=<?=$myProd->NID['value']?>" target="_blank"><?=more?></a>
			 </div>
			
		     <?php	$i++;
			} ?>
        <div >  <a class="prod_more" href="../pages/products.php?lang=<?=$GLOBALS['lang']?>&v=t" target="_blank"><?=All_Products?></a> </div>
    </div>
    <?php //////////////////////////////////////////////////////////////////////////////////////////////////////////////?>
    <div class="latest_news">
        <p class="content_hd"><?=Latest_News?></p>
   	    <?php
			include_once "../obj/news.class.php";
			
			$myNews = new News();
			$tbl=table("select * from {$myNews->tblname} order by NDate Desc limit 3");
			$i=0;
			?><div class="content"><? 

//bringing latest three news from db and display them in boxes with photos

			while ($row=mysql_fetch_array($tbl))
			{
			 $myNews->FillIn($row);
				?>
				<div class="news_blk blk_<?=$i+1?>" style="<? if($i>1){echo "margin-left:0px";} ?>">
					<div class="news_title"><?=$myNews->{'news_title_'.$GLOBALS['lang']}['value']?></div>
					<div class="news_img">
	                <?php 
	                    $src = "../documents/thumbs/SNEWS_{$myNews->news_id['value']}.{$myNews->news_pic['ext']}";
	                    if(!file_exists($src)){$src = "../images/test1.png";}
	                 ?>
	                <img src="<?=$src?>" /></div>
					<div class="news_desc"><?=$myNews->{'news_desc_'.$GLOBALS['lang']}['value']?></div>
					<div >  <a class="news_more" href="../pages/news.php?lang=<?=$GLOBALS['lang']?>&v=c&NID=<?=$myNews->NID['value']?>" target="_blank"><?=more?></a> </div>
				 </div>
		     <?php $i++;
			} ?>
			</div><!-- content -->
        <div >  <a class="news_more" href="../pages/news.php?lang=<?=$GLOBALS['lang']?>&v=t" target="_blank"><?=All_News?></a> </div>
    </div>
<?php

$myframe->footer();   //calling the footer template