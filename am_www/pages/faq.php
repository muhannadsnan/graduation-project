<?php
include_once '../common/pframe.php';
include_once '../obj/faq.class.php';
include_once '../cms/navigator.php'; 

$myframe=new pframe();
$myframe->header(FAQ);
$myFAQ=new faq($_REQUEST['NID']);
$pagePRIV = "FAQ_MAN";
if(user_has_permission(array("A", $pagePRIV))){	$adding ="";/*if adding is about another OBJ*/	}else{$adding=""; $pagePRIV="";	}

switch ($_REQUEST['v'])
	{
	//EDITOR________________________________________________________________________________
	case "e":

	// routine as usual for editor
	/***/IS_SECURE($pagePRIV);/***/		
		if ($myFAQ->NID['IsNew']){	$ttl=Add_x; }else { $ttl=Edit_x; }
		
		$myframe->open_box("withTree", $ttl,"panel", $pagePRIV, $adding);
		$myFAQ->DisplayEditor("_n");
		$myframe->close_box("panel");
	
	break;
	//Viewer________________________________________________________________________________
	case "c":
	
// also routine for card
		$myframe->open_box("withTree", $myFAQ->{'faq_title_'.$GLOBALS['lang']}['value'],"panel", $pagePRIV, $adding);
		$myframe->card(
				$myFAQ->faq_id['value'],
				$myFAQ->{'faq_title_'.$GLOBALS['lang']}['value'],
				$myFAQ->{'faq_desc_'.$GLOBALS['lang']}['value'],
				"",
				"","no_more",true,$myFAQ->NVNom['value'],
				$myFAQ->NDate['value'],"",$pagePRIV);
	
		$myFAQ->More_DVNom();	
		$myframe->close_box("panel");
	
	break;	
	//TABLE________________________________________________________________________________
	case "t":
		
		$myframe->open_box("withTree", FAQ,"panel", $adding);
		$wherestr="  ";
		$sql="select * from {$myFAQ->tblname} {$wherestr} order by NDate desc";
		$faq=new Navigator($sql, $_GET['cur_page'], 6, "select count(NID) from {$myFAQ->tblname} {$wherestr} ");
		if(@mysql_num_rows(mysql_query($sql)) != 0)
		{
			/////START CARDS EXPLORER		
			while ($faqRow=mysql_fetch_array($faq->result)){
				
				$myFAQ->FillIn($faqRow);
				
				$myframe->card($myFAQ->NID['value'],
								$myFAQ->{'faq_title_'.$GLOBALS['lang']}['value'],
								$myFAQ->{'faq_desc_'.$GLOBALS['lang']}['value'],
								"",
								"","no_more",true,"",""
                                     /*$myFAQ->NVNom['value'],$myFAQ->NDate['value']*/,"", $pagePRIV);
			}
			//////END CARDS EXPLORER
			$faq->Draw_Navigator_Line();
		}else{	?><div style="color:#999;width:90%;border:1px #ccc solid;padding:15px;margin:auto;text-align: center;"><?=No_Rows_Selected?></div><?	}
		$myframe->close_box("panel");
		?>

		<script>
//Here is the Javascript for the "show details when title clicked" feature
// Auto height animation	
		jQuery.fn.animateAuto = function(prop, speed, callback){
		    var elem, height, width;
		    return this.each(function(i, el){
		        el = jQuery(el), elem = el.clone().css({"height":"auto","width":"auto"}).appendTo("body");
		        height = elem.css("height"),
		        width = elem.css("width"),
		        elem.remove();
		        
		        if(prop === "height")
		            el.animate({"height":height}, speed, callback);
		        else if(prop === "width")
		            el.animate({"width":width}, speed, callback);  
		        else if(prop === "both")
		            el.animate({"width":width,"height":height}, speed, callback);
		    });  
		}

		$( ".item_txt" ).height(0) ;

			//////// when clicking on a title the description appears under it
	
			$( ".item_title" ).click(function() { 
		    if ( $(this).next( ".item_txt" ).height() != 0)
		    	$(this).next( ".item_txt" ).animate({ height: 0 }, 400 );
		    else{
		    	$(this).next( ".item_txt" ).animateAuto("height", 1000);
		    }
		});
		</script><? 	
	break;
	//DELETE_______________________________________________________________________________
	case "d":
		/***/IS_SECURE($pagePRIV);/***/			
		if ($myFAQ->NID['IsNew'])  break;
		
		$myframe->open_box("withTree", Del_faqs,"panel", $pagePRIV, $adding);
		$myFAQ->DisplayDelMsg();
		$myframe->close_box("panel");
	
	break;
}
/*************************   related pages  ******************************/
$myframe->Display_Related_Pages(FAQ,array("A", $pagePRIV));

$myframe->footer();
?>