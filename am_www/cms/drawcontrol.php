<?php 
$GLOBALS ['calloaded'] = false;
function draw_control($DataRow, $actrl, $lblstyle = "")
{
	if ($actrl ['permission'] !== null && ! user_has_permission ( $actrl ['permission'] ))
	{return;}

// CHeching the value of the attribute 'control' with the 'Switch' for the given object
	switch ($actrl ['control'])
	{
		case 'hidden' : // control = hidden . input type = hidden

			echo '<div class="jfld' . $lblstyle . '"><input type="hidden" name="txt_' . $actrl ['name'] . '" id="txt_' . $actrl ['name'] . '" value="' . $actrl ['value'] . '" /></div>';
			break;

		case 'text' : // control = text . input type = text

			echo '<div class="jfld' . $lblstyle . '"><div class="field_label' . $lblstyle . '">' .addreq ( $actrl ['required'] ). constant ( $actrl ['caption'] )  . '</div><div class="txtfld' . $lblstyle . '"><input type="text" name="txt_' . $actrl ['name'] . '" id="txt_' . $actrl ['name'] . '" class="validate_' . $actrl ['validate'] . '" value="' . $actrl ['value'] . '" /></div><div class="field_note' . $lblstyle . '">' . $actrl ['note'] . '</div></div>';
			break;

		case 'password' : // control = password . input type = password

			echo '<div class="jfld' . $lblstyle . '"><div class="field_label' . $lblstyle . '">'.addreq ( $actrl ['required'] ) . constant ( $actrl ['caption'] ) . '</div><div class="txtfld' . $lblstyle . '"><input  type="password" name="txt_' . $actrl ['name'] . '" id="txt_' . $actrl ['name'] . '" value="' . $actrl ['value'] . '" /></div><div class="field_note' . $lblstyle . '">' . $actrl ['note'] . '</div></div>';
			if (user_has_permission ( array ("A" ) ))
			{
				$myrepass = 'value="' . $actrl ['value'] . '"';
			}
			echo '<div class="jfld' . $lblstyle . '"><div class="field_label' . $lblstyle . '">'.addreq ( $actrl ['required'] ) . constant ( 'Re-Type' ) . ' ' . constant ( $actrl ['caption'] )  . '</div><div class="txtfld' . $lblstyle . '" ><input type="password" name="txt_' . $actrl ['name'] . '_r" id="txt_' . $actrl ['name'] . '_r" ' . $myrepass . ' /></div></div>';
			break;

		case 'textarea' : // control = text area . html <textarea>

			echo '<div class="jfld' . $lblstyle . '"><div class="field_label' . $lblstyle . '">'.addreq ( $actrl ['required'] ) . constant ( $actrl ['caption'] )  . '</div><div class="txtfld' . $lblstyle . '"><textarea   name="txt_' . $actrl ['name'] . '" id="txt_' . $actrl ['name'] . '" cols="40" rows="10">' . $actrl ['value'] . '</textarea></div><div class="field_note' . $lblstyle . '">' . $actrl ['note'] . '</div></div>';
			break;

		case 'fkey' : // control = fkey, (html select), Foreign value brought from another table

			$mytbl = table ( "select {$actrl['fID']}, {$actrl['fTitle']} from {$actrl['ftbl']} {$actrl['fFltr']} order by {$actrl['fTitle']}" );
			echo '<div class="jfld' . $lblstyle . '"><div class="field_label' . $lblstyle . '">'.addreq ( $actrl ['required'] ) . constant ( $actrl ['caption'] )  . '</div>
			<div class="txtfld' . $lblstyle . '">
			<select  name="txt_' . $actrl ['name'] . '" id="txt_' . $actrl ['name'] . '">';

//each foreign key control has 'options' attribute, that must match constant variable to display
// these options can also brought by performing sql query in attributes such as:
// ftbl, fTitle, fID, fFltr
			foreach ( $actrl ['options'] as $kopt => $vopt )
			{
				$actrl ['value'] == $kopt ? $selme = "selected" : $selme = "";
				echo '<option value="' . $kopt . '" ' . $selme . ' >' . constant ( $vopt ) . '</option>';
			}
			while ( $fkrow = mysql_fetch_array ( $mytbl ) )
			{
				$actrl ['value'] == $fkrow [0] ? $selme = "selected" : $selme = "";
				echo '<option value="' . $fkrow [0] . '" ' . $selme . ' >' . $fkrow [1] . '</option>';
			}
			echo '</select></div><div class="field_note' . $lblstyle . '">' . $actrl ['note'] . '</div></div>';
			break;

// control = list, specific values that we want to give to a field, we can define the options by the attribute 'options' that take a value of array('op1'=>'val1', 'op2'=>'val2'….)
		case 'list' :
			echo '<div class="jfld' . $lblstyle . '"><div class="field_label' . $lblstyle . '">'.addreq ( $actrl ['required'] ) . constant ( $actrl ['caption'] ) . '</div><div class="txtfld' . $lblstyle . '"><select  name="txt_' . $actrl ['name'] . '" id="txt_' . $actrl ['name'] . '">';
			foreach ( $actrl ['options'] as $kopt => $vopt )
			{
				$actrl ['value'] == $kopt ? $selme = "selected" : $selme = "";
				echo '<option value="' . $kopt . '" ' . $selme . ' >' . constant ( $vopt ) . '</option>';
			}
			echo '</select></div><div class="field_note' . $lblstyle . '">' . $actrl ['note'] . '</div></div>';
			break;
// control = countries, list of countries
		case 'countries' :
			echo '<div class="jfld' . $lblstyle . '"><div class="field_label' . $lblstyle . '">' .addreq ( $actrl ['required'] ). constant ( $actrl ['caption'] ) . '</div><div class="txtfld' . $lblstyle . '"><select  name="txt_' . $actrl ['name'] . '" id="txt_' . $actrl ['name'] . '">';
			$tbl = table ( "select * from countries" );
			while ( $dd = mysql_fetch_row ( $tbl ) )
			{
				$actrl ['value'] == $dd [1] ? $selme = "selected" : $selme = "";
				echo '<option value="' . $dd [1] . '" ' . $selme . ' >' . $dd [1] . '</option>';
			}
			echo '</select></div><div class="field_note' . $lblstyle . '">' . $actrl ['note'] . '</div></div>';
			break;
// control = date, when a db field has DATETIME type, it takes this case.
		case 'date' :
			if (! $GLOBALS ['calloaded'])
			{
				echo '<!-- import the calendar script -->
					<script src="../jscal2-1.9/src/js/jscal2.js"></script>
			    	<script src="../jscal2-1.9/src/js/lang/en.js"></script>
			    	<link rel="stylesheet" type="text/css" href="../jscal2-1.9/src/css/jscal2.css" />
			    	<link rel="stylesheet" type="text/css" href="../jscal2-1.9/src/css/border-radius.css" />
			    	<link rel="stylesheet" type="text/css" href="../jscal2-1.9/src/css/steel/steel.css" />';
			}
// calling javascript files

			$GLOBALS ['calloaded'] = true;

// now display html input an other html elements for collecting date value
			echo '<div class="field_box' . $lblstyle . '"><div class="ttxtg field_label' . $lblstyle . '">'.addreq ( $actrl ['required'] ) . constant ( $actrl ['caption'] )  . '</div><div class="txtfld' . $lblstyle . '"  ><input type="text"  name="txt_' . $actrl ['name'] . '" id="txt_' . $actrl ['name'] . '" value="' . $actrl ['value'] . '" /> <img id="img_' . $actrl ['name'] . '"  name="img_' . $actrl ['name'] . '" style="cursor:pointer;" src="../images/cal.gif" alt="' . Click_Here_to_Pick_up_the_date . '" class="datecalimg' . $lblstyle . '"   />
		<script type="text/javascript">
		var cal_' . $actrl ['name'] . ' = Calendar.setup({
        	onSelect   : function() { this.hide() },
        	showTime   : ' . $actrl ['withtime'] . '
      	});
      	cal_' . $actrl ['name'] . '.manageFields("img_' . $actrl ['name'] . '", "txt_' . $actrl ['name'] . '", "' . $actrl ['format'] . '");
		</script></div></div>';
			break;

// control = file, displays the button to browse for the file you want to upload… input type = file
		case 'file' :
			echo '<div  class="jfld' . $lblstyle . '" style="clear:both;overflow:hidden;">	<input type="hidden" name="MAX_FILE_SIZE" value="100000000" />'
			.'<div class="field_label' . $lblstyle . '">'.addreq ( $actrl ['required'] ) . constant ( $actrl ['caption'] )  . '</div><div class="txtfld' . $lblstyle . '"><input  name="txt_' . $actrl ['name'] . '" id="txt_' . $actrl ['name'] . '" type="file" /></div><div class="field_note' . $lblstyle . '">' . $actrl ['note'] . '</div></div>';

//if you upload an image file, it may have attribute 'resize' which makes a thumb copy of the image
			if ($actrl ['view'] == 'image' && $DataRow->NID ['IsNew'] == false)
			{
				if ($actrl ['resize'] == true)
				{
					echo '<div style="clear:both;overflow:hidden;width:200px;margin:auto"><img style="margin:15px" src="' . $DataRow->get_file_path ( $actrl, "thumb" ) . '" /></div>';
				} else
				{
					echo '<div style="clear:both;overflow:hidden;width:200px;margin:auto"><img style="margin:15px" src="' . $DataRow->get_file_path ( $actrl ) . '" /></div>';
				}
			}			
			break;		
	} 
}

// some fields in php class files has attribule 'required'='required', to validate the form and prevent empty values. Those field appear with '*' sign.
function addreq($myreq) 
{
	if ($myreq == true)
	{?><span class="requied_field">*</span><? }
}
?>