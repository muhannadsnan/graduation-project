function DataGrid(_gd_name, _parent_div, mydata, mydesc)
{	
	var _desc=mydesc;
	var _data=mydata;
	
	this.Display=function Display() {_Display()};
	this.gd_name=_gd_name;
	this.parent_div=_parent_div;
	
	function _Display()
	{
		//Container
		$("#" + _parent_div).append("<div id='" + _gd_name + "_grid' class='dg_container'></div>");
		
		//HEAD
		$("#" + _gd_name + "_grid").append("<div id='" + _gd_name + "_grid_head' class='dg_head'></div>");		
		$("#"+ _gd_name +"_grid_head").append('<div id="' + _gd_name + '_grid_row_head"></div>');
		$('#' + _gd_name + '_grid_row_head').append('<table id="' + _gd_name + '_grid_row_table_head"><tr></tr></table>');
		$.each(_desc['cols'], function (icol, col) {
			$('#' + _gd_name + '_grid_row_head' + ' tr').append('<th id="' + _gd_name + '_grid_cell_' + icol + '" style="width:' + col['width'] + 'px">' + col['HeaderText'] + '</th>');
		});
		
		//BODY
		$("#" + _gd_name + "_grid").append("<div id='" + _gd_name + "_grid_body' class='dg_body'></div>");		
		//BODY--ROWS
		var ctext = "";
		$.each( _data, function (ir, r) {
			$("#"+ _gd_name +"_grid_body").append('<div id="' + _gd_name + '_grid_row_' + ir + '"></div>');
			$('#' + _gd_name + '_grid_row_' + ir).append('<table class="dg_row" id="' + _gd_name + '_grid_row_table_' + ir + '"><tr></tr></table>');
			$.each(r, function (ic, c) {
				if (_desc['cols'][ic]['Type']=='0'){ctext=c;}
				if (_desc['cols'][ic]['Type']=='2'){ctext='<a href="' + c[1] + '">' + c[0] + '</a>';}
				if (_desc['cols'][ic]['Type']=='4'){
					if (_desc['cols'][ic]['ButtonType']=='0'){
						var imgurl=_desc['cols'][ic]['ImgURL'];var btntxt=_desc['cols'][ic]['btnText'];
						if (c[1]!=undefined){btntxt=c[1];}	if (c[2]!=undefined){imgurl=c[2];}
						ctext='<input type="button" value="' + btntxt + '" style="cursor:pointer;" onclick="window.location.href=\'' + c[0] + '\'">';
					}
					if (_desc['cols'][ic]['ButtonType']=='1'){
						var imgurl=_desc['cols'][ic]['ImgURL'];var btntxt=_desc['cols'][ic]['btnText'];var theimg="";
						if (c[1]!=undefined){btntxt=c[1];}	if (c[2]!=undefined){imgurl=c[2];}
						if (imgurl!="") {theimg='<img class="dg_btn_img" style="border-width:0px;" alt="' + btntxt + '" src="' + imgurl + '" />';}
						ctext='<a href="' + c[0] + '">' + theimg + btntxt + '</a>';
					}
					if (_desc['cols'][ic]['ButtonType']=='2'){
						var imgurl=_desc['cols'][ic]['ImgURL'];var btntxt=_desc['cols'][ic]['btnText'];
						if (c[1]!=undefined){btntxt=c[1];}	if (c[2]!=undefined){imgurl=c[2];}
						ctext='<a href="' + c[0] + '"><img style="border-width:0px" alt="' + btntxt + '" src="' + imgurl + '" /></a>';
					}
				}
				$('#' + _gd_name + '_grid_row_' + ir + ' tr').append('<td id="' + _gd_name + '_grid_cell_' + ir + '_' + ic + '" style="width:' + _desc['cols'][ic]['width'] + 'px;' + _desc['cols'][ic]['Style'] + '">' + ctext + '</td>');
			});
		});
				
		//FOOTER
		$("#" + _gd_name + "_grid").append("<div id='" + _gd_name + "_grid_foot' class='dg_foot'></div>");		
		$("#"+ _gd_name +"_grid_foot").append('<div id="' + _gd_name + '_grid_row_foot"></div>');
		$('#' + _gd_name + '_grid_row_foot').append('<table id="' + _gd_name + '_grid_row_table_foot"><tr></tr></table>');
		$.each(_desc['cols'], function (icol, col) {
			$('#' + _gd_name + '_grid_row_foot' + ' tr').append('<th id="' + _gd_name + '_grid_cell_' + icol + '" style="width:' + col['width'] + 'px">' + col['FooterText'] + '</th>');
		});
		
$(".dg_row").mousemove(function () { 
	$(this).css("background-color", "whitesmoke");
});
$(".dg_row").mouseout(function () { 
	$(this).css("background-color", "white");
});

	}
}