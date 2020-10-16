<?php
include_once '../common/pframe.php';
include_once '../obj/job.class.php';
include_once '../cms/navigator.php'; 

$myframe=new pframe();
$myframe->header(View_Jobs);
$myP=new Job($_REQUEST['NID']);
$pagePRIV = "JOBS_MAN";
if(user_has_permission(array("A", $pagePRIV))){	$adding ="";/*if adding is about another OBJ*/	}else{$adding=""; $pagePRIV="";	}

switch ($_REQUEST['v'])
	{
	//EDITOR________________________________________________________________________________
	case "e":	
		
	/***/IS_SECURE($pagePRIV);/***/	
		if ($myP->NID['IsNew']){	$ttl=Add_x; }else { $ttl=Edit_x; }
		
		$myframe->open_box("withTree", $ttl,"panel", $pagePRIV, $adding);
		$myP->DisplayEditor("_n");
		$myframe->close_box("panel");
	
	break;
	//Viewer________________________________________________________________________________
	case "c":
	
		$myframe->open_box("withTree", $myP->{'job_title_'.$GLOBALS['lang']}['value'],"panel", $pagePRIV, $adding);
		$myframe->card(
				$myP->job_id['value'],
				$myP->{'job_title_'.$GLOBALS['lang']}['value'],
				$myP->{'job_desc_'.$GLOBALS['lang']}['value'],
				"",
				"",more,true,""/*$myJob->NVNom['value']*/,
				$myP->NDate['value'],"",$pagePRIV);
	
		$myP->More_DVNom();	
		$myframe->close_box("panel");
	
	break;	
	//TABLE_________________________________________________________________________________
	case "t":
		
		$myframe->open_box("withTree", Jobs, "panel", $adding);
		$wherestr="  ";
		$sql="select * from {$myP->tblname} {$wherestr} order by NDate desc";
		$job=new Navigator($sql, $_GET['cur_page'], 6, "select count(NID) from {$myP->tblname} {$wherestr} ");
		
		if(@mysql_num_rows(mysql_query($sql)) != 0)
		{
			/////START CARDS EXPLORER
			while ($jobRow=mysql_fetch_array($job->result)){
				
				$myP->FillIn($jobRow);
				$myframe->card($myP->NID['value'],
								$myP->{'job_title_'.$GLOBALS['lang']}['value'],
								$myP->{'job_desc_'.$GLOBALS['lang']}['value'],
								"",
								"",/*more*/"no_more",true,""
                                      /*$myJob->NVNom['value']*/,$myP->NDate['value'],"", $pagePRIV);
			}
			//////END CARDS EXPLORER
			$job->Draw_Navigator_Line();
		}else{
			?><div style="color:#999;width:90%;border:1px #ccc solid;padding:15px;margin:auto;text-align: center;"><?=No_Rows_Selected?></div><?
			}
		$myframe->close_box("panel");
		?>
				<script>
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
		if ($myP->NID['IsNew'])  break;
		
		$myframe->open_box("withTree", Del_x, "panel", $pagePRIV, $adding);
		$myP->DisplayDelMsg();
		$myframe->close_box("panel");
	
	break;	

}
/*************************   related pages  ******************************/
$myframe->Display_Related_Pages(Jobs,array("A", $pagePRIV));

$myframe->footer();
?> 