<?php
if (!$GLOBALS['fiveStarScriptLoaded']){
	?>
	<script type="text/javascript" src="../cms/5star_script.js"></script>
	<?php
	$GLOBALS['fiveStarScriptLoaded']=true;
}

function fiveStar($tbl, $id_name, $id_val) {
	
$query = mysql_query("SELECT * FROM {$tbl} where {$id_name} = '{$id_val}' ");

$row = mysql_fetch_array($query);

	$rating = (int)$row[rating];
	?>
	<div class="fivestarObj">
	<div class="floatleft">
		<div id="rating_<?php echo $id_val; ?>" tbl="<?=$tbl?>" idn="<?=$id_name?>" disabled="<?php if (isset($_COOKIE['rated'.$id_val])) { echo "disabled"; } ?>">
			<span class="star_1"><img src="../images/star_blank.png" alt="" <?php if($rating > 0) { echo"class='hover'"; } ?> /></span>
			<span class="star_2"><img src="../images/star_blank.png" alt="" <?php if($rating > 1.5) { echo"class='hover'"; } ?> /></span>
			<span class="star_3"><img src="../images/star_blank.png" alt="" <?php if($rating > 2.5) { echo"class='hover'"; } ?> /></span>
			<span class="star_4"><img src="../images/star_blank.png" alt="" <?php if($rating > 3.5) { echo"class='hover'"; } ?> /></span>
			<span class="star_5"><img src="../images/star_blank.png" alt="" <?php if($rating > 4.5) { echo"class='hover'"; } ?> /></span>
			<span class="star_rating">(<strong><?php echo $rating; ?></strong>)</span>
		</div>
	</div>

	<div class="clearleft">&nbsp;</div>
	</div>
	<?php	

}
?>