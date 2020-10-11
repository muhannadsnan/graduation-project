<?php
include_once '../db/mysqlcon.php';

$rating = (int)$_POST['rating'];
$NID = $_POST['id'];
$tbl = $_POST['tbl'];
$IDn = $_POST['idn'];

$query = mysql_query("SELECT * FROM {$tbl} WHERE {$IDn} = '".$NID."'") or die();

while($row = mysql_fetch_array($query)) {

	if($rating > 5 || $rating < 1) {
		echo"* > 5 / * < 1";
	}
	
	elseif(isset($_COOKIE['rated'.$NID])) {
		echo"<div class='highlight'>!!! #".$_COOKIE['rated'.$NID]."</div>";
	}
	else {
	
		setcookie("rated".$NID, $rating, time()+60*60*24*365, "/");

		$total_ratings = $row['total_ratings'];
		$total_rating = $row['total_rating'];
		$current_rating = $row['rating'];

		$new_total_rating = $total_rating + $rating;
		$new_total_ratings = $total_ratings + 1;
		$new_rating = $new_total_rating / $new_total_ratings;
		

		// Lets run the queries. 

		mysql_query("UPDATE {$tbl} SET total_rating = '".$new_total_rating."' WHERE {$IDn} = '".$NID."'") or die();
		mysql_query("UPDATE {$tbl} SET rating = '".$new_rating."' WHERE {$IDn} = '".$NID."'") or die();
		mysql_query("UPDATE {$tbl} SET total_ratings = '".$new_total_ratings."' WHERE {$IDn} = '".$NID."'") or die();

		echo"<div class='highlight'>#{$rating}</div>";

	}
}




?>