<?php
//VER 2.0
require_once ('../lang/lang_'.$GLOBALS["lang"].'.inc');
class Navigator
{
var $result;
var $ful_result;
	
var $records_per_page=5;
var $cur_page=0;
var $page_num=0;

var $total_num_rows=0;
var $total_num_pages=0;

var $old_first=false;

function Navigator($sql, $cur_page=0, $records_per_page=5, $countsqlstr=""){
	//full result
	$this->ful_result=mysql_query("$sql",$GLOBALS['con']);
	
	//Navigator Properties
	$this->records_per_page=$records_per_page;
	$this->cur_page=$cur_page;
	$this->page_num=$cur_page + 1;

	if ($countsqlstr==""){
		$this->total_num_rows=mysql_num_rows($this->ful_result);
	}else {
		$counttb=table($countsqlstr);
		if ($counttb){
			$this->total_num_rows=mysql_result($counttb,0,0);
		}else {
			$this->total_num_rows=mysql_num_rows($this->ful_result);
		}
	}
	
	$this->total_num_pages=ceil($this->total_num_rows/$records_per_page);

	$this->old_first=$old_first;
	
	//current page result
	$limit_str="LIMIT ". $cur_page * $records_per_page .", $records_per_page";
 	$this->result=mysql_query("$sql $limit_str",$GLOBALS['con']);
}

//////////DEFAULT NAVIGATOR LINE///////////////////////////////////////////
function Draw_Navigator_Line($class="",$frm="",$pms=""){
$main_fields=url_pms(array("lang", "cur_page"));
echo '<div class="page_nav_div">';
echo('<div class="navbar">');
echo '<a id="txt_a">'.$this->total_num_rows.' '.rows_found.' / '.$this->total_num_pages.' '.Page.'</a>';
if($this->page_num > 1) {
	//all pages except page number 1
	$prev_page = $this->cur_page - 1;
	echo '<a frm="'.$frm.'" href="'.$_SERVER['PHP_SELF'].'?lang='.$GLOBALS['lang'].'&'.$main_fields.'&cur_page=0&'.$pms.'" class="'.$class.'" >1</a>';
	echo '<a frm="'.$frm.'" class="do_go '.$class.'" href="'.$_SERVER['PHP_SELF'].'?lang='.$GLOBALS['lang'].'&'.$main_fields.'&cur_page='.$prev_page.'&'.$pms.'"><img style="border-width:0px; margin-top:5px;" src="../images/go_'.align.'.gif" alt="..." /></a>';
}//==============================================
	$num_page_1=$this->page_num-2;$cur_page_1=$this->cur_page-2;
	if ($cur_page_1>=1) {echo '<a frm="'.$frm.'"  class="'.$class.'" href="'.$_SERVER['PHP_SELF'].'?lang='.$GLOBALS['lang'].'&'.$main_fields.'&cur_page='.$cur_page_1.'&'.$pms.'">'.$num_page_1.'</a>';}
	$num_page_2=$this->page_num-1;$cur_page_2=$this->cur_page-1;
	if ($cur_page_2>=1) {echo '<a frm="'.$frm.'"  class="'.$class.'" href="'.$_SERVER['PHP_SELF'].'?lang='.$GLOBALS['lang'].'&'.$main_fields.'&cur_page='.$cur_page_2.'&'.$pms.'">'.$num_page_2.'</a>';}
	echo '<a id="txt_a"><strong>'.$this->page_num.'</strong></a>';
	$num_page_4=$this->page_num+1;$cur_page_4=$this->cur_page+1;
	if ($cur_page_4<$this->total_num_pages-1) {echo '<a  frm="'.$frm.'" class="'.$class.'" href="'.$_SERVER['PHP_SELF'].'?lang='.$GLOBALS['lang'].'&'.$main_fields.'&cur_page='.$cur_page_4.'&'.$pms.'">'.$num_page_4.'</a>';}
	$num_page_5=$this->page_num+2;$cur_page_5=$this->cur_page+2;
	if ($cur_page_5<$this->total_num_pages-1) {echo '<a  frm="'.$frm.'" class="'.$class.'" href="'.$_SERVER['PHP_SELF'].'?lang='.$GLOBALS['lang'].'&'.$main_fields.'&cur_page='.$cur_page_5.'&'.$pms.'">'.$num_page_5.'</a>';}
//================================================
if($this->page_num < $this->total_num_pages) {
	$next_page = $this->cur_page + 1;
	$last_page = $this->total_num_pages - 1;
	echo '<a frm="'.$frm.'" class="do_go '.$class.'" href="'.$_SERVER['PHP_SELF'].'?lang='.$GLOBALS['lang'].'&'.$main_fields.'&cur_page='.$next_page.'&'.$pms.'"><img style="border-width:0px; margin-top:5px;" src="../images/go_'.r_align.'.gif" alt="..." /></a>';
	echo '<a frm="'.$frm.'" class="'.$class.'" href="'.$_SERVER['PHP_SELF'].'?lang='.$GLOBALS['lang'].'&'.$main_fields.'&cur_page='.$last_page.'&'.$pms.'">'.$this->total_num_pages.'</a>';
}
echo("</div>");
echo '</div>';
}
//////////////END OF NAVIGATOR LINE/////////////////////////////////
//////////WORDS NAVIGATOR LINE///////////////////////////////////////////
function Draw_Navigator_Line_W(){
$main_fields=url_pms(array("lang", "cur_page"));
echo('<div class="navbar">');
echo '<a id="txt_a">'.$this->total_num_rows.' '.rows_found.' / '.$this->total_num_pages.' '.Page.'</a>';
if($this->page_num > 1) {
	//all pages except page number 1
	$prev_page = $this->cur_page - 1;
	echo '<a href="?lang='.$GLOBALS['lang'].'&'.$main_fields.'&cur_page=0">'.First.'</a>';
	echo '<a class="do_go" href="?lang='.$GLOBALS['lang'].'&'.$main_fields.'&cur_page='.$prev_page.'">'.Previous.'</a>';
}//==============================================
	$num_page_1=$this->page_num-2;$cur_page_1=$this->cur_page-2;
	if ($cur_page_1>=1) {echo '<a href="?lang='.$GLOBALS['lang'].'&'.$main_fields.'&cur_page='.$cur_page_1.'">'.$num_page_1.'</a>';}
	$num_page_2=$this->page_num-1;$cur_page_2=$this->cur_page-1;
	if ($cur_page_2>=1) {echo '<a href="?lang='.$GLOBALS['lang'].'&'.$main_fields.'&cur_page='.$cur_page_2.'">'.$num_page_2.'</a>';}
	echo '<a id="txt_a"><strong>'.$this->page_num.'</strong></a>';
	$num_page_4=$this->page_num+1;$cur_page_4=$this->cur_page+1;
	if ($cur_page_4<$this->total_num_pages-1) {echo '<a href="?lang='.$GLOBALS['lang'].'&'.$main_fields.'&cur_page='.$cur_page_4.'">'.$num_page_4.'</a>';}
	$num_page_5=$this->page_num+2;$cur_page_5=$this->cur_page+2;
	if ($cur_page_5<$this->total_num_pages-1) {echo '<a href="?lang='.$GLOBALS['lang'].'&'.$main_fields.'&cur_page='.$cur_page_5.'">'.$num_page_5.'</a>';}
//================================================
if($this->page_num < $this->total_num_pages) {
	$next_page = $this->cur_page + 1;
	$last_page = $this->total_num_pages - 1;
	echo '<a class="do_go" href="?lang='.$GLOBALS['lang'].'&'.$main_fields.'&cur_page='.$next_page.'">'.Next.'</a>';
	echo '<a href="?lang='.$GLOBALS['lang'].'&'.$main_fields.'&cur_page='.$last_page.'">'.Last.'</a>';
}
echo("</div>");
}
//////////////END OF NAVIGATOR LINE/////////////////////////////////
}
?>