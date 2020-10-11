function PathTrack(_pt_name, _parent_div, mydata)
{
	var _data=mydata;
	
	this.Display=function Display() {_Display()};
	this.pt_name=_pt_name;
	this.parent_div=_parent_div;
	
	function _Display()
	{
		$("#" + _parent_div).append("<div id='" + _pt_name + "_path' class='pt_container'></div>");
		var tab_sep="";var tab_icon="";var tab_text="";var tab_arrow="";var tab_link="";var itab=0;
		$.each(_data['tabs'], function (itab, tab) {
			tab_sep="";tab_icon="";tab_text="";tab_arrow="";tab_link="";itab+=1;
			if (tab['pt_sep']!=false) {tab_sep="<div class='pt_sep'>&nbsp;</div>";}
			if (tab['pt_icon']!="") {tab_icon="<div class='pt_icon'><img src='" + tab['pt_icon'] + "' /></div>";}
			if (tab['pt_text']!="") {tab_text="<div class='pt_text' itab='" + itab + "'>" + tab['pt_text'] + "</div>";}
			if (tab['pt_icon']!="" || tab['pt_text']!="") {tab_link="<div class='pt_link' id='pt_link_" + itab + "' onclick='window.location.href=\"" + tab['pt_link'] + "\"'>" + tab_icon + tab_text + "</div>";}
			if (tab['pt_arrow']!=false) {tab_arrow="<div class='pt_arrow' id='pt_arrow_" + itab + "'><div class='pt_arrow_img'>&nbsp;</div></div>";}
			$('#' + _pt_name + '_path').append("<div class='container_tab'>" + tab_sep + tab_link + tab_arrow + "</div>");
		});
		
	$(".pt_text").mouseover(function(){
		var tabi=$(this).attr("itab");
		$("#pt_link_" + tabi).addClass("pt_link_hover");
		$("#pt_arrow_" + tabi).addClass("pt_arrow_hover");
	});
	
	$(".pt_text").mouseout(function(){
		var tabi=$(this).attr("itab");
		$("#pt_link_" + tabi).removeClass("pt_link_hover");
		$("#pt_arrow_" + tabi).removeClass("pt_arrow_hover");
	});
		
	}
	
}