<?php
require_once '../db/mysqlcon.php';
require_once '../lang/lang_'.$GLOBALS['lang'].'.inc';

require_once '../cms/IPframe.php'; //-->
class pframe implements IPframe {

//______________________________________________________________________________________________________________________________________
	
	public function header($title="",$pms=array()) {
		
		$title=="" ? $title=PROJECT_TITLE : $title=PROJECT_TITLE." - ".$title; //PROJECT TITLE 

			include_once '../tpl/tpl.tpl.inc';
			//tpl_header($title,$pms['mnu'],$pms['rel']);
			tpl_header($title);
			//echo '<div id="all_bdy">';
		
	}
//______________________________________________________________________________________________________________________________________

	public function footer($pms=array()) {
		
			include_once '../tpl/tpl.tpl.inc';
			//echo '</div>';
			tpl_footer();
	}
//______________________________________________________________________________________________________________________________________
	
	public function open_box($box_title="", $box_type="", $box_id="") {
		
		$open=true;
		include '../tpl/' . $box_type . '.box.inc';		
	}
//______________________________________________________________________________________________________________________________________
	 
	public function close_box($box_type="") {
		
		$close=true;
		include '../tpl/' . $box_type . '.box.inc';		
	}
//______________________________________________________________________________________________________________________________________
	
	function card($NID, $card_title, $card_sum, $card_photo, $page_link="", $more_text="", $showfooter=true, $visits="", $NDate="", $NType="")
	{
		$NType !== "" ? $ntp="&NType=" . $NType : $ntp="";
		
		include_once '../tpl/news.card.inc';
		card($NID, $card_title, $card_sum, $card_photo, $page_link, $more_text, $showfooter, $visits, $NDate, $NType);	
	}
	
	function pcard($NID, $card_title, $card_sum, $card_photo, $page_link="", $more_text="", $showfooter=true, $visits="", $NDate="", $card_table="")
	{	
		include_once '../tpl/products.card.inc';
		pcard($NID, $card_title, $card_sum, $card_photo, $page_link, $more_text, $showfooter, $visits, $NDate, $card_table);
	}

	
	function DrawLinks($links) {
		
		include_once '../tpl/ext.tpl.inc';
		DrawLinks($links);
	}
	
	function DrawPhotos($images,$AID) {
		
		include_once '../tpl/ext.tpl.inc';
		DrawPhotos($images,$AID);
	}
	///////////////////////////////////////////////////NEW CUSTOM FUNCTIONS
	
	function Get_Client_IP() {
	
		$ipaddress = '';
		if (getenv('HTTP_CLIENT_IP'))
			$ipaddress = getenv('HTTP_CLIENT_IP');
		else if(getenv('HTTP_X_FORWARDED_FOR'))
			$ipaddress = getenv('HTTP_X_FORWARDED_FOR');
		else if(getenv('HTTP_X_FORWARDED'))
			$ipaddress = getenv('HTTP_X_FORWARDED');
		else if(getenv('HTTP_FORWARDED_FOR'))
			$ipaddress = getenv('HTTP_FORWARDED_FOR');
		else if(getenv('HTTP_FORWARDED'))
			$ipaddress = getenv('HTTP_FORWARDED');
		else if(getenv('REMOTE_ADDR'))
			$ipaddress = getenv('REMOTE_ADDR');
		else
			$ipaddress = 'UNKNOWN';
	
		return $ipaddress;
	}//________________________________  get_client_ip <<< _____________________________________________________
	
	function Display_Control_Panel(){
	
		//echo "Display_Control_Panel()<br/>";
		?>
		<div class="control_panel" >
			<h2><?=Control_panel?></h2>
			<a href="../pages/data.php?v=d&?lang=<?=$GLOBALS['lang']?>"><?=Show_data?></a>
			<a href="../pages/search.php?v=s&?lang=<?=$GLOBALS['lang']?>"><?=Search?></a>
			<a href="../pages/users.php?v=u&?lang=<?=$GLOBALS['lang']?>"><?=Users?></a>
			<a href="../pages/change_pass.php?v=c&?lang=<?=$GLOBALS['lang']?>"><?=Change_Admin_Password?></a>
			<a href="../pages/blocked.php?v=b&?lang=<?=$GLOBALS['lang']?>"><?=Blocked?></a>
		</div><?
	}//________________________________  Display_Control_Panel <<< _____________________________________________________
	
	function Display_Side_Menu(){
	
		echo "Display_Side_Menu()<br/>";
	}//________________________________  Display_Side_Menu <<< _____________________________________________________
	
	
}
?>