<?php
include_once("../db/mysqlcon.php");
function Draw_Counter()
{
$oldnum=get_data_in("select CountID from tcount","CountID");
if (!session_is_registered("StopVisitsCount"))
{
$oldnum+=1;
cmd("update tcount set CountID='$oldnum'");
$_SESSION['StopVisitsCount']=true;
}

return '<span class="counter">'.Visitors.': <span class="counter_nom">'.$oldnum.'</span></span>';
}
//إبراهيم خليل
/*
CREATE TABLE IF NOT EXISTS `tcount` (
  `CountID` bigint(1) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dumping data for table `tcount`
--

INSERT INTO `tcount` (`CountID`) VALUES
(0);
 * */
?>
