<?php 
include_once '../db/mysqlcon.php';
include_once '../common/pframe.php';

$myframe=new pframe();
$myframe->header();

?>
	<table class="data_intry_table">
		<form id="data_intry_form" action="insert.php" method="GET" >
		
			<?php //$result=1; ?>
			<tr><th colspan = "2"><span><?=Inter_data?> </span></th></tr>
			<tr><td class="label"><span><?=name?>: </span></td> <td class="inp"><input type="text" name="txtname" value=""/></td></tr>
			<tr><td class="label"><span><?=father?>: </span></td> <td class="inp"><input type="" name="" value="" /></td></tr>
			<tr><td class="label"><span><?=the_date?>: </span></td> <td class="inp"><input type="" name="" value="" /></td></tr>
			<tr><td class="label"><span><?=address?>: </span></td> <td class="inp"><textarea rows="10" cols="30">
					The cat was playing in the garden.
				</textarea></td></tr>
			<tr><td colspan = "2"><input type="submit" name="submit" value="<?=Save_data?>" /></td></tr>
				<input type="hidden" name="is_ok" value="<?=$result?>" />
				
		
		</form>
	</table>


<?php
$myframe->footer();