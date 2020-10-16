<?php 
include_once '../common/pframe.php';
include_once '../cms/navigator.php';
include_once "../obj/service.class.php";

$myframe=new pframe();
$myframe->header(Our_Services);
$myService = new Service($_REQUEST['NID']);
$pagePRIV = "SERVICES_MAN";
if(user_has_permission(array("A", $pagePRIV))){	$adding ="";/*if adding is about another OBJ*/	}else{$adding=""; $pagePRIV="";	}

switch ($_REQUEST['v'])
{
	case "e": /********************* add, edit ************************/
	/***/IS_SECURE($pagePRIV);/***/
		
		if ($myService->NID['IsNew']){	$ttl=Add_x; }else { $ttl=Edit_x; }
		
		$myframe->open_box("withTree", $ttl,"panel", $pagePRIV, $adding);
		$myService->DisplayEditor("_n");
		$myframe->close_box("panel");
	break;
	
	case "t":  /****************** All elements **************************/
		$myframe->open_box("withTree", Our_Services,"panel",$adding);
		
		$wherestr="";
		$sql="select * from {$myService->tblname} {$wherestr} order by NDate desc";
		$serv=new Navigator($sql, $_GET['cur_page'], 10, "select count(NID) from {$myService->tblname} {$wherestr} ");
		
		if(@mysql_num_rows(mysql_query($sql)) != 0)
		{
			?><table id="Our_Services_tbl sortable"><?
					////START CARDS NAVIGATOR
					$i=0;
					$tr_class="";
					while ($ServRow=mysql_fetch_array($serv->result))
					{
						$myService->FillIn($ServRow);
						$myframe->card(
							$myService->srv_id['value'],
							$myService->{'srv_title_'.$GLOBALS['lang']}['value'],
							$myService->{'srv_desc_'.$GLOBALS['lang']}['value'],"",
							"","no_more",true,
							"",
							$myService->NDate['value'],$pagePRIV);
						$i++;
					}
					?></table><?
					//////END CARDS NAVIGATOR
			$serv->Draw_Navigator_Line();
		}else{
			?><div style="color:#999;width:90%;border:1px #ccc solid;padding:15px;margin:auto;text-align: center;"><?=No_Rows_Selected?></div><?
			}
		$myframe->close_box("panel");
		?>
						<script>
// the "click title to show details" feature
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
							//////// usage	
							$( ".item_title" ).click(function() { 
						    if ( $(this).next( ".item_txt" ).height() != 0)
						    	$(this).next( ".item_txt" ).animate({ height: 0 }, 400 );
						    else{
						    	$(this).next( ".item_txt" ).animateAuto("height", 1000);
						    }
						});
						</script><? 
	break;
	
	case "d": /********************* delete **********************/
	/***/IS_SECURE($pagePRIV);/***/	
	
		if ($myP->NID['IsNew'])  break;
	
		$myframe->open_box("withTree", Del_x,"panel", $pagePRIV, $adding);
		$myService->DisplayDelMsg();
		$myframe->close_box("panel");
	
	break;
}
/*************************   related pages  ******************************/
$myframe->Display_Related_Pages(Services,array("A", $pagePRIV));

$myframe->footer();
?>