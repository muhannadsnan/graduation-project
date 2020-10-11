<?php
function get_file_icon($ftype)
{
	if (file_exists("../ext/icons/".$ftype.".png"))
	{
		return "<img src='../ext/icons/{$ftype}.png' style='border:0px-width' class='file_icon' />";
	}else {
		return "<img src='../ext/icons/file.png' style='border:0px-width' class='file_icon' />";
	}
}
?>