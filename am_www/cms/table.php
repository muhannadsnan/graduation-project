<?php
require_once '../cms/navigator.php';
function DisplayTable($cols, $DataSet, $sql, $showediting=false, $wherestmnt="", $joinlink="", $joinparam="",$jointtls=""){
	$DataSetTmp=new $DataSet("new");
	echo '<table class="data_grid" >';
	foreach ($cols as $col) {
		$sosi=$DataSetTmp->$col;
		echo "<th>".constant($sosi['caption'])."</th>";
	}
	if ($joinlink!="") {
	if (is_array($joinlink)) {
		foreach ($jointtls as $jl) {
			if (substr($jl,0,1)=="#"){
				echo "<th>&nbsp;</th>";
			}elseif (substr($jl,0,1)=="@"){
				
			}else { 
				echo "<th>".$jl."</th>";	
			}
		}
	}else {
		echo "<th></th>";
	}
	}
	foreach ($_GET as $pmk => $pmv) {
		if ($pmk!="v" && $pmk!="lang" && $pmk!="NID"){
		$strpms[]=$pmk."=".$pmv;	
		}
	}
	$strpms=@join("&",$strpms);
	
	$cur_page=$_REQUEST['cur_page'];
	$old_first=false;
	
	$nav1=new Navigator($sql, $cur_page, 20, "select count({$cols[0]}) from {$DataSetTmp->tblname} {$wherestmnt}");
	while ($tblrow=mysql_fetch_array($nav1->result)) {
		echo "<tr>";
		foreach ($cols as $col) {
				$thecol=$DataSetTmp->$col;
			
				if (in_array($thecol['type'], array('varchar', 'char', 'text', 'ID', 'int', 'double', 'datetime')))
				{
					if (in_array($thecol['control'], array('fkey'))){
						if ($thecol['showkey']){$thefkey=$tblrow[$col];}
						echo "<td> {$thefkey} - ".get_data_in("select {$thecol['fTitle']} from {$thecol['ftbl']} where {$thecol['fID']} like '{$tblrow[$col]}' ", $thecol['fTitle'])."</td>";
					}elseif (in_array($thecol['control'], array('list'))){
						if (is_array($jointtls)) {$a_links=array_keys($jointtls,"@".$thecol['name']);}
						if ($a_links[0]!==null){
							$link_id=$a_links[0];
							echo "<td><a href='{$joinlink[$link_id]}&{$joinparam[$link_id]}={$tblrow[$DataSetTmp->NID['name']]}'>".constant($thecol['options'][$tblrow[$col]])."</a></td>";
						}else{
							echo "<td>".constant($thecol['options'][$tblrow[$col]])."</td>";
						}
					}else{
						if (is_array($jointtls)) {$a_links=array_keys($jointtls,"@".$thecol['name']);}
						if ($a_links[0]!==null){
							$link_id=$a_links[0];
							echo "<td><a href='{$joinlink[$link_id]}&{$joinparam[$link_id]}={$tblrow[$DataSetTmp->NID['name']]}'>{$tblrow[$col]}</a></td>";
						}else{
							echo "<td>{$tblrow[$col]}</td>";
						}
					}
				}elseif (in_array($thecol['type'], array('file')) && in_array($thecol['view'], array('image'))){
					if ($thecol['resize']==true)
					{
						echo '<td><img src="'.$DataSetTmp->thumbs_path.$thecol['sizes']['thumb']['p'].$thecol['prefix'].$tblrow[$DataSetTmp->NID['name']].".".$tblrow[$col].'" /></td>';
					}else {
						echo "<td><img src=\"{$DataSetTmp->documents_path}{$thecol['prefix']}{$tblrow[$DataSetTmp->NID['name']]}.{$tblrow[$col]}\" /></td>";
					}
				}elseif (in_array($thecol['type'], array('file')) && $thecol['view']=='link'){
					echo "<td><a href=\"{$DataSetTmp->documents_folder}{$thecol['prefix']}{$tblrow[$DataSetTmp->NID['name']]}.{$tblrow[$col]}\">".View."</a></td>";
				}
		}
		if ($showediting){
			if ($joinlink!="") {
			if (is_array($joinlink)){
				for ($i=0; $i < count($joinlink); $i++){
					if (substr($jointtls[$i],0,1)=="#"){
						echo "<td><a href='{$joinlink[$i]}&{$joinparam[$i]}={$tblrow[$DataSetTmp->NID['name']]}'>".substr($jointtls[$i],1)."</a></td>";
					}elseif(substr($jointtls[$i],0,1)=="@"){
					}else{
						echo "<td>".showview_details($joinlink[$i]."&".$joinparam[$i]."=".$tblrow[$DataSetTmp->NID['name']], true, "", array("N"))."</td>";
					}	
				}
			}else {
				echo "<td>".showview_details($joinlink."&".$joinparam."=".$tblrow[$DataSetTmp->NID['name']], true, "", array("N"))."</td>";	
			}
			}
			if (user_has_permission(array("A"))) {
			echo "<td>".showedit($_SERVER['PHP_SELF']."?lang={$GLOBALS['lang']}&NID={$tblrow[$DataSetTmp->NID['name']]}&v=e&{$strpms}", false, "", array("A"))."</td>";
			echo "<td>".showdelet($_SERVER['PHP_SELF']."?lang={$GLOBALS['lang']}&NID={$tblrow[$DataSetTmp->NID['name']]}&v=d&{$strpms}", false, "", array("A"))."</td>";
			}
		}
		echo "</tr>";
	}
	
	echo "</table>";
	
	//4.Draw navigator line
	echo '<div class="page_nav_div" style="margin-top:20px; padding-bottom:20px;margin-left:20px;">';
	$nav1->Draw_Navigator_Line("v=t&".$strpms);
	echo "</div>";
}
?>