<style>
.time_input{direction:ltr; text-align:left;padding-right:10px;}
.half_hour{-moz-user-select:none;cursor:pointer;float:left;display:block;border:0px #C3D9FF solid;font-size:7pt;width:13px;height:25px;font-family:Tahoma;background-color:#C3D9FF;padding:3px 1px;}
.selected_half_hour{background-color:orange;color:white;}
.half_hour_day{}
.half_hour_night{}
.dt_txt{border-width:0px;width:70px;}
</style>
	<? if (!$GLOBALS['calloaded']){ ?>
		<!-- import the calendar script -->
		<link rel="stylesheet" type="text/css" media="all" href="../calendar/themes/aqua.css">
		<script type="text/javascript" src="../calendar/src/utils.js"></script>
		<script type="text/javascript" src="../calendar/src/calendar.js"></script>
		<script type="text/javascript" src="../calendar/lang/calendar-en.js"></script>
		<script type="text/javascript" src="../calendar/src/calendar-setup.js"></script>
	<? } $GLOBALS['calloaded']=true; ?>
<script type="text/javascript">
function print_time_line()
{
	$(".mycal_time_line").append("<input id='txtfrom' type='hidden' name='" + $(".mycal_time_line").attr("name") + "_from' value='" + $(".mycal_time_line").attr("value") + " 00:00:00 am' />");	
	$(".mycal_time_line").append("<input id='txtto' type='hidden' name='" + $(".mycal_time_line").attr("name") + "_to' value='" + $(".mycal_time_line").attr("value") + " 00:00:00 am' />");
	$(".mycal_time_line").append("<div class='time_input'> <img id='dimg_" + $(".mycal_time_line").attr("name") + "'  name='dimg_" + $(".mycal_time_line").attr("name") + "' style='cursor:pointer;' src='../images/cal.gif' alt='' class='datecalimg' class='over(this)'  /> <input type='text' class='dt_txt' value='" + $(".mycal_time_line").attr("value") + "' name='dtxt_" + $(".mycal_time_line").attr("name") + "' id='dtxt_" + $(".mycal_time_line").attr("name") + "' /> <span class='logme'> Select Time</span></div>");
		
		var cal = new Zapatec.Calendar.setup({		
			inputField:"dtxt_" + $(".mycal_time_line").attr("name"),
			ifFormat:"%Y-%m-%d",
			button:"dimg_" + $(".mycal_time_line").attr("name"),
			showsTime:'false'		
		});	
		
	var am_pm=['am', 'pm'];
	var lastdn=0;
	var gon=0;
	var start_time='';
	var end_time='';
	var daytime="day";
	$.each(am_pm, function (t, ti){
		for(i=1; i<=12; i++)
		{
			var dn = i; if (ti=="pm") {dn=i+12;} 
			if (i==12) {if (ti=="am"){ti="pm"} else {ti="am"}}
			if (i >= 6 && ti=="am") {daytime="day"}else{daytime="night"}
			$(".mycal_time_line").append("<div UNSELECTABLE='on' class='half_hour first_half half_hour_" + daytime + "' dval='" + dn + "' tval='" + i + " " + ti + "'>" + i + " " + ti + "</div>");
			$(".mycal_time_line").append("<div UNSELECTABLE='on' class='half_hour second_half half_hour_" + daytime + "' dval='" + (dn + 0.5) + "' tval='" + i + ":30 " + ti + "'>&nbsp;</div>");
		}
	});
	
	//$(".half_hour").selectable("disable");
	
	$(".half_hour").mousedown(function () {
		lastdn=$(this).attr('dval');
		$(".half_hour").removeClass("selected_half_hour");
		$(this).addClass("selected_half_hour");
		start_time=$(this).attr('tval');
		end_time=$(this).attr('tval');
		$(".logme").text(start_time + ' - ' + end_time);
		$("#txtfrom").attr("value", $("#dtxt_" + $(".mycal_time_line").attr("name")).attr("value") + " " + start_time);
		$("#txtto").attr("value", $("#dtxt_" + $(".mycal_time_line").attr("name")).attr("value") + " " + end_time);
		gon=1;
	});
	$(".half_hour").mouseup(function () {gon=0;});
	$(".mycal_time_line").mouseenter(function () {gon=0;});
	$(".half_hour").mouseover(function () {
		if (parseFloat($(this).attr('dval'))<parseFloat(lastdn) || parseFloat($(this).attr('dval'))>(parseFloat(lastdn)+0.5)) {return;}
		if (gon==1) {
			lastdn=$(this).attr('dval');
			$(this).addClass("selected_half_hour");
			//start_time=$(this).attr('tval');
			end_time=$(this).attr('tval');
			$(".logme").text(start_time + ' - ' + end_time);
			$("#txtfrom").attr("value", $("#dtxt_" + $(".mycal_time_line").attr("name")).attr("value") + " " + start_time);
			$("#txtto").attr("value", $("#dtxt_" + $(".mycal_time_line").attr("name")).attr("value") + " " + end_time);
		}
	});
	
}

$("document").ready(function () {print_time_line();})
</script>
<?
$pd=$_GET['sd'];
if ($pd=="") {$pd=date("Y-m-d");}
?>
<div class="mycal_time_line" name="time_track" value="<?=$pd ?>"></div>