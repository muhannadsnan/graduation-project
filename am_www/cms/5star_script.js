
$(document).ready(function() {
	
	
	$("[id^=rating_]").hover(function() {
		if ($(this).attr('disabled') == "disabled") return;
		rid = $(this).attr("id").split("_")[1];
		$("#rating_"+rid).children("[class^=star_]").children('img').hover(function() {

			$("#rating_"+rid).children("[class^=star_]").children('img').removeClass("hover");

			/* The hovered item number */
			var hovered = $(this).parent().attr("class").split("_")[1];
	
			while(hovered > 0) {
				$("#rating_"+rid).children(".star_"+hovered).children('img').addClass("hover");
				hovered--;
			}

		});
	});
	
	//$("[id^=rating_]").

	$("[id^=rating_]").children("[class^=star_]").click(function() {
		if ($(this).parent().attr('disabled') == "disabled") return;
		
		var current_star = $(this).attr("class").split("_")[1];
		var rid = $(this).parent().attr("id").split("_")[1];
		var rtbl = $(this).parent().attr("tbl");
		var ridn = $(this).parent().attr("idn");
		$('#rating_'+rid).load('../cms/5star_send.php', {rating: current_star, id: rid, tbl: rtbl, idn: ridn});

	});




});